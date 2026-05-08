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

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Build CSP header - more permissive in development, strict in production
        if (app()->isLocal()) {
            // Development: Allow Vite dev server on both localhost and 127.0.0.1 on any port
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net http://localhost:* http://127.0.0.1:*; font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net; img-src 'self' data: https://xerte.deltion.nl https://images.unsplash.com https://primary.jwwb.nl; connect-src 'self' https://o4511341734395904.ingest.de.sentry.io http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*";
        } else {
            // Production: Strict CSP
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net; font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net; img-src 'self' data: https://xerte.deltion.nl https://images.unsplash.com https://primary.jwwb.nl; connect-src 'self' https://o4511341734395904.ingest.de.sentry.io";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}







