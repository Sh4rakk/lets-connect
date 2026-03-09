<x-app-layout>
    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('workshop-dashboard')" wire:navigate>Terug naar overzicht</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">{{$wsm->workshop->name}} {{$wsm->moment->time}}</h1>
    </x-slot>

    <div class="overflow-hidden max-w-5xl m-auto mt-6 mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
        @if(count($bookings) > 0)
        <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto">
            <thead class="bg-blue-00">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($bookings as $booking)
                    <tr onclick="window.location.href='{{ route('edit-student', $booking->student->id) }}'" class="cursor-pointer hover:bg-deltion-orange-100 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $booking->student->name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $booking->student->email }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $booking->student->class }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(count($bookings) === 0)
            <div class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Geen inschrijvingen gevonden @if(!empty($class))voor klas {{$class}}.@endif</div>
        @endif
    </div>
</x-app-layout>

