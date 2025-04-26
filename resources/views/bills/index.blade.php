<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-800 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Factures') }}
                </h2>
                <a href="{{ route('bills.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-teal-700 dark:bg-teal-900 dark:text-teal-200 rounded-md hover:bg-teal-50 dark:hover:bg-teal-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouvelle Facture') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('status'))
                        <div class="mb-2 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-2 text-sm rounded" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Filtres améliorés -->
                    <div class="mb-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-sm shadow-sm">
                        <form action="{{ route('bills.index') }}" method="GET" id="search-form" class="space-y-3">
                            <!-- Barre de recherche principale -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                       placeholder="{{ __('N° Facture, Magasin, Client...') }}"
                                       class="w-full pl-10 p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-teal-500 dark:focus:border-teal-500">
                            </div>

                            <!-- Filtres actifs -->
                            <div id="active-filters" class="flex flex-wrap items-center gap-2">
                                <!-- Les filtres actifs seront insérés ici par JavaScript -->
                            </div>

                            <!-- Boutons de filtres -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                                <button type="button" id="shop-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>{{ __('Magasin') }}</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="shop_id" id="shop_id" value="{{ request('shop_id') }}">

                                <button type="button" id="client-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>{{ __('Client') }}</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="client_id" id="client_id" value="{{ request('client_id') }}">

                                <button type="button" id="seller-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>{{ __('Vendeur') }}</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="seller_id" id="seller_id" value="{{ request('seller_id') }}">

                                <button type="button" id="status-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>{{ __('Statut') }}</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="status" id="status" value="{{ request('status') }}">

                                <button type="button" id="period-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-teal-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>{{ __('Période') }}</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="period" id="period" value="{{ request('period') }}">
                            </div>

                            <!-- Options de date avancées (masquées par défaut) -->
                            <div id="date-range-options" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-2 p-2 bg-gray-100 dark:bg-gray-600 rounded-lg mt-2">
                                <div>
                                    <label for="date_from" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Date de début') }}</label>
                                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="date_to" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Date de fin') }}</label>
                                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                           class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <!-- Bouton rechercher -->
                            <div class="flex justify-center md:justify-end mt-4">
                                <button type="submit" id="search-button" class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Rechercher') }}
                                </button>
                                <button type="button" id="advanced-search-toggle" class="ml-2 inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Avancé') }}
                                </button>
                                <button type="button" id="reset-filters" class="ml-2 inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Réinitialiser') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm border dark:border-gray-700 shadow-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/12">{{ __('Facture') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">{{ __('Magasin') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">{{ __('Client') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/8">{{ __('Date') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/8">{{ __('Échéance') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/8">{{ __('Montant') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/8">{{ __('Statut') }}</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/8">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($bills as $bill)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-gray-300">
                                        {{ $bill->reference }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                @if ($bill->shop->logo)
                                                    <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$bill->shop->logo) }}" alt="{{ $bill->shop->name }}">
                                                @else
                                                    <div class="h-6 w-6 rounded-md bg-teal-100 text-teal-700 dark:bg-teal-800 dark:text-teal-300 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                {{ $bill->shop->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                <div class="h-6 w-6 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                {{ $bill->client->name }}
                                                <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $bill->client->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $bill->date ? $bill->date->format('d/m/Y') : '' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : '' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                        <span class="text-teal-600 dark:text-teal-400">{{ number_format($bill->total, 2) }} FCFA</span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if ($bill->status === 'paid')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    {{ __('Payée') }}
                                                </span>
                                        @elseif ($bill->status === 'pending' || $bill->status === 'En attente')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                    {{ __('En attente') }}
                                                </span>
                                        @elseif ($bill->status === 'overdue')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    {{ __('En retard') }}
                                                </span>
                                        @elseif ($bill->status === 'cancelled')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ __('Annulée') }}
                                                </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-right">
                                        <div class="flex justify-end space-x-1">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300" title="{{ __('Voir') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('bills.edit', $bill) }}" class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300" title="{{ __('Modifier') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('bills.download', $bill) }}" class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300" title="{{ __('Télécharger') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <button type="button" onclick="confirmDelete('{{ $bill->id }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="{{ __('Supprimer') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-3 text-center text-gray-500 dark:text-gray-400 text-xs">{{ __('Aucune facture trouvée') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bills->appends(request()->except('page'))->links() }}
                    </div>

                    <!-- Modal de confirmation de suppression -->
                    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white dark:bg-gray-800 p-3">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-8 w-8 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white" id="modal-title">
                                                {{ __('Confirmation de suppression') }}
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('Êtes-vous sûr de vouloir supprimer cette facture ? Cette action est irréversible.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" onclick="cancelDelete()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 dark:focus:ring-offset-gray-800">
                                            {{ __('Annuler') }}
                                        </button>
                                        <form id="deleteForm" method="POST" action="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded hover:bg-red-700 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modaux pour les filtres -->
    <!-- Modal Magasin -->
    <div id="shop-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">{{ __('Sélectionner un magasin') }}</h3>
                            <div class="mb-4">
                                <input type="text" id="shop-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="{{ __('Rechercher...') }}">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="shop-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="shop-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">{{ __('Tous les magasins') }}</li>
                                    @foreach ($shops as $shop)
                                        <li class="shop-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $shop->id }}">{{ $shop->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Client -->
    <div id="client-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">{{ __('Sélectionner un client') }}</h3>
                            <div class="mb-4">
                                <input type="text" id="client-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="{{ __('Rechercher...') }}">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="client-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="client-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">{{ __('Tous les clients') }}</li>
                                    @foreach ($clients as $client)
                                        <li class="client-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $client->id }}">{{ $client->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Vendeur -->
    <div id="seller-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">{{ __('Sélectionner un vendeur') }}</h3>
                            <div class="mb-4">
                                <input type="text" id="seller-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="{{ __('Rechercher...') }}">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="seller-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="seller-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">{{ __('Tous les vendeurs') }}</li>
                                    @foreach ($sellers as $seller)
                                        <li class="seller-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $seller->id }}">{{ $seller->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Statut -->
    <div id="status-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">{{ __('Sélectionner un statut') }}</h3>
                            <div class="mt-2">
                                <ul id="status-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">{{ __('Tous les statuts') }}</li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="paid">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span>
                                            {{ __('Payée') }}
                                        </span>
                                    </li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="pending">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span>
                                            {{ __('En attente') }}
                                        </span>
                                    </li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="overdue">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-red-400 mr-2"></span>
                                            {{ __('En retard') }}
                                        </span>
                                    </li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="cancelled">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                            {{ __('Annulée') }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Période -->
    <div id="period-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">{{ __('Sélectionner une période') }}</h3>
                            <div class="mt-2">
                                <ul id="period-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">{{ __('Toutes les périodes') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="today">{{ __('Aujourd\'hui') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="week">{{ __('Cette semaine') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="month">{{ __('Ce mois') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="quarter">{{ __('Ce trimestre') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="year">{{ __('Cette année') }}</li>
                                    <li class="period-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="custom">
                                        <span class="inline-flex items-center text-teal-600 dark:text-teal-400">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                                            </svg>
                                            {{ __('Période personnalisée') }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript pour les modaux et filtres -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Structure de données pour les filtres sélectionnés
            const filters = {
                shop: { id: '', name: '' },
                client: { id: '', name: '' },
                seller: { id: '', name: '' },
                status: { id: '', name: '' },
                period: { id: '', name: '' }
            };

            // Tableaux pour stocker les données
            const shops = [
                { id: '', name: '{{ __("Tous les magasins") }}' },
                    @foreach ($shops as $shop)
                { id: '{{ $shop->id }}', name: '{{ $shop->name }}' },
                @endforeach
            ];

            const clients = [
                { id: '', name: '{{ __("Tous les clients") }}' },
                    @foreach ($clients as $client)
                { id: '{{ $client->id }}', name: '{{ $client->name }}' },
                @endforeach
            ];

            const sellers = [
                { id: '', name: '{{ __("Tous les vendeurs") }}' },
                    @foreach ($sellers as $seller)
                { id: '{{ $seller->id }}', name: '{{ $seller->name }}' },
                @endforeach
            ];

            const statuses = [
                { id: '', name: '{{ __("Tous les statuts") }}' },
                { id: 'paid', name: '{{ __("Payée") }}' },
                { id: 'pending', name: '{{ __("En attente") }}' },
                { id: 'overdue', name: '{{ __("En retard") }}' },
                { id: 'cancelled', name: '{{ __("Annulée") }}' }
            ];

            const periods = [
                { id: '', name: '{{ __("Toutes les périodes") }}' },
                { id: 'today', name: '{{ __("Aujourd\'hui") }}' },
                { id: 'week', name: '{{ __("Cette semaine") }}' },
                { id: 'month', name: '{{ __("Ce mois") }}' },
                { id: 'quarter', name: '{{ __("Ce trimestre") }}' },
                { id: 'year', name: '{{ __("Cette année") }}' },
                { id: 'custom', name: '{{ __("Période personnalisée") }}' }
            ];

            // Initialiser les filtres actifs depuis les paramètres d'URL
            function initFilters() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('shop_id') && urlParams.get('shop_id')) {
                    const shopId = urlParams.get('shop_id');
                    const shop = shops.find(s => s.id === shopId);
                    if (shop) {
                        filters.shop = shop;
                    }
                }

                if (urlParams.has('client_id') && urlParams.get('client_id')) {
                    const clientId = urlParams.get('client_id');
                    const client = clients.find(c => c.id === clientId);
                    if (client) {
                        filters.client = client;
                    }
                }

                if (urlParams.has('seller_id') && urlParams.get('seller_id')) {
                    const sellerId = urlParams.get('seller_id');
                    const seller = sellers.find(s => s.id === sellerId);
                    if (seller) {
                        filters.seller = seller;
                    }
                }

                if (urlParams.has('status') && urlParams.get('status')) {
                    const statusId = urlParams.get('status');
                    const status = statuses.find(s => s.id === statusId);
                    if (status) {
                        filters.status = status;
                    }
                }

                if (urlParams.has('period') && urlParams.get('period')) {
                    const periodId = urlParams.get('period');
                    const period = periods.find(p => p.id === periodId);
                    if (period) {
                        filters.period = period;

                        // Si période personnalisée, afficher les champs de date
                        if (periodId === 'custom') {
                            document.getElementById('date-range-options').classList.remove('hidden');
                        }
                    }
                }

                // Afficher les dates si elles sont présentes dans l'URL
                if (urlParams.has('date_from') || urlParams.has('date_to')) {
                    document.getElementById('date-range-options').classList.remove('hidden');
                }

                updateFilterButtons();
                updateActiveFilters();
            }

            // Mettre à jour les boutons de filtre
            function updateFilterButtons() {
                // Shop
                const shopButton = document.getElementById('shop-filter-btn');
                if (filters.shop.id) {
                    shopButton.classList.add('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    shopButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    shopButton.querySelector('span').textContent = filters.shop.name;
                    document.getElementById('shop_id').value = filters.shop.id;
                } else {
                    shopButton.classList.remove('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    shopButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    shopButton.querySelector('span').textContent = '{{ __("Magasin") }}';
                    document.getElementById('shop_id').value = '';
                }

                // Client
                const clientButton = document.getElementById('client-filter-btn');
                if (filters.client.id) {
                    clientButton.classList.add('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    clientButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    clientButton.querySelector('span').textContent = filters.client.name;
                    document.getElementById('client_id').value = filters.client.id;
                } else {
                    clientButton.classList.remove('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    clientButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    clientButton.querySelector('span').textContent = '{{ __("Client") }}';
                    document.getElementById('client_id').value = '';
                }

                // Seller
                const sellerButton = document.getElementById('seller-filter-btn');
                if (filters.seller.id) {
                    sellerButton.classList.add('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    sellerButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    sellerButton.querySelector('span').textContent = filters.seller.name;
                    document.getElementById('seller_id').value = filters.seller.id;
                } else {
                    sellerButton.classList.remove('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    sellerButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    sellerButton.querySelector('span').textContent = '{{ __("Vendeur") }}';
                    document.getElementById('seller_id').value = '';
                }

                // Status
                const statusButton = document.getElementById('status-filter-btn');
                if (filters.status.id) {
                    statusButton.classList.add('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    statusButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    statusButton.querySelector('span').textContent = filters.status.name;
                    document.getElementById('status').value = filters.status.id;
                } else {
                    statusButton.classList.remove('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    statusButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    statusButton.querySelector('span').textContent = '{{ __("Statut") }}';
                    document.getElementById('status').value = '';
                }

                // Period
                const periodButton = document.getElementById('period-filter-btn');
                if (filters.period.id) {
                    periodButton.classList.add('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    periodButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    periodButton.querySelector('span').textContent = filters.period.name;
                    document.getElementById('period').value = filters.period.id;
                } else {
                    periodButton.classList.remove('bg-teal-100', 'text-teal-700', 'border-teal-300', 'dark:bg-teal-900', 'dark:text-teal-300', 'dark:border-teal-700');
                    periodButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300', 'dark:bg-gray-700', 'dark:text-white', 'dark:border-gray-600');
                    periodButton.querySelector('span').textContent = '{{ __("Période") }}';
                    document.getElementById('period').value = '';
                }
            }

            // Mettre à jour l'affichage des filtres actifs
            function updateActiveFilters() {
                const activeFiltersContainer = document.getElementById('active-filters');
                activeFiltersContainer.innerHTML = '';

                if (!filters.shop.id && !filters.client.id && !filters.seller.id && !filters.status.id && !filters.period.id
                    && !document.getElementById('search').value
                    && !document.getElementById('date_from').value
                    && !document.getElementById('date_to').value) {
                    return;
                }

                // Ajouter un label pour les filtres actifs
                const label = document.createElement('span');
                label.className = 'text-xs font-medium text-gray-700 dark:text-gray-300';
                label.textContent = '{{ __("Filtres actifs:") }}';
                activeFiltersContainer.appendChild(label);

                // Fonction pour créer un tag de filtre
                function createFilterTag(filter, key) {
                    if (!filter.id) return null;

                    const tag = document.createElement('span');
                    tag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200';

                    const text = document.createElement('span');
                    text.textContent = getFilterLabel(key) + ': ' + filter.name;

                    const closeBtn = document.createElement('button');
                    closeBtn.className = 'ml-1 text-teal-500 hover:text-teal-700 dark:text-teal-300 dark:hover:text-teal-100 focus:outline-none';
                    closeBtn.innerHTML = `
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;

                    closeBtn.addEventListener('click', function() {
                        filters[key] = { id: '', name: '' };
                        updateFilterButtons();
                        updateActiveFilters();

                        // Si on supprime une période personnalisée, on masque les champs de date
                        if (key === 'period' && filter.id === 'custom') {
                            document.getElementById('date-range-options').classList.add('hidden');
                            document.getElementById('date_from').value = '';
                            document.getElementById('date_to').value = '';
                        }
                    });

                    tag.appendChild(text);
                    tag.appendChild(closeBtn);

                    return tag;
                }

                // Fonction pour obtenir le label du filtre
                function getFilterLabel(key) {
                    switch(key) {
                        case 'shop': return '{{ __("Magasin") }}';
                        case 'client': return '{{ __("Client") }}';
                        case 'seller': return '{{ __("Vendeur") }}';
                        case 'status': return '{{ __("Statut") }}';
                        case 'period': return '{{ __("Période") }}';
                        default: return key;
                    }
                }

                // Ajouter filtre de recherche si présent
                if (document.getElementById('search').value) {
                    const searchTag = document.createElement('span');
                    searchTag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200';

                    const text = document.createElement('span');
                    text.textContent = '{{ __("Recherche") }}: "' + document.getElementById('search').value + '"';

                    const closeBtn = document.createElement('button');
                    closeBtn.className = 'ml-1 text-teal-500 hover:text-teal-700 dark:text-teal-300 dark:hover:text-teal-100 focus:outline-none';
                    closeBtn.innerHTML = `
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;

                    closeBtn.addEventListener('click', function() {
                        document.getElementById('search').value = '';
                        updateActiveFilters();
                    });

                    searchTag.appendChild(text);
                    searchTag.appendChild(closeBtn);
                    activeFiltersContainer.appendChild(searchTag);
                }

                // Ajouter filtres de dates si présents
                if (document.getElementById('date_from').value || document.getElementById('date_to').value) {
                    const dateTag = document.createElement('span');
                    dateTag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200';

                    const dateFrom = document.getElementById('date_from').value;
                    const dateTo = document.getElementById('date_to').value;

                    const text = document.createElement('span');
                    if (dateFrom && dateTo) {
                        text.textContent = '{{ __("Date") }}: ' + dateFrom + ' - ' + dateTo;
                    } else if (dateFrom) {
                        text.textContent = '{{ __("Date à partir de") }}: ' + dateFrom;
                    } else {
                        text.textContent = '{{ __("Date jusqu\'à") }}: ' + dateTo;
                    }

                    const closeBtn = document.createElement('button');
                    closeBtn.className = 'ml-1 text-teal-500 hover:text-teal-700 dark:text-teal-300 dark:hover:text-teal-100 focus:outline-none';
                    closeBtn.innerHTML = `
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;

                    closeBtn.addEventListener('click', function() {
                        document.getElementById('date_from').value = '';
                        document.getElementById('date_to').value = '';
                        updateActiveFilters();
                    });

                    dateTag.appendChild(text);
                    dateTag.appendChild(closeBtn);
                    activeFiltersContainer.appendChild(dateTag);
                }

                // Ajouter les tags de filtres
                const shopTag = createFilterTag(filters.shop, 'shop');
                if (shopTag) activeFiltersContainer.appendChild(shopTag);

                const clientTag = createFilterTag(filters.client, 'client');
                if (clientTag) activeFiltersContainer.appendChild(clientTag);

                const sellerTag = createFilterTag(filters.seller, 'seller');
                if (sellerTag) activeFiltersContainer.appendChild(sellerTag);

                const statusTag = createFilterTag(filters.status, 'status');
                if (statusTag) activeFiltersContainer.appendChild(statusTag);

                const periodTag = createFilterTag(filters.period, 'period');
                if (periodTag) activeFiltersContainer.appendChild(periodTag);

                // Ajouter un bouton pour effacer tous les filtres
                if (activeFiltersContainer.childNodes.length > 1) {
                    const resetBtn = document.createElement('button');
                    resetBtn.className = 'ml-2 text-xs text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300';
                    resetBtn.textContent = '{{ __("Effacer tout") }}';
                    resetBtn.addEventListener('click', function() {
                        resetAllFilters();
                    });
                    activeFiltersContainer.appendChild(resetBtn);
                }
            }

            // Réinitialiser tous les filtres
            function resetAllFilters() {
                filters.shop = { id: '', name: '' };
                filters.client = { id: '', name: '' };
                filters.seller = { id: '', name: '' };
                filters.status = { id: '', name: '' };
                filters.period = { id: '', name: '' };
                document.getElementById('search').value = '';
                document.getElementById('date_from').value = '';
                document.getElementById('date_to').value = '';
                document.getElementById('date-range-options').classList.add('hidden');
                updateFilterButtons();
                updateActiveFilters();
            }

            // Fonction pour rechercher dans une liste
            function filterList(searchText, items) {
                if (!searchText) {
                    items.forEach(item => {
                        item.style.display = '';
                    });
                    return;
                }

                searchText = searchText.toLowerCase();

                items.forEach(item => {
                    const itemText = item.textContent.toLowerCase();
                    if (itemText.includes(searchText)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Gestionnaires pour les boutons de filtres
            document.getElementById('shop-filter-btn').addEventListener('click', function() {
                document.getElementById('shop-modal').classList.remove('hidden');
            });

            document.getElementById('client-filter-btn').addEventListener('click', function() {
                document.getElementById('client-modal').classList.remove('hidden');
            });

            document.getElementById('seller-filter-btn').addEventListener('click', function() {
                document.getElementById('seller-modal').classList.remove('hidden');
            });

            document.getElementById('status-filter-btn').addEventListener('click', function() {
                document.getElementById('status-modal').classList.remove('hidden');
            });

            document.getElementById('period-filter-btn').addEventListener('click', function() {
                document.getElementById('period-modal').classList.remove('hidden');
            });

            // Fermer tous les modaux
            document.querySelectorAll('.modal-close').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('[id$="-modal"]').forEach(modal => {
                        modal.classList.add('hidden');
                    });
                });
            });

            // Cliquer à l'extérieur ferme les modaux
            document.querySelectorAll('[id$="-modal"]').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            });

            // Gestionnaires de recherche
            document.getElementById('shop-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.shop-item');
                filterList(searchText, items);
            });

            document.getElementById('client-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.client-item');
                filterList(searchText, items);
            });

            document.getElementById('seller-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.seller-item');
                filterList(searchText, items);
            });

            // Gestionnaires de sélection
            document.querySelectorAll('.shop-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent.trim();
                    filters.shop = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('shop-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.client-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent.trim();
                    filters.client = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('client-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.seller-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent.trim();
                    filters.seller = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('seller-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.status-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const statusNames = {
                        '': '{{ __("Tous les statuts") }}',
                        'paid': '{{ __("Payée") }}',
                        'pending': '{{ __("En attente") }}',
                        'overdue': '{{ __("En retard") }}',
                        'cancelled': '{{ __("Annulée") }}'
                    };
                    const name = statusNames[id] || id;
                    filters.status = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('status-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.period-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const periodNames = {
                        '': '{{ __("Toutes les périodes") }}',
                        'today': '{{ __("Aujourd\'hui") }}',
                        'week': '{{ __("Cette semaine") }}',
                        'month': '{{ __("Ce mois") }}',
                        'quarter': '{{ __("Ce trimestre") }}',
                        'year': '{{ __("Cette année") }}',
                        'custom': '{{ __("Période personnalisée") }}'
                    };
                    const name = periodNames[id] || id;
                    filters.period = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('period-modal').classList.add('hidden');

                    // Si période personnalisée, afficher les champs de date
                    if (id === 'custom') {
                        document.getElementById('date-range-options').classList.remove('hidden');
                    } else if (id === '') {
                        document.getElementById('date-range-options').classList.add('hidden');
                        document.getElementById('date_from').value = '';
                        document.getElementById('date_to').value = '';
                    }
                });
            });

            // Bouton de recherche avancée
            document.getElementById('advanced-search-toggle').addEventListener('click', function() {
                const dateRangeOptions = document.getElementById('date-range-options');
                if (dateRangeOptions.classList.contains('hidden')) {
                    dateRangeOptions.classList.remove('hidden');
                } else {
                    dateRangeOptions.classList.add('hidden');
                }
            });

            // Gestionnaire pour le bouton de réinitialisation
            document.getElementById('reset-filters').addEventListener('click', function() {
                resetAllFilters();
            });

            // Gestionnaire pour le formulaire
            document.getElementById('search-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Créer un FormData à partir du formulaire
                const formData = new FormData(this);

                // Construire l'URL avec seulement les paramètres non-vides
                const params = new URLSearchParams();

                for (const [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        params.append(key, value);
                    }
                }

                // Rediriger vers la nouvelle URL
                window.location.href = '{{ route('bills.index') }}' + (params.toString() ? '?' + params.toString() : '');
            });

            // Fonction supplémentaire pour le modal de suppression
            window.confirmDelete = function(billId) {
                document.getElementById('deleteForm').action = `{{ url('bills') }}/${billId}`;
                document.getElementById('deleteModal').classList.remove('hidden');
            };

            window.cancelDelete = function() {
                document.getElementById('deleteModal').classList.add('hidden');
            };

            // Initialiser l'interface
            initFilters();
        });
    </script>
</x-app-layout>
