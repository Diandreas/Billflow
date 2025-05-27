<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-700 py-2 px-3 rounded-lg shadow-sm">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Tableau de bord Inventaire') }}
                </h2>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-orange-700 rounded-md hover:bg-orange-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouveau Produit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Produits</div>
                            <div class="text-lg font-semibold dark:text-white">{{ $stats['total_products'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-red-100 dark:bg-red-900 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Rupture de Stock</div>
                            <div class="text-lg font-semibold dark:text-white">{{ $stats['out_of_stock'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Stock Faible</div>
                            <div class="text-lg font-semibold dark:text-white">{{ $stats['low_stock'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 dark:bg-green-900 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Valeur du Stock</div>
                            <div class="text-lg font-semibold dark:text-white">{{ number_format($stats['total_stock_value'], 2) }} FCFA</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <!-- Produits en stock faible -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg lg:col-span-2">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Produits en stock faible ou rupture</h3>
                    </div>
                    <div class="p-2 max-h-80 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Produit') }}</th>
                                <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stock') }}</th>
                                <th scope="col" class="px-2 py-1 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($lowStockProducts as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                @if ($product->image)
                                                    <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                                @else
                                                    <div class="h-6 w-6 rounded-md bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-orange-600 dark:text-orange-300" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap">
                                        @if ($product->stock_quantity <= 0)
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    {{ __('Rupture') }}
                                                </span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    {{ $product->stock_quantity }} {{ __('(Faible)') }}
                                                </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 whitespace-nowrap text-right text-xs">
                                        <button type="button" onclick="confirmAdjust('{{ $product->id }}')" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="{{ __('Ajuster le stock') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 py-2 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Aucun produit en stock faible ou rupture') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mouvements récents -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Derniers mouvements de stock</h3>
                    </div>
                    <div class="p-2 max-h-80 overflow-y-auto">
                        <div class="space-y-2">
                            @forelse ($recentMovements as $movement)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded p-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $movement->product->name ?? 'Produit inconnu' }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span>
                                            @if ($movement->type === 'entry')
                                                <span class="text-green-600 dark:text-green-400">+{{ $movement->quantity }}</span>
                                            @elseif ($movement->type === 'exit')
                                                <span class="text-red-600 dark:text-red-400">-{{ $movement->quantity }}</span>
                                            @else
                                                <span class="text-blue-600 dark:text-blue-400">{{ $movement->quantity }}</span>
                                            @endif
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $movement->reference }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 dark:text-gray-400 text-xs p-3">
                                    Aucun mouvement récent
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des produits -->
            <div class="mt-3 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200">Inventaire complet</h3>

                    <!-- Filtres rapides -->
                    <div class="flex flex-col sm:flex-row gap-2 mb-2">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="text" id="searchInput" class="w-full pl-10 py-2 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-orange-500 focus:border-orange-500" placeholder="Rechercher un produit...">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                            <div>
                                <select id="categoryFilter" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">{{ __('Toutes catégories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select id="brandFilter" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">{{ __('Toutes marques') }}</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select id="modelFilter" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-orange-500 focus:border-orange-500" {{ $models->isEmpty() && !request('brand_id') ? 'disabled' : '' }}>
                                    <option value="">{{ __('Tous modèles') }}</option>
                                    @foreach($models as $model)
                                        <option value="{{ $model->id }}" {{ request('product_model_id') == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select id="stockStatusFilter" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">{{ __('Tout état de stock') }}</option>
                                    <option value="in">{{ __('En stock') }}</option>
                                    <option value="low">{{ __('Stock bas') }}</option>
                                    <option value="out">{{ __('Rupture') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Produit') }}</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Marque/Modèle') }}</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stock') }}</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Prix') }}</th>
                            <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody id="inventoryTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @if(isset($inventories))
                            @forelse ($inventories as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 inventory-row"
                                    data-name="{{ $product->name }}"
                                    data-sku="{{ $product->sku }}"
                                    data-category="{{ $product->category->name ?? 'N/A' }}"
                                    data-price="{{ $product->default_price }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                    data-barcode="{{ $product->barcode ?? '' }}"
                                    data-description="{{ $product->description ?? '' }}">
                                    <td class="px-2 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 mr-2">
                                                @if ($product->image)
                                                    <img class="h-8 w-8 rounded-md object-cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-md bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600 dark:text-orange-300" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 30) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $product->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        @if($product->brand)
                                            {{ $product->brand->name }}
                                            @if($product->productModel)
                                                <span class="text-xs text-gray-400">{{ $product->productModel->name }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap">
                                        @if ($product->stock_quantity <= 0)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                {{ __('Rupture') }}
                                            </span>
                                        @elseif ($product->stock_quantity <= $product->stock_alert_threshold && $product->stock_alert_threshold > 0)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                {{ $product->stock_quantity }} {{ __('(Faible)') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap">
                                        <div class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ number_format($product->default_price, 2) }} FCFA</div>
                                        @if ($product->compare_price > 0)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 line-through">{{ number_format($product->compare_price, 2) }} FCFA</div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-right text-xs">
                                        <div class="flex justify-end space-x-1">
                                            <a href="{{ route('products.show', $product) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300" title="{{ __('Voir') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <button type="button" onclick="confirmAdjust('{{ $product->id }}')" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="{{ __('Ajuster le stock') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-2 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Aucun produit trouvé') }}</td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Aucun produit disponible</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-4 px-4 py-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Affichage de <span id="startIndex">1</span> à <span id="endIndex">{{ min(10, count($inventories)) }}</span> sur <span id="totalItems">{{ count($inventories) }}</span> produits
                    </div>
                    <div class="flex space-x-2">
                        <button id="prevPage" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            Précédent
                        </button>
                        <button id="nextPage" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            Suivant
                        </button>
                    </div>
                </div>
                <div class="p-2 text-center">
                    <a href="{{ route('inventory.movements') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        Voir tous les mouvements d'inventaire →
                    </a>
                </div>
            </div>

            <!-- Inclure les modals -->
            @include('inventory.partials.adjust-modal')
            @include('inventory.partials.delete-modal')
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsPerPage = 10;
            let currentPage = 1;
            let filteredItems = [];
            let sortConfig = {
                column: 'name',
                direction: 'asc'
            };
            let searchTimeout;

            // Get all table rows and convert to array
            const inventoryRows = Array.from(document.querySelectorAll('.inventory-row'));

            // Get UI elements
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const stockFilter = document.getElementById('stockFilter');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const startIndexElem = document.getElementById('startIndex');
            const endIndexElem = document.getElementById('endIndex');
            const totalItemsElem = document.getElementById('totalItems');
            const tableHeaders = document.querySelectorAll('th[data-sort]');

            // Initialize
            filterAndDisplayItems();

            // Event listeners
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterAndDisplayItems, 300);
            });
            categoryFilter.addEventListener('change', filterAndDisplayItems);
            stockFilter.addEventListener('change', filterAndDisplayItems);
            prevPageBtn.addEventListener('click', goToPrevPage);
            nextPageBtn.addEventListener('click', goToNextPage);

            // Add sort event listeners
            tableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;

                    // Toggle direction if same column clicked again
                    if (sortConfig.column === column) {
                        sortConfig.direction = sortConfig.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortConfig.column = column;
                        sortConfig.direction = 'asc';
                    }

                    // Update sort icons
                    updateSortIcons();

                    // Re-filter and display
                    filterAndDisplayItems();
                });
            });

            function updateSortIcons() {
                tableHeaders.forEach(header => {
                    const icon = header.querySelector('.sort-icon');
                    if (header.dataset.sort === sortConfig.column) {
                        icon.textContent = sortConfig.direction === 'asc' ? '↑' : '↓';
                    } else {
                        icon.textContent = '↕';
                    }
                });
            }

            function filterAndDisplayItems() {
                // Reset pagination
                currentPage = 1;

                // Get filter values
                const searchValue = searchInput.value.toLowerCase();
                const categoryValue = categoryFilter.value.toLowerCase();
                const stockValue = stockFilter.value;

                // Filter items
                filteredItems = inventoryRows.filter(row => {
                    const name = row.dataset.name.toLowerCase();
                    const sku = row.dataset.sku.toLowerCase();
                    const barcode = row.dataset.barcode ? row.dataset.barcode.toLowerCase() : '';
                    const category = row.dataset.category.toLowerCase();
                    const description = row.dataset.description ? row.dataset.description.toLowerCase() : '';
                    const stock = parseInt(row.dataset.stock, 10);

                    // Search filter
                    const matchesSearch = !searchValue ||
                        name.indexOf(searchValue) >= 0 ||
                        sku.indexOf(searchValue) >= 0 ||
                        barcode.indexOf(searchValue) >= 0 ||
                        description.indexOf(searchValue) >= 0 ||
                        category.indexOf(searchValue) >= 0;

                    // Category filter
                    const matchesCategory = !categoryValue || category === categoryValue;

                    // Stock filter
                    let matchesStock = true;
                    if (stockValue === 'in-stock') {
                        matchesStock = stock > 5;
                    } else if (stockValue === 'low-stock') {
                        matchesStock = stock > 0 && stock <= 5;
                    } else if (stockValue === 'out-of-stock') {
                        matchesStock = stock <= 0;
                    }

                    return matchesSearch && matchesCategory && matchesStock;
                });

                // Sort items
                filteredItems.sort((a, b) => {
                    let aValue = a.dataset[sortConfig.column];
                    let bValue = b.dataset[sortConfig.column];

                    if (sortConfig.column === 'price' || sortConfig.column === 'stock') {
                        aValue = parseFloat(aValue);
                        bValue = parseFloat(bValue);
                    }

                    if (aValue < bValue) return sortConfig.direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return sortConfig.direction === 'asc' ? 1 : -1;
                    return 0;
                });

                displayItems();
                updatePaginationControls();
            }

            function displayItems() {
                const tableBody = document.getElementById('inventoryTableBody');

                // Calculate start and end indices for current page
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, filteredItems.length);

                // Update pagination info
                startIndexElem.textContent = filteredItems.length > 0 ? startIndex + 1 : 0;
                endIndexElem.textContent = endIndex;
                totalItemsElem.textContent = filteredItems.length;

                // Hide all rows
                inventoryRows.forEach(row => {
                    row.style.display = 'none';
                });

                // Show filtered rows for current page
                for (let i = startIndex; i < endIndex; i++) {
                    filteredItems[i].style.display = '';
                }

                // No results found
                if (filteredItems.length === 0) {
                    // Supprimer tout contenu existant
                    tableBody.innerHTML = '';

                    // Ajouter un message "aucun résultat"
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.innerHTML = `
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center py-4">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-base font-medium text-gray-900 dark:text-gray-100">Aucun produit ne correspond à votre recherche</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </td>
                `;
                    tableBody.appendChild(noResultsRow);
                } else {
                    // Remove any "no results" message if it exists
                    const noResultsRow = tableBody.querySelector('tr:not(.inventory-row)');
                    if (noResultsRow) {
                        noResultsRow.remove();
                    }
                }
            }

            function updatePaginationControls() {
                const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
            }

            function goToPrevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    displayItems();
                    updatePaginationControls();
                }
            }

            function goToNextPage() {
                const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    displayItems();
                    updatePaginationControls();
                }
            }

            // Initial sort icon update
            updateSortIcons();
        });
    </script>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const brandFilter = document.getElementById('brandFilter');
        const modelFilter = document.getElementById('modelFilter');
        const stockStatusFilter = document.getElementById('stockStatusFilter');
        
        // Fonction pour appliquer les filtres
        function applyFilters() {
            const searchValue = searchInput.value.toLowerCase();
            const categoryValue = categoryFilter.value;
            const brandValue = brandFilter.value;
            const modelValue = modelFilter.value;
            const stockValue = stockStatusFilter.value;
            
            // Construire l'URL avec les paramètres de filtrage
            let url = '{{ route("inventory.index") }}?';
            const params = [];
            
            if (searchValue) params.push(`search=${encodeURIComponent(searchValue)}`);
            if (categoryValue) params.push(`category_id=${categoryValue}`);
            if (brandValue) params.push(`brand_id=${brandValue}`);
            if (modelValue) params.push(`product_model_id=${modelValue}`);
            if (stockValue) params.push(`stock_status=${stockValue}`);
            
            window.location.href = url + params.join('&');
        }
        
        // Événements pour les changements de filtres
        [categoryFilter, stockStatusFilter].forEach(filter => {
            filter.addEventListener('change', applyFilters);
        });
        
        // Événement pour la recherche (avec délai)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
        
        // Gestion de la marque et du modèle
        if (brandFilter && modelFilter) {
            brandFilter.addEventListener('change', function() {
                const brandId = this.value;
                
                // Si une marque est sélectionnée, charger les modèles correspondants
                if (brandId) {
                    // Désactiver le select des modèles pendant le chargement
                    modelFilter.disabled = true;
                    modelFilter.innerHTML = '<option value="">{{ __("Chargement...") }}</option>';
                    
                    // Charger les modèles pour cette marque
                    fetch(`/products/search-models?brand_id=${brandId}`)
                        .then(response => response.json())
                        .then(models => {
                            modelFilter.innerHTML = '<option value="">{{ __("Tous modèles") }}</option>';
                            
                            models.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.name;
                                modelFilter.appendChild(option);
                            });
                            
                            modelFilter.disabled = false;
                            
                            // Appliquer les filtres après le chargement des modèles
                            applyFilters();
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement des modèles:', error);
                            modelFilter.innerHTML = '<option value="">{{ __("Erreur de chargement") }}</option>';
                            modelFilter.disabled = false;
                        });
                } else {
                    // Réinitialiser le select des modèles
                    modelFilter.innerHTML = '<option value="">{{ __("Tous modèles") }}</option>';
                    modelFilter.disabled = true;
                    
                    // Appliquer les filtres
                    applyFilters();
                }
            });
            
            // Si le modèle est activé mais pas désactivé par défaut, ajouter un événement change
            if (!modelFilter.disabled) {
                modelFilter.addEventListener('change', applyFilters);
            }
        }
    });
</script>
@endpush
