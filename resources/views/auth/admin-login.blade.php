<x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    <!-- Logo -->
    <div class="flex flex-row gap-5 justify-center mb-6" style="margin-right: 5px;">
        <img src="{{ asset('/images/deltion.png') }}" alt="Logo" class="hover-scale m-auto w-auto h-16" >
    </div>

    <div class="login-container m-auto !h-auto">
        <img src="{{ asset('/images/Lets-connect-logo.png') }}" alt="Logo" class="hover-scale m-auto w-auto h-20 md:h-36">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('admin.login.store') }}" class="register-form">
            @csrf

            <div class="forms">
                <!-- Email Address -->
                <div class="form-group">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus readonly />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center mt-3 justify-center">
                <span class="text-gray-600">
                    {{ __('Ben je verdwaald?') }}
                </span>
                    <span class="text-black ms-1 me-1">
                    |
                </span>
                    <a class="text-black hover:text-deltion-orange-900 font-bold transition-all rounded-md" href="{{ route('login') }}">
                        {{ __('Terug naar Login') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>

