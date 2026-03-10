<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Workshop Overzicht</h1>
    </x-slot>

<div class="container mx-auto p-6 max-w-5xl">
    <div class="flex justify-between items-center mb-6">

        @php
            $signupsOpen = \App\Models\Setting::where('key', 'signups_open')->first();
            $isOpen = $signupsOpen && $signupsOpen->value == '1';
        @endphp

        <form action="{{ url('/toggle-signups') }}" method="POST" class="">
            @csrf
            <button type="submit" class="px-6 py-2 text-white font-semibold rounded-lg transition-colors {{ $isOpen ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                {!! $isOpen ? '✓ Inschrijvingen Open' : '✗ Inschrijvingen Gesloten' !!}
            </button>
        </form>
        <form>
            <div class="text-end px-6 py-2 text-white font-semibold rounded-lg transition-colors bg-green-600 hover:bg-green-700">
                <a href="{{ route('users.export') }}"class="btn btn-success">
                    <i class="fas fa-file-excel"></i> 📊 Export to Excel
                </a>
            </div>
        </form>
    </div>

    {!! session('success') ? '<div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">' . session('success') . '</div>' : '' !!}

    @php
        $workshopsByName = $workshopmoments->groupBy('workshop.name');
        $isAdmin = Auth::user() && Auth::user()->hasRole('admin');
        $shouldLock = !$isOpen && !$isAdmin;
    @endphp

    @foreach ($workshopsByName as $workshopName => $workshopMoments)
        <div class="border border-black-300 rounded-lg mb-2 {{ $shouldLock ? 'relative opacity-75' : '' }}" style="{{ $shouldLock ? 'position: relative;' : '' }}">
            {!! $shouldLock ? '<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; z-index: 10;"><div style="background: white; padding: 20px 40px; border-radius: 8px; text-align: center;"><div style="font-size: 48px; margin-bottom: 8px;" draggable="false">🔒</div><p style="font-weight: 600; color: #374151;">Registraties zijn gesloten</p></div></div>' : '' !!}

            <button class="w-full text-left p-6 {{ $shouldLock ? 'bg-gray-400' : 'bg-blue-600' }} text-white font-semibold flex justify-between items-center transition-all duration-300 accordion-btn {{ $shouldLock ? 'cursor-not-allowed' : '' }}"
                    onclick="{{ !$shouldLock ? 'toggleAccordion(this)' : '' }}"
                    {{ $shouldLock ? 'disabled' : '' }}>
                <span>{{ $workshopName }}</span>
                <span class="transition-transform transform">+</span>
            </button>

            <div class="hidden p-6 border-t border-black-300 bg-white accordion-content">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto {{ $shouldLock ? 'opacity-50' : '' }}">
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
                                <tr class="{{ !$shouldLock ? 'hover:bg-gray-50' : '' }}">
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->moment->time }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->room_name }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->capacity }}</td>
                                    <td class="px-6 py-4 border-b text-sm">{{ count($wm->bookings) }}</td>

                                    <td class="px-6 py-4 border-b text-sm">
                                        {!! count($wm->bookings) < $wm->workshop->capacity ? '<span class="text-green-600 font-semibold">✅ Nog plek</span>' : '<span class="text-red-600 font-semibold">❌ Vol</span>' !!}
                                    </td>

                                    <td class="px-6 py-4 border-b text-sm">
                                        {!! $isAdmin ? '<a href="' . route('workshop-moment.showbookings', ['wsm' => $wm]) . '" class="text-deltion-blue-600 font-semibold hover:underline">🔗 Bekijk</a>' : ($isOpen ? '<a href="' . route('workshop-moment.showbookings', ['wsm' => $wm]) . '" class="text-deltion-blue-600 font-semibold hover:underline">🔗 Bekijk</a>' : '<span class="text-gray-400 font-semibold">🔒 Locked</span>') !!}
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
