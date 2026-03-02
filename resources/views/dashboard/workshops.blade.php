<div class="container mx-auto p-6 max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-center flex-1 text-gray-800">Workshop Overzicht</h1>
        
        @php
            $signupsOpen = \App\Models\Setting::where('key', 'signups_open')->first();
            $isOpen = $signupsOpen && $signupsOpen->value == '1';
        @endphp

        <form action="{{ url('/toggle-signups') }}" method="POST" class="ml-4">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-lg font-semibold text-white transition-all @if($isOpen) bg-green-600 hover:bg-green-700 @else bg-red-600 hover:bg-red-700 @endif">
                @if($isOpen)
                    ✅ Registrations Open
                @else
                    ❌ Registrations Closed
                @endif
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @php
        $workshopsByName = $workshopmoments->groupBy('workshop.name');
    @endphp

    @foreach ($workshopsByName as $workshopName => $workshopMoments)
        <div class="border border-black-300 rounded-lg mb-2">
            <button class="w-full text-left p-6 bg-blue-600 text-white font-semibold flex justify-between items-center transition-all duration-300 accordion-btn"
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
                                           class="text-blue-600 font-semibold hover:underline">
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
