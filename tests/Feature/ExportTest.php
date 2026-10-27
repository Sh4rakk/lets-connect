<?php

namespace Tests\Feature;

use App\Models\Bookings;
use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private function maakAdmin()
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    // de exports disk met een echte export en een "geheim" bestand buiten de zipped_exports map
    private function maakExportBestanden()
    {
        Storage::fake('exports');
        Storage::disk('exports')->put('zipped_exports/lets-connect-klassen-2026-01-01_10-00-00.zip', 'zip inhoud');
        Storage::disk('exports')->put('geheim.zip', 'mag je niet zien');
        Storage::disk('exports')->put('generated_exports/export-1/klas.xlsx', 'mag je ook niet zien');
    }

    public function test_workshop_export_werkt()
    {
        $admin = $this->maakAdmin();

        DB::table('moments')->insert([
            ['id' => 1, 'time' => '09:00'],
            ['id' => 2, 'time' => '10:00'],
        ]);
        $workshop = Workshop::create(['name' => 'Van mbo naar hbo', 'capacity' => 10]);
        $metBoeking = WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);
        WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 2]);
        Bookings::create(['wm_id' => $metBoeking->id, 'student_id' => User::factory()->create()->id]);

        // de link op het workshop overzicht doet zelf ook urlencode
        $response = $this->actingAs($admin)->get(route('export-workshop', ['workshopName' => urlencode('Van mbo naar hbo')]));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_workshop_export_werkt_met_rare_tekens_in_de_naam()
    {
        $admin = $this->maakAdmin();

        // zo staat er echt een workshop in de database, met een enter aan het eind
        $naam = "Dare to create: Hoe kom je aan je eerste opdracht?\n";
        DB::table('moments')->insert(['id' => 1, 'time' => '09:00']);
        $workshop = Workshop::create(['name' => $naam, 'capacity' => 10]);
        WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);

        $response = $this->actingAs($admin)->get(route('export-workshop', ['workshopName' => urlencode($naam)]));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/^attachment; filename="Workshop_Dare_to_create_Hoe_kom_je_aan_je_eerste_opdracht_[0-9_-]+\.xlsx"$/',
            $response->headers->get('Content-Disposition')
        );
    }

    public function test_student_kan_geen_export_downloaden()
    {
        $this->maakExportBestanden();
        $student = User::factory()->create();

        $this->actingAs($student)->get('/files/download/lets-connect-klassen-2026-01-01_10-00-00.zip')->assertForbidden();
        $this->actingAs($student)->delete('/files/delete/lets-connect-klassen-2026-01-01_10-00-00.zip')->assertForbidden();
        Storage::disk('exports')->assertExists('zipped_exports/lets-connect-klassen-2026-01-01_10-00-00.zip');
    }

    public function test_admin_kan_export_downloaden_en_verwijderen()
    {
        $this->maakExportBestanden();
        $admin = $this->maakAdmin();

        $this->actingAs($admin)->get('/files/download/lets-connect-klassen-2026-01-01_10-00-00.zip')
            ->assertOk()
            ->assertDownload('lets-connect-klassen-2026-01-01_10-00-00.zip');

        $this->actingAs($admin)->delete('/files/delete/lets-connect-klassen-2026-01-01_10-00-00.zip')
            ->assertSessionHas('success');
        Storage::disk('exports')->assertMissing('zipped_exports/lets-connect-klassen-2026-01-01_10-00-00.zip');
    }

    public function test_kan_geen_bestanden_buiten_de_export_map_downloaden()
    {
        $this->maakExportBestanden();
        $admin = $this->maakAdmin();

        $paden = [
            '..%2Fgeheim.zip',
            '..%5Cgeheim.zip',
            '..%2F..%2F..%2F.env',
            '..%5C..%5C..%5C.env',
            '%2E%2E%2Fgeheim.zip',
            '..',
            'geheim.txt',
            '..%2Fgenerated_exports%2Fexport-1%2Fklas.xlsx',
        ];

        foreach ($paden as $pad) {
            $this->actingAs($admin)->get('/files/download/' . $pad)->assertNotFound();
        }
    }

    public function test_kan_geen_bestanden_buiten_de_export_map_verwijderen()
    {
        $this->maakExportBestanden();
        $admin = $this->maakAdmin();

        foreach (['..%2Fgeheim.zip', '..%5Cgeheim.zip', '%2E%2E%2Fgeheim.zip'] as $pad) {
            $response = $this->actingAs($admin)->delete('/files/delete/' . $pad);
            $this->assertNotEquals(200, $response->getStatusCode());
            $this->assertFalse(session()->has('success'));
        }

        Storage::disk('exports')->assertExists('geheim.zip');
        Storage::disk('exports')->assertExists('zipped_exports/lets-connect-klassen-2026-01-01_10-00-00.zip');
    }
}
