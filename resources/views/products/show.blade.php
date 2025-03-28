<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ $product->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails du produit et statistiques') }}
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('products.edit', $product) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Information du produit -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Information produit') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Nom') }}</p>
                                    <p class="mt-1">{{ $product->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Description') }}</p>
                                    <p class="mt-1">{{ $product->description ?: 'Pas de description' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Prix par défaut') }}</p>
                                    <p class="mt-1">{{ number_format($product->default_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Date de création') }}</p>
                                    <p class="mt-1">{{ $product->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Statistiques de ventes') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Utilisé dans') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['usage_count'] }} factures</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Quantité totale vendue') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['total_quantity'] }} unités</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Chiffre d\'affaires total') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($stats['total_sales'], 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Historique d\'utilisation') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Prix moyen utilisé') }}</p>
                                    <p class="mt-1">{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Première utilisation') }}</p>
                                    <p class="mt-1">{{ $stats['first_use'] ? $stats['first_use']->format('d/m/Y') : 'Jamais utilisé' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Dernière utilisation') }}</p>
                                    <p class="mt-1">{{ $stats['last_use'] ? $stats['last_use']->format('d/m/Y') : 'Jamais utilisé' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des prix -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Historique des prix') }}</h3>
                    
                    @if(count($priceHistory) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Prix utilisé') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nombre d\'utilisations') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($priceHistory as $price)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($price->unit_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $price->usage_count }} fois
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>{{ __('Aucun historique de prix disponible') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Factures associées -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Factures contenant ce produit</h3>
                    
                    <div class="mb-4">
                        <input type="text" id="searchInvoice" placeholder="Rechercher par référence, client ou date..." class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    @if($invoices->isEmpty())
                        <p class="text-gray-500">Aucune facture ne contient ce produit pour le moment.</p>
                    @else
                        <div id="noInvoiceResults" class="text-gray-500 py-4 hidden">
                            <p>Aucune facture ne correspond à votre recherche.</p>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Référence
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Client
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Quantité
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr class="invoice-row" 
                                           data-reference="{{ strtolower($invoice->reference) }}" 
                                           data-client="{{ strtolower($invoice->client->name) }}" 
                                           data-date="{{ $invoice->date->format('d/m/Y') }}">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">{{ $invoice->reference }}</a>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->client->name }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->status }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $invoice->pivot->quantity }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ number_format($invoice->pivot->total, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <a href="{{ route('bills.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Voir') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    <script>
        // Graphique des ventes
        document.addEventListener('DOMContentLoaded', function() {
            // Code existant pour le graphique (si présent)
            
            // Fonctionnalité de recherche pour les factures
            const searchInput = document.getElementById('searchInvoice');
            const invoiceRows = document.querySelectorAll('.invoice-row');
            const noInvoiceResults = document.getElementById('noInvoiceResults');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;
                    
                    invoiceRows.forEach(row => {
                        const reference = row.dataset.reference;
                        const client = row.dataset.client;
                        const date = row.dataset.date;
                        
                        if (reference.includes(searchTerm) || client.includes(searchTerm) || date.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    // Afficher un message si aucun résultat
                    if (visibleCount === 0 && invoiceRows.length > 0) {
                        noInvoiceResults.classList.remove('hidden');
                    } else {
                        noInvoiceResults.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
