<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Workshop Overzicht</h1>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('/css/wdashboard.css') }}">

    @php
        $workshopsByName = $workshopmoments->groupBy('workshop.name');
    @endphp

    {{-- Inschrijvingen open/dicht gaat via de api, zie resources/js/admin/workshopsOverview.js --}}
    <div class="container mx-auto p-6 max-w-5xl"
         x-data="workshopsOverview({ signupsOpen: @js(\App\Models\Setting::signupsOpen()), workshopNames: @js($workshopsByName->keys()) })">
    <div class="flex justify-between items-center mb-6">

        <button type="button"
                @click="toggleSignups()"
                :disabled="toggling"
                class="px-6 py-2 text-white font-semibold rounded-lg transition-colors"
                :class="signupsOpen ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                x-text="signupsOpen ? '✓ Inschrijvingen Open' : '✗ Inschrijvingen Gesloten'">
        </button>
        <form>
            <a href="{{ route('export-all-users') }}" class="px-6 py-2 text-white font-semibold rounded-lg bg-green-600">
                Alle gebruikers exporteren
            </a>
        </form>
    </div>

    <template x-if="message">
        <div class="mb-4 p-4 rounded-lg relative flex items-start" :class="message.color === 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
            <div class="flex-1" x-text="message.text"></div>

            <button
                type="button"
                @click="message = null"
                class="ml-3 -mt-1 -mr-1 focus:outline-none transition-colors"
                aria-label="Sluit bericht"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>

        <button id="toggleAllBtn" class="px-6 py-2 mb-2 text-white font-semibold rounded-lg bg-deltion-blue-900" @click="toggleAll()" x-text="anyOpen ? 'Alles inklappen' : 'Alles uitklappen'"></button>

     @foreach ($workshopsByName as $workshopName => $workshopMoments)
        <div class="border border-black-300 rounded-lg mb-2">
            <button class="w-full text-left p-6 accordion-btn bg-deltion-blue-900 text-white font-semibold flex justify-between items-center transition-all duration-300"
                    @click="toggle(@js($workshopName))">
                <span>{{ $workshopName }}</span>
                <span class="transition-transform transform" x-text="open[@js($workshopName)] ? '−' : '+'"></span>
            </button>

            {{-- wdashboard.css klapt open/dicht met de class "open" (met animatie) --}}
            <div class="p-6 border-t border-black-300 bg-white accordion-content" :class="{ 'open': open[@js($workshopName)] }">
                <div class="mb-4">
                        <a href="{{ route('export-workshop', ['workshopName' => urlencode($workshopName)]) }}" class="px-6 py-2 text-white font-semibold rounded-lg bg-green-600">{{ "Exporteren naar excel" }}</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600"> Tijd</th>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600"> Locatie</th>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600"> Capaciteit</th>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600"> Boekingen</th>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-4 text-left border-b text-sm font-semibold text-gray-600">Actie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workshopMoments as $wm)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->moment->time }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->location }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->capacity }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->bookings_count }}</td>

                                    <td class="px-6 py-4 border-b text-sm">
                                        @if ($wm->bookings_count < $wm->workshop->capacity)
                                            <span class="text-green-600 font-semibold">✅ Nog plek</span>
                                        @else
                                            <span class="text-red-600 font-semibold">❌ Vol</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 border-b text-sm">
                                        <a href="{{ route('workshop-moment.showbookings', ['wsm' => $wm]) }}" class="text-deltion-blue-600 font-semibold hover:underline">🔗 Bekijk</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
</x-app-layout>
