<?php

namespace Database\Seeders;

use App\Models\Bookings;
use App\Models\User;
use App\Models\Moment;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LaratrustSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(WorkshopSeeder::class);
        User::insert([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'class' => 'SD2A'
        ]);

        DB::table('users')->insert([
            'name' => 'user',
            'email' => 'user@example.com',
            'class' => 'SD2A'
        ]);

        User::find(1)->addRole('admin');
        User::find(2)->addRole('user');

        Moment::insert(['id' => '1', 'time' => '13:00 - 13:45']);
        Moment::insert(['id' => '2', 'time' => '13:45 - 14:30']);
        Moment::insert(['id' => '3', 'time' => '15:00 - 15:45']);

        $index = 0;
        foreach (Workshop::all() as $workshop)
        {

            if($index > 2)
            {
                WorkshopMoment::insert([
                    'moment_id' => '1',
                    'workshop_id' => $workshop->id,
                ]);
                WorkshopMoment::insert([
                    'moment_id' => '2',
                    'workshop_id' => $workshop->id,
                ]);
                WorkshopMoment::insert([
                    'moment_id' => '3',
                    'workshop_id' => $workshop->id,
                ]);
            } else {
                WorkshopMoment::insert([
                    'moment_id' => $workshop->id,
                    'workshop_id' => $workshop->id,
                ]);
            }
            $index++;
        }

        Bookings::factory()->count(50)->create();
    }
}
