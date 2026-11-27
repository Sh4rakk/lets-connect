<x-workshop-layout :title="$isAdmin ? 'Workshopleider dashboard' : 'Mijn workshops'"
                   :back-url="$isAdmin ? route('selectionDashboard') : null" back-label="Admin dashboard">
    @if ($isAdmin && app()->environment('local'))
        <p class="mb-4 text-right">
            <a href="{{ route('feedback.test') }}" class="inline-block px-3 py-2 text-sm font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50">Feedback testen</a>
        </p>
    @endif

    @forelse ($workshops as $workshop)
        <section class="mb-5 bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-b border-gray-200">
                @if ($workshop->image_url)
                    <img src="{{ $workshop->image_url }}" alt="" loading="lazy" class="h-9 w-14 shrink-0 rounded-md object-cover bg-gray-200">
                @else
                    <span class="h-9 w-14 shrink-0 rounded-md bg-gray-200 border border-dashed border-gray-300"></span>
                @endif
                <div>
                    <h2 class="font-bold">{{ $workshop->name }}</h2>
                    <p class="text-xs text-gray-500">
                        {{ $workshop->location ?: 'Lokaal onbekend' }}
                        @if ($isAdmin)
                            | {{ $workshop->leader?->email ?? 'Geen workshopleider' }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 text-xs text-gray-600 border-b border-gray-200">
                            <th class="px-4 py-2 font-medium">Ronde</th>
                            <th class="px-4 py-2 font-medium">Tijd</th>
                            <th class="px-4 py-2 font-medium">Verwacht</th>
                            <th class="px-4 py-2"><span class="sr-only">Acties</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($workshop->workshopMoments as $wm)
                            <tr>
                                <td class="px-4 py-2 font-semibold whitespace-nowrap">Ronde {{ $wm->moment_id }}</td>
                                <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $wm->moment?->time }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $wm->bookings_count }} / {{ $wm->maxSpots() }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-2 whitespace-nowrap text-xs font-medium">
                                        <a href="{{ route('workshop-moment.qr', $wm) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded hover:bg-gray-50">QR-code</a>
                                        <a href="{{ route('leader.feedback', $wm) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded hover:bg-gray-50">Feedback ({{ $wm->feedback_count }})</a>
                                        <a href="{{ route('leader.expected', $wm) }}" class="px-3 py-1.5 text-white bg-deltion-blue-900 border border-deltion-blue-900 rounded hover:bg-deltion-blue-800">Verwachting</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">Deze workshop staat in geen enkele ronde.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @empty
        <p class="p-6 text-center bg-white border border-gray-200 rounded-xl text-gray-500">Er zijn nog geen workshops.</p>
    @endforelse
</x-workshop-layout>
