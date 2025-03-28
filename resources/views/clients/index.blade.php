<x-app-layout>
    <x-slot name="header">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-6 px-4 sm:px-6 lg:px-8 mb-6 rounded-xl shadow-md">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex-1 min-w-0 mb-4 md:mb-0">
                    <h2 class="text-2xl font-bold leading-7 text-white sm:text-3xl sm:truncate">
                        {{ __('Gestion des clients') }}
                    </h2>
                </div>
                <div class="flex space-x-3">
                    <button onclick="toggleModal('createClient')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium bg-white text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                        <i class="bi bi-plus-lg mr-2"></i>
                        {{ __('Ajouter un client') }}
                    </button>
                    <button onclick="toggleImportModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium bg-green-50 text-green-700 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        {{ __('Importer') }}
                    </button>
                    <a href="{{ route('clients.export') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <i class="bi bi-cloud-download mr-2"></i>
                        {{ __('Exporter') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Subscription Info Banner -->
            @if (isset($subscription))
                <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-800">Clients liés à l'abonnement</h3>
                            <p class="text-indigo-600">{{ $subscription->plan->name }} - Valide du {{ $subscription->starts_at->format('d/m/Y') }} au {{ $subscription->ends_at->format('d/m/Y') }}</p>
                            <div class="mt-1 flex items-center">
                                <span class="text-sm font-medium text-indigo-700">{{ count($clients) }} clients sur {{ $subscription->plan->max_clients }} autorisés</span>
                                <div class="ml-2 w-40 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, (count($clients) / $subscription->plan->max_clients) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('subscriptions.show', $subscription) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            Voir l'abonnement
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Main Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Actions and Stats -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <!-- Search and filters -->
                        <div class="w-full md:w-2/3 space-y-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="clientSearchInput" class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Rechercher par nom, téléphone ou autre info...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button id="clearSearchBtn" class="text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                <select id="filterGender" class="rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les genres</option>
                                    <option value="M">Hommes</option>
                                    <option value="F">Femmes</option>
                                    <option value="Other">Autres</option>
                                </select>
                                
                                <select id="filterSort" class="rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="name_asc">Nom (A-Z)</option>
                                    <option value="name_desc">Nom (Z-A)</option>
                                    <option value="birth_asc">Âge (croissant)</option>
                                    <option value="birth_desc">Âge (décroissant)</option>
                                    <option value="bills_desc">Factures (décroissant)</option>
                                </select>
                                
                                <button id="toggleAdvancedSearch" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                                    </svg>
                                    Recherche avancée
                                </button>
                            </div>
                            
                            <div id="advancedSearchPanel" class="hidden mt-2 p-3 bg-gray-50 rounded-md">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="date" id="birthFrom" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-gray-500">à</span>
                                            <input type="date" id="birthTo" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Factures</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="number" min="0" id="minBills" placeholder="Min" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-gray-500">à</span>
                                            <input type="number" min="0" id="maxBills" placeholder="Max" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dernière facture</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="date" id="lastBillFrom" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <span class="text-gray-500">à</span>
                                            <input type="date" id="lastBillTo" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-3">
                                    <button id="applyAdvancedFilters" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-1.5 px-4 rounded-md transition-colors">
                                        Appliquer les filtres
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="w-full md:w-1/3 flex flex-col md:items-end">
                            <div class="w-full md:w-auto flex md:flex-col gap-3">
                                <div class="px-4 py-2 bg-blue-50 rounded-lg flex items-center">
                                    <div class="mr-3 p-2 bg-blue-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-700 font-medium">Total Clients</p>
                                        <p class="text-lg font-semibold text-blue-800" id="totalClientCount">{{ $clients->total() }}</p>
                                    </div>
                                </div>
                                <div class="px-4 py-2 bg-green-50 rounded-lg flex items-center">
                                    <div class="mr-3 p-2 bg-green-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-green-700 font-medium">Total Factures</p>
                                        <p class="text-lg font-semibold text-green-800" id="totalBillCount">{{ $clients->sum('bills_count') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clients Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="clientsTable">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nom
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Genre
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date de Naissance
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Téléphones
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Factures
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($clients as $client)
                                    <tr class="client-row hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                                                    <span class="font-medium text-sm">{{ strtoupper(substr($client->name, 0, 2)) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                                    <div class="text-sm text-gray-500">Client #{{ $client->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($client->sex == 'M')
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Homme</span>
                                            @elseif($client->sex == 'F')
                                                <span class="px-2 py-1 text-xs font-medium bg-pink-100 text-pink-800 rounded-full">Femme</span>
                                            @elseif($client->sex == 'Other')
                                                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Autre</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Non spécifié</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($client->birth)
                                                <div class="text-sm text-gray-900">{{ $client->birth->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $client->birth->age }} ans</div>
                                            @else
                                                <span class="text-gray-400">Non spécifié</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                @foreach($client->phones as $index => $phone)
                                                    <div class="flex items-center{{ $index > 0 ? ' mt-1' : '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                        </svg>
                                                        <span class="text-sm">{{ $phone->number }}</span>
                                                        <a href="#" class="ml-2 text-gray-400 hover:text-gray-600 transition-colors" title="Envoyer un SMS" onclick="sendSms('{{ $phone->number }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $client->bills_count }} factures</div>
                                                @if($client->bills_count > 0 && isset($client->last_bill_date))
                                                    <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                                                        Dernière: {{ \Carbon\Carbon::parse($client->last_bill_date)->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <button onclick="viewClient({{ $client->id }})" class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <button onclick="editClient({{ $client->id }})" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </button>
                                                <button onclick="confirmDelete({{ $client->id }}, '{{ $client->name }}')" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                        <div class="py-1">
                                                            <a href="{{ route('clients.bills.create', $client) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Créer une facture</a>
                                                            <a href="{{ route('clients.bills.index', $client) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Voir les factures</a>
                                                            <button onclick="sendSmsPromo({{ $client->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Envoyer promo</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="noResultsMessage" class="hidden py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">Essayez de modifier vos critères de recherche ou ajoutez un nouveau client.</p>
                        <div class="mt-6">
                            <button type="button" onclick="toggleModal('createClient')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Nouveau Client
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Affichage de <span class="font-medium" id="displayedRangeStart">{{ $clients->firstItem() }}</span> à <span class="font-medium" id="displayedRangeEnd">{{ $clients->lastItem() }}</span> sur <span class="font-medium" id="totalItems">{{ $clients->total() }}</span> clients
                                </p>
                            </div>
                            <div>
                                {{ $clients->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Client Modal -->
    <div id="newClientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900">Nouveau Client</h3>
                <button onclick="toggleModal('newClientModal')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="newClientForm" class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Genre</label>
                    <select name="sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Non spécifié</option>
                        <option value="M">Homme</option>
                        <option value="F">Femme</option>
                        <option value="Other">Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                    <input type="date" name="birth"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div id="phonesContainer">
                    <label class="block text-sm font-medium text-gray-700">Téléphones</label>
                    <div class="space-y-2">
                        <div class="flex gap-2">
                            <input type="text" name="phones[]" placeholder="+237 6XX XX XX XX"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" onclick="addPhoneField()"
                                    class="mt-1 bg-green-500 hover:bg-green-600 text-white h-10 w-10 rounded-md flex items-center justify-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t flex justify-end gap-2">
                    <button type="button" onclick="toggleModal('newClientModal')"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmer la suppression</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer le client <span id="deleteClientName" class="font-medium text-gray-800"></span>? Cette action est irréversible.</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <form id="deleteClientForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="deleteClientId" name="client_id">
                            <div class="flex justify-center space-x-4">
                                <button onclick="toggleModal('deleteModal')" type="button" class="px-4 py-2 bg-gray-100 text-gray-800 text-base font-medium rounded-md w-24 border border-gray-300 shadow-sm hover:bg-gray-200 focus:outline-none">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 shadow-sm hover:bg-red-700 focus:outline-none">
                                    Supprimer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Clients Modal -->
    <div id="importClientsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative bg-white rounded-2xl shadow-xl mx-auto p-0 max-w-3xl w-full transform transition-transform duration-300 scale-95 opacity-0" id="importModalContent">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">
                        <i class="bi bi-cloud-upload mr-2"></i>{{ __('Importer des clients') }}
                    </h3>
                    <button type="button" onclick="toggleImportModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <p class="text-gray-600">Importez vos clients à partir d'un fichier CSV ou Excel. Vous pourrez associer les colonnes avec les champs appropriés.</p>
                </div>

                <div class="space-y-6">
                    <!-- Étape 1: Téléchargement du fichier -->
                    <div id="importStep1" class="import-step">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">1. Sélectionnez votre fichier</h4>
                        
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-8 mb-4 bg-gray-50 relative">
                            <input type="file" id="importFile" accept=".csv,.xlsx,.xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="text-center pointer-events-none">
                                <i class="bi bi-file-earmark-spreadsheet text-5xl text-indigo-500 mb-3"></i>
                                <p class="text-gray-600 mb-2">Glissez-déposez votre fichier ici ou cliquez pour parcourir</p>
                                <p class="text-sm text-gray-500">Formats acceptés: CSV, Excel (.xlsx, .xls)</p>
                            </div>
                        </div>
                        
                        <div id="selectedFileContainer" class="hidden mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="bi bi-file-earmark-spreadsheet text-xl text-indigo-500 mr-2"></i>
                                    <span id="selectedFileName" class="text-gray-700 font-medium"></span>
                                </div>
                                <button type="button" id="removeFileBtn" class="text-gray-400 hover:text-gray-600">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <a href="/templates/import-clients.csv" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                    <i class="bi bi-download mr-1"></i>
                                    Télécharger un modèle
                                </a>
                            </div>
                            <button type="button" id="goToStep2Btn" disabled
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2.5 px-6 rounded-xl shadow-sm transition duration-200 opacity-50 cursor-not-allowed">
                                Continuer <i class="bi bi-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 2: Mappage des colonnes -->
                    <div id="importStep2" class="import-step hidden">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">2. Associez les colonnes</h4>
                        
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="flex items-center text-gray-700 mb-3">
                                <i class="bi bi-info-circle text-indigo-500 mr-2"></i>
                                <span>Associez les colonnes de votre fichier avec les champs correspondants</span>
                            </div>
                            
                            <div id="columnMappingContainer" class="space-y-3">
                                <!-- Les champs de mappage seront générés ici via JavaScript -->
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" id="backToStep1Btn" class="border border-gray-300 text-gray-700 font-medium py-2.5 px-6 rounded-xl shadow-sm hover:bg-gray-50 transition duration-200">
                                <i class="bi bi-arrow-left mr-1"></i> Retour
                            </button>
                            <button type="button" id="goToStep3Btn" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2.5 px-6 rounded-xl shadow-sm transition duration-200">
                                Aperçu <i class="bi bi-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 3: Aperçu et validation -->
                    <div id="importStep3" class="import-step hidden">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">3. Aperçu et importation</h4>
                        
                        <div class="overflow-x-auto bg-gray-50 rounded-xl p-4 mb-6">
                            <table class="min-w-full divide-y divide-gray-200" id="previewTable">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <!-- Les en-têtes seront générés ici via JavaScript -->
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Les données de prévisualisation seront générées ici via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-gray-600 text-sm">
                                <i class="bi bi-people mr-1"></i>
                                <span id="recordCount">0 clients à importer</span>
                            </div>
                            
                            <div class="flex items-center">
                                <label class="flex items-center text-sm text-gray-700">
                                    <input type="checkbox" id="skipHeaderRow" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mr-2">
                                    Ignorer la première ligne (en-têtes)
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button type="button" id="backToStep2Btn" class="border border-gray-300 text-gray-700 font-medium py-2.5 px-6 rounded-xl shadow-sm hover:bg-gray-50 transition duration-200">
                                <i class="bi bi-arrow-left mr-1"></i> Retour
                            </button>
                            <button type="button" id="importBtn" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium py-2.5 px-6 rounded-xl shadow-sm transition duration-200">
                                <i class="bi bi-check-circle mr-1"></i> Importer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Promo Modal -->
    <div id="smsPromoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900">Envoyer un message promotionnel</h3>
                <button onclick="toggleModal('smsPromoModal')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="smsPromoForm" class="mt-4 space-y-4">
                <input type="hidden" id="smsPromoClientId" name="client_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destinataire</label>
                    <div id="smsPromoRecipient" class="text-sm text-gray-800 bg-gray-50 p-2 rounded-md"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Modèle de message</label>
                    <select id="smsPromoTemplate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionner un modèle</option>
                        <option value="promo_10">Promotion 10% de réduction</option>
                        <option value="promo_25">Promotion 25% de réduction</option>
                        <option value="birthday">Message d'anniversaire</option>
                        <option value="new_arrival">Nouveaux produits</option>
                        <option value="custom">Message personnalisé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="smsPromoMessage" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Votre message..."></textarea>
                    <div class="mt-1 flex justify-between text-xs text-gray-500">
                        <span>Caractères: <span id="smsCharCount">0</span>/160</span>
                        <span id="smsSegmentCount">1 segment</span>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Planifier l'envoi</span>
                    </label>
                    <div class="mt-2 hidden">
                        <input type="datetime-local" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="pt-3 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Coût estimé: <span class="font-medium">1 crédit SMS</span>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="toggleModal('smsPromoModal')"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-300 transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Client Modal -->
    <div id="createClient" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative bg-white rounded-2xl shadow-xl mx-auto p-0 w-full max-w-md transform transition-transform duration-300">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">
                        <i class="bi bi-person-plus mr-2"></i>{{ __('Ajouter un client') }}
                    </h3>
                    <button type="button" onclick="toggleModal('createClient')" class="text-white hover:text-gray-200 focus:outline-none">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <form id="quickClientForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom complet') }} <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required 
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                        <input type="email" id="email" name="email"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm">
                    </div>

                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Genre') }}</label>
                        <select id="sex" name="sex" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm">
                            <option value="">{{ __('Non spécifié') }}</option>
                            <option value="M">{{ __('Homme') }}</option>
                            <option value="F">{{ __('Femme') }}</option>
                            <option value="Other">{{ __('Autre') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="birth" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de naissance') }}</label>
                        <input type="date" id="birth" name="birth"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Téléphones') }}
                            <button type="button" id="addPhoneField" class="ml-2 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                <i class="bi bi-plus-circle"></i> {{ __('Ajouter') }}
                            </button>
                        </label>
                        <div id="phoneFields" class="space-y-2">
                            <div class="phone-field flex items-center">
                                <input type="text" name="phones[]" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm" placeholder="Numéro de téléphone">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adresse') }}</label>
                        <textarea id="address" name="address" rows="2"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"></textarea>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="toggleModal('createClient')" 
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition duration-200">
                            {{ __('Annuler') }}
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-sm transition duration-200">
                            {{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Client-side search implementation
        let allClients = @json($clients->items());
        let clientsCache = [...allClients];
        
        // Utility functions
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
        
        function addPhoneField() {
            const container = document.querySelector('#phonesContainer .space-y-2');
            const phoneFields = container.querySelectorAll('input[name="phones[]"]');
            const lastPhoneField = phoneFields[phoneFields.length - 1];
            
            const newPhoneFieldContainer = document.createElement('div');
            newPhoneFieldContainer.className = 'flex gap-2';
            newPhoneFieldContainer.innerHTML = `
                <input type="text" name="phones[]" placeholder="+237 6XX XX XX XX"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" onclick="removePhoneField(this)"
                        class="mt-1 bg-red-500 hover:bg-red-600 text-white h-10 w-10 rounded-md flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            container.appendChild(newPhoneFieldContainer);
        }
        
        function removePhoneField(button) {
            const fieldContainer = button.parentElement;
            fieldContainer.remove();
        }
        
        function confirmDelete(clientId, clientName) {
            document.getElementById('deleteClientId').value = clientId;
            document.getElementById('deleteClientName').textContent = clientName;
            const form = document.getElementById('deleteClientForm');
            form.action = `/clients/${clientId}`;
            toggleModal('deleteModal');
        }
        
        function viewClient(clientId) {
            window.location.href = `/clients/${clientId}`;
        }
        
        function editClient(clientId) {
            window.location.href = `/clients/${clientId}/edit`;
        }
        
        function sendSms(phoneNumber) {
            alert(`Envoyer un SMS à ${phoneNumber}`);
            // Implement SMS sending functionality
        }
        
        function sendSmsPromo(clientId) {
            const client = allClients.find(c => c.id === clientId);
            if (!client) return;
            
            document.getElementById('smsPromoClientId').value = clientId;
            document.getElementById('smsPromoRecipient').textContent = `${client.name} - ${client.phones.map(p => p.number).join(', ')}`;
            
            toggleModal('smsPromoModal');
        }
        
        // Client-side search implementation
        const searchInput = document.getElementById('clientSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const filterGenderSelect = document.getElementById('filterGender');
        const filterSortSelect = document.getElementById('filterSort');
        const toggleAdvancedSearchBtn = document.getElementById('toggleAdvancedSearch');
        const advancedSearchPanel = document.getElementById('advancedSearchPanel');
        const applyAdvancedFiltersBtn = document.getElementById('applyAdvancedFilters');
        const clientsTable = document.getElementById('clientsTable');
        const noResultsMessage = document.getElementById('noResultsMessage');
        
        // Debounce function to improve search performance
        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }
        
        // Apply filters and search
        const applyFilters = debounce(() => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const genderFilter = filterGenderSelect.value;
            const sortOption = filterSortSelect.value;
            
            // Advanced search filters
            const birthFromDate = document.getElementById('birthFrom').value;
            const birthToDate = document.getElementById('birthTo').value;
            const minBills = document.getElementById('minBills').value;
            const maxBills = document.getElementById('maxBills').value;
            const lastBillFromDate = document.getElementById('lastBillFrom').value;
            const lastBillToDate = document.getElementById('lastBillTo').value;
            
            // Filter clients
            let filteredClients = allClients.filter(client => {
                // Basic name and phone search
                const nameMatch = client.name.toLowerCase().includes(searchTerm);
                const phoneMatch = client.phones.some(phone => 
                    phone.number.toLowerCase().includes(searchTerm)
                );
                
                // Gender filter
                const genderMatch = !genderFilter || client.sex === genderFilter;
                
                // Advanced filters
                let advancedMatch = true;
                
                // Birth date range filter
                if (birthFromDate && client.birth) {
                    advancedMatch = advancedMatch && new Date(client.birth) >= new Date(birthFromDate);
                }
                if (birthToDate && client.birth) {
                    advancedMatch = advancedMatch && new Date(client.birth) <= new Date(birthToDate);
                }
                
                // Bills count filter
                if (minBills) {
                    advancedMatch = advancedMatch && client.bills_count >= parseInt(minBills);
                }
                if (maxBills) {
                    advancedMatch = advancedMatch && client.bills_count <= parseInt(maxBills);
                }
                
                // Last bill date filter
                if (lastBillFromDate && client.last_bill_date) {
                    advancedMatch = advancedMatch && new Date(client.last_bill_date) >= new Date(lastBillFromDate);
                }
                if (lastBillToDate && client.last_bill_date) {
                    advancedMatch = advancedMatch && new Date(client.last_bill_date) <= new Date(lastBillToDate);
                }
                
                return (nameMatch || phoneMatch) && genderMatch && advancedMatch;
            });
            
            // Sort clients
            if (sortOption) {
                const [field, direction] = sortOption.split('_');
                filteredClients.sort((a, b) => {
                    let valueA, valueB;
                    if (field === 'name') {
                        valueA = a.name.toLowerCase();
                        valueB = b.name.toLowerCase();
                    } else if (field === 'birth') {
                        valueA = a.birth ? new Date(a.birth) : new Date(0);
                        valueB = b.birth ? new Date(b.birth) : new Date(0);
                    } else if (field === 'bills') {
                        valueA = a.bills_count;
                        valueB = b.bills_count;
                    }
                    
                    if (direction === 'asc') {
                        return valueA > valueB ? 1 : -1;
                    } else {
                        return valueA < valueB ? 1 : -1;
                    }
                });
            }
            
            // Update displayed clients
            updateClientTable(filteredClients);
        });
        
        // Update the client table with filtered results
        function updateClientTable(clients) {
            const tbody = clientsTable.querySelector('tbody');
            tbody.innerHTML = '';
            
            if (clients.length === 0) {
                clientsTable.classList.add('hidden');
                noResultsMessage.classList.remove('hidden');
                return;
            }
            
            clientsTable.classList.remove('hidden');
            noResultsMessage.classList.add('hidden');
            
            clients.forEach(client => {
                const tr = document.createElement('tr');
                tr.className = 'client-row hover:bg-gray-50 transition-colors';
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                                <span class="font-medium text-sm">${client.name.substring(0, 2).toUpperCase()}</span>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">${client.name}</div>
                                <div class="text-sm text-gray-500">Client #${client.id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        ${getGenderBadge(client.sex)}
                    </td>
                    <td class="px-6 py-4">
                        ${client.birth ? 
                            `<div class="text-sm text-gray-900">${formatDate(client.birth)}</div>
                            <div class="text-xs text-gray-500">${calculateAge(client.birth)} ans</div>` : 
                            `<span class="text-gray-400">Non spécifié</span>`
                        }
                    </td>
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            ${client.phones.map((phone, index) => `
                                <div class="flex items-center${index > 0 ? ' mt-1' : ''}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="text-sm">${phone.number}</span>
                                    <a href="#" class="ml-2 text-gray-400 hover:text-gray-600 transition-colors" title="Envoyer un SMS" onclick="sendSms('${phone.number}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                                        </svg>
                                    </a>
                                </div>
                            `).join('')}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">${client.bills_count} factures</div>
                            ${client.bills_count > 0 && client.last_bill_date ? 
                                `<span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                                    Dernière: ${formatDate(client.last_bill_date)}
                                </span>` : ''
                            }
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewClient(${client.id})" class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button onclick="editClient(${client.id})" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button onclick="confirmDelete(${client.id}, '${client.name}')" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="/clients/${client.id}/bills/create" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Créer une facture</a>
                                        <a href="/clients/${client.id}/bills" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Voir les factures</a>
                                        <button onclick="sendSmsPromo(${client.id})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Envoyer promo</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            // Update pagination info
            document.getElementById('displayedRangeStart').textContent = 1;
            document.getElementById('displayedRangeEnd').textContent = clients.length;
            document.getElementById('totalItems').textContent = clients.length;
            document.getElementById('totalClientCount').textContent = clients.length;
        }
        
        // Helper functions
        function getGenderBadge(gender) {
            if (gender === 'M') {
                return '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Homme</span>';
            } else if (gender === 'F') {
                return '<span class="px-2 py-1 text-xs font-medium bg-pink-100 text-pink-800 rounded-full">Femme</span>';
            } else if (gender === 'Other') {
                return '<span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Autre</span>';
            } else {
                return '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Non spécifié</span>';
            }
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
        
        function calculateAge(birthDateString) {
            const birthDate = new Date(birthDateString);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }
        
        // Event listeners
        searchInput.addEventListener('input', applyFilters);
        
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterGenderSelect.value = '';
            filterSortSelect.value = 'name_asc';
            
            // Clear advanced filters
            document.getElementById('birthFrom').value = '';
            document.getElementById('birthTo').value = '';
            document.getElementById('minBills').value = '';
            document.getElementById('maxBills').value = '';
            document.getElementById('lastBillFrom').value = '';
            document.getElementById('lastBillTo').value = '';
            
            applyFilters();
        });
        
        filterGenderSelect.addEventListener('change', applyFilters);
        filterSortSelect.addEventListener('change', applyFilters);
        
        toggleAdvancedSearchBtn.addEventListener('click', () => {
            advancedSearchPanel.classList.toggle('hidden');
        });
        
        applyAdvancedFiltersBtn.addEventListener('click', applyFilters);
        
        // New client form
        document.getElementById('newClientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement new client creation here
            alert('Client créé avec succès !');
            toggleModal('newClientModal');
        });
        
        // Delete client form
        document.getElementById('deleteClientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement client deletion here
            alert('Client supprimé avec succès !');
            toggleModal('deleteModal');
        });
        
        // SMS Promo form
        document.getElementById('smsPromoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement SMS sending here
            alert('Message envoyé avec succès !');
            toggleModal('smsPromoModal');
        });
        
        // SMS character counter
        const smsPromoMessage = document.getElementById('smsPromoMessage');
        const smsCharCount = document.getElementById('smsCharCount');
        const smsSegmentCount = document.getElementById('smsSegmentCount');
        
        smsPromoMessage.addEventListener('input', function() {
            const chars = this.value.length;
            smsCharCount.textContent = chars;
            
            const segments = Math.ceil(chars / 160);
            smsSegmentCount.textContent = segments === 1 ? '1 segment' : `${segments} segments`;
        });
        
        // SMS Template selection
        const smsPromoTemplate = document.getElementById('smsPromoTemplate');
        
        smsPromoTemplate.addEventListener('change', function() {
            const template = this.value;
            let message = '';
            
            switch(template) {
                case 'promo_10':
                    message = "Offre spéciale! Bénéficiez de 10% de réduction sur tout notre catalogue jusqu'au 31/03. Présentez ce SMS en boutique ou utilisez le code PROMO10 en ligne.";
                    break;
                case 'promo_25':
                    message = "SOLDES EXCEPTIONNELLES! 25% de réduction sur tous nos produits jusqu'au 15/04. Profitez-en vite, stocks limités! Code: PROMO25";
                    break;
                case 'birthday':
                    message = "Joyeux anniversaire! Pour célébrer ce jour spécial, nous vous offrons 15% de réduction sur votre prochain achat. Valable 7 jours, code: BIRTHDAY";
                    break;
                case 'new_arrival':
                    message = "Nouveautés en boutique! Venez découvrir notre nouvelle collection printemps-été. Présentation exclusive ce week-end, 5% offerts sur vos achats.";
                    break;
                case 'custom':
                    message = "";
                    break;
            }
            
            smsPromoMessage.value = message;
            smsPromoMessage.dispatchEvent(new Event('input'));
        });
        
        // Initialize client-side search
        document.addEventListener('DOMContentLoaded', function() {
            // Pre-fill data with initial load
            clientsCache = [...allClients];
        });

        // Gestion du formulaire d'ajout rapide de client
        document.getElementById('quickClientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Afficher un indicateur de chargement
            Swal.fire({
                title: 'Création en cours...',
                html: 'Veuillez patienter pendant la création du client',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Récupération des données du formulaire
            const formData = new FormData(this);
            
            // Conversion en objet JSON
            const jsonData = {};
            formData.forEach((value, key) => {
                // Gérer les tableaux (comme les numéros de téléphone)
                if (key.endsWith('[]')) {
                    const cleanKey = key.substring(0, key.length - 2);
                    if (!jsonData[cleanKey]) {
                        jsonData[cleanKey] = [];
                    }
                    if (value.trim() !== '') {
                        jsonData[cleanKey].push(value);
                    }
                } else {
                    jsonData[key] = value;
                }
            });
            
            // Envoi de la requête
            fetch('{{ route('clients.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                console.log("Réponse du serveur:", data);
                
                if (data.success) {
                    // Fermer le modal
                    toggleModal('createClient');
                    
                    // Réinitialiser le formulaire
                    this.reset();
                    
                    // Afficher un message de succès
                    Swal.fire({
                        icon: 'success',
                        title: 'Client ajouté!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    
                    // Recharger la page après un court délai
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Afficher une erreur
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message || 'Une erreur est survenue lors de la création du client'
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                
                // Afficher une erreur
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la communication avec le serveur'
                });
            });
        });
        
        // Ajout d'un champ téléphone supplémentaire
        document.getElementById('addPhoneField').addEventListener('click', function() {
            const phoneFieldsContainer = document.getElementById('phoneFields');
            
            const newPhoneField = document.createElement('div');
            newPhoneField.className = 'phone-field flex items-center mt-2';
            newPhoneField.innerHTML = `
                <input type="text" name="phones[]" class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm" placeholder="Numéro de téléphone">
                <button type="button" class="ml-2 text-red-500 hover:text-red-700 remove-phone">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            phoneFieldsContainer.appendChild(newPhoneField);
            
            // Ajouter l'événement pour supprimer le champ
            newPhoneField.querySelector('.remove-phone').addEventListener('click', function() {
                newPhoneField.remove();
            });
        });
        
        // Suppression d'un champ téléphone existant
        document.querySelectorAll('.remove-phone').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.phone-field').remove();
            });
        });

        // Import Modal Functions
        function toggleImportModal() {
            const modal = document.getElementById('importClientsModal');
            const modalContent = document.getElementById('importModalContent');
            
            if (modal.classList.contains('hidden')) {
                // Ouvrir la modal
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                // Réinitialiser à l'étape 1
                showImportStep(1);
            } else {
                // Fermer la modal
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        }
        
        function showImportStep(step) {
            // Cacher toutes les étapes
            document.querySelectorAll('.import-step').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Afficher l'étape demandée
            document.getElementById(`importStep${step}`).classList.remove('hidden');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const importFile = document.getElementById('importFile');
            const selectedFileContainer = document.getElementById('selectedFileContainer');
            const selectedFileName = document.getElementById('selectedFileName');
            const removeFileBtn = document.getElementById('removeFileBtn');
            const goToStep2Btn = document.getElementById('goToStep2Btn');
            const backToStep1Btn = document.getElementById('backToStep1Btn');
            const goToStep3Btn = document.getElementById('goToStep3Btn');
            const backToStep2Btn = document.getElementById('backToStep2Btn');
            const importBtn = document.getElementById('importBtn');
            const columnMappingContainer = document.getElementById('columnMappingContainer');
            const previewTable = document.getElementById('previewTable');
            const skipHeaderRow = document.getElementById('skipHeaderRow');
            const recordCount = document.getElementById('recordCount');
            
            let fileData = null;
            let headers = [];
            let parsedData = [];
            let columnMapping = {};
            
            // Champs disponibles dans l'application
            const availableFields = [
                { id: 'name', label: 'Nom' },
                { id: 'email', label: 'Email' },
                { id: 'phone', label: 'Téléphone' },
                { id: 'address', label: 'Adresse' },
                { id: 'company', label: 'Entreprise' },
                { id: 'notes', label: 'Notes' }
            ];
            
            // Gérer le téléchargement de fichier
            importFile.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    selectedFileName.textContent = file.name;
                    selectedFileContainer.classList.remove('hidden');
                    goToStep2Btn.disabled = false;
                    goToStep2Btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    
                    // Lire le fichier
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        fileData = event.target.result;
                        parseFileData(file.name, fileData);
                    };
                    reader.readAsText(file);
                }
            });
            
            // Supprimer le fichier
            removeFileBtn.addEventListener('click', function() {
                importFile.value = '';
                selectedFileContainer.classList.add('hidden');
                goToStep2Btn.disabled = true;
                goToStep2Btn.classList.add('opacity-50', 'cursor-not-allowed');
                fileData = null;
                headers = [];
                parsedData = [];
            });
            
            // Navigation entre les étapes
            goToStep2Btn.addEventListener('click', function() {
                createColumnMappingUI();
                showImportStep(2);
            });
            
            backToStep1Btn.addEventListener('click', function() {
                showImportStep(1);
            });
            
            goToStep3Btn.addEventListener('click', function() {
                createPreviewTable();
                showImportStep(3);
            });
            
            backToStep2Btn.addEventListener('click', function() {
                showImportStep(2);
            });
            
            importBtn.addEventListener('click', function() {
                importClients();
            });
            
            // Analyser les données du fichier
            function parseFileData(filename, data) {
                headers = [];
                parsedData = [];
                
                if (filename.endsWith('.csv')) {
                    // Analyser CSV
                    const lines = data.split('\n');
                    if (lines.length > 0) {
                        // Extraire les en-têtes
                        headers = lines[0].split(',').map(header => header.trim().replace(/"/g, ''));
                        
                        // Extraire les données
                        for (let i = 1; i < lines.length; i++) {
                            if (lines[i].trim() !== '') {
                                const values = lines[i].split(',').map(value => value.trim().replace(/"/g, ''));
                                const row = {};
                                headers.forEach((header, index) => {
                                    row[header] = values[index] || '';
                                });
                                parsedData.push(row);
                            }
                        }
                    }
                } else if (filename.endsWith('.xlsx') || filename.endsWith('.xls')) {
                    // Pour Excel, nous aurons besoin d'une bibliothèque comme SheetJS/xlsx
                    // Cette partie nécessiterait l'intégration d'une bibliothèque externe
                    alert('Le support des fichiers Excel nécessite une bibliothèque supplémentaire. Veuillez utiliser un fichier CSV pour le moment.');
                }
            }
            
            // Créer l'interface de mappage des colonnes
            function createColumnMappingUI() {
                columnMappingContainer.innerHTML = '';
                
                headers.forEach(header => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center space-x-4';
                    
                    const sourceCol = document.createElement('div');
                    sourceCol.className = 'w-1/2';
                    sourceCol.innerHTML = `
                        <div class="text-sm font-medium text-gray-700">${header}</div>
                        <div class="text-xs text-gray-500">Colonne du fichier</div>
                    `;
                    
                    const arrow = document.createElement('div');
                    arrow.className = 'text-gray-400';
                    arrow.innerHTML = '<i class="bi bi-arrow-right"></i>';
                    
                    const selectCol = document.createElement('div');
                    selectCol.className = 'w-1/2';
                    
                    const select = document.createElement('select');
                    select.className = 'mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md';
                    select.dataset.sourceColumn = header;
                    
                    // Option vide
                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = 'Ne pas importer';
                    select.appendChild(emptyOption);
                    
                    // Options pour chaque champ disponible
                    availableFields.forEach(field => {
                        const option = document.createElement('option');
                        option.value = field.id;
                        option.textContent = field.label;
                        
                        // Auto-mapper les colonnes avec des noms similaires
                        if (header.toLowerCase() === field.label.toLowerCase() || 
                            header.toLowerCase() === field.id.toLowerCase()) {
                            option.selected = true;
                            columnMapping[header] = field.id;
                        }
                        
                        select.appendChild(option);
                    });
                    
                    // Événement pour mettre à jour le mapping
                    select.addEventListener('change', function() {
                        columnMapping[header] = this.value;
                    });
                    
                    selectCol.appendChild(select);
                    
                    row.appendChild(sourceCol);
                    row.appendChild(arrow);
                    row.appendChild(selectCol);
                    
                    columnMappingContainer.appendChild(row);
                });
            }
            
            // Créer la table de prévisualisation
            function createPreviewTable() {
                // Nettoyer la table existante
                const thead = previewTable.querySelector('thead tr');
                const tbody = previewTable.querySelector('tbody');
                thead.innerHTML = '';
                tbody.innerHTML = '';
                
                // Ajouter les en-têtes
                const mappedHeaders = headers.filter(header => columnMapping[header] && columnMapping[header] !== '');
                mappedHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.className = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
                    th.textContent = availableFields.find(field => field.id === columnMapping[header])?.label || header;
                    thead.appendChild(th);
                });
                
                // Ajouter les données
                const dataToShow = skipHeaderRow.checked && parsedData.length > 0 ? parsedData.slice(1) : parsedData;
                dataToShow.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    
                    mappedHeaders.forEach(header => {
                        const td = document.createElement('td');
                        td.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                        td.textContent = row[header] || '';
                        tr.appendChild(td);
                    });
                    
                    tbody.appendChild(tr);
                });
                
                // Mettre à jour le compteur d'enregistrements
                recordCount.textContent = `${dataToShow.length} clients à importer`;
            }
            
            // Importer les clients
            function importClients() {
                const dataToImport = skipHeaderRow.checked && parsedData.length > 0 ? parsedData.slice(1) : parsedData;
                
                // Préparer les données pour l'import
                const clientsToImport = dataToImport.map(row => {
                    const client = {};
                    
                    // Mapper les colonnes selon la configuration
                    headers.forEach(header => {
                        if (columnMapping[header] && columnMapping[header] !== '') {
                            client[columnMapping[header]] = row[header] || '';
                        }
                    });
                    
                    return client;
                });
                
                // Vérifier s'il y a des données à importer
                if (clientsToImport.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Aucun client à importer. Veuillez vérifier votre fichier.',
                    });
                    return;
                }
                
                // Désactiver le bouton d'import et afficher une animation de chargement
                importBtn.disabled = true;
                importBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-1"></i> Importation...';
                
                // Envoyer les données au serveur
                fetch('/clients/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        clients: clientsToImport
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: `${data.imported} clients ont été importés avec succès.`,
                        }).then(() => {
                            // Fermer la modal et rafraîchir la page
                            toggleImportModal();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message || 'Une erreur est survenue lors de l\'importation.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de l\'importation. Veuillez réessayer.',
                    });
                })
                .finally(() => {
                    // Réactiver le bouton d'import
                    importBtn.disabled = false;
                    importBtn.innerHTML = '<i class="bi bi-check-circle mr-1"></i> Importer';
                });
            }
            
            // Événement pour mettre à jour la prévisualisation lorsque l'option "ignorer l'en-tête" change
            skipHeaderRow.addEventListener('change', function() {
                createPreviewTable();
            });
        });
    </script>
</x-app-layout>