<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Let's Connect</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-gray-100 font-sans text-gray-900 antialiased">

    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex h-14 max-w-5xl items-center px-4 sm:px-6">
            <a href="{{ url('/') }}" class="text-lg font-bold text-deltion-blue-900">Let's <span class="text-deltion-orange-900">Connect</span></a>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-4xl overflow-hidden rounded-xl border border-gray-200 bg-white md:grid-cols-[1fr_1.1fr]">

            <div class="flex flex-col justify-center p-8 sm:p-10">
                <p class="text-6xl font-extrabold leading-none tracking-tight text-deltion-orange-900">@yield('code')</p>
                <h1 class="mt-5 text-2xl font-bold text-deltion-blue-900">@yield('title')</h1>
                <p class="mt-3 text-gray-600">@yield('message')</p>

                @hasSection('tips')
                    <ul class="mt-5 space-y-2 border-l-2 border-deltion-orange-200 pl-4 text-sm text-gray-600">
                        @yield('tips')
                    </ul>
                @endif

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}" class="rounded-md bg-deltion-blue-900 px-4 py-2 text-sm font-semibold text-white hover:bg-deltion-blue-950">Naar mijn workshops</a>
                    <button type="button" onclick="history.length > 1 ? history.back() : location.assign('{{ url('/') }}')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-deltion-blue-900 hover:bg-gray-50">Vorige pagina</button>
                </div>
            </div>

            {{-- de puzzel uit de Let's Connect huisstijl, de tekst onderaan valt weg door de crop --}}
            <div class="order-first flex items-end justify-center bg-deltion-orange-50 px-6 pt-8 md:order-none md:px-8 md:pt-12">
                <div class="aspect-[910/560] w-full max-w-sm overflow-hidden md:max-w-none">
                    <img src="{{ asset('images/Lets-connect-logo.png') }}" alt="" class="w-full mix-blend-multiply">
                </div>
            </div>
        </div>
    </main>

</body>
</html>
