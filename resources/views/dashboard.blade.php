@extends('layouts.app')

@section('page_name', 'dashboard')

@section('content')

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Tableau de bord') }}
                    @if(isset($selectedShop))
                        <span class="text-indigo-600 dark:text-indigo-400"> - {{ $selectedShop->name }}</span>
                    @endif
                </h2>
            </div>
            <div class="flex space-x-3">
                @if(isset($shops) && count($shops) > 0)
                    <div>
                        <form action="{{ route('dashboard') }}" method="GET" class="inline-flex">
                            <select name="shop_id" id="shop_selector" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 text-sm pr-8" onchange="this.form.submit()">
                                <option value="">{{ __('Tableau global') }}</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ request()->input('shop_id') == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
                <a href="{{ route('stats.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-1.5 px-3 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150 text-sm">
                    <i class="fas fa-download mr-1"></i>
                    {{ __('Exporter') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">

            <!-- Actions rapides -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3">
                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-bolt text-yellow-500 mr-1"></i>
                        {{ __('Actions rapides') }}
                    </h3>
                </div>
                <div class="p-2">
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                        <a href="{{ route('bills.create') }}" class="flex flex-col items-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800/40 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/60 transition-colors">
                                <i class="fas fa-file-invoice text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400 text-center">{{ __('Nouvelle facture') }}</span>
                        </a>

                        <a href="{{ route('clients.create') }}" class="flex flex-col items-center p-2 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-800/40 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-green-200 dark:group-hover:bg-green-800/60 transition-colors">
                                <i class="fas fa-user-plus text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400 text-center">{{ __('Nouveau client') }}</span>
                        </a>

                        <a href="{{ route('products.create') }}" class="flex flex-col items-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-800/40 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/60 transition-colors">
                                <i class="fas fa-box text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-purple-700 dark:text-purple-400 text-center">{{ __('Nouveau produit') }}</span>
                        </a>

                        <a href="{{ route('inventory.receive') }}" class="flex flex-col items-center p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-800/40 text-orange-600 dark:text-orange-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/60 transition-colors">
                                <i class="fas fa-solid fa-arrow-down text-lg"></i>
                            </div>
{{--                            <i class="fa-solid fa-arrow-down"></i>--}}
                            <span class="text-xs font-medium text-orange-700 dark:text-orange-400 text-center">{{ __('Réception') }}</span>
                        </a>

                        <a href="{{ route('barters.create') }}" class="flex flex-col items-center p-2 bg-violet-50 dark:bg-violet-900/20 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-violet-100 dark:bg-violet-800/40 text-violet-600 dark:text-violet-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-violet-200 dark:group-hover:bg-violet-800/60 transition-colors">
                                <i class="fas fa-exchange-alt text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-violet-700 dark:text-violet-400 text-center">{{ __('Troc') }}</span>
                        </a>

                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <a href="{{ route('commissions.index') }}" class="flex flex-col items-center p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/40 transition-colors group">
                                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-800/40 text-teal-600 dark:text-teal-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-teal-200 dark:group-hover:bg-teal-800/60 transition-colors">
                                    <i class="fas fa-hand-holding-usd text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-teal-700 dark:text-teal-400 text-center">{{ __('Commissions') }}</span>
                            </a>
                        @elseif(auth()->user()->role === 'vendeur')
                            <a href="{{ route('commissions.vendor-report', auth()->id()) }}" class="flex flex-col items-center p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/40 transition-colors group">
                                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-800/40 text-teal-600 dark:text-teal-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-teal-200 dark:group-hover:bg-teal-800/60 transition-colors">
                                    <i class="fas fa-hand-holding-usd text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-teal-700 dark:text-teal-400 text-center">{{ __('Mes commissions') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistiques sommaires -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-indigo-100 dark:bg-indigo-900/30 mr-3">
                            <i class="fas fa-receipt text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total Factures') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['totalBills'] ?? 0 }}</div>
                            <a href="{{ route('bills.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center">
                                {{ __('Voir toutes') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-green-100 dark:bg-green-900/30 mr-3">
                            <i class="fas fa-calendar-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Ce mois') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['monthlyBills'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if(isset($globalStats['monthlyBillsPercentChange']))
                                    <span class="{{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        <i class="fas {{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                        {{ abs($globalStats['monthlyBillsPercentChange']) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-blue-100 dark:bg-blue-900/30 mr-3">
                            <i class="fas fa-hand-holding-usd text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Revenu Total') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['totalRevenue'] ?? '0 FCFA' }}</div>
                            <a href="#" id="showRevenueChart" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center">
                                {{ __('Tendances') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-3 flex items-start">
                        <div class="rounded-full p-2 bg-yellow-100 dark:bg-yellow-900/30 mr-3">
                            <i class="fas fa-shopping-cart text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Panier Moyen') }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['averageTicket'] ?? '0 FCFA' }}</div>
                            <a href="#" id="showAverageChart" class="text-xs text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 inline-flex items-center">
                                {{ __('Analyser') }} <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques principaux -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                <!-- Graphique principal -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-chart-line text-indigo-500 mr-1"></i>
                            {{ __('Performance des ventes') }}
                        </h3>
                        <div class="flex gap-2">
                            <select id="chartPeriod" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-xs pr-6">
                                <option value="month">Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                            </select>
                            <button id="downloadChartBtn" class="bg-gray-100 dark:bg-gray-700 p-1 rounded text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <i class="fas fa-download text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-3">
                        <div style="height: 240px;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Statuts et Paiements -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-chart-pie text-blue-500 mr-1"></i>
                            {{ __('Statuts & Paiements') }}
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-2 p-3">
                        <div>
                            <h4 class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Statut des factures') }}</h4>
                            <div style="height: 110px;">
                                <canvas id="billStatusChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('Méthodes de paiement') }}</h4>
                            <div style="height: 110px;">
                                <canvas id="paymentMethodsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières factures et Top Clients -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                <!-- Dernières Factures -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-receipt text-indigo-500 mr-1"></i>
                            {{ __('Dernières Factures') }}
                        </h3>
                        <a href="{{ route('bills.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-xs font-medium inline-flex items-center">
                            {{ __('Voir tout') }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Référence</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($latestBills as $bill)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            {{ $bill->reference }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">
                                        <a href="{{ route('clients.show', $bill->client) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ $bill->client->name }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $bill->date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                                ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                                <i class="fas {{ $bill->status === 'paid' ? 'fa-check-circle' :
                                                ($bill->status === 'pending' ? 'fa-clock' :
                                                'fa-times-circle') }} mr-1"></i>
                                                {{ $bill->status === 'paid' ? 'Payée' : ($bill->status === 'pending' ? 'En attente' : 'Annulée') }}
                                            </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">
                                        {{ number_format($bill->total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Clients -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-trophy text-amber-500 mr-1"></i>
                            {{ __('Top Clients') }}
                        </h3>
                        <a href="{{ route('clients.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-xs font-medium inline-flex items-center">
                            {{ __('Voir tout') }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Factures</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($topClients as $client)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                        <a href="{{ $client->url ?? '#' }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            {{ $client->name }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $client->count }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $client->total }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(isset($sellerStats) && count($sellerStats) > 0)
                <!-- Performance des vendeurs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-users text-indigo-500 mr-1"></i>
                            {{ __('Performance des Vendeurs') }}
                            @if(isset($selectedShop))
                                <span class="text-sm font-normal text-gray-600 dark:text-gray-400">- {{ $selectedShop->name }}</span>
                            @endif
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Vendeur') }}
                                </th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Ventes') }}
                                </th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Montant total') }}
                                </th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Commission') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sellerStats as $seller)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-gray-300">{{ $seller['name'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $seller['sales_count'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $seller['sales_total'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $seller['commission'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Variables pour stocker les instances de graphiques
                let salesChart = null;
                let billStatusChart = null;
                let paymentMethodsChart = null;

                // Fonction pour formater les montants
                function formatMoney(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'decimal',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value) + ' FCFA';
                }

                // Initialisation des graphiques
                initSalesChart();
                initBillStatusChart();
                initPaymentMethodsChart();

                // Graphique de performance des ventes
                function initSalesChart() {
                    const ctx = document.getElementById('salesChart').getContext('2d');

                    // Données d'exemple (remplacer par des données réelles via AJAX)
                    const salesData = {
                        labels: {!! json_encode($salesChartData['labels'] ?? ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4']) !!},
                        datasets: [
                            {
                                label: 'Ce mois',
                                data: {!! json_encode($salesChartData['current'] ?? [0, 0, 0, 0]) !!},
                                borderColor: 'rgba(99, 102, 241, 1)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Mois précédent',
                                data: {!! json_encode($salesChartData['previous'] ?? [0, 0, 0, 0]) !!},
                                borderColor: 'rgba(156, 163, 175, 1)',
                                backgroundColor: 'rgba(156, 163, 175, 0.1)',
                                borderDash: [5, 5],
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    };

                    salesChart = new Chart(ctx, {
                        type: 'line',
                        data: salesData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += formatMoney(context.parsed.y);
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return (value / 1000000).toFixed(1) + 'M';
                                            }
                                            return (value / 1000) + 'K';
                                        },
                                        font: {
                                            size: 9
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Graphique des statuts de factures
                function initBillStatusChart() {
                    const ctx = document.getElementById('billStatusChart').getContext('2d');

                    // Données d'exemple (remplacer par des données réelles via AJAX)
                    const statusData = {
                        labels: {!! json_encode($billStatusData['labels'] ?? ['Payée', 'En attente', 'Annulée']) !!},
                        datasets: [{
                            data: {!! json_encode($billStatusData['values'] ?? [0, 0, 0]) !!},
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

                    billStatusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: statusData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            },
                            cutout: '70%'
                        }
                    });
                }

                // Graphique des méthodes de paiement
                function initPaymentMethodsChart() {
                    const ctx = document.getElementById('paymentMethodsChart').getContext('2d');

                    // Données d'exemple (remplacer par des données réelles via AJAX)
                    const paymentData = {
                        labels: {!! json_encode($paymentMethodsData['labels'] ?? ['Espèces', 'Carte', 'Mobile Money', 'Virement']) !!},
                        datasets: [{
                            data: {!! json_encode($paymentMethodsData['values'] ?? [0, 0, 0, 0]) !!},
                            backgroundColor: [
                                'rgba(79, 70, 229, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(59, 130, 246, 0.7)'
                            ],
                            borderColor: [
                                'rgba(79, 70, 229, 1)',
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(59, 130, 246, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };

                    paymentMethodsChart = new Chart(ctx, {
                        type: 'pie',
                        data: paymentData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Écouter les changements de période pour le graphique de ventes
                document.getElementById('chartPeriod').addEventListener('change', function() {
                    // Ici, vous pouvez mettre à jour le graphique avec des données AJAX
                    // basées sur la période sélectionnée
                    // Pour l'exemple, on simule un changement de données
                    updateSalesChartData(this.value);
                });

                // Fonction pour mettre à jour les données du graphique de ventes
                function updateSalesChartData(period) {
                    // Cette fonction serait normalement une requête AJAX
                    // Pour l'exemple, on simule un changement de données
                    const shopId = @if(isset($selectedShop)) {{ $selectedShop->id }} @else null @endif;

                    // Requête AJAX (à implémenter)
                    console.log(`Updating chart for period: ${period} and shop: ${shopId}`);

                    // Mise à jour simulée pour l'exemple
                    if (salesChart) {
                        // Simuler des nouvelles données
                        const newData = [
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000)
                        ];

                        const prevData = [
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000),
                            Math.floor(Math.random() * 100000)
                        ];

                        // Mettre à jour le graphique
                        salesChart.data.datasets[0].data = newData;
                        salesChart.data.datasets[1].data = prevData;
                        salesChart.update();
                    }
                }

                // Téléchargement du graphique en PNG
                document.getElementById('downloadChartBtn').addEventListener('click', function() {
                    if (salesChart) {
                        const link = document.createElement('a');
                        link.download = 'performance_ventes.png';
                        link.href = document.getElementById('salesChart').toDataURL('image/png');
                        link.click();
                    }
                });
            });
        </script>
    @endpush
@endsection
