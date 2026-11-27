@php
    $editing = $workshop->exists;
    $input = 'w-full rounded-md border-gray-300 text-sm focus:border-deltion-blue-900 focus:ring-deltion-blue-900';
    $hasOld = session()->hasOldInput();
@endphp

<x-workshop-layout :title="$editing ? $workshop->name.' bewerken' : 'Workshop toevoegen'"
                   :back-url="route('workshop-dashboard')" back-label="Workshop overzicht">
    <div class="mx-auto max-w-xl bg-white border border-gray-200 rounded-xl p-5 sm:p-8">
        <form id="workshop-form" method="POST" enctype="multipart/form-data"
              action="{{ $editing ? route('workshops.update', $workshop) : route('workshops.store') }}"
              x-data="workshopForm({ imageUrl: @js($workshop->image_url) })">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="name" class="block mb-1 text-xs font-medium">Titel</label>
                    <input id="name" name="name" type="text" required maxlength="255" class="{{ $input }}"
                           placeholder="Bijv. Fotografie in de studio" value="{{ old('name', $workshop->name) }}">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <label for="location" class="block mb-1 text-xs font-medium">Lokaal</label>
                    <input id="location" name="location" type="text" maxlength="255" class="{{ $input }}"
                           placeholder="Bijv. B1.12" value="{{ old('location', $workshop->location) }}">
                    <x-input-error :messages="$errors->get('location')" class="mt-1" />
                </div>
            </div>

            <div class="mt-4">
                <label for="full_description" class="block mb-1 text-xs font-medium">Beschrijving</label>
                <textarea id="full_description" name="full_description" rows="5" maxlength="9999" class="{{ $input }}"
                          placeholder="Wat gaan studenten doen? Dit ziet de student als hij op de i klikt.">{{ old('full_description', trim((string) $workshop->full_description)) }}</textarea>
                <x-input-error :messages="$errors->get('full_description')" class="mt-1" />
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <span class="block mb-1 text-xs font-medium">Afbeelding</span>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png" class="sr-only" x-ref="image" @change="pick($event)">

                <label for="image" x-show="!preview" @if ($workshop->image_url) x-cloak @endif
                       class="block p-6 text-center bg-gray-50 border border-dashed rounded-md cursor-pointer"
                       :class="dragging ? 'border-deltion-blue-900' : 'border-gray-300'"
                       @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="drop($event)">
                    <span class="block text-sm font-semibold">Sleep een afbeelding hierheen of klik om te kiezen</span>
                    <span class="block text-xs text-gray-500">JPG of PNG, liefst liggend, maximaal 2 MB</span>
                </label>

                <div x-show="preview" x-cloak class="flex flex-wrap items-center gap-4" @dragover.prevent @drop.prevent="drop($event)">
                    <img :src="preview" alt="Afbeelding van de workshop" class="h-24 w-36 rounded-md object-cover bg-gray-200">
                    <div class="min-w-0">
                        <label for="image" class="inline-block px-3 py-2 text-sm font-medium border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">Vervangen</label>
                        <p class="mt-1 text-xs text-gray-500 break-all" x-text="fileName"></p>
                    </div>
                </div>
                <p x-show="imageError" x-cloak x-text="imageError" class="mt-1 text-sm text-red-600"></p>
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
                <span class="block mb-1 text-xs font-medium">Rondes en capaciteit</span>
                <x-input-error :messages="$errors->get('rounds')" class="mb-1" />

                @foreach ($rounds as $round)
                    @php
                        $locked = $round['bookings'] > 0;
                        $enabled = $locked || ($hasOld ? (bool) old("rounds.{$round['id']}.enabled") : $round['enabled']);
                    @endphp
                    <div class="py-2 border-b border-gray-200" x-data="{ enabled: @js($enabled) }">
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            @if ($locked)
                                <input type="hidden" name="rounds[{{ $round['id'] }}][enabled]" value="1">
                            @endif
                            <label class="flex items-center gap-3 {{ $locked ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                   @if ($locked) title="Deze ronde heeft inschrijvingen en kan niet uit" @endif>
                                <x-round-switch name="rounds[{{ $round['id'] }}][enabled]" value="1" x-model="enabled" :locked="$locked" />
                                <span :class="enabled ? 'font-semibold' : 'text-gray-500'">Ronde {{ $round['id'] }}</span>
                                <span class="text-xs text-gray-500">{{ $round['time'] }}</span>
                            </label>
                            <div class="ml-auto flex items-center gap-3">
                                @if ($locked)
                                    <span class="text-xs text-gray-600">{{ $round['bookings'] }} ingeschreven</span>
                                @endif
                                <input type="number" name="rounds[{{ $round['id'] }}][capacity]" min="{{ max(1, $round['bookings']) }}" max="999"
                                       aria-label="Plekken in ronde {{ $round['id'] }}" placeholder="-" :required="enabled" :disabled="!enabled"
                                       value="{{ old("rounds.{$round['id']}.capacity", $round['capacity']) }}"
                                       class="w-16 py-1 text-sm text-right rounded-md border-gray-300 disabled:bg-gray-100 disabled:text-gray-400 focus:border-deltion-blue-900 focus:ring-deltion-blue-900">
                                <span class="text-xs text-gray-500">plekken</span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('rounds.'.$round['id'].'.capacity')" class="mt-1" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <label for="leader_email" class="block mb-1 text-xs font-medium">E-mail workshopleider (optioneel)</label>
                <input id="leader_email" name="leader_email" type="email" maxlength="255" class="{{ $input }}"
                       placeholder="Bijv. docent@deltion.nl" value="{{ old('leader_email', $workshop->leader?->email) }}">
                <p class="mt-1 text-xs text-gray-500">Deze persoon kan inloggen en ziet de verwachte studenten en QR-codes van deze workshop.</p>
                <x-input-error :messages="$errors->get('leader_email')" class="mt-1" />
            </div>
        </form>

        <x-input-error :messages="$errors->get('delete')" class="mt-4" />

        <div class="mt-8 flex flex-wrap items-center gap-3">
            @if ($editing)
                <form method="POST" action="{{ route('workshops.destroy', $workshop) }}"
                      onsubmit="return confirm('Weet je zeker dat je deze workshop wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-red-700 hover:underline">Workshop verwijderen</button>
                </form>
            @endif
            <div class="ml-auto flex gap-2 text-sm">
                <a href="{{ route('workshop-dashboard') }}" class="px-4 py-2 font-medium border border-gray-300 rounded-md hover:bg-gray-50">Annuleren</a>
                <button type="submit" form="workshop-form" class="px-4 py-2 font-semibold tracking-wider text-white bg-deltion-orange-900 rounded-md hover:bg-deltion-orange-700">OPSLAAN</button>
            </div>
        </div>
    </div>
</x-workshop-layout>
