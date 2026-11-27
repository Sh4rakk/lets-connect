@php
    $expected = $students->count();
    $spots = $wsm->maxSpots();
@endphp

<x-workshop-layout title="Verwachting" :back-url="route('leader.dashboard')"
                   :back-label="auth()->user()->hasRole('admin') ? 'Workshopleider dashboard' : 'Mijn workshops'">
    <div class="mx-auto max-w-2xl">
        <nav class="flex flex-wrap gap-2 mb-4 print:hidden" aria-label="Rondes">
            @foreach ($rounds as $round)
                <a href="{{ route('leader.expected', $round) }}" @if ($round->id === $wsm->id) aria-current="page" @endif
                   class="px-3 py-2 text-sm border rounded-md {{ $round->id === $wsm->id ? 'bg-deltion-blue-900 border-deltion-blue-900 text-white' : 'bg-white border-gray-300 hover:bg-gray-50' }}">Ronde {{ $round->moment_id }}</a>
            @endforeach
        </nav>

        <div class="mb-5 bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-lg font-bold">{{ $wsm->workshop->name }}</h2>
            <p class="text-sm text-gray-500">Ronde {{ $wsm->moment_id }} ({{ $wsm->moment?->time }}), {{ $wsm->workshop->location ?: 'Lokaal onbekend' }}</p>

            <dl class="mt-4 grid grid-cols-3 gap-3 text-center">
                <div class="p-3 bg-gray-50 rounded-md">
                    <dt class="text-xs text-gray-500">Verwacht</dt>
                    <dd class="text-2xl font-bold">{{ $expected }}</dd>
                </div>
                <div class="p-3 bg-gray-50 rounded-md">
                    <dt class="text-xs text-gray-500">Plekken</dt>
                    <dd class="text-2xl font-bold">{{ $spots }}</dd>
                </div>
                <div class="p-3 bg-gray-50 rounded-md">
                    <dt class="text-xs text-gray-500">Vrij</dt>
                    <dd class="text-2xl font-bold">{{ max(0, $spots - $expected) }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-600 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Student</th>
                        <th class="px-4 py-3 font-medium">Klas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($students as $student)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $student->class }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-500">Nog geen inschrijvingen voor deze ronde.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-workshop-layout>
