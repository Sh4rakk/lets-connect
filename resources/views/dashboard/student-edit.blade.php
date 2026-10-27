<x-app-layout>

    <x-slot name="leftHeader">
        <x-secondary-anchor :href="route('students-overview')">Terug naar studenten overzicht</x-secondary-anchor>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center text-gray-800">{{ $user->name }} Bewerken</h1>
    </x-slot>

    {{-- Opslaan gaat via PATCH /api/users/{id}/bookings, zie resources/js/admin/studentEdit.js --}}
    <div class="container mx-auto p-6 max-w-5xl"
         x-data="studentEdit({ userId: @js($user->id), bookingsByRound: @js($bookingsByRound), optionsByRound: @js($workshopOptionsByRound) })">
        <template x-if="message">
            <div class="mb-4 p-4 rounded-lg" :class="message.color === 'green' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="message.text"></div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-lg font-semibold text-deltion-blue-900 mb-4">Studentgegevens</h2>

                <div class="space-y-4">
                    <div>
                        <label for="student-name" class="block text-sm font-bold text-deltion-blue-900 mb-2">Naam</label>
                        <input id="student-name" type="text" value="{{ $user->name }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>

                    <div>
                        <label for="student-email" class="block text-sm font-bold text-deltion-blue-900 mb-2">Email</label>
                        <input id="student-email" type="text" value="{{ $user->email }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>

                    <div>
                        <label for="student-class" class="block text-sm font-bold text-deltion-blue-900 mb-2">Klas</label>
                        <input id="student-class" type="text" value="{{ $user->class }}" readonly class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900" />
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm p-6">
                <h2 class="text-lg font-semibold text-deltion-blue-900 mb-4">Inschrijvingen per ronde</h2>

                <form @submit.prevent="save()" class="space-y-4">
                    <template x-for="round in [1, 2, 3]" :key="round">
                        <div class="rounded-lg border p-4" :class="bookingsByRound[round] ? 'border-deltion-blue-200 bg-deltion-blue-50' : 'border-gray-300 bg-gray-100'">
                            <label :for="'round-' + round" class="block text-sm font-semibold" :class="bookingsByRound[round] ? 'text-deltion-blue-900' : 'text-gray-700'" x-text="'Ronde ' + round"></label>

                            <p x-show="bookingsByRound[round]" class="text-xs text-gray-700 mt-1" x-text="bookingsByRound[round] ? `Huidig: ${bookingsByRound[round].workshop} (${bookingsByRound[round].time})` : ''"></p>
                            <p x-show="!bookingsByRound[round]" class="text-xs text-gray-500 mt-1">Huidig: Niet ingeschreven</p>

                            <select
                                :id="'round-' + round"
                                x-model="rounds[round]"
                                class="mt-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Niet ingeschreven</option>
                                <template x-for="option in optionsByRound[round]" :key="option.id">
                                    <option :value="option.id" x-text="option.name" :selected="String(option.id) === String(rounds[round])"></option>
                                </template>
                            </select>

                            <p x-show="errors[round]" x-cloak class="text-sm text-red-600 mt-1" x-text="errors[round]"></p>
                        </div>
                    </template>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="saving" class="px-4 py-2 bg-deltion-blue-900 text-white rounded-md text-sm font-medium hover:bg-blue-800 transition-colors">
                            <span x-text="saving ? 'Opslaan...' : 'Inschrijvingen opslaan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

