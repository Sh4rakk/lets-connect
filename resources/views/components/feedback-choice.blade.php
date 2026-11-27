@props(['round' => false, 'checked' => false])

<label class="cursor-pointer">
    <input type="radio" required @checked($checked) {{ $attributes->merge(['class' => 'peer sr-only']) }}>
    <span @class([
        'flex items-center justify-center text-sm font-semibold bg-white border border-gray-300 rounded-full peer-checked:bg-deltion-orange-900 peer-checked:border-deltion-orange-900 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-deltion-blue-900',
        'h-11 w-11' => $round,
        'px-4 py-2' => ! $round,
    ])>{{ $slot }}</span>
</label>
