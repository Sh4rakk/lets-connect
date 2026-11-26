@php($editing = $workshop->exists)

<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('workshop-dashboard')">Terug naar workshop overzicht</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">{{ $editing ? $workshop->name . ' bewerken' : 'Workshop toevoegen' }}</h1>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <form method="POST"
              action="{{ $editing ? route('workshops.update', $workshop) : route('workshops.store') }}"
              enctype="multipart/form-data"
              class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 sm:p-10"
              x-data="{ preview: @js($workshop->image_url), fileName: '' }">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">Niet alles is goed ingevuld, kijk de velden hieronder na.</div>
            @endif

            {{-- gegevens --}}
            <div class="grid gap-4 sm:grid-cols-[1fr_200px]">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-deltion-blue-900">Titel</label>
                    <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name', $workshop->name) }}"
                           placeholder="Bijv. Fotografie in de studio"
                           class="w-full rounded-md border-gray-300 text-sm focus:border-deltion-orange-900 focus:ring-deltion-orange-900">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <label for="location" class="mb-1.5 block text-sm font-medium text-deltion-blue-900">Lokaal</label>
                    <input id="location" name="location" type="text" maxlength="255" value="{{ old('location', $workshop->location) }}"
                           placeholder="Bijv. B1.12"
                           class="w-full rounded-md border-gray-300 text-sm focus:border-deltion-orange-900 focus:ring-deltion-orange-900">
                    <x-input-error :messages="$errors->get('location')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="full_description" class="mb-1.5 block text-sm font-medium text-deltion-blue-900">Beschrijving</label>
                <textarea id="full_description" name="full_description" rows="5" maxlength="9999"
                          placeholder="Wat gaan studenten doen? Dit zien ze als ze op de i klikken."
                          class="w-full rounded-md border-gray-300 text-sm focus:border-deltion-orange-900 focus:ring-deltion-orange-900">{{ old('full_description', $workshop->full_description) }}</textarea>
                <x-input-error :messages="$errors->get('full_description')" class="mt-1" />
            </div>

            <hr class="border-gray-200">

            {{-- afbeelding --}}
            <div>
                <span class="mb-1.5 block text-sm font-medium text-deltion-blue-900">Afbeelding</span>

                <div x-show="preview" x-cloak class="flex items-center gap-4">
                    <img :src="preview" alt="" class="h-28 w-48 rounded-md object-cover">
                    <div class="space-y-2">
                        <label for="image" class="inline-block cursor-pointer rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Vervangen</label>
                        <p class="text-xs text-gray-500" x-text="fileName"></p>
                    </div>
                </div>

                <label for="image" x-show="!preview"
                       class="flex cursor-pointer flex-col items-center gap-1 rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center hover:border-deltion-blue-900">
                    <span class="text-sm font-semibold text-deltion-blue-900">Klik om een afbeelding te kiezen</span>
                    <span class="text-xs text-gray-500">JPG, PNG of WEBP, max 4 MB, liefst liggend</span>
                </label>

                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only"
                       @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); fileName = f.name; }">
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>

            <hr class="border-gray-200">

            {{-- rondes --}}
            <div>
                <span class="mb-1 block text-sm font-medium text-deltion-blue-900">Rondes en capaciteit</span>
                <x-input-error :messages="$errors->get('rounds')" class="mb-1" />

                <div class="divide-y divide-gray-200 border-b border-gray-200">
                    @foreach ($rounds as $i => $round)
                        @php($enabled = (bool) old("rounds.{$round['id']}.enabled", $round['enabled']))
                        @php($locked = $round['enabled'] && $round['bookings'] > 0)
                        <div class="py-3" x-data="{ on: @js($enabled) }">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                                @if ($locked)
                                    {{-- ronde met inschrijvingen kan niet uit --}}
                                    <input type="hidden" name="rounds[{{ $round['id'] }}][enabled]" value="1">
                                @else
                                    <input type="hidden" name="rounds[{{ $round['id'] }}][enabled]" value="0">
                                @endif

                                <label class="flex flex-1 items-center gap-3 {{ $locked ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                       @if ($locked) title="Er staan al studenten ingeschreven, deze ronde kan niet uit." @endif>
                                    <input type="checkbox" class="peer sr-only" x-model="on"
                                           @if (!$locked) name="rounds[{{ $round['id'] }}][enabled]" value="1" @endif
                                           @disabled($locked)>
                                    <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-deltion-orange-900 {{ $locked ? 'opacity-50' : '' }}"
                                          :class="on ? 'bg-green-600' : 'bg-gray-300'">
                                        <span class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform" :class="on ? 'translate-x-[18px]' : 'translate-x-0.5'"></span>
                                    </span>
                                    <span class="text-sm font-semibold" :class="on ? 'text-deltion-blue-900' : 'text-gray-500'">Ronde {{ $i + 1 }}</span>
                                    <span class="text-sm text-gray-500">{{ $round['time'] }}</span>
                                </label>

                                @if ($round['bookings'] > 0)
                                    <span class="text-xs font-medium text-gray-500">{{ $round['bookings'] }} ingeschreven</span>
                                @endif

                                <div class="flex items-center gap-2">
                                    <input type="number" min="{{ max(1, $round['bookings']) }}" max="1000"
                                           name="rounds[{{ $round['id'] }}][capacity]"
                                           value="{{ old("rounds.{$round['id']}.capacity", $round['capacity']) }}"
                                           :disabled="!on" :required="on"
                                           aria-label="Capaciteit ronde {{ $i + 1 }}"
                                           class="w-[5rem] rounded-md border-gray-300 text-right text-sm focus:border-deltion-orange-900 focus:ring-deltion-orange-900 disabled:bg-gray-100 disabled:text-gray-400">
                                    <span class="text-xs text-gray-500">plekken</span>
                                </div>
                            </div>
                            <x-input-error :messages="array_merge($errors->get('rounds.' . $round['id'] . '.enabled'), $errors->get('rounds.' . $round['id'] . '.capacity'))" class="mt-1" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <div class="flex-1">
                    @if ($editing)
                        <button type="submit" form="delete-workshop" class="text-sm font-semibold text-red-700 hover:underline">Workshop verwijderen</button>
                    @endif
                </div>
                <a href="{{ route('workshop-dashboard') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Annuleren</a>
                <button type="submit" class="rounded-md bg-deltion-orange-900 px-5 py-2 text-sm font-semibold uppercase tracking-wide text-white hover:bg-deltion-orange-950">Opslaan</button>
            </div>
        </form>

        @if ($editing)
            @php($totalBookings = collect($rounds)->sum('bookings'))
            <form id="delete-workshop" method="POST" action="{{ route('workshops.destroy', $workshop) }}"
                  onsubmit="return confirm(@js($totalBookings > 0
                      ? "Weet je het zeker? Er staan {$totalBookings} inschrijving(en) op deze workshop. Die studenten moeten dan opnieuw kiezen."
                      : 'Weet je zeker dat je deze workshop wilt verwijderen?'))">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</x-app-layout>
