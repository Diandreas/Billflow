<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Réception de stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-6">{{ __('Enregistrer une réception de produits') }}</h3>
                    
                    <form action="{{ route('inventory.process-receive') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Informations sur la réception -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Référence') }}</label>
                                    <input type="text" id="reference" name="reference" class="w-full rounded-md border-gray-300" 
                                        placeholder="{{ __('Bon de livraison, commande, etc.') }}" 
                                        value="{{ old('reference', 'Réception ' . now()->format('Y-m-d')) }}">
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                                    <input type="text" id="notes" name="notes" class="w-full rounded-md border-gray-300" 
                                        placeholder="{{ __('Informations supplémentaires') }}">
                                </div>
                            </div>

                            <!-- Liste des produits -->
                            <div>
                                <h4 class="text-md font-medium mb-2">{{ __('Produits reçus') }}</h4>
                                
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-12 gap-4 mb-2 text-sm font-medium text-gray-700">
                                        <div class="col-span-5">{{ __('Produit') }}</div>
                                        <div class="col-span-2">{{ __('Quantité') }}</div>
                                        <div class="col-span-3">{{ __('Prix d\'achat unitaire') }}</div>
                                        <div class="col-span-2">{{ __('Actions') }}</div>
                                    </div>
                                </div>
                                
                                <div id="products-container">
                                    <!-- Les lignes de produits seront ajoutées ici -->
                                    <div class="product-row grid grid-cols-12 gap-4 mb-4 items-center">
                                        <div class="col-span-5">
                                            <select name="product_id[]" class="w-full rounded-md border-gray-300" required>
                                                <option value="">{{ __('Sélectionner un produit') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}" data-cost="{{ $product->cost_price }}">
                                                        {{ $product->name }} ({{ __('Stock actuel') }}: {{ $product->stock_quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <input type="number" name="quantity[]" min="1" value="1" class="w-full rounded-md border-gray-300" required>
                                        </div>
                                        <div class="col-span-3">
                                            <input type="number" name="cost_price[]" min="0" step="0.01" class="w-full rounded-md border-gray-300" placeholder="{{ __('Optionnel') }}">
                                        </div>
                                        <div class="col-span-2">
                                            <button type="button" class="remove-product text-red-600 hover:text-red-800" style="display: none;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" id="add-product" class="flex items-center text-indigo-600 hover:text-indigo-900 mt-2">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('Ajouter un produit') }}
                                </button>
                            </div>

                            <div class="flex justify-end">
                                <a href="{{ route('inventory.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 mr-2">
                                    {{ __('Annuler') }}
                                </a>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    {{ __('Enregistrer la réception') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('products-container');
            const addButton = document.getElementById('add-product');
            
            // Fonction pour ajouter un produit
            function addProductRow() {
                const row = document.querySelector('.product-row').cloneNode(true);
                
                // Réinitialiser les valeurs
                row.querySelector('select[name="product_id[]"]').value = '';
                row.querySelector('input[name="quantity[]"]').value = '1';
                row.querySelector('input[name="cost_price[]"]').value = '';
                
                // Afficher le bouton de suppression
                row.querySelector('.remove-product').style.display = 'block';
                row.querySelector('.remove-product').addEventListener('click', function() {
                    row.remove();
                });
                
                container.appendChild(row);
            }
            
            // Événement pour ajouter un produit
            addButton.addEventListener('click', addProductRow);
            
            // Afficher le prix d'achat quand un produit est sélectionné
            container.addEventListener('change', function(e) {
                if (e.target.name === 'product_id[]') {
                    const row = e.target.closest('.product-row');
                    const costInput = row.querySelector('input[name="cost_price[]"]');
                    const option = e.target.options[e.target.selectedIndex];
                    
                    if (option && option.dataset.cost) {
                        costInput.value = option.dataset.cost;
                    }
                }
            });
            
            // Activer le bouton de suppression sur la première ligne si on ajoute d'autres lignes
            addButton.addEventListener('click', function() {
                const firstRow = document.querySelector('.product-row');
                firstRow.querySelector('.remove-product').style.display = 'block';
                firstRow.querySelector('.remove-product').addEventListener('click', function() {
                    firstRow.remove();
                });
            });
        });
    </script>
</x-app-layout> 