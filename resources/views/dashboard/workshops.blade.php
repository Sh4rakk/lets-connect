<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Workshop Overzicht</h1>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('/css/wdashboard.css') }}">

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

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg relative flex items-start">
            <div class="flex-1">
                {!! session('success') !!}
            </div>

            <button
                type="button"
                onclick="this.parentElement.style.display = 'none';"
                class="ml-3 -mt-1 -mr-1 text-green-700 hover:text-green-900 focus:outline-none transition-colors"
                aria-label="Sluit bericht"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @php
        $workshopsByName = $workshopmoments->groupBy('workshop.name');
        $isAdmin = Auth::user() && Auth::user()->hasRole('admin');
        $shouldLock = !$isOpen && !$isAdmin;
    @endphp
        <button id="toggleAllBtn" class="toggleAccordionAll" onclick="toggleAllAccordions()">Alles uitklappen</button>
        @php
            $workshopsByName = $workshopmoments->groupBy('workshop.name');
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
                <div class="mb-4">
                    <a href="{{ route('workshop.export', ['workshopName' => urlencode($workshopName)]) }}" class="inline-block px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                        📊 Export {{ $workshopName }}
                    </a>
                </div>
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
                                    <td class="px-6 py-4 border-b text-sm">{{ $wm->workshop->location }}</td>
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
    <!-- Javascript voor de accordion toggle -->
    <script>
        function toggleAccordion(button) {
            let content = button.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
                button.classList.remove('open');  // Verwijder de "open" class van de knop
                button.querySelector("span:last-child").textContent = "+";  // Zet het teken terug naar "+"
            } else {
                content.style.display = "block";
                button.classList.add('open');  // Voeg de "open" class toe aan de knop
                button.querySelector("span:last-child").textContent = "−";  // Zet het teken naar "-"
            }
        }

        // Deze code zorgt ervoor dat alle accordion-content standaard zichtbaar zijn
        document.addEventListener("DOMContentLoaded", function () {
            let accordionContents = document.querySelectorAll(".accordion-content");
            accordionContents.forEach(function (content) {
                content.style.display = "block";  // Zorg ervoor dat alle accordion-secties open zijn
            });

            // Ook de "+" naar "−" veranderen voor de visuele indicatie
            let accordionButtons = document.querySelectorAll(".accordion-btn");
            accordionButtons.forEach(function (button) {
                button.querySelector("span:last-child").textContent = "−";  // Zet de teken om naar "-"
            });
        });

        // Nieuwe functie om alles open of dicht te klappen
        function toggleAllAccordions() {
            let accordionContents = document.querySelectorAll(".accordion-content");
            let accordionButtons = document.querySelectorAll(".accordion-btn");
            let toggleButton = document.getElementById("toggleAllBtn");
            let isAnyOpen = Array.from(accordionContents).some(content => content.style.display === "block");

            if (isAnyOpen) {
                // Als een van de accordion-secties open is, sluit dan alles
                accordionContents.forEach(function (content) {
                    content.style.display = "none";
                });
                accordionButtons.forEach(function (button) {
                    button.querySelector("span:last-child").textContent = "+";  // Zet de teken om naar "+"
                });
                toggleButton.textContent = "Alles uitklappen"; // Zet de knoptekst naar 'Alles uitklappen'
            } else {
                // Als geen enkele accordion-sectie open is, open dan alles
                accordionContents.forEach(function (content) {
                    content.style.display = "block";
                });
                accordionButtons.forEach(function (button) {
                    button.querySelector("span:last-child").textContent = "−";  // Zet de teken om naar "−"
                });
                toggleButton.textContent = "Alles inklappen"; // Zet de knoptekst naar 'Alles inklappen'
            }
        }
    </script>
</x-app-layout>
