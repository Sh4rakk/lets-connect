<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('dashboard')">Terug naar workshop selectie</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Admin Dashboard</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">
        <div class="flex flex-row justify-center gap-8">

            <a href="{{ route('students-overview') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105" >
                Studenten overzicht
            </a>

            <a href="{{ route('workshop-dashboard') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105" >
                Workshop overzicht
            </a>

            <a href="{{ route('class-dashboard') }}" class="p-12 bg-deltion-blue-900 text-white font-medium text-lg rounded-xl transition-transform hover:scale-105" >
                Klas overzicht
            </a>

        </div>
        <div style="margin: 10vh"></div>
        <div class="p-4 flex flex-col gap-4 outline bg-deltion-orange-900 text-white font-medium text-lg rounded-xl" >
            <a class="">
                Laatst ingeschreven student: {{ $latestUser->name ?? 'N/A' }} ({{ $latestUser->created_at->format('d-m-Y') ?? 'N/A' }})
            </a>
            <a>
                Populairste workshop: {{ $popularWorkshop->name ?? 'N/A' }} ({{ $popularWorkshop->bookings_count ?? 0 }} bookings)
            </a>
            <a>
                Totaal aantal studenten: {{ $totalStudents ?? '0' }}
            </a>
            <a>
                Totaal ingeschreven studenten: {{ $totalBookings ?? '0' }}
            </a>
            <a>
                Mogelijk dubbel student: {{ $totalGhosts ?? '0' }}
            </a>
            <a>
                Studenten zonder workshops: {{ $noWorkshops ?? '0' }}
            </a>
        </div>

        <div style="margin: 5vh"></div>

        @if($studentsWithoutWorkshops && count($studentsWithoutWorkshops) > 0)
            <div class="p-4 outline bg-deltion-orange-900 text-white rounded-xl">
                <h2 class="text-xl font-bold mb-4">Studenten zonder workshop inschrijvingen:</h2>
                <div class="space-y-2">
                    @foreach($studentsWithoutWorkshops as $student)
                        <div class="p-3 bg-deltion-orange-800 rounded flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $student->name }}</p>
                                <p class="text-sm">Klas: {{ $student->class }} | Email: {{ $student->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
