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
                                <p class="text-sm text-gray-500 mt-1">Locatie: {{ $booking->workshopMoments->workshop->location ?? 'Nader te bepalen' }}</p>
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
        @php
            $registrationsClosed = !\App\Models\Setting::signupsOpen();
            $workshopData = $workshops->map->only(['id', 'name', 'full_description', 'image_url', 'location'])->values();
        @endphp

        {{-- Alle logica staat in resources/js/dashboard/workshopPicker.js --}}
        <div x-data="workshopPicker({ workshops: @js($workshopData), registrationsClosed: @js($registrationsClosed) })"
             @click.window="closeInfoOnOutsideClick($event)">

        <!-- PC View -->
        <div class="hidden md:block">
            <div class="tutorial-overlay" id="tutorial-overlay" x-show="$store.tutorial.active" x-cloak>
                <div class="tutorial-step" id="tutorial-step" :style="$store.tutorial.step > 0 ? 'position: relative; top: 25%;' : ''">
                    <button @click="$store.tutorial.skip()" class="absolute top-2 right-2 text-2xl">×</button>
                    <p id="tutorial-text" x-text="$store.tutorial.texts[$store.tutorial.step]"></p>
                    <div class="tutorial-buttons" :style="{ justifyContent: $store.tutorial.step === 0 ? 'center' : 'space-between' }">
                        <button id="prevButton" x-show="$store.tutorial.step > 0 && !$store.tutorial.isLastStep" @click="$store.tutorial.prev()">Terug</button>
                        <button id="nextButton" :disabled="$store.tutorial.nextDisabled" :class="{ 'disabledStyle': $store.tutorial.nextDisabled }" @click="$store.tutorial.next()" x-text="$store.tutorial.nextLabel"></button>
                        <button x-show="!$store.tutorial.isLastStep" @click="$store.tutorial.skip()" class="skip-button">Overslaan</button>
                    </div>
                </div>
            </div>
            <!-- Foutmelding bij workshop ongeldige ronde -->
            <div id="error-popup" class="error-overlay" :style="error ? 'display: flex;' : ''">
                <div class="error-box">
                    <h3 id="error-title" x-text="error?.title"></h3>
                    <p id="error-message" x-text="error?.message"></p>
                    <button @click="error = null">Sluiten</button>
                </div>
            </div>
            @if (session('status') == 'success')
            <x-success-msg message="{{ session('message') }}" color="green" />
            @elseif (session('status') == 'failed')
            <x-success-msg message="{{ session('message') }}" color="red" />
            @endif
            <!-- Main Content -->
            <div class="main">
                <div class="rounds">
                    <template x-for="round in [1, 2, 3]" :key="round">
                        <div class="flex" :id="'round' + round" :class="{ 'tutorial-focus': ($store.tutorial.is(3) && round === 1) || ($store.tutorial.is(4) && selected[round]) }">
                            <div class="flex col round placeholder" :style="selected[round] ? 'visibility: hidden;' : ''">
                                <p x-text="'Ronde ' + round"></p>
                                <p x-text="roundTimes[round]"></p>
                            </div>
                            <div class="round" :id="round" @dragover.prevent @drop.prevent="dropOnRound(round)">
                                <template x-for="card in (selected[round] ? [workshop(selected[round])] : [])" :key="card.id">
                                    @include('dashboard.partials.workshop-card', ['inRound' => true])
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="workshops" id="4" @dragover.prevent @drop.prevent="dropOnList()">
                    <template x-for="(card, index) in availableWorkshops" :key="card.id">
                        @include('dashboard.partials.workshop-card', ['inRound' => false])
                    </template>
                </div>
            </div>
            <div class="flex gap-2">
                <div id="confirmation-popup" class="confpopup" :style="confirmOpen ? 'display: flex;' : ''">
                    <div class="popup-content">
                        <p>Wil je dit opslaan?</p>
                        <form id="confirmForm" @submit.prevent="save()">
                            <div class="button-container">
                                <button id="confirm-yes-button" type="submit" class="yes-button" :disabled="saving">Ja</button>
                                <span id="save-spinner" x-show="saving" x-cloak>Opslaan...</span>
                                <button type="button" class="no-button" @click="confirmOpen = false">Nee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="flex">
                <button id="save-button" x-show="allRoundsSelected" x-cloak @click="confirmOpen = true">Opslaan</button>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden p-4 pb-24 bg-gray-50 min-h-full">

            <!-- Mobile Tutorial -->
            <div x-data="mobileTutorial" @keydown.window="onKeydown($event)">
                <div class="mobile-tutorial-overlay" id="mobileTutorial" x-show="open" x-cloak>
                    <div class="mobile-tutorial-box">
                        <button @click="open = false" class="absolute top-2 right-2 text-2xl">×</button>
                        <button @click="open = false" class="absolute top-2 left-2 text-sm px-2 py-1 bg-gray-200 rounded">Overslaan</button>
                        <h3 id="mobileTutorialTitle" class="text-2xl font-bold mb-4" x-text="current.title"></h3>
                        <p id="mobileTutorialText" class="text-lg mb-6">
                            <span class="block text-sm text-blue-600 mb-2" x-text="`Stap ${step + 1} van ${steps.length}`"></span>
                            <span x-text="current.text"></span>
                        </p>
                        <div class="flex gap-2 justify-center">
                            <button id="mobilePrevBtn" x-show="step > 0" @click="prev()" class="px-4 py-2 bg-blue-500 text-white rounded">Terug</button>
                            <button id="mobileNextBtn" @click="next()" class="px-4 py-2 bg-blue-500 text-white rounded" x-text="isLastStep ? 'Klaar' : 'Volgende'"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Foutmelding -->
            <div id="error-popup-mobile" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50" :class="error ? 'flex' : 'hidden'" x-cloak>
                <div class="bg-white rounded-lg p-6 max-w-sm">
                    <h3 id="error-title-mobile" class="text-xl font-bold mb-2" x-text="error?.title"></h3>
                    <p id="error-message-mobile" class="text-gray-700 mb-4" x-text="error?.message"></p>
                    <button @click="error = null" class="w-full px-4 py-2 bg-blue-600 text-white rounded">Sluiten</button>
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
                <template x-for="round in [1, 2, 3]" :key="round">
                    <div class="bg-deltion-blue-50 border-l-4 border-deltion-blue-900 p-3 rounded flex items-center justify-end gap-2">
                        <div class="text-left flex-1">
                            <p class="text-sm font-semibold text-gray-700" x-text="`Ronde ${round} (${roundTimes[round]})`"></p>
                            <p :id="'mobile-round-' + round"
                               :class="selected[round] ? 'text-gray-800 font-semibold' : 'text-gray-600'"
                               x-text="selected[round] ? workshop(selected[round]).name : 'Geen workshop geselecteerd'"></p>
                        </div>
                        <button :id="'remove-round-' + round" x-show="selected[round]" @click="removeFromRound(round)" class="text-3xl text-deltion-orange-400 flex-shrink-0">×</button>
                    </div>
                </template>
            </div>

            <!-- Workshops Grid -->
            <div id="mobile-workshops-container" class="space-y-3 mb-6" :class="{ 'opacity-50 pointer-events-none': registrationsClosed }">
                <template x-for="card in availableWorkshops" :key="card.id">
                    <div :id="card.id"
                         class="bg-white border-2 border-gray-300 rounded-lg p-4 relative"
                         :class="{ 'cursor-pointer hover:border-deltion-blue-900 hover:shadow-md transition': !registrationsClosed }"
                         @click="pickMobile(card.id)">

                        <template x-if="registrationsClosed">
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <span class="text-4xl">🔒</span>
                            </div>
                        </template>

                        <div class="flex items-start gap-3">
                            <img :src="card.image_url" :alt="card.name" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-gray-800 me-auto" x-text="card.name"></h3>
                                    <button type="button" class="mobile-info flex-shrink-0 ms-auto" @click.stop="infoWorkshop = card" :disabled="registrationsClosed">i</button>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="(card.full_description ?? '').substring(0, 100) + '...'"></p>
                                <p class="text-xs text-gray-500 mt-2" x-text="'Locatie: ' + (card.location ?? '')"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Spacer to prevent content from being hidden behind fixed save button -->
            <div class="h-32"></div>

            <!-- Save Button -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-300 z-10">
                <button id="mobile-save-button"
                        @click="openMobileSave()"
                        :disabled="!allRoundsSelected || registrationsClosed"
                        class="w-full px-6 py-3 text-white font-bold rounded-lg"
                        :class="allRoundsSelected && !registrationsClosed ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'">
                    Opslaan
                </button>
            </div>

            <!-- Workshop Selection Modal -->
            <div id="workshopSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 items-end" :class="modalWorkshopId ? 'flex' : 'hidden'" x-cloak>
                <div class="bg-white w-full rounded-t-lg p-6 animate-slide-up">
                    <div class="flex items-center justify-between w-full mb-4">
                        <h2 id="workshopModalTitle" class="text-2xl font-bold me-auto" x-text="workshop(modalWorkshopId)?.name"></h2>
                        <button @click="modalWorkshopId = null" class="text-3xl ms-4 leading-none text-gray-400">×</button>
                    </div>
                    <p class="text-gray-600 mb-6">Selecteer een ronde:</p>
                    <div class="space-y-2">
                        <template x-for="round in [1, 2, 3]" :key="round">
                            <button :id="'mobile-modal-round-' + round"
                                    type="button"
                                    @click="addToRoundMobile(round)"
                                    :disabled="modalRoundDisabled(round)"
                                    class="w-full px-4 py-3 text-white font-bold rounded-lg"
                                    :class="modalRoundDisabled(round) ? 'bg-gray-400 cursor-not-allowed' : 'bg-deltion-blue-900 hover:bg-deltion-blue-600 cursor-pointer'"
                                    x-text="modalRoundText(round)">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Workshop Information Modal -->
            <div id="workshopInformationModal" class="fixed inset-0 bg-black bg-opacity-50 z-40 items-end" :class="infoWorkshop ? 'flex' : 'hidden'" x-cloak>
                <div class="bg-white w-full rounded-t-lg animate-slide-up max-h-[90dvh] overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 w-full border-b border-gray-100">
                        <h2 id="workshopInformationTitle" class="text-2xl me-auto font-bold" x-text="infoWorkshop?.name"></h2>
                        <button @click="infoWorkshop = null" class="text-3xl ms-auto leading-none text-gray-400">×</button>
                    </div>
                    <div class="overflow-y-auto px-6 pb-6">
                        <span id="InformationDescription" class="block break-words pt-3" x-text="infoWorkshop?.full_description"></span>
                    </div>
                </div>
            </div>

            <!-- Save Confirmation Modal -->
            <div id="mobileSavePopup" class="fixed inset-0 bg-black bg-opacity-50 z-40 items-center justify-center" :class="mobileConfirmOpen ? 'flex' : 'hidden'" x-cloak>
                <div class="bg-white rounded-lg p-6 max-w-sm">
                    <h2 class="text-2xl font-bold mb-4">Opslaan?</h2>
                    <p class="text-gray-600 mb-6">Wil je je workshop selectie opslaan?</p>
                    <form @submit.prevent="save()" class="space-y-2">
                        <button type="submit" :disabled="saving" class="w-full px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700">
                            Ja, opslaan
                        </button>
                        <button type="button" @click="mobileConfirmOpen = false" class="w-full px-4 py-2 bg-gray-300 text-gray-800 font-bold rounded hover:bg-gray-400">
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
        </div>
        @endif
    </body>
    </html>
</x-app-layout>
