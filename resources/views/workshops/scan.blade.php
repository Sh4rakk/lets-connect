<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $workshop->name }} - Let's Connect</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-100 text-gray-900">
    <main class="mx-auto max-w-xl p-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-deltion-blue-900">{{ $workshop->name }}</h1>

            <div class="mt-4 space-y-1 text-sm">
                <p><span class="font-semibold">Locatie:</span> {{ $workshop->location ?: 'Onbekend' }}</p>
                <p><span class="font-semibold">Capaciteit:</span> {{ $workshop->capacity ? $workshop->capacity.' personen' : 'Onbekend' }}</p>
                @if ($workshop->workshopMoments->isNotEmpty())
                    <p><span class="font-semibold">Tijden:</span> {{ $workshop->workshopMoments->map(fn ($wm) => $wm->moment?->time)->filter()->join(', ') }}</p>
                @endif
            </div>

            @if ($workshop->full_description)
                <p class="mt-4 text-gray-700">{{ trim($workshop->full_description) }}</p>
            @endif

            <hr class="my-4">

            @auth
                @if ($myMoments->isNotEmpty())
                    <p class="font-semibold">Je bent ingeschreven voor deze workshop ({{ $myMoments->join(', ') }}).</p>
                @else
                    <p class="font-semibold">Je bent niet ingeschreven voor deze workshop.</p>
                @endif
                <p class="text-sm text-gray-500">Ingelogd als {{ auth()->user()->name }}</p>
            @else
                <p>Log in om je aanwezigheid te registreren.</p>
                <a href="{{ route('login') }}" class="mt-2 inline-block px-4 py-2 text-white rounded bg-deltion-blue-900">Inloggen</a>
            @endauth
        </div>
    </main>
</body>
</html>
