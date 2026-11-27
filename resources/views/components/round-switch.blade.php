@props(['locked' => false])

<span class="relative inline-flex shrink-0">
    <input type="checkbox" @disabled($locked) {{ $attributes->merge(['class' => 'peer sr-only']) }}>
    <span class="block h-5 w-9 rounded-full bg-gray-300 transition-colors peer-checked:bg-green-700 peer-disabled:opacity-50 peer-focus-visible:ring-2 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-deltion-blue-900"></span>
    <span class="pointer-events-none absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4"></span>
</span>
