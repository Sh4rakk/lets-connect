<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Moment;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkshopBeheerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');

        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->addRole('admin');

        foreach (['13:00 - 13:45', '13:45 - 14:30', '15:00 - 15:45'] as $tijd) {
            Moment::create(['time' => $tijd]);
        }
    }

    private function gegevens(array $extra = []): array
    {
        return array_merge([
            'name' => 'Fotografie in de studio',
            'location' => 'B1.12',
            'full_description' => 'Maak een portret.',
            'rounds' => [
                1 => ['enabled' => '1', 'capacity' => '20'],
                2 => ['enabled' => '1', 'capacity' => '15'],
            ],
        ], $extra);
    }

    private function maakWorkshop(array $capaciteiten = [1 => 20, 2 => 15], string $naam = 'Podcast'): Workshop
    {
        $workshop = Workshop::create(['name' => $naam, 'capacity' => 10, 'location' => 'C2.01']);

        foreach ($capaciteiten as $ronde => $capaciteit) {
            WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $ronde, 'capacity' => $capaciteit]);
        }

        return $workshop;
    }

    private function schrijfIn(Workshop $workshop, int $ronde): User
    {
        $student = User::factory()->create();
        Bookings::create([
            'student_id' => $student->id,
            'wm_id' => $workshop->workshopMoments()->where('moment_id', $ronde)->value('id'),
        ]);

        return $student;
    }

    public function test_overzicht_toont_workshops_per_ronde()
    {
        $workshop = $this->maakWorkshop([1 => 1]);
        $this->schrijfIn($workshop, 1);

        $this->actingAs($this->admin)->get('/workshop-dashboard')
            ->assertOk()
            ->assertSee('Workshop overzicht')
            ->assertSee('Podcast')
            ->assertSee('1 / 1')
            ->assertSee('vol')
            ->assertSee('Geen workshops in deze ronde.');
    }

    public function test_alleen_admin_kan_workshops_beheren()
    {
        $workshop = $this->maakWorkshop();
        $student = User::factory()->create();

        $this->get('/workshops/create')->assertForbidden();
        $this->actingAs($student)->get('/workshops/create')->assertForbidden();
        $this->actingAs($student)->post('/workshops', $this->gegevens())->assertForbidden();
        $this->actingAs($student)->get("/workshops/{$workshop->id}/edit")->assertForbidden();
        $this->actingAs($student)->put("/workshops/{$workshop->id}", $this->gegevens())->assertForbidden();
        $this->actingAs($student)->delete("/workshops/{$workshop->id}")->assertForbidden();

        $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'name' => 'Podcast']);
    }

    public function test_admin_kan_workshop_toevoegen_met_afbeelding()
    {
        $bestand = UploadedFile::fake()->image('Fotografie Studio.jpg');

        $this->actingAs($this->admin)->get('/workshops/create')->assertOk()->assertSee('Workshop toevoegen');
        $this->actingAs($this->admin)->post('/workshops', $this->gegevens(['image' => $bestand]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('workshop-dashboard'));

        $workshop = Workshop::where('name', 'Fotografie in de studio')->firstOrFail();

        $this->assertTrue($workshop->is_manual);
        $this->assertSame([1 => 20, 2 => 15], $workshop->workshopMoments()->pluck('capacity', 'moment_id')->all());
        $this->assertStringStartsWith('/storage/workshops/fotografie-studio-', $workshop->image_url);
        Storage::disk('public')->assertExists('workshops/'.basename($workshop->image_url));

        $this->actingAs($this->admin)->get('/workshop-dashboard')->assertSee('handmatig');
    }

    public function test_ongeldige_gegevens_worden_geweigerd()
    {
        $this->maakWorkshop();
        $this->actingAs($this->admin);

        $this->post('/workshops', $this->gegevens(['name' => '']))->assertSessionHasErrors('name');
        $this->post('/workshops', $this->gegevens(['name' => 'Podcast']))->assertSessionHasErrors('name');
        $this->post('/workshops', $this->gegevens(['rounds' => [1 => ['capacity' => '5']]]))->assertSessionHasErrors('rounds');
        $this->post('/workshops', $this->gegevens(['rounds' => [1 => ['enabled' => '1', 'capacity' => '0']]]))->assertSessionHasErrors('rounds.1.capacity');
        $this->post('/workshops', $this->gegevens(['leader_email' => 'geen email']))->assertSessionHasErrors('leader_email');
        $this->post('/workshops', $this->gegevens(['image' => UploadedFile::fake()->create('virus.php', 10)]))->assertSessionHasErrors('image');

        $this->assertSame(1, Workshop::count());
    }

    public function test_admin_kan_workshop_bewerken_en_rondes_wisselen()
    {
        $workshop = $this->maakWorkshop();

        $this->actingAs($this->admin)->get("/workshops/{$workshop->id}/edit")->assertOk()->assertSee('Podcast bewerken');
        $this->actingAs($this->admin)->put("/workshops/{$workshop->id}", $this->gegevens([
            'name' => 'Podcast opnemen',
            'rounds' => [
                1 => ['enabled' => '1', 'capacity' => '8'],
                3 => ['enabled' => '1', 'capacity' => '12'],
            ],
        ]))->assertSessionHasNoErrors();

        $this->assertSame('Podcast opnemen', $workshop->fresh()->name);
        $this->assertFalse($workshop->fresh()->is_manual);
        $this->assertSame([1 => 8, 3 => 12], $workshop->workshopMoments()->pluck('capacity', 'moment_id')->all());
    }

    public function test_nieuwe_afbeelding_vervangt_de_oude()
    {
        $this->actingAs($this->admin);
        $this->post('/workshops', $this->gegevens(['image' => UploadedFile::fake()->image('oud.jpg')]));
        $workshop = Workshop::firstOrFail();
        $oud = basename($workshop->image_url);

        $this->put("/workshops/{$workshop->id}", $this->gegevens(['image' => UploadedFile::fake()->image('nieuw.png')]))->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('workshops/'.$oud);
        Storage::disk('public')->assertExists('workshops/'.basename($workshop->fresh()->image_url));
    }

    public function test_ronde_met_inschrijvingen_kan_niet_uit_of_te_klein()
    {
        $workshop = $this->maakWorkshop();
        $this->schrijfIn($workshop, 1);
        $this->schrijfIn($workshop, 1);
        $this->actingAs($this->admin);

        $this->put("/workshops/{$workshop->id}", $this->gegevens(['rounds' => [2 => ['enabled' => '1', 'capacity' => '5']]]))
            ->assertSessionHasErrors('rounds.1.capacity');
        $this->put("/workshops/{$workshop->id}", $this->gegevens(['rounds' => [1 => ['enabled' => '1', 'capacity' => '1']]]))
            ->assertSessionHasErrors('rounds.1.capacity');

        $this->assertSame('Podcast', $workshop->fresh()->name);
        $this->assertSame([1 => 20, 2 => 15], $workshop->workshopMoments()->pluck('capacity', 'moment_id')->all());
        $this->assertSame(2, Bookings::count());
    }

    public function test_workshop_verwijderen_mag_alleen_zonder_inschrijvingen()
    {
        $vol = $this->maakWorkshop();
        $leeg = $this->maakWorkshop([1 => 5], 'Leeg');
        $this->schrijfIn($vol, 1);
        $this->actingAs($this->admin);

        $this->delete("/workshops/{$vol->id}")->assertSessionHasErrors('delete');
        $this->delete("/workshops/{$leeg->id}")->assertSessionHasNoErrors();

        $this->assertDatabaseHas('workshops', ['id' => $vol->id]);
        $this->assertDatabaseMissing('workshops', ['id' => $leeg->id]);
        $this->assertSame(1, Bookings::count());
    }

    public function test_capaciteit_per_ronde_telt_bij_inschrijven()
    {
        Setting::create(['key' => 'signups_open', 'value' => '1']);
        $klein = $this->maakWorkshop([1 => 1, 2 => 5, 3 => 5], 'Klein');
        $a = $this->maakWorkshop([1 => 5, 2 => 5, 3 => 5], 'A');
        $b = $this->maakWorkshop([1 => 5, 2 => 5, 3 => 5], 'B');
        $this->schrijfIn($klein, 1);
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', ['rounds' => [1 => $klein->id, 2 => $a->id, 3 => $b->id]])->assertStatus(422);
        $this->actingAs($student)->postJson('/api/bookings', ['rounds' => [1 => $a->id, 2 => $klein->id, 3 => $b->id]])->assertCreated();

        $plekken = collect($this->actingAs($student)->getJson('/api/workshop-moments')->json('data'))
            ->where('workshop_id', $klein->id)
            ->pluck('spots_left', 'moment_id');

        $this->assertSame([1 => 0, 2 => 4, 3 => 5], $plekken->sortKeys()->all());
    }

    public function test_zonder_capaciteit_per_ronde_telt_die_van_de_workshop()
    {
        $workshop = $this->maakWorkshop([1 => null]);

        $this->assertSame(10, $workshop->workshopMoments()->first()->maxSpots());
    }
}
