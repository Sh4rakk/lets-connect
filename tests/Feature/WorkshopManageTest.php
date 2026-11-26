<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Moment;
use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkshopManageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Moment::insert(['time' => '13:00 - 13:45']);
        Moment::insert(['time' => '13:45 - 14:30']);
        Moment::insert(['time' => '15:00 - 15:45']);
    }

    private function maakAdmin()
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    public function test_admin_kan_workshop_toevoegen_met_capaciteit_per_ronde()
    {
        Storage::fake('public');
        $admin = $this->maakAdmin();

        $this->actingAs($admin)->post('/workshops', [
            'name' => 'VR & gamen',
            'location' => 'D1.05',
            'full_description' => 'Gamen met een VR bril.',
            'image' => UploadedFile::fake()->image('vr.jpg', 800, 450),
            'rounds' => [
                1 => ['enabled' => '1', 'capacity' => '12'],
                2 => ['enabled' => '1', 'capacity' => '8'],
                3 => ['enabled' => '0', 'capacity' => '20'],
            ],
        ])->assertRedirect('/workshop-dashboard');

        $workshop = Workshop::where('name', 'VR & gamen')->first();
        $this->assertTrue($workshop->is_manual);
        $this->assertNotNull($workshop->qr_token);
        $this->assertSame([1 => 12, 2 => 8], $workshop->workshopMoments()->pluck('capacity', 'moment_id')->all());
        Storage::disk('public')->assertExists('workshops/' . basename($workshop->image_url));

        $this->actingAs($admin)->get('/workshop-dashboard')->assertOk()->assertSee('VR &amp; gamen', false);
    }

    public function test_minimaal_een_ronde_moet_aan()
    {
        $admin = $this->maakAdmin();

        $this->actingAs($admin)->post('/workshops', [
            'name' => 'Leeg',
            'rounds' => [1 => ['enabled' => '0', 'capacity' => '10']],
        ])->assertSessionHasErrors('rounds');

        $this->assertSame(0, Workshop::count());
    }

    public function test_ronde_met_inschrijvingen_kan_niet_uit_en_capaciteit_niet_te_laag()
    {
        $admin = $this->maakAdmin();
        $workshop = Workshop::create(['name' => 'Fotografie', 'capacity' => 20]);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1, 'capacity' => 20]);
        Bookings::create(['wm_id' => $wm->id, 'student_id' => User::factory()->create()->id]);
        Bookings::create(['wm_id' => $wm->id, 'student_id' => User::factory()->create()->id]);

        $this->actingAs($admin)->put("/workshops/{$workshop->id}", [
            'name' => 'Fotografie',
            'rounds' => [1 => ['enabled' => '0'], 2 => ['enabled' => '1', 'capacity' => '10']],
        ])->assertSessionHasErrors('rounds.1.enabled');

        $this->actingAs($admin)->put("/workshops/{$workshop->id}", [
            'name' => 'Fotografie',
            'rounds' => [1 => ['enabled' => '1', 'capacity' => '1']],
        ])->assertSessionHasErrors('rounds.1.capacity');

        // niets is veranderd door de mislukte pogingen
        $this->assertSame(1, $workshop->workshopMoments()->count());
        $this->assertSame(20, $wm->fresh()->capacity);
    }

    public function test_admin_kan_ronde_toevoegen_uitzetten_en_capaciteit_wijzigen()
    {
        $admin = $this->maakAdmin();
        $workshop = Workshop::create(['name' => 'Koken', 'capacity' => 16]);
        WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);
        WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 2]);

        $this->actingAs($admin)->put("/workshops/{$workshop->id}", [
            'name' => 'Brood bakken',
            'location' => 'A0.04',
            'rounds' => [
                1 => ['enabled' => '1', 'capacity' => '10'],
                2 => ['enabled' => '0'],
                3 => ['enabled' => '1', 'capacity' => '14'],
            ],
        ])->assertRedirect('/workshop-dashboard');

        $workshop->refresh();
        $this->assertSame('Brood bakken', $workshop->name);
        $this->assertSame([1 => 10, 3 => 14], $workshop->workshopMoments()->orderBy('moment_id')->pluck('capacity', 'moment_id')->all());
    }

    public function test_capaciteit_per_ronde_telt_bij_inschrijven()
    {
        $workshop = Workshop::create(['name' => 'Klein', 'capacity' => 20]);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1, 'capacity' => 1]);
        $this->assertSame(1, $wm->effectiveCapacity());

        $zonder = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 2]);
        $this->assertSame(20, $zonder->effectiveCapacity());
    }

    public function test_admin_kan_workshop_met_inschrijvingen_verwijderen()
    {
        $admin = $this->maakAdmin();
        $workshop = Workshop::create(['name' => 'Weg', 'capacity' => 5]);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);
        Bookings::create(['wm_id' => $wm->id, 'student_id' => User::factory()->create()->id]);

        $this->actingAs($admin)->delete("/workshops/{$workshop->id}")->assertRedirect('/workshop-dashboard');

        $this->assertSame(0, Workshop::count());
        $this->assertSame(0, WorkshopMoment::count());
        $this->assertSame(0, Bookings::count());
    }

    public function test_student_kan_workshops_niet_beheren()
    {
        $student = User::factory()->create();
        $workshop = Workshop::create(['name' => 'X', 'capacity' => 5]);

        $this->actingAs($student)->get('/workshops/create')->assertForbidden();
        $this->actingAs($student)->post('/workshops', ['name' => 'Y'])->assertForbidden();
        $this->actingAs($student)->get("/workshops/{$workshop->id}/edit")->assertForbidden();
        $this->actingAs($student)->delete("/workshops/{$workshop->id}")->assertForbidden();
    }

    public function test_formulier_paginas_laden()
    {
        $admin = $this->maakAdmin();
        $workshop = Workshop::create(['name' => 'Escape room', 'capacity' => 20]);
        WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);

        $this->actingAs($admin)->get('/workshops/create')->assertOk()->assertSee('Workshop toevoegen');
        $this->actingAs($admin)->get("/workshops/{$workshop->id}/edit")->assertOk()->assertSee('Escape room bewerken');
    }
}
