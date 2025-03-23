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
            <div class="flex space-x-4">
                <a href="{{ route('clients.edit', $client) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('clients.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Information du client -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Informations personnelles') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nom') }}</p>
                                    <p class="mt-1">{{ $client->name }}</p>
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
                                    <p class="mt-1">{{ $client->birth->format('d/m/Y') }}</p>
                                </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Client depuis') }}</p>
                                    <p class="mt-1">{{ $client->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Coordonnées') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Téléphone') }}</p>
                                    <div class="mt-1">
                                        @if($client->phones->count() > 0)
                                            @foreach($client->phones as $phone)
                                                <p class="flex items-center">
                                                    <i class="bi bi-telephone mr-2 text-indigo-500"></i>
                                                    {{ $phone->number }}
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
                                        {{ $client->email }}
                                    </p>
                                </div>
                                @endif
                                @if(isset($client->address))
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Adresse') }}</p>
                                    <p class="mt-1 flex items-center">
                                        <i class="bi bi-geo-alt mr-2 text-indigo-500"></i>
                                        {{ $client->address }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Statistiques client') }}</h3>
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
                                    <p class="mt-1">{{ number_format($stats['average_bill'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des factures -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Factures du client') }}</h3>
                        <a href="{{ route('bills.create', ['client_id' => $client->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                            <i class="bi bi-plus-lg mr-2"></i>
                            {{ __('Nouvelle facture') }}
                        </a>
                    </div>
                    
                    <!-- Barre de recherche -->
                    <div class="mb-6 relative">
                        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchBills"
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Rechercher une facture...">
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
                                @foreach($client->bills as $bill)
                                <tr class="bill-row hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
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
                                        <span class="px-3 py-1 inline-flex items-center text-sm font-semibold rounded-full
                                            {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                               ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800') }}">
                                            <i class="bi {{ $bill->status === 'paid' ? 'bi-check-circle' :
                                                           ($bill->status === 'pending' ? 'bi-clock' :
                                                            'bi-exclamation-circle') }} mr-1"></i>
                                            {{ ucfirst($bill->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
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
                // Recherche de factures
                const searchInput = document.getElementById('searchBills');
                const rows = document.querySelectorAll('#billsTableBody tr');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
