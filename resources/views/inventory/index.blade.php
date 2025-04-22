<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 py-2 px-3 rounded-lg shadow-sm">
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
                <div class="bg-white rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Total Produits</div>
                            <div class="text-lg font-semibold">{{ $stats['total_products'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-red-100 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Rupture de Stock</div>
                            <div class="text-lg font-semibold">{{ $stats['out_of_stock'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Stock Faible</div>
                            <div class="text-lg font-semibold">{{ $stats['low_stock'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-3">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Valeur du Stock</div>
                            <div class="text-lg font-semibold">{{ number_format($stats['total_stock_value'], 2) }} €</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <!-- Produits en stock faible -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg lg:col-span-2">
                    <div class="p-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700">Produits en stock faible ou rupture</h3>
                    </div>
                    <div class="p-2 max-h-80 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Produit') }}</th>
                                    <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                    <th scope="col" class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                                    <th scope="col" class="px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($lowStockProducts as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                    @if ($product->image)
                                                        <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                                    @else
                                                        <div class="h-6 w-6 rounded-md bg-orange-100 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-xs font-medium text-gray-900">{{ $product->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-500">
                                            {{ $product->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap">
                                            @if ($product->stock_quantity <= 0)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">
                                                    {{ __('Rupture') }}
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $product->stock_quantity }} {{ __('(Faible)') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1 whitespace-nowrap text-right text-xs">
                                            <button type="button" onclick="confirmAdjust('{{ $product->id }}')" class="text-blue-600 hover:text-blue-900" title="{{ __('Ajuster le stock') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-2 py-2 text-center text-sm text-gray-500">{{ __('Aucun produit en stock faible ou rupture') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mouvements récents -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700">Derniers mouvements de stock</h3>
                    </div>
                    <div class="p-2 max-h-80 overflow-y-auto">
                        <div class="space-y-2">
                            @forelse ($recentMovements as $movement)
                                <div class="bg-gray-50 rounded p-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ $movement->product->name ?? 'Produit inconnu' }}</span>
                                        <span class="text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span>
                                            @if ($movement->type === 'entry')
                                                <span class="text-green-600">+{{ $movement->quantity }}</span>
                                            @elseif ($movement->type === 'exit')
                                                <span class="text-red-600">-{{ $movement->quantity }}</span>
                                            @else
                                                <span class="text-blue-600">{{ $movement->quantity }}</span>
                                            @endif
                                        </span>
                                        <span class="text-gray-500">{{ $movement->reference }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 text-xs p-3">
                                    Aucun mouvement récent
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des produits -->
            <div class="mt-3 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-700">Inventaire complet</h3>
                    
                    <!-- Filtres rapides -->
                    <div class="flex space-x-2">
                        <form action="{{ route('inventory.index') }}" method="GET" class="flex items-center">
                            <input type="text" name="search" placeholder="Rechercher..." class="text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 w-40">
                            <button type="submit" class="ml-1 p-1.5 bg-orange-600 text-white rounded-md text-xs hover:bg-orange-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Produit') }}</th>
                                <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Prix') }}</th>
                                <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                                <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($inventories))
                                @forelse ($inventories as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 mr-2">
                                                    @if ($product->image)
                                                        <img class="h-8 w-8 rounded-md object-cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                                    @else
                                                        <div class="h-8 w-8 rounded-md bg-orange-100 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-xs font-medium text-gray-900">{{ $product->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ Str::limit($product->description, 30) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            <div>{{ $product->sku }}</div>
                                            @if ($product->barcode)
                                                <div class="text-xs text-gray-400">{{ $product->barcode }}</div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $product->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">{{ number_format($product->price, 2) }} €</div>
                                            @if ($product->compare_price > 0)
                                                <div class="text-xs text-gray-500 line-through">{{ number_format($product->compare_price, 2) }} €</div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            @if ($product->stock_quantity <= 0)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">
                                                    {{ __('Rupture') }}
                                                </span>
                                            @elseif ($product->stock_quantity <= $product->stock_alert_threshold)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $product->stock_quantity }} {{ __('(Faible)') }}
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-right text-xs">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('products.show', $product) }}" class="text-orange-600 hover:text-orange-900" title="{{ __('Voir') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="confirmAdjust('{{ $product->id }}')" class="text-blue-600 hover:text-blue-900" title="{{ __('Ajuster le stock') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-2 py-2 text-center text-sm text-gray-500">{{ __('Aucun produit trouvé') }}</td>
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

                <div class="p-3">
                    {{ $inventories->appends(request()->except('page'))->links() }}
                </div>
            </div>

            <!-- Inclure les modals -->
            @include('inventory.partials.adjust-modal')
            @include('inventory.partials.delete-modal')
        </div>
    </div>
</x-app-layout>
