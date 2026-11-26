<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Moment;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuccessPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_succes_pagina_toont_gekozen_workshops()
    {
        $this->withoutVite();
        $student = User::factory()->create();
        $moment = Moment::create(['time' => '13:00 - 13:45']);
        $workshop = Workshop::create(['name' => 'Podcast opnemen', 'capacity' => 10, 'location' => 'C2.01']);
        $wm = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $moment->id]);
        Bookings::create(['wm_id' => $wm->id, 'student_id' => $student->id]);

        $this->actingAs($student)->get('/success')
            ->assertOk()
            ->assertSee('Je inschrijving is opgeslagen')
            ->assertSee('Podcast opnemen')
            ->assertSee('C2.01');
    }

    public function test_succes_pagina_zonder_inschrijvingen_gaat_naar_dashboard()
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get('/success')->assertRedirect('/dashboard');
    }

    public function test_gast_moet_inloggen_voor_succes_pagina()
    {
        $this->get('/success')->assertRedirect('/login');
    }
}
