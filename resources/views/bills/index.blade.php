<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
                {{ __('Factures') }}
            </h2>
            <a href="{{ route('bills.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                <i class="bi bi-plus-lg mr-2"></i>
                Nouvelle Facture
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="p-8">
                    <!-- Filtres et recherche -->
                    <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-people mr-2"></i>Client
                            </label>
                            <select id="client_filter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Tous les clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-search mr-2"></i>Recherche
                            </label>
                            <div class="relative">
                                <input type="text" id="search" placeholder="N° facture, référence..."
                                       class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Recherche avancée -->
                    <div class="mb-8">
                        <button id="toggle_advanced_search" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-150">
                            <i class="bi bi-funnel mr-2"></i>
                            Recherche avancée
                        </button>
                        <div id="advanced_search_fields" class="hidden mt-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-calendar2-minus mr-2"></i>Date de début
                                    </label>
                                    <input type="date" id="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-calendar2-plus mr-2"></i>Date de fin
                                    </label>
                                    <input type="date" id="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-currency-dollar mr-2"></i>Montant minimum
                                    </label>
                                    <input type="number" id="min_amount" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="bi bi-currency-dollar mr-2"></i>Montant maximum
                                    </label>
                                    <input type="number" id="max_amount" placeholder="999999" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Rechercher -->
                    <div class="mb-8">
                        <button id="search_button" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-150">
                            <i class="bi bi-search mr-2"></i>
                            Rechercher
                        </button>
                    </div>

                    <!-- Filtres actifs -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-3 flex items-center">
                            <i class="bi bi-funnel-fill mr-2"></i>Filtres actifs
                        </h3>
                        <div id="active_filters" class="flex flex-wrap gap-2">
                            <!-- Les filtres actifs seront affichés ici -->
                        </div>
                    </div>

                    <!-- Tableau des factures -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <i class="bi bi-hash mr-1"></i>Référence
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <i class="bi bi-person mr-1"></i>Client
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <i class="bi bi-calendar2 mr-1"></i>Date
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Total HT
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
                            @foreach($bills as $bill)
                                <tr class="bill-row hover:bg-gray-50 transition-colors duration-150"
                                    data-client="{{ $bill->client->id }}"
                                    data-status="{{ $bill->status }}"
                                    data-date="{{ $bill->date->format('Y-m-d') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $bill->reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->client->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->date->format('d/m/Y') }}
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
                                        <span class="px-3 py-1 inline-flex items-center text-sm font-semibold rounded-full
                                            {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                               ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800') }}">
                                            <i class="bi {{ $bill->status === 'paid' ? 'bi-check-circle' :
                                                           ($bill->status === 'pending' ? 'bi-clock' :
                                                            'bi-exclamation-circle') }} mr-1"></i>
                                            {{ ucfirst($bill->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end space-x-3">
                                            <a href="{{ route('bills.show', $bill) }}"
                                               class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                               title="Voir">
                                                <i class="bi bi-eye text-lg"></i>
                                            </a>
                                            <a href="{{ route('bills.edit', $bill) }}"
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                               title="Modifier">
                                                <i class="bi bi-pencil text-lg"></i>
                                            </a>
                                            <a href="{{ route('bills.download', $bill) }}"
                                               class="text-gray-600 hover:text-gray-900 transition-colors duration-150"
                                               title="Télécharger">
                                                <i class="bi bi-download text-lg"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $bills->links() }}
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
                const clientFilter = document.getElementById('client_filter');
                const statusFilter = document.getElementById('status_filter');
                const periodFilter = document.getElementById('period_filter');
                const searchInput = document.getElementById('search');
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');
                const minAmountInput = document.getElementById('min_amount');
                const maxAmountInput = document.getElementById('max_amount');
                const searchButton = document.getElementById('search_button');
                const toggleAdvancedSearchButton = document.getElementById('toggle_advanced_search');
                const advancedSearchFields = document.getElementById('advanced_search_fields');
                const activeFilters = document.getElementById('active_filters');

                // Séparer les champs de recherche standard et avancée
                const standardFilterInputs = [
                    clientFilter,
                    statusFilter,
                    periodFilter,
                    searchInput
                ];

                const advancedFilterInputs = [
                    startDateInput,
                    endDateInput,
                    minAmountInput,
                    maxAmountInput
                ];

                function updateQueryString() {
                    const url = new URL(window.location);
                    const params = {
                        client: clientFilter.value,
                        status: statusFilter.value,
                        period: periodFilter.value,
                        search: searchInput.value,
                        start_date: startDateInput.value,
                        end_date: endDateInput.value,
                        min_amount: minAmountInput.value,
                        max_amount: maxAmountInput.value
                    };

                    Object.keys(params).forEach(key => {
                        if (params[key]) {
                            url.searchParams.set(key, params[key]);
                        } else {
                            url.searchParams.delete(key);
                        }
                    });

                    window.location.href = url.toString();
                }

                // Les filtres standard déclenchent toujours la recherche immédiatement
                standardFilterInputs.forEach(input => {
                    input.addEventListener('change', updateQueryString);
                });

                // Appliquer les filtres lors de l'appui sur Entrée dans le champ de recherche standard
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        updateQueryString();
                    }
                });

                // Pour la recherche avancée, on attend le clic sur le bouton Rechercher
                searchButton.addEventListener('click', function() {
                    // Vérifier si au moins un champ de recherche avancée est rempli
                    const hasAdvancedFilters = advancedFilterInputs.some(input => input.value !== '');
                    if (hasAdvancedFilters) {
                        updateQueryString();
                    } else {
                        // Si aucun champ avancé n'est rempli, utiliser les filtres standard
                        updateQueryString();
                    }
                });

                // Le reste du code reste identique...
                function displayActiveFilters() {
                    activeFilters.innerHTML = '';
                    const urlParams = new URLSearchParams(window.location.search);
                    const filterLabels = {
                        client: 'Client',
                        status: 'Statut',
                        period: 'Période',
                        search: 'Recherche',
                        start_date: 'Date début',
                        end_date: 'Date fin',
                        min_amount: 'Montant min',
                        max_amount: 'Montant max'
                    };

                    const filterIcons = {
                        client: 'bi-people',
                        status: 'bi-check2-circle',
                        period: 'bi-calendar3',
                        search: 'bi-search',
                        start_date: 'bi-calendar2-minus',
                        end_date: 'bi-calendar2-plus',
                        min_amount: 'bi-currency-dollar',
                        max_amount: 'bi-currency-dollar'
                    };

                    urlParams.forEach((value, key) => {
                        if (filterLabels[key]) {
                            const filterElement = document.createElement('div');
                            filterElement.classList.add(
                                'bg-gray-100',
                                'hover:bg-gray-200',
                                'text-gray-800',
                                'px-4',
                                'py-2',
                                'rounded-lg',
                                'flex',
                                'items-center',
                                'transition-colors',
                                'duration-150'
                            );
                            filterElement.innerHTML = `
                                <i class="bi ${filterIcons[key]} mr-2"></i>
                                <span>${filterLabels[key]}: ${value}</span>
                                <button class="ml-2 text-gray-500 hover:text-red-500 transition-colors duration-150"
                                        onclick="removeFilter('${key}')"
                                        title="Supprimer le filtre">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            `;
                            activeFilters.appendChild(filterElement);
                        }
                    });

                    // Ajouter le bouton "Effacer tous les filtres" s'il y a des filtres actifs
                    if (urlParams.toString()) {
                        const clearAllButton = document.createElement('div');
                        clearAllButton.classList.add(
                            'bg-red-100',
                            'hover:bg-red-200',
                            'text-red-800',
                            'px-4',
                            'py-2',
                            'rounded-lg',
                            'flex',
                            'items-center',
                            'cursor-pointer',
                            'transition-colors',
                            'duration-150'
                        );
                        clearAllButton.innerHTML = `
                            <i class="bi bi-trash mr-2"></i>
                            <span>Effacer tous les filtres</span>
                        `;
                        clearAllButton.addEventListener('click', clearAllFilters);
                        activeFilters.appendChild(clearAllButton);
                    }
                }

                function removeFilter(key) {
                    const url = new URL(window.location);
                    url.searchParams.delete(key);
                    window.location.href = url.toString();
                }

                function clearAllFilters() {
                    window.location.href = window.location.pathname;
                }

                // Gestionnaires d'événements
                searchButton.addEventListener('click', updateQueryString);
                toggleAdvancedSearchButton.addEventListener('click', function() {
                    const isHidden = advancedSearchFields.classList.contains('hidden');
                    advancedSearchFields.classList.toggle('hidden');

                    // Animation de rotation de l'icône
                    const icon = this.querySelector('i');
                    if (isHidden) {
                        this.classList.add('bg-gray-200');
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        this.classList.remove('bg-gray-200');
                        icon.style.transform = 'rotate(0deg)';
                    }
                });

                // Initialisation
                displayActiveFilters();

                // Appliquer les filtres lors de la modification des champs
                const filterInputs = [
                    clientFilter,
                    statusFilter,
                    periodFilter,
                    searchInput,
                    startDateInput,
                    endDateInput,
                    minAmountInput,
                    maxAmountInput
                ];

                filterInputs.forEach(input => {
                    input.addEventListener('change', updateQueryString);
                });

                // Appliquer les filtres lors de l'appui sur Entrée dans le champ de recherche
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        updateQueryString();
                    }
                });
            });

        </script>
    @endpush
</x-app-layout>
