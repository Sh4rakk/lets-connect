<x-feedback-layout :title="$round->workshop->name">
    <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
        <h1 class="text-xl font-bold">Bedankt voor je feedback!</h1>
        <p class="mt-2 text-sm text-gray-500 break-words">Je feedback voor {{ $round->workshop->name }} (ronde {{ $round->moment_id }}) is opgeslagen. Je kan dit scherm nu sluiten.</p>
    </div>
</x-feedback-layout>
