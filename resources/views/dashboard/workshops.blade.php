<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Workshop Overzicht</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">

        @php
            $workshopsByName = $workshopmoments->groupBy('workshop.name');
        @endphp

        @foreach ($workshopsByName as $workshopName => $workshopMoments)
            <div class="border border-black-300 rounded-lg mb-2">
                <button class="w-full text-left p-6 bg-blue-900 text-white font-semibold flex justify-between items-center transition-all duration-300 accordion-btn"
                        onclick="toggleAccordion(this)">
                    <span>{{ $workshopName }}</span>
                    <span class="transition-transform transform">+</span>
                </button>

                <div class="hidden p-6 border-t border-black-300 bg-white accordion-content">
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
                                        <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->room_name }}</td>
                                        <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->capacity }}</td>
                                        <td class="px-6 py-4 border-b text-sm">{{ count($wm->bookings) }}</td>

                                        <td class="px-6 py-4 border-b text-sm">
                                            @if (count($wm->bookings) < $wm->workshop->capacity)
                                                <span class="text-green-600 font-semibold">✅ Nog plek</span>
                                            @else
                                                <span class="text-red-600 font-semibold">❌ Vol</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 border-b text-sm">
                                            <a href="{{ route('workshop-moment.showbookings', ['wsm' => $wm]) }}"
                                               class="text-deltion-blue-600 font-semibold hover:underline">
                                                🔗 Bekijk
                                            </a>
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
    <!-- Laad de JavaScript en CSS -->
    <link rel="stylesheet" href="{{ asset('css/wdashboard.css') }}">
    <script src="{{ asset('js/accordion.js') }}"></script>

</x-app-layout>
