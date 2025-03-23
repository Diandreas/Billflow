<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Modifier la Facture') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Modifier les détails de la facture') }}
                </p>
            </div>
            <a href="{{ route('bills.show', $bill) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="billForm" action="{{ route('bills.update', $bill) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Client Section --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ __('Informations de Base') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Client Selection --}}
                            <div>
                                <label for="client_id" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Client') }}
                                </label>
                                <div class="searchable-select-container">
                                    <input type="text"
                                           class="searchable-select-search w-full rounded-md border-gray-300"
                                           placeholder="Rechercher un client..."
                                           value="{{ $bill->client->name }}">
                                    <select id="client_id"
                                            name="client_id"
                                            class="searchable-select w-full rounded-md border-gray-300"
                                            required>
                                        <option value="">{{ __('Sélectionner un client') }}</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $bill->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} - {{ $client->email ?? 'Pas d\'email' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('client_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date et TVA --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="date" class="block mb-1 text-sm font-medium text-gray-700">
                                        {{ __('Date') }}
                                    </label>
                                    <input type="date"
                                           id="date"
                                           name="date"
                                           value="{{ old('date', $bill->date->format('Y-m-d')) }}"
                                           class="w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label for="tax_rate" class="block mb-1 text-sm font-medium text-gray-700">
                                        {{ __('TVA (%)') }}
                                    </label>
                                    <input type="number"
                                           id="tax_rate"
                                           name="tax_rate"
                                           value="{{ old('tax_rate', $bill->tax_rate) }}"
                                           class="w-full rounded-md border-gray-300">
                                </div>
                            </div>

                            {{-- Statut --}}
                            <div>
                                <label for="status" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Statut') }}
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full rounded-md border-gray-300">
                                    <option value="pending" {{ $bill->status === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                    <option value="paid" {{ $bill->status === 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                                    <option value="cancelled" {{ $bill->status === 'cancelled' ? 'selected' : '' }}>{{ __('Annulée') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Section --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ __('Produits et Services') }}
                            </h3>
                            <button type="button"
                                    onclick="addProductRow()"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Ajouter un Produit') }}
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Produit') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                        {{ __('Quantité') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                        {{ __('Prix unitaire (FCFA)') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                        {{ __('Total (FCFA)') }}
                                    </th>
                                    <th class="relative px-6 py-3 w-20">
                                        <span class="sr-only">{{ __('Actions') }}</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="productsContainer" class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>

                        {{-- Totals --}}
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex flex-col items-end space-y-2">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-500">{{ __('Sous-total:') }}</span>
                                    <span id="subtotal" class="ml-2">0 FCFA</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-500">{{ __('TVA:') }}</span>
                                    <span id="taxAmount" class="ml-2">0 FCFA</span>
                                </div>
                                <div class="text-lg font-bold">
                                    <span class="text-gray-900">{{ __('Total:') }}</span>
                                    <span id="total" class="ml-2 text-indigo-600">0 FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        {{ __('Mettre à jour la Facture') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .searchable-select-container {
                position: relative;
            }
            .searchable-select-search {
                width: 100%;
                padding: 0.5rem 1rem;
                margin-bottom: 0.25rem;
            }
            .searchable-select {
                width: 100%;
            }
            .searchable-select option {
                padding: 0.5rem 1rem;
            }
            .searchable-select option:hover {
                background-color: #f3f4f6;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Initialisation des select recherchables
            function initializeSearchableSelects() {
                document.querySelectorAll('.searchable-select-container').forEach(container => {
                    const select = container.querySelector('select');
                    const searchInput = container.querySelector('input');
                    const options = Array.from(select.options);

                    // Fonction de recherche
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();

                        options.forEach(option => {
                            const text = option.text.toLowerCase();
                            option.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    });

                    // Mise à jour du champ de recherche lors de la sélection
                    select.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        searchInput.value = selectedOption.text;
                    });
                });
            }

            // Fonction pour ajouter une ligne produit
            function addProductRow(productId = '', quantity = 1, unitPrice = 0) {
                const container = document.getElementById('productsContainer');
                const rowId = `product-row-${Date.now()}`;

                const row = document.createElement('tr');
                row.id = rowId;
                row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="searchable-select-container">
                        <input type="text"
                               class="searchable-select-search rounded-md border-gray-300"
                               placeholder="Rechercher un produit...">
                        <select name="products[]"
                                class="searchable-select rounded-md border-gray-300"
                                required
                                onchange="updatePrice('${rowId}', this)">
                            <option value="">{{ __('Sélectionner un produit') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-price="{{ $product->default_price }}"
                                        ${productId == {{ $product->id }} ? 'selected' : ''}>
                                    {{ $product->name }} - {{ $product->reference ?? 'Sans référence' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <input type="number"
                           name="quantities[]"
                           value="${quantity}"
                           min="1"
                           required
                           class="quantity w-full rounded-md border-gray-300"
                           onchange="calculateRowTotal('${rowId}')">
                </td>
                <td class="px-6 py-4">
                    <input type="number"
                           name="prices[]"
                           value="${unitPrice}"
                           class="price w-full rounded-md border-gray-300"
                           required
                           step="1"
                           min="0"
                           onchange="calculateRowTotal('${rowId}')">
                </td>
                <td class="px-6 py-4">
                    <span class="row-total">${(quantity * unitPrice).toLocaleString('fr-FR')} FCFA</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <button type="button"
                            onclick="removeRow('${rowId}')"
                            class="text-red-600 hover:text-red-900">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </td>
            `;

                container.appendChild(row);
                initializeSearchableSelects();
                calculateRowTotal(rowId);
            }

            // Mettre à jour le prix quand un produit est sélectionné
            function updatePrice(rowId, select) {
                const row = document.getElementById(rowId);
                const priceInput = row.querySelector('.price');
                const selectedOption = select.options[select.selectedIndex];

                if (selectedOption && selectedOption.dataset.price) {
                    priceInput.value = selectedOption.dataset.price;
                    calculateRowTotal(rowId);
                }
            }

            // Calculer le total d'une ligne
            function calculateRowTotal(rowId) {
                const row = document.getElementById(rowId);
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const total = quantity * price;

                row.querySelector('.row-total').textContent = `${total.toLocaleString('fr-FR')} FCFA`;
                calculateTotals();
            }

            // Calculer les totaux
            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('#productsContainer tr').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const price = parseFloat(row.querySelector('.price').value) || 0;
                    subtotal += quantity * price;
                });

                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = subtotal * (taxRate / 100);
                const total = subtotal + taxAmount;

                document.getElementById('subtotal').textContent = `${subtotal.toLocaleString('fr-FR')} FCFA`;
                document.getElementById('taxAmount').textContent = `${taxAmount.toLocaleString('fr-FR')} FCFA`;
                document.getElementById('total').textContent = `${total.toLocaleString('fr-FR')} FCFA`;
            }

            // Supprimer une ligne
            function removeRow(rowId) {
                document.getElementById(rowId).remove();
                calculateTotals();
            }

            // Initialisation
            document.addEventListener('DOMContentLoaded', function() {
                initializeSearchableSelects();
                
                // Chargement des produits existants
                @foreach($bill->products as $product)
                    addProductRow(
                        '{{ $product->id }}',
                        {{ $product->pivot->quantity }},
                        {{ $product->pivot->unit_price }}
                    );
                @endforeach
                
                // Si aucun produit, ajoutons une ligne vide
                if (document.querySelectorAll('#productsContainer tr').length === 0) {
                    addProductRow();
                }
                
                calculateTotals();
            });
        </script>
    @endpush
</x-app-layout>
