<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ $product->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails du produit et statistiques') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('products.edit', $product) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Information du produit -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Information produit') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nom') }}</p>
                                    <p class="mt-1">{{ $product->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Description') }}</p>
                                    <p class="mt-1">{{ $product->description ?: 'Pas de description' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Prix par défaut') }}</p>
                                    <p class="mt-1">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Date de création') }}</p>
                                    <p class="mt-1">{{ $product->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Statistiques de ventes') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Utilisé dans') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['usage_count'] }} factures</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Quantité totale vendue') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['total_quantity'] }} unités</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires total') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Historique d\'utilisation') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Prix moyen utilisé') }}</p>
                                    <p class="mt-1">{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Première utilisation') }}</p>
                                    <p class="mt-1">{{ $stats['first_use'] ? $stats['first_use']->format('d/m/Y') : 'Jamais utilisé' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Dernière utilisation') }}</p>
                                    <p class="mt-1">{{ $stats['last_use'] ? $stats['last_use']->format('d/m/Y') : 'Jamais utilisé' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des prix -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Historique des prix') }}</h3>
                    
                    @if(count($priceHistory) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Prix utilisé') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nombre d\'utilisations') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($priceHistory as $price)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($price->unit_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $price->usage_count }} fois
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>{{ __('Aucun historique de prix disponible') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Factures associées -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Factures utilisant ce produit') }}</h3>
                    
                    @if(count($product->bills) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Référence') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Client') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Quantité') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Prix unitaire') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Total') }}
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->bills as $bill)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $bill->reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->client->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->pivot->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($bill->pivot->unit_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">
                                        {{ number_format($bill->pivot->total, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Voir') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>{{ __('Ce produit n\'a pas encore été utilisé dans des factures') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush
</x-app-layout>
