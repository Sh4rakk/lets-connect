<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LoginCodeController extends Controller
{
    // GET /auth/verify-otp
    public function showVerifyForm(Request $request): View
    {
        /** @var User|null $authUser */
        $authUser = $request->user();

        if (
            $authUser &&
            !$authUser->hasVerifiedEmail() &&
            !(method_exists($authUser, 'hasRole') && $authUser->hasRole('admin'))
        ) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $email = $request->query('email') ?: $request->old('email');

        $requestKey = 'request-code:' . $email . ':' . $request->ip();
        $verifyKey = 'verify-code:' . $email . ':' . $request->ip();


        $cooldownSeconds = max(RateLimiter::availableIn($requestKey), RateLimiter::availableIn($verifyKey), 30);

        return view('auth.verify-otp', [
            'email' => $email,
            'cooldownSeconds' => $cooldownSeconds,
        ]);
    }

    private function issueAndSendCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            // migration-backed columns
            'login_code_hash' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new LoginCode($code));
    }

    // POST /auth/login-code/request
    public function requestCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $key = 'request-code:' . $data['email'] . ':' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            throw ValidationException::withMessages([
                'email' => "Te veel pogingen. Wacht {$minutes} minuut/minuten ({$seconds} seconden) voordat je een nieuwe code aanvraagt.",
            ]);
        }

        /** @var User|null $authUser */
        $authUser = $request->user();


        if ($authUser) {
            $data['email'] = $authUser->email;
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'No account found for this email.',
            ]);
        }


        if ($user->hasRole('admin')) {
            return redirect()
                ->route('admin.login')
                ->with('admin_email', $user->email);
        }

        if ($user->login_code_expires_at) {
            $expiresAt = Carbon::parse($user->login_code_expires_at);
            if ($expiresAt->isFuture() && Carbon::now()->diffInSeconds($expiresAt) > 570) {
                return redirect()
                    ->route('auth.verify-otp', ['email' => $data['email']])
                    ->with('status', 'We hebben een code naar je e-mailadres gestuurd.');
            }
        }


        $lock = Cache::lock('issue-code-' . $user->id, 10);

        if (!$lock->get()) {
            return redirect()
                ->route('auth.verify-otp', ['email' => $data['email']])
                ->with('status', 'We hebben een code naar je e-mailadres gestuurd.');
        }

        RateLimiter::hit($key, 60); // 1 minute lockout after 3 attempts

        try {
            $this->issueAndSendCode($user);
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'email' => 'Could not send email. Check your mail settings/logs.',
            ]);
        }

        return redirect()
            ->route('auth.verify-otp', ['email' => $data['email']])
            ->with('status', 'We hebben een code naar je e-mailadres gestuurd.');
    }

    // POST /auth/login-code/verify
    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
        ]);

        $key = 'verify-code:' . $data['email'] . ':' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            throw ValidationException::withMessages([
                'code' => "Te veel foute pogingen. Wacht {$minutes} minuten voordat je het opnieuw probeert.",
            ]);
        }

        /** @var User|null $user */
        $user = User::query()->where('email', $data['email'])->first();

        $code = trim((string) $data['code']);

        if (
            !$user ||
            !$user->login_code_hash ||
            !$user->login_code_expires_at ||
            !Hash::check($code, (string) $user->login_code_hash) ||
            Carbon::parse($user->login_code_expires_at)->isPast()
        ) {
            RateLimiter::hit($key, 300);
            throw ValidationException::withMessages([
                'code' => 'The code is invalid or expired.',
            ]);
        }

        RateLimiter::clear($key);

        $user->forceFill([
            'login_code_hash' => null,
            'login_code_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?: Carbon::now(),
            'last_login_at' => Carbon::now(),
        ])->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->hasRole('admin')) {
            return redirect()->route('selectionDashboard');
        }

        return redirect()->intended('/dashboard');
    }
}
