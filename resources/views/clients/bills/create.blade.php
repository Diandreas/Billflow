<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Nouvelle facture pour {{ $client->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Créez une facture pour ce client') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('clients.show', $client) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ __('Veuillez corriger les erreurs suivantes:') }}</p>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mb-5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2">
                        <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: 33%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span class="font-medium text-indigo-600">Client</span>
                        <span>Produits</span>
                        <span>Finalisation</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main form -->
                <div class="lg:col-span-2">
                    <form id="billForm" action="{{ route('bills.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">

                        <!-- Client Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Informations Client') }}
                                    </h3>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="flex flex-col sm:flex-row sm:justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Client') }}:</h4>
                                            <p class="font-medium text-gray-800">{{ $client->name }}</p>
                                            @if($client->email)
                                                <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-3 sm:mt-0">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Contact') }}:</h4>
                                            @if($client->phones->count() > 0)
                                                @foreach($client->phones as $phone)
                                                    <p class="text-sm text-gray-600">{{ $phone->number }}</p>
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-500">{{ __('Aucun téléphone') }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-3 sm:mt-0">
                                            <h4 class="text-sm font-medium text-gray-500">{{ __('Adresse') }}:</h4>
                                            <p class="text-sm text-gray-600">{{ $client->address ?: __('Non renseignée') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Date et TVA --}}
                                    <div>
                                        <label for="date" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Date') }}
                                        </label>
                                        <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               required>
                                        @error('date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tax_rate" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Taux de TVA (%)') }}
                                        </label>
                                        <input type="number" id="tax_rate" name="tax_rate" value="18"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               min="0" max="100" step="0.5">
                                        @error('tax_rate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Produits') }}
                                    </h3>
                                    <button type="button" id="addProductBtn"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Ajouter un produit') }}
                                    </button>
                                </div>

                                <div id="productsContainer">
                                    <!-- Produits seront ajoutés ici -->
                                </div>

                                <template id="productTemplate">
                                    <div class="product-item border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                            <div class="md:col-span-5">
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    {{ __('Produit') }}
                                                </label>
                                                <div class="product-select-container relative">
                                                    <select name="products[]" class="product-select w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                        <option value="">{{ __('Sélectionner un produit') }}</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->default_price }}">
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    {{ __('Quantité') }}
                                                </label>
                                                <input type="number" name="quantities[]" value="1" min="1"
                                                       class="product-quantity w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                       required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    {{ __('Prix unitaire') }}
                                                </label>
                                                <input type="number" name="unit_prices[]" value="0" min="0"
                                                       class="product-price w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                       required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    {{ __('Total') }}
                                                </label>
                                                <input type="number" class="product-total w-full rounded-md bg-gray-50 border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                       readonly>
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" class="remove-product-btn w-full px-2 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Notes and Status Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    {{ __('Finalisation') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="status" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Statut') }}
                                        </label>
                                        <select id="status" name="status"
                                                class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="pending">{{ __('En attente') }}</option>
                                            <option value="paid">{{ __('Payée') }}</option>
                                            <option value="overdue">{{ __('En retard') }}</option>
                                        </select>
                                        @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="due_date" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Date d\'échéance') }}
                                        </label>
                                        <input type="date" id="due_date" name="due_date"
                                               value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        @error('due_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="block mb-1 text-sm font-medium text-gray-700">
                                        {{ __('Notes') }}
                                    </label>
                                    <textarea id="notes" name="notes" rows="3"
                                              class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                              placeholder="{{ __('Notes ou informations supplémentaires') }}"></textarea>
                                    @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-between items-center pt-4">
                                    <a href="{{ route('clients.show', $client) }}"
                                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-medium text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        {{ __('Annuler') }}
                                    </a>
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                            id="submitButton" disabled>
                                        {{ __('Créer la facture') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Summary panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ __('Récapitulatif') }}
                            </h3>

                            <div class="border-t border-gray-200 pt-4 pb-2">
                                <div class="flex justify-between py-1 text-sm">
                                    <span class="text-gray-600">{{ __('Sous-total HT') }}:</span>
                                    <span class="font-medium text-gray-900" id="subtotal">0 FCFA</span>
                                </div>
                                <div class="flex justify-between py-1 text-sm">
                                    <span class="text-gray-600">{{ __('TVA') }} (<span id="taxRate">18</span>%):</span>
                                    <span class="font-medium text-gray-900" id="tax">0 FCFA</span>
                                </div>
                                <div class="flex justify-between py-1 text-sm">
                                    <span class="text-gray-600">{{ __('Nombre de produits') }}:</span>
                                    <span class="font-medium text-gray-900" id="productCount">0</span>
                                </div>
                                <div class="flex justify-between py-3 text-base font-medium border-t border-gray-200 mt-2">
                                    <span class="text-gray-900">{{ __('Total TTC') }}:</span>
                                    <span class="text-indigo-600" id="total">0 FCFA</span>
                                </div>
                            </div>

                            <div class="mt-4 text-xs text-gray-500">
                                <p class="mb-1">
                                    {{ __('* Le montant total est calculé automatiquement en fonction des produits ajoutés.') }}
                                </p>
                                <p>
                                    {{ __('* La TVA est calculée sur le prix HT.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const productsContainer = document.getElementById('productsContainer');
                const productTemplate = document.getElementById('productTemplate');
                const addProductBtn = document.getElementById('addProductBtn');
                const taxRateInput = document.getElementById('tax_rate');
                const taxRateDisplay = document.getElementById('taxRate');
                const submitButton = document.getElementById('submitButton');
                
                // Update display values
                const updateTotals = () => {
                    const productItems = document.querySelectorAll('.product-item');
                    let subtotal = 0;
                    
                    productItems.forEach(item => {
                        const quantity = parseInt(item.querySelector('.product-quantity').value) || 0;
                        const price = parseFloat(item.querySelector('.product-price').value) || 0;
                        const total = quantity * price;
                        
                        item.querySelector('.product-total').value = total;
                        subtotal += total;
                    });
                    
                    const taxRate = parseFloat(taxRateInput.value) || 0;
                    const tax = subtotal * (taxRate / 100);
                    const total = subtotal + tax;
                    
                    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
                    document.getElementById('tax').textContent = formatCurrency(tax);
                    document.getElementById('total').textContent = formatCurrency(total);
                    document.getElementById('productCount').textContent = productItems.length;
                    
                    // Update progress bar
                    const progressBar = document.getElementById('progressBar');
                    if (productItems.length > 0) {
                        progressBar.style.width = '66%';
                        submitButton.disabled = false;
                    } else {
                        progressBar.style.width = '33%';
                        submitButton.disabled = true;
                    }
                };
                
                // Format currency
                const formatCurrency = (value) => {
                    return Math.round(value).toLocaleString('fr-FR') + ' FCFA';
                };
                
                // Add product item
                const addProductItem = () => {
                    const template = productTemplate.content.cloneNode(true);
                    productsContainer.appendChild(template);
                    
                    // Initialize the new product item
                    const newItem = productsContainer.lastElementChild;
                    
                    // Set up product select change handler
                    const productSelect = newItem.querySelector('.product-select');
                    productSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const defaultPrice = selectedOption.dataset.price || 0;
                        newItem.querySelector('.product-price').value = defaultPrice;
                        updateTotals();
                    });
                    
                    // Set up quantity and price change handlers
                    newItem.querySelector('.product-quantity').addEventListener('input', updateTotals);
                    newItem.querySelector('.product-price').addEventListener('input', updateTotals);
                    
                    // Set up remove button
                    newItem.querySelector('.remove-product-btn').addEventListener('click', function() {
                        newItem.remove();
                        updateTotals();
                    });
                    
                    updateTotals();
                };
                
                // Add first product item
                addProductItem();
                
                // Add product button handler
                addProductBtn.addEventListener('click', addProductItem);
                
                // Tax rate change handler
                taxRateInput.addEventListener('input', function() {
                    taxRateDisplay.textContent = this.value;
                    updateTotals();
                });
            });
        </script>
    @endpush
</x-app-layout> 