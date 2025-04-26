<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-900 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Gestion des Trocs
                </h2>
                <a href="{{ route('barters.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 dark:bg-indigo-950 dark:text-indigo-200 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouveau
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('success'))
                        <div class="mb-2 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-2 text-sm rounded" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-2 bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-2 text-sm rounded" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="mb-3 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-sm shadow-sm">
                        <form action="{{ route('barters.index') }}" method="GET" id="search-form" class="space-y-3">
                            <!-- Barre de recherche principale -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                       placeholder="Rechercher par référence, client, articles..."
                                       class="w-full pl-10 p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                            </div>

                            <!-- Filtres actifs -->
                            <div id="active-filters" class="flex flex-wrap items-center gap-2">
                                <!-- Les filtres actifs seront insérés ici par JavaScript -->
                            </div>

                            <!-- Boutons de filtres -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                                <button type="button" id="client-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>Client</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="client_id" id="client_id" value="{{ request('client_id') }}">

                                <button type="button" id="shop-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>Boutique</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="shop_id" id="shop_id" value="{{ request('shop_id') }}">

                                <button type="button" id="seller-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>Vendeur</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="seller_id" id="seller_id" value="{{ request('seller_id') }}">

                                <button type="button" id="status-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>Statut</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="status" id="status" value="{{ request('status') }}">

                                <button type="button" id="type-filter-btn" class="filter-button text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-3 py-2 text-center inline-flex items-center justify-between dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span>Type</span>
                                    <svg class="w-2.5 h-2.5 ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                    </svg>
                                </button>
                                <input type="hidden" name="type" id="type" value="{{ request('type') }}">
                            </div>

                            <!-- Bouton rechercher -->
                            <div class="flex justify-center md:justify-end mt-4">
                                <button type="submit" id="search-button" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    Rechercher
                                </button>
                                <button type="button" id="reset-filters" class="ml-2 inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Réinitialiser
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm border dark:border-gray-700 shadow-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Réf</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Boutique</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendeur</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($barters as $barter)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $barter->reference }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $barter->client->name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $barter->shop->name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $barter->seller->name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ $barter->type == 'same_type' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' }}">
                                                {{ $barter->type == 'same_type' ? 'Même type' : 'Différent' }}
                                            </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                        @if ($barter->status === 'pending')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">En attente</span>
                                        @elseif ($barter->status === 'completed')
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Complété</span>
                                        @else
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Annulé</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-gray-300">{{ $barter->created_at->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-right">
                                        <div class="flex justify-end space-x-1">
                                            <a href="{{ route('barters.show', $barter) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Détails">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            @if ($barter->status === 'pending')
                                                <a href="{{ route('barters.edit', $barter) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Modifier">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-3 text-center text-gray-500 dark:text-gray-400 text-xs">Aucun troc enregistré</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $barters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modaux pour les filtres -->
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Sélectionner un client</h3>
                            <div class="mb-4">
                                <input type="text" id="client-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Rechercher un client...">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="client-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="client-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">Tous les clients</li>
                                    @foreach ($clients as $client)
                                        <li class="client-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $client->id }}">{{ $client->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Boutique -->
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Sélectionner une boutique</h3>
                            <div class="mb-4">
                                <input type="text" id="shop-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Rechercher une boutique...">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="shop-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="shop-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">Toutes les boutiques</li>
                                    @foreach ($shops as $shop)
                                        <li class="shop-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $shop->id }}">{{ $shop->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Sélectionner un vendeur</h3>
                            <div class="mb-4">
                                <input type="text" id="seller-search" class="w-full p-2 border rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Rechercher un vendeur...">
                            </div>
                            <div class="mt-2 max-h-60 overflow-y-auto">
                                <ul id="seller-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="seller-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">Tous les vendeurs</li>
                                    @foreach ($sellers as $seller)
                                        <li class="seller-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="{{ $seller->id }}">{{ $seller->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Sélectionner un statut</h3>
                            <div class="mt-2">
                                <ul id="status-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">Tous les statuts</li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="pending">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span>
                                            En attente
                                        </span>
                                    </li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="completed">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span>
                                            Complété
                                        </span>
                                    </li>
                                    <li class="status-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="cancelled">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-red-400 mr-2"></span>
                                            Annulé
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Type -->
    <div id="type-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Sélectionner un type</h3>
                            <div class="mt-2">
                                <ul id="type-list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li class="type-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="">Tous les types</li>
                                    <li class="type-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="same_type">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-blue-400 mr-2"></span>
                                            Même type
                                        </span>
                                    </li>
                                    <li class="type-item p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" data-id="different_type">
                                        <span class="inline-flex items-center">
                                            <span class="w-2 h-2 rounded-full bg-purple-400 mr-2"></span>
                                            Type différent
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="modal-close mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Fermer
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
                client: { id: '', name: '' },
                shop: { id: '', name: '' },
                seller: { id: '', name: '' },
                status: { id: '', name: '' },
                type: { id: '', name: '' }
            };

            // Tableaux pour stocker les données
            const clients = [
                { id: '', name: 'Tous les clients' },
                    @foreach ($clients as $client)
                { id: '{{ $client->id }}', name: '{{ $client->name }}' },
                @endforeach
            ];

            const shops = [
                { id: '', name: 'Toutes les boutiques' },
                    @foreach ($shops as $shop)
                { id: '{{ $shop->id }}', name: '{{ $shop->name }}' },
                @endforeach
            ];

            const sellers = [
                { id: '', name: 'Tous les vendeurs' },
                    @foreach ($sellers as $seller)
                { id: '{{ $seller->id }}', name: '{{ $seller->name }}' },
                @endforeach
            ];

            const statuses = [
                { id: '', name: 'Tous les statuts' },
                { id: 'pending', name: 'En attente' },
                { id: 'completed', name: 'Complété' },
                { id: 'cancelled', name: 'Annulé' }
            ];

            const types = [
                { id: '', name: 'Tous les types' },
                { id: 'same_type', name: 'Même type' },
                { id: 'different_type', name: 'Type différent' }
            ];

            // Initialiser les filtres actifs depuis les paramètres d'URL
            function initFilters() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('client_id') && urlParams.get('client_id')) {
                    const clientId = urlParams.get('client_id');
                    const client = clients.find(c => c.id === clientId);
                    if (client) {
                        filters.client = client;
                    }
                }

                if (urlParams.has('shop_id') && urlParams.get('shop_id')) {
                    const shopId = urlParams.get('shop_id');
                    const shop = shops.find(s => s.id === shopId);
                    if (shop) {
                        filters.shop = shop;
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

                if (urlParams.has('type') && urlParams.get('type')) {
                    const typeId = urlParams.get('type');
                    const type = types.find(t => t.id === typeId);
                    if (type) {
                        filters.type = type;
                    }
                }

                updateFilterButtons();
                updateActiveFilters();
            }

            // Mettre à jour les boutons de filtre
            function updateFilterButtons() {
                // Client
                const clientButton = document.getElementById('client-filter-btn');
                if (filters.client.id) {
                    clientButton.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    clientButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    clientButton.querySelector('span').textContent = filters.client.name;
                    document.getElementById('client_id').value = filters.client.id;
                } else {
                    clientButton.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    clientButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    clientButton.querySelector('span').textContent = 'Client';
                    document.getElementById('client_id').value = '';
                }

                // Shop
                const shopButton = document.getElementById('shop-filter-btn');
                if (filters.shop.id) {
                    shopButton.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    shopButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    shopButton.querySelector('span').textContent = filters.shop.name;
                    document.getElementById('shop_id').value = filters.shop.id;
                } else {
                    shopButton.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    shopButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    shopButton.querySelector('span').textContent = 'Boutique';
                    document.getElementById('shop_id').value = '';
                }

                // Seller
                const sellerButton = document.getElementById('seller-filter-btn');
                if (filters.seller.id) {
                    sellerButton.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    sellerButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    sellerButton.querySelector('span').textContent = filters.seller.name;
                    document.getElementById('seller_id').value = filters.seller.id;
                } else {
                    sellerButton.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    sellerButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    sellerButton.querySelector('span').textContent = 'Vendeur';
                    document.getElementById('seller_id').value = '';
                }

                // Status
                const statusButton = document.getElementById('status-filter-btn');
                if (filters.status.id) {
                    statusButton.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    statusButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    statusButton.querySelector('span').textContent = filters.status.name;
                    document.getElementById('status').value = filters.status.id;
                } else {
                    statusButton.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    statusButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    statusButton.querySelector('span').textContent = 'Statut';
                    document.getElementById('status').value = '';
                }

                // Type
                const typeButton = document.getElementById('type-filter-btn');
                if (filters.type.id) {
                    typeButton.classList.add('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    typeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    typeButton.querySelector('span').textContent = filters.type.name;
                    document.getElementById('type').value = filters.type.id;
                } else {
                    typeButton.classList.remove('bg-indigo-100', 'text-indigo-700', 'border-indigo-300');
                    typeButton.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    typeButton.querySelector('span').textContent = 'Type';
                    document.getElementById('type').value = '';
                }
            }

            // Mettre à jour l'affichage des filtres actifs
            function updateActiveFilters() {
                const activeFiltersContainer = document.getElementById('active-filters');
                activeFiltersContainer.innerHTML = '';

                if (!filters.client.id && !filters.shop.id && !filters.seller.id && !filters.status.id && !filters.type.id) {
                    activeFiltersContainer.classList.add('hidden');
                    return;
                }

                activeFiltersContainer.classList.remove('hidden');

                // Ajouter un label pour les filtres actifs
                const label = document.createElement('span');
                label.className = 'text-xs font-medium text-gray-700 dark:text-gray-300';
                label.textContent = 'Filtres actifs:';
                activeFiltersContainer.appendChild(label);

                // Fonction pour créer un tag de filtre
                function createFilterTag(filter, key) {
                    if (!filter.id) return;

                    const tag = document.createElement('span');
                    tag.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200';

                    const text = document.createElement('span');
                    text.textContent = `${key === 'client' ? 'Client' : key === 'shop' ? 'Boutique' : key === 'seller' ? 'Vendeur' : key === 'status' ? 'Statut' : 'Type'}: ${filter.name}`;

                    const closeBtn = document.createElement('button');
                    closeBtn.className = 'ml-1 text-indigo-500 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-100 focus:outline-none';
                    closeBtn.innerHTML = `
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;

                    closeBtn.addEventListener('click', function() {
                        filters[key] = { id: '', name: '' };
                        updateFilterButtons();
                        updateActiveFilters();
                    });

                    tag.appendChild(text);
                    tag.appendChild(closeBtn);

                    return tag;
                }

                // Ajouter les tags de filtres
                const clientTag = createFilterTag(filters.client, 'client');
                if (clientTag) activeFiltersContainer.appendChild(clientTag);

                const shopTag = createFilterTag(filters.shop, 'shop');
                if (shopTag) activeFiltersContainer.appendChild(shopTag);

                const sellerTag = createFilterTag(filters.seller, 'seller');
                if (sellerTag) activeFiltersContainer.appendChild(sellerTag);

                const statusTag = createFilterTag(filters.status, 'status');
                if (statusTag) activeFiltersContainer.appendChild(statusTag);

                const typeTag = createFilterTag(filters.type, 'type');
                if (typeTag) activeFiltersContainer.appendChild(typeTag);

                // Ajouter un bouton pour effacer tous les filtres
                if (clientTag || shopTag || sellerTag || statusTag || typeTag) {
                    const resetBtn = document.createElement('button');
                    resetBtn.className = 'ml-2 text-xs text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300';
                    resetBtn.textContent = 'Effacer tout';
                    resetBtn.addEventListener('click', function() {
                        filters.client = { id: '', name: '' };
                        filters.shop = { id: '', name: '' };
                        filters.seller = { id: '', name: '' };
                        filters.status = { id: '', name: '' };
                        filters.type = { id: '', name: '' };
                        updateFilterButtons();
                        updateActiveFilters();
                    });
                    activeFiltersContainer.appendChild(resetBtn);
                }
            }

            // Fonction pour rechercher dans une liste
            function filterList(searchText, items, filterKey) {
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
            document.getElementById('client-filter-btn').addEventListener('click', function() {
                document.getElementById('client-modal').classList.remove('hidden');
            });

            document.getElementById('shop-filter-btn').addEventListener('click', function() {
                document.getElementById('shop-modal').classList.remove('hidden');
            });

            document.getElementById('seller-filter-btn').addEventListener('click', function() {
                document.getElementById('seller-modal').classList.remove('hidden');
            });

            document.getElementById('status-filter-btn').addEventListener('click', function() {
                document.getElementById('status-modal').classList.remove('hidden');
            });

            document.getElementById('type-filter-btn').addEventListener('click', function() {
                document.getElementById('type-modal').classList.remove('hidden');
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
            document.getElementById('client-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.client-item');
                filterList(searchText, items, 'client');
            });

            document.getElementById('shop-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.shop-item');
                filterList(searchText, items, 'shop');
            });

            document.getElementById('seller-search').addEventListener('input', function(e) {
                const searchText = e.target.value;
                const items = document.querySelectorAll('.seller-item');
                filterList(searchText, items, 'seller');
            });

            // Gestionnaires de sélection
            document.querySelectorAll('.client-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent;
                    filters.client = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('client-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.shop-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent;
                    filters.shop = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('shop-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.seller-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.textContent;
                    filters.seller = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('seller-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.status-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = id === 'pending' ? 'En attente' : id === 'completed' ? 'Complété' : id === 'cancelled' ? 'Annulé' : 'Tous les statuts';
                    filters.status = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('status-modal').classList.add('hidden');
                });
            });

            document.querySelectorAll('.type-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = id === 'same_type' ? 'Même type' : id === 'different_type' ? 'Type différent' : 'Tous les types';
                    filters.type = { id, name };
                    updateFilterButtons();
                    updateActiveFilters();
                    document.getElementById('type-modal').classList.add('hidden');
                });
            });

            // Gestionnaire pour le bouton de réinitialisation
            document.getElementById('reset-filters').addEventListener('click', function() {
                filters.client = { id: '', name: '' };
                filters.shop = { id: '', name: '' };
                filters.seller = { id: '', name: '' };
                filters.status = { id: '', name: '' };
                filters.type = { id: '', name: '' };
                document.getElementById('search').value = '';
                updateFilterButtons();
                updateActiveFilters();
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
                window.location.href = '{{ route('barters.index') }}' + (params.toString() ? '?' + params.toString() : '');
            });

            // Initialiser l'interface
            initFilters();
        });
    </script>
</x-app-layout>
