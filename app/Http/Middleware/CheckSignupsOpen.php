<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckSignupsOpen
{
    public function handle(Request $request, Closure $next)
    {
        $signupsOpen = Setting::where('key', 'signups_open')->first();
        
        if (!$signupsOpen || $signupsOpen->value == '0') {
            return redirect('/login')->with('error', 'Registrations are currently closed.');
        }

        return $next($request);
    }
}
