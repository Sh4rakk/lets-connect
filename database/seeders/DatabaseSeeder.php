<?php

namespace Database\Seeders;

use App\Models\Bookings;
use App\Models\User;
use App\Models\Moment;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $adminId = Str::uuid()->toString();
        User::insert([
            'id' => $adminId,
            'name' => 'admin',
            'email' => 'admin@example.com',
            'class' => 'OSD2A'
        ]);

        $userId = Str::uuid()->toString();
        DB::table('users')->insert([
            'id' => $userId,
            'name' => 'user',
            'email' => 'user@example.com',
            'class' => 'OSD2A'
        ]);

        User::find($adminId)->addRole('admin');
        User::find($userId)->addRole('user');

        Moment::insert(['time' => '13:00 - 13:45']);
        Moment::insert(['time' => '13:45 - 14:30']);
        Moment::insert(['time' => '15:00 - 15:45']);

        foreach (Workshop::all() as $workshop)
        {
            WorkshopMoment::insert([
                'moment_id' => 1,
                'workshop_id' => $workshop->id,
            ]);
            WorkshopMoment::insert([
                'moment_id' => 2,
                'workshop_id' => $workshop->id,
            ]);
            WorkshopMoment::insert([
                'moment_id' => 3,
                'workshop_id' => $workshop->id,
            ]);
        }

        Bookings::factory()->count(50)->create();
    }
}
