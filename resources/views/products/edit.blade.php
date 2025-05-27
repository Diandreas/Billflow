<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Modifier le Produit') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Modifier les informations du produit') }}
                </p>
            </div>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Nom du produit') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
                            </div>
                            <div>
                                <label for="type" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Type') }}</label>
                                <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="physical" {{ old('type', $product->type) == 'physical' ? 'selected' : '' }}>{{ __('Produit physique') }}</option>
                                    <option value="service" {{ old('type', $product->type) == 'service' ? 'selected' : '' }}>{{ __('Service') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="default_price" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Prix de vente') }}</label>
                                <input type="number" step="0.01" min="0" id="default_price" name="default_price" value="{{ old('default_price', $product->default_price) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                            <div>
                                <label for="sku" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Référence/SKU') }}</label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                        </div>

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="supplier_id" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Fournisseur') }}</label>
                                <select id="supplier_id" name="supplier_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="">{{ __('Aucun') }}</option>
                                    @foreach($suppliers ?? [] as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Catégorie') }}</label>
                                <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="">{{ __('Aucune') }}</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Champs pour la marque et le modèle -->
                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="brand_autocomplete" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Marque') }}</label>
                                <div class="relative">
                                    <input type="text" id="brand_autocomplete" 
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" 
                                        placeholder="{{ __('Rechercher ou créer une marque') }}"
                                        value="{{ $product->brand->name ?? '' }}"
                                        autocomplete="off">
                                    <input type="hidden" id="brand_id" name="brand_id" value="{{ old('brand_id', $product->brand_id) }}">
                                    <div id="brand_suggestions" class="absolute z-10 hidden bg-white border border-gray-300 w-full mt-1 rounded-lg shadow-lg"></div>
                                </div>
                            </div>
                            <div>
                                <label for="model_autocomplete" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Modèle') }}</label>
                                <div class="relative">
                                    <input type="text" id="model_autocomplete" 
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" 
                                        placeholder="{{ __('Rechercher ou créer un modèle') }}"
                                        value="{{ $product->productModel->name ?? '' }}"
                                        autocomplete="off"
                                        {{ $product->brand_id ? '' : 'disabled' }}>
                                    <input type="hidden" id="product_model_id" name="product_model_id" value="{{ old('product_model_id', $product->product_model_id) }}">
                                    <div id="model_suggestions" class="absolute z-10 hidden bg-white border border-gray-300 w-full mt-1 rounded-lg shadow-lg"></div>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="accounting_category" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Catégorie comptable') }}</label>
                                <input type="text" id="accounting_category" name="accounting_category" value="{{ old('accounting_category', $product->accounting_category) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                            <div>
                                <label for="tax_category" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Catégorie fiscale') }}</label>
                                <input type="text" id="tax_category" name="tax_category" value="{{ old('tax_category', $product->tax_category) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                        </div>

                        <!-- Section spécifique aux produits physiques -->
                        <div id="physical-product-section" class="border-t border-gray-200 pt-4 mt-4">
                            <h3 class="text-lg font-medium mb-2">{{ __('Informations de stock') }}</h3>
                            <div class="grid gap-4 mb-4 sm:grid-cols-3">
                                <div>
                                    <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Quantité en stock') }}</label>
                                    <input type="number" min="0" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                                <div>
                                    <label for="stock_alert_threshold" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Seuil d\'alerte') }}</label>
                                    <input type="number" min="0" id="stock_alert_threshold" name="stock_alert_threshold" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                                <div>
                                    <label for="cost_price" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Prix d\'achat') }}</label>
                                    <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                            </div>

                            <!-- Option de troc -->
                            <div class="mt-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_barterable" name="is_barterable" value="1" {{ old('is_barterable', $product->is_barterable) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="is_barterable" class="ml-2 block text-sm font-medium text-gray-900">
                                        {{ __('Disponible pour le troc') }}
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ __('Cochez cette case si ce produit peut être utilisé dans les opérations de troc') }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Mettre à jour') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const physicalSection = document.getElementById('physical-product-section');

            function togglePhysicalSection() {
                if (typeSelect.value === 'service') {
                    physicalSection.style.display = 'none';
                } else {
                    physicalSection.style.display = 'block';
                }
            }

            // Initial state
            togglePhysicalSection();

            // Listen for changes
            typeSelect.addEventListener('change', togglePhysicalSection);
        });
    </script>

    @include('products.create-supplier-modal')
</x-app-layout>

@section('scripts')
<script>
// Autocomplétion pour les marques
const brandAutocomplete = document.getElementById('brand_autocomplete');
const brandIdInput = document.getElementById('brand_id');
const brandSuggestions = document.getElementById('brand_suggestions');
const modelAutocomplete = document.getElementById('model_autocomplete');
const modelIdInput = document.getElementById('product_model_id');
const modelSuggestions = document.getElementById('model_suggestions');

// Recherche de marques
brandAutocomplete.addEventListener('input', async function() {
    const query = this.value.trim();
    
    if (query.length < 2) {
        brandSuggestions.classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/products/search-brands?query=${encodeURIComponent(query)}`);
        const brands = await response.json();
        
        // Afficher les suggestions
        brandSuggestions.innerHTML = '';
        
        if (brands.length === 0) {
            // Option pour créer une nouvelle marque
            const createItem = document.createElement('div');
            createItem.className = 'p-2 cursor-pointer hover:bg-gray-100';
            createItem.innerHTML = `<strong>Créer la marque:</strong> ${query}`;
            createItem.addEventListener('click', async function() {
                try {
                    const response = await fetch('/products/create-brand', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ name: query })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        brandAutocomplete.value = result.brand.name;
                        brandIdInput.value = result.brand.id;
                        brandSuggestions.classList.add('hidden');
                        modelAutocomplete.disabled = false;
                        modelAutocomplete.focus();
                        modelIdInput.value = '';
                        modelAutocomplete.value = '';
                    } else {
                        alert(result.message || 'Une erreur est survenue');
                    }
                } catch (error) {
                    console.error('Erreur lors de la création de la marque:', error);
                }
            });
            
            brandSuggestions.appendChild(createItem);
        } else {
            // Afficher les marques existantes
            brands.forEach(brand => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer hover:bg-gray-100';
                item.textContent = brand.name;
                item.addEventListener('click', function() {
                    brandAutocomplete.value = brand.name;
                    brandIdInput.value = brand.id;
                    brandSuggestions.classList.add('hidden');
                    modelAutocomplete.disabled = false;
                    modelAutocomplete.focus();
                    // Réinitialiser le modèle si on change de marque
                    if (modelIdInput.dataset.brandId !== brand.id) {
                        modelIdInput.value = '';
                        modelAutocomplete.value = '';
                    }
                    modelIdInput.dataset.brandId = brand.id;
                });
                
                brandSuggestions.appendChild(item);
            });
        }
        
        brandSuggestions.classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors de la recherche de marques:', error);
    }
});

// Recherche de modèles
modelAutocomplete.addEventListener('input', async function() {
    const query = this.value.trim();
    const brandId = brandIdInput.value;
    
    if (query.length < 2 || !brandId) {
        modelSuggestions.classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/products/search-models?query=${encodeURIComponent(query)}&brand_id=${brandId}`);
        const models = await response.json();
        
        // Afficher les suggestions
        modelSuggestions.innerHTML = '';
        
        if (models.length === 0) {
            // Option pour créer un nouveau modèle
            const createItem = document.createElement('div');
            createItem.className = 'p-2 cursor-pointer hover:bg-gray-100';
            createItem.innerHTML = `<strong>Créer le modèle:</strong> ${query}`;
            createItem.addEventListener('click', async function() {
                try {
                    const response = await fetch('/products/create-model', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ 
                            name: query,
                            brand_id: brandId
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        modelAutocomplete.value = result.model.name;
                        modelIdInput.value = result.model.id;
                        modelSuggestions.classList.add('hidden');
                    } else {
                        if (result.model) {
                            // Le modèle existe déjà, utiliser celui-ci
                            modelAutocomplete.value = result.model.name;
                            modelIdInput.value = result.model.id;
                            modelSuggestions.classList.add('hidden');
                        } else {
                            alert(result.message || 'Une erreur est survenue');
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la création du modèle:', error);
                }
            });
            
            modelSuggestions.appendChild(createItem);
        } else {
            // Afficher les modèles existants
            models.forEach(model => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer hover:bg-gray-100';
                item.textContent = model.name;
                item.addEventListener('click', function() {
                    modelAutocomplete.value = model.name;
                    modelIdInput.value = model.id;
                    modelSuggestions.classList.add('hidden');
                });
                
                modelSuggestions.appendChild(item);
            });
        }
        
        modelSuggestions.classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors de la recherche de modèles:', error);
    }
});

// Fermer les suggestions si on clique ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('#brand_autocomplete') && !event.target.closest('#brand_suggestions')) {
        brandSuggestions.classList.add('hidden');
    }
    
    if (!event.target.closest('#model_autocomplete') && !event.target.closest('#model_suggestions')) {
        modelSuggestions.classList.add('hidden');
    }
});
</script>
@endsection
