<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('selectionDashboard')">Terug naar admin dashboard</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">Klassen Overzicht</h1>
    </x-slot>

    {{-- Klassen en studenten komen uit de api, zie resources/js/admin/classesOverview.js --}}
    <div class="container mx-auto p-6 max-w-5xl" x-data="classesOverview">
        <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6 flex-row flex">
                <form @submit.prevent="loadClass()" class="space-y-4">
                    <div class="">
                        <div>
                            <label for="filterClass" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                            <select id="filterClass" x-model="selectedName" @change="loadClass()" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Kies een klas</option>

                                <template x-for="(groupClasses, groupName) in groups" :key="groupName">
                                    <optgroup :label="groupName">
                                        <template x-for="schoolClass in groupClasses" :key="schoolClass.id">
                                            <option :value="schoolClass.name" x-text="schoolClass.name" :selected="schoolClass.name === selectedName"></option>
                                        </template>
                                    </optgroup>
                                </template>

                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            Klas ophalen
                        </button>
                        <a href="{{ route('export-all-classes') }}" @click.prevent="startExport($el.href)" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition-colors" aria-label="Klas exporteren naar excel">
                            Alle klassen exporteren naar excel
                        </a>
                    </div>
                </form>
                <div class="ms-3 p-3 flex-1">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <h3 class="text-xs font-semibold text-gray-800">Klas export geschiedenis</h3>
                            <button onclick="location.reload()" class="px-2 py-0.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                Verversen
                            </button>
                        </div>
                    @if(count($exportFiles) > 0)
                    <div class="max-h-20 overflow-y-auto space-y-1">
                            @foreach($exportFiles as $file)
                                <div class="bg-gray-50 border border-gray-200 rounded p-2 flex items-center justify-between text-sm">
                                    <p class="text-xs text-gray-700 truncate">{{ basename($file) }}</p>
                                    <div class="flex gap-1">
                                        <a href="{{ route('download-export', basename($file)) }}" class="px-2 py-0.5 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700">
                                            Download
                                        </a>
                                        <form action="{{ route('delete-export', basename($file)) }}" method="POST" style="display:inline;" onsubmit="return confirm('Weet je zeker dat je dit bestand wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-0.5 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <template x-if="selectedClass">
            <div>
            <div class="overflow-hidden max-w-5xl m-auto mb-6 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-deltion-blue-50 text-deltion-blue-900 font-bold rounded-lg text-lg" x-text="selectedClass.name"></div>

                            <a :href="'{{ url('/export/classes') }}/' + encodeURIComponent(selectedClass.name)" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition-colors" aria-label="Klas exporteren naar excel">
                                Klas exporteren naar excel
                            </a>
                        </div>

                        <div class="text-sm text-gray-700">
                            <div x-show="selectedClass.LOB">
                                <span class="font-semibold text-deltion-blue-900">LOB'er(s)</span>: <span x-text="selectedClass.LOB"></span>
                            </div>
                            <div class="mt-2" x-show="!loadingClass">
                                <span x-show="isComplete" class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-green-100 text-green-800 font-semibold">Klas is compleet</span>
                                <span x-show="!isComplete" class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-red-100 text-red-800 font-semibold">Klas is niet compleet</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-center items-stretch gap-6">
                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal verwachte Studenten</span>
                            <span class="text-deltion-blue-900 font-bold text-center text-xl" x-text="selectedClass.total_amount"></span>
                        </div>

                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal Studenten Accounts Aangemaakt</span>
                            <span class="text-deltion-blue-900 font-bold text-center text-xl" x-text="loadingClass ? '...' : users.length"></span>
                        </div>

                        <div class="flex-1 border-2 border-deltion-blue-900 rounded-lg p-4 flex flex-col items-center">
                            <span class="text-deltion-blue-900 font-bold text-center mb-3">Aantal Ingeschreven Studenten</span>
                            <span class="text-deltion-blue-900 font-bold text-center text-xl" x-text="loadingClass ? '...' : registeredStudents"></span>
                        </div>
                    </div>
                </div>
            </div>

            <table class="min-w-full bg-deltion-blue-900 divide-y divide-gray-200 table-auto rounded-xl">
                <thead class="bg-blue-00">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Naam</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Klas</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
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
            </div>
        </template>

        <!-- Export Loading Modal -->
        <div id="exportModal" x-show="exporting" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-8 flex flex-col items-center gap-4">
                <!-- Loading Spinner -->
                <div class="w-12 h-12 border-4 border-gray-300 border-t-green-600 rounded-full animate-spin"></div>
                <p class="text-gray-800 font-semibold text-lg">Uw export word gegenereerd</p>
            </div>
        </div>
    </div>
</x-app-layout>
