<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <!-- Logo -->
    <div class="logo">
        <img src="https://xerte.deltion.nl/USER-FILES/3183-cmartens-site/media/Deltion_College_CMYK_145x57.png" alt="Deltion Logo" class="deltion-logo">
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="full-page-background"></div>

    <div class="login-container">
        <form id="loginForm" method="POST" action="{{ route('auth.login-code.request') }}" class="login-form">
            @csrf

            <!-- Email -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Nog geen account?') }}
                </a>

                <x-primary-button class="ms-3">
                    {{ __('Verder') }}
                </x-primary-button>
            </div>

            <div class="mt-4 text-sm">
                <a class="underline text-gray-600 hover:text-gray-900"
                   href="{{ route('auth.verify-otp', ['email' => old('email')]) }}">
                    {{ __('Heb je al een code? Verifieer hier') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
