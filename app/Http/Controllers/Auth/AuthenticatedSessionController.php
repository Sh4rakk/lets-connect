<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\SendMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Emails that require 2FA verification
     */
    protected $emailsRequiring2FA = [
        'damianvandernat@gmail.com',
    ];

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if the authenticated user's email requires 2FA
        $user = Auth::user();
        if (in_array($user->email, $this->emailsRequiring2FA)) {
            // Generate a random 6-digit code
            $code = sprintf('%06d', random_int(0, 999999));
            
            // Store the code in the session
            $request->session()->put('two_factor_code', $code);
            $request->session()->put('two_factor_expires_at', now()->addMinutes(5));
            
            // Prepare email content
            $subject = 'Your Two-Factor Authentication Code';
            $body = '<h2>Two-Factor Authentication</h2>
                    <p>Hello ' . $user->name . ',</p>
                    <p>You are receiving this email because we received a login attempt for your account.</p>
                    <p>Your two-factor authentication code is: <strong style="font-size: 24px; letter-spacing: 4px;">' . $code . '</strong></p>
                    <p>This code will expire in 5 minutes.</p>
                    <p>If you did not request this code, please secure your account immediately.</p>';
            
            // Send the email with the code
            Mail::to($user->email)->send(new SendMail($subject, $body));
            
            // Redirect to the two-factor challenge page
            return redirect()->route('two-factor.challenge');
        }

        // Normal login flow for users not requiring 2FA
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}