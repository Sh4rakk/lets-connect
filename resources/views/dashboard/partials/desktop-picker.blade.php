{{-- Desktop: eerst een ronde aanklikken, dan een workshop. Logica in resources/js/dashboard/workshopPicker.js --}}
<div class="hidden md:block">
    <div class="mx-auto max-w-6xl px-6 pb-32 pt-8">
        @if (session('status') == 'success')
        <x-success-msg message="{{ session('message') }}" color="green" />
        @elseif (session('status') == 'failed')
        <x-success-msg message="{{ session('message') }}" color="red" />
        @endif

        <h1 class="text-2xl font-bold text-deltion-blue-900">Kies je workshops</h1>
        <p class="mt-1 text-gray-600">
            @if ($registrationsClosed)
                De inschrijvingen zijn gesloten.
            @else
                Je kiest voor elke ronde één workshop. Klik op een ronde en daarna op de workshop die je wilt.
            @endif
        </p>

        {{-- de drie rondes --}}
        <div class="mt-6 grid grid-cols-3 gap-3">
            <template x-for="round in [1, 2, 3]" :key="round">
                <div class="relative rounded-lg border-2"
                     :class="activeRound === round ? 'border-deltion-blue-900 bg-deltion-blue-50' : 'border-gray-200 bg-white hover:border-gray-300'">
                    <button type="button" @click="activeRound = round" class="flex w-full items-center gap-3 p-4 pr-10 text-left">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                              :class="selected[round] || activeRound === round ? 'bg-deltion-blue-900 text-white' : 'border-2 border-gray-300 text-gray-400'"
                              x-text="round"></span>
                        <span class="min-w-0">
                            <span class="block text-xs font-medium text-gray-500" x-text="`Ronde ${round} · ${roundTimes[round]}`"></span>
                            <span class="mt-0.5 block truncate"
                                  :class="selected[round] ? 'font-semibold text-deltion-blue-900' : 'text-gray-400'"
                                  x-text="selected[round] ? workshop(selected[round]).name : 'Nog niet gekozen'"></span>
                        </span>
                    </button>
                    <button type="button" x-show="selected[round]" x-cloak @click="removeFromRound(round)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded px-1.5 text-xl leading-none text-gray-400 hover:text-red-700"
                            :aria-label="`Ronde ${round} leegmaken`">&times;</button>
                </div>
            </template>
        </div>

        <h2 class="mb-3 mt-8 text-sm font-semibold text-gray-500" x-text="`Workshops voor ronde ${activeRound}`"></h2>

        {{-- alle workshops, de plekken gelden voor de gekozen ronde --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3" :class="{ 'pointer-events-none opacity-60': registrationsClosed }">
            <template x-for="card in workshops" :key="card.id">
                <div class="relative flex flex-col overflow-hidden rounded-lg border-2 bg-white transition-colors"
                     :class="roundOf(card.id)
                         ? 'border-deltion-blue-900'
                         : (canChoose(card.id) ? 'border-gray-200 hover:border-gray-400' : 'border-gray-200')">
                    {{-- balletje met het nummer van de ronde waarvoor deze workshop gekozen is --}}
                    <span x-show="roundOf(card.id)" x-cloak
                          class="absolute left-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-deltion-blue-900 text-sm font-bold text-white shadow"
                          :title="`Gekozen voor ronde ${roundOf(card.id)}`"
                          x-text="roundOf(card.id)"></span>
                    <button type="button" @click="choose(card.id)" class="flex flex-1 flex-col text-left"
                            :class="[
                                canChoose(card.id) || selected[activeRound] === card.id ? 'cursor-pointer' : 'cursor-not-allowed',
                                !roundOf(card.id) && !canChoose(card.id) ? 'opacity-50' : ''
                            ]">
                        <template x-if="card.image_url">
                            <img :src="card.image_url" alt="" loading="lazy" class="h-36 w-full object-cover">
                        </template>
                        <template x-if="!card.image_url">
                            <div class="h-36 w-full bg-gray-100"></div>
                        </template>
                        <div class="flex-1 p-4">
                            <h3 class="font-semibold text-deltion-blue-900" x-text="card.name"></h3>
                            <p class="mt-0.5 text-sm text-gray-500" x-show="card.location" x-text="card.location"></p>
                            <p class="mt-2 line-clamp-2 text-sm text-gray-600" x-text="card.full_description"></p>
                        </div>
                    </button>
                    <div class="flex items-center justify-between border-t border-gray-100 px-4 py-2.5 text-sm">
                        <span class="font-medium" :class="cardStatus(card.id).class" x-text="cardStatus(card.id).text"></span>
                        <button type="button" @click="infoCard = card" class="font-semibold text-deltion-blue-900 hover:underline">Meer info</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- opslaan balk als alle rondes gekozen zijn --}}
    <div x-show="allRoundsSelected && !registrationsClosed" x-cloak
         class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-4">
            <p class="text-sm text-gray-600">Je hebt voor alle drie de rondes een workshop gekozen.</p>
            <button type="button" @click="confirmOpen = true"
                    class="rounded-md bg-deltion-orange-900 px-6 py-2.5 text-sm font-semibold uppercase tracking-wide text-white hover:bg-deltion-orange-950">Opslaan</button>
        </div>
    </div>

    {{-- bevestigen --}}
    <div x-show="confirmOpen" x-cloak @keydown.escape.window="confirmOpen = false" @click.self="confirmOpen = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6">
            <h2 class="text-xl font-bold text-deltion-blue-900">Klopt je keuze?</h2>
            <ul class="mt-4 divide-y divide-gray-100 text-sm">
                <template x-for="round in [1, 2, 3]" :key="round">
                    <li class="flex justify-between gap-4 py-2">
                        <span class="text-gray-500" x-text="`Ronde ${round}`"></span>
                        <span class="text-right font-semibold text-deltion-blue-900" x-text="selected[round] ? workshop(selected[round]).name : ''"></span>
                    </li>
                </template>
            </ul>
            <p class="mt-4 text-sm text-gray-500">Na het opslaan kun je je keuze niet zelf meer aanpassen.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="confirmOpen = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Terug</button>
                <button type="button" @click="save()" :disabled="saving"
                        class="rounded-md bg-deltion-orange-900 px-5 py-2 text-sm font-semibold text-white hover:bg-deltion-orange-950 disabled:opacity-60"
                        x-text="saving ? 'Bezig met opslaan...' : 'Ja, opslaan'"></button>
            </div>
        </div>
    </div>

    {{-- meer info --}}
    <div x-show="infoCard" x-cloak @keydown.escape.window="infoCard = null" @click.self="infoCard = null"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="max-h-[85vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white">
            <template x-if="infoCard?.image_url">
                <img :src="infoCard.image_url" alt="" class="max-h-64 w-full object-cover">
            </template>
            <div class="p-6">
                <h2 class="text-xl font-bold text-deltion-blue-900" x-text="infoCard?.name"></h2>
                <p class="mt-1 text-sm text-gray-500" x-show="infoCard?.location" x-text="infoCard?.location"></p>
                <p class="mt-4 whitespace-pre-line text-sm text-gray-700" x-text="infoCard?.full_description"></p>
                <p class="mt-4 text-sm text-gray-500">
                    Meer over het programma lees je
                    <a href="https://xerte.deltion.nl/play.php?template_id=8708#programma" target="_blank" class="font-semibold text-deltion-blue-900 underline">hier</a>.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="infoCard = null" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Sluiten</button>
                    <button type="button" x-show="infoCard && canChoose(infoCard.id) && selected[activeRound] !== infoCard.id"
                            @click="choose(infoCard.id)"
                            class="rounded-md bg-deltion-blue-900 px-4 py-2 text-sm font-semibold text-white hover:bg-deltion-blue-950"
                            x-text="`Kies voor ronde ${activeRound}`"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- foutmelding --}}
    <div x-show="error" x-cloak @click.self="error = null"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-xl bg-white p-6">
            <h3 class="text-lg font-bold text-deltion-blue-900" x-text="error?.title"></h3>
            <p class="mt-2 text-sm text-gray-700" x-text="error?.message"></p>
            <div class="mt-5 flex justify-end">
                <button type="button" @click="error = null" class="rounded-md bg-deltion-blue-900 px-4 py-2 text-sm font-semibold text-white">Sluiten</button>
            </div>
        </div>
    </div>
</div>
