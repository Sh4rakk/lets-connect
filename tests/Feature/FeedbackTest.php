<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Moment;
use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    private Workshop $workshop;
    private WorkshopMoment $ronde;
    private WorkshopMoment $andereRonde;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Moment::create(['time' => '13:00 - 13:45']);
        Moment::create(['time' => '13:45 - 14:30']);

        $this->workshop = Workshop::create(['name' => 'Fotografie', 'capacity' => 20]);
        $this->ronde = WorkshopMoment::create(['workshop_id' => $this->workshop->id, 'moment_id' => 1]);
        $this->andereRonde = WorkshopMoment::create(['workshop_id' => $this->workshop->id, 'moment_id' => 2]);
    }

    private function beoordeel(array $extra = [])
    {
        return $this->post(route('feedback.rating', $this->ronde->qr_token), array_merge([
            'rating' => '4',
            'good' => 'Leuke uitleg',
            'better' => 'Meer tijd',
        ], $extra));
    }

    private function beantwoord(array $extra = [])
    {
        return $this->post(route('feedback.questions', $this->ronde->qr_token), array_merge([
            'step_token' => Feedback::value('step_token'),
            'learned' => 'heel_erg',
            'fun' => 'beetje',
            'recommend' => '1',
        ], $extra));
    }

    public function test_scannen_opent_de_feedback_van_de_juiste_ronde()
    {
        $this->followingRedirects()->get($this->ronde->qrUrl())
            ->assertOk()
            ->assertSee('Hoe vond je')
            ->assertSee('Fotografie')
            ->assertSee('Ronde 1');

        $this->get(route('feedback.show', $this->andereRonde->qr_token))->assertSee('Ronde 2');
    }

    public function test_onbekende_code_geeft_404()
    {
        $onbekend = str_repeat('x', 40);

        $this->get(route('feedback.show', $onbekend))->assertNotFound();
        $this->post(route('feedback.rating', $onbekend), ['rating' => '4'])->assertNotFound();
        $this->get(route('feedback.show', $this->workshop->qr_token))->assertNotFound();
        $this->assertSame(0, Feedback::count());
    }

    public function test_beide_stappen_worden_opgeslagen()
    {
        $this->beoordeel()->assertOk()->assertSee('Was de workshop leerzaam?');
        $this->beantwoord()->assertOk()->assertSee('Bedankt voor je feedback!');

        $feedback = Feedback::sole();

        $this->assertSame($this->ronde->id, $feedback->workshop_moment_id);
        $this->assertSame(4, $feedback->rating);
        $this->assertSame('Leuke uitleg', $feedback->good);
        $this->assertSame('Meer tijd', $feedback->better);
        $this->assertSame('heel_erg', $feedback->learned);
        $this->assertSame('beetje', $feedback->fun);
        $this->assertTrue($feedback->recommend);
        $this->assertNull($feedback->step_token);
    }

    public function test_tekst_is_optioneel_en_nee_wordt_opgeslagen()
    {
        $this->post(route('feedback.rating', $this->ronde->qr_token), ['rating' => '1'])->assertOk();
        $this->beantwoord(['recommend' => '0'])->assertOk();

        $this->assertNull(Feedback::sole()->good);
        $this->assertFalse(Feedback::sole()->recommend);
    }

    public function test_eerste_stap_blijft_bewaard_als_je_daarna_stopt()
    {
        $this->beoordeel();

        $this->assertSame(4, Feedback::sole()->rating);
        $this->assertNull(Feedback::sole()->learned);
    }

    public function test_ongeldig_cijfer_wordt_geweigerd()
    {
        foreach (['', '0', '6', 'abc', ['4']] as $cijfer) {
            $this->beoordeel(['rating' => $cijfer])->assertSessionHasErrors('rating');
        }

        $this->beoordeel(['good' => str_repeat('a', 1001)])->assertSessionHasErrors('good');
        $this->assertSame(0, Feedback::count());
    }

    public function test_alle_drie_de_vragen_zijn_verplicht()
    {
        $this->beoordeel();

        $this->beantwoord(['fun' => ''])->assertStatus(422)->assertSee('Beantwoord alle 3 de vragen.');
        $this->beantwoord(['learned' => 'onzin'])->assertStatus(422);
        $this->assertNull(Feedback::sole()->learned);

        $this->beantwoord()->assertOk();
        $this->assertSame('heel_erg', Feedback::sole()->learned);
    }

    public function test_je_kan_een_ronde_maar_een_keer_beoordelen()
    {
        $cookie = 'feedback_'.$this->ronde->id;
        $this->beoordeel()->assertCookie($cookie, '1');

        $this->withCookie($cookie, '1')->get(route('feedback.show', $this->ronde->qr_token))->assertSee('Bedankt voor je feedback!');
        $this->withCookie($cookie, '1')->beoordeel()->assertRedirect(route('feedback.show', $this->ronde->qr_token));
        $this->withCookie($cookie, '1')->get(route('feedback.show', $this->andereRonde->qr_token))->assertSee('Hoe vond je');

        $this->assertSame(1, Feedback::count());
    }

    public function test_antwoorden_kunnen_niet_op_andermans_feedback()
    {
        $this->beoordeel();
        $antwoord = ['learned' => 'niet', 'fun' => 'niet', 'recommend' => '0'];

        $this->beantwoord(['step_token' => 'fout']);
        $this->beantwoord(['step_token' => ['fout']]);
        $this->beantwoord(['step_token' => null]);
        $this->post(route('feedback.questions', $this->andereRonde->qr_token), ['step_token' => Feedback::value('step_token')] + $antwoord);
        $this->assertNull(Feedback::sole()->learned);

        $oudeToken = Feedback::value('step_token');
        $this->beantwoord();
        $this->beantwoord(['step_token' => $oudeToken] + $antwoord);
        $this->assertSame('heel_erg', Feedback::sole()->learned);
    }

    public function test_feedback_is_anoniem()
    {
        $student = User::factory()->create();
        $this->actingAs($student);
        $this->beoordeel();
        $this->beantwoord();

        $this->assertEqualsCanonicalizing([
            'id', 'workshop_moment_id', 'rating', 'good', 'better', 'learned',
            'fun', 'recommend', 'step_token', 'created_at', 'updated_at',
        ], Schema::getColumnListing('feedback'));
        $this->assertStringNotContainsString($student->id, Feedback::sole()->toJson());
    }

    public function test_code_van_alle_rondes_laat_je_een_ronde_kiezen()
    {
        $this->get($this->workshop->qrUrl())
            ->assertOk()
            ->assertSee(route('feedback.show', $this->ronde->qr_token))
            ->assertSee(route('feedback.show', $this->andereRonde->qr_token));
    }

    private function maakAdmin(): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    public function test_testpagina_opent_het_formulier_opnieuw()
    {
        $cookie = 'feedback_'.$this->ronde->id;
        $this->actingAs($this->maakAdmin());

        $this->get('/feedback-test')->assertOk()->assertSee('Fotografie')->assertSee('Feedback geven');
        $this->withCookie($cookie, '1')->post(route('feedback.test.start', $this->ronde))
            ->assertRedirect(route('feedback.show', $this->ronde->qr_token))
            ->assertCookieExpired($cookie);
    }

    public function test_testpagina_is_alleen_voor_admins()
    {
        Role::firstOrCreate(['name' => 'admin']);

        $this->get('/feedback-test')->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/feedback-test')->assertForbidden();
        $this->post(route('feedback.test.start', $this->ronde))->assertForbidden();
    }

    public function test_testpagina_bestaat_niet_in_productie()
    {
        $this->actingAs($this->maakAdmin());
        $this->app['env'] = 'production';

        $this->get('/feedback-test')->assertNotFound();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('feedback.test.start', $this->ronde))->assertNotFound();
    }

    public function test_workshop_met_feedback_kan_niet_weg()
    {
        $admin = $this->maakAdmin();
        $this->beoordeel();
        $this->actingAs($admin);

        $this->delete(route('workshops.destroy', $this->workshop))->assertSessionHasErrors('delete');
        $this->put(route('workshops.update', $this->workshop), [
            'name' => 'Fotografie',
            'rounds' => [2 => ['enabled' => '1', 'capacity' => '5']],
        ])->assertSessionHasErrors('rounds.1.capacity');

        $this->assertSame(1, Feedback::count());
        $this->assertSame(2, $this->workshop->workshopMoments()->count());
    }
}
