<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'ends_with:@st.deltion.nl', 'unique:users,email'],
            'klas' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'class' => $request->klas,
            // 'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));


        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            // migration-backed columns
            'login_code_hash' => Hash::make($code),
            'login_code_expires_at' => Carbon::now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new LoginCode($code));

        return redirect()
            ->route('auth.verify-otp', ['email' => $user->email])
            ->with('status', 'We hebben een code naar je e-mailadres gestuurd.');
    }
}
