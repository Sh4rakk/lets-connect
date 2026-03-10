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

        </div>
    </div>

</x-app-layout>
