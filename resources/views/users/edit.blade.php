<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Modifier l\'utilisateur') }} - {{ $user->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left mr-1"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Nom -->
                            <div>
                                <x-input-label for="name" :value="__('Nom')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            
                            <!-- Téléphone -->
                            <div>
                                <x-input-label for="phone" :value="__('Téléphone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $user->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            
                            <!-- Rôle -->
                            <div>
                                <x-input-label for="role" :value="__('Rôle')" />
                                <select id="role" name="role" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="vendeur" {{ (old('role', $user->role) == 'vendeur') ? 'selected' : '' }}>{{ __('Vendeur') }}</option>
                                    <option value="manager" {{ (old('role', $user->role) == 'manager') ? 'selected' : '' }}>{{ __('Manager') }}</option>
                                    @if(auth()->user()->isAdmin())
                                    <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>{{ __('Administrateur') }}</option>
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                            
                            <!-- Commission Rate (pour les vendeurs) -->
                            <div id="commission-container" style="{{ $user->role != 'vendeur' ? 'display: none;' : '' }}">
                                <x-input-label for="commission_rate" :value="__('Taux de commission (%)')" />
                                <x-text-input id="commission_rate" class="block mt-1 w-full" type="number" name="commission_rate" :value="old('commission_rate', $user->commission_rate)" min="0" max="100" step="0.01" />
                                <x-input-error :messages="$errors->get('commission_rate')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Applicable uniquement pour les vendeurs. Valeur par défaut appliquée aux nouvelles boutiques.') }}
                                </p>
                            </div>
                            
                            <!-- Mot de passe - optionnel lors de l'édition -->
                            <div>
                                <x-input-label for="password" :value="__('Nouveau mot de passe (facultatif)')" />
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Laissez vide pour conserver le mot de passe actuel.') }}
                                </p>
                            </div>
                            
                            <!-- Confirmation Mot de passe -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmer le nouveau mot de passe')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                            
                            <!-- Actif -->
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ $user->is_active ? 'checked' : '' }} 
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">{{ __('Compte actif') }}</label>
                            </div>
                            
                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button class="ml-3">
                                    {{ __('Mettre à jour l\'utilisateur') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const commissionContainer = document.getElementById('commission-container');
            
            function toggleCommissionField() {
                commissionContainer.style.display = roleSelect.value === 'vendeur' ? 'block' : 'none';
            }
            
            roleSelect.addEventListener('change', toggleCommissionField);
        });
    </script>
    @endpush
</x-app-layout> 