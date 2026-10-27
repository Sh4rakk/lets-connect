<x-app-layout>
    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('workshop-dashboard')">Terug naar overzicht</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">{{$wsm->workshop->name}} {{$wsm->moment->time}}</h1>
    </x-slot>

    {{-- De studenten komen uit /api/workshop-moments/{id}, zie resources/js/admin/workshopMomentBookings.js --}}
    <div class="overflow-hidden max-w-5xl m-auto mt-6 mb-6 rounded-xl border border-gray-200 bg-white shadow-sm"
         x-data="workshopMomentBookings({ workshopMomentId: @js($wsm->id), className: @js($class) })">
        <table x-show="visibleBookings.length > 0" x-cloak class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto">
            <thead class="bg-blue-00">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                <template x-for="booking in visibleBookings" :key="booking.id">
                    <tr @click="window.location.href = '{{ url('/edit-student') }}/' + booking.student.id" class="cursor-pointer hover:bg-deltion-orange-100 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6" x-text="booking.student.name"></td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700" x-text="booking.student.email"></td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700" x-text="booking.student.class"></td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div x-show="loading" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Inschrijvingen laden...</div>
        <div x-show="error" x-cloak x-text="error" class="py-4 pl-4 pr-3 text-sm font-medium text-red-600"></div>
        <div x-show="!loading && !error && visibleBookings.length === 0" x-cloak class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
            Geen inschrijvingen gevonden<span x-show="className" x-text="' voor klas ' + className + '.'"></span>
        </div>
    </div>
</x-app-layout>
