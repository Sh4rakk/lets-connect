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

        <div class="overflow-hidden max-w-5xl m-auto mb-8 rounded-xl border border-deltion-orange-200 bg-deltion-orange-50 shadow-sm">
            <div class="bg-deltion-orange-900 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Dashboard Overzicht</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Laatst ingeschreven student:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $latestUser->name ?? 'N/A' }} ({{ $latestUser->created_at->format('d-m-Y') ?? 'N/A' }})</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Populairste workshop:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $popularWorkshop->name ?? 'N/A' }} ({{ $popularWorkshop->bookings_count ?? 0 }} bookings)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Totaal aantal studenten:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $totalStudents ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Totaal ingeschreven studenten:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $totalBookings ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-deltion-orange-200">
                    <span class="text-gray-700">Mogelijk dubbel student:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $totalGhosts ?? '0' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-700">Studenten zonder workshops:</span>
                    <span class="font-semibold text-deltion-orange-900">{{ $noWorkshops ?? '0' }}</span>
                </div>
            </div>
        </div>

        @if($studentsWithoutWorkshops && count($studentsWithoutWorkshops) > 0)
            <div class="overflow-hidden max-w-5xl m-auto rounded-xl border border-red-200 bg-red-50 shadow-sm">
                <div class="bg-red-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Studenten zonder workshop inschrijvingen</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        @foreach($studentsWithoutWorkshops as $student)
                            <a href="{{route('edit-student', $student->id)}}" class="p-1">
                                <div  class="p-4 bg-white border border-red-200 rounded-lg hover:shadow-md transition-shadow">
                                    <p class="font-semibold text-gray-900">{{ $student->name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">Klas: <span class="font-medium">{{ $student->class }}</span> | Email: <span class="font-medium">{{ $student->email }}</span></p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
