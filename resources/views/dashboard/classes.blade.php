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
                                <option value="">-- Alle klassen --</option>

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
                <div class="p-6 flex justify-center">
                    <div class="border-2 border-deltion-blue-900 rounded-lg p-2 w-30 ">
                        <span class="text-deltion-blue-900 font-bold text-center w-full">Aantal Studenten</span>
                    </div>
                </div>
            </div>
       @endif

{{--        <div class="overflow-hidden max-w-5xl m-auto mt-6 mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">--}}
{{--            @if(count($users) > 0)--}}
{{--                <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto">--}}
{{--                    <thead class="bg-blue-00">--}}
{{--                    <tr>--}}
{{--                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>--}}
{{--                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>--}}
{{--                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}

{{--                    <tbody class="divide-y divide-gray-200 bg-white">--}}
{{--                    @foreach ($users as $user)--}}
{{--                        <tr class="hover:bg-deltion-orange-100 transition-colors">--}}
{{--                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">--}}
{{--                                <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->name }}</a>--}}
{{--                            </td>--}}
{{--                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">--}}
{{--                                <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->email }}</a>--}}
{{--                            </td>--}}
{{--                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">--}}
{{--                                <a href="{{ route('edit-student', $user->id) }}" class="block">{{ $user->class }}</a>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            @endif--}}

{{--            @if(count($users) === 0)--}}
{{--                    <div class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Geen studenten gevonden.</div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}


</x-app-layout>


