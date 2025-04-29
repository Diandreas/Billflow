<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $product->name }}
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->type == 'service' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' }}">
                    {{ $product->type == 'service' ? 'Service' : 'Produit physique' }}
                </span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600 text-white text-sm rounded-md">
                    <i class="bi bi-pencil mr-1"></i> {{ __('Modifier') }}
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sm rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200">
                    <i class="bi bi-arrow-left mr-1"></i> {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Carte d'informations principales -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Informations générales -->
                        <div class="space-y-4">
                            <h3 class="font-medium text-gray-900 dark:text-gray-100 text-lg">{{ __('Informations produit') }}</h3>

                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Prix de vente') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</p>
                            </div>

                            @if($product->description)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $product->description }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Référence/SKU') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $product->sku ?: __('Non définie') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Catégorie') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $product->category ? $product->category->name : __('Non catégorisé') }}</p>
                                </div>
                            </div>

                            @if($product->type === 'physical')
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Disponible pour le troc') }}</p>
                                    <div class="flex items-center mt-1">
                                        @if($product->is_barterable)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">{{ __('Oui') }}</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ __('Non') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Statistiques -->
                        <div class="space-y-4">
                            <h3 class="font-medium text-gray-900 dark:text-gray-100 text-lg">{{ __('Statistiques') }}</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Quantité vendue') }}</p>
                                    <p class="text-lg font-semibold text-indigo-700 dark:text-indigo-400">{{ $stats['total_quantity'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Chiffre d\'affaires') }}</p>
                                    <p class="text-lg font-semibold text-indigo-700 dark:text-indigo-400">{{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Prix moyen') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Utilisé dans') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $stats['usage_count'] }} factures</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Première utilisation') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $stats['first_use'] ? $stats['first_use']->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Dernière utilisation') }}</p>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $stats['last_use'] ? $stats['last_use']->format('d/m/Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock (pour produits physiques) ou message (pour services) -->
                        <div class="space-y-4">
                            @if($product->type != 'service')
                                <h3 class="font-medium text-gray-900 dark:text-gray-100 text-lg">{{ __('Informations de stock') }}</h3>

                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Stock actuel') }}</p>
                                        <p class="text-2xl font-bold {{ $product->isOutOfStock() ? 'text-red-600 dark:text-red-400' : ($product->isLowStock() ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400') }}">
                                            {{ $product->stock_quantity }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Seuil d\'alerte') }}</p>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $product->stock_alert_threshold ?: '-' }}</p>
                                    </div>
                                </div>

                                @if($product->cost_price)
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Prix d\'achat') }}</p>
                                            <p class="text-gray-900 dark:text-gray-100">{{ number_format($product->cost_price, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Marge') }}</p>
                                            <p class="{{ $product->getProfitMargin() > 20 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                {{ number_format($product->getProfitMargin(), 1) }}%
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-2">
                                    <a href="{{ route('inventory.adjustment') }}?product_id={{ $product->id }}" class="inline-flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                        <i class="bi bi-arrow-repeat mr-1"></i> {{ __('Ajuster le stock') }}
                                    </a>
                                </div>
                            @else
                                <div class="bg-purple-50 dark:bg-purple-900 rounded-md p-4 border border-purple-200 dark:border-purple-800">
                                    <h3 class="font-medium text-purple-800 dark:text-purple-200 text-lg mb-2">{{ __('Service') }}</h3>
                                    <p class="text-purple-700 dark:text-purple-300 text-sm">
                                        {{ __('Ce service n\'est pas suivi en stock. Vous pouvez le facturer sans limite de quantité.') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section des statistiques de troc - uniquement si produit physique ET en stock -->
            @if($product->type === 'physical' && $product->is_barterable))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 text-xl mb-4">{{ __('Statistiques des trocs') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Nombre total de trocs -->
                            <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4 border border-purple-100 dark:border-purple-800 flex flex-col">
                                <p class="text-sm text-purple-700 dark:text-purple-300">Total des trocs</p>
                                <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">{{ $barterStats['total_barters'] }}</p>
                                <div class="mt-2 flex items-center text-sm">
                                <span class="flex items-center text-purple-600 dark:text-purple-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414zM9 4a1 1 0 112 0v5.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L9 9.586V4z" />
                                    </svg>
                                    {{ __('Échanges') }}
                                </span>
                                </div>
                            </div>

                            <!-- Entrées vs Sorties -->
                            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 border border-blue-100 dark:border-blue-800 flex flex-col">
                                <p class="text-sm text-blue-700 dark:text-blue-300">Distribution des échanges</p>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <div>
                                        <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('Entrées') }}</p>
                                        <p class="text-xl font-bold text-blue-800 dark:text-blue-200">{{ $barterStats['given_barters'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('Sorties') }}</p>
                                        <p class="text-xl font-bold text-blue-800 dark:text-blue-200">{{ $barterStats['received_barters'] }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 relative pt-1">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-200 dark:bg-blue-800">
                                        @if($barterStats['total_barters'] > 0)
                                            <div style="width:{{ ($barterStats['given_barters'] / $barterStats['total_barters']) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 dark:bg-blue-500"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quantité totale échangée -->
                            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4 border border-green-100 dark:border-green-800 flex flex-col">
                                <p class="text-sm text-green-700 dark:text-green-300">Quantité échangée</p>
                                <p class="text-2xl font-bold text-green-800 dark:text-green-200">{{ $barterStats['total_quantity'] }}</p>
                                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                                    <p>{{ __('Valeur moyenne:') }} {{ number_format($barterStats['average_value'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>

                            <!-- Valeur totale échangée -->
                            <div class="bg-amber-50 dark:bg-amber-900 rounded-lg p-4 border border-amber-100 dark:border-amber-800 flex flex-col">
                                <p class="text-sm text-amber-700 dark:text-amber-300">Valeur totale échangée</p>
                                <p class="text-2xl font-bold text-amber-800 dark:text-amber-200">{{ number_format($barterStats['total_value'], 0, ',', ' ') }} FCFA</p>
                                <div class="mt-2 text-sm text-amber-600 dark:text-amber-400">
                                    <p>{{ __('Représente') }} {{ $product->sales > 0 ? number_format(($barterStats['total_value'] / ($product->sales + $barterStats['total_value'])) * 100, 1) : '0' }}% {{ __('des revenus') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($barterStats['total_barters'] > 0)
                            <div class="mt-4">
                                <a href="{{ route('barters.index', ['product_id' => $product->id]) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Voir tous les trocs avec ce produit') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Onglets pour alterner entre les deux sections -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex">
                        <button id="tab-prices" class="tab-button py-3 px-4 border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 font-medium">
                            {{ __('Historique des prix') }} <span class="ml-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-full text-xs font-medium">{{ count($priceHistory) }}</span>
                        </button>
                        <button id="tab-invoices" class="tab-button py-3 px-4 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 font-medium">
                            {{ __('Factures') }} <span class="ml-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full text-xs font-medium">{{ $invoices->count() }}</span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Historique des prix - visible par défaut -->
            <div id="prices-content" class="tab-content bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-5">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 text-lg">{{ __('Historique des prix utilisés') }}</h3>

                            <div class="flex items-center space-x-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Prix par défaut') }}: <span class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</span></span>
                            <button id="refreshPriceHistory" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 ml-2">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </div>

                    <div id="priceHistoryLoader" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Chargement...') }}</p>
                            </div>

                    <div id="priceHistoryContainer" class="hidden">
                        <!-- Les données seront chargées par JavaScript -->
                    </div>

                    <div id="noPriceHistoryMessage" class="text-center py-6 text-gray-500 dark:text-gray-400 hidden">
                        <p>{{ __('Aucun historique de prix disponible') }}</p>
                    </div>
                </div>
            </div>

            <!-- Prix graphique - template qui sera cloné par JavaScript -->
            <template id="price-card-template">
                <div class="price-card border rounded-md mb-6">
                                    <div class="p-4">
                        <div class="flex justify-between items-start">
                                            <div>
                                <div class="flex items-center">
                                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100 price-value"></div>
                                    <span class="price-badge ml-2 px-2 py-1 text-xs rounded-md hidden"></span>
                                                </div>
                                                <div class="mt-1 flex items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 usage-count"></span>
                                    <span class="price-diff ml-2 text-sm"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200">Quantité totale: <span class="total-quantity"></span></div>
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200">Montant total: <span class="total-amount"></span></div>
                            </div>
                                                </div>
                    </div>
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Première utilisation</div>
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200 first-used"></div>
                                            </div>
                                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Dernière utilisation</div>
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200 last-used"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                        <button class="view-invoices-btn text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center">
                                            <i class="bi bi-filter mr-1"></i> Voir factures
                                        </button>

                        <button class="set-default-btn text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 hidden">
                                                Définir par défaut
                        </button>
                        </div>
                </div>
            </template>

            <!-- Liste des factures - caché par défaut -->
            <div id="invoices-content" class="tab-content bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-5">
                    <div class="mb-4 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInvoice" placeholder="Rechercher par référence, client ou date..." class="pl-10 w-full sm:w-96 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            @if($priceHistory && $priceHistory->count() > 0)
                                <div class="flex items-center">
                                    <select id="priceFilter" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100">
                                        <option value="">Tous les prix</option>
                                        @foreach($priceHistory as $price)
                                            <option value="{{ $price->price }}">
                                                {{ number_format($price->price, 0, ',', ' ') }} FCFA
                                                ({{ $price->usage_count ?? 0 }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="flex items-center">
                                <select id="pageSizeFilter" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="10">10 par page</option>
                                    <option value="25">25 par page</option>
                                    <option value="50">50 par page</option>
                                    <option value="all">Tout</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($invoices->isEmpty())
                        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                            <p>{{ __('Aucune facture ne contient ce produit pour le moment.') }}</p>
                        </div>
                    @else
                        <div id="noInvoiceResults" class="text-gray-500 dark:text-gray-400 py-4 hidden">
                            <p>{{ __('Aucune facture ne correspond à votre recherche.') }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                <tr>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="reference">
                                        {{ __('Référence') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="client">
                                        {{ __('Client') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="date">
                                        {{ __('Date') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="quantity">
                                        {{ __('Qté') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="price">
                                        {{ __('Prix unitaire') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="status">
                                        {{ __('Statut') }} <i class="bi bi-arrow-down-up ml-1"></i>
                                    </th>
                                    <th class="px-4 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-right">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                @foreach($invoices as $invoice)
                                    <tr class="invoice-row hover:bg-gray-50 dark:hover:bg-gray-700"
                                        data-reference="{{ strtolower($invoice->reference) }}"
                                        data-client="{{ strtolower($invoice->client->name) }}"
                                        data-date="{{ $invoice->date->format('d/m/Y') }}"
                                        data-date-sort="{{ $invoice->date->format('Y-m-d') }}"
                                        data-price="{{ $invoice->pivot->price }}"
                                        data-quantity="{{ $invoice->pivot->quantity }}"
                                        data-total="{{ $invoice->pivot->total }}"
                                        data-status="{{ strtolower($invoice->status) }}">
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                            <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">{{ $invoice->reference }}</a>
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $invoice->client->name }}
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $invoice->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $invoice->pivot->quantity }}
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ number_format($invoice->pivot->price, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm">
                                                <span class="px-2 py-1 text-xs rounded-full {{
                                                    $invoice->status == 'paid' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                                                    ($invoice->status == 'pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' :
                                                    'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200')
                                                }}">{{ $invoice->status }}</span>
                                        </td>
                                        <td class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-sm text-right">
                                            <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
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
                                <span id="showing-entries" class="text-sm text-gray-600 dark:text-gray-400">
                                    <span id="showing-start">1</span>-<span id="showing-end">10</span> sur <span id="total-entries">{{ $invoices->count() }}</span>
                                </span>
                            </div>
                            <div class="pagination-controls flex space-x-1">
                                <button id="prev-page" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900 disabled:opacity-50" disabled>
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <div id="pagination-numbers" class="flex space-x-1">
                                    <!-- Les numéros de page seront générés dynamiquement ici -->
                                </div>
                                <button id="next-page" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section des trocs associés - uniquement si produit physique ET en stock -->
            @if($product->type === 'physical' && !$product->isOutOfStock() && isset($barterItems) && $barterItems->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-5">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 text-xl mb-4">{{ __('Trocs associés') }}</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Référence') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Client') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Quantité') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Statut') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($barterItems as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('barters.show', $item->barter) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                {{ $item->barter->reference }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->barter->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->barter->client->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($item->type === 'given')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">{{ __('Donné par client') }}</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">{{ __('Reçu par client') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                                    'completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                                    'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                                ][$item->barter->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ __($item->barter->status) }}
                                        </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('barters.show', $item->barter) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ __('Voir') }}</a>
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const productId = '{{ $product->id }}';
            const defaultPrice = {{ $product->default_price }};
            const priceHistoryContainer = document.getElementById('priceHistoryContainer');
            const priceHistoryLoader = document.getElementById('priceHistoryLoader');
            const noPriceHistoryMessage = document.getElementById('noPriceHistoryMessage');
            
            // Historique des prix préchargé
            const priceHistory = @json($priceHistory);
            
            // Gestion des onglets
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Désactiver tous les boutons et contenus
                    tabButtons.forEach(btn => {
                        btn.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                    });

                    tabContents.forEach(content => content.classList.add('hidden'));

                    // Activer l'onglet cliqué
                    button.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                    button.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');

                    // Afficher le contenu correspondant
                    const tabId = button.id.replace('tab-', '');
                    document.getElementById(`${tabId}-content`).classList.remove('hidden');
                });
            });

            // Afficher directement l'historique des prix
            displayPriceHistory();
            
            // Fonction pour afficher l'historique des prix
            function displayPriceHistory() {
                // Masquer le loader
                priceHistoryLoader.classList.add('hidden');
                
                if (priceHistory.length === 0) {
                    noPriceHistoryMessage.classList.remove('hidden');
                    return;
                }
                
                // Créer une grille pour les cartes
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-1 lg:grid-cols-2 gap-6';
                priceHistoryContainer.appendChild(grid);
                priceHistoryContainer.classList.remove('hidden');
                
                // Afficher chaque prix
                priceHistory.forEach(price => {
                    createPriceCard(price, grid);
                });
            }
            
            // Créer une carte de prix
            function createPriceCard(price, container) {
                const isDefaultPrice = price.is_default;
                const priceDiff = price.price - defaultPrice;
                const pricePercent = defaultPrice ? ((priceDiff / defaultPrice) * 100).toFixed(1) : 0;

                // Cloner le template
                const priceCard = document.getElementById('price-card-template').content.cloneNode(true);
                const card = priceCard.querySelector('.price-card');
                
                // Styliser la carte
                if (isDefaultPrice) {
                    card.classList.add('border-indigo-300', 'dark:border-indigo-700', 'bg-indigo-50', 'dark:bg-indigo-900');
                } else {
                    card.classList.add('border-gray-200', 'dark:border-gray-700');
                }
                
                // Définir les valeurs
                card.dataset.price = price.price;
                card.querySelector('.price-value').textContent = formatPrice(price.price);
                card.querySelector('.usage-count').textContent = `${price.usage_count} facture(s)`;
                card.querySelector('.total-quantity').textContent = formatNumber(price.total_quantity);
                card.querySelector('.total-amount').textContent = formatPrice(price.total_amount);
                
                // Dates d'utilisation
                card.querySelector('.first-used').textContent = price.first_used ? formatDate(price.first_used) : 'N/A';
                card.querySelector('.last-used').textContent = price.last_used ? formatDate(price.last_used) : 'N/A';
                
                // Différence de prix
                const priceDiffElement = card.querySelector('.price-diff');
                if (priceDiff !== 0) {
                    priceDiffElement.textContent = `${priceDiff > 0 ? '+' : ''}${pricePercent}%`;
                    priceDiffElement.classList.add(priceDiff > 0 ? 'text-green-600' : 'text-red-600');
                    priceDiffElement.classList.add(priceDiff > 0 ? 'dark:text-green-400' : 'dark:text-red-400');
                }
                
                // Badge "Par défaut"
                const badgeElement = card.querySelector('.price-badge');
                if (isDefaultPrice) {
                    badgeElement.textContent = 'Par défaut';
                    badgeElement.classList.add('bg-indigo-100', 'dark:bg-indigo-800', 'text-indigo-700', 'dark:text-indigo-300');
                    badgeElement.classList.remove('hidden');
                }
                
                // Bouton "Définir par défaut"
                const setDefaultBtn = card.querySelector('.set-default-btn');
                if (!isDefaultPrice) {
                    setDefaultBtn.classList.remove('hidden');
                    setDefaultBtn.addEventListener('click', function() {
                        window.location.href = `{{ route('products.edit', $product) }}?set_price=${price.price}`;
                    });
                }
                
                // Bouton "Voir factures"
                const viewInvoicesBtn = card.querySelector('.view-invoices-btn');
                viewInvoicesBtn.addEventListener('click', function() {
                    showInvoicesByPrice(price.price);
                });
                
                // Ajouter la carte au conteneur
                container.appendChild(card);
            }

            // Fonction pour afficher les factures selon le prix
            function showInvoicesByPrice(price) {
                // Changer d'onglet
                document.getElementById('tab-invoices').click();
                
                // Filtrer les factures
                const rows = document.querySelectorAll('#invoicesTableBody tr');
                let visible = 0;
                
                rows.forEach(row => {
                    if (parseFloat(row.dataset.price) === parseFloat(price)) {
                        row.classList.remove('hidden');
                        visible++;
                    } else {
                        row.classList.add('hidden');
                    }
                });
                
                // Afficher ou masquer le message "Aucun résultat"
                document.getElementById('noInvoiceResults').classList.toggle('hidden', visible > 0);
                
                // Mettre à jour le filtre
                const priceFilterSelect = document.getElementById('priceFilter');
                if (priceFilterSelect) {
                    Array.from(priceFilterSelect.options).forEach(option => {
                        if (parseFloat(option.value) === parseFloat(price)) {
                            priceFilterSelect.value = option.value;
                        }
                    });
                }
            }

            // Fonctions utilitaires
            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR', {
                    style: 'currency',
                    currency: 'XOF',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(price).replace('XOF', 'FCFA');
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('fr-FR').format(number);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('fr-FR');
            }

            // Initialiser la pagination et le tri
            setupClientPagination();
        });
    </script>
    @endpush
</x-app-layout>
