@props(['title', 'backUrl' => null, 'backLabel' => null])

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Let's Connect</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-jakarta antialiased bg-gray-100 text-deltion-blue-900 min-h-screen">
    <header class="bg-white print:hidden">
        <div class="flex items-center justify-between px-4 sm:px-7 py-3 border-b border-gray-200">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('/images/deltion.png') }}" alt="Deltion College" class="h-7 w-auto">
                <span class="h-6 border-l border-gray-300"></span>
                <span class="text-lg font-bold">Let's <span class="text-deltion-orange-900">Connect</span></span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-deltion-blue-900">Log uit</button>
            </form>
        </div>
        <div class="flex flex-wrap sm:grid sm:grid-cols-[1fr_auto_1fr] items-center gap-3 px-4 sm:px-7 py-2 border-b border-gray-200">
            <div>
                @if ($backUrl)
                    <a href="{{ $backUrl }}" class="inline-block px-3 py-2 text-sm font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50">&larr; {{ $backLabel }}</a>
                @endif
            </div>
            <h1 class="text-lg font-bold text-center break-words">{{ $title }}</h1>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-7">
        {{ $slot }}
    </main>
</body>
</html>
