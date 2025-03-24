<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                    Factures de {{ $client->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Historique complet des factures') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('clients.bills.create', $client) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                    <i class="bi bi-plus-lg mr-2"></i>
                    Nouvelle Facture
                </a>
                <a href="{{ route('clients.show', $client) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Retour au client
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="p-8">
                    <!-- Recherche -->
                    <div class="mb-6 relative">
                        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchBills"
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Rechercher une facture...">
                    </div>
                    
                    <!-- Filtres -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-check2-circle mr-2"></i>Statut
                            </label>
                            <select id="status_filter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="paid">Payée</option>
                                <option value="pending">En attente</option>
                                <option value="overdue">En retard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-calendar3 mr-2"></i>Période
                            </label>
                            <select id="period_filter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Toutes les périodes</option>
                                <option value="current_month">Mois en cours</option>
                                <option value="last_month">Mois dernier</option>
                                <option value="current_year">Année en cours</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="reset_filters" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors duration-150">
                                <i class="bi bi-x-circle mr-2"></i>Réinitialiser les filtres
                            </button>
                        </div>
                    </div>

                    <div id="noResultsMessage" class="py-8 text-center text-gray-500 hidden">
                        <p class="mb-2 font-medium">Aucune facture ne correspond à vos critères</p>
                        <p>Essayez de modifier vos filtres ou votre recherche</p>
                    </div>

                    @if($client->bills->count() > 0)
                        <div class="overflow-x-auto rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span class="flex items-center">Référence</span>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span class="flex items-center">Date</span>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span class="flex items-center">Produits</span>
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        HT
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        TVA
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Total TTC
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="bi bi-check2-circle mr-1"></i>Statut
                                    </th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="bills-table-body">
                                @foreach($client->bills as $bill)
                                    <tr class="bill-row hover:bg-gray-50 transition-colors duration-150"
                                        data-status="{{ $bill->status }}"
                                        data-date="{{ $bill->date->format('Y-m-d') }}"
                                        data-reference="{{ strtolower($bill->reference) }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $bill->reference }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $bill->date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 line-clamp-1">
                                                {{ $bill->products->count() }} produit(s)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            {{ number_format($bill->total - $bill->tax_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            {{ number_format($bill->tax_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                            {{ number_format($bill->total, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($bill->status == 'paid')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Payée
                                                </span>
                                            @elseif($bill->status == 'pending')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @elseif($bill->status == 'overdue')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    En retard
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex justify-end space-x-3">
                                                <a href="{{ route('bills.show', $bill) }}"
                                                   class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                                   title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('bills.edit', $bill) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                                                   title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('bills.download', $bill) }}"
                                                   class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                                   title="Télécharger">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-white rounded-lg p-8 text-center">
                            <div class="text-indigo-500 mb-4">
                                <i class="bi bi-receipt-cutoff text-6xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">{{ __('Aucune facture') }}</h3>
                            <p class="text-gray-500 mb-6">{{ __('Ce client n\'a pas encore de factures.') }}</p>
                            <a href="{{ route('bills.create', ['client_id' => $client->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                                <i class="bi bi-plus-lg mr-2"></i>
                                {{ __('Créer une première facture') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <style>
            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchBills');
            const statusFilter = document.getElementById('status_filter');
            const periodFilter = document.getElementById('period_filter');
            const resetButton = document.getElementById('reset_filters');
            const billRows = document.querySelectorAll('.bill-row');
            const noResultsMessage = document.getElementById('noResultsMessage');

            // Fonction pour filtrer les factures
            function filterBills() {
                const searchTerm = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                const period = periodFilter.value;
                
                let visibleCount = 0;
                
                // Dates pour le filtre de période
                const today = new Date();
                const currentMonth = today.getMonth();
                const currentYear = today.getFullYear();
                
                // Date du premier jour du mois dernier
                const lastMonthStart = new Date(currentYear, currentMonth - 1, 1);
                // Date du dernier jour du mois dernier
                const lastMonthEnd = new Date(currentYear, currentMonth, 0);
                
                // Date du premier jour de l'année en cours
                const currentYearStart = new Date(currentYear, 0, 1);
                
                billRows.forEach(row => {
                    const billDate = new Date(row.dataset.date);
                    const billStatus = row.dataset.status;
                    const billReference = row.dataset.reference;
                    
                    // Condition de recherche
                    const matchesSearch = searchTerm === '' || 
                                         billReference.includes(searchTerm);
                    
                    // Condition de statut
                    const matchesStatus = status === '' || 
                                         billStatus === status;
                    
                    // Condition de période
                    let matchesPeriod = true;
                    if (period === 'current_month') {
                        matchesPeriod = billDate.getMonth() === currentMonth && 
                                       billDate.getFullYear() === currentYear;
                    } else if (period === 'last_month') {
                        matchesPeriod = billDate >= lastMonthStart && billDate <= lastMonthEnd;
                    } else if (period === 'current_year') {
                        matchesPeriod = billDate.getFullYear() === currentYear;
                    }
                    
                    // Afficher ou masquer la ligne
                    if (matchesSearch && matchesStatus && matchesPeriod) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Afficher le message si aucun résultat
                if (visibleCount === 0) {
                    noResultsMessage.style.display = 'block';
                } else {
                    noResultsMessage.style.display = 'none';
                }
            }
            
            // Événements
            searchInput.addEventListener('input', filterBills);
            statusFilter.addEventListener('change', filterBills);
            periodFilter.addEventListener('change', filterBills);
            
            // Réinitialiser les filtres
            resetButton.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = '';
                periodFilter.value = '';
                filterBills();
            });
        });
    </script>
    @endpush
</x-app-layout> 