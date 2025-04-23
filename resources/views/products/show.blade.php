<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->type == 'service' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $product->type == 'service' ? 'Service' : 'Produit physique' }}
                </span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                    <i class="bi bi-pencil mr-1"></i> {{ __('Modifier') }}
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-sm rounded-md hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-1"></i> {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Carte d'informations principales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Informations générales -->
                        <div class="space-y-4">
                            <h3 class="font-medium text-gray-900 text-lg">{{ __('Informations produit') }}</h3>

                            <div>
                                <p class="text-sm text-gray-500">{{ __('Prix de vente') }}</p>
                                <p class="text-lg font-bold">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</p>
                            </div>

                            @if($product->description)
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Description') }}</p>
                                <p>{{ $product->description }}</p>
                            </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Référence/SKU') }}</p>
                                    <p>{{ $product->sku ?: __('Non définie') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Catégorie') }}</p>
                                    <p>{{ $product->category ? $product->category->name : __('Non catégorisé') }}</p>
                                </div>
                            </div>
                            
                            @if($product->type === 'physical')
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500">{{ __('Disponible pour le troc') }}</p>
                                <div class="flex items-center mt-1">
                                    @if($product->is_barterable)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ __('Oui') }}</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ __('Non') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Statistiques -->
                        <div class="space-y-4">
                            <h3 class="font-medium text-gray-900 text-lg">{{ __('Statistiques') }}</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Quantité vendue') }}</p>
                                    <p class="text-lg font-semibold text-indigo-700">{{ $stats['total_quantity'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Chiffre d\'affaires') }}</p>
                                    <p class="text-lg font-semibold text-indigo-700">{{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Prix moyen') }}</p>
                                    <p>{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Utilisé dans') }}</p>
                                    <p>{{ $stats['usage_count'] }} factures</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Première utilisation') }}</p>
                                    <p>{{ $stats['first_use'] ? $stats['first_use']->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Dernière utilisation') }}</p>
                                    <p>{{ $stats['last_use'] ? $stats['last_use']->format('d/m/Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock (pour produits physiques) ou message (pour services) -->
                        <div class="space-y-4">
                            @if($product->type != 'service')
                                <h3 class="font-medium text-gray-900 text-lg">{{ __('Informations de stock') }}</h3>

                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <p class="text-sm text-gray-500">{{ __('Stock actuel') }}</p>
                                        <p class="text-2xl font-bold {{ $product->isOutOfStock() ? 'text-red-600' : ($product->isLowStock() ? 'text-amber-600' : 'text-green-600') }}">
                                            {{ $product->stock_quantity }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('Seuil d\'alerte') }}</p>
                                        <p>{{ $product->stock_alert_threshold ?: '-' }}</p>
                                    </div>
                                </div>

                                @if($product->cost_price)
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('Prix d\'achat') }}</p>
                                        <p>{{ number_format($product->cost_price, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('Marge') }}</p>
                                        <p class="{{ $product->getProfitMargin() > 20 ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ number_format($product->getProfitMargin(), 1) }}%
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <div class="mt-2">
                                    <a href="{{ route('inventory.adjustment') }}?product_id={{ $product->id }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                        <i class="bi bi-arrow-repeat mr-1"></i> {{ __('Ajuster le stock') }}
                                    </a>
                                </div>
                            @else
                                <div class="bg-purple-50 rounded-md p-4 border border-purple-200">
                                    <h3 class="font-medium text-purple-800 text-lg mb-2">{{ __('Service') }}</h3>
                                    <p class="text-purple-700 text-sm">
                                        {{ __('Ce service n\'est pas suivi en stock. Vous pouvez le facturer sans limite de quantité.') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglets pour alterner entre les deux sections -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex">
                        <button id="tab-prices" class="tab-button py-3 px-4 border-b-2 border-indigo-500 text-indigo-600 font-medium">
                            {{ __('Historique des prix') }} <span class="ml-2 bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full text-xs font-medium">{{ count($priceHistory) }}</span>
                        </button>
                        <button id="tab-invoices" class="tab-button py-3 px-4 border-b-2 border-transparent text-gray-500 hover:border-gray-300 font-medium">
                            {{ __('Factures') }} <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium">{{ $invoices->count() }}</span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Historique des prix - visible par défaut -->
            <div id="prices-content" class="tab-content bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="font-medium text-gray-900 text-lg">{{ __('Historique des prix utilisés') }}</h3>

                        @if(count($priceHistory) > 0)
                        <div class="flex items-center space-x-2 text-sm">
                            <span class="text-gray-500">{{ __('Prix par défaut') }}: <span class="font-medium">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</span></span>
                        </div>
                        @endif
                    </div>

                    @if(count($priceHistory) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($priceHistory as $price)
                            @php
                                $defaultPrice = $product->default_price ?: 1;
                                $priceDiff = $price->price - $product->default_price;
                                $pricePercent = ($priceDiff / $defaultPrice) * 100;
                                $isDefaultPrice = $price->price == $product->default_price;
                                $usageCount = property_exists($price, 'usage_count') ? $price->usage_count : 1;
                            @endphp
                            <div class="price-card border rounded-md {{ $isDefaultPrice ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200' }}" data-price="{{ $price->price }}">
                                <div class="p-4">
                                    <div class="flex justify-between">
                                        <div>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ number_format($price->price, 0, ',', ' ') }} FCFA
                                            </div>
                                            <div class="mt-1 flex items-center">
                                                <span class="text-sm text-gray-500">{{ $usageCount }} facture(s)</span>

                                                @if($priceDiff != 0)
                                                <span class="ml-2 text-sm {{ $priceDiff > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $priceDiff > 0 ? '+' : '' }}{{ number_format($pricePercent, 1) }}%
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            @if($isDefaultPrice)
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-md">Par défaut</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 flex justify-between">
                                    <button
                                        onclick="showInvoicesByPrice('{{ $price->price }}')"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center"
                                        title="Voir les factures utilisant ce prix">
                                        <i class="bi bi-filter mr-1"></i> Voir factures
                                    </button>

                                    @if(!$isDefaultPrice)
                                    <a href="{{ route('products.edit', $product) }}?set_price={{ $price->price }}"
                                        class="text-xs text-gray-600 hover:text-gray-800"
                                        title="Définir comme prix par défaut">
                                        Définir par défaut
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <p>{{ __('Aucun historique de prix disponible') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Liste des factures - caché par défaut -->
            <div id="invoices-content" class="tab-content bg-white shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-5">
                    <div class="mb-4 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInvoice" placeholder="Rechercher par référence, client ou date..." class="pl-10 w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            @if($priceHistory && $priceHistory->count() > 0)
                            <div class="flex items-center">
                                <select id="priceFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Tous les prix</option>
                                    @foreach($priceHistory as $price)
                                        <option value="{{ $price->price }}">
                                            {{ number_format($price->price, 0, ',', ' ') }} FCFA
                                            ({{ $usageCount }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="flex items-center">
                                <select id="pageSizeFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="10">10 par page</option>
                                    <option value="25">25 par page</option>
                                    <option value="50">50 par page</option>
                                    <option value="all">Tout</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($invoices->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p>{{ __('Aucune facture ne contient ce produit pour le moment.') }}</p>
                        </div>
                    @else
                        <div id="noInvoiceResults" class="text-gray-500 py-4 hidden">
                            <p>{{ __('Aucune facture ne correspond à votre recherche.') }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="reference">
                                            {{ __('Référence') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="client">
                                            {{ __('Client') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="date">
                                            {{ __('Date') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="quantity">
                                            {{ __('Qté') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="price">
                                            {{ __('Prix unitaire') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer" data-sort="status">
                                            {{ __('Statut') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                        </th>
                                        <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                    @foreach($invoices as $invoice)
                                        <tr class="invoice-row hover:bg-gray-50"
                                            data-reference="{{ strtolower($invoice->reference) }}"
                                            data-client="{{ strtolower($invoice->client->name) }}"
                                            data-date="{{ $invoice->date->format('d/m/Y') }}"
                                            data-date-sort="{{ $invoice->date->format('Y-m-d') }}"
                                            data-price="{{ $invoice->pivot->price }}"
                                            data-quantity="{{ $invoice->pivot->quantity }}"
                                            data-total="{{ $invoice->pivot->total }}"
                                            data-status="{{ strtolower($invoice->status) }}">
                                            <td class="px-4 py-3 border-b border-gray-200">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ $invoice->reference }}</a>
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm">
                                                {{ $invoice->client->name }}
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm">
                                                {{ $invoice->date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm">
                                                {{ $invoice->pivot->quantity }}
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm font-medium">
                                                {{ number_format($invoice->pivot->price, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm">
                                                <span class="px-2 py-1 text-xs rounded-full {{
                                                    $invoice->status == 'paid' ? 'bg-green-100 text-green-800' :
                                                    ($invoice->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-red-100 text-red-800')
                                                }}">{{ $invoice->status }}</span>
                                            </td>
                                            <td class="px-4 py-3 border-b border-gray-200 text-sm text-right">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Voir') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination client-side simplifiée -->
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <span id="showing-entries" class="text-sm text-gray-600">
                                    <span id="showing-start">1</span>-<span id="showing-end">10</span> sur <span id="total-entries">{{ $invoices->count() }}</span>
                                </span>
                            </div>
                            <div class="pagination-controls flex space-x-1">
                                <button id="prev-page" class="px-3 py-1 rounded border border-gray-300 text-sm text-gray-700 hover:bg-indigo-50 disabled:opacity-50" disabled>
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <div id="pagination-numbers" class="flex space-x-1">
                                    <!-- Les numéros de page seront générés dynamiquement ici -->
                                </div>
                                <button id="next-page" class="px-3 py-1 rounded border border-gray-300 text-sm text-gray-700 hover:bg-indigo-50">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section des trocs associés -->
            @if($product->type === 'physical' && isset($barterItems) && $barterItems->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <h3 class="font-medium text-gray-900 text-xl mb-4">{{ __('Trocs associés') }}</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Client') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantité') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($barterItems as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('barters.show', $item->barter) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $item->barter->reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->barter->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->barter->client->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($item->type === 'given')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ __('Donné par client') }}</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">{{ __('Reçu par client') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ][$item->barter->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ __($item->barter->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('barters.show', $item->barter) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Voir') }}</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Désactiver tous les boutons et contenus
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-indigo-500', 'text-indigo-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });

                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Activer l'onglet cliqué
                    button.classList.remove('border-transparent', 'text-gray-500');
                    button.classList.add('border-indigo-500', 'text-indigo-600');

                    // Afficher le contenu correspondant
                    const contentId = button.id.replace('tab-', '') + '-content';
                    document.getElementById(contentId).classList.remove('hidden');
                });
            });

            // Fonctionnalité pour afficher les factures par prix
            window.showInvoicesByPrice = function(price) {
                // Passer à l'onglet factures
                document.getElementById('tab-invoices').click();

                // Définir le filtre de prix
                const priceFilter = document.getElementById('priceFilter');
                if (priceFilter) {
                    priceFilter.value = price;
                    filterInvoices();
                }
            };

            // Filtrage des factures
            const searchInput = document.getElementById('searchInvoice');
            const priceFilter = document.getElementById('priceFilter');

            if (searchInput) {
                searchInput.addEventListener('input', filterInvoices);
            }

            if (priceFilter) {
                priceFilter.addEventListener('change', filterInvoices);
            }

            function filterInvoices() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedPrice = priceFilter ? priceFilter.value : '';

                // Filtrer les factures
                const rows = Array.from(document.querySelectorAll('.invoice-row'));

                filteredRows = rows.filter(row => {
                    const reference = row.dataset.reference;
                    const client = row.dataset.client;
                    const date = row.dataset.date;
                    const price = row.dataset.price;

                    const matchesSearch = !searchTerm ||
                        reference.includes(searchTerm) ||
                        client.includes(searchTerm) ||
                        date.includes(searchTerm);

                    const matchesPrice = !selectedPrice || price === selectedPrice;

                    return matchesSearch && matchesPrice;
                });

                // Mise à jour du message d'aucun résultat
                const noResults = document.getElementById('noInvoiceResults');
                if (filteredRows.length === 0 && rows.length > 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }

                // Appliquer le tri actuel
                applySorting();

                // Réinitialiser la pagination
                currentPage = 1;
                updatePagination();
            }

            // Système de pagination côté client
            let currentPage = 1;
            let pageSize = 10;
            let filteredRows = [];
            let sortField = 'date';
            let sortDirection = 'desc';

            // Initialiser la pagination
            function setupClientPagination() {
                // Initialiser le tableau filtré
                filteredRows = Array.from(document.querySelectorAll('.invoice-row'));

                // Configurer les contrôles de pagination
                document.getElementById('prev-page').addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });

                document.getElementById('next-page').addEventListener('click', () => {
                    const maxPage = Math.ceil(filteredRows.length / pageSize);
                    if (currentPage < maxPage) {
                        currentPage++;
                        updatePagination();
                    }
                });

                // Gérer la taille de page
                const pageSizeFilter = document.getElementById('pageSizeFilter');
                if (pageSizeFilter) {
                    pageSizeFilter.addEventListener('change', function() {
                        if (this.value === 'all') {
                            pageSize = filteredRows.length;
                        } else {
                            pageSize = parseInt(this.value);
                        }
                        currentPage = 1;
                        updatePagination();
                    });
                }

                // Configurer le tri des colonnes
                document.querySelectorAll('th[data-sort]').forEach(header => {
                    header.addEventListener('click', () => {
                        const field = header.dataset.sort;

                        if (field === sortField) {
                            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                        } else {
                            sortField = field;
                            sortDirection = 'asc';
                        }

                        // Mettre à jour l'icône de tri
                        document.querySelectorAll('th[data-sort] i').forEach(icon => {
                            icon.className = 'bi bi-arrow-down-up ml-1';
                        });

                        const icon = header.querySelector('i');
                        if (icon) {
                            icon.className = `bi bi-arrow-${sortDirection === 'asc' ? 'up' : 'down'} ml-1`;
                        }

                        applySorting();
                        updatePagination();
                    });
                });

                // Tri initial par date (desc)
                const dateHeader = document.querySelector('th[data-sort="date"]');
                if (dateHeader) {
                    const icon = dateHeader.querySelector('i');
                    if (icon) icon.className = 'bi bi-arrow-down ml-1';
                }

                applySorting();
                updatePagination();
            }

            // Fonction de tri
            function applySorting() {
                filteredRows.sort((a, b) => {
                    let valueA = a.dataset[sortField];
                    let valueB = b.dataset[sortField];

                    // Tri spécial pour les dates
                    if (sortField === 'date' && a.dataset['dateSort'] && b.dataset['dateSort']) {
                        valueA = a.dataset['dateSort'];
                        valueB = b.dataset['dateSort'];
                    }

                    // Convertir en nombres si nécessaire
                    if (['price', 'quantity', 'total'].includes(sortField)) {
                        valueA = parseFloat(valueA) || 0;
                        valueB = parseFloat(valueB) || 0;
                    }

                    // Comparaison
                    if (valueA < valueB) return sortDirection === 'asc' ? -1 : 1;
                    if (valueA > valueB) return sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            // Mettre à jour l'affichage de la pagination
            function updatePagination() {
                const paginationNumbers = document.getElementById('pagination-numbers');
                const prevButton = document.getElementById('prev-page');
                const nextButton = document.getElementById('next-page');
                const showingStart = document.getElementById('showing-start');
                const showingEnd = document.getElementById('showing-end');
                const totalEntries = document.getElementById('total-entries');

                // Masquer toutes les lignes
                document.querySelectorAll('.invoice-row').forEach(row => {
                    row.style.display = 'none';
                });

                // Calculer les indices
                const startIndex = (currentPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, filteredRows.length);

                // Afficher les lignes de la page actuelle
                for (let i = startIndex; i < endIndex; i++) {
                    filteredRows[i].style.display = '';
                }

                // Mettre à jour l'info de pagination
                if (filteredRows.length > 0) {
                    showingStart.textContent = startIndex + 1;
                    showingEnd.textContent = endIndex;
                    totalEntries.textContent = filteredRows.length;
                } else {
                    showingStart.textContent = '0';
                    showingEnd.textContent = '0';
                    totalEntries.textContent = '0';
                }

                // État des boutons
                prevButton.disabled = currentPage === 1;
                nextButton.disabled = endIndex >= filteredRows.length;

                // Générer les boutons de page
                const maxPage = Math.ceil(filteredRows.length / pageSize);
                paginationNumbers.innerHTML = '';

                if (maxPage <= 5) {
                    // Afficher tous les numéros de page
                    for (let i = 1; i <= maxPage; i++) {
                        addPageButton(i);
                    }
                } else {
                    // Version simplifiée avec ellipsis
                    if (currentPage <= 3) {
                        for (let i = 1; i <= 3; i++) addPageButton(i);
                        addEllipsis();
                        addPageButton(maxPage);
                    } else if (currentPage >= maxPage - 2) {
                        addPageButton(1);
                        addEllipsis();
                        for (let i = maxPage - 2; i <= maxPage; i++) addPageButton(i);
                    } else {
                        addPageButton(1);
                        addEllipsis();
                        addPageButton(currentPage - 1);
                        addPageButton(currentPage);
                        addPageButton(currentPage + 1);
                        addEllipsis();
                        addPageButton(maxPage);
                    }
                }

                function addPageButton(page) {
                    const button = document.createElement('button');
                    button.classList.add('px-3', 'py-1', 'rounded', 'border', 'text-sm');

                    if (page === currentPage) {
                        button.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                    } else {
                        button.classList.add('border-gray-300', 'text-gray-700', 'hover:bg-indigo-50');
                    }

                    button.textContent = page;
                    button.addEventListener('click', () => {
                        currentPage = page;
                        updatePagination();
                    });

                    paginationNumbers.appendChild(button);
                }

                function addEllipsis() {
                    const span = document.createElement('span');
                    span.classList.add('px-2', 'py-1', 'text-sm', 'text-gray-500');
                    span.textContent = '...';
                    paginationNumbers.appendChild(span);
                }
            }

            // Initialiser la pagination
            setupClientPagination();
        });
    </script>
</x-app-layout>
