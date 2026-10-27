<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Setting;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    // maakt 3 rondes en 3 workshops aan, elke workshop zit in elke ronde
    private function maakWorkshops($capacity = 10)
    {
        DB::table('moments')->insert([
            ['id' => 1, 'time' => '09:00'],
            ['id' => 2, 'time' => '10:00'],
            ['id' => 3, 'time' => '11:00'],
        ]);

        $workshops = [];
        for ($i = 1; $i <= 3; $i++) {
            $workshop = Workshop::create(['name' => 'Workshop ' . $i, 'capacity' => $capacity]);

            for ($ronde = 1; $ronde <= 3; $ronde++) {
                WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $ronde]);
            }

            $workshops[] = $workshop;
        }

        return $workshops;
    }

    private function inschrijvingenOpen()
    {
        Setting::create(['key' => 'signups_open', 'value' => '1']);
    }

    public function test_student_kan_drie_workshops_boeken()
    {
        Mail::fake();
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        $response->assertCreated();
        $this->assertEquals(3, Bookings::where('student_id', $student->id)->count());
    }

    public function test_boeking_komt_in_de_goede_ronde()
    {
        Mail::fake();
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[2]->id, 2 => $workshops[0]->id, 3 => $workshops[1]->id],
        ]);

        // workshop 3 moet in ronde 1 zitten
        $wm = WorkshopMoment::where('workshop_id', $workshops[2]->id)->where('moment_id', 1)->first();
        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm->id]);
    }

    public function test_student_krijgt_bevestigings_mail()
    {
        Mail::fake();
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        Mail::assertSent(\App\Mail\SendMail::class);
    }

    public function test_kan_niet_boeken_als_inschrijvingen_dicht_zijn()
    {
        Setting::create(['key' => 'signups_open', 'value' => '0']);
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        $response->assertForbidden();
        $this->assertEquals(0, Bookings::count());
    }

    public function test_kan_niet_boeken_als_workshop_vol_is()
    {
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops(1);

        // iemand anders zit al in workshop 1 ronde 1
        $andereStudent = User::factory()->create();
        $wm = WorkshopMoment::where('workshop_id', $workshops[0]->id)->where('moment_id', 1)->first();
        Bookings::create(['wm_id' => $wm->id, 'student_id' => $andereStudent->id]);

        $student = User::factory()->create();
        $response = $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Bookings::where('student_id', $student->id)->count());
    }

    public function test_kan_niet_boeken_met_workshop_die_niet_bestaat()
    {
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => 'bestaat-niet', 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Bookings::count());
    }

    public function test_kan_niet_boeken_met_maar_twee_rondes()
    {
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $response = $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id],
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, Bookings::count());
    }

    public function test_gast_kan_niet_boeken()
    {
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();

        $response = $this->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        $response->assertUnauthorized();
        $this->assertEquals(0, Bookings::count());
    }

    public function test_student_kan_keuze_veranderen()
    {
        Mail::fake();
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[0]->id, 2 => $workshops[1]->id, 3 => $workshops[2]->id],
        ]);

        // nu andere keuze
        $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[1]->id, 2 => $workshops[2]->id, 3 => $workshops[0]->id],
        ]);

        // nog steeds 3 boekingen, niet 6
        $this->assertEquals(3, Bookings::where('student_id', $student->id)->count());

        $wm = WorkshopMoment::where('workshop_id', $workshops[1]->id)->where('moment_id', 1)->first();
        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm->id]);
    }

    public function test_keuze_veranderen_werkt_ook_als_boekingen_door_elkaar_staan()
    {
        // dit ging fout met de oude MOD(id, 3) code: andere studenten boeken tussendoor
        Mail::fake();
        $this->inschrijvingenOpen();
        $workshops = $this->maakWorkshops();
        $student = User::factory()->create();
        $andereStudent = User::factory()->create();

        $wm = fn ($workshop, $ronde) => WorkshopMoment::where('workshop_id', $workshop->id)->where('moment_id', $ronde)->first()->id;

        // boekingen door elkaar heen aanmaken, zoals op de echte inschrijfdag
        Bookings::create(['wm_id' => $wm($workshops[0], 1), 'student_id' => $student->id]);
        Bookings::create(['wm_id' => $wm($workshops[1], 1), 'student_id' => $andereStudent->id]);
        Bookings::create(['wm_id' => $wm($workshops[2], 1), 'student_id' => $andereStudent->id]);
        Bookings::create(['wm_id' => $wm($workshops[1], 2), 'student_id' => $student->id]);
        Bookings::create(['wm_id' => $wm($workshops[0], 2), 'student_id' => $andereStudent->id]);
        Bookings::create(['wm_id' => $wm($workshops[0], 3), 'student_id' => $andereStudent->id]);
        Bookings::create(['wm_id' => $wm($workshops[2], 3), 'student_id' => $student->id]);

        $this->actingAs($student)->postJson('/api/bookings', [
            'rounds' => [1 => $workshops[1]->id, 2 => $workshops[2]->id, 3 => $workshops[0]->id],
        ])->assertCreated();

        // elke ronde moet precies 1 boeking hebben met de nieuwe workshop
        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm($workshops[1], 1)]);
        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm($workshops[2], 2)]);
        $this->assertDatabaseHas('bookings', ['student_id' => $student->id, 'wm_id' => $wm($workshops[0], 3)]);
        $this->assertEquals(3, Bookings::where('student_id', $student->id)->count());
    }

    public function test_capaciteit_per_ronde_klopt()
    {
        $workshops = $this->maakWorkshops(2);
        $student = User::factory()->create();
        $wm = WorkshopMoment::where('workshop_id', $workshops[0]->id)->where('moment_id', 2)->first();
        Bookings::create(['wm_id' => $wm->id, 'student_id' => User::factory()->create()->id]);

        $response = $this->actingAs($student)->getJson('/api/workshop-moments');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $wm->id, 'moment_id' => 2, 'spots_left' => 1]);
    }

    public function test_student_kan_geen_namen_van_andere_studenten_ophalen()
    {
        $this->maakWorkshops();
        $student = User::factory()->create();

        $this->actingAs($student)->getJson('/api/workshop-moments?with[bookings]=1')->assertStatus(422);
        $this->actingAs($student)->getJson('/api/workshops?with[bookings]=1')->assertStatus(422);
    }

    public function test_student_kan_niet_filteren_op_kolommen_van_andere_tabellen()
    {
        // het package checkt dit zelf niet, daarom staat OnlyAllowedColumns in de requests
        $this->maakWorkshops();
        $student = User::factory()->create();

        $this->actingAs($student)->getJson('/api/workshop-moments?where[bookings.student.email]=a@b.nl')->assertStatus(422);
        $this->actingAs($student)->getJson('/api/workshop-moments?where[$or][0][bookings.student.email]=a@b.nl')->assertStatus(422);
        $this->actingAs($student)->getJson('/api/workshop-moments?orderBy=bookings.student_id')->assertStatus(422);
    }

    public function test_student_mag_wel_gewoon_filteren()
    {
        $this->maakWorkshops();
        $student = User::factory()->create();

        $response = $this->actingAs($student)->getJson('/api/workshop-moments?where[moment_id]=2');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }
}
