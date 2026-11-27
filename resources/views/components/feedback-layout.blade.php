@props(['title'])

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} - Let's Connect</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-dvh font-jakarta antialiased bg-gray-100 text-deltion-blue-900">
    <header class="flex items-center gap-3 px-4 py-3 bg-white border-b border-gray-200">
        <img src="{{ asset('/images/deltion.png') }}" alt="Deltion College" class="h-7 w-auto">
        <span class="h-6 border-l border-gray-300"></span>
        <span class="text-lg font-bold">Let's <span class="text-deltion-orange-900">Connect</span></span>
    </header>

    <main class="flex flex-col flex-1 w-full max-w-md mx-auto px-5 py-6">
        {{ $slot }}
    </main>
</body>
</html>
