<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <div class="flex flex-row gap-5 justify-center mb-6" style="margin-right: 5px;">
        <img src="{{ asset('/images/deltion.png') }}" alt="Logo" class="hover-scale m-auto w-auto h-16" >
    </div>

    @php
        $statusMessage = session('status') ?: 'We hebben een code naar je e-mailadres gestuurd.';
        $statusClass = session('status') ? 'alert-success' : 'alert-info';
        $prefillEmail = old('email', $email ?? request('email'));
    @endphp

    <div class="login-container m-auto !h-auto">
        <img src="{{ asset('/images/Lets-connect-logo.png') }}" alt="Logo" class="hover-scale m-auto mb-6 w-auto h-20 md:h-36">
        <div class="alert {{ $statusClass }}">
            {{ $statusMessage }}
        </div>

        <p> Voer deze code in om in te loggen. </p>

        <form method="POST" action="{{ route('auth.login-code.verify') }}" class="login-form">
            @csrf

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$prefillEmail" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="code" :value="__('Code')" />
                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required inputmode="numeric" autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="flex items-center mt-3">
                <button type="submit" class="w-full max-w-xs m-auto items-center px-4 py-2 bg-deltion-orange-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-deltion-blue-900 focus:bg-deltion-orange-900 active:bg-deltion-orange-900 focus:outline-none focus:ring-2 focus:ring-deltion-blue-900 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Verifiëren') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('auth.login-code.request') }}" class="login-form" style="margin-top: -1rem;">
            @csrf
            <input type="hidden" name="email" value="{{ $prefillEmail }}" />

            <div class="flex items-center mt-8 justify-center">
                <span class="text-gray-600">
                    {{ __('Geen code ontvangen?') }}
                </span>
                <span class="text-black ms-1 me-1">
                    |
                </span>
                <x-secondary-button type="submit">
                    {{ __('Code opnieuw verzenden') }}
                </x-secondary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
