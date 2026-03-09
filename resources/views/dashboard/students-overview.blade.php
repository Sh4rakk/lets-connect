<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')" wire:navigate>Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Studenten Overzicht</h1>
    </x-slot>

    <div class="container mx-auto p-6 max-w-5xl">
        <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <form method="GET" action="{{ route('students-overview') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="filterName" class="block text-sm font-bold text-deltion-blue-900 mb-2">Naam</label>
                            <input type="text" id="filterName" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Zoek op naam..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="filterEmail" class="block text-sm font-bold text-deltion-blue-900 mb-2">Email</label>
                            <input type="text" id="filterEmail" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="Zoek op email..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="filterClass" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                            <select id="filterClass" name="class" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">-- Alle klassen --</option>
                                <optgroup label="Sign">
                                    <option value="SIM2A" {{ ($filters['class'] ?? '') === 'SIM2A' ? 'selected' : '' }}>SIM2A</option>
                                    <option value="SIM2B" {{ ($filters['class'] ?? '') === 'SIM2B' ? 'selected' : '' }}>SIM2B</option>
                                    <option value="SIS3A" {{ ($filters['class'] ?? '') === 'SIS3A' ? 'selected' : '' }}>SIS3A</option>
                                    <option value="SIA3A" {{ ($filters['class'] ?? '') === 'SIA3A' ? 'selected' : '' }}>SIA3A</option>
                                </optgroup>
                                <optgroup label="Musicalperformer">
                                    <option value="AR2MP" {{ ($filters['class'] ?? '') === 'AR2MP' ? 'selected' : '' }}>AR2MP</option>
                                </optgroup>
                                <optgroup label="Podium-en Evenemententechniek">
                                    <option value="P2.1" {{ ($filters['class'] ?? '') === 'P2.1' ? 'selected' : '' }}>P2.1</option>
                                    <option value="P2.2" {{ ($filters['class'] ?? '') === 'P2.2' ? 'selected' : '' }}>P2.2</option>
                                    <option value="P2.3" {{ ($filters['class'] ?? '') === 'P2.3' ? 'selected' : '' }}>P2.3</option>
                                </optgroup>
                                <optgroup label="Acteur">
                                    <option value="2ARAC" {{ ($filters['class'] ?? '') === '2ARAC' ? 'selected' : '' }}>2ARAC</option>
                                </optgroup>
                                <optgroup label="Mode">
                                    <option value="2AFT" {{ ($filters['class'] ?? '') === '2AFT' ? 'selected' : '' }}>2AFT</option>
                                    <option value="2FC" {{ ($filters['class'] ?? '') === '2FC' ? 'selected' : '' }}>2FC</option>
                                    <option value="2FR" {{ ($filters['class'] ?? '') === '2FR' ? 'selected' : '' }}>2FR</option>
                                    <option value="2FDA" {{ ($filters['class'] ?? '') === '2FDA' ? 'selected' : '' }}>2FDA</option>
                                    <option value="2FDB" {{ ($filters['class'] ?? '') === '2FDB' ? 'selected' : '' }}>2FDB</option>
                                </optgroup>
                                <optgroup label="Mediavormgeving">
                                    <option value="MV2A" {{ ($filters['class'] ?? '') === 'MV2A' ? 'selected' : '' }}>MV2A</option>
                                    <option value="MV2B" {{ ($filters['class'] ?? '') === 'MV2B' ? 'selected' : '' }}>MV2B</option>
                                    <option value="MV2C" {{ ($filters['class'] ?? '') === 'MV2C' ? 'selected' : '' }}>MV2C</option>
                                </optgroup>
                                <optgroup label="Av-Specialist">
                                    <option value="AV3A" {{ ($filters['class'] ?? '') === 'AV3A' ? 'selected' : '' }}>AV3A</option>
                                </optgroup>
                                <optgroup label="Fotograaf">
                                    <option value="AV3B" {{ ($filters['class'] ?? '') === 'AV3B' ? 'selected' : '' }}>AV3B</option>
                                </optgroup>
                                <optgroup label="Expert IT systems and devices">
                                    <option value="IT2A" {{ ($filters['class'] ?? '') === 'IT2A' ? 'selected' : '' }}>IT2A</option>
                                    <option value="IT2B" {{ ($filters['class'] ?? '') === 'IT2B' ? 'selected' : '' }}>IT2B</option>
                                </optgroup>
                                <optgroup label="Allround medewerkers IT systems and devices">
                                    <option value="MI1A" {{ ($filters['class'] ?? '') === 'MI1A' ? 'selected' : '' }}>MI1A</option>
                                    <option value="MI2A" {{ ($filters['class'] ?? '') === 'MI2A' ? 'selected' : '' }}>MI2A</option>
                                </optgroup>
                                <optgroup label="Software Developer">
                                    <option value="SD2A" {{ ($filters['class'] ?? '') === 'SD2A' ? 'selected' : '' }}>SD2A</option>
                                    <option value="SD2B" {{ ($filters['class'] ?? '') === 'SD2B' ? 'selected' : '' }}>SD2B</option>
                                    <option value="SD2O" {{ ($filters['class'] ?? '') === 'SD2O' ? 'selected' : '' }}>SD2O</option>
                                </optgroup>
                                <optgroup label="Interieuradviseur">
                                    <option value="BOW2A" {{ ($filters['class'] ?? '') === 'BOW2A' ? 'selected' : '' }}>BOW2A</option>
                                    <option value="BOW2B" {{ ($filters['class'] ?? '') === 'BOW2B' ? 'selected' : '' }}>BOW2B</option>
                                    <option value="BOW2C" {{ ($filters['class'] ?? '') === 'BOW2C' ? 'selected' : '' }}>BOW2C</option>
                                    <option value="BOW2D" {{ ($filters['class'] ?? '') === 'BOW2D' ? 'selected' : '' }}>BOW2D</option>
                                    <option value="BOW2V" {{ ($filters['class'] ?? '') === 'BOW2V' ? 'selected' : '' }}>BOW2V</option>
                                </optgroup>
                                <optgroup label="Creative Development">
                                    <option value="CD2A" {{ ($filters['class'] ?? '') === 'CD2A' ? 'selected' : '' }}>CD2A</option>
                                    <option value="CD2B" {{ ($filters['class'] ?? '') === 'CD2B' ? 'selected' : '' }}>CD2B</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            Zoeken
                        </button>
                        <a href="{{ route('students-overview') }}" class="px-4 py-2 bg-gray-200 text-deltion-blue-900 rounded-md text-sm font-medium hover:bg-gray-300 transition-colors">
                            Filters wissen
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden max-w-5xl m-auto mt-6 mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            @if(count($users) > 0)
                <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto">
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

            @if(count($users) === 0)
                    <div class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Geen studenten gevonden.</div>
            @endif
        </div>
    </div>


</x-app-layout>


