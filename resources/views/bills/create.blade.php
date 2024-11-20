
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouvelle Facture') }}
            </h2>
            <a href="{{ route('bills.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="billForm" action="{{ route('bills.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Client Section -->
                            <div>
                                <label class="block mb-2">
                                    <span class="text-gray-700">Client</span>
                                    <div class="flex space-x-2">
                                        <select id="client_id" name="client_id" class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="">Sélectionner un client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button"
                                                onclick="toggleModal('newClientModal')"
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            +
                                        </button>
                                    </div>
                                </label>
                            </div>

                            <!-- Date and Tax Section -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2">
                                        <span class="text-gray-700">Date</span>
                                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                                               class="mt-1 block w-full rounded-md border-gray-300">
                                    </label>
                                </div>
                                <div>
                                    <label class="block mb-2">
                                        <span class="text-gray-700">TVA (%)</span>
                                        <input type="number" name="tax_rate" id="tax_rate" value="20"
                                               class="mt-1 block w-full rounded-md border-gray-300">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Produits</h3>
                                <button type="button"
                                        onclick="addProductRow()"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Ajouter un produit
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="productsContainer"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Totals Section -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex flex-col items-end space-y-2">
                                <div class="text-sm">
                                    <span class="font-medium">Sous-total:</span>
                                    <span id="subtotal" class="ml-2">0.00 €</span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium">TVA:</span>
                                    <span id="taxAmount" class="ml-2">0.00 €</span>
                                </div>
                                <div class="text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="total" class="ml-2">0.00 €</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Créer la facture
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- New Client Modal -->
    <div id="newClientModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                // Suite de resources/views/bills/create.blade.php (Modal Client)
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nouveau Client</h3>
                <form id="newClientForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Genre</label>
                        <select name="sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Non spécifié</option>
                            <option value="M">Homme</option>
                            <option value="F">Femme</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                        <input type="date" name="birth"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div id="phonesContainer">
                        <label class="block text-sm font-medium text-gray-700">Téléphones</label>
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="phones[]"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <button type="button" onclick="addPhoneField()"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleModal('newClientModal')"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Product Modal -->
    <div id="newProductModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nouveau Produit</h3>
                <form id="newProductForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleModal('newProductModal')"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleModal(modalId) {
                document.getElementById(modalId).classList.toggle('hidden');
            }

            function addPhoneField() {
                const container = document.querySelector('#phonesContainer .space-y-2');
                const div = document.createElement('div');
                div.className = 'flex gap-2';
                div.innerHTML = `
                <input type="text" name="phones[]"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <button type="button" onclick="this.parentElement.remove()"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    -
                </button>
            `;
                container.appendChild(div);
            }

            function addProductRow() {
                const container = document.getElementById('productsContainer');
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <select name="products[]" class="block w-full rounded-md border-gray-300 product-select">
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                </select>
                <button type="button" onclick="toggleModal('newProductModal')"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    +
                </button>
            </div>
        </td>
        <td class="px-6 py-4">
            <input type="number" name="quantities[]" value="1" min="1"
                   class="block w-full rounded-md border-gray-300 quantity">
        </td>
        <td class="px-6 py-4">
            <input type="number" name="unit_prices[]" step="0.01" min="0"
                   class="block w-full rounded-md border-gray-300 unit-price">
        </td>
        <td class="px-6 py-4">
            <span class="line-total">0.00 €</span>
        </td>
        <td class="px-6 py-4">
            <button type="button" onclick="this.closest('tr').remove(); calculateTotals()"
                    class="text-red-600 hover:text-red-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </td>
`;
                container.appendChild(row);

                // Initialiser TomSelect sur le nouveau select
                new TomSelect(row.querySelector('.product-select'), {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    load: function(query, callback) {
                        fetch(`/products/search?q=${query}`)
                            .then(response => response.json())
                            .then(json => callback(json));
                    }
                });
            }

            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('#productsContainer tr').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    const lineTotal = quantity * unitPrice;
                    row.querySelector('.line-total').textContent = lineTotal.toFixed(2) + ' €';
                    subtotal += lineTotal;
                });

                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = subtotal * (taxRate / 100);
                const total = subtotal + taxAmount;

                document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
                document.getElementById('taxAmount').textContent = taxAmount.toFixed(2) + ' €';
                document.getElementById('total').textContent = total.toFixed(2) + ' €';
            }

            // Event Listeners pour les formulaires modaux
            document.getElementById('newClientForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('/clients', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            const select = document.getElementById('client_id');
                            const option = new Option(data.client.name, data.client.id);
                            select.add(option);
                            select.value = data.client.id;
                            toggleModal('newClientModal');
                            this.reset();
                        }
                    });
            });

            // Initialisation
            document.addEventListener('DOMContentLoaded', function() {
                // Initialiser le premier select de client avec TomSelect
                new TomSelect('#client_id', {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    load: function(query, callback) {
                        fetch(`/clients/search?q=${query}`)
                            .then(response => response.json())
                            .then(json => callback(json));
                    }
                });

                // Ajouter la première ligne de produit
                addProductRow();
            });
        </script>
    @endpush
</x-app-layout>
