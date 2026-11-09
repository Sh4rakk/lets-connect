<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Moment;
use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function maakAdmin(): User
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    private function maakWorkshop(string $naam = 'Robotica'): Workshop
    {
        return Workshop::create([
            'name' => $naam,
            'full_description' => 'Bouw je eigen robot.',
            'capacity' => 20,
            'location' => 'Lokaal B1.12',
        ]);
    }

    public function test_elke_workshop_krijgt_een_unieke_qr_token()
    {
        $a = $this->maakWorkshop('A');
        $b = $this->maakWorkshop('B');

        $this->assertSame(40, strlen($a->qr_token));
        $this->assertNotSame($a->qr_token, $b->qr_token);
    }

    public function test_admin_ziet_qr_code_die_naar_de_juiste_workshop_wijst()
    {
        $workshop = $this->maakWorkshop();

        $this->actingAs($this->maakAdmin())
            ->get(route('workshop.qr', $workshop))
            ->assertOk()
            ->assertSee('Robotica')
            ->assertSee('<svg', false)
            ->assertSee($workshop->qrUrl());
    }

    public function test_student_en_gast_kunnen_qr_pagina_niet_openen()
    {
        $workshop = $this->maakWorkshop();

        $this->actingAs(User::factory()->create())->get(route('workshop.qr', $workshop))->assertForbidden();
        $this->post(route('workshop.qr.regenerate', $workshop))->assertForbidden();
    }

    public function test_scannen_toont_de_juiste_workshop()
    {
        $robotica = $this->maakWorkshop('Robotica');
        $this->maakWorkshop('Koken');

        $this->get('/qr/'.$robotica->qr_token)
            ->assertOk()
            ->assertSee('Robotica')
            ->assertSee('Lokaal B1.12')
            ->assertSee('Bouw je eigen robot.')
            ->assertDontSee('Koken');
    }

    public function test_onbekende_qr_code_geeft_404()
    {
        $this->maakWorkshop();

        $this->get('/qr/'.str_repeat('x', 40))->assertNotFound();
    }

    public function test_nieuwe_qr_code_maakt_de_oude_ongeldig()
    {
        $workshop = $this->maakWorkshop();
        $oud = $workshop->qr_token;

        $this->actingAs($this->maakAdmin())
            ->post(route('workshop.qr.regenerate', $workshop))
            ->assertRedirect(route('workshop.qr', $workshop));

        $this->assertNotSame($oud, $workshop->fresh()->qr_token);
        $this->get('/qr/'.$oud)->assertNotFound();
        $this->get('/qr/'.$workshop->fresh()->qr_token)->assertOk();
    }

    public function test_ingelogde_student_ziet_of_hij_ingeschreven_is()
    {
        $workshop = $this->maakWorkshop();
        $moment = Moment::create(['time' => '10:00 - 11:00']);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $moment->id]);
        $student = User::factory()->create();
        Bookings::create(['student_id' => $student->id, 'wm_id' => $wm->id]);

        $this->actingAs($student)
            ->get('/qr/'.$workshop->qr_token)
            ->assertOk()
            ->assertSee('Je bent ingeschreven voor deze workshop (10:00 - 11:00)');
    }

    public function test_gast_wordt_na_inloggen_teruggestuurd_naar_de_workshop()
    {
        $workshop = $this->maakWorkshop();

        $this->get('/qr/'.$workshop->qr_token)->assertSee('Log in');
        $this->assertSame($workshop->qrUrl(), session('url.intended'));
    }
}
