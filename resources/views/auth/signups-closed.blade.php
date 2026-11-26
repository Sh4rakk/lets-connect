<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <div class="full-page-background"></div>

    <div class="login-container m-auto !h-auto">
        <h1 class="text-2xl font-bold">Inschrijvingen gesloten</h1>
        <p class="text-gray-600">Heb je vragen of wil je een wijziging doorgeven? Mail ons op <a class="text-deltion-blue-900 underline font-semibold" href="mailto:{{ $contactEmail ?? config('mail.from.address') }}">{{ $contactEmail ?? config('mail.from.address') }}</a>.</p>

        @if(Auth::check() && isset($bookings) && $bookings->count())
            <div class="text-left">
                <p class="mb-2 text-sm font-semibold text-deltion-blue-900">Jouw workshops</p>
                <x-booking-summary :bookings="$bookings" class="bg-white" />
            </div>
        @elseif(Auth::check())
            <p class="text-sm text-gray-600">Je bent ingelogd als <b>{{ Auth::user()->name }}</b> en je staat nergens voor ingeschreven.</p>
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
