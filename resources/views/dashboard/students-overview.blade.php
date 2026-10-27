<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Studenten Overzicht</h1>
    </x-slot>

    {{-- Zoekt via /api/users, zie resources/js/admin/studentsOverview.js --}}
    <div class="container mx-auto p-6 max-w-5xl" x-data="studentsOverview">
        <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <form @submit.prevent="search()" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="filterName" class="block text-sm font-bold text-deltion-blue-900 mb-2">Naam</label>
                            <input type="text" id="filterName" x-model="filters.name" @input.debounce.300ms="search()" placeholder="Zoek op naam..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="filterEmail" class="block text-sm font-bold text-deltion-blue-900 mb-2">Email</label>
                            <input type="text" id="filterEmail" x-model="filters.email" @input.debounce.300ms="search()" placeholder="Zoek op email..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="filterClass" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                            <select id="filterClass" x-model="filters.class" @change="search()" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">-- Alle klassen --</option>

                                <optgroup label="Media vormgeving">
                                    <option value="MV2A">MV2A</option>
                                    <option value="MV2B">MV2B</option>
                                    <option value="MV2C">MV2C</option>
                                </optgroup>

                                <optgroup label="Interieuradviseur">
                                    <option value="BOW3B">BOW3B</option>
                                    <option value="BOW3C">BOW3C</option>
                                    <option value="BOW3D">BOW3D</option>
                                    <option value="BOW2A">BOW2A</option>
                                    <option value="BOW2B">BOW2B</option>
                                    <option value="BOW2C">BOW2C</option>
                                    <option value="BOW2V">BOW2V</option>
                                </optgroup>

                                <optgroup label="Podium- en Evenemententechniek">
                                    <option value="P2.1">P2.1</option>
                                    <option value="P2.2">P2.2</option>
                                </optgroup>

                                <optgroup label="Muzikant">
                                    <option value="AR2MZ">AR2MZ</option>
                                </optgroup>

                                <optgroup label="Acteur">
                                    <option value="AR2AC">AR2AC</option>
                                </optgroup>

                                <optgroup label="Expert IT systems and devices">
                                    <option value="IT2A">IT2A</option>
                                </optgroup>

                                <optgroup label="Software developer">
                                    <option value="SD2A">SD2A</option>
                                    <option value="SD2B">SD2B</option>
                                </optgroup>

                                <optgroup label="AV-productie">
                                    <option value="AV3B">AV3B</option>
                                </optgroup>

                                <optgroup label="Fotograaf">
                                    <option value="AV3A">AV3A</option>
                                </optgroup>

                                <optgroup label="Mode">
                                    <option value="2FR">2FR</option>
                                    <option value="2FC">2FC</option>
                                    <option value="2AFT">2AFT</option>
                                    <option value="2FT">2FT</option>
                                    <option value="2FDA">2FDA</option>
                                    <option value="2FDB">2FDB</option>
                                </optgroup>

                                <optgroup label="Creative Development">
                                    <option value="CD2A">CD2A</option>
                                    <option value="CD2B">CD2B</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            Zoeken
                        </button>
                        <a href="{{ route('students-overview') }}" @click.prevent="clearFilters()" class="px-4 py-2 bg-gray-200 text-deltion-blue-900 rounded-md text-sm font-medium hover:bg-gray-300 transition-colors">
                            Filters wissen
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden max-w-5xl m-auto mt-6 mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <table x-show="users.length > 0" class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto">
                <thead class="bg-blue-00">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white" :class="{ 'opacity-50': loading }">
                <template x-for="user in users" :key="user.id">
                    <tr class="hover:bg-deltion-orange-100 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            <a :href="'{{ url('/edit-student') }}/' + user.id" class="block" x-text="user.name"></a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                            <a :href="'{{ url('/edit-student') }}/' + user.id" class="block" x-text="user.email"></a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                            <a :href="'{{ url('/edit-student') }}/' + user.id" class="block" x-text="user.class"></a>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>

            <div x-show="loading && users.length === 0" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Studenten laden...</div>
            <div x-show="failed" x-cloak class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-red-600">De studenten konden niet worden opgehaald.</div>
            <div x-show="!loading && !failed && users.length === 0" x-cloak class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">Geen studenten gevonden.</div>
        </div>
    </div>


</x-app-layout>


