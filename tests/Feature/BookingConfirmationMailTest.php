<?php

namespace Tests\Feature;

use App\Mail\SendMail;
use App\Models\Bookings;
use App\Models\Setting;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingConfirmationMailTest extends TestCase
{
    use RefreshDatabase;

    private array $workshops = [];

    protected function setUp(): void
    {
        parent::setUp();

        Setting::create(['key' => 'signups_open', 'value' => '1']);
        DB::table('moments')->insert([
            ['id' => 1, 'time' => '09:00'],
            ['id' => 2, 'time' => '10:00'],
            ['id' => 3, 'time' => '11:00'],
        ]);

        for ($i = 1; $i <= 4; $i++) {
            $workshop = Workshop::create(['name' => 'Workshop ' . $i, 'capacity' => 30, 'location' => 'B1.2' . $i]);

            for ($ronde = 1; $ronde <= 3; $ronde++) {
                WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => $ronde]);
            }

            $this->workshops[] = $workshop;
        }
    }

    private function rounds(int $a = 0, int $b = 1, int $c = 2): array
    {
        return ['rounds' => [1 => $this->workshops[$a]->id, 2 => $this->workshops[$b]->id, 3 => $this->workshops[$c]->id]];
    }

    public function test_opslaan_stuurt_een_bevestigingsmail()
    {
        Mail::fake();
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', $this->rounds())->assertCreated();

        Mail::assertSent(SendMail::class, 1);
    }

    public function test_dezelfde_keuzes_nog_een_keer_opslaan_stuurt_geen_tweede_mail()
    {
        Mail::fake();
        $student = User::factory()->create();

        foreach (range(1, 5) as $ignored) {
            $this->actingAs($student)->postJson('/api/bookings', $this->rounds())->assertCreated();
        }

        Mail::assertSent(SendMail::class, 1);
        $this->assertSame(3, Bookings::where('student_id', $student->id)->count());
    }

    public function test_keuze_veranderen_stuurt_een_nieuwe_bevestiging()
    {
        Mail::fake();
        $student = User::factory()->create();

        $this->actingAs($student)->postJson('/api/bookings', $this->rounds(0, 1, 2));
        $this->actingAs($student)->postJson('/api/bookings', $this->rounds(0, 1, 3));

        Mail::assertSent(SendMail::class, 2);
        $this->assertSame(3, Bookings::where('student_id', $student->id)->count());
    }

    public function test_mislukte_mail_kan_later_opnieuw()
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('smtp down'));
        $student = User::factory()->create();

        // de keuze wordt wel opgeslagen, ook als de mail mislukt
        $this->actingAs($student)->postJson('/api/bookings', $this->rounds())->assertCreated();

        $student->refresh();
        $this->assertNull($student->confirmation_sent_at);
        $this->assertNull($student->confirmation_key);
    }

    public function test_mail_werkt_zonder_ingelogde_gebruiker()
    {
        $student = User::factory()->create(['name' => 'Tineke Palland']);
        $wm = WorkshopMoment::where('workshop_id', $this->workshops[0]->id)->where('moment_id', 1)->first();
        Bookings::create(['wm_id' => $wm->id, 'student_id' => $student->id]);

        $this->assertNull(Auth::user());

        // Mail::fake rendert de mail niet, dus hier echt renderen
        $rendered = (new SendMail('Onderwerp', 'Tekst', $student))->render();

        $this->assertStringContainsString('Beste Tineke Palland', $rendered);
        $this->assertStringContainsString('Workshop 1', $rendered);
        $this->assertStringContainsString('B1.21', $rendered);
    }

    public function test_mail_gebruikt_nog_steeds_de_ingelogde_gebruiker()
    {
        $student = User::factory()->create(['name' => 'Tineke Palland']);

        $rendered = (new SendMail('Onderwerp', 'Tekst'))->render();
        $this->assertStringContainsString('Beste', $rendered);

        $this->actingAs($student);
        $rendered = (new SendMail('Onderwerp', 'Tekst'))->render();
        $this->assertStringContainsString('Beste Tineke Palland', $rendered);
    }

    public function test_resend_command_stuurt_alleen_met_send_en_een_venster()
    {
        Mail::fake();
        $student = User::factory()->create();
        $wm = WorkshopMoment::first();
        Bookings::create(['wm_id' => $wm->id, 'student_id' => $student->id]);

        $this->artisan('bookings:resend-confirmations --send')->assertFailed();
        $this->artisan('bookings:resend-confirmations --all')->assertSuccessful();
        Mail::assertNothingSent();

        $this->artisan('bookings:resend-confirmations --all --send')->assertSuccessful();
        Mail::assertSent(SendMail::class, 1);

        // nu bevestigd, dus nog een keer draaien stuurt niks
        $this->artisan('bookings:resend-confirmations --all --send')->assertSuccessful();
        Mail::assertSent(SendMail::class, 1);
    }
}
