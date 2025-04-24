<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Nouveau Troc
                </h2>
                <a href="{{ route('barters.index') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    <form action="{{ route('barters.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Informations de base -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <h3 class="text-md font-medium text-gray-700 mb-2">Informations de base</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Client -->
                                <div>
                                    <label for="client_id" class="block text-sm font-medium text-gray-700">Client <span class="text-red-500">*</span></label>
                                    <select name="client_id" id="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Boutique -->
                                <div>
                                    <label for="shop_id" class="block text-sm font-medium text-gray-700">Boutique <span class="text-red-500">*</span></label>
                                    <select name="shop_id" id="shop_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="">Sélectionner une boutique</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shop_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Vendeur -->
                                <div>
                                    <label for="seller_id" class="block text-sm font-medium text-gray-700">Vendeur <span class="text-red-500">*</span></label>
                                    <select name="seller_id" id="seller_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="">Sélectionner un vendeur</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" 
                                                {{ old('seller_id') == $seller->id ? 'selected' : '' }}
                                                {{ (!old('seller_id') && Auth::user()->role == 'vendeur' && Auth::id() == $seller->id) ? 'selected' : '' }}>
                                                {{ $seller->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('seller_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Type de troc -->
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">Type de troc <span class="text-red-500">*</span></label>
                                    <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="same_type" {{ old('type') == 'same_type' ? 'selected' : '' }}>Même type</option>
                                        <option value="different_type" {{ old('type') == 'different_type' ? 'selected' : '' }}>Types différents</option>
                                    </select>
                                    @error('type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Articles donnés par le client -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <h3 class="text-md font-medium text-blue-700 mb-2">Articles donnés par le client</h3>
                            
                            <div id="given-items-container">
                                <div class="given-item border-b border-blue-200 pb-3 mb-3">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-blue-700">Nom <span class="text-red-500">*</span></label>
                                            <input type="text" name="given_items[0][name]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-blue-700">Valeur unitaire <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" min="0" name="given_items[0][value]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-blue-700">Quantité <span class="text-red-500">*</span></label>
                                            <input type="number" min="1" name="given_items[0][quantity]" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div class="md:pt-6">
                                            <button type="button" class="text-xs bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 remove-given-item hidden">Supprimer</button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-sm font-medium text-blue-700">Description</label>
                                        <textarea name="given_items[0][description]" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-given-item" class="mt-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Ajouter un article
                            </button>
                        </div>

                        <!-- Articles reçus par le client -->
                        <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-100">
                            <h3 class="text-md font-medium text-green-700 mb-2">Articles reçus par le client</h3>
                            
                            <div id="received-items-container">
                                <div class="received-item border-b border-green-200 pb-3 mb-3">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-green-700">Nom <span class="text-red-500">*</span></label>
                                            <input type="text" name="received_items[0][name]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-green-700">Valeur unitaire <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" min="0" name="received_items[0][value]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-green-700">Quantité <span class="text-red-500">*</span></label>
                                            <input type="number" min="1" name="received_items[0][quantity]" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-green-700">Produit (optionnel)</label>
                                            <select name="received_items[0][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <option value="">Aucun produit</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:pt-6">
                                            <button type="button" class="text-xs bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 remove-received-item hidden">Supprimer</button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label class="block text-sm font-medium text-green-700">Description</label>
                                        <textarea name="received_items[0][description]" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="add-received-item" class="mt-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Ajouter un article
                            </button>
                        </div>

                        <!-- Paiement complémentaire -->
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                            <h3 class="text-md font-medium text-yellow-700 mb-2">Paiement complémentaire (si nécessaire)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-yellow-700">Méthode de paiement</label>
                                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Aucun paiement</option>
                                        <option value="espèces">Espèces</option>
                                        <option value="carte">Carte bancaire</option>
                                        <option value="virement">Virement</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons de soumission -->
                        <div class="flex justify-end mt-4">
                            <a href="{{ route('barters.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 mr-2">Annuler</a>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gestion des articles donnés
            let givenItemsCount = 1;
            const givenItemsContainer = document.getElementById('given-items-container');
            const addGivenItemButton = document.getElementById('add-given-item');

            addGivenItemButton.addEventListener('click', function() {
                const newItem = givenItemsContainer.querySelector('.given-item').cloneNode(true);
                const inputs = newItem.querySelectorAll('input, textarea, select');
                
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[0\]/, `[${givenItemsCount}]`));
                        input.value = '';
                        if (input.type === 'number' && input.name.includes('[quantity]')) {
                            input.value = '1';
                        }
                    }
                });

                const removeButton = newItem.querySelector('.remove-given-item');
                removeButton.classList.remove('hidden');
                removeButton.addEventListener('click', function() {
                    newItem.remove();
                });

                givenItemsContainer.appendChild(newItem);
                givenItemsCount++;
            });

            // Gestion des articles reçus
            let receivedItemsCount = 1;
            const receivedItemsContainer = document.getElementById('received-items-container');
            const addReceivedItemButton = document.getElementById('add-received-item');

            addReceivedItemButton.addEventListener('click', function() {
                const newItem = receivedItemsContainer.querySelector('.received-item').cloneNode(true);
                const inputs = newItem.querySelectorAll('input, textarea, select');
                
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[0\]/, `[${receivedItemsCount}]`));
                        input.value = '';
                        if (input.type === 'number' && input.name.includes('[quantity]')) {
                            input.value = '1';
                        }
                        if (input.tagName === 'SELECT') {
                            input.selectedIndex = 0;
                        }
                    }
                });

                const removeButton = newItem.querySelector('.remove-received-item');
                removeButton.classList.remove('hidden');
                removeButton.addEventListener('click', function() {
                    newItem.remove();
                });

                receivedItemsContainer.appendChild(newItem);
                receivedItemsCount++;
            });
        });
    </script>
    @endpush
</x-app-layout> 