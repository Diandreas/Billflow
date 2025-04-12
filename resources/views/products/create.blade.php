<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Nouveau Produit') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Ajouter un nouveau produit au catalogue') }}
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
                    <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Nom du produit') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
                            </div>
                            <div>
                                <label for="type" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Type') }}</label>
                                <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>{{ __('Produit physique') }}</option>
                                    <option value="service" {{ old('type', 'service') == 'service' ? 'selected' : '' }}>{{ __('Service') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="default_price" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Prix de vente') }}</label>
                                <input type="number" step="0.01" min="0" id="default_price" name="default_price" value="{{ old('default_price') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                            <div>
                                <label for="sku" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Référence/SKU') }}</label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                        </div>

                        <div class="grid gap-4 mb-4 sm:grid-cols-2">
                            <div>
                                <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Catégorie') }}</label>
                                <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                    <option value="">{{ __('Aucune catégorie') }}</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="accounting_category" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Catégorie comptable') }}</label>
                                <input type="text" id="accounting_category" name="accounting_category" value="{{ old('accounting_category') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            </div>
                        </div>

                        <!-- Section spécifique aux produits physiques -->
                        <div id="physical-product-section" class="border-t border-gray-200 pt-4 mt-4">
                            <h3 class="text-lg font-medium mb-2">{{ __('Informations de stock') }}</h3>
                            <div class="grid gap-4 mb-4 sm:grid-cols-3">
                                <div>
                                    <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Quantité en stock') }}</label>
                                    <input type="number" min="0" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                                <div>
                                    <label for="stock_alert_threshold" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Seuil d\'alerte') }}</label>
                                    <input type="number" min="0" id="stock_alert_threshold" name="stock_alert_threshold" value="{{ old('stock_alert_threshold') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                                <div>
                                    <label for="cost_price" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Prix d\'achat') }}</label>
                                    <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block mb-2 text-sm font-medium text-gray-900">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Créer le produit') }}
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
</x-app-layout>
