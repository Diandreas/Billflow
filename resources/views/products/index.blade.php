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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search Bar -->
                    <div class="mb-6 relative">
                        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="search"
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Rechercher un produit...">
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer" onclick="window.location.href='{{ route('products.show', $product) }}'">
                                <div class="bg-indigo-50 p-4 border-b relative">
                                    <div class="absolute top-2 right-2 flex space-x-2">
                                        <button onclick="event.stopPropagation(); editProduct({{ $product->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 bg-white p-1.5 rounded-full shadow-sm">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="event.stopPropagation();" class="text-red-600 hover:text-red-900 bg-white p-1.5 rounded-full shadow-sm">
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

    <div id="newProductModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="absolute top-3 right-3">
                <button type="button" onclick="toggleModal('newProductModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-medium text-gray-900 mb-4">{{ __('Nouveau Produit') }}</h3>
                <form id="newProductForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Nom') }}</label>
                        <input type="text" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Prix par défaut (FCFA)') }}</label>
                        <input type="number" name="default_price" step="0.01" min="0" value="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleModal('newProductModal')"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors duration-150">
                            {{ __('Annuler') }}
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">
                            {{ __('Créer') }}
                        </button>
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
        </style>
    @endpush

    @push('scripts')
    <script>
        // Fonction pour basculer la visibilité du modal
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

        // Fonction pour rediriger vers la page d'édition
        function editProduct(id) {
            window.location.href = `/products/${id}/edit`;
        }

        // Gestion du formulaire d'ajout de produit
        document.getElementById('newProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route('products.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    description: formData.get('description'),
                    default_price: formData.get('default_price')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirection ou rafraîchissement de la page
                    window.location.reload();
                } else {
                    alert('Erreur lors de la création du produit');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        });

        // Filtrage des produits avec la barre de recherche
        document.getElementById('search').addEventListener('input', function() {
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
    </script>
    @endpush

</x-app-layout>
