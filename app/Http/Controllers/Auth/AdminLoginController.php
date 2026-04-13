<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $email = session('admin_email');
        if (!$email) {
            return redirect()->route('login');
        }

        return view('auth.admin-login', ['email' => $email]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                $request->session()->regenerate();
                return redirect()->route('selectionDashboard');
            }

            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'You are not authorized to login from here.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }
}
