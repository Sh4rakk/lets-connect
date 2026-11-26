<x-app-layout>
    <x-slot name="leftHeader">
        <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma" target="_blank" class="flex gap-1">
            <p class="text-deltion-blue-900 font-semibold text-xl">Let's</p>
            <p class="text-deltion-orange-900 font-semibold text-xl">Connect</p>
        </a>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workshop Dashboard</h2>
    </x-slot>

    <div class="flex min-h-full items-start justify-center px-4 py-10 sm:py-16">
        <div class="w-full max-w-lg rounded-xl border border-gray-200 bg-white p-6 text-center sm:p-8">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-2xl font-bold text-green-700" aria-hidden="true">✓</span>

            <h1 class="mt-4 text-2xl font-bold text-deltion-blue-900">Je inschrijving is opgeslagen</h1>
            <p class="mt-2 text-gray-600">Je krijgt een bevestiging in je mail. Dit zijn je workshops:</p>

            <x-booking-summary :bookings="$bookings" class="mt-6" />

            <p class="mt-6 text-sm text-gray-500">
                Wil je iets wijzigen? Mail naar
                <a href="mailto:{{ config('mail.from.address') }}" class="font-semibold text-deltion-blue-900 underline">{{ config('mail.from.address') }}</a>.
            </p>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Uitloggen</button>
            </form>
        </div>
    </div>
</x-app-layout>
