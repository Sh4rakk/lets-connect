<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('workshop-dashboard')">Terug naar workshops</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">QR-code workshop</h1>
    </x-slot>

    <div class="mx-auto max-w-xl p-6">
        @if (session('status'))
            <p class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('status') }}</p>
        @endif

        <div class="bg-white rounded-lg shadow p-6 text-center">
            <h2 class="text-xl font-bold text-deltion-blue-900">{{ $workshop->name }}</h2>
            <p class="text-gray-600">{{ $workshop->location }}</p>

            <div class="mx-auto my-4 w-64 [&>svg]:w-full [&>svg]:h-auto">
                {!! $qrSvg !!}
            </div>

            <p class="text-sm text-gray-500 break-all">{{ $scanUrl }}</p>
        </div>

        <form method="POST" action="{{ route('workshop.qr.regenerate', $workshop) }}" class="mt-4 text-center"
              onsubmit="return confirm('Weet je het zeker? De huidige QR-code werkt daarna niet meer.')">
            @csrf
            <button type="submit" class="px-4 py-2 text-white rounded bg-deltion-blue-900">Nieuwe QR-code maken</button>
        </form>
    </div>
</x-app-layout>
