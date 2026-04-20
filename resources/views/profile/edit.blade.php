<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-xl font-bold text-white">
            👤 Mon Profil
        </h2>
    </x-slot>

    <div class="flex min-h-screen bg-amber-50">
        
        <!-- Sidebar Navigation Client -->
        @php
            // Pass pendingOrders count to sidebar
            $pendingOrders = \App\Models\Order::where('user_id', Auth::id())
                ->where('statut', 'pending')
                ->count();
        @endphp
        @include('client.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <!-- Informations Profil -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border-2 border-amber-100">
                    <div class="max-w-xl">
                        <header class="mb-6">
                            <h3 class="text-lg font-medium text-amber-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informations du Profil
                            </h3>
                            
                            <p class="mt-1 text-sm text-gray-600">
                                Mettez à jour vos informations de compte.
                            </p>
                        </header>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Nom')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button class="bg-amber-500 hover:bg-amber-600 focus:bg-amber-600">
                                    Sauvegarder
                                </x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600"
                                    >
                                        ✅ Sauvegardé avec succès !
                                    </p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border-2 border-amber-100">
                    <div class="max-w-xl">
                        <header class="mb-6">
                            <h3 class="text-lg font-medium text-amber-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                                Changer le Mot de Passe
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Assurez-vous d'utiliser un mot de passe sécurisé.
                            </p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div>
                                <x-input-label for="current_password" :value="__('Mot de passe actuel')" />
                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500" autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Nouveau mot de passe')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button class="bg-amber-500 hover:bg-amber-600 focus:bg-amber-600">
                                    Mettre à jour
                                </x-primary-button>

                                @if (session('status') === 'password-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600"
                                    >
                                        ✅ Mot de passe mis à jour !
                                    </p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Suppression compte -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border-2 border-red-100">
                    <div class="max-w-xl">
                        <header class="mb-6">
                            <h3 class="text-lg font-medium text-red-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                Supprimer le Compte
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Une fois votre compte supprimé, toutes vos données seront effacées.
                            </p>
                        </header>

                        <x-danger-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                            class="bg-red-600 hover:bg-red-700"
                        >
                            Supprimer mon compte
                        </x-danger-button>

                        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900">
                                    Êtes-vous sûr de vouloir supprimer votre compte ?
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    Cette action est irréversible. Entrez votre mot de passe pour confirmer.
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="password" value="Mot de passe" class="sr-only" />
                                    <x-text-input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="mt-1 block w-3/4 bg-amber-50 border-amber-200"
                                        placeholder="Votre mot de passe"
                                    />
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        Annuler
                                    </x-secondary-button>

                                    <x-danger-button class="ml-3 bg-red-600 hover:bg-red-700">
                                        Oui, supprimer
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
