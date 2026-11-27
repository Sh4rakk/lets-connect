<x-workshop-layout title="Feedback testen" :back-url="route('leader.dashboard')" back-label="Workshopleider dashboard">
    <p class="mb-4 text-sm text-gray-600">Kies een ronde om het feedbackformulier te openen. Test feedback komt echt in de database en telt mee in de resultaten.</p>

    <div class="relative overflow-x-auto bg-white border border-gray-200 rounded-xl">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-600 border-b border-gray-200">
                    <th class="px-4 py-3 font-medium">Workshop</th>
                    <th class="px-4 py-3 font-medium">Ronde</th>
                    <th class="px-4 py-3 font-medium">Feedback</th>
                    <th class="px-4 py-3"><span class="sr-only">Acties</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($workshops as $workshop)
                    @foreach ($workshop->workshopMoments as $wm)
                        <tr>
                            <td class="px-4 py-2 font-semibold">{{ $workshop->name }}</td>
                            <td class="px-4 py-2 text-gray-500 whitespace-nowrap">Ronde {{ $wm->moment_id }} ({{ $wm->moment?->time }})</td>
                            <td class="px-4 py-2">{{ $wm->feedback_count }}</td>
                            <td class="px-4 py-2">
                                <div class="flex justify-end gap-2 whitespace-nowrap text-xs font-medium">
                                    <a href="{{ route('leader.feedback', $wm) }}" class="px-3 py-1.5 bg-white border border-gray-300 rounded hover:bg-gray-50">Resultaten</a>
                                    <form method="POST" action="{{ route('feedback.test.start', $wm) }}" target="_blank">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-white bg-deltion-blue-900 border border-deltion-blue-900 rounded hover:bg-deltion-blue-800">Feedback geven</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                @if ($workshops->flatMap->workshopMoments->isEmpty())
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Er zijn nog geen workshops.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</x-workshop-layout>
