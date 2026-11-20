<?php

namespace Tests\Feature;

use App\Models\Workshop;
use Database\Seeders\WorkshopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopSeederTest extends TestCase
{
    use RefreshDatabase;

    // de scraper draait niet in de tests, dus hier moeten de placeholders komen
    public function test_seeder_gebruikt_placeholders_als_de_scraper_niet_draait()
    {
        $this->seed(WorkshopSeeder::class);

        $this->assertSame(6, Workshop::count());
        $this->assertSame(0, Workshop::whereNull('qr_token')->count());
        $this->assertSame(0, Workshop::where('capacity', '<=', 0)->count());
    }
}
