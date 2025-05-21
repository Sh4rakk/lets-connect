<?php

namespace Database\Seeders;

use Error;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pdo = DB::connection()->getPdo();
        $jsonData = "";
        // Read JSON file
        $jsonFile = 'http://localhost:4001/getData';
        try {
            $jsonData = file_get_contents($jsonFile);
        } catch(Exception $e) {
            throw new Error("JSON data not found. Are you sure the scraper is running?");
        }
        $workshops = json_decode($jsonData, true);

        $arrayIds = [];
        // Insert data into the database
        foreach ($workshops["Titles"] as $workshop) {
            // Insert workshop into the 'workshops' table
            $stmt = $pdo->prepare("INSERT INTO workshops (name) VALUES (:name)");
            $stmt->execute([
                'name' => $workshop      
            ]);
            array_push($arrayIds, $pdo->lastInsertId());
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
