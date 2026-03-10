<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('students-overview')" wire:navigate>Terug naar studenten overzicht</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">{{ $user->name }} Bewerken</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">
        @if (session('status') === 'success')
            <x-success-msg message="{{ session('message') }}" color="green" />
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-lg font-semibold text-deltion-blue-900 mb-4">Studentgegevens</h2>

                <div class="space-y-4">
                    <div>
                        <label for="student-name" class="block text-sm font-bold text-deltion-blue-900 mb-2">Naam</label>
                        <input id="student-name" type="text" value="{{ $user->name }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>

                    <div>
                        <label for="student-email" class="block text-sm font-bold text-deltion-blue-900 mb-2">Email</label>
                        <input id="student-email" type="text" value="{{ $user->email }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>

                    <div>
                        <label for="student-class" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                        <input id="student-class" type="text" value="{{ $user->class }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-lg font-semibold text-deltion-blue-900 mb-4">Inschrijvingen per ronde</h2>

                <form method="POST" action="{{ route('edit-student.bookings.update', $user->id) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    @for ($round = 1; $round <= 3; $round++)
                        @php
                            $booking = $bookingsByRound[$round] ?? null;
                            $selectedWmId = old('rounds.' . $round, $booking['wm_id'] ?? '');
                            $options = $workshopOptionsByRound[$round] ?? [];
                        @endphp

                        <div class="rounded-lg border p-4 {{ $booking ? 'border-deltion-blue-200 bg-deltion-blue-50' : 'border-gray-300 bg-gray-100' }}">
                            <label for="round-{{ $round }}" class="block text-sm font-semibold {{ $booking ? 'text-deltion-blue-900' : 'text-gray-700' }}">Ronde {{ $round }}</label>

                            @if ($booking)
                                <p class="text-xs text-gray-700 mt-1">Huidig: {{ $booking['workshop'] }} ({{ $booking['time'] }})</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">Huidig: Niet ingeschreven</p>
                            @endif

                            <select
                                id="round-{{ $round }}"
                                name="rounds[{{ $round }}]"
                                class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Niet ingeschreven</option>
                                @foreach ($options as $option)
                                    <option value="{{ $option['id'] }}" {{ (string) $selectedWmId === (string) $option['id'] ? 'selected' : '' }}>
                                        {{ $option['name'] }}
                                    </option>
                                @endforeach
                            </select>

                            @error('rounds.' . $round)
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endfor

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            Inschrijvingen opslaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

