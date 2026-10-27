<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    private function maakAdmin()
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        return $admin;
    }

    private function maakWorkshopMoment()
    {
        DB::table('moments')->insert(['id' => 1, 'time' => '09:00']);
        $workshop = Workshop::create(['name' => 'Workshop 1', 'capacity' => 10]);

        return WorkshopMoment::create(['workshop_id' => $workshop->id, 'moment_id' => 1]);
    }

    public function test_student_kan_admin_api_niet_gebruiken()
    {
        $student = User::factory()->create();
        $wm = $this->maakWorkshopMoment();

        $this->actingAs($student)->getJson('/api/users')->assertForbidden();
        $this->actingAs($student)->getJson('/api/users/' . $student->id)->assertForbidden();
        $this->actingAs($student)->patchJson('/api/users/' . $student->id . '/bookings', [])->assertForbidden();
        $this->actingAs($student)->getJson('/api/classes')->assertForbidden();
        $this->actingAs($student)->getJson('/api/statistics')->assertForbidden();
        $this->actingAs($student)->postJson('/api/settings/toggle-signups')->assertForbidden();
        $this->actingAs($student)->getJson('/api/workshop-moments/' . $wm->id)->assertForbidden();
    }

    public function test_admin_kan_admin_api_gebruiken()
    {
        $admin = $this->maakAdmin();
        $wm = $this->maakWorkshopMoment();

        $this->actingAs($admin)->getJson('/api/users')->assertOk();
        $this->actingAs($admin)->getJson('/api/users/' . $admin->id)->assertOk();
        $this->actingAs($admin)->getJson('/api/classes')->assertOk();
        $this->actingAs($admin)->getJson('/api/statistics')->assertOk();
        $this->actingAs($admin)->getJson('/api/workshop-moments/' . $wm->id)->assertOk();
    }

    public function test_admin_mag_wel_relaties_laden()
    {
        // zelfde request als in BookingTest, daar krijgt een student 422
        $admin = $this->maakAdmin();
        $this->maakWorkshopMoment();

        $this->actingAs($admin)->getJson('/api/workshop-moments?with[bookings]=1')->assertOk();
    }

    public function test_login_code_is_nooit_zichtbaar_in_de_api()
    {
        $admin = $this->maakAdmin();
        $student = User::factory()->create(['login_code_hash' => 'geheim']);

        $response = $this->actingAs($admin)->getJson('/api/users/' . $student->id);

        $response->assertOk();
        $response->assertJsonMissingPath('data.login_code_hash');

        // ook niet op filteren
        $this->actingAs($admin)->getJson('/api/users?where[login_code_hash]=geheim')->assertStatus(422);
    }

    public function test_admin_kan_studenten_zoeken_op_naam()
    {
        $admin = $this->maakAdmin();
        User::factory()->create(['name' => 'Fokke Test']);
        User::factory()->create(['name' => 'Iemand Anders']);

        $response = $this->actingAs($admin)->getJson('/api/users?search[fields][]=name&search[query]=fokke');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Fokke Test']);
    }
}
