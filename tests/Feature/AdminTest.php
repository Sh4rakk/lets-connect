<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function maakAdmin()
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    public function test_admin_kan_admin_paginas_openen()
    {
        $this->withoutVite();
        $admin = $this->maakAdmin();

        $this->actingAs($admin)->get('/admin-dashboard')->assertOk();
        $this->actingAs($admin)->get('/students-overview')->assertOk();
        $this->actingAs($admin)->get('/workshop-dashboard')->assertOk();
        $this->actingAs($admin)->get('/class-dashboard')->assertOk();
    }

    public function test_student_kan_admin_paginas_niet_openen()
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get('/admin-dashboard')->assertForbidden();
        $this->actingAs($student)->get('/students-overview')->assertForbidden();
        $this->actingAs($student)->get('/workshop-dashboard')->assertForbidden();
        $this->actingAs($student)->get('/class-dashboard')->assertForbidden();
        $this->actingAs($student)->get('/export/all-users')->assertForbidden();
    }

    public function test_gast_kan_admin_paginas_niet_openen()
    {
        $this->get('/admin-dashboard')->assertForbidden();
        $this->get('/students-overview')->assertForbidden();
    }

    public function test_student_kan_inschrijvingen_niet_aan_of_uit_zetten()
    {
        Setting::create(['key' => 'signups_open', 'value' => '1']);
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/settings/toggle-signups')->assertForbidden();

        $this->assertEquals('1', Setting::where('key', 'signups_open')->first()->value);
    }

    public function test_admin_kan_inschrijvingen_aan_en_uit_zetten()
    {
        Setting::create(['key' => 'signups_open', 'value' => '1']);
        $admin = $this->maakAdmin();

        $this->actingAs($admin)->postJson('/api/settings/toggle-signups')->assertOk();
        $this->assertEquals('0', Setting::where('key', 'signups_open')->first()->value);

        $this->actingAs($admin)->postJson('/api/settings/toggle-signups')->assertOk();
        $this->assertEquals('1', Setting::where('key', 'signups_open')->first()->value);
    }

    public function test_admin_kan_workshop_van_student_veranderen()
    {
        $admin = $this->maakAdmin();
        $student = User::factory()->create();

        DB::table('moments')->insert(['id' => 1, 'time' => '09:00']);
        $workshop1 = Workshop::create(['name' => 'Workshop 1', 'capacity' => 10]);
        $workshop2 = Workshop::create(['name' => 'Workshop 2', 'capacity' => 10]);
        $wm1 = WorkshopMoment::create(['workshop_id' => $workshop1->id, 'moment_id' => 1]);
        $wm2 = WorkshopMoment::create(['workshop_id' => $workshop2->id, 'moment_id' => 1]);

        Bookings::create(['wm_id' => $wm1->id, 'student_id' => $student->id]);

        $this->actingAs($admin)->patchJson('/api/users/' . $student->id . '/bookings', [
            'rounds' => [1 => $wm2->id],
        ]);

        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm2->id]);
        $this->assertDatabaseMissing('bookings', ['student_id' => $student->id, 'wm_id' => $wm1->id]);
    }

    public function test_admin_kan_student_niet_in_volle_workshop_zetten()
    {
        $admin = $this->maakAdmin();
        $student = User::factory()->create();
        $andereStudent = User::factory()->create();

        DB::table('moments')->insert(['id' => 1, 'time' => '09:00']);
        $workshop = Workshop::create(['name' => 'Volle workshop', 'capacity' => 1]);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);

        Bookings::create(['wm_id' => $wm->id, 'student_id' => $andereStudent->id]);

        $this->actingAs($admin)->patchJson('/api/users/' . $student->id . '/bookings', [
            'rounds' => [1 => $wm->id],
        ])->assertStatus(422);

        $this->assertEquals(0, Bookings::where('student_id', $student->id)->count());
    }
}
