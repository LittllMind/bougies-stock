<x-guest-layout>
    <x-slot name="header">
        Mot de passe oublié
    </x-slot>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Vous avez oublié votre mot de passe ? Pas de souci. Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de réinitialisation.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-amber-600" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full bg-white border-amber-200 text-gray-800 focus:border-amber-500 focus:ring focus:ring-amber-200" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200">
                {{ __('Envoyer le lien') }}
            </button>
        </div>
    </form>
</x-guest-layout>
