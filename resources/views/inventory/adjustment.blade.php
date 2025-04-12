<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajustement de stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-6">{{ __('Ajuster manuellement le niveau de stock') }}</h3>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('inventory.process-adjustment') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                            <!-- Produit -->
                            <div class="md:col-span-3">
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Produit') }} <span class="text-red-500">*</span></label>
                                <select id="product_id" name="product_id" class="w-full rounded-md border-gray-300" required>
                                    <option value="">{{ __('Sélectionner un produit') }}</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }} data-stock="{{ $product->stock_quantity }}">
                                            {{ $product->name }} ({{ __('Stock actuel') }}: {{ $product->stock_quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Type d'ajustement -->
                            <div class="md:col-span-1">
                                <label for="adjustment_type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }} <span class="text-red-500">*</span></label>
                                <select id="adjustment_type" name="adjustment_type" class="w-full rounded-md border-gray-300" required>
                                    <option value="add">{{ __('Ajouter') }}</option>
                                    <option value="subtract">{{ __('Retirer') }}</option>
                                    <option value="set">{{ __('Définir') }}</option>
                                </select>
                            </div>

                            <!-- Quantité -->
                            <div class="md:col-span-1">
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Quantité') }} <span class="text-red-500">*</span></label>
                                <input type="number" id="quantity" name="quantity" min="1" value="1" class="w-full rounded-md border-gray-300" required>
                            </div>

                            <!-- Stock actuel (lecture seule) -->
                            <div class="md:col-span-1">
                                <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Stock actuel') }}</label>
                                <input type="text" id="current_stock" class="w-full rounded-md border-gray-300 bg-gray-100" readonly>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                                <textarea id="notes" name="notes" rows="3" class="w-full rounded-md border-gray-300" placeholder="{{ __('Raison de l\'ajustement') }}"></textarea>
                            </div>

                            <!-- Prévisualisation du résultat -->
                            <div class="md:col-span-6 bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Résultat après ajustement') }}</h4>
                                <div class="flex items-center">
                                    <div id="stock_calculation" class="text-lg font-medium mr-4"></div>
                                    <div id="stock_result" class="text-2xl font-bold"></div>
                                </div>
                            </div>

                            <div class="md:col-span-6 flex justify-end">
                                <a href="{{ route('inventory.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 mr-2">
                                    {{ __('Annuler') }}
                                </a>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    {{ __('Effectuer l\'ajustement') }}
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
            const productSelect = document.getElementById('product_id');
            const typeSelect = document.getElementById('adjustment_type');
            const quantityInput = document.getElementById('quantity');
            const currentStockInput = document.getElementById('current_stock');
            const stockCalculation = document.getElementById('stock_calculation');
            const stockResult = document.getElementById('stock_result');
            
            function updateStockDisplay() {
                if (!productSelect.value) {
                    currentStockInput.value = '';
                    stockCalculation.textContent = '';
                    stockResult.textContent = '';
                    return;
                }
                
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const currentStock = parseInt(selectedOption.dataset.stock) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                let newStock = currentStock;
                
                currentStockInput.value = currentStock;
                
                // Calculate new stock based on adjustment type
                if (typeSelect.value === 'add') {
                    newStock = currentStock + quantity;
                    stockCalculation.textContent = `${currentStock} + ${quantity} =`;
                } else if (typeSelect.value === 'subtract') {
                    newStock = Math.max(0, currentStock - quantity);
                    stockCalculation.textContent = `${currentStock} - ${quantity} =`;
                } else if (typeSelect.value === 'set') {
                    newStock = quantity;
                    stockCalculation.textContent = `${quantity} =`;
                }
                
                // Display result with color coding
                stockResult.textContent = newStock;
                if (newStock > currentStock) {
                    stockResult.className = 'text-2xl font-bold text-green-600';
                } else if (newStock < currentStock) {
                    stockResult.className = 'text-2xl font-bold text-red-600';
                } else {
                    stockResult.className = 'text-2xl font-bold text-gray-700';
                }
            }
            
            // Add event listeners
            productSelect.addEventListener('change', updateStockDisplay);
            typeSelect.addEventListener('change', updateStockDisplay);
            quantityInput.addEventListener('input', updateStockDisplay);
            
            // Initialize display
            updateStockDisplay();
        });
    </script>
</x-app-layout> 