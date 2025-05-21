<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma">
                <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
                <div class="image-container">
                    <img src="{{ asset('images/Letsconnect2.0.jpeg') }}" alt="Klik hier">
                    <div class="image-title">
                    </div>
                </div>
            </a> 
        </h2>
        @role('admin')
            <a href="/wdashboard" style="text-decoration: underline">Bekijk het overzicht</a>
        @endrole
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
        @role('admini')
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
                    <div class='workshop' id='workshop{{ $workshop->id - 1 }}' capacity="{{ $workshop->capacity}}" draggable='true' ondragstart='drag(event)'> 
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
                        <div class="locationWorkshop">Deze workshop vind plaats in: <div id="location">{{ $workshop->location }}</div></div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex">
            <div id="save-button-container" style="display: none;"></div>
            <div id="confirmation-popup" class="confpopup" style="display: none;">
                <div class="popup-content">
                    <p>Wil je dit opslaan?</p>
                    <form id="confirmForm" method="POST" action="{{ url('/save') }}">
                        @csrf
                        <input type="hidden" name="save1" id="save1" value="">
                        <input type="hidden" name="save2" id="save2" value="">
                        <input type="hidden" name="save3" id="save3" value="">
                        <div class="button-container">
                            <button type="button" id="confirm-yes-button" onclick="confirmSave(event)">
                                Ja
                                <span id="save-spinner" class="spinner" style="display: none;"></span>
                            </button>
                            </button>
                            <button type="button" class="no-button" onclick="cancelSave()">Nee</button>
                        </div>
                    </form>
                </div>
            </div>
        <div class="flex">
            <button id="save-button" style="display: none;" onclick="showSavePopup()">Opslaan</button>
        </div> 
        <div class="flex">
            <div id="workshopsPopup">
                <div class="popupWrapper">
                    <div class="close-button" onclick="closeWorkshops()">X</div>
                    <div class="flex">
                        <div class="chosenRound"></div>
                        <div class="roundWorkshop"></div>
                    </div>
                    <p class="loader"></p>
                </div>
            </div>
        </div>

        <div class="flex">
            <div id="selectedWorkshopPopup">
                <div class="selectedWrapper">
                    <p>Wil je deze workshop bewaren?</p>
                    <p class="workshopNameInfo"></p>
                    <p>Plekken over:</p>
                    <p id="viewCapacityRound"></p>
                    <div class="button-container">
                        <button class="yes-button" onclick="addConfirmedWorkshop()">Ja</button>
                        <button class="no-button" onclick="removeConfirm()">Nee</button>
                    </div>
                </div>
            </div>
        </div> 
        <!-- loading script after html has loaded because of getElementById -->
        <script src="{{ asset('/js/capacityWorkshops.js') }}"></script>
        <script src="{{ asset('/js/tutorial.js') }}"></script>
        <script src="{{ asset('/js/dragAndDrop.js') }}"></script>
        <script src="{{ asset('/js/sameWorkshop.js') }}"></script>  
        <script src="{{ asset('/js/mobileClickRound.js') }} "></script>  
        @endrole
        {{-- @role('admini') --}}
        <div>
            <h1>De inschrijvingen zijn op dit moment gesloten.</h1>
        </div>
        {{-- @endrole --}}
    </body>
    </html>
</x-app-layout>