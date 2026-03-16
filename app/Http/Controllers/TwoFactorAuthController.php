<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Mail\TwoFactorMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthController extends Controller
{
    /**
     * Show the two-factor authentication challenge page.
     */
    public function showChallenge()
    {
        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        // Check if the code matches and hasn't expired
        if (
            $request->session()->get('two_factor_code') === $request->code && 
            now()->lt($request->session()->get('two_factor_expires_at'))
        ) {
            // Clear the session data
            $request->session()->forget(['two_factor_code', 'two_factor_expires_at']);
            
            // Mark the authentication as complete
            $request->session()->put('auth.two_factor_confirmed', true);
            
            // Redirect to the intended page
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        return back()->withErrors([
            'code' => 'The provided verification code was invalid or has expired.',
        ]);
    }

    /**
     * Resend the two-factor authentication code.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();
        
        // Generate a new random 6-digit code
        $code = sprintf('%06d', random_int(0, 999999));
        
        // Update the code in the session
        $request->session()->put('two_factor_code', $code);
        $request->session()->put('two_factor_expires_at', now()->addMinutes(5));
        
        // Prepare email content
        $subject = 'Your New Two-Factor Authentication Code';
        $body = '<h2>Two-Factor Authentication</h2>
                <p>Hello ' . $user->name . ',</p>
                <p>You requested a new verification code.</p>
                <p>Your two-factor authentication code is: <strong style="font-size: 24px; letter-spacing: 4px;">' . $code . '</strong></p>
                <p>This code will expire in 5 minutes.</p>
                <p>If you did not request this code, please secure your account immediately.</p>';
        
        // Send the email with the code
        Mail::to($user->email)->send(new SendMail($subject, $body));
        
        return back()->with('status', 'A new verification code has been sent to your email address.');
    }
}