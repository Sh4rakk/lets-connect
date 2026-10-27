<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_gaat_naar_registreren_als_inschrijvingen_open_zijn()
    {
        Setting::create(['key' => 'signups_open', 'value' => '1']);

        $this->get('/')->assertRedirect(route('register'));
    }

    public function test_home_gaat_naar_login_als_inschrijvingen_dicht_zijn()
    {
        Setting::create(['key' => 'signups_open', 'value' => '0']);

        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_student_ziet_dashboard_als_inschrijvingen_open_zijn()
    {
        $this->withoutVite();
        Setting::create(['key' => 'signups_open', 'value' => '1']);
        $student = User::factory()->create();

        $this->actingAs($student)->get('/dashboard')->assertOk()->assertViewIs('dashboard');
    }

    public function test_student_ziet_gesloten_pagina_als_inschrijvingen_dicht_zijn()
    {
        $this->withoutVite();
        Setting::create(['key' => 'signups_open', 'value' => '0']);
        $student = User::factory()->create();

        $this->actingAs($student)->get('/dashboard')->assertViewIs('auth.signups-closed');
    }

    public function test_admin_ziet_dashboard_ook_als_inschrijvingen_dicht_zijn()
    {
        $this->withoutVite();
        Setting::create(['key' => 'signups_open', 'value' => '0']);
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->addRole('admin');

        $this->actingAs($admin)->get('/dashboard')->assertViewIs('dashboard');
    }

    public function test_gast_moet_eerst_inloggen_voor_dashboard()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
