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
        <script src="{{ asset('/js/tutorial.js') }}"></script>
        <script src="{{ asset('/js/mobileDashboard.js') }}"></script>
        <script src="{{ asset('/js/mobileTutorial.js') }}"></script>
    </head>
    <body>
        @if(isset($bookings) && $bookings->count() >= 3)
            <div class="w-full min-h-full bg-gray-50 flex flex-col items-center p-4">
                <div class="bg-white p-6 rounded-lg shadow-md max-w-lg w-full text-center my-auto">
                    <h2 class="text-2xl font-bold text-deltion-blue-900 mb-4">Je hebt jouw keuze voor de workshops al doorgegeven.</h2>
                    <p class="text-gray-600 mb-6">In je mailbox vind je een bevestiging hiervan.</p>

                    <div class="space-y-4 mb-8 text-left">
                        @foreach($bookings->sortBy('workshopMoments.moment_id') as $booking)
                            <div class="bg-gray-50 border-l-4 border-deltion-orange-500 p-4 rounded shadow-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-gray-800">Ronde {{ $booking->workshopMoments->moment_id }}</span>
                                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">{{ $booking->workshopMoments->moment->time }}</span>
                                </div>
                                <h3 class="font-semibold text-deltion-blue-900">{{ $booking->workshopMoments->workshop->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">Locatie: {{ $booking->workshopMoments->workshop->room_name ?? 'Nader te bepalen' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg text-sm text-gray-700">
                        <p class="font-semibold mb-1">Wil je je keuze wijzigen?</p>
                        <p>Stuur dan een mail naar <a href="mailto:{{ config('mail.from.address') }}" class="text-deltion-blue-600 underline font-bold">{{ config('mail.from.address') }}</a></p>
                    </div>
                </div>
            </div>
        @else

        <!-- PC View -->
        <div class="hidden md:block">
            <!-- Tutorial -->
            <div class="tutorial-overlay" id="tutorial-overlay" style="display: none;">
                <div class="tutorial-step" id="tutorial-step">
                    <button onclick="skipTutorial()" class="absolute top-2 right-2 text-2xl text-gray-500 hover:text-gray-700">×</button>
                    <p id="tutorial-text"></p>
                    <div class="tutorial-buttons">
                        <button id="prevButton" onclick="prevStep()" >Terug</button>
                        <button id="nextButton" onclick="nextStep()">Volgende</button>
                        <button onclick="skipTutorial()" class="skip-button">Skip</button>
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
                        <div class='workshop' id='workshop{{ $workshop->id}}' capacity="{{ $workshop->capacity}}" draggable='true' ondragstart='drag(event)' style='@if($registrationsClosed)opacity: 0.5; position: relative;@endif'>
                            @if($registrationsClosed)
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 32px; z-index: 10;">🔒</div>
                            @endif
                                <div class='popup' id='popup{{ $workshop->id}}' draggable='false'>
                                    <button class='close' onclick="closePopup('{{ $workshop->id }}')">x</button>
                                    <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma" class="popup-header" target="_blank">Klik <span class="highlight">hier</span> voor meer informatie</a>
                                    <div class='description'>
                                        <div class='descriptionText'>{{ $workshop->full_description }} </div>
                                        <div class='descriptionImage'><img src="{{ $workshop->image_url }}" alt="Workshop afbeelding"></div>
                                    </div>
                                </div>
                                <div class="workshop-header">
                                    <div class='info' onclick='info(event)' id='info{{ $workshop->id}}' tabindex='0'>i</div>
                                    <div class="title showText" id='title{{ $workshop->id}}' workshop='{{ $workshop->name }}'>{{$workshop->name }}  </div>
                                </div>
                                <div class="capacityText" id="capacityText{{ $workshop->id}}"></div>
                                <div class="locationWorkshop hidden">Deze workshop vind plaats in: <div id="location">{{ $workshop->location }}</div></div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex">
                <div id="save-button-container" style="display: none;"></div>
                <div id="confirmation-popup" class="confpopup">
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
            </div>
            <div class="flex">
                <button id="save-button" style="display: none;" onclick="showSavePopup()">Opslaan</button>
            </div>
            <!-- loading script after html has loaded because of getElementById -->
            <script src="{{ asset('/js/capacityWorkshops.js') }}"></script>
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

        <!-- Mobile View -->
        <div class="md:hidden p-4 pb-24 bg-gray-50 min-h-full">

            <!-- Mobile Tutorial -->
            <div id="mobileTutorialOverlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 max-w-sm mx-4 shadow-xl">
                    <button onclick="closeMobileTutorial()" class="absolute top-4 right-4 text-3xl text-gray-400 hover:text-gray-600">×</button>
                    <h2 id="mobileTutorialTitle" class="text-2xl font-bold text-deltion-blue-900 mb-4"></h2>
                    <p id="mobileTutorialText" class="text-gray-700 mb-6 leading-relaxed"></p>
                    <div class="flex gap-2 justify-center">
                        <button id="mobilePrevBtn" onclick="prevMobileStep()" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-semibold hidden">Terug</button>
                        <button id="mobileNextBtn" onclick="nextMobileStep()" class="px-6 py-2 bg-deltion-blue-900 text-white rounded-lg hover:bg-deltion-blue-700 font-semibold">Volgende</button>
                    </div>
                </div>
            </div>

            <!-- Foutmelding -->
            <div id="error-popup-mobile" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
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
                <p class="text-gray-600 mt-1">{{ $registrationsClosed ? 'Inschrijvingen zijn gesloten' : 'Klik op een workshop om deze toe te voegen' }}</p>
            </div>

            <!-- Selected Rounds Display -->
            <div class="mb-6 space-y-2">
                <div class="bg-deltion-blue-50 border-l-4 border-deltion-blue-900 p-3 rounded flex items-center justify-end gap-2">
                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-700">Ronde 1 (13:00 - 13:45)</p>
                        <p id="mobile-round-1" class="text-gray-600">Geen workshop geselecteerd</p>
                    </div>
                    <button id="remove-round-1" onclick="removeWorkshopRound(1)" class="hidden text-3xl text-deltion-orange-400 flex-shrink-0">×</button>
                </div>
                <div class="bg-deltion-blue-50 border-l-4 border-deltion-blue-900 p-3 rounded flex items-center justify-end gap-2">
                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-700">Ronde 2 (13:45 - 14:30)</p>
                        <p id="mobile-round-2" class="text-gray-600">Geen workshop geselecteerd</p>
                    </div>
                    <button id="remove-round-2" onclick="removeWorkshopRound(2)" class="hidden text-3xl text-deltion-orange-400 flex-shrink-0">×</button>
                </div>
                <div class="bg-deltion-blue-50 border-l-4 border-deltion-blue-900 p-3 rounded flex items-center justify-end gap-2">
                    <div class="text-left flex-1">
                        <p class="text-sm font-semibold text-gray-700">Ronde 3 (15:00 - 15:45)</p>
                        <p id="mobile-round-3" class="text-gray-600">Geen workshop geselecteerd</p>
                    </div>
                    <button id="remove-round-3" onclick="removeWorkshopRound(3)" class="hidden text-3xl text-deltion-orange-400 flex-shrink-0">×</button>
                </div>
            </div>

            <!-- Workshops Grid -->
            <div id="mobile-workshops-container" class="space-y-3 mb-6 @if($registrationsClosed) opacity-50 pointer-events-none @endif">
                    @foreach ($workshops as $workshop)
                    <div id="{{ $workshop->id }}"
                         class="bg-white border-2 border-gray-300 rounded-lg p-4 relative
                    @unless($registrationsClosed)
                        cursor-pointer hover:border-deltion-blue-900 hover:shadow-md transition
                    @endunless"
                         @unless($registrationsClosed)
                             onclick="selectWorkshopMobile('{{ $workshop->id }}', {{ json_encode($workshop->name) }})"
                        @endunless>

                        @if($registrationsClosed)
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <span class="text-4xl">🔒</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <img src="{{ $workshop->image_url }}" alt="{{ $workshop->name }}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-gray-800 me-auto">{{ $workshop->name }}</h3>
                                    <button type="button" class="mobile-info flex-shrink-0 ms-auto" onclick='mobileInfo(event, @json($workshop->name), @json($workshop->full_description))' id="info{{ $workshop->id }}" tabindex="0" @if($registrationsClosed) disabled @endif
                                    >i</button>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ substr($workshop->full_description, 0, 100) }}...</p>
                                <p class="text-xs text-gray-500 mt-2">Locatie: {{ $workshop->room_name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Spacer to prevent content from being hidden behind fixed save button -->
            <div class="h-32"></div>

            <!-- Save Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-300 z-10">
                <button id="mobile-save-button" onclick="showMobileSavePopup()" class="w-full px-6 py-3 {{ $registrationsClosed ? 'bg-gray-400 text-white cursor-not-allowed' : 'bg-gray-400 text-white' }} font-bold rounded-lg"{{ $registrationsClosed ? 'disabled' : '' }}>
                    Opslaan
                </button>
            </div>

            <!-- Workshop Selection Modal -->
            <div id="workshopSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 items-end">
                <div class="bg-white w-full rounded-t-lg p-6 animate-slide-up">
                    <div class="flex items-center justify-between w-full mb-4">
                        <h2 id="workshopModalTitle" class="text-2xl font-bold me-auto"></h2>
                        <button onclick="closeWorkshopSelection()" class="text-3xl ms-4 leading-none text-gray-400">×</button>
                    </div>
                    <p class="text-gray-600 mb-6">Selecteer een ronde:</p>
                    <div class="space-y-2">
                        <button id="mobile-modal-round-1" type="button" onclick="addToRoundMobile(1)" class="w-full px-4 py-3 bg-deltion-blue-900 text-white font-bold rounded-lg hover:bg-deltion-blue-600">
                            Ronde 1 (13:00 - 13:45)
                        </button>
                        <button id="mobile-modal-round-2" type="button" onclick="addToRoundMobile(2)" class="w-full px-4 py-3 bg-deltion-blue-900 text-white font-bold rounded-lg hover:bg-deltion-blue-600">
                            Ronde 2 (13:45 - 14:30)
                        </button>
                        <button id="mobile-modal-round-3" type="button" onclick="addToRoundMobile(3)" class="w-full px-4 py-3 bg-deltion-blue-900 text-white font-bold rounded-lg hover:bg-deltion-blue-600">
                            Ronde 3 (15:00 - 15:45)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Workshop Information Modal -->
            <div id="workshopInformationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 items-end">
                <div class="bg-white w-full rounded-t-lg animate-slide-up max-h-[90dvh] overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 w-full border-b border-gray-100">
                        <h2 id="workshopInformationTitle" class="text-2xl me-auto font-bold"></h2>
                        <button onclick="closeInformation()" class="text-3xl ms-auto leading-none text-gray-400">×</button>
                    </div>
                    <div class="overflow-y-auto px-6 pb-6">
                        <span id="InformationDescription" class="block break-words pt-3">
                        </span>
                    </div>
                </div>
            </div>

            <!-- Save Confirmation Modal -->
            <div id="mobileSavePopup" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 items-center justify-center">
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
        </div>
        @endif
    </body>
    </html>
</x-app-layout>
