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
                            <div id="commission-container" style="{{ old('role', $user->role) != 'vendeur' ? 'display: none;' : '' }}">
                                <x-input-label for="commission_rate" :value="__('Taux de commission (%)')" />
                                <x-text-input id="commission_rate" class="block mt-1 w-full" type="number" name="commission_rate" :value="old('commission_rate', $user->commission_rate)" min="0" max="100" step="0.01" />
                                <x-input-error :messages="$errors->get('commission_rate')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Applicable uniquement pour les vendeurs. Valeur par défaut appliquée aux nouvelles boutiques.') }}
                                </p>
                            </div>

                            <!-- Boutiques assignées -->
                            <div id="shops-container" class="mb-4" style="{{ old('role', $user->role) == 'admin' ? 'display: none;' : '' }}">
                                <x-input-label for="shops" :value="__('Boutiques assignées')" />
                                <div class="mt-2 border border-gray-200 dark:border-gray-700 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @php
                                            // Récupérer les boutiques attribuées et le statut de manager
                                            $userShopIds = $userShops->pluck('id')->toArray();
                                            $userManagerShopIds = $userShops->where('pivot.is_manager', true)->pluck('id')->toArray();

                                            // Si l'utilisateur connecté est un manager, ne montrer que ses boutiques gérées
                                            $availableShops = $shops;
                                            if (!auth()->user()->isAdmin()) {
                                                $managedShopIds = auth()->user()->shops()->where('shop_user.is_manager', true)->pluck('shops.id')->toArray();
                                                $availableShops = $shops->whereIn('id', $managedShopIds);
                                            }
                                        @endphp

                                        @foreach($availableShops as $shop)
                                            <div class="flex items-start space-x-3">
                                                <div class="flex h-5 items-center">
                                                    <input id="shop_{{ $shop->id }}" name="shops[]" value="{{ $shop->id }}" type="checkbox"
                                                           {{ in_array($shop->id, old('shops', $userShopIds)) ? 'checked' : '' }}
                                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                </div>
                                                <div class="text-sm leading-6">
                                                    <label for="shop_{{ $shop->id }}" class="font-medium text-gray-900 dark:text-gray-100">{{ $shop->name }}</label>
                                                    <p class="text-gray-500 dark:text-gray-400">{{ $shop->address }}</p>

                                                    <div class="mt-2 shop-manager-option" style="{{ old('role', $user->role) == 'manager' ? '' : 'display: none;' }}">
                                                        <div class="flex items-center">
                                                            <input id="manager_{{ $shop->id }}" name="manager_shops[]" value="{{ $shop->id }}" type="checkbox"
                                                                   {{ in_array($shop->id, old('manager_shops', $userManagerShopIds)) ? 'checked' : '' }}
                                                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                            <label for="manager_{{ $shop->id }}" class="ml-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                {{ __('Assigner comme manager') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Un vendeur ne peut être assigné qu\'à une seule boutique.') }}
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
                const shopsContainer = document.getElementById('shops-container');
                const shopCheckboxes = document.querySelectorAll('input[name="shops[]"]');
                const managerOptions = document.querySelectorAll('.shop-manager-option');

                function toggleCommissionField() {
                    commissionContainer.style.display = roleSelect.value === 'vendeur' ? 'block' : 'none';
                }

                function toggleShopOptions() {
                    // Show/hide shop manager options based on role
                    managerOptions.forEach(option => {
                        option.style.display = roleSelect.value === 'manager' ? 'block' : 'none';
                    });

                    // For vendors, enforce single shop selection
                    if (roleSelect.value === 'vendeur') {
                        let selectedCount = 0;

                        // Count initial selected shops
                        shopCheckboxes.forEach(checkbox => {
                            if (checkbox.checked) selectedCount++;
                        });

                        shopCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                if (this.checked) {
                                    // If this one is being checked
                                    // Uncheck all others first
                                    shopCheckboxes.forEach(cb => {
                                        if (cb !== this && cb.checked) {
                                            cb.checked = false;
                                        }
                                    });
                                }
                            });
                        });
                    }

                    // Show or hide shops container based on role
                    shopsContainer.style.display = roleSelect.value === 'admin' ? 'none' : 'block';
                }

                // Initial setup
                toggleCommissionField();
                toggleShopOptions();

                // Add event listener for role changes
                roleSelect.addEventListener('change', function() {
                    toggleCommissionField();
                    toggleShopOptions();

                    // Reset shop selections when changing roles
                    if (roleSelect.value === 'admin') {
                        shopCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
