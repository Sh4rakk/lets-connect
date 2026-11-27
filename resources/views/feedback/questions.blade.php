@php
    $questions = [
        'learned' => ['Was de workshop leerzaam?', \App\Models\Feedback::SCALE],
        'fun' => ['Was de workshop leuk?', \App\Models\Feedback::SCALE],
        'recommend' => ['Zou je hem aanraden?', ['1' => 'Ja', '0' => 'Nee']],
    ];
@endphp

<x-feedback-layout :title="$round->workshop->name">
    <form method="POST" action="{{ route('feedback.questions', $token) }}" class="flex flex-col flex-1"
          x-data="{ sending: false }" @submit="sending = true" @pageshow.window="sending = false">
        @csrf
        <input type="hidden" name="step_token" value="{{ $stepToken }}">

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h1 class="text-xl font-bold break-words">{{ $round->workshop->name }}</h1>
            <p class="mt-1 text-xs text-gray-500">3 korte vragen &middot; anoniem</p>

            @foreach ($questions as $name => [$question, $options])
                <fieldset class="relative mt-3">
                    <legend class="mb-2 text-sm font-medium">{{ $question }}</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($options as $value => $label)
                            <x-feedback-choice :name="$name" :value="$value" :checked="($answers[$name] ?? null) === (string) $value">{{ $label }}</x-feedback-choice>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            @if ($error)
                <p class="mt-3 text-sm text-red-600">{{ $error }}</p>
            @endif
        </div>

        <button type="submit" :disabled="sending" class="w-full mt-auto py-3 text-sm font-semibold tracking-wider text-white bg-deltion-orange-900 rounded-md hover:bg-deltion-orange-700 disabled:opacity-50">VERSTUREN</button>
    </form>
</x-feedback-layout>
