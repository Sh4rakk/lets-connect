<x-workshop-layout title="Workshop overzicht" :back-url="route('selectionDashboard')" back-label="Admin dashboard">
    <div x-data="workshopsOverview({ signupsOpen: @js($signupsOpen), firstRound: @js($moments->first()?->id) })">
        @if (session('status'))
            <p class="mb-4 p-3 rounded-md bg-green-100 text-green-800 text-sm">{{ session('status') }}</p>
        @endif

        <p x-show="message" x-cloak x-text="message?.text" class="mb-4 p-3 rounded-md text-sm"
           :class="message?.color === 'green' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"></p>

        <div class="flex flex-wrap items-end justify-between gap-x-3 mb-4 border-b border-gray-300">
            <div class="flex flex-wrap gap-x-5 text-sm" role="tablist">
                @foreach ($moments as $moment)
                    <button type="button" role="tab" @click="round = {{ $moment->id }}"
                            :aria-selected="round === {{ $moment->id }}"
                            class="pt-2 pb-3 -mb-px border-b-2 whitespace-nowrap"
                            :class="round === {{ $moment->id }} ? 'border-deltion-blue-900 text-deltion-blue-900 font-bold' : 'border-transparent text-gray-500'">
                        Ronde {{ $moment->id }}
                        <span class="ml-1 text-[11px] font-normal text-gray-500">{{ Str::before($moment->time, ' ') }} &middot; {{ $workshopMoments->get($moment->id)?->count() ?? 0 }} workshops</span>
                    </button>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-3 pb-2">
                <label class="flex items-center gap-2 text-sm font-medium cursor-pointer">
                    <x-round-switch data-signups x-bind:checked="signupsOpen" x-bind:disabled="toggling" x-on:change="toggleSignups()" />
                    <span x-text="signupsOpen ? 'Inschrijvingen open' : 'Inschrijvingen gesloten'"></span>
                </label>
                <a href="{{ route('export-all-users') }}" class="px-3 py-2 text-sm font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50">Exporteren</a>
                <a href="{{ route('workshops.create') }}" class="px-3 py-2 text-sm font-semibold tracking-wider text-white bg-deltion-orange-900 rounded-md hover:bg-deltion-orange-700">+ WORKSHOP</a>
            </div>
        </div>

        @foreach ($moments as $moment)
            <div x-show="round === {{ $moment->id }}" @if (! $loop->first) x-cloak @endif class="relative overflow-x-auto bg-white border border-gray-200 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 text-xs text-gray-600 border-b border-gray-200">
                            <th class="px-4 py-3 font-medium">Workshop</th>
                            <th class="px-4 py-3 font-medium">Lokaal</th>
                            <th class="px-4 py-3 font-medium">Ingeschreven</th>
                            <th class="px-4 py-3"><span class="sr-only">Acties</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($workshopMoments->get($moment->id, collect()) as $wm)
                            @php($full = $wm->bookings_count >= $wm->maxSpots())
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        @if ($wm->workshop->image_url)
                                            <img src="{{ $wm->workshop->image_url }}" alt="" loading="lazy" class="h-9 w-14 shrink-0 rounded-md object-cover bg-gray-200">
                                        @else
                                            <span class="h-9 w-14 shrink-0 rounded-md bg-gray-200 border border-dashed border-gray-300"></span>
                                        @endif
                                        <span class="font-semibold">{{ $wm->workshop->name }}</span>
                                        @if ($wm->workshop->is_manual)
                                            <span class="px-1.5 py-0.5 text-xs text-gray-500 border border-gray-300 rounded">handmatig</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $wm->workshop->location }}</td>
                                <td class="px-4 py-2 whitespace-nowrap {{ $full ? 'text-red-700' : '' }}">
                                    {{ $wm->bookings_count }} / {{ $wm->maxSpots() }}
                                    @if ($full)
                                        <span class="ml-1 text-xs">vol</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-2 whitespace-nowrap text-xs font-medium">
                                        <a href="{{ route('workshop-moment.showbookings', $wm) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded hover:bg-gray-50">Inschrijvingen</a>
                                        <a href="{{ route('workshop-moment.qr', $wm) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded hover:bg-gray-50">QR-code</a>
                                        <a href="{{ route('workshops.edit', $wm->workshop) }}" class="px-3 py-1.5 text-white bg-deltion-blue-900 border border-deltion-blue-900 rounded hover:bg-deltion-blue-800">Bewerken</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Geen workshops in deze ronde.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-workshop-layout>
