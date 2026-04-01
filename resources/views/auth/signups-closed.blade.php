<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <div class="full-page-background"></div>

    <div class="login-container m-auto !h-auto">
        <h1 class="text-2xl font-bold">Inschrijvingen gesloten</h1>
        <p>Heb je vragen of wil je een wijziging doorgeven? Mail ons op <a class="text-deltion-blue-900 underline font-semibold" href="mailto:{{ $contactEmail ?? config('mail.from.address') }}">{{ $contactEmail ?? config('mail.from.address') }}</a>.</p>

        @if(Auth::check() && isset($bookings) && $bookings->count())
            <div class="bg-gray-100 border-l-2 border-gray-300 p-4 rounded text-left">
                <p class="font-semibold text-gray-800 mb-2">Jouw huidige inschrijvingen</p>
                <div class="space-y-2 text-sm text-gray-700">
                    @foreach($bookings->sortBy('workshopMoments.moment_id') as $booking)
                        <div class="flex flex-col">
                            <span class="font-semibold">Ronde {{ $booking->workshopMoments->moment_id }} ({{ $booking->workshopMoments->moment->time }})</span>
                            <span>{{ $booking->workshopMoments->workshop->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(Auth::check() && isset($bookings) && $bookings->count() == 0)
            <div class="bg-gray-100 border-l-2 border-gray-300 p-4 rounded text-left">
                <p class="font-semibold text-gray-800 mb-2">U bent nergens ingeschreven</p>
                <span>U bent ingelogd als: <b>{{Auth::user()->name}}</b></span>
            </div>
        @endif

        @if(Auth::check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-secondary-button type="submit" class=" text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">Terug naar login</x-secondary-button>
            </form>
        @else
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">Terug naar login</a>
        @endif
    </div>
</x-guest-layout>
