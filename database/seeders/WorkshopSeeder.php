<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class WorkshopSeeder extends Seeder
{
    /**
     * Placeholder workshops voor als de scraper niet draait, zodat een verse
     * installatie en db:migrate gewoon werken.
     * Vervang deze door de echte Let's Connect workshops zodra die er zijn.
     */
    protected function workshopTemplates(): array
    {
        $workshops = [];

        for ($i = 1; $i <= 6; $i++) {
            $workshops[] = [
                'name' => 'Placeholder workshop ' . $i,
                'full_description' => 'Dit is een placeholder. De echte workshop komt hier zodra de inhoud bekend is.',
                'capacity' => 20,
                'location' => 'Lokaal ' . $i,
                'image_url' => null,
            ];
        }

        return $workshops;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workshops = $this->scraperData();

        if ($workshops === null) {
            $this->command?->warn('Scraper niet bereikbaar, placeholder workshops worden gebruikt.');
            $this->seedTemplates();

            return;
        }

        $this->seedFromScraper($workshops);
    }

    // haalt de workshops op bij de scraper, null als die niet draait of rare data geeft
    private function scraperData(): ?array
    {
        try {
            $jsonData = file_get_contents('http://localhost:4001/getData');
        } catch (Throwable) {
            return null;
        }

        $workshops = json_decode((string) $jsonData, true);

        if (!is_array($workshops) || !isset($workshops['Titles'], $workshops['Descriptions'], $workshops['Images'])) {
            return null;
        }

        return $workshops;
    }

    private function seedTemplates(): void
    {
        foreach ($this->workshopTemplates() as $workshop) {
            DB::table('workshops')->insert([
                'id' => (string) Str::uuid(),
                'name' => $workshop['name'],
                'full_description' => $workshop['full_description'],
                'capacity' => $workshop['capacity'],
                'location' => $workshop['location'],
                'image_url' => $workshop['image_url'],
                'qr_token' => Workshop::newQrToken(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedFromScraper(array $workshops): void
    {
        $pdo = DB::connection()->getPdo();

        $arrayIds = [];
        // Insert data into the database
        foreach ($workshops["Titles"] as $workshop) {
            // Generate a UUID for the workshop
            $workshopId = Str::uuid()->toString();
            // Insert workshop into the 'workshops' table
            $stmt = $pdo->prepare("INSERT INTO workshops (id, name, qr_token, created_at, updated_at) VALUES (:id, :name, :qr_token, :created_at, :updated_at)");
            $now = now()->toDateTimeString();
            $stmt->execute([
                'id' => $workshopId,
                'name' => $workshop,
                'qr_token' => Workshop::newQrToken(),
                'created_at' => $now,
                'updated_at' => $now,

            ]);
            array_push($arrayIds, $workshopId);
        }

        $i = 0;
        foreach ($workshops["Descriptions"] as $element) {

            $completeOmschrijving = "";
            foreach ( $element["description"] as $description) {
                    $completeOmschrijving .= " ". $description;
            }

        //    preg_match('/maximaal\s+(\d+)\s+personen/i',$completeOmschrijving, $matches);
            if (preg_match('/maximaal[\s\x{a0}]+(\d+)[\s\x{a0}]+personen/u', $completeOmschrijving, $matches)) {
                $maxPersonen = $matches[1];
            } else {
                $maxPersonen = 0;
            }

            if (preg_match('/((?<=Deze.workshop.vindt.plaats.in:.).*)/miu', $completeOmschrijving, $matches2)) {
                $locatie = $matches2[1];
            } else {
                $locatie = null;
            }

            // var_dump($matches);

            $stmt = $pdo->prepare("UPDATE  workshops SET full_description=:co , capacity=:capacity , location=:location WHERE id=:workshop_id");
            $stmt->execute([
                ':workshop_id' => $arrayIds[$i],
                ':co' => $completeOmschrijving,
                ':capacity' =>  $maxPersonen,
                ':location' => $locatie
            ]);
            $i++;
        }

        $i = 0;
        foreach ($workshops["Images"] as $element) {

            $imageUrl = "";
            foreach ( $element["image"] as $description) {
                    $imageUrl =  $description;
            }

            // var_dump($imageUrl);

            $stmt = $pdo->prepare("UPDATE  workshops SET image_url=:iu  WHERE id=:workshop_id");
            $stmt->execute([
                ':workshop_id' => $arrayIds[$i],
                ':iu' => $imageUrl,
            ]);
            $i++;
        }
    }
}
