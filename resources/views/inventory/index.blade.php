<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion de l\'inventaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques d'inventaire -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Vue d\'ensemble du stock') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-500">{{ __('Produits en stock') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-500">{{ __('Produits en rupture') }}</p>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-500">{{ __('Produits en niveau bas') }}</p>
                            <p class="text-2xl font-bold text-amber-500">{{ $stats['low_stock'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-500">{{ __('Valeur totale du stock') }}</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_stock_value'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Gestion de l'inventaire -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 md:w-1/3">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Actions d\'inventaire') }}</h3>
                        <div class="space-y-3">
                            <a href="{{ route('inventory.receive') }}" class="block bg-green-100 text-green-800 hover:bg-green-200 px-4 py-3 rounded-lg font-medium">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('Recevoir un stock') }}
                                </div>
                                <p class="text-sm text-green-700 mt-1">{{ __('Enregistrer une entrée de produits dans l\'inventaire') }}</p>
                            </a>
                            
                            <a href="{{ route('inventory.adjustment') }}" class="block bg-blue-100 text-blue-800 hover:bg-blue-200 px-4 py-3 rounded-lg font-medium">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('Ajuster le stock') }}
                                </div>
                                <p class="text-sm text-blue-700 mt-1">{{ __('Ajuster manuellement les niveaux de stock') }}</p>
                            </a>
                            
                            <a href="{{ route('inventory.movements') }}" class="block bg-purple-100 text-purple-800 hover:bg-purple-200 px-4 py-3 rounded-lg font-medium">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    {{ __('Historique des mouvements') }}
                                </div>
                                <p class="text-sm text-purple-700 mt-1">{{ __('Consulter tous les mouvements de stock') }}</p>
                            </a>
                            
                            <a href="{{ route('products.index') }}?stock=low" class="block bg-amber-100 text-amber-800 hover:bg-amber-200 px-4 py-3 rounded-lg font-medium">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    {{ __('Produits à réapprovisionner') }}
                                </div>
                                <p class="text-sm text-amber-700 mt-1">{{ __('Voir les produits avec un niveau de stock bas') }}</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Produits en alerte de stock -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 md:w-2/3">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">{{ __('Alertes de stock') }}</h3>
                            @if(count($lowStockProducts) > 0)
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ count($lowStockProducts) }} {{ __('produits') }}
                                </span>
                            @endif
                        </div>
                        
                        @if(count($lowStockProducts) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Produit') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Stock actuel') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Seuil d\'alerte') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Catégorie') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($lowStockProducts as $product)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $product->name }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $product->sku }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="{{ $product->stock_quantity <= 0 ? 'text-red-600 font-bold' : 'text-amber-600' }}">
                                                        {{ $product->stock_quantity }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $product->stock_alert_threshold }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $product->category ? $product->category->name : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                        {{ __('Voir') }}
                                                    </a>
                                                    <a href="{{ route('inventory.adjustment') }}?product_id={{ $product->id }}" class="text-green-600 hover:text-green-900">
                                                        {{ __('Ajuster') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-green-50 rounded-lg p-4 text-green-800">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <p>{{ __('Tous les produits sont à des niveaux de stock acceptables.') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Derniers mouvements de stock -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Derniers mouvements de stock') }}</h3>
                        <a href="{{ route('inventory.movements') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            {{ __('Voir tout l\'historique') }} →
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Produit') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Quantité') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Référence') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Par') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentMovements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $movement->product ? $movement->product->name : 'Produit inconnu' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $movement->type == 'entrée' ? 'bg-green-100 text-green-800' : 
                                                   ($movement->type == 'sortie' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                                            {{ $movement->type == 'entrée' ? 'text-green-600' : 
                                               ($movement->type == 'sortie' ? 'text-red-600' : 'text-blue-600') }}">
                                            {{ $movement->type == 'entrée' ? '+' : ($movement->type == 'sortie' ? '-' : '') }}{{ abs($movement->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->reference ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->user ? $movement->user->name : 'Système' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ __('Aucun mouvement de stock récent') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $recentMovements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 