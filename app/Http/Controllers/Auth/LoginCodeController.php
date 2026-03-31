<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        return view('auth.verify-otp', [
            'email' => $request->query('email'),
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

        /** @var User|null $authUser */
        $authUser = $request->user();

        // If logged in, only allow OTP request for your own email (prevents swapping emails).
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
            throw ValidationException::withMessages([
                'code' => 'The code is invalid or expired.',
            ]);
        }

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
