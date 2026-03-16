<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <!-- Formulier Container -->
    <div class="login-container">
        
        <!-- Logo en "Let's Connect" tekst bovenaan in de form -->
        <div class="logo-container" style="display: flex; align-items: center; justify-content: center; ">
            <!-- Logo -->
            <div class="logo" style="margin-right: 5px;">
                <img src="{{ asset('/images/Letsconnect2.0.jpeg') }}" alt="Logo" class="logo-image" style="width: 250px;">
            </div>

            <!-- "Let's Connect" tekst -->
            <div class="logo-text" style="font-size: 20px; display: flex; align-items: center;">
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-3" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            <!-- Email -->
            <div class="mt-2">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('username')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            {{-- <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Onthoud Mij') }}</span>
                </label>
            </div> --}}

            <div class="flex items-center justify-end mt-3">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Nog geen account?') }}
                </a>

                <x-primary-button class="ms-3">
                    {{ __('Log In') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
