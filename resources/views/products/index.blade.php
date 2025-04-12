<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Produits') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Gérez votre catalogue de produits et services') }}
                </p>
            </div>
            <button onclick="toggleModal('newProductModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                <i class="bi bi-plus-lg mr-2"></i>
                {{ __('Nouveau Produit') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                            <i class="bi bi-box-seam text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Total des produits') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="bi bi-check2-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Produits actifs') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_products'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="bi bi-currency-dollar text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                            <i class="bi bi-tag text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Prix moyen') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="mb-4">
                <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="{{ __('Rechercher un produit...') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Tous les types') }}</option>
                            <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>{{ __('Produits physiques') }}</option>
                            <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>{{ __('Services') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="stock" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Tous les stocks') }}</option>
                            <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>{{ __('En stock') }}</option>
                            <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>{{ __('Stock bas') }}</option>
                            <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>{{ __('Épuisé') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="sort" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Nom') }}</option>
                            <option value="default_price" {{ request('sort') == 'default_price' ? 'selected' : '' }}>{{ __('Prix') }}</option>
                            <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>{{ __('Date de création') }}</option>
                            <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>{{ __('Stock') }}</option>
                            <option value="usage_count" {{ request('sort') == 'usage_count' ? 'selected' : '' }}>{{ __('Utilisation') }}</option>
                            <option value="total_sales" {{ request('sort') == 'total_sales' ? 'selected' : '' }}>{{ __('Ventes') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="direction" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>{{ __('Croissant') }}</option>
                            <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>{{ __('Décroissant') }}</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        {{ __('Filtrer') }}
                    </button>
                    
                    @if(request()->anyFilled(['search', 'type', 'stock', 'sort', 'direction']))
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Réinitialiser') }}
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer product-card" 
                                 onclick="window.location.href='{{ route('products.show', $product) }}'">
                                <div class="bg-indigo-50 p-4 border-b relative">
                                    <div class="absolute top-2 right-2 flex space-x-2">
                                        <button onclick="event.stopPropagation(); editProduct({{ $product->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 bg-white p-1.5 rounded-full shadow-sm">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="event.stopPropagation();" class="text-red-600 hover:text-red-900 bg-white p-1.5 rounded-full shadow-sm delete-product-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <h3 class="text-lg font-semibold text-indigo-900 mb-1">{{ $product->name }}</h3>
                                    <p class="text-indigo-700 font-medium">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $product->description ?: 'Pas de description' }}</p>
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="text-gray-500 flex items-center">
                                            <i class="bi bi-receipt mr-1"></i>
                                            {{ $product->bills_count }} factures
                                        </div>
                                        <div class="text-indigo-600 font-medium">
                                            {{ number_format($product->total_sales ?? 0, 0, ',', ' ') }} FCFA
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="newProductModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative bg-white rounded-2xl shadow-xl mx-auto p-0 max-w-md w-full transform transition-transform duration-300 scale-95 opacity-0" id="modalContent">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">
                        <i class="bi bi-box-seam mr-2"></i>{{ __('Nouveau Produit') }}
                    </h3>
                    <button type="button" onclick="toggleModal('newProductModal')" class="text-white hover:text-gray-200 focus:outline-none">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="newProductForm" method="POST" action="{{ route('products.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom du produit') }}</label>
                        <input type="text" name="name" id="name" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} ({{ __('optionnelle') }})</label>
                        <textarea name="description" id="description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    </div>
                    <div>
                        <label for="default_price" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prix par défaut') }} (FCFA)</label>
                        <input type="number" min="0" name="default_price" id="default_price" value="0" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type de produit') }}</label>
                        <select name="type" id="type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="physical">{{ __('Produit physique') }}</option>
                            <option value="service" selected>{{ __('Service') }}</option>
                        </select>
                    </div>
                    <div class="flex justify-between pt-4">
                        <a href="{{ route('products.create') }}" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2.5 px-4 rounded-xl shadow-sm flex items-center transition duration-200">
                            <i class="bi bi-plus-lg mr-1"></i>{{ __('Créer un produit complet') }}
                        </a>
                        <div>
                            <button type="button" onclick="toggleModal('newProductModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2.5 px-4 rounded-xl mr-2 shadow-sm">
                                {{ __('Annuler') }}
                            </button>
                            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2.5 px-6 rounded-xl shadow-sm transition duration-200">
                                <i class="bi bi-check-lg mr-1"></i>{{ __('Créer') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            .product-card {
                cursor: pointer;
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }
            
            .product-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
        </style>
    @endpush

    @push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle modal avec animation
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContent = document.getElementById('modalContent');
            
            if (modal.classList.contains('hidden')) {
                // Afficher le modal
                modal.classList.remove('hidden');
                // Animation d'entrée
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                // Animation de sortie
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                // Cacher le modal après l'animation
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        }

        // Fonction pour rediriger vers la page d'édition d'un produit
        function editProduct(id) {
            window.location.href = `/products/${id}/edit`;
        }

        // Soumission du formulaire de création de produit
        const newProductForm = document.getElementById('newProductForm');
        if (newProductForm) {
            newProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // S'assurer que default_price a une valeur
                const defaultPriceInput = document.getElementById('default_price');
                if (!defaultPriceInput.value || defaultPriceInput.value === '') {
                    defaultPriceInput.value = '0';
                }
                
                // Récupération des données du formulaire
                const formData = new FormData(this);
                
                // Conversion en objet JSON avec gestion spécifique de default_price
                const jsonData = {};
                formData.forEach((value, key) => {
                    if (key === 'default_price') {
                        jsonData[key] = value === '' ? 0 : Number(value);
                    } else {
                        jsonData[key] = value;
                    }
                });
                
                // Définir explicitement un type s'il n'est pas présent
                if (!jsonData.type) {
                    jsonData.type = 'service';
                }
                
                // Envoi de la requête
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'  // Indique une requête AJAX
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.headers.get('content-type')?.includes('text/html')) {
                            throw new Error('Le serveur a répondu avec une page HTML au lieu de JSON');
                        }
                        throw new Error('Erreur réseau ou côté serveur: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Fermer le modal
                        toggleModal('newProductModal');
                        
                        // Afficher un message de succès
                        Swal.fire({
                            icon: 'success',
                            title: 'Produit créé',
                            text: data.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#fff',
                            iconColor: '#4F46E5',
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                        
                        // Recharger la page après un court délai
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Afficher un message d'erreur
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Une erreur est survenue',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            background: '#fff',
                            iconColor: '#EF4444',
                            customClass: {
                                popup: 'colored-toast'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de la création du produit',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        background: '#fff',
                        iconColor: '#EF4444',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });
                });
            });
        }

        // Filtrage des produits avec la barre de recherche
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.grid > div').forEach(product => {
                    const name = product.querySelector('h3').textContent.toLowerCase();
                    const description = product.querySelector('p').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || description.includes(searchTerm)) {
                        product.style.display = '';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        }

        // Confirmation de suppression avec SweetAlert
        document.addEventListener('click', function(e) {
            const deleteButton = e.target.closest('.delete-product-btn');
            if (deleteButton && deleteButton.closest('form[action*="products"]') && deleteButton.closest('form[method="POST"]')) {
                e.preventDefault();
                const form = deleteButton.closest('form');
                
                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous ne pourrez pas revenir en arrière!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    customClass: {
                        confirmButton: 'swal2-confirm-button',
                        cancelButton: 'swal2-cancel-button'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Soumettre le formulaire via AJAX pour éviter le rechargement de la page
                        const url = form.getAttribute('action');
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                _method: 'DELETE'
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur serveur: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé!',
                                    text: data.message,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    background: '#fff',
                                    iconColor: '#4F46E5'
                                });
                                
                                // Supprimer la carte du produit du DOM
                                const productCard = deleteButton.closest('.product-card');
                                if (productCard) {
                                    productCard.remove();
                                } else {
                                    // Recharger la page si l'élément ne peut pas être trouvé
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: data.message || "Impossible de supprimer ce produit",
                                    confirmButtonColor: '#4F46E5'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Une erreur est survenue lors de la suppression du produit',
                                confirmButtonColor: '#4F46E5'
                            });
                        });
                    }
                });
            }
        });
    </script>

    <style>
        /* Styles pour les boutons SweetAlert */
        .swal2-confirm-button {
            padding: 0.5rem 1.5rem !important;
            font-weight: 500 !important;
            border-radius: 0.5rem !important;
        }
        
        .swal2-cancel-button {
            padding: 0.5rem 1.5rem !important;
            font-weight: 500 !important;
            border-radius: 0.5rem !important;
        }
        
        /* Animation pour le modal */
        #modalContent {
            transition: all 0.3s ease-out;
        }
    </style>
    @endpush

</x-app-layout>
