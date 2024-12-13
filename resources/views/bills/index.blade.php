<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Factures') }}
            </h2>
            <a href="{{ route('bills.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle Facture
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filtres et recherche -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                            <select id="client_filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Tous les clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select id="status_filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="paid">Payée</option>
                                <option value="pending">En attente</option>
                                <option value="overdue">En retard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
                            <select id="period_filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Toutes les périodes</option>
                                <option value="current_month">Mois en cours</option>
                                <option value="last_month">Mois dernier</option>
                                <option value="current_year">Année en cours</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                            <input type="text" id="search" placeholder="N° facture, référence..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <!-- Recherche avancée (déroulant) -->
                    <div class="mb-6">
                        <button id="toggle_advanced_search" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Recherche avancée
                        </button>
                        <div id="advanced_search_fields" class="hidden mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                                <input type="date" id="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                                <input type="date" id="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Montant minimum</label>
                                <input type="number" id="min_amount" placeholder="Montant minimum" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Montant maximum</label>
                                <input type="number" id="max_amount" placeholder="Montant maximum" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Rechercher -->
                    <div class="mb-6">
                        <button id="search_button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Rechercher
                        </button>
                    </div>

                    <!-- Filtres actifs -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Filtres actifs</h3>
                        <div id="active_filters" class="flex flex-wrap gap-2">
                            <!-- Les filtres actifs seront affichés ici -->
                        </div>
                    </div>

                    <!-- Tableau des factures -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Référence
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total HT
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TVA
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total TTC
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 bg-gray-50"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="bills-table-body">
                            @foreach($bills as $bill)
                                <tr class="bill-row" data-client="{{ $bill->client->id }}" data-status="{{ $bill->status }}" data-date="{{ $bill->date->format('Y-m-d') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                               ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($bill->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('bills.edit', $bill) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('bills.download', $bill) }}" class="text-gray-600 hover:text-gray-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
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

                function displayActiveFilters() {
                    activeFilters.innerHTML = '';
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.forEach((value, key) => {
                        const filterElement = document.createElement('div');
                        filterElement.classList.add('bg-gray-100', 'text-gray-800', 'px-3', 'py-1', 'rounded-full', 'flex', 'items-center');
                        filterElement.innerHTML = `
                            <span>${key}: ${value}</span>
                            <button class="ml-2 text-red-500" onclick="removeFilter('${key}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        `;
                        activeFilters.appendChild(filterElement);
                    });
                }

                function removeFilter(key) {
                    const url = new URL(window.location);
                    url.searchParams.delete(key);
                    window.location.href = url.toString();
                }

                searchButton.addEventListener('click', updateQueryString);
                toggleAdvancedSearchButton.addEventListener('click', function() {
                    advancedSearchFields.classList.toggle('hidden');
                });

                displayActiveFilters();
            });
        </script>
    @endpush
</x-app-layout>
