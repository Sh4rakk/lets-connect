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
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', 'https://fonts.bunny.net'];
        $connectSrc = [
            "'self'",
            'https://o4511341734395904.ingest.de.sentry.io',
        ];

        if ($isLocal) {
            // Allow Vite dev server + HMR websocket during local development.
            $scriptSrc[] = 'http://127.0.0.1:5173';
            $styleSrc[] = 'http://127.0.0.1:5173';
            $connectSrc[] = 'http://127.0.0.1:5173';
            $connectSrc[] = 'ws://127.0.0.1:5173';
        }

        // Optionally allow Google Analytics / Tag Manager origins when enabled.
        // Toggle via config/services.php or .env (set SERVICES_ANALYTICS_ENABLED or add analytics key).
        // Prefer config() so the check works when config is cached.
        if (config('services.analytics.enabled', false)) {
            // Measurement Protocol / mp/collect requires connect-src to allow google-analytics origin
            $connectSrc[] = 'https://www.google-analytics.com';

            // If you load analytics scripts (gtag, gtm), allow their script origins too
            $scriptSrc[] = 'https://www.googletagmanager.com';
            $scriptSrc[] = 'https://www.google-analytics.com';
        }

        // Allow client-side mailing APIs when configured (Resend / Postmark / SES)
        // These are only added when the corresponding service credentials are present in config.
        if (config('services.resend.key')) {
            $connectSrc[] = 'https://api.resend.com';
        }

        if (config('services.postmark.token')) {
            $connectSrc[] = 'https://api.postmarkapp.com';
        }

        // If using SES via AWS SDK from the client (rare), allow the regional SES endpoint.
        // This will add an origin like https://email.eu-west-1.amazonaws.com when ses.region is set.
        if (config('services.ses.key') && config('services.ses.region')) {
            $region = config('services.ses.region');
            $connectSrc[] = "https://email.$region.amazonaws.com";
        }

        $csp = implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSrc),
            'script-src-elem '.implode(' ', $scriptSrc),
            'style-src '.implode(' ', $styleSrc),
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







