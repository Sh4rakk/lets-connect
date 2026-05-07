<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $isLocal = app()->environment('local');

        $scriptSrc = ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'blob:'];
        $connectSrc = [
            "'self'",
            'https://o4511341734395904.ingest.de.sentry.io',
        ];

        if ($isLocal) {
            // Allow Vite dev server + HMR websocket during local development.
            $connectSrc[] = 'http://127.0.0.1:5173';
            $connectSrc[] = 'ws://127.0.0.1:5173';
        }

        $csp = implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSrc),
            'script-src-elem '.implode(' ', $scriptSrc),
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net",
            "img-src 'self' data: https://xerte.deltion.nl https://images.unsplash.com https://primary.jwwb.nl",
            'connect-src '.implode(' ', $connectSrc),
        ]);

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}







