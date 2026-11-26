<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Workshop Overzicht</h1>
    </x-slot>

    {{-- Inschrijvingen open/dicht gaat via de api, zie resources/js/admin/workshopsOverview.js --}}
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6"
         x-data="workshopsOverview({ signupsOpen: @js(\App\Models\Setting::signupsOpen()), firstRound: @js($rounds->first()['moment']->id ?? null) })">

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 flex items-start rounded-lg bg-green-100 p-4 text-green-700">
                <div class="flex-1">{{ session('status') }}</div>
                <button type="button" @click="show = false" class="ml-3" aria-label="Sluit bericht">✕</button>
            </div>
        @endif

        <template x-if="message">
            <div class="mb-4 flex items-start rounded-lg p-4" :class="message.color === 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                <div class="flex-1" x-text="message.text"></div>
                <button type="button" @click="message = null" class="ml-3" aria-label="Sluit bericht">✕</button>
            </div>
        </template>

        {{-- acties, daaronder de rondes als tabs --}}
        <div class="mb-4 flex flex-wrap items-center justify-end gap-3">
            <button type="button" @click="toggleSignups()" :disabled="toggling"
                    class="flex items-center gap-2 text-sm font-semibold text-deltion-blue-900 disabled:opacity-50"
                    role="switch" :aria-checked="signupsOpen">
                <span class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors" :class="signupsOpen ? 'bg-green-600' : 'bg-gray-300'">
                    <span class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform" :class="signupsOpen ? 'translate-x-[18px]' : 'translate-x-0.5'"></span>
                </span>
                <span x-text="signupsOpen ? 'Inschrijvingen open' : 'Inschrijvingen gesloten'"></span>
            </button>

            <a href="{{ route('export-all-users') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Exporteren</a>
            <a href="{{ route('workshops.create') }}" class="rounded-md bg-deltion-orange-900 px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white hover:bg-deltion-orange-950">+ Workshop</a>
        </div>

        <nav class="flex flex-wrap gap-x-6 border-b border-gray-300" aria-label="Rondes">
            @foreach ($rounds as $i => $round)
                <button type="button"
                        @click="round = {{ $round['moment']->id }}"
                        class="-mb-px flex items-baseline gap-2 whitespace-nowrap border-b-[3px] pb-3 transition-colors"
                        :class="round === {{ $round['moment']->id }} ? 'border-deltion-blue-900 text-deltion-blue-900 font-bold' : 'border-transparent text-gray-500 font-medium hover:text-deltion-blue-900'">
                    <span>Ronde {{ $i + 1 }}</span>
                    <span class="text-xs font-normal text-gray-500">{{ $round['moment']->time }} · {{ $round['workshopMoments']->count() }} workshops</span>
                </button>
            @endforeach
        </nav>

        @foreach ($rounds as $round)
            <div x-show="round === {{ $round['moment']->id }}" @if (!$loop->first) x-cloak @endif
                 class="mt-5 overflow-hidden rounded-xl border border-gray-200 bg-white">
                @if ($round['workshopMoments']->isEmpty())
                    <p class="p-6 text-sm text-gray-500">Er zijn nog geen workshops in deze ronde.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                                <tr>
                                    <th class="px-5 py-3">Workshop</th>
                                    <th class="px-5 py-3">Lokaal</th>
                                    <th class="px-5 py-3">Ingeschreven</th>
                                    <th class="px-5 py-3"><span class="sr-only">Acties</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($round['workshopMoments'] as $wm)
                                    @php($capacity = $wm->effectiveCapacity())
                                    @php($full = $wm->bookings_count >= $capacity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                @if ($wm->workshop->image_url)
                                                    <img src="{{ $wm->workshop->image_url }}" alt="" loading="lazy" class="h-10 w-16 shrink-0 rounded object-cover">
                                                @else
                                                    <div class="h-10 w-16 shrink-0 rounded bg-gray-200"></div>
                                                @endif
                                                <span class="font-semibold text-deltion-blue-900">{{ $wm->workshop->name }}</span>
                                                @if ($wm->workshop->is_manual)
                                                    <span class="rounded border border-gray-300 px-1.5 py-0.5 text-[11px] font-medium text-gray-500">handmatig</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-3 text-gray-500">{{ $wm->workshop->location ?: '–' }}</td>
                                        <td class="whitespace-nowrap px-5 py-3 font-medium {{ $full ? 'text-red-700' : 'text-deltion-blue-900' }}">
                                            {{ $wm->bookings_count }} / {{ $capacity }}
                                            @if ($full)
                                                <span class="ml-1 text-xs font-semibold">vol</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex justify-end gap-2 whitespace-nowrap">
                                                <a href="{{ route('workshop-moment.showbookings', ['wsm' => $wm]) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-deltion-blue-900 hover:bg-gray-50">Inschrijvingen</a>
                                                <a href="{{ route('workshop.qr', $wm->workshop) }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-deltion-blue-900 hover:bg-gray-50">QR-code</a>
                                                <a href="{{ route('workshops.edit', $wm->workshop) }}" class="rounded-md bg-deltion-blue-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-deltion-blue-950">Bewerken</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>
