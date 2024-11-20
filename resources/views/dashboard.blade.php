
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques sommaires -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Factures</div>
                        <div class="text-2xl font-bold text-gray-900">{{ App\Models\Bill::count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Clients</div>
                        <div class="text-2xl font-bold text-gray-900">{{ App\Models\Client::count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Produits</div>
                        <div class="text-2xl font-bold text-gray-900">{{ App\Models\Product::count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Ce mois</div>
                        <div class="text-2xl font-bold text-gray-900">{{ App\Models\Bill::whereMonth('created_at', now()->month)->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Graphique avec contrôles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Statistiques Détaillées</h3>

                    <!-- Contrôles du graphique -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de graphique</label>
                            <select id="chartType" class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="line">Ligne</option>
                                <option value="area">Zone</option>
                                <option value="bar">Barres</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                            <select id="timeRange" class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="month">30 derniers jours</option>
                                <option value="quarter">3 derniers mois</option>
                                <option value="year">Année</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Métrique</label>
                            <select id="metric" class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="count">Nombre de factures</option>
                                <option value="amount">Montant total</option>
                                <option value="avgTicket">Panier moyen</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grouper par</label>
                            <select id="groupBy" class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="day">Par jour</option>
                                <option value="week">Par semaine</option>
                                <option value="month">Par mois</option>
                            </select>
                        </div>
                    </div>

                    <!-- Résumé des statistiques -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600 mb-1">Total Période</div>
                            <div id="totalPeriod" class="text-2xl font-bold">Chargement...</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-sm text-green-600 mb-1">Moyenne</div>
                            <div id="averagePeriod" class="text-2xl font-bold">Chargement...</div>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <div class="text-sm text-purple-600 mb-1">Évolution</div>
                            <div id="evolution" class="text-2xl font-bold">Chargement...</div>
                        </div>
                    </div>

                    <!-- Container du graphique -->
                    <div id="chartContainer" class="w-full h-[400px]"></div>
                </div>
            </div>

            <!-- Dernières Factures et Clients -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Dernières Factures</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Réf.</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(App\Models\Bill::with('client')->latest()->take(5)->get() as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $bill->reference }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $bill->client->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ number_format($bill->total, 2) }} €
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Derniers Clients</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(App\Models\Client::with('phones')->latest()->take(5)->get() as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $client->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $client->phones->first()?->number ?? '-' }}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let chart = null;

                function initializeChart() {
                    const chartContainer = document.getElementById('chartContainer');
                    const chartType = document.getElementById('chartType').value;
                    const timeRange = document.getElementById('timeRange').value;
                    const metric = document.getElementById('metric').value;
                    const groupBy = document.getElementById('groupBy').value;

                    // Récupérer les données via AJAX
                    fetch(`/api/dashboard/stats?timeRange=${timeRange}&metric=${metric}&groupBy=${groupBy}`)
                        .then(response => response.json())
                        .then(data => {
                            // Configurer le graphique avec Recharts
                            const chartComponent = new recharts.ResponsiveContainer({
                                width: '100%',
                                height: 400
                            });

                            const ChartType = chartType === 'line' ? recharts.LineChart :
                                chartType === 'area' ? recharts.AreaChart :
                                    recharts.BarChart;

                            const chart = new ChartType({
                                data: data,
                                margin: { top: 10, right: 30, left: 0, bottom: 0 }
                            });

                            // Ajouter les composants du graphique
                            chart.appendChild(new recharts.CartesianGrid({
                                strokeDasharray: "3 3"
                            }));

                            chart.appendChild(new recharts.XAxis({
                                dataKey: "date"
                            }));

                            chart.appendChild(new recharts.YAxis());
                            chart.appendChild(new recharts.Tooltip());
                            chart.appendChild(new recharts.Legend());

                            // Ajouter la série de données
                            const DataComponent = chartType === 'line' ? recharts.Line :
                                chartType === 'area' ? recharts.Area :
                                    recharts.Bar;

                            chart.appendChild(new DataComponent({
                                type: "monotone",
                                dataKey: metric,
                                stroke: "#8884d8",
                                fill: "#8884d8",
                                strokeWidth: 2
                            }));

                            // Rendu du graphique
                            chartContainer.innerHTML = '';
                            chartComponent.appendChild(chart);
                            chartContainer.appendChild(chartComponent);

                            // Mettre à jour les statistiques
                            updateStats(data);
                        });
                }

                function updateStats(data) {
                    const metric = document.getElementById('metric').value;
                    const total = data.reduce((sum, item) => sum + item[metric], 0);
                    const average = total / data.length;
                    const evolution = ((data[data.length - 1][metric] - data[0][metric]) / data[0][metric] * 100).toFixed(1);

                    document.getElementById('totalPeriod').textContent = formatValue(total, metric);
                    document.getElementById('averagePeriod').textContent = formatValue(average, metric) + '/jour';
                    document.getElementById('evolution').textContent = `${evolution}%`;
                    document.getElementById('evolution').className =
                        `text-2xl font-bold ${evolution >= 0 ? 'text-green-600' : 'text-red-600'}`;
                }

                function formatValue(value, metric) {
                    switch(metric) {
                        case 'count':
                            return Math.round(value) + ' factures';
                        case 'amount':
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'EUR'
                            }).format(value);
                        case 'avgTicket':
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'EUR'
                            }).format(value);
                        default:
                            return value;
                    }
                }

                // Event listeners pour les contrôles
                document.getElementById('chartType').addEventListener('change', initializeChart);
                document.getElementById('timeRange').addEventListener('change', initializeChart);
                document.getElementById('metric').addEventListener('change', initializeChart);
                document.getElementById('groupBy').addEventListener('change', initializeChart);

                // Initialisation
                initializeChart();
            });
        </script>
    @endpush
</x-app-layout>
