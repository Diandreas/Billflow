@extends('layouts.app')

@section('page_name', 'dashboard')

@section('content')

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Tableau de bord') }}
                    @if(isset($selectedShop))
                        <span class="text-indigo-600"> - {{ $selectedShop->name }}</span>
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Aperçu général et performances de votre activité') }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if(isset($shops) && count($shops) > 0)
                <div>
                    <form action="{{ route('dashboard') }}" method="GET" class="inline-flex">
                        <select name="shop_id" id="shop_selector" class="rounded-lg border-gray-300 text-sm pr-8" onchange="this.form.submit()">
                            <option value="">{{ __('Tableau de bord global') }}</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ request()->input('shop_id') == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
                <a href="{{ route('stats.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150 text-sm"
                   title="{{ __('Télécharger les statistiques globales au format CSV') }}">
                    <i class="bi bi-cloud-download mr-1"></i>
                    {{ __('Exporter') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Actions rapides -->
            <div class="mb-4 bg-gradient-to-r from-indigo-600 to-blue-500 rounded-lg shadow">
                <div class="p-4">
                    <h3 class="text-base font-bold text-white mb-2">{{ __('Actions rapides') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <a href="{{ route('bills.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-2 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Créer une nouvelle facture rapidement') }}">
                            <i class="bi bi-receipt text-xl mb-1"></i>
                            <span class="text-sm font-medium">{{ __('Nouvelle facture') }}</span>
                        </a>
                        <a href="{{ route('clients.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-2 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Ajouter un nouveau client à votre base de données') }}">
                            <i class="bi bi-person-plus text-xl mb-1"></i>
                            <span class="text-sm font-medium">{{ __('Nouveau client') }}</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-2 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Ajouter un nouveau produit ou service à votre catalogue') }}">
                            <i class="bi bi-box-seam text-xl mb-1"></i>
                            <span class="text-sm font-medium">{{ __('Nouveau produit') }}</span>
                        </a>
                        <a href="{{ route('inventory.receive') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-2 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Enregistrer une entrée de stock pour vos produits') }}">
                            <i class="bi bi-truck text-xl mb-1"></i>
                            <span class="text-sm font-medium">{{ __('Réception stock') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            
            @if(isset($selectedShop))
            <!-- Informations sur la boutique sélectionnée -->
            <div class="mb-4 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 flex items-start">
                    <div class="mr-4">
                        @if($selectedShop->logo_path)
                            <img src="{{ asset('storage/' . $selectedShop->logo_path) }}" alt="{{ $selectedShop->name }}" class="w-16 h-16 object-cover rounded">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="bi bi-shop text-3xl text-gray-500"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $selectedShop->name }}</h3>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="bi bi-geo-alt mr-1"></i> {{ $selectedShop->address }}
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="bi bi-telephone mr-1"></i> {{ $selectedShop->phone }} | 
                            <i class="bi bi-envelope mr-1"></i> {{ $selectedShop->email }}
                        </p>
                        <div class="flex mt-2 text-sm">
                            <div class="mr-4">
                                <span class="font-medium text-gray-700">{{ __('Managers:') }}</span>
                                <span>
                                    @forelse($selectedShop->managers as $manager)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $manager->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500">{{ __('Aucun') }}</span>
                                    @endforelse
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">{{ __('Statut:') }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $selectedShop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $selectedShop->is_active ? __('Actif') : __('Inactif') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistiques sommaires -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Nombre total de factures créées depuis le début.') }}">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-indigo-100 mr-3">
                            <i class="bi bi-receipt text-base text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">{{ __('Total Factures') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $globalStats['totalBills'] ?? 0 }}</div>
                            <a href="{{ route('bills.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 inline-flex items-center">
                                {{ __('Voir toutes') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Nombre de factures créées ce mois-ci et comparaison avec le mois précédent.') }}">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-green-100 mr-3">
                            <i class="bi bi-calendar-check text-base text-green-600"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">{{ __('Ce mois') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $globalStats['monthlyBills'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500">
                                @if(isset($globalStats['monthlyBillsPercentChange']))
                                    <span class="{{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'text-green-600' : 'text-red-600' }}"
                                          title="{{ $globalStats['monthlyBillsPercentChange'] >= 0 ? __('Augmentation par rapport au mois dernier') : __('Diminution par rapport au mois dernier') }}">
                                        <i class="bi {{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                        {{ abs($globalStats['monthlyBillsPercentChange']) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Revenu total généré par toutes les factures depuis le début.') }}">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-blue-100 mr-3">
                            <i class="bi bi-cash-coin text-base text-blue-600"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">{{ __('Revenu Total') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $globalStats['totalRevenue'] ?? '0 FCFA' }}</div>
                            <a href="#" id="showRevenueChart" class="text-xs text-blue-600 hover:text-blue-800 inline-flex items-center">
                                {{ __('Tendances') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Montant moyen de chaque facture sur l\'ensemble des factures.') }}">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-yellow-100 mr-3">
                            <i class="bi bi-cart-check text-base text-yellow-600"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500">{{ __('Panier Moyen') }}</div>
                            <div class="text-lg font-bold text-gray-900">{{ $globalStats['averageTicket'] ?? '0 FCFA' }}</div>
                            <a href="#" id="showAverageChart" class="text-xs text-yellow-600 hover:text-yellow-800 inline-flex items-center">
                                {{ __('Analyser') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($sellerStats) && count($sellerStats) > 0)
            <!-- Performances des vendeurs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4">
                    <h3 class="font-semibold text-lg text-gray-800 mb-3">
                        <i class="bi bi-people text-indigo-500 mr-1"></i>
                        {{ __('Performance des Vendeurs') }} - {{ $selectedShop->name }}
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Vendeur') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Ventes') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Montant total') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Commission') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sellerStats as $seller)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $seller['name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $seller['sales_count'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $seller['sales_total'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $seller['commission'] }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Graphiques d'analyse -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Performance des ventes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">
                            <i class="bi bi-graph-up text-indigo-500 mr-1"></i>
                            {{ __('Performance des ventes') }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">{{ __('Comparaison des 30 derniers jours') }}</p>
                        <div class="h-64">
                            <canvas id="salesPerformanceChart" class="w-full"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Informations financières -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">
                            <i class="bi bi-cash text-green-500 mr-1"></i>
                            {{ __('Financier') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Statut des factures') }}</h4>
                                <div class="h-40">
                                    <canvas id="billStatusChart" class="w-full"></canvas>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Paiements') }}</h4>
                                <div class="h-40">
                                    <canvas id="paymentMethodsChart" class="w-full"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique principal dynamique -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-lg text-gray-800">
                            <i class="bi bi-graph-up-arrow text-indigo-500 mr-1"></i>
                            {{ __('Analyse des ventes') }}
                        </h3>
                        <div class="flex gap-2">
                            <button id="downloadChartBtn" class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    title="{{ __('Télécharger le graphique actuel en image PNG') }}">
                                <i class="bi bi-download mr-1"></i> Télécharger
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select id="chartType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                    title="{{ __('Choisir le type de graphique (Barres ou Lignes)') }}">
                                <option value="bar">Barres</option>
                                <option value="line">Lignes</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Période</label>
                            <select id="timeRange" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                    title="{{ __('Sélectionner une période prédéfinie ou personnalisée') }}">
                                <option value="month" selected>Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" id="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                   max="{{ now()->format('Y-m-d') }}"
                                   title="{{ __('Date de début pour la période personnalisée') }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" id="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                   max="{{ now()->format('Y-m-d') }}"
                                   title="{{ __('Date de fin pour la période personnalisée') }}">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Métrique</label>
                            <select id="metric" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                    title="{{ __('Choisir la donnée à visualiser (Nombre de factures ou Montant total)') }}">
                                <option value="count">Nombre de factures</option>
                                <option value="amount">Montant total</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistiques dynamiques de la plage -->
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="bg-gray-100 p-2 rounded-lg" title="{{ __('Revenu total généré sur la période sélectionnée.') }}">
                            <div class="text-xs font-medium text-gray-600">Revenu Total</div>
                            <div id="totalRevenueSpan" class="text-base font-bold text-gray-900 flex items-center">
                                0 FCFA
                                <span id="revenueChange" class="ml-1 text-xs"></span>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-2 rounded-lg" title="{{ __('Panier moyen (montant moyen par facture) sur la période sélectionnée.') }}">
                            <div class="text-xs font-medium text-gray-600">Panier Moyen</div>
                            <div id="averageTicketSpan" class="text-base font-bold text-gray-900 flex items-center">
                                0 FCFA
                                <span id="ticketChange" class="ml-1 text-xs"></span>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-2 rounded-lg" title="{{ __('Nombre total de factures émises sur la période sélectionnée.') }}">
                            <div class="text-xs font-medium text-gray-600">Nombre de Factures</div>
                            <div id="billCountSpan" class="text-base font-bold text-gray-900 flex items-center">
                                0
                                <span id="countChange" class="ml-1 text-xs"></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full" style="height: 300px;">
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Section des analyses par thèmes -->
            <h3 class="font-semibold text-lg text-gray-800 mb-2">
                <i class="bi bi-clipboard-data text-indigo-500 mr-1"></i>
                {{ __('Tableaux de bord thématiques') }}
            </h3>

            <!-- Thème: Performance des ventes et Statuts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex justify-between items-center p-3 border-b border-gray-200">
                            <div>
                                <h3 class="font-semibold text-base">
                                    <i class="bi bi-currency-dollar text-green-500 mr-1"></i>
                                    {{ __('Performance des ventes') }}
                                </h3>
                                <p class="text-xs text-gray-500">Comparaison des 30 derniers jours</p>
                            </div>
                            <div class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded" title="{{ __('Analyse financière') }}">
                                {{ __('Financier') }}
                            </div>
                        </div>
                        <div class="p-3" title="{{ __('Graphique comparant les revenus des 30 derniers jours avec la période précédente.') }}">
                            <div style="height: 200px;">
                                <canvas id="revenueComparisonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-base">
                                    <i class="bi bi-pie-chart text-blue-500 mr-1"></i>
                                    {{ __('Statut des factures') }}
                                </h3>
                                <div class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded" title="{{ __('Répartition des factures par statut de paiement') }}">
                                    {{ __('Paiements') }}
                                </div>
                            </div>
                        </div>
                        <div class="p-3" title="{{ __('Graphique montrant la proportion des factures payées, en attente ou annulées.') }}">
                            <div style="height: 200px;">
                                <canvas id="invoiceStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables (améliorées) -->
            <h3 class="font-semibold text-lg text-gray-800 mb-2">
                <i class="bi bi-table text-indigo-500 mr-1"></i>
                {{ __('Données récentes') }}
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <!-- Dernières Factures -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center p-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-semibold text-base">
                                <i class="bi bi-receipt text-indigo-500 mr-1"></i>
                                {{ __('Dernières Factures') }}
                            </h3>
                            <p class="text-xs text-gray-500">Les 5 factures les plus récentes</p>
                        </div>
                        <a href="{{ route('bills.index') }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium inline-flex items-center"
                           title="{{ __('Voir toutes les factures') }}">
                            {{ __('Voir tout') }} 
                            <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($latestBills as $bill)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $bill->reference }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                            <a href="{{ route('clients.show', $bill->client) }}" class="hover:text-indigo-600">
                                                {{ $bill->client->name }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                            {{ $bill->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                                   ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    'bg-red-100 text-red-800') }}"
                                                title="{{ $bill->status === 'paid' ? __('Payée') : ($bill->status === 'pending' ? __('En attente') : __('Annulée')) }}">
                                                <i class="bi {{ $bill->status === 'paid' ? 'bi-check-circle' : 
                                                   ($bill->status === 'pending' ? 'bi-clock' : 
                                                    'bi-exclamation-circle') }} mr-1"></i>
                                                {{ ucfirst($bill->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                            {{ number_format($bill->total, 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Clients -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center p-3 border-b border-gray-200">
                        <div>
                            <h3 class="font-semibold text-base">
                                <i class="bi bi-star text-amber-500 mr-1"></i>
                                {{ __('Top Clients') }}
                            </h3>
                            <p class="text-xs text-gray-500">Les clients les plus actifs</p>
                        </div>
                        <a href="{{ route('clients.index') }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium inline-flex items-center"
                           title="{{ __('Voir tous les clients') }}">
                            {{ __('Voir tout') }}
                            <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factures</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Évolution</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topClients as $client)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                           <a>
                                            {{-- <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:text-indigo-900"> --}}
                                                {{ $client->name }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $client->count }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $client->total }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            @if(isset($client->trend) && $client->trend !== 0)
                                                <span class="{{ $client->trend > 0 ? 'text-green-600' : 'text-red-600' }} inline-flex items-center"
                                                      title="{{ __('Évolution du chiffre d\'affaires par rapport à la période précédente') }}">
                                                    <i class="bi {{ $client->trend > 0 ? 'bi-arrow-up' : 'bi-arrow-down' }} mr-1"></i>
                                                    {{ abs($client->trend) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-500">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script pour passer l'ID de boutique aux graphiques -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Obtenir les éléments qui déclenchent les requêtes AJAX
                    const timeRange = document.getElementById('timeRange');
                    const startDate = document.getElementById('startDate');
                    const endDate = document.getElementById('endDate');
                    const metric = document.getElementById('metric');
                    const chartType = document.getElementById('chartType');
                    
                    // Paramètre shop_id pour les requêtes AJAX
                    @if(isset($selectedShop))
                    window.selectedShopId = {{ $selectedShop->id }};
                    @else
                    window.selectedShopId = null;
                    @endif
                    
                    // Modifier les URL des requêtes AJAX pour inclure shop_id
                    const originalFetchStats = window.fetchStats;
                    const originalFetchRevenueComparison = window.fetchRevenueComparison;
                    const originalFetchInvoiceStatus = window.fetchInvoiceStatus;
                    const originalFetchInventoryStats = window.fetchInventoryStats;
                    
                    if (originalFetchStats) {
                        window.fetchStats = function(params) {
                            if (window.selectedShopId) {
                                params.shop_id = window.selectedShopId;
                            }
                            return originalFetchStats(params);
                        };
                    }
                    
                    if (originalFetchRevenueComparison) {
                        window.fetchRevenueComparison = function() {
                            const url = '/dashboard/revenue-comparison' + (window.selectedShopId ? '?shop_id=' + window.selectedShopId : '');
                            return fetch(url);
                        };
                    }
                    
                    if (originalFetchInvoiceStatus) {
                        window.fetchInvoiceStatus = function() {
                            const url = '/dashboard/invoice-status' + (window.selectedShopId ? '?shop_id=' + window.selectedShopId : '');
                            return fetch(url);
                        };
                    }
                    
                    if (originalFetchInventoryStats) {
                        window.fetchInventoryStats = function() {
                            const url = '/dashboard/inventory-stats' + (window.selectedShopId ? '?shop_id=' + window.selectedShopId : '');
                            return fetch(url);
                        };
                    }
                    
                    // Redéclencher le chargement des données
                    if (timeRange && metric && chartType) {
                        setTimeout(function() {
                            updateChart();
                        }, 100);
                    }
                });
            </script>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let myChart = null;
                let revenueComparisonChart = null;
                let invoiceStatusChart = null;

                function formatMoney(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'decimal',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value) + ' FCFA';
                }

                // Statut des factures (Pie Chart) - DYNAMIQUE
                loadInvoiceStatusChart();
                
                // Comparaison des revenus (Line chart) - DYNAMIQUE
                loadRevenueComparisonChart();
                
                // Fonction pour charger le graphique de statut des factures de manière dynamique
                function loadInvoiceStatusChart() {
                    const statusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
                    
                    fetch('/dashboard/invoice-status', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const statusData = {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.7)',
                                    'rgba(234, 179, 8, 0.7)',
                                    'rgba(239, 68, 68, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(234, 179, 8, 1)',
                                    'rgba(239, 68, 68, 1)'
                                ],
                                borderWidth: 1
                            }]
                        };
                        
                        if (invoiceStatusChart) {
                            invoiceStatusChart.destroy();
                        }
                        
                        invoiceStatusChart = new Chart(statusCtx, {
                            type: 'doughnut',
                            data: statusData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading invoice status chart:', error);
                    });
                }
                
                // Fonction pour charger le graphique de comparaison des revenus de manière dynamique
                function loadRevenueComparisonChart() {
                    const comparisonCtx = document.getElementById('revenueComparisonChart').getContext('2d');
                    
                    fetch('/dashboard/revenue-comparison', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (revenueComparisonChart) {
                            revenueComparisonChart.destroy();
                        }
                        
                        revenueComparisonChart = new Chart(comparisonCtx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [
                                    {
                                        label: 'Ce mois',
                                        data: data.currentData,
                                        borderColor: 'rgba(99, 102, 241, 1)',
                                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                        fill: true,
                                        tension: 0.4
                                    },
                                    {
                                        label: 'Mois précédent',
                                        data: data.previousData,
                                        borderColor: 'rgba(156, 163, 175, 1)',
                                        backgroundColor: 'rgba(156, 163, 175, 0.1)',
                                        borderDash: [5, 5],
                                        fill: true,
                                        tension: 0.4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                if (value >= 1000000) {
                                                    return (value / 1000000).toFixed(1) + 'M FCFA';
                                                }
                                                return value / 1000 + 'K FCFA';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading revenue comparison chart:', error);
                    });
                }

                // Fonction pour mettre à jour le graphique principal
                function updateChart() {
                    const timeRange = document.getElementById('timeRange').value;
                    const metric = document.getElementById('metric').value;
                    const chartType = document.getElementById('chartType').value;
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;

                    // Désactiver les champs de date si la période prédéfinie est sélectionnée
                    const dateInputs = document.querySelectorAll('#startDate, #endDate');
                    dateInputs.forEach(input => {
                        input.disabled = timeRange !== 'custom';
                    });

                    const params = new URLSearchParams({
                        timeRange: timeRange,
                        metric: metric,
                        startDate: startDate,
                        endDate: endDate
                    });

                    fetch('/dashboard/stats?' + params.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur réseau');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Data received:', data);

                            const ctx = document.getElementById('statsChart').getContext('2d');

                            if (myChart) {
                                myChart.destroy();
                            }

                            // Calcul robuste du revenu total
                            const totalRevenue = data.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);

                            // Calcul robuste du nombre de factures
                            const billCount = data.reduce((sum, item) => sum + (item.count || 0), 0);

                            // Calcul sécurisé du panier moyen
                            const averageTicket = billCount > 0 ? totalRevenue / billCount : 0;

                            // Vérifiez si les valeurs sont des nombres
                            if (isNaN(totalRevenue) || isNaN(billCount) || isNaN(averageTicket)) {
                                console.error('Invalid data received:', data);
                                return;
                            }

                            // Formatage et affichage des statistiques
                            document.getElementById('totalRevenueSpan').textContent = formatMoney(Math.round(totalRevenue));
                            document.getElementById('billCountSpan').textContent = billCount;
                            document.getElementById('averageTicketSpan').textContent = billCount > 0
                                ? formatMoney(Math.round(averageTicket))
                                : '0 FCFA';

                            myChart = new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: data.map(item => item.date),
                                    datasets: [{
                                        label: metric === 'count' ? 'Nombre de factures' : 'Montant',
                                        data: data.map(item => metric === 'count' ? item.count : parseFloat(item.amount)),
                                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                                        borderColor: 'rgba(99, 102, 241, 1)',
                                        borderWidth: 1,
                                        tension: 0.4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    if (metric === 'amount') {
                                                        if (value >= 1000000) {
                                                            return (value / 1000000).toFixed(1) + 'M FCFA';
                                                        }
                                                        return value / 1000 + 'K FCFA';
                                                    }
                                                    return value;
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.dataset.label || '';
                                                    if (label) {
                                                        label += ': ';
                                                    }
                                                    if (context.parsed.y !== null) {
                                                        if (metric === 'amount') {
                                                            label += formatMoney(context.parsed.y);
                                                        } else {
                                                            label += context.parsed.y;
                                                        }
                                                    }
                                                    return label;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });
                }

                // Téléchargement du graphique en PNG
                document.getElementById('downloadChartBtn').addEventListener('click', function() {
                    if (myChart) {
                        const link = document.createElement('a');
                        link.download = 'statistiques_ventes.png';
                        link.href = document.getElementById('statsChart').toDataURL('image/png');
                        link.click();
                    }
                });

                // Définir les écouteurs d'événements pour les contrôles
                document.getElementById('chartType').addEventListener('change', updateChart);
                document.getElementById('timeRange').addEventListener('change', updateChart);
                document.getElementById('metric').addEventListener('change', updateChart);
                document.getElementById('startDate').addEventListener('change', updateChart);
                document.getElementById('endDate').addEventListener('change', updateChart);

                // Définir dates par défaut pour le mois en cours
                const today = new Date();
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                document.getElementById('startDate').valueAsDate = firstDay;
                document.getElementById('endDate').valueAsDate = lastDay;

                // Initialiser le graphique au chargement
                updateChart();
                
                // Charger les données d'inventaire si la section existe
                if (document.getElementById('inventory-dashboard')) {
                    loadInventoryStats();
                }
                
                // Fonction pour charger les statistiques d'inventaire
                function loadInventoryStats() {
                    fetch('/dashboard/inventory-stats', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Mettre à jour les éléments d'interface avec les données d'inventaire
                        if (document.getElementById('lowStockCount')) {
                            document.getElementById('lowStockCount').textContent = data.lowStockProducts;
                        }
                        if (document.getElementById('stockValue')) {
                            document.getElementById('stockValue').textContent = data.stockValue;
                        }
                        
                        // Mise à jour des mouvements récents si la section existe
                        const movementsContainer = document.getElementById('recentMovements');
                        if (movementsContainer && data.recentMovements) {
                            let html = '';
                            data.recentMovements.forEach(movement => {
                                const typeClass = movement.type === 'entrée' ? 'text-green-500' : 'text-red-500';
                                const icon = movement.type === 'entrée' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle';
                                
                                html += `
                                <div class="border-b border-gray-200 pb-2 mb-2">
                                    <div class="flex items-center">
                                        <i class="bi ${icon} ${typeClass} mr-2"></i>
                                        <div>
                                            <div class="font-medium">${movement.product.name}</div>
                                            <div class="text-sm text-gray-500">
                                                Quantité: ${movement.quantity} · ${new Date(movement.created_at).toLocaleDateString()}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            });
                            
                            movementsContainer.innerHTML = html || '<p class="text-gray-500">Aucun mouvement récent</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading inventory stats:', error);
                    });
                }
            });
        </script>
    @endpush
@endsection

