<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('dashboard')">Terug naar workshop selectie</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Admin Dashboard</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">
        <div class="flex flex-row justify-center gap-8 mb-12">

            <a href="{{ route('students-overview') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105 shadow-lg" >
                Studenten overzicht
            </a>

            <a href="{{ route('workshop-dashboard') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105 shadow-lg" >
                Workshop overzicht
            </a>

            <a href="{{ route('class-dashboard') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105 shadow-lg" >
                Klas overzicht
            </a>

        </div>

        {{-- De cijfers komen uit /api/statistics, zie resources/js/admin/statistics.js --}}
        <div x-data="statistics">
        <div class="overflow-hidden max-w-5xl m-auto mb-8 rounded-xl border border-deltion-orange-200 bg-deltion-orange-50 shadow-sm">
            <div class="bg-deltion-orange-900 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Dashboard Overzicht</h2>
            </div>
            <div class="p-6 space-y-3 bg-white">
                <p x-show="failed" x-cloak class="text-red-600">De cijfers konden niet worden opgehaald.</p>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Laatst ingeschreven student:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="stats ? latestUser : '...'"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Populairste workshop:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="stats ? popularWorkshop : '...'"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Totaal aantal studenten:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="value('total_students')"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Totaal ingeschreven studenten:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="value('total_bookings')"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Mogelijk dubbel student:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="value('total_ghosts')"></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Studenten zonder workshops:</span>
                    <span class="font-semibold text-deltion-orange-900" x-text="value('no_workshops')"></span>
                </div>
            </div>
        </div>

        <div x-show="stats?.students_without_workshops?.length > 0" x-cloak class="overflow-hidden max-w-5xl m-auto rounded-xl border border-red-200 bg-red-50 shadow-sm">
            <div class="bg-red-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Studenten zonder workshop inschrijvingen</h2>
            </div>
            <div class="p-6 bg-white    ">
                <div class="space-y-2">
                    <template x-for="student in stats?.students_without_workshops ?? []" :key="student.id">
                        <a :href="'{{ url('/edit-student') }}/' + student.id" class="p-1">
                            <div  class="p-4 bg-white border border-red-200 rounded-lg hover:shadow-md transition-shadow">
                                <p class="font-semibold text-gray-900" x-text="student.name"></p>
                                <p class="text-sm text-gray-600 mt-1">Klas: <span class="font-medium" x-text="student.class"></span> | Email: <span class="font-medium" x-text="student.email"></span></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </div>

</x-app-layout>
