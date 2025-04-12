<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Tableau de bord') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Aperçu général et performances de votre activité') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('stats.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150"
                   title="{{ __('Télécharger les statistiques globales au format CSV') }}">
                    <i class="bi bi-cloud-download mr-2"></i>
                    {{ __('Exporter les statistiques') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Actions rapides -->
            <div class="mb-6 bg-gradient-to-r from-indigo-600 to-blue-500 rounded-xl shadow-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-4">{{ __('Actions rapides') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="{{ route('bills.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Créer une nouvelle facture rapidement') }}">
                            <i class="bi bi-receipt text-3xl mb-2"></i>
                            <span class="font-medium">{{ __('Nouvelle facture') }}</span>
                        </a>
                        <a href="{{ route('clients.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Ajouter un nouveau client à votre base de données') }}">
                            <i class="bi bi-person-plus text-3xl mb-2"></i>
                            <span class="font-medium">{{ __('Nouveau client') }}</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Ajouter un nouveau produit ou service à votre catalogue') }}">
                            <i class="bi bi-box-seam text-3xl mb-2"></i>
                            <span class="font-medium">{{ __('Nouveau produit') }}</span>
                        </a>
                        <a href="{{ route('inventory.receive') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-white flex flex-col items-center justify-center transition duration-200"
                           title="{{ __('Enregistrer une entrée de stock pour vos produits') }}">
                            <i class="bi bi-truck text-3xl mb-2"></i>
                            <span class="font-medium">{{ __('Réception stock') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques sommaires -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Nombre total de factures créées depuis le début.') }}">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-indigo-100 mr-4">
                            <i class="bi bi-receipt text-xl text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Total Factures') }}</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $globalStats['totalBills'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">{{ __('Toutes périodes confondues') }}</div>
                            <a href="{{ route('bills.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 inline-flex items-center mt-1">
                                {{ __('Voir toutes les factures') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Nombre de factures créées ce mois-ci et comparaison avec le mois précédent.') }}">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-green-100 mr-4">
                            <i class="bi bi-calendar-check text-xl text-green-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Ce mois') }}</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $globalStats['monthlyBills'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">
                                @if(isset($globalStats['monthlyBillsPercentChange']))
                                    <span class="{{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'text-green-600' : 'text-red-600' }}"
                                          title="{{ $globalStats['monthlyBillsPercentChange'] >= 0 ? __('Augmentation par rapport au mois dernier') : __('Diminution par rapport au mois dernier') }}">
                                        <i class="bi {{ $globalStats['monthlyBillsPercentChange'] >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                        {{ abs($globalStats['monthlyBillsPercentChange']) }}% vs. mois dernier
                                    </span>
                                @endif
                            </div>
                            <a href="#" id="showMonthlyChart" class="text-xs text-green-600 hover:text-green-800 inline-flex items-center mt-1">
                                {{ __('Voir l\'évolution') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Revenu total généré par toutes les factures depuis le début.') }}">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-blue-100 mr-4">
                            <i class="bi bi-cash-coin text-xl text-blue-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Revenu Total') }}</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $globalStats['totalRevenue'] ?? '0 FCFA' }}</div>
                            <div class="text-sm text-gray-500">{{ __('Depuis le début') }}</div>
                            <a href="#" id="showRevenueChart" class="text-xs text-blue-600 hover:text-blue-800 inline-flex items-center mt-1">
                                {{ __('Voir les tendances') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" title="{{ __('Montant moyen de chaque facture sur l\'ensemble des factures.') }}">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-yellow-100 mr-4">
                            <i class="bi bi-cart-check text-xl text-yellow-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Panier Moyen') }}</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $globalStats['averageTicket'] ?? '0 FCFA' }}</div>
                            <div class="text-sm text-gray-500">{{ __('Sur toutes les factures') }}</div>
                            <a href="#" id="showAverageChart" class="text-xs text-yellow-600 hover:text-yellow-800 inline-flex items-center mt-1">
                                {{ __('Analyser les détails') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique principal dynamique -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-xl text-gray-800">
                            <i class="bi bi-graph-up-arrow text-indigo-500 mr-2"></i>
                            {{ __('Analyse des ventes') }}
                        </h3>
                        <div class="flex gap-2">
                            <button id="downloadChartBtn" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    title="{{ __('Télécharger le graphique actuel en image PNG') }}">
                                <i class="bi bi-download mr-2"></i> Télécharger
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="chartType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    title="{{ __('Choisir le type de graphique (Barres ou Lignes)') }}">
                                <option value="bar">Barres</option>
                                <option value="line">Lignes</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                            <select id="timeRange" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    title="{{ __('Sélectionner une période prédéfinie ou personnalisée') }}">
                                <option value="month" selected>Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" id="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   max="{{ now()->format('Y-m-d') }}"
                                   title="{{ __('Date de début pour la période personnalisée') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" id="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   max="{{ now()->format('Y-m-d') }}"
                                   title="{{ __('Date de fin pour la période personnalisée') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Métrique</label>
                            <select id="metric" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    title="{{ __('Choisir la donnée à visualiser (Nombre de factures ou Montant total)') }}">
                                <option value="count">Nombre de factures</option>
                                <option value="amount">Montant total</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistiques dynamiques de la plage -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-100 p-4 rounded-lg" title="{{ __('Revenu total généré sur la période sélectionnée.') }}">
                            <div class="text-sm font-medium text-gray-600">Revenu Total</div>
                            <div id="totalRevenueSpan" class="text-xl font-bold text-gray-900 flex items-center">
                                0 FCFA
                                <span id="revenueChange" class="ml-2 text-xs"></span>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg" title="{{ __('Panier moyen (montant moyen par facture) sur la période sélectionnée.') }}">
                            <div class="text-sm font-medium text-gray-600">Panier Moyen</div>
                            <div id="averageTicketSpan" class="text-xl font-bold text-gray-900 flex items-center">
                                0 FCFA
                                <span id="ticketChange" class="ml-2 text-xs"></span>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg" title="{{ __('Nombre total de factures émises sur la période sélectionnée.') }}">
                            <div class="text-sm font-medium text-gray-600">Nombre de Factures</div>
                            <div id="billCountSpan" class="text-xl font-bold text-gray-900 flex items-center">
                                0
                                <span id="countChange" class="ml-2 text-xs"></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full" style="height: 400px;">
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Section des analyses par thèmes -->
            <h3 class="font-semibold text-xl text-gray-800 mb-4">
                <i class="bi bi-clipboard-data text-indigo-500 mr-2"></i>
                {{ __('Tableaux de bord thématiques') }}
            </h3>

            <!-- Thème: Performance des ventes -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                            <div>
                                <h3 class="font-semibold text-xl">
                                    <i class="bi bi-currency-dollar text-green-500 mr-2"></i>
                                    {{ __('Performance des ventes') }}
                                </h3>
                                <p class="text-sm text-gray-500">Comparaison des 30 derniers jours</p>
                            </div>
                            <div class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded" title="{{ __('Analyse financière') }}">
                                {{ __('Financier') }}
                            </div>
                        </div>
                        <div class="p-6" title="{{ __('Graphique comparant les revenus des 30 derniers jours avec la période précédente.') }}">
                            <div class="h-64">
                                <canvas id="revenueComparisonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-xl">
                                    <i class="bi bi-pie-chart text-blue-500 mr-2"></i>
                                    {{ __('Statut des factures') }}
                                </h3>
                                <div class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded" title="{{ __('Répartition des factures par statut de paiement') }}">
                                    {{ __('Paiements') }}
                                </div>
                            </div>
                        </div>
                        <div class="p-6" title="{{ __('Graphique montrant la proportion des factures payées, en attente ou annulées.') }}">
                            <div class="h-64">
                                <canvas id="invoiceStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thème: Activité clients et produits -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                            <div>
                                <h3 class="font-semibold text-xl">
                                    <i class="bi bi-people text-purple-500 mr-2"></i>
                                    {{ __('Acquisitions') }}
                                </h3>
                                <p class="text-sm text-gray-500">Nouveaux clients par période</p>
                            </div>
                            <div class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded" title="{{ __('Analyse des clients') }}">
                                {{ __('Clients') }}
                            </div>
                        </div>
                        <div class="p-6" title="{{ __('Graphique montrant le nombre de nouveaux clients acquis par mois/trimestre/année.') }}">
                            <div class="h-64">
                                <canvas id="clientAcquisitionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                            <div>
                                <h3 class="font-semibold text-xl">
                                    <i class="bi bi-box-seam text-amber-500 mr-2"></i>
                                    {{ __('Top produits') }}
                                </h3>
                                <p class="text-sm text-gray-500">Produits les plus vendus du mois</p>
                            </div>
                            <div class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded" title="{{ __('Analyse des produits') }}">
                                {{ __('Produits') }}
                            </div>
                        </div>
                        <div class="p-6" title="{{ __('Graphique montrant les produits qui génèrent le plus de revenus ou sont le plus souvent vendus.') }}">
                            <div class="h-64">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables (améliorées) -->
            <h3 class="font-semibold text-xl text-gray-800 mb-4">
                <i class="bi bi-table text-indigo-500 mr-2"></i>
                {{ __('Données récentes') }}
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Dernières Factures -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <div>
                            <h3 class="font-semibold text-xl">
                                <i class="bi bi-receipt text-indigo-500 mr-2"></i>
                                {{ __('Dernières Factures') }}
                            </h3>
                            <p class="text-sm text-gray-500">Les 5 factures les plus récentes</p>
                        </div>
                        <a href="{{ route('bills.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium inline-flex items-center"
                           title="{{ __('Voir toutes les factures') }}">
                            {{ __('Voir tout') }} 
                            <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($latestBills as $bill)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $bill->reference }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('clients.show', $bill->client) }}" class="hover:text-indigo-600">
                                                {{ $bill->client->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $bill->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                    <div class="flex justify-between items-center p-6 border-b border-gray-200">
                        <div>
                            <h3 class="font-semibold text-xl">
                                <i class="bi bi-star text-amber-500 mr-2"></i>
                                {{ __('Top Clients') }}
                            </h3>
                            <p class="text-sm text-gray-500">Les clients les plus actifs</p>
                        </div>
                        <a href="{{ route('clients.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium inline-flex items-center"
                           title="{{ __('Voir tous les clients') }}">
                            {{ __('Voir tout') }}
                            <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factures</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Évolution</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topClients as $client)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                           <a>
                                            {{-- <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:text-indigo-900"> --}}
                                                {{ $client->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->total }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
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
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    @push('scripts')
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

                // Statut des factures (Pie Chart)
                const statusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
                
                // Simuler des données pour l'exemple (à remplacer par des données réelles)
                const statusData = {
                    labels: ['Payé', 'En attente', 'Annulé'],
                    datasets: [{
                        data: [65, 30, 5],
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

                // Comparaison des revenus (Line chart)
                const comparisonCtx = document.getElementById('revenueComparisonChart').getContext('2d');
                
                // Simuler des données pour l'exemple (à remplacer par des données réelles)
                const labels = ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'];
                const currentData = [1500000, 1800000, 1600000, 2000000];
                const previousData = [1400000, 1600000, 1500000, 1700000];
                
                revenueComparisonChart = new Chart(comparisonCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Ce mois',
                                data: currentData,
                                borderColor: 'rgba(99, 102, 241, 1)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Mois précédent',
                                data: previousData,
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
            });
        </script>
    @endpush
</x-app-layout>

