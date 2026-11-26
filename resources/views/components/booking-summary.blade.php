@props(['bookings'])

{{-- De gekozen workshops van een student, per ronde met een genummerd balletje (zelfde als op het dashboard) --}}
<ol {{ $attributes->merge(['class' => 'divide-y divide-gray-100 rounded-lg border border-gray-200 text-left']) }}>
    @foreach ($bookings->sortBy(fn ($booking) => $booking->workshopMoments->moment_id) as $booking)
        @php($wm = $booking->workshopMoments)
        <li class="flex items-center gap-4 px-4 py-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-deltion-blue-900 text-sm font-bold text-white">{{ $loop->iteration }}</span>
            <div class="min-w-0">
                <p class="truncate font-semibold text-deltion-blue-900">{{ $wm->workshop->name }}</p>
                <p class="text-sm text-gray-500">
                    {{ $wm->moment->time }}@if ($wm->workshop->location) · {{ $wm->workshop->location }}@endif
                </p>
            </div>
        </li>
    @endforeach
</ol>
