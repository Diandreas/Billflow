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
                                            <div class="flex items-center space-x-2">
                                                <input type="hidden" name="product_id[]" class="product-id" required>
                                                <div class="product-display flex-1 bg-gray-100 p-3 rounded-md">
                                                    <span class="product-placeholder text-gray-500">{{ __('Sélectionner un produit') }}</span>
                                                    <div class="product-info hidden">
                                                        <div class="font-medium product-name"></div>
                                                        <div class="text-sm text-gray-600">
                                                            {{ __('Stock actuel') }}: <span class="product-stock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="select-product bg-indigo-600 text-white p-2 rounded-md hover:bg-indigo-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </button>
                                            </div>
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

    <!-- Modal de sélection de produit -->
    <div id="productSelectModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 transform transition-all">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-lg p-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-white">
                        {{ __('Sélectionner un produit') }}
                    </h3>
                    <button type="button" class="close-modal text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <input type="text" id="productSearchInput" class="w-full rounded-md border-gray-300" placeholder="{{ __('Rechercher un produit...') }}">
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nom') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Prix d\'achat') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="productList">
                            @foreach($products as $product)
                                <tr class="product-item hover:bg-gray-50 cursor-pointer" 
                                    data-id="{{ $product->id }}" 
                                    data-name="{{ $product->name }}" 
                                    data-stock="{{ $product->stock_quantity }}" 
                                    data-cost="{{ $product->cost_price }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $product->type === 'physical' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $product->type === 'physical' ? __('Produit') : __('Service') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $product->stock_quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('products-container');
            const addButton = document.getElementById('add-product');
            const modal = document.getElementById('productSelectModal');
            const productSearchInput = document.getElementById('productSearchInput');
            let currentProductRow = null;
            
            // Fonction pour ajouter un produit
            function addProductRow() {
                const row = document.querySelector('.product-row').cloneNode(true);
                
                // Réinitialiser les valeurs
                row.querySelector('input.product-id').value = '';
                row.querySelector('.product-placeholder').classList.remove('hidden');
                row.querySelector('.product-info').classList.add('hidden');
                row.querySelector('input[name="quantity[]"]').value = '1';
                row.querySelector('input[name="cost_price[]"]').value = '';
                
                // Configurer le bouton de sélection de produit
                const selectBtn = row.querySelector('.select-product');
                selectBtn.addEventListener('click', function() {
                    openProductModal(row);
                });
                
                // Afficher le bouton de suppression
                row.querySelector('.remove-product').style.display = 'block';
                row.querySelector('.remove-product').addEventListener('click', function() {
                    row.remove();
                });
                
                container.appendChild(row);
            }
            
            // Configurer le bouton de sélection de produit pour la première ligne
            const firstRowSelectBtn = document.querySelector('.product-row .select-product');
            firstRowSelectBtn.addEventListener('click', function() {
                openProductModal(firstRowSelectBtn.closest('.product-row'));
            });
            
            // Événement pour ajouter un produit
            addButton.addEventListener('click', addProductRow);
            
            // Ouvrir le modal de sélection de produit
            function openProductModal(row) {
                currentProductRow = row;
                modal.classList.remove('hidden');
                productSearchInput.value = '';
                productSearchInput.focus();
                filterProducts('');
            }
            
            // Fermer le modal
            document.querySelectorAll('.close-modal').forEach(btn => {
                btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });
            
            // Cliquer en dehors du modal pour fermer
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
            
            // Filtrer les produits
            productSearchInput.addEventListener('input', function() {
                filterProducts(this.value.toLowerCase());
            });
            
            function filterProducts(searchTerm) {
                const rows = document.querySelectorAll('#productList tr.product-item');
                
                rows.forEach(row => {
                    const name = row.querySelector('td:first-child').textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            
            // Sélectionner un produit
            document.querySelectorAll('#productList tr.product-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectProduct(this);
                });
            });
            
            function selectProduct(productElement) {
                const id = productElement.dataset.id;
                const name = productElement.dataset.name;
                const stock = productElement.dataset.stock;
                const cost = productElement.dataset.cost;
                
                // Mettre à jour les champs dans la ligne
                currentProductRow.querySelector('input.product-id').value = id;
                currentProductRow.querySelector('.product-name').textContent = name;
                currentProductRow.querySelector('.product-stock').textContent = stock;
                
                // Afficher les infos du produit
                currentProductRow.querySelector('.product-placeholder').classList.add('hidden');
                currentProductRow.querySelector('.product-info').classList.remove('hidden');
                
                // Mettre à jour le prix d'achat si disponible
                if (cost) {
                    currentProductRow.querySelector('input[name="cost_price[]"]').value = cost;
                }
                
                // Fermer le modal
                modal.classList.add('hidden');
                
                // Notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Produit sélectionné',
                    text: `${name} a été ajouté à la réception`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    background: '#fff',
                    iconColor: '#10B981'
                });
            }
            
            // Activer le bouton de suppression sur la première ligne si on ajoute d'autres lignes
            addButton.addEventListener('click', function() {
                const firstRow = document.querySelector('.product-row');
                firstRow.querySelector('.remove-product').style.display = 'block';
                firstRow.querySelector('.remove-product').addEventListener('click', function() {
                    firstRow.remove();
                });
            });
            
            // Validation et confirmation du formulaire
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault(); // Empêcher la soumission par défaut
                
                const productIds = document.querySelectorAll('input.product-id');
                let valid = true;
                let emptyProductRow = false;
                
                // Vérifier si au moins un produit est sélectionné
                for (let i = 0; i < productIds.length; i++) {
                    if (!productIds[i].value) {
                        emptyProductRow = true;
                        valid = false;
                        break;
                    }
                }
                
                if (emptyProductRow) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Veuillez sélectionner un produit pour chaque ligne ou supprimer les lignes vides',
                        confirmButtonColor: '#4F46E5'
                    });
                    return;
                }
                
                if (valid) {
                    // Confirmation avant soumission
                    Swal.fire({
                        title: 'Confirmer la réception',
                        text: 'Êtes-vous sûr de vouloir enregistrer cette réception de stock ?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4F46E5',
                        cancelButtonColor: '#EF4444',
                        confirmButtonText: 'Oui, enregistrer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Soumettre le formulaire
                            this.submit();
                            
                            // Montrer un indicateur de chargement
                            Swal.fire({
                                title: 'Enregistrement en cours...',
                                text: 'Veuillez patienter',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
    
    @push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
</x-app-layout> 