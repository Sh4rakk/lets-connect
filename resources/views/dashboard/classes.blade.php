<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')" wire:navigate>Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Klassen Overzicht</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">
        <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <form method="GET" action="{{ route('class-dashboard') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="filterClass" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                            <select id="filterClass" name="class" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Kies een klas</option>

                                @foreach($classes as $groupName => $groupClasses)
                                    <optgroup label="{{ $groupName }}">
                                        @foreach($groupClasses as $class)
                                            <option value="{{$class->name}}" {{ $selectedClass === $class->name ? 'selected' : '' }}>{{$class->name}}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            Klas ophalen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(request('class') !== null)
            <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-deltion-blue-50 text-deltion-blue-900 font-bold rounded-lg text-lg">
                                {{ request('class') }}
                            </div>

                            <a href="{{ route('class-dashboard', array_merge(request()->query(), ['export' => 'excel'])) }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition-colors" aria-label="Klas exporteren naar excel">
                                Klas exporteren naar excel
                            </a>
                        </div>

                        <div class="text-sm text-gray-700">
                            <div>
                                @if(isset($selectedClass->LOB))
                                    <span class="font-semibold text-deltion-blue-900">LOB'er(s)</span>: {{$selectedClass->LOB}}
                                @endif
                            </div>
                            @if(isset($selectedClass->total_amount))
                                <div class="mt-2">
                                    @if(isset($selectedClass) && isset($registeredStudents) && $selectedClass->total_amount == $registeredStudents)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-green-100 text-green-800 font-semibold">Klas is compleet</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-red-100 text-red-800 font-semibold">Klas is niet compleet</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-center items-stretch gap-6">
                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal verwachte Studenten</span>
                            @if(isset($selectedClass->total_amount))
                            <span class="text-deltion-blue-900 font-bold text-center text-xl">{{$selectedClass->total_amount}}</span>
                            @endif
                        </div>

                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal Studenten Accounts Aangemaakt</span>
                            <span class="text-deltion-blue-900 font-bold text-center text-xl">{{count($users)}}</span>
                        </div>

                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal Ingeschreven Studenten</span>
                            <span class="text-deltion-blue-900 font-bold text-center text-xl">{{$registeredStudents}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto rounded-xl">
            <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto rounded-xl">
                <thead class="bg-blue-00">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($users as $user)
                    <tr class="hover:bg-deltion-orange-100 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->name }}</a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                            <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->email }}</a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                            <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->class }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
       @endif
</x-app-layout>


