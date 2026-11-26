<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('dashboard')">Terug naar workshop selectie</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Admin dashboard</h1>
    </x-slot>

    @php($signupsOpen = \App\Models\Setting::signupsOpen())

    {{-- De cijfers komen uit /api/statistics, zie resources/js/admin/statistics.js --}}
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6" x-data="statistics">

        <p x-show="failed" x-cloak class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700">De cijfers konden niet worden opgehaald.</p>

        {{-- hoe staan de inschrijvingen ervoor --}}
        <section class="grid overflow-hidden rounded-xl border border-gray-200 bg-white md:grid-cols-[1.4fr_1fr]">
            <div class="flex flex-col justify-center p-6 sm:p-8">
                <p class="text-sm text-gray-600">
                    Inschrijvingen zijn <span class="font-semibold {{ $signupsOpen ? 'text-green-700' : 'text-gray-900' }}">{{ $signupsOpen ? 'open' : 'gesloten' }}</span>
                    · <a href="{{ route('workshop-dashboard') }}" class="text-deltion-blue-900 underline underline-offset-2 hover:no-underline">{{ $signupsOpen ? 'sluiten' : 'openen' }}</a>
                </p>

                <p class="mt-3 text-3xl font-bold text-deltion-blue-900">
                    <span x-text="value('total_bookings')"></span>
                    <span class="text-lg font-semibold text-gray-500">van de <span x-text="value('total_students')"></span> studenten ingeschreven</span>
                </p>

                <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full bg-deltion-orange-900" :style="`width: ${signedUpPercentage}%`"></div>
                </div>

                {{-- de losse cijfers, gewoon op een regel --}}
                <dl class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <div class="flex gap-1.5"><dt class="text-gray-500">Workshops</dt><dd class="font-semibold text-deltion-blue-900" x-text="stats ? workshopsTotal : '...'"></dd></div>
                    <div class="flex gap-1.5"><dt class="text-gray-500">Vol</dt><dd class="font-semibold text-deltion-blue-900" x-text="stats ? fullTotal : '...'"></dd></div>
                    <div class="flex gap-1.5">
                        <dt class="text-gray-500">Zonder workshop</dt>
                        <dd class="font-semibold">
                            <a x-show="stats?.no_workshops > 0" href="#nog-niet-ingeschreven" class="text-red-700 underline underline-offset-2 hover:no-underline" x-text="stats?.no_workshops"></a>
                            <span x-show="!(stats?.no_workshops > 0)" class="text-deltion-blue-900" x-text="value('no_workshops')"></span>
                        </dd>
                    </div>
                    <div class="flex gap-1.5"><dt class="text-gray-500">Mogelijk dubbel</dt><dd class="font-semibold text-deltion-blue-900" x-text="value('total_ghosts')"></dd></div>
                </dl>
            </div>

            <div class="hidden items-end justify-center bg-deltion-orange-50 px-8 pt-8 md:flex">
                <div class="aspect-[910/560] w-full max-w-xs overflow-hidden">
                    <img src="{{ asset('images/Lets-connect-logo.png') }}" alt="" class="w-full mix-blend-multiply">
                </div>
            </div>
        </section>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">

            <div class="space-y-8 lg:col-span-2">
                {{-- bezetting per ronde --}}
                <section>
                    <h2 class="mb-3 text-sm font-bold text-deltion-blue-900">Bezetting per ronde</h2>
                    <div class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white">
                        <template x-for="round in stats?.rounds ?? []" :key="round.id">
                            <div class="px-5 py-4">
                                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                    <p>
                                        <span class="font-semibold text-deltion-blue-900" x-text="'Ronde ' + round.number"></span>
                                        <span class="ml-1 text-xs text-gray-500" x-text="round.time + ' · ' + round.workshops + ' workshops' + (round.full ? ' · ' + round.full + ' vol' : '')"></span>
                                    </p>
                                    <p class="text-sm font-medium text-deltion-blue-900">
                                        <span x-text="round.booked"></span> / <span x-text="round.capacity"></span>
                                        <span class="text-gray-500">plekken</span>
                                    </p>
                                </div>
                                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full"
                                         :class="fill(round) >= 90 ? 'bg-red-600' : 'bg-deltion-blue-900'"
                                         :style="`width: ${fill(round)}%`"></div>
                                </div>
                            </div>
                        </template>
                        <p x-show="stats && !stats.rounds?.length" class="px-5 py-4 text-sm text-gray-500">Er zijn nog geen rondes.</p>
                        <p x-show="!stats && !failed" class="px-5 py-4 text-sm text-gray-500">Laden...</p>
                    </div>
                </section>

                {{-- naar de overzichten --}}
                <section>
                    <h2 class="mb-3 text-sm font-bold text-deltion-blue-900">Overzichten</h2>
                    <nav class="divide-y divide-gray-200 overflow-hidden rounded-xl border border-gray-200 bg-white">
                        <a href="{{ route('workshop-dashboard') }}" class="flex items-baseline justify-between gap-4 px-5 py-3.5 hover:bg-gray-50">
                            <span class="font-semibold text-deltion-blue-900">Workshops</span>
                            <span class="text-right text-sm text-gray-500">rondes, QR-codes, bewerken</span>
                        </a>
                        <a href="{{ route('students-overview') }}" class="flex items-baseline justify-between gap-4 px-5 py-3.5 hover:bg-gray-50">
                            <span class="font-semibold text-deltion-blue-900">Studenten</span>
                            <span class="text-right text-sm text-gray-500">zoeken, workshops omzetten</span>
                        </a>
                        <a href="{{ route('class-dashboard') }}" class="flex items-baseline justify-between gap-4 px-5 py-3.5 hover:bg-gray-50">
                            <span class="font-semibold text-deltion-blue-900">Klassen</span>
                            <span class="text-right text-sm text-gray-500">wie zit waar</span>
                        </a>
                    </nav>
                </section>
            </div>

            <div class="space-y-8">
                {{-- korte info --}}
                <section>
                    <h2 class="mb-3 text-sm font-bold text-deltion-blue-900">Recent</h2>
                    <div class="rounded-xl border border-gray-200 bg-white px-5 text-sm">
                        <div class="border-b border-gray-200 py-4">
                            <p class="text-xs font-semibold text-gray-500">Laatst ingeschreven</p>
                            <p class="mt-1 font-semibold text-deltion-blue-900" x-text="stats ? (stats.latest_user?.name ?? 'Nog niemand') : '...'"></p>
                            <p class="text-gray-500" x-show="latestUserDate" x-text="latestUserDate"></p>
                        </div>
                        <div class="py-4">
                            <p class="text-xs font-semibold text-gray-500">Populairste workshop</p>
                            <p class="mt-1 font-semibold text-deltion-blue-900" x-text="stats ? (stats.popular_workshop?.name ?? 'Nog geen') : '...'"></p>
                            <p class="text-gray-500" x-show="stats?.popular_workshop?.name" x-text="(stats?.popular_workshop?.bookings_count ?? 0) + ' inschrijvingen'"></p>
                        </div>
                    </div>
                </section>

                {{-- exports die je vaak nodig hebt --}}
                <section>
                    <h2 class="mb-3 text-sm font-bold text-deltion-blue-900">Exporteren</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('export-all-users') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Alle studenten</a>
                        <a href="{{ route('export-all-classes') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Alle klassen</a>
                    </div>
                </section>
            </div>
        </div>

        {{-- studenten die nog nergens staan --}}
        <section id="nog-niet-ingeschreven" x-show="stats?.students_without_workshops?.length > 0" x-cloak class="mt-8 scroll-mt-28">
            <div class="mb-3 flex items-baseline gap-2">
                <h2 class="text-sm font-bold text-deltion-blue-900">Nog niet ingeschreven</h2>
                <span class="text-xs text-gray-500" x-text="(stats?.students_without_workshops?.length ?? 0) + ' studenten'"></span>
            </div>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                            <tr>
                                <th class="px-5 py-3">Naam</th>
                                <th class="px-5 py-3">Klas</th>
                                <th class="px-5 py-3">E-mail</th>
                                <th class="px-5 py-3"><span class="sr-only">Acties</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="student in stats?.students_without_workshops ?? []" :key="student.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-semibold text-deltion-blue-900" x-text="student.name"></td>
                                    <td class="whitespace-nowrap px-5 py-3 text-gray-500" x-text="student.class || '–'"></td>
                                    <td class="px-5 py-3 text-gray-500" x-text="student.email"></td>
                                    <td class="px-5 py-3 text-right">
                                        <a :href="'{{ url('/edit-student') }}/' + student.id" class="rounded-md bg-deltion-blue-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-deltion-blue-950">Bewerken</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

</x-app-layout>
