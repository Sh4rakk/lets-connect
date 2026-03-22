<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <div class="logo">
        <img src="https://xerte.deltion.nl/USER-FILES/3183-cmartens-site/media/Deltion_College_CMYK_145x57.png" alt="Deltion Logo" class="deltion-logo">
    </div>

    <div class="full-page-background"></div>

    @php
        $statusMessage = session('status') ?: 'We hebben een code naar je e-mailadres gestuurd.';
        $statusClass = session('status') ? 'alert-success' : 'alert-info';
        $prefillEmail = old('email', $email ?? request('email'));
    @endphp

    <div class="login-container">
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

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ms-3">
                    {{ __('Verify') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('auth.login-code.request') }}" class="login-form" style="margin-top: 1rem;">
            @csrf
            <input type="hidden" name="email" value="{{ $prefillEmail }}" />
            <x-secondary-button type="submit">
                {{ __('Resend code') }}
            </x-secondary-button>
        </form>
    </div>
</x-guest-layout>
