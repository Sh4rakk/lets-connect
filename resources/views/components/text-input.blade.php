@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-deltion-blue-900 focus:border-deltion-orange-900 focus:ring-deltion-orange-900 rounded-md shadow-sm']) }}>
