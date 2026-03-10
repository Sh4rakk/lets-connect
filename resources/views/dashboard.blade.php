<x-app-layout>
    <x-slot name="leftHeader">
        <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma" target="_blank" class="flex gap-1">
            <p class="deltion-blue font-semibold text-xl">Let's</p>
            <p class="deltion-orange font-semibold text-xl">Connect</p>
        </a>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Workshop Dashboard</h2>
    </x-slot>

    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <title>Let's Connect</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="{{ asset('/css/dashboard.css') }}" rel="stylesheet">
        <script src="{{ asset('/js/confirmPopup.js') }}"></script>
        <script src="{{ asset('/js/errorPopup.js') }}"></script>
        <script src="{{ asset('/js/infoPopup.js') }}"></script>
    </head>
    <body>

        <!-- PC View -->
        <div class="hidden md:block">
            <!-- Tutorial -->
            <div class="tutorial-overlay" id="tutorial-overlay" style="display: none;">
                <div class="tutorial-step" id="tutorial-step">
                    <p id="tutorial-text"></p>
                    <div class="tutorial-buttons">
                        <button id="prevButton" onclick="prevStep()" >Terug</button>
                        <button id="nextButton" onclick="nextStep()">Volgende</button>
                    </div>
                </div>
            </div>
            <!-- Foutmelding bij workshop ongeldige ronde -->
            <div id="error-popup" class="error-overlay" style="display:none;">
                <div class="error-box">
                    <h3 id="error-title">Foutmelding</h3>
                    <p id="error-message">Dit is een foutmelding.</p>
                    <button onclick="closeErrorPopup()">Sluiten</button>
                </div>
            </div>
            @if (session('status') == 'success')
            <x-success-msg message="{{ session('message') }}" color="green" />
            @elseif (session('status') == 'failed')
            <x-success-msg message="{{ session('message') }}" color="red" />
            @endif
            @php
                $signupsOpen = \App\Models\Setting::where('key', 'signups_open')->first();
                $registrationsClosed = !($signupsOpen && $signupsOpen->value == '1');
            @endphp
            <!-- Main Content -->
            <div class="main">
                <div class="rounds">
                    <div class="flex" id="round1">
                        <div class="flex col round placeholder">
                            <p>Ronde 1</p>
                            <p>13:00 - 13:45</p>
                        </div>
                        <div class="round" ondrop="drop(event, this)" ondragover="allowDrop(event)" id="1"></div>
                    </div>
                    <div class="flex" id="round2">
                        <div class="flex col round placeholder">
                            <p>Ronde 2</p>
                            <p>13:45 - 14:30</p>
                        </div>
                        <div class="round" ondrop="drop(event, this)" ondragover="allowDrop(event)" id="2"></div>
                    </div>
                    <div class="flex" id="round3">
                        <div class="flex col round placeholder">
                            <p>Ronde 3</p>
                            <p>15:00 - 15:45</p>
                        </div>
                        <div class="round" ondrop="drop(event, this)" ondragover="allowDrop(event)" id="3"></div>
                    </div>
                </div>
                <div class="workshops" ondrop="drop(event, this)" ondragover="allowDrop(event)" draggable="true" id="4">
                    @foreach ($workshops as $workshop)
                        <div class='workshop' id='workshop{{ $workshop->id - 1 }}' capacity="{{ $workshop->capacity}}" draggable='true' ondragstart='drag(event)' style='@if($registrationsClosed)opacity: 0.5; position: relative;@endif'>
                            @if($registrationsClosed)
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 32px; z-index: 10;">🔒</div>
                            @endif
                            <div class='info' onclick='info(event)' id='info{{ $workshop->id - 1 }}' tabindex='0'>i</div>
                            <div class='popup' id='popup{{ $workshop->id - 1 }}' draggable='false'>
                                <button class='close' onclick='closePopup({{ $workshop->id - 1 }})'>x</button>
                                <a href='https://xerte.deltion.nl/play.php?template_id=8708#programma' class='popup-header' target='_blank'>Klik <span class='highlight'>hier</span> voor meer informatie</a>
                                <div class='description'>
                                    <div class='descriptionText'>{{ $workshop->full_description }} </div>
                                    <div class='descriptionImage'><img src='{{ $workshop->image_url }}'></div>
                                </div>
                            </div>
                            <div class='title showText' id='title{{ $workshop->id - 1 }}' workshop='{{ $workshop->name }}'>{{$workshop->name }}  </div>
                            <div class="capacityText title hiddenText" id="capacityText{{ $workshop->id -1}}"></div>
                            <div class="locationWorkshop">Deze workshop vind plaats in: <div id="location">#</div></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex">
                <div id="save-button-container" style="display: none;"></div>
                <div id="confirmation-popup" class="confpopup" style="display: none;">
                    <div class="popup-content">
                        <p>Wil je dit opslaan?</p>
                        <form method="POST" action="{{ url('/save') }}">
                            @csrf
                            <input type="hidden" name="save1" id="save1" value="">
                            <input type="hidden" name="save2" id="save2" value="">
                            <input type="hidden" name="save3" id="save3" value="">
                            <div class="button-container">
                                <button type="submit" class="yes-button" onclick="SaveSave()">Ja</button>
                                <button type="button" class="no-button" onclick="cancelSave()">Nee</button>
                            </div>
                        </form>
                    </div>
                </div>
            <div class="flex">
                <button id="save-button" style="display: none;" onclick="showSavePopup()">Opslaan</button>
            </div>
            <!-- loading script after html has loaded because of getElementById -->
            <script src="{{ asset('/js/capacityWorkshops.js') }}"></script>
            <script src="{{ asset('/js/tutorial.js') }}"></script>
            <script src="{{ asset('/js/dragAndDrop.js') }}"></script>
            <script>
                const registrationsClosed = @json($registrationsClosed);
                if (registrationsClosed) {
                    document.querySelectorAll('.workshop').forEach(workshop => {
                        workshop.draggable = false;
                    });
                }
            </script>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden p-4 bg-gray-50 min-h-screen">

            <!-- Mobile Tutorial -->
            <div class="mobile-tutorial-overlay" id="mobileTutorial" style="display: none;">
                <div class="mobile-tutorial-box">
                    <button onclick="closeMobileTutorial()" class="absolute top-2 right-2 text-2xl">×</button>
                    <p id="mobileTutorialText" class="text-lg font-semibold mb-4"></p>
                    <div class="flex gap-2 justify-center">
                        <button onclick="prevMobileStep()" class="px-4 py-2 bg-blue-500 text-white rounded">Terug</button>
                        <button onclick="nextMobileStep()" class="px-4 py-2 bg-blue-500 text-white rounded">Volgende</button>
                    </div>
                </div>
            </div>

            <!-- Foutmelding -->
            <div id="error-popup-mobile" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg p-6 max-w-sm">
                    <h3 id="error-title-mobile" class="text-xl font-bold mb-2">Foutmelding</h3>
                    <p id="error-message-mobile" class="text-gray-700 mb-4">Dit is een foutmelding.</p>
                    <button onclick="closeErrorPopupMobile()" class="w-full px-4 py-2 bg-blue-600 text-white rounded">Sluiten</button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('status') == 'success')
            <x-success-msg message="{{ session('message') }}" color="green" />
            @elseif (session('status') == 'failed')
            <x-success-msg message="{{ session('message') }}" color="red" />
            @endif

            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Workshop Selectie</h1>
                <p class="text-gray-600 mt-1">Klik op een workshop om deze toe te voegen</p>
            </div>

            <!-- Selected Rounds Display -->
            <div class="mb-6 space-y-2">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                    <p class="text-sm font-semibold text-gray-700">Ronde 1 (13:00 - 13:45)</p>
                    <p id="mobile-round-1" class="text-gray-600">Geen workshop geselecteerd</p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded">
                    <p class="text-sm font-semibold text-gray-700">Ronde 2 (13:45 - 14:30)</p>
                    <p id="mobile-round-2" class="text-gray-600">Geen workshop geselecteerd</p>
                </div>
                <div class="bg-purple-50 border-l-4 border-purple-500 p-3 rounded">
                    <p class="text-sm font-semibold text-gray-700">Ronde 3 (15:00 - 15:45)</p>
                    <p id="mobile-round-3" class="text-gray-600">Geen workshop geselecteerd</p>
                </div>
            </div>

            <!-- Workshops Grid -->
            <div class="space-y-3 mb-6">
                @foreach ($workshops as $workshop)
                    <div class="bg-white border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition"
                         onclick="selectWorkshopMobile({{ $workshop->id - 1 }}, '{{ addslashes($workshop->name) }}')">
                        <div class="flex items-start gap-3">
                            <img src="{{ $workshop->image_url }}" alt="{{ $workshop->name }}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800">{{ $workshop->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ substr($workshop->full_description, 0, 100) }}...</p>
                                <p class="text-xs text-gray-500 mt-2">Locatie: {{ $workshop->room_name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Save Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-300">
                <button onclick="showMobileSavePopup()" class="w-full px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">
                    Opslaan
                </button>
            </div>

            <!-- Workshop Selection Modal -->
            <div id="workshopSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 flex items-end">
                <div class="bg-white w-full rounded-t-lg p-6 animate-slide-up">
                    <button onclick="closeWorkshopSelection()" class="absolute top-2 right-4 text-3xl text-gray-400">×</button>
                    <h2 id="workshopModalTitle" class="text-2xl font-bold mb-4"></h2>
                    <p class="text-gray-600 mb-6">Selecteer een ronde:</p>
                    <div class="space-y-2">
                        <button onclick="addToRoundMobile(1)" class="w-full px-4 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600">
                            Ronde 1 (13:00 - 13:45)
                        </button>
                        <button onclick="addToRoundMobile(2)" class="w-full px-4 py-3 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600">
                            Ronde 2 (13:45 - 14:30)
                        </button>
                        <button onclick="addToRoundMobile(3)" class="w-full px-4 py-3 bg-purple-500 text-white font-bold rounded-lg hover:bg-purple-600">
                            Ronde 3 (15:00 - 15:45)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Save Confirmation Modal -->
            <div id="mobileSavePopup" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 max-w-sm">
                    <h2 class="text-2xl font-bold mb-4">Opslaan?</h2>
                    <p class="text-gray-600 mb-6">Wil je je workshop selectie opslaan?</p>
                    <form method="POST" action="{{ url('/save') }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="save1" id="save1Mobile" value="">
                        <input type="hidden" name="save2" id="save2Mobile" value="">
                        <input type="hidden" name="save3" id="save3Mobile" value="">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700">
                            Ja, opslaan
                        </button>
                        <button type="button" onclick="closeMobileSavePopup()" class="w-full px-4 py-2 bg-gray-300 text-gray-800 font-bold rounded hover:bg-gray-400">
                            Nee, annuleren
                        </button>
                    </form>
                </div>
            </div>

            <style>
                @keyframes slideUp {
                    from { transform: translateY(100%); }
                    to { transform: translateY(0); }
                }
                .animate-slide-up { animation: slideUp 0.3s ease-out; }
            </style>

            <script src="{{ asset('/js/mobileTutorial.js') }}"></script>
            <script>
                let currentWorkshopId = null;
                let currentWorkshopName = null;
                let selectedRounds = { 1: null, 2: null, 3: null };

                function selectWorkshopMobile(workshopId, workshopName) {
                    currentWorkshopId = workshopId;
                    currentWorkshopName = workshopName;
                    document.getElementById('workshopModalTitle').textContent = workshopName;
                    document.getElementById('workshopSelectionModal').classList.remove('hidden');
                }

                function closeWorkshopSelection() {
                    document.getElementById('workshopSelectionModal').classList.add('hidden');
                    currentWorkshopId = null;
                    currentWorkshopName = null;
                }

                function addToRoundMobile(round) {
                    if (currentWorkshopId === null) return;

                    selectedRounds[round] = currentWorkshopId;

                    const roundElements = {
                        1: document.getElementById('mobile-round-1'),
                        2: document.getElementById('mobile-round-2'),
                        3: document.getElementById('mobile-round-3')
                    };

                    roundElements[round].textContent = currentWorkshopName;
                    roundElements[round].classList.remove('text-gray-600');
                    roundElements[round].classList.add('text-gray-800', 'font-semibold');

                    closeWorkshopSelection();
                }

                function showMobileSavePopup() {

                    if (!selectedRounds[1] || !selectedRounds[2] || !selectedRounds[3]) {
                        document.getElementById('mobileSavePopup').classList.add('hidden');
                        document.getElementById('error-message-mobile').textContent = "Selecteer eerst een workshop voor alle rondes.";
                        document.getElementById('error-popup-mobile').classList.remove('hidden');
                        return;
                    }

                    document.getElementById('save1Mobile').value = selectedRounds[1];
                    document.getElementById('save2Mobile').value = selectedRounds[2];
                    document.getElementById('save3Mobile').value = selectedRounds[3];
                    document.getElementById('mobileSavePopup').classList.remove('hidden');
                }

                function closeMobileSavePopup() {
                    document.getElementById('mobileSavePopup').classList.add('hidden');
                }

                function closeErrorPopupMobile() {
                    document.getElementById('error-popup-mobile').classList.add('hidden');
                    document.getElementById('mobileSavePopup').classList.add('hidden');
                }

            </script>
        </div>

    </body>
    </html>
</x-app-layout>
