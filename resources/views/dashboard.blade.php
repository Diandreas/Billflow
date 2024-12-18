<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques sommaires -->
{{--            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">--}}
{{--                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--                    <div class="p-6">--}}
{{--                        <div class="text-sm font-medium text-gray-500">Total Factures</div>--}}
{{--                        <div class="text-2xl font-bold text-gray-900">{{ $globalStats['totalBills'] }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--                    <div class="p-6">--}}
{{--                        <div class="text-sm font-medium text-gray-500">Ce mois</div>--}}
{{--                        <div class="text-2xl font-bold text-gray-900">{{ $globalStats['monthlyBills'] }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--                    <div class="p-6">--}}
{{--                        <div class="text-sm font-medium text-gray-500">Revenu Total</div>--}}
{{--                        <div class="text-2xl font-bold text-gray-900">{{ $globalStats['totalRevenue'] }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--                    <div class="p-6">--}}
{{--                        <div class="text-sm font-medium text-gray-500">Panier Moyen</div>--}}
{{--                        <div class="text-2xl font-bold text-gray-900">{{ $globalStats['averageTicket'] }}</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Graphique -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="chartType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="bar">Barres</option>
                                <option value="line">Lignes</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                            <select id="timeRange" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="month" selected>Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" id="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   max="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" id="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                   max="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Métrique</label>
                            <select id="metric" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="count">Nombre de factures</option>
                                <option value="amount">Montant total</option>
                            </select>
                        </div>
                    </div>

                    <!-- Statistiques dynamiques de la plage -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Revenu Total</div>
                            <div id="totalRevenueSpan" class="text-xl font-bold text-gray-900">0 FCFA</div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Panier Moyen</div>
                            <div id="averageTicketSpan" class="text-xl font-bold text-gray-900">0 FCFA</div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Nombre de Factures</div>
                            <div id="billCountSpan" class="text-xl font-bold text-gray-900">0</div>
                        </div>
                    </div>

                    <div class="w-full" style="height: 400px;">
                        <canvas id="statsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables (restent inchangées) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Dernières Factures -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-xl mb-4">Dernières Factures</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($latestBills as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bill->reference }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bill->client->name }}</td>
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
                    <div class="p-6">
                        <h3 class="font-semibold text-xl mb-4">Top Clients</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factures</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topClients as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->total }}</td>
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
                let myChart = null;

                function formatMoney(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        style: 'decimal',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value) + ' FCFA';
                }

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
                            console.log('Data received:', data); // Ajoutez cette ligne pour déboguer

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
                                                    if (metric === 'count') return value;
                                                    return formatMoney(value);
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.dataset.label || '';
                                                    if (label) {
                                                        label += ': ';
                                                    }
                                                    if (metric === 'count') {
                                                        label += context.parsed.y;
                                                    } else {
                                                        label += formatMoney(context.parsed.y);
                                                    }
                                                    return label;
                                                }
                                            }
                                        }
                                    },
                                    interaction: {
                                        intersect: false,
                                        mode: 'index'
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            const chartContainer = document.getElementById('statsChart');
                            chartContainer.parentElement.innerHTML =
                                "<div class='text-center text-gray-500 mt-4'>" +
                                "<p>Erreur lors du chargement des données</p>" +
                                "<button onclick='updateChart()' class='mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md'>" +
                                "Réessayer" +
                                "</button>" +
                                "</div>";
                        });
                }

                // Event listeners
                document.getElementById('chartType').addEventListener('change', updateChart);
                document.getElementById('timeRange').addEventListener('change', function() {
                    if (this.value !== 'custom') {
                        document.getElementById('startDate').value = '';
                        document.getElementById('endDate').value = '';
                    }
                    updateChart();
                });
                document.getElementById('startDate').addEventListener('change', updateChart);
                document.getElementById('endDate').addEventListener('change', updateChart);
                document.getElementById('metric').addEventListener('change', updateChart);

                // Initialisation
                updateChart();
            });


        </script>
    @endpush
</x-app-layout>

