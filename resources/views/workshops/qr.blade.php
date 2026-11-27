@php
    $isAdmin = auth()->user()->hasRole('admin');
    $tab = 'px-3 py-2 text-sm border rounded-md';
@endphp

<x-workshop-layout title="QR-code workshop"
                   :back-url="$isAdmin ? route('workshop-dashboard') : route('leader.dashboard')"
                   :back-label="$isAdmin ? 'Workshop overzicht' : 'Mijn workshops'">
    <div class="mx-auto max-w-xl">
        @if (session('status'))
            <p class="mb-4 p-3 rounded-md bg-green-100 text-green-800 text-sm">{{ session('status') }}</p>
        @endif

        <nav class="flex flex-wrap justify-center gap-2 mb-4 print:hidden" aria-label="Rondes">
            @foreach ($rounds as $wm)
                <a href="{{ route('workshop-moment.qr', $wm) }}" @if ($round?->id === $wm->id) aria-current="page" @endif
                   class="{{ $tab }} {{ $round?->id === $wm->id ? 'bg-deltion-blue-900 border-deltion-blue-900 text-white' : 'bg-white border-gray-300 hover:bg-gray-50' }}">Ronde {{ $wm->moment_id }}</a>
            @endforeach
            @if ($isAdmin)
                <a href="{{ route('workshop.qr', $workshop) }}" @if (! $round) aria-current="page" @endif
                   class="{{ $tab }} {{ $round ? 'bg-white border-gray-300 hover:bg-gray-50' : 'bg-deltion-blue-900 border-deltion-blue-900 text-white' }}">Alle rondes</a>
            @endif
        </nav>

        <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
            <h2 class="text-xl font-bold">{{ $workshop->name }}</h2>
            @if ($round)
                <p class="font-semibold text-deltion-orange-800">Ronde {{ $round->moment_id }} ({{ $round->moment?->time }})</p>
            @endif
            <p class="text-gray-600">{{ $workshop->location }}</p>

            <div class="mx-auto my-4 w-64 max-w-full [&>svg]:w-full [&>svg]:h-auto">
                {!! $qrSvg !!}
            </div>

            <p class="text-sm text-gray-500 break-all">{{ $scanUrl }}</p>
        </div>

        <div class="mt-4 flex flex-wrap justify-center gap-2 text-sm print:hidden">
            <button type="button" onclick="window.print()" class="px-4 py-2 font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50">Afdrukken</button>
            <form method="POST" action="{{ $round ? route('workshop-moment.qr.regenerate', $round) : route('workshop.qr.regenerate', $workshop) }}"
                  onsubmit="return confirm('Weet je het zeker? De huidige QR-code werkt daarna niet meer.')">
                @csrf
                <button type="submit" class="px-4 py-2 font-medium text-white rounded-md bg-deltion-blue-900 hover:bg-deltion-blue-800">Nieuwe QR-code maken</button>
            </form>
        </div>
    </div>
</x-workshop-layout>
