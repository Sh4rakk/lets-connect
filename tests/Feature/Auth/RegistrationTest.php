<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Setting::query()->create([
            'key' => 'signups_open',
            'value' => '1',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '12345678@st.deltion.nl',
            'klas' => 'SD2A',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('auth.verify-otp', ['email' => '12345678@st.deltion.nl'], false));
    }
}
