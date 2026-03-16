<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSignupsOpen
{
    public function handle(Request $request, Closure $next)
    {
        $signupsOpen = Setting::where('key', 'signups_open')->first();
        $signupsClosed = !$signupsOpen || $signupsOpen->value === '0';

        // Allow admins (or any authenticated user you want to exempt) to proceed even when closed.
        if ($signupsClosed && Auth::check() && Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        // If signups are closed and no exemption applies, show the notice page.
        if ($signupsClosed) {
            $bookings = Auth::check()
                ? Auth::user()->bookings()->with('workshopMoments.workshop', 'workshopMoments.moment')->get()
                : null;

            return response()->view('auth.signups-closed', [
                'contactEmail' => config('mail.from.address'),
                'bookings' => $bookings,
            ], 200);
        }

        return $next($request);
    }
}
