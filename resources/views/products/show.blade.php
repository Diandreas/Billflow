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
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                   title="{{ __('Modifier les informations de ce produit') }}">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50"
                   title="{{ __('Retourner à la liste des produits') }}">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Titre et informations principales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                        <div class="flex space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $product->type == 'service' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}"
                                  title="{{ $product->type == 'service' ? __('Service ne nécessitant pas de suivi de stock') : __('Produit physique géré en stock') }}">
                                <i class="bi {{ $product->type == 'service' ? 'bi-gear' : 'bi-box' }} mr-1"></i>
                                {{ $product->type == 'service' ? __('Service') : __('Produit physique') }}
                            </span>
                            
                            @if($product->type != 'service')
                                @if($product->isOutOfStock())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"
                                          title="{{ __('Ce produit est actuellement en rupture de stock.') }}">
                                        <i class="bi bi-exclamation-triangle mr-1"></i>
                                        {{ __('Épuisé') }}
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"
                                          title="{{ __('Le stock de ce produit est inférieur au seuil d\'alerte.') }}">
                                        <i class="bi bi-exclamation-circle mr-1"></i>
                                        {{ __('Stock faible') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                                          title="{{ __('Ce produit est disponible en stock.') }}">
                                        <i class="bi bi-check-circle mr-1"></i>
                                        {{ __('En stock') }}
                                    </span>
                                @endif
                            @endif
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                                  title="{{ __('Statut commercial du produit') }}">
                                <i class="bi bi-tag mr-1"></i>
                                {{ $product->status ?? 'Actif' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">{{ __('Description') }}</p>
                            <p class="text-gray-900">{{ $product->description ?: __('Aucune description') }}</p>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-1">{{ __('Prix de vente') }}</p>
                                <p class="text-xl font-semibold text-gray-900" title="{{ __('Prix de vente par défaut de ce produit') }}">
                                    {{ number_format($product->default_price, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-1">{{ __('Référence/SKU') }}</p>
                                <p class="text-gray-900">{{ $product->sku ?: __('Non définie') }}</p>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-1">{{ __('Catégorie') }}</p>
                                <p class="text-gray-900">
                                    @if($product->category)
                                        <span class="inline-flex items-center" title="{{ __('Catégorie associée à ce produit') }}">
                                            <i class="bi bi-folder mr-1 text-indigo-500"></i>
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        {{ __('Non catégorisé') }}
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-1">{{ __('Catégorie comptable') }}</p>
                                <p class="text-gray-900">
                                    @if($product->accounting_category)
                                        <span class="inline-flex items-center" title="{{ __('Code comptable associé pour l\'export') }}">
                                            <i class="bi bi-calculator mr-1 text-indigo-500"></i>
                                            {{ $product->accounting_category }}
                                        </span>
                                    @else
                                        {{ __('Non définie') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($product->type != 'service')
                        <div>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="font-medium text-lg mb-2">{{ __('Informations de stock') }}</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('Stock actuel') }}</p>
                                        <div class="flex items-center">
                                            <p class="text-xl font-semibold {{ $product->isLowStock() ? 'text-amber-600' : ($product->isOutOfStock() ? 'text-red-600' : 'text-green-600') }}"
                                               title="{{ __('Quantité actuellement disponible en stock') }}">
                                                {{ $product->stock_quantity }}
                                            </p>
                                            @if($product->stock_alert_threshold && $product->stock_quantity <= $product->stock_alert_threshold)
                                                <span class="ml-2 flex-shrink-0 inline-block px-2 py-0.5 text-xs font-medium rounded-full {{ $product->isOutOfStock() ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}"
                                                      title="{{ __('Le stock est au niveau ou en dessous du seuil d\'alerte') }}">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                </span>
                                            @endif
                                        </div>

                                        @if($product->stock_alert_threshold && $product->stock_quantity <= $product->stock_alert_threshold)
                                            <div class="mt-1" title="{{ __('Visualisation du niveau de stock par rapport au seuil d\'alerte') }}">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $product->isOutOfStock() ? 'bg-red-500' : 'bg-amber-500' }}" style="width: {{ min(100, ($product->stock_quantity / $product->stock_alert_threshold) * 100) }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('Seuil d\'alerte') }}</p>
                                        <p class="text-gray-900" title="{{ __('Niveau de stock minimum avant d\'être alerté') }}">
                                            {{ $product->stock_alert_threshold ?: __('Non défini') }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('Prix d\'achat') }}</p>
                                        <p class="text-gray-900" title="{{ __('Prix d\'achat moyen ou dernier prix d\'achat') }}">
                                            {{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : __('Non défini') }}
                                        </p>
                                    </div>
                                    
                                    @if($product->cost_price && $product->cost_price > 0)
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">{{ __('Marge') }}</p>
                                        <p class="flex items-center {{ $product->getProfitMargin() > 20 ? 'text-green-600' : 'text-amber-600' }} font-medium"
                                           title="{{ __('Marge bénéficiaire brute (Prix vente - Prix d\'achat) / Prix vente') }}">
                                            <i class="bi {{ $product->getProfitMargin() > 20 ? 'bi-arrow-up' : 'bi-arrow-down' }} mr-1"></i>
                                            {{ number_format($product->getProfitMargin(), 2) }}%
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('inventory.adjustment') }}?product_id={{ $product->id }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                       title="{{ __('Effectuer une entrée, sortie ou correction de stock') }}">
                                        <i class="bi bi-pencil-square mr-1"></i>
                                        {{ __('Ajuster le stock') }}
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h3 class="font-medium text-lg mb-2">{{ __('Derniers mouvements') }}</h3>
                                <div class="overflow-x-auto">
                                    @if($product->inventoryMovements && $product->inventoryMovements->count() > 0)
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantité') }}</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Réf.') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($product->inventoryMovements->take(5) as $movement)
                                                    <tr>
                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="px-3 py-2 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $movement->type == 'entrée' ? 'bg-green-100 text-green-800' : ($movement->type == 'sortie' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}"
                                                                  title="{{ $movement->type == 'entrée' ? __('Entrée en stock') : ($movement->type == 'sortie' ? __('Sortie de stock (vente, etc.)') : __('Ajustement de stock')) }}">
                                                                <i class="bi {{ $movement->type == 'entrée' ? 'bi-box-arrow-in-down' : ($movement->type == 'sortie' ? 'bi-box-arrow-up' : 'bi-arrow-left-right') }} mr-1"></i>
                                                                {{ ucfirst($movement->type) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-sm {{ $movement->type == 'entrée' ? 'text-green-600' : ($movement->type == 'sortie' ? 'text-red-600' : 'text-blue-600') }}">
                                                            {{ $movement->type == 'entrée' ? '+' : ($movement->type == 'sortie' ? '-' : '') }}{{ abs($movement->quantity) }}
                                                        </td>
                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $movement->reference ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="mt-2 text-right">
                                            <a href="{{ route('inventory.movements') }}?product_id={{ $product->id }}" class="text-sm text-indigo-600 hover:text-indigo-900 inline-flex items-center"
                                               title="{{ __('Voir l\'historique complet des mouvements de stock') }}">
                                                {{ __('Voir tous les mouvements') }} 
                                                <i class="bi bi-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-sm">{{ __('Aucun mouvement de stock enregistré') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <div>
                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                <h3 class="font-medium text-lg mb-2 text-purple-800">{{ __('Informations sur le service') }}</h3>
                                <p class="text-purple-800">
                                    {{ __('Ce service n\'est pas suivi en stock. Vous pouvez le facturer sans limite de quantité.') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

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
                                    <p class="mt-1" title="{{ $product->created_at->diffForHumans() }}">{{ $product->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Statistiques de ventes') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Utilisé dans') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600" title="{{ __('Nombre total de factures différentes où ce produit apparaît') }}">
                                        {{ $stats['usage_count'] }} factures
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Quantité totale vendue') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600" title="{{ __('Somme des quantités vendues sur toutes les factures') }}">
                                        {{ $stats['total_quantity'] }} unités
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires total') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600" title="{{ __('Montant total généré par la vente de ce produit') }}">
                                        {{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Historique d\'utilisation') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Prix moyen utilisé') }}</p>
                                    <p class="mt-1" title="{{ __('Prix moyen auquel ce produit a été facturé') }}">
                                        {{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Première utilisation') }}</p>
                                    <p class="mt-1" title="{{ $stats['first_use'] ? $stats['first_use']->diffForHumans() : '' }}">
                                        {{ $stats['first_use'] ? $stats['first_use']->format('d/m/Y') : 'Jamais utilisé' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Dernière utilisation') }}</p>
                                    <p class="mt-1" title="{{ $stats['last_use'] ? $stats['last_use']->diffForHumans() : '' }}">
                                        {{ $stats['last_use'] ? $stats['last_use']->format('d/m/Y') : 'Jamais utilisé' }}
                                    </p>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Factures contenant ce produit</h3>
                    
                    <div class="mb-4">
                        <input type="text" id="searchInvoice" placeholder="Rechercher par référence, client ou date..." class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    @if($invoices->isEmpty())
                        <p class="text-gray-500">Aucune facture ne contient ce produit pour le moment.</p>
                    @else
                        <div id="noInvoiceResults" class="text-gray-500 py-4 hidden">
                            <p>Aucune facture ne correspond à votre recherche.</p>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Référence
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Client
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Quantité
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr class="invoice-row" 
                                           data-reference="{{ strtolower($invoice->reference) }}" 
                                           data-client="{{ strtolower($invoice->client->name) }}" 
                                           data-date="{{ $invoice->date->format('d/m/Y') }}">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">{{ $invoice->reference }}</a>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->client->name }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->status }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->pivot->quantity }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ number_format($invoice->pivot->total, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Voir') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    <script>
        // Graphique des ventes
        document.addEventListener('DOMContentLoaded', function() {
            // Code existant pour le graphique (si présent)
            
            // Fonctionnalité de recherche pour les factures
            const searchInput = document.getElementById('searchInvoice');
            const invoiceRows = document.querySelectorAll('.invoice-row');
            const noInvoiceResults = document.getElementById('noInvoiceResults');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;
                    
                    invoiceRows.forEach(row => {
                        const reference = row.dataset.reference;
                        const client = row.dataset.client;
                        const date = row.dataset.date;
                        
                        if (reference.includes(searchTerm) || client.includes(searchTerm) || date.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Afficher un message si aucun résultat
                    if (visibleCount === 0 && invoiceRows.length > 0) {
                        noInvoiceResults.classList.remove('hidden');
                    } else {
                        noInvoiceResults.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
