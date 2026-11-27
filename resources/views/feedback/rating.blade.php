@php
    $input = 'w-full rounded-md border-gray-300 text-sm focus:border-deltion-blue-900 focus:ring-deltion-blue-900';
@endphp

<x-feedback-layout :title="$round->workshop->name">
    <form method="POST" action="{{ route('feedback.rating', $token) }}" class="flex flex-col flex-1"
          x-data="{ sending: false }" @submit="sending = true" @pageshow.window="sending = false">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-sm text-gray-500">Hoe vond je</p>
            <h1 class="mt-1 text-xl font-bold break-words">{{ $round->workshop->name }}</h1>
            <p class="mt-1 text-xs text-gray-500">Ronde {{ $round->moment_id }} &middot; je beoordeling is anoniem</p>

            <fieldset class="relative mt-2">
                <legend class="sr-only">Cijfer</legend>
                <div class="flex justify-between">
                    @foreach (range(1, 5) as $number)
                        <x-feedback-choice round name="rating" :value="$number" :checked="old('rating') == $number">{{ $number }}</x-feedback-choice>
                    @endforeach
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Slecht</span>
                    <span>Top</span>
                </div>
            </fieldset>
            <x-input-error :messages="$errors->get('rating')" class="mt-1" />

            <div class="mt-3">
                <label for="good" class="block mb-1 text-sm font-medium">Wat vond je goed? (optioneel)</label>
                <input id="good" name="good" type="text" maxlength="1000" placeholder="Typ hier..." value="{{ old('good') }}" class="{{ $input }}">
                <x-input-error :messages="$errors->get('good')" class="mt-1" />
            </div>

            <div class="mt-3">
                <label for="better" class="block mb-1 text-sm font-medium">Wat kan beter? (optioneel)</label>
                <input id="better" name="better" type="text" maxlength="1000" placeholder="Typ hier..." value="{{ old('better') }}" class="{{ $input }}">
                <x-input-error :messages="$errors->get('better')" class="mt-1" />
            </div>
        </div>

        <button type="submit" :disabled="sending" class="w-full mt-auto py-3 text-sm font-semibold tracking-wider text-white bg-deltion-orange-900 rounded-md hover:bg-deltion-orange-700 disabled:opacity-50">VERSTUREN</button>
    </form>
</x-feedback-layout>
