@props(['title', 'rows'])

@php($total = array_sum($rows))

<section class="bg-white border border-gray-200 rounded-xl p-5">
    <h3 class="text-sm font-bold">{{ $title }}</h3>
    <dl class="mt-3 space-y-2 text-sm">
        @foreach ($rows as $label => $count)
            @php($percent = $total ? (int) round($count / $total * 100) : 0)
            <div class="flex items-center gap-3" title="{{ $label }}: {{ $count }} van {{ $total }} ({{ $percent }}%)">
                <dt class="w-16 shrink-0 text-gray-600">{{ $label }}</dt>
                <dd class="flex flex-1 items-center gap-3 min-w-0">
                    <span class="flex-1 h-2.5 bg-gray-100 rounded-r">
                        <span class="block h-2.5 bg-deltion-blue-900 rounded-r" style="width: {{ $percent }}%"></span>
                    </span>
                    <span class="w-[4.5rem] shrink-0 text-right whitespace-nowrap text-gray-700 tabular-nums">{{ $count }} ({{ $percent }}%)</span>
                </dd>
            </div>
        @endforeach
    </dl>
</section>
