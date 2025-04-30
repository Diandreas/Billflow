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

                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <a href="{{ route('products.create') }}" class="flex flex-col items-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors group">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-800/40 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/60 transition-colors">
                                <i class="fas fa-box text-lg"></i>
                            </div>
                            <span class="text-xs font-medium text-purple-700 dark:text-purple-400 text-center">{{ __('Nouveau produit') }}</span>
                        </a>
                        @endif

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

                            <a href="{{ route('commission-payments.index') }}" class="flex flex-col items-center p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors group">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-800/40 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/60 transition-colors">
                                    <i class="fas fa-money-bill-wave text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-indigo-700 dark:text-indigo-400 text-center">{{ __('Paiements') }}</span>
                            </a>
                        @elseif(auth()->user()->role === 'vendeur')
                            <a href="{{ route('commissions.vendor-report', auth()->id()) }}" class="flex flex-col items-center p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/40 transition-colors group">
                                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-800/40 text-teal-600 dark:text-teal-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-teal-200 dark:group-hover:bg-teal-800/60 transition-colors">
                                    <i class="fas fa-hand-holding-usd text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-teal-700 dark:text-teal-400 text-center">{{ __('Mes commissions') }}</span>
                            </a>

                            <a href="{{ route('commission-payments.vendor-history', auth()->id()) }}" class="flex flex-col items-center p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors group">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-800/40 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mb-1 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/60 transition-colors">
                                    <i class="fas fa-money-bill-wave text-lg"></i>
                                </div>
                                <span class="text-xs font-medium text-indigo-700 dark:text-indigo-400 text-center">{{ __('Mes paiements') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistiques sommaires -->
            @if(auth()->user()->role !== 'vendeur')
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
            @endif

            @if(auth()->user()->role === 'vendeur' && isset($vendorStats))
            <!-- Statistiques personnelles du vendeur -->
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-chart-line text-indigo-500 mr-1"></i>
                    {{ __('Mes statistiques de vente') }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-3 flex items-start">
                            <div class="rounded-full p-2 bg-indigo-100 dark:bg-indigo-900/30 mr-3">
                                <i class="fas fa-receipt text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Mes Ventes') }}</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $vendorStats['total_sales'] ?? 0 }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    @if(isset($vendorStats['monthly_sales']))
                                        <span class="{{ $vendorStats['monthly_sales_percent_change'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            <i class="fas {{ $vendorStats['monthly_sales_percent_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                            {{ abs($vendorStats['monthly_sales_percent_change']) }}% ce mois
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
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total Commissions') }}</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $vendorStats['total_commissions'] ?? 0 }}</div>
                                <div class="text-xs text-green-600 dark:text-green-400">
                                    {{ $vendorStats['commission_rate'] ?? '0%' }} de taux
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-3 flex items-start">
                            <div class="rounded-full p-2 bg-green-100 dark:bg-green-900/30 mr-3">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions Payées') }}</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $vendorStats['paid_commissions'] ?? 0 }}</div>
                                <a href="{{ route('commissions.user-history', auth()->id()) }}" class="text-xs text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 inline-flex items-center">
                                    {{ __('Historique') }} <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-3 flex items-start">
                            <div class="rounded-full p-2 bg-yellow-100 dark:bg-yellow-900/30 mr-3">
                                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('En Attente') }}</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $vendorStats['pending_commissions'] ?? 0 }}</div>
                                <a href="{{ route('commissions.pending', auth()->id()) }}" class="text-xs text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 inline-flex items-center">
                                    {{ __('Voir détails') }} <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé de performance du vendeur -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3">
                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-user-chart text-indigo-500 mr-1"></i>
                        {{ __('Mon Profil de Performance') }}
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
                        <div class="mb-4 md:mb-0">
                            <div class="w-24 h-24 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-user text-indigo-600 dark:text-indigo-400 text-4xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">
                                {{ auth()->user()->name }}
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                <span class="text-indigo-600 dark:text-indigo-400 font-medium">{{ auth()->user()->role }}</span>
                                @if(isset($selectedShop))
                                    <span class="ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-800 dark:text-gray-300 text-xs">
                                        {{ $selectedShop->name }}
                                    </span>
                                @endif
                            </p>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Ventes') }}</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $vendorStats['total_sales'] ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Chiffre d\'affaires') }}</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $vendorStats['total_sales_amount'] ?? '0 FCFA' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Taux de commission') }}</p>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $vendorStats['commission_rate'] ?? '0%' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Commissions') }}</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $vendorStats['total_amount'] ?? '0 FCFA' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <!-- Statistiques des paiements de commissions -->
                @if(isset($globalStats['commissionsPayments']))
                <div class="mb-3">
                    <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-money-bill-wave text-indigo-500 mr-1"></i>
                        {{ __('Paiements de commissions') }}
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-3 flex items-start">
                                <div class="rounded-full p-2 bg-indigo-100 dark:bg-indigo-900/30 mr-3">
                                    <i class="fas fa-money-bill-wave text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total Paiements') }}</div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['commissionsPayments']['totalPayments'] ?? 0 }}</div>
                                    <a href="{{ route('commission-payments.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center">
                                        {{ __('Voir tous') }} <i class="fas fa-arrow-right ml-1"></i>
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
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['commissionsPayments']['monthlyPayments'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if(isset($globalStats['commissionsPayments']['monthlyPaymentsPercentChange']))
                                            <span class="{{ $globalStats['commissionsPayments']['monthlyPaymentsPercentChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                <i class="fas {{ $globalStats['commissionsPayments']['monthlyPaymentsPercentChange'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                                {{ abs($globalStats['commissionsPayments']['monthlyPaymentsPercentChange']) }}%
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
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Montant Total') }}</div>
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $globalStats['commissionsPayments']['totalAmount'] ?? '0 FCFA' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            @if(auth()->user()->role !== 'vendeur')
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
            @else
            <!-- Mes dernières factures (pour vendeur) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3">
                <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-receipt text-indigo-500 mr-1"></i>
                        {{ __('Mes Dernières Factures') }}
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
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isManager() && isset($latestCommissionPayments) && $latestCommissionPayments->count() > 0)
                <!-- Derniers paiements de commissions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3">
                    <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            <i class="fas fa-money-bill-wave text-indigo-500 mr-1"></i>
                            {{ __('Derniers Paiements de Commissions') }}
                        </h3>
                        <a href="{{ route('commission-payments.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-xs font-medium inline-flex items-center">
                            {{ __('Voir tout') }}
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Référence</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendeur</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Boutique</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Méthode</th>
                                <th class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($latestCommissionPayments as $payment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                            <a href="{{ route('commission-payments.show', $payment) }}">{{ $payment->reference }}</a>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $payment->user->name }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $payment->shop->name }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                            {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                                {{ $payment->payment_method }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $payment->paid_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(isset($sellerStats) && count($sellerStats) > 0 && auth()->user()->role !== 'vendeur')
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

            <!-- Après les autres sections du tableau de bord, ajouter la section des fournisseurs -->
            @can('admin')
            <div class="my-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Analyse des fournisseurs') }}</h3>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4" id="supplierStats">
                    <!-- Meilleurs fournisseurs par ventes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ __('Top fournisseurs par ventes') }}</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 max-h-60 overflow-y-auto supplier-list">
                                <div class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Chargement...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meilleurs fournisseurs par quantité en stock -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ __('Top fournisseurs par stock') }}</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 max-h-60 overflow-y-auto supplier-list">
                                <div class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Chargement...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meilleurs fournisseurs par valeur de stock -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ __('Top fournisseurs par valeur de stock') }}</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 max-h-60 overflow-y-auto supplier-list">
                                <div class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Chargement...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
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

                // Chargement des statistiques des fournisseurs pour les administrateurs
                @can('admin')
                    loadSupplierStats();
                @endcan
            });

            // Fonction pour charger les statistiques des fournisseurs
            function loadSupplierStats() {
                const shopId = document.getElementById('shop_selector')?.value || '';

                // Utiliser l'URL correcte (web route au lieu d'API route)
                fetch(`/dashboard/top-suppliers?shop_id=${shopId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Afficher les fournisseurs par ventes
                            const salesListEl = document.querySelector('#supplierStats > div:nth-child(1) .supplier-list');
                            renderSupplierList(salesListEl, data.data.top_suppliers_by_sales, 'bills_count', 'products_sold', 'total_sales', true);

                            // Afficher les fournisseurs par quantité en stock
                            const stockListEl = document.querySelector('#supplierStats > div:nth-child(2) .supplier-list');
                            renderSupplierList(stockListEl, data.data.top_suppliers_by_stock, 'products_count', 'total_stock', null, false);

                            // Afficher les fournisseurs par valeur de stock
                            const stockValueListEl = document.querySelector('#supplierStats > div:nth-child(3) .supplier-list');
                            renderSupplierList(stockValueListEl, data.data.top_suppliers_by_stock_value, 'products_count', null, 'stock_value', false);
                        } else {
                            throw new Error('Données non valides');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des statistiques des fournisseurs:', error);

                        // Afficher un message d'erreur dans les conteneurs
                        const containers = document.querySelectorAll('#supplierStats .supplier-list');
                        containers.forEach(container => {
                            container.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-sm text-red-500 dark:text-red-400">Impossible de charger les données</p>
                </div>
            `;
                        });
                    });
            }

            // Fonction pour afficher une liste de fournisseurs
            function renderSupplierList(container, suppliers, countLabel, quantityLabel, valueLabel, isSales) {
                if (!suppliers || suppliers.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aucune donnée disponible') }}</p>
                        </div>
                    `;
                    return;
                }

                let html = '';

                suppliers.forEach((supplier, index) => {
                    // Formatage des valeurs
                    const number = index + 1;
                    const count = supplier[countLabel] || 0;
                    const quantity = quantityLabel ? (supplier[quantityLabel] || 0) : null;
                    const value = valueLabel ? (supplier[valueLabel] || 0) : null;

                    html += `
                        <div class="flex justify-between items-center p-2 ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : ''}">
                            <div class="flex items-center">
                                <span class="text-gray-500 dark:text-gray-400 w-6 text-center">${number}.</span>
                                <span class="font-medium ml-2">${supplier.name}</span>
                            </div>
                            <div class="text-right text-sm">
                                ${count ? `<div><span class="text-gray-500 dark:text-gray-400">${isSales ? 'Factures:' : 'Produits:'}</span> ${count}</div>` : ''}
                                ${quantity ? `<div><span class="text-gray-500 dark:text-gray-400">Quantité:</span> ${formatNumber(quantity)}</div>` : ''}
                                ${value ? `<div><span class="text-gray-500 dark:text-gray-400">${isSales ? 'Ventes:' : 'Valeur:'}</span> ${formatCurrency(value)}</div>` : ''}
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            }

            // Fonction pour formater un nombre avec séparateur de milliers
            function formatNumber(number) {
                return new Intl.NumberFormat('fr-FR').format(Math.round(number));
            }

            // Fonction pour formater un montant en FCFA
            function formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR', {
                    style: 'currency',
                    currency: 'XOF',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(Math.round(amount));
            }
        </script>
    @endpush
@endsection
