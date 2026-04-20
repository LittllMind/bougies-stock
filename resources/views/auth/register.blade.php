<x-guest-layout>
    <x-slot name="header">
        Inscription
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom')" class="text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full bg-white border-amber-200 text-gray-800 focus:border-amber-500 focus:ring focus:ring-amber-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full bg-white border-amber-200 text-gray-800 focus:border-amber-500 focus:ring focus:ring-amber-200" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" class="text-gray-700" />

            <x-text-input id="password" class="block mt-1 w-full bg-white border-amber-200 text-gray-800 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-gray-700" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-white border-amber-200 text-gray-800 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-amber-600 hover:text-amber-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>

            <button type="submit" class="ms-4 px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200">
                {{ __("S'inscrire") }}
            </button>
        </div>
    </form>
</x-guest-layout>
