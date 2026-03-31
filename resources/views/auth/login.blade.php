<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">
    <div class="flex flex-row gap-5 justify-center mb-6" style="margin-right: 5px;">
        <img src="{{ asset('/images/deltion.png') }}" alt="Logo" class="hover-scale m-auto w-auto h-16" >
    </div>
    <!-- Formulier Container -->
    <div class="login-container !h-auto">
        <img src="{{ asset('/images/Lets-connect-logo.png') }}" alt="Logo" class="hover-scale m-auto w-auto h-36">
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

            <div class="flex items-center mt-3">
                <button type="submit" class="w-80 m-auto items-center px-4 py-2 bg-deltion-orange-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-deltion-blue-900 focus:bg-deltion-orange-900 active:bg-deltion-orange-900 focus:outline-none focus:ring-2 focus:ring-deltion-blue-900 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{__('Log In')}}
                </button>
            </div>
            <div class="flex items-center mt-3 justify-center">
                <span class="text-gray-600">
                    {{ __('Heb je nog geen account?') }}
                </span>
                <span class="text-black ms-1 me-1">
                    |
                </span>
                <a class="text-black hover:text-deltion-orange-900 font-bold transition-all rounded-md" href="{{ route('register') }}">
                    {{ __('Account aanmaken') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
