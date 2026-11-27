<x-workshop-layout title="Feedback" :back-url="route('leader.dashboard')"
                   :back-label="auth()->user()->hasRole('admin') ? 'Workshopleider dashboard' : 'Mijn workshops'">
    <div class="mx-auto max-w-2xl space-y-5">
        <nav class="flex flex-wrap gap-2 print:hidden" aria-label="Rondes">
            @foreach ($rounds as $round)
                <a href="{{ route('leader.feedback', $round) }}" @if ($round->id === $wsm->id) aria-current="page" @endif
                   class="px-3 py-2 text-sm border rounded-md {{ $round->id === $wsm->id ? 'bg-deltion-blue-900 border-deltion-blue-900 text-white' : 'bg-white border-gray-300 hover:bg-gray-50' }}">Ronde {{ $round->moment_id }}</a>
            @endforeach
        </nav>

        <section class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-lg font-bold break-words">{{ $wsm->workshop->name }}</h2>
            <p class="text-sm text-gray-500">Ronde {{ $wsm->moment_id }} ({{ $wsm->moment?->time }}), {{ $wsm->workshop->location ?: 'Lokaal onbekend' }}</p>

            @if ($total > 0)
                <dl class="mt-4 grid grid-cols-3 gap-3 text-center">
                    <div class="px-1 py-3 bg-gray-50 rounded-md">
                        <dt class="text-xs text-gray-500">Gemiddeld</dt>
                        <dd class="text-2xl font-bold">{{ $average }}</dd>
                        <dd class="text-xs text-gray-500">van 5</dd>
                    </div>
                    <div class="px-1 py-3 bg-gray-50 rounded-md">
                        <dt class="text-xs text-gray-500">Beoordelingen</dt>
                        <dd class="text-2xl font-bold">{{ $total }}</dd>
                    </div>
                    <div class="px-1 py-3 bg-gray-50 rounded-md">
                        <dt class="text-xs text-gray-500">Vragen ingevuld</dt>
                        <dd class="text-2xl font-bold">{{ $answered }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-gray-500">Nog geen feedback voor deze ronde.</p>
            @endif
        </section>

        @if ($total > 0)
            <x-feedback-bars title="Cijfer (1 is slecht, 5 is top)" :rows="$ratings" />

            @if ($answered > 0)
                @foreach ($questions as $question => $rows)
                    <x-feedback-bars :title="$question" :rows="$rows" />
                @endforeach
            @endif

            @foreach ($comments as $title => $texts)
                <section class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="text-sm font-bold">{{ $title }}</h3>
                    @if ($texts->isEmpty())
                        <p class="mt-2 text-sm text-gray-500">Niemand heeft dit ingevuld.</p>
                    @else
                        <ul class="mt-2 divide-y divide-gray-200 text-sm">
                            @foreach ($texts as $text)
                                <li class="py-2 break-words">{{ $text }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        @endif
    </div>
</x-workshop-layout>
