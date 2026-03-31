    <x-guest-layout>
    <link href="{{ asset('/css/form.css') }}" rel="stylesheet">

    @if (session('error'))
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; border-radius: 12px; padding: 40px; max-width: 500px; text-align: center; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
                <div style="font-size: 48px; margin-bottom: 20px;">🔒</div>
                <h2 style="font-size: 28px; font-weight: bold; color: #1f2937; margin-bottom: 16px;">Sorry, registreren is momenteel gesloten!</h2>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 24px; line-height: 1.6;">Registraties zijn tijdelijk gesloten</p>
                <a href="{{ route('login') }}" style="display: inline-block; background: #3b82f6; color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.3s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">Go to Login</a>
            </div>
        </div>
    @else
    <!-- Logo -->
    <div class="logo">
        <img src="{{ asset('/images/deltion.png') }}" alt="Deltion Logo" class="deltion-logo">
    </div>

    <!-- Registratieformulier -->
    <div class="register-container" style="max-width: 400px; margin: 0 auto; padding: 20px;">
        <form method="POST" action="{{ route('register') }}" class="register-form">
            @csrf

            <div class="logo-container" style="display: flex; align-items: center; justify-content: center; margin-bottom: 5px; text-align: center;">
                <div class="logo">
                    <img src="{{ asset('/images/Lets-connect-logo.png') }}" alt="Logo" class="logo-image" style="max-width: 250px; height: auto; margin-bottom: 0;">
                </div>
                <div class="logo-text" style="margin-left: 10px; font-size: 20px; font-weight: bold;">
                </div>
            </div>

            <div class="forms">
                <!-- Naam invoeren -->
                <div class="form-group" style="margin-bottom: 10px;">
                    <x-input-label for="name" :value="__('Naam')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Voer je volledige naam in" required autofocus autocomplete="name" style="width: 100%; max-width: 550px;" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- E-mailadres invoeren -->
                <div class="form-group" style="margin-bottom: 10px;">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="Voer je studentenemail in" required autocomplete="username" style="width: 100%; max-width: 550px;" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Opleiding en Klas -->
                <label for="opleiding" style="margin-bottom: 5px;">Opleiding:</label>
                <div class="form-group-row" style="margin-bottom: 10px; display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <select id="opleiding" name="opleiding" style="width: 100%; max-width: 280px;">
                            <option value="">Kies een opleiding</option>
                            <option value="opleiding1">Sign</option>
                            <option value="opleiding2">Musicalperformer</option>
                            <option value="opleiding3">Podium-en Evenemententechniek</option>
                            <option value="opleiding4">Acteur</option>
                            <option value="opleiding5">Mode</option>
                            <option value="opleiding6">Mediavormgeving</option>
                            <option value="opleiding7">Av-Specialist</option>
                            <option value="opleiding8">Fotograaf</option>
                            <option value="opleiding9">Expert IT systems and devices</option>
                            <option value="opleiding10">Allround medewerkers IT systems and devices</option>
                            <option value="opleiding11">Software Developer</option>
                            <option value="opleiding12">Interieuradviseur</option>
                            <option value="opleiding13">Creative Development</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <select id="klas" name="klas">
                            <option value="">Kies een klas</option>
                        </select>
                        <x-input-error :messages="$errors->get('klas')" class="mt-2" />
                    </div>
                </div>

                <!-- Registreren Button -->
                <div class="form-actions flex items-center justify-end mt-2">
                    <button type="submit" class="w-80 m-auto items-center px-4 py-2 bg-deltion-orange-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-deltion-blue-900 focus:bg-deltion-orange-900 active:bg-deltion-orange-900 focus:outline-none focus:ring-2 focus:ring-deltion-blue-900 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Registreren') }}
                    </button>
                </div>
                <div class="flex items-center mt-3 justify-center">
                <span class="text-gray-600">
                    {{ __('Heb je al een account?') }}
                </span>
                    <span class="text-black ms-1 me-1">
                    |
                </span>
                    <a class="text-black hover:text-deltion-orange-900 font-bold transition-all rounded-md" href="{{ route('login') }}">
                        {{ __('Inloggen') }}
                    </a>
                </div>
            </div>
        </form>

        <div id="dataPopup" style="display: none">
            <div class="dataWrapperPopup">
                <div class="innerDataTitel">
                    Weet je zeker dat deze gegevens kloppen?
                </div>
                <div class="innerDataWrapper">
                    <div id="userData"></div>
                </div>
                <div class="buttonWrapper">
                    <x-secondary-button class="popupButtonNo">
                        Nee
                    </x-secondary-button>
                    <x-primary-button class="popupButtonYes">
                        Ja
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/register.js') }}"></script>
    <script src="{{ asset('/js/registerConfirm.js') }}"></script>
    @endif
</x-guest-layout>
