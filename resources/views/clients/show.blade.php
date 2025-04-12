<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ $client->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails et historique du client') }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                @if($stats['total_bills'] > 5 || $stats['total_revenue'] > 1000000)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800"
                          title="{{ __('Ce client a un volume de commande élevé') }}">
                        <i class="bi bi-award mr-1"></i>
                        {{ __('Client VIP') }}
                    </span>
                @endif

                @if($client->created_at && $client->created_at->diffInMonths(now()) > 12)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                          title="{{ __('Client depuis plus d\'un an') }}">
                        <i class="bi bi-heart mr-1"></i>
                        {{ __('Client fidèle') }}
                    </span>
                @endif
                
                <a href="{{ route('clients.edit', $client) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                   title="{{ __('Modifier les informations de ce client') }}">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('clients.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50"
                   title="{{ __('Retourner à la liste des clients') }}">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques sommaires -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-green-100 mr-4">
                            <i class="bi bi-receipt text-xl text-green-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Total Factures') }}</div>
                            <div class="text-2xl font-bold text-gray-900" title="{{ __('Nombre total de factures émises pour ce client') }}">{{ $stats['total_bills'] }}</div>
                            <a href="#bills" class="text-xs text-green-600 hover:text-green-800 inline-flex items-center mt-1">
                                {{ __('Voir les factures') }} <i class="bi bi-arrow-down ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-indigo-100 mr-4">
                            <i class="bi bi-cash-coin text-xl text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires') }}</div>
                            <div class="text-2xl font-bold text-indigo-700" title="{{ __('Montant total facturé à ce client') }}">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</div>
                            <div class="text-xs text-gray-500 mt-1" title="{{ __('Montant moyen par facture pour ce client') }}">
                                {{ __('Valeur moyenne') }}: {{ number_format($stats['average_bill'], 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-start">
                        <div class="rounded-full p-3 bg-amber-100 mr-4">
                            <i class="bi bi-calendar-check text-xl text-amber-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">{{ __('Client depuis') }}</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $client->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1" title="{{ __('Date de création du client dans le système') }}">
                                {{ $client->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information du client -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                <i class="bi bi-person text-indigo-500 mr-2"></i>
                                {{ __('Informations personnelles') }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nom') }}</p>
                                    <p class="mt-1 flex items-center">
                                        <i class="bi bi-person-circle mr-2 text-gray-400"></i>
                                        {{ $client->name }}
                                    </p>
                                </div>
                                @if($client->sex)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Sexe') }}</p>
                                    <p class="mt-1">
                                        @if($client->sex == 'M')
                                            <i class="bi bi-gender-male text-blue-500"></i> Masculin
                                        @elseif($client->sex == 'F')
                                            <i class="bi bi-gender-female text-pink-500"></i> Féminin
                                        @else
                                            <i class="bi bi-gender-ambiguous text-purple-500"></i> Autre
                                        @endif
                                    </p>
                                </div>
                                @endif
                                @if($client->birth)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Date de naissance') }}</p>
                                    <p class="mt-1 flex items-center">
                                        <i class="bi bi-calendar-date mr-2 text-gray-400"></i>
                                        {{ $client->birth->format('d/m/Y') }}
                                        <span class="ml-2 text-xs text-gray-500">
                                            ({{ $client->birth->age }} ans)
                                        </span>
                                    </p>
                                </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Statut du client') }}</p>
                                    <div class="mt-1">
                                        @if($stats['total_bills'] > 10)
                                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                                  title="{{ __('Plus de 10 factures, client très régulier') }}">
                                                <i class="bi bi-stars mr-1"></i>
                                                {{ __('Premium') }}
                                            </span>
                                        @elseif($stats['total_bills'] > 5)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                                  title="{{ __('Plus de 5 factures, client régulier') }}">
                                                <i class="bi bi-star-half mr-1"></i>
                                                {{ __('Régulier') }}
                                            </span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded"
                                                  title="{{ __('Client avec moins de 5 factures') }}">
                                                <i class="bi bi-person-check mr-1"></i>
                                                {{ __('Standard') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                <i class="bi bi-telephone text-green-500 mr-2"></i>
                                {{ __('Coordonnées') }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Téléphone') }}</p>
                                    <div class="mt-1">
                                        @if($client->phones->count() > 0)
                                            @foreach($client->phones as $phone)
                                                <p class="flex items-center">
                                                    <i class="bi bi-telephone mr-2 text-indigo-500"></i>
                                                    {{ $phone->number }}
                                                    @if($loop->first)
                                                        <span class="ml-2 text-xs px-1 py-0.5 bg-indigo-100 text-indigo-800 rounded" title="{{ __('Numéro principal') }}">Principal</span>
                                                    @endif
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-gray-400">{{ __('Aucun téléphone enregistré') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(isset($client->email))
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Email') }}</p>
                                    <p class="mt-1 flex items-center">
                                        <i class="bi bi-envelope mr-2 text-indigo-500"></i>
                                        <a href="mailto:{{ $client->email }}" class="text-indigo-600 hover:text-indigo-800"
                                           title="{{ __('Envoyer un email au client') }}">
                                            {{ $client->email }}
                                        </a>
                                    </p>
                                </div>
                                @endif
                                @if(isset($client->address))
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Adresse') }}</p>
                                    <p class="mt-1 flex items-start">
                                        <i class="bi bi-geo-alt mt-1 mr-2 text-indigo-500"></i>
                                        <span title="{{ __('Adresse postale du client') }}">{{ $client->address }}</span>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center">
                                <i class="bi bi-graph-up text-red-500 mr-2"></i>
                                {{ __('Statistiques client') }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nombre de factures') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['total_bills'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires total') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Valeur moyenne par facture') }}</p>
                                    <p class="mt-1 flex items-center">
                                        {{ number_format($stats['average_bill'], 0, ',', ' ') }} FCFA
                                        @if($stats['average_bill'] > 100000)
                                            <span class="ml-2 text-xs text-green-600" title="{{ __('Panier moyen supérieur à 100 000 FCFA') }}">
                                                <i class="bi bi-arrow-up"></i> Élevé
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Dernière commande') }}</p>
                                    <p class="mt-1">
                                        @if(isset($stats['last_bill_date']))
                                            <span class="flex items-center">
                                                <i class="bi bi-calendar-date mr-2 text-gray-400"></i>
                                                {{ $stats['last_bill_date']->format('d/m/Y') }}
                                                <span class="ml-2 text-xs {{ $stats['last_bill_date']->diffInDays(now()) > 90 ? 'text-red-600' : 'text-gray-500' }}"
                                                      title="{{ __('Date de la dernière facture émise pour ce client') }}">
                                                    ({{ $stats['last_bill_date']->diffForHumans() }})
                                                </span>
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ __('Aucune commande') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des factures -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" id="bills">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <i class="bi bi-receipt-cutoff text-indigo-500 mr-2"></i>
                                {{ __('Factures du client') }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ __('Historique complet des factures et commandes') }}
                            </p>
                        </div>
                        <a href="{{ route('bills.create', ['client_id' => $client->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                           title="{{ __('Créer une nouvelle facture pour ce client') }}">
                            <i class="bi bi-plus-lg mr-2"></i>
                            {{ __('Nouvelle facture') }}
                        </a>
                    </div>
                    
                    <!-- Filtres et recherche -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input type="text" id="searchBills" placeholder="Rechercher une facture..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select id="statusFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="paid">Payée</option>
                                <option value="pending">En attente</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                        </div>
                        <div>
                            <select id="dateFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Toutes les dates</option>
                                <option value="month">Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                                <option value="year">Cette année</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($client->bills->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="bi bi-hash mr-1"></i>{{ __('Référence') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="bi bi-calendar2 mr-1"></i>{{ __('Date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="bi bi-card-checklist mr-1"></i>{{ __('Produits') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Total HT') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('TVA') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Total TTC') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="bi bi-check2-circle mr-1"></i>{{ __('Statut') }}
                                    </th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="billsTableBody">
                                @foreach($paginatedBills as $bill)
                                <tr class="bill-row hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $bill->reference }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $bill->date->format('d/m/Y') }}
                                        @if($bill->date->isToday())
                                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">
                                                {{ __('Aujourd\'hui') }}
                                            </span>
                                        @elseif($bill->date->isCurrentWeek())
                                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">
                                                {{ __('Cette semaine') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-900" title="{{ $bill->products->pluck('name')->implode(', ') }}">{{ $bill->products->count() }} produit(s)</span>
                                            @if($bill->products->count() > 5)
                                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded"
                                                      title="{{ __('Facture volumineuse') }}">
                                                    <i class="bi bi-cart-check"></i>
                                                </span>
                                            @endif
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
                                        <span class="px-3 py-1 inline-flex items-center text-xs font-semibold rounded-full
                                            {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                               ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800') }}"
                                            title="{{ $bill->status === 'paid' ? __('Facture payée') : ($bill->status === 'pending' ? __('En attente de paiement') : __('Facture annulée')) }}">
                                            <i class="bi {{ $bill->status === 'paid' ? 'bi-check-circle' :
                                                          ($bill->status === 'pending' ? 'bi-clock' :
                                                           'bi-exclamation-circle') }} mr-1"></i>
                                            {{ ucfirst($bill->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="{{ __('Voir le détail de la facture') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('bills.edit', $bill) }}" class="text-amber-600 hover:text-amber-900" title="{{ __('Modifier la facture') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $paginatedBills->links() }}
                    </div>
                    @else
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <i class="bi bi-receipt-cutoff text-gray-400 text-5xl mb-3 block"></i>
                        <p class="text-gray-500 mb-4">{{ __('Ce client n\'a pas encore de factures') }}</p>
                        <a href="{{ route('bills.create', ['client_id' => $client->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            <i class="bi bi-plus-lg mr-2"></i>
                            {{ __('Créer sa première facture') }}
                        </a>
                    </div>
                    @endif
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
            const searchBills = document.getElementById('searchBills');
            const statusFilter = document.getElementById('statusFilter');
            const dateFilter = document.getElementById('dateFilter');
            const billRows = document.querySelectorAll('.bill-row');

            if(searchBills && statusFilter && dateFilter) {
                const filterBills = () => {
                    const searchTerm = searchBills.value.toLowerCase();
                    const statusTerm = statusFilter.value.toLowerCase();
                    const dateTerm = dateFilter.value;

                    billRows.forEach(row => {
                        const reference = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                        const statusText = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                        const dateElement = row.querySelector('td:nth-child(2)');
                        const dateText = dateElement.textContent.split(' ')[0]; // First part before any badges
                        const dateParts = dateText.split('/');
                        const date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                        const now = new Date();

                        let showBySearch = !searchTerm || reference.includes(searchTerm);
                        let showByStatus = !statusTerm || statusText.includes(statusTerm);
                        
                        let showByDate = true;
                        if (dateTerm === 'month') {
                            showByDate = date.getMonth() === now.getMonth() && 
                                        date.getFullYear() === now.getFullYear();
                        } else if (dateTerm === 'quarter') {
                            const currentQuarter = Math.floor(now.getMonth() / 3);
                            const dateQuarter = Math.floor(date.getMonth() / 3);
                            showByDate = dateQuarter === currentQuarter && 
                                        date.getFullYear() === now.getFullYear();
                        } else if (dateTerm === 'year') {
                            showByDate = date.getFullYear() === now.getFullYear();
                        }

                        row.style.display = (showBySearch && showByStatus && showByDate) ? '' : 'none';
                    });
                };

                searchBills.addEventListener('input', filterBills);
                statusFilter.addEventListener('change', filterBills);
                dateFilter.addEventListener('change', filterBills);
            }
        });
    </script>
    @endpush
</x-app-layout>
