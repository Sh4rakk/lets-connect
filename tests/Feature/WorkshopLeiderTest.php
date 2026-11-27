<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Feedback;
use App\Models\Moment;
use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkshopLeiderTest extends TestCase
{
    use RefreshDatabase;

    private User $leider;
    private Workshop $eigen;
    private Workshop $ander;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Role::create(['name' => 'admin']);
        Moment::create(['time' => '13:00 - 13:45']);
        Moment::create(['time' => '13:45 - 14:30']);

        $this->leider = User::factory()->create(['class' => User::LEADER_CLASS]);
        $this->eigen = $this->maakWorkshop('Eigen workshop', $this->leider);
        $this->ander = $this->maakWorkshop('Andere workshop');
    }

    private function maakWorkshop(string $naam, ?User $leider = null): Workshop
    {
        $workshop = new Workshop(['name' => $naam, 'capacity' => 10, 'location' => 'B1.12']);
        $workshop->leader_id = $leider?->id;
        $workshop->save();

        foreach ([1, 2] as $ronde) {
            WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $ronde, 'capacity' => 10 + $ronde]);
        }

        return $workshop;
    }

    private function maakAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    private function ronde(Workshop $workshop, int $ronde): WorkshopMoment
    {
        return $workshop->workshopMoments()->where('moment_id', $ronde)->firstOrFail();
    }

    private function schrijfIn(WorkshopMoment $ronde, string $naam): User
    {
        $student = User::factory()->create(['name' => $naam]);
        Bookings::create(['student_id' => $student->id, 'wm_id' => $ronde->id]);

        return $student;
    }

    public function test_leider_ziet_alleen_eigen_workshops()
    {
        $this->actingAs($this->leider)->get('/workshopleider')
            ->assertOk()
            ->assertSee('Eigen workshop')
            ->assertDontSee('Andere workshop');
    }

    public function test_admin_ziet_alle_workshops()
    {
        $this->actingAs($this->maakAdmin())->get('/workshopleider')
            ->assertOk()
            ->assertSee('Eigen workshop')
            ->assertSee('Andere workshop');
    }

    public function test_student_en_gast_kunnen_leider_paginas_niet_openen()
    {
        $ronde = $this->ronde($this->eigen, 1);
        $paginas = ['/workshopleider', route('leader.expected', $ronde), route('leader.feedback', $ronde), route('workshop-moment.qr', $ronde)];

        foreach ($paginas as $pagina) {
            $this->get($pagina)->assertRedirect(route('login'));
        }

        foreach ($paginas as $pagina) {
            $this->actingAs(User::factory()->create())->get($pagina)->assertForbidden();
        }
    }

    public function test_verwachting_toont_alleen_studenten_van_die_ronde()
    {
        $this->schrijfIn($this->ronde($this->eigen, 1), 'Student Ronde Een');
        $this->schrijfIn($this->ronde($this->eigen, 2), 'Student Ronde Twee');

        $this->actingAs($this->leider)->get(route('leader.expected', $this->ronde($this->eigen, 1)))
            ->assertOk()
            ->assertSee('Student Ronde Een')
            ->assertDontSee('Student Ronde Twee');
    }

    public function test_leider_kan_niet_bij_een_andere_workshop()
    {
        $ronde = $this->ronde($this->ander, 1);
        $token = $ronde->qr_token;
        $this->actingAs($this->leider);

        $this->get(route('leader.expected', $ronde))->assertForbidden();
        $this->get(route('leader.feedback', $ronde))->assertForbidden();
        $this->get(route('workshop-moment.qr', $ronde))->assertForbidden();
        $this->post(route('workshop-moment.qr.regenerate', $ronde))->assertForbidden();
        $this->get('/workshop-dashboard')->assertForbidden();
        $this->get(route('workshops.edit', $this->eigen))->assertForbidden();

        $this->assertSame($token, $ronde->fresh()->qr_token);
    }

    public function test_feedback_pagina_toont_de_resultaten_van_die_ronde()
    {
        $een = $this->ronde($this->eigen, 1);
        $twee = $this->ronde($this->eigen, 2);
        Feedback::create(['workshop_moment_id' => $een->id, 'rating' => 5, 'good' => 'Leuke uitleg', 'learned' => 'heel_erg', 'fun' => 'beetje', 'recommend' => true]);
        Feedback::create(['workshop_moment_id' => $een->id, 'rating' => 4, 'better' => '<b>Meer tijd</b>', 'learned' => 'niet', 'fun' => 'beetje', 'recommend' => false]);
        Feedback::create(['workshop_moment_id' => $een->id, 'rating' => 2, 'step_token' => 'open']);
        Feedback::create(['workshop_moment_id' => $twee->id, 'rating' => 1, 'good' => 'Tekst van ronde twee']);

        $this->actingAs($this->leider)->get(route('leader.feedback', $een))
            ->assertOk()
            ->assertViewHas('average', '3,7')
            ->assertViewHas('total', 3)
            ->assertViewHas('answered', 2)
            ->assertViewHas('ratings', [5 => 1, 4 => 1, 3 => 0, 2 => 1, 1 => 0])
            ->assertViewHas('questions', [
                'Was de workshop leerzaam?' => ['Niet' => 1, 'Beetje' => 0, 'Heel erg' => 1],
                'Was de workshop leuk?' => ['Niet' => 0, 'Beetje' => 2, 'Heel erg' => 0],
                'Zou je hem aanraden?' => ['Ja' => 1, 'Nee' => 1],
            ])
            ->assertSee('Leuke uitleg')
            ->assertSee('<b>Meer tijd</b>')
            ->assertDontSee('<b>Meer tijd</b>', false)
            ->assertDontSee('Tekst van ronde twee');

        $this->get('/workshopleider')->assertSee('Feedback (3)')->assertSee('Feedback (1)');
    }

    public function test_feedback_pagina_zonder_feedback()
    {
        $this->actingAs($this->leider)->get(route('leader.feedback', $this->ronde($this->eigen, 1)))
            ->assertOk()
            ->assertSee('Nog geen feedback voor deze ronde.')
            ->assertDontSee('Gemiddeld');
    }

    public function test_feedback_met_alleen_een_cijfer()
    {
        $ronde = $this->ronde($this->eigen, 1);
        Feedback::create(['workshop_moment_id' => $ronde->id, 'rating' => 3]);

        $this->actingAs($this->leider)->get(route('leader.feedback', $ronde))
            ->assertOk()
            ->assertSee('3,0')
            ->assertSee('Niemand heeft dit ingevuld.')
            ->assertDontSee('Was de workshop leerzaam?');
    }

    public function test_admin_kan_alle_feedback_zien()
    {
        $this->actingAs($this->maakAdmin())->get(route('leader.feedback', $this->ronde($this->ander, 1)))->assertOk();
    }

    public function test_elke_ronde_heeft_een_eigen_qr_code()
    {
        $een = $this->ronde($this->eigen, 1);
        $twee = $this->ronde($this->eigen, 2);

        $this->assertNotSame($een->qr_token, $twee->qr_token);
        $this->actingAs($this->leider)->get(route('workshop-moment.qr', $een))
            ->assertOk()
            ->assertSee('<svg', false)
            ->assertSee($een->qrUrl())
            ->assertDontSee($twee->qrUrl())
            ->assertDontSee('Alle rondes');
    }

    public function test_ronde_zonder_token_krijgt_er_een()
    {
        $ronde = $this->ronde($this->eigen, 1);
        WorkshopMoment::whereKey($ronde->id)->update(['qr_token' => null]);

        $this->actingAs($this->leider)->get(route('workshop-moment.qr', $ronde))->assertOk();

        $this->assertSame(40, strlen($ronde->fresh()->qr_token));
    }

    public function test_scannen_van_een_ronde_stuurt_door_naar_feedback()
    {
        $een = $this->ronde($this->eigen, 1);
        $twee = $this->ronde($this->eigen, 2);

        $this->get($een->qrUrl())->assertRedirect(route('feedback.show', $een->qr_token));
        $this->get($twee->qrUrl())->assertRedirect(route('feedback.show', $twee->qr_token));
    }

    public function test_nieuwe_qr_code_geldt_alleen_voor_die_ronde()
    {
        $een = $this->ronde($this->eigen, 1);
        $twee = $this->ronde($this->eigen, 2);
        $oud = $een->qrUrl();

        $this->actingAs($this->leider)->post(route('workshop-moment.qr.regenerate', $een))
            ->assertRedirect(route('workshop-moment.qr', $een));

        $this->get($oud)->assertNotFound();
        $this->followingRedirects()->get($een->fresh()->qrUrl())->assertOk();
        $this->followingRedirects()->get($twee->qrUrl())->assertOk();
        $this->get($this->eigen->qrUrl())->assertOk();
    }

    public function test_qr_token_en_leider_staan_niet_in_de_api()
    {
        $json = $this->actingAs(User::factory()->create())->getJson('/api/workshop-moments')->assertOk()->getContent();

        $this->assertStringNotContainsString('qr_token', $json);
        $this->assertStringNotContainsString('leader_id', $json);

        $this->getJson('/api/workshop-moments?where[qr_token][contains]=a')->assertStatus(422);
        $this->getJson('/api/workshops?where[leader_id]=x')->assertStatus(422);
    }

    public function test_admin_wijst_leider_aan_en_kan_die_weer_weghalen()
    {
        $gegevens = [
            'name' => 'Andere workshop',
            'leader_email' => ' Jan.Docent@deltion.nl ',
            'rounds' => [1 => ['enabled' => '1', 'capacity' => '10']],
        ];
        $this->actingAs($this->maakAdmin());

        $this->put(route('workshops.update', $this->ander), $gegevens)->assertSessionHasNoErrors();
        $docent = User::where('email', 'jan.docent@deltion.nl')->firstOrFail();

        $this->assertSame('docent', $docent->class);
        $this->assertTrue($docent->isWorkshopLeader());

        $this->put(route('workshops.update', $this->ander), ['leader_email' => ''] + $gegevens)->assertSessionHasNoErrors();

        $this->assertFalse($docent->isWorkshopLeader());
        $this->actingAs($docent)->get('/workshopleider')->assertForbidden();
    }

    public function test_leider_komt_na_inloggen_op_zijn_dashboard()
    {
        $this->leider->forceFill([
            'login_code_hash' => Hash::make('123456'),
            'login_code_expires_at' => now()->addMinutes(5),
        ])->save();

        $this->post('/auth/login-code/verify', ['email' => $this->leider->email, 'code' => '123456'])
            ->assertRedirect(route('leader.dashboard'));
    }

    public function test_leider_telt_niet_mee_als_student()
    {
        $student = User::factory()->create(['name' => 'Echte Student']);
        $data = $this->actingAs($this->maakAdmin())->getJson('/api/statistics')->assertOk()->json('data');

        $this->assertSame(User::where('class', '!=', 'docent')->count() - 2, $data['total_students']);
        $this->assertNotContains($this->leider->id, array_column($data['students_without_workshops'], 'id'));
        $this->assertModelExists($student);
    }
}
