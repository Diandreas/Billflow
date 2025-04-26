<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    {{ __('Facture') }} : {{ $bill->reference }}
                    <span class="px-3 py-1 text-sm rounded-full
                        {{ $bill->status === 'paid' ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200' :
                           ($bill->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200' :
                            'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200') }}">
                        {{ ucfirst($bill->status ?? 'pending') }}
                    </span>
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Émise le') }} {{ $bill->date->format('d/m/Y') }} - {{ $bill->client->name }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('bills.download', $bill) }}"
                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-700 dark:hover:bg-indigo-600">
                    <i class="bi bi-download mr-1"></i>
                    {{ __('PDF') }}
                </a>
                <a href="{{ route('bills.edit', $bill) }}"
                   class="inline-flex items-center px-3 py-1.5 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600">
                    <i class="bi bi-pencil mr-1"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('bills.index') }}"
                   class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="bi bi-arrow-left mr-1"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <!-- Informations principales -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <!-- Facturé à -->
                            <div class="flex">
                                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-300 rounded-full mr-3">
                                    <i class="bi bi-person text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $bill->client->name }}</h3>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        @if(isset($bill->client->email))
                                            <div class="flex items-center">
                                                <i class="bi bi-envelope mr-1 text-xs"></i> {{ $bill->client->email }}
                                            </div>
                                        @endif
                                        @if($bill->client->phones && $bill->client->phones->count() > 0)
                                            <div class="flex items-center">
                                                <i class="bi bi-telephone mr-1 text-xs"></i> {{ $bill->client->phones->first()->number }}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('clients.show', $bill->client) }}" class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center">
                                        <i class="bi bi-box-arrow-up-right mr-1"></i> Détails client
                                    </a>
                                </div>
                            </div>

                            <!-- Détails de la facture -->
                            @php $settings = \App\Models\Setting::first(); @endphp
                            <div class="text-right">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Date d\'échéance') }}</div>
                                <div class="text-base font-medium text-gray-900 dark:text-gray-100">
                                    {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : $bill->date->addDays(30)->format('d/m/Y') }}
                                </div>
                                @if($settings)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $settings->company_name }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Section spéciale pour les factures de troc -->
                        @if($bill->is_barter_bill && $bill->barter)
                            <div class="mt-4 p-3 rounded-lg bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-medium text-purple-800 dark:text-purple-300 flex items-center">
                                        <i class="bi bi-arrow-left-right mr-2"></i>
                                        {{ __('Facture de Troc') }}
                                    </div>
                                    <a href="{{ route('barters.show', $bill->barter) }}" class="text-xs text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">
                                        {{ $bill->barter->reference }} <i class="bi bi-box-arrow-up-right ml-1"></i>
                                    </a>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex justify-between mb-1 text-xs">
                                        <span>{{ __('Valeur donnée par le client:') }}</span>
                                        <span class="font-medium">{{ number_format($bill->barter->value_given, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between mb-1 text-xs">
                                        <span>{{ __('Valeur reçue par le client:') }}</span>
                                        <span class="font-medium">{{ number_format($bill->barter->value_received, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between text-xs font-medium {{ $bill->barter->additional_payment > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        <span>{{ __('Paiement complémentaire:') }}</span>
                                        <span>{{ number_format(abs($bill->barter->additional_payment), 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Barre de progression du statut -->
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1.5">
                                @if($bill->status === 'paid')
                                    <div class="bg-green-600 dark:bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                                @elseif($bill->status === 'pending')
                                    <div class="bg-yellow-400 dark:bg-yellow-500 h-2 rounded-full" style="width: 50%"></div>
                                @else
                                    <div class="bg-red-600 dark:bg-red-500 h-2 rounded-full" style="width: 25%"></div>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ __('Émise') }}</span>
                                <span>{{ __('En attente') }}</span>
                                <span>{{ __('Payée') }}</span>
                            </div>
                        </div>

                        <!-- Boutons de changement de statut -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="paid">
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'paid' ? 'bg-green-600 dark:bg-green-700 text-white' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 hover:bg-green-600 dark:hover:bg-green-700 hover:text-white' }} border border-green-600 dark:border-green-700 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'paid' ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle-fill mr-1"></i>
                                    {{ __('Payée') }}
                                </button>
                            </form>

                            <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="pending">
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'pending' ? 'bg-yellow-600 dark:bg-yellow-700 text-white' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 hover:bg-yellow-600 dark:hover:bg-yellow-700 hover:text-white' }} border border-yellow-600 dark:border-yellow-700 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'pending' ? 'disabled' : '' }}>
                                    <i class="bi bi-clock-fill mr-1"></i>
                                    {{ __('En attente') }}
                                </button>
                            </form>

                            <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'cancelled' ? 'bg-red-600 dark:bg-red-700 text-white' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 hover:bg-red-600 dark:hover:bg-red-700 hover:text-white' }} border border-red-600 dark:border-red-700 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'cancelled' ? 'disabled' : '' }}>
                                    <i class="bi bi-x-circle-fill mr-1"></i>
                                    {{ __('Annulée') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Totaux & QR Code -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <!-- Résumé des totaux -->
                        <div class="mb-4">
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('Résumé') }}</h3>
                            @if(!$bill->is_barter_bill)
                                <div class="flex justify-between py-2 text-sm">
                                    <div class="text-gray-600 dark:text-gray-400">{{ __('Sous-total') }}</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($bill->total - $bill->tax_amount, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div class="flex justify-between py-2 text-sm border-b border-gray-200 dark:border-gray-700">
                                    <div class="text-gray-600 dark:text-gray-400">{{ __('TVA') }} ({{ $bill->tax_rate }}%)</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($bill->tax_amount, 0, ',', ' ') }} FCFA</div>
                                </div>
                            @endif
                            <div class="flex justify-between py-3 text-base font-bold">
                                <div class="text-gray-900 dark:text-gray-100">{{ __('Total') }}</div>
                                <div class="text-indigo-700 dark:text-indigo-400">{{ number_format($bill->total, 0, ',', ' ') }} FCFA</div>
                            </div>

                            @if($bill->status !== 'paid')
                                <div class="mt-3">
                                    <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form w-full">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-medium text-sm text-white hover:bg-green-700 dark:hover:bg-green-600">
                                            <i class="bi bi-check-circle mr-1"></i>
                                            {{ __('Marquer comme payée') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <!-- QR Code simplifié -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('QR Code d\'authenticité') }}</h3>
                            </div>
                            <div class="flex justify-center">
                                @php
                                    try {
                                        $qrCodeData = App::make(\App\Http\Controllers\BillController::class)->generateQrCode($bill);
                                        $error = null;
                                    } catch (\Exception $e) {
                                        $qrCodeData = null;
                                        $error = $e->getMessage();
                                    }
                                @endphp

                                @if($qrCodeData)
                                    <img src="data:image/png;base64,{{ $qrCodeData }}" class="w-28 h-28" alt="QR Code">
                                @else
                                    <div class="w-28 h-28 flex items-center justify-center bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs text-center">
                                        QR code non disponible
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-2">{{ __('Scanner pour vérifier l\'authenticité') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
                <div id="products">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">{{ __('Produits et services') }}</h3>
                        <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-medium px-2 py-0.5 rounded inline-flex items-center">
                            <i class="bi bi-box-seam mr-1"></i>
                            {{ $bill->items->count() }} {{ __('articles') }}
                        </span>
                    </div>

                    <!-- Barre de recherche et filtres condensés -->
                    <div class="mb-3 flex flex-col sm:flex-row sm:items-center gap-2">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <input type="text" id="searchProduct" placeholder="Rechercher un produit..."
                                   class="w-full pl-9 py-1.5 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        </div>

                        <select id="priceFilter" class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100 text-sm py-1.5">
                            <option value="">{{ __('Tous les prix') }}</option>
                            @php
                                $uniquePrices = $bill->items->pluck('unit_price')->unique()->sort();
                                $priceGroups = $bill->items->groupBy('unit_price');
                            @endphp
                            @foreach($uniquePrices as $price)
                                <option value="{{ $price }}">
                                    {{ number_format($price, 0, ',', ' ') }} FCFA ({{ $priceGroups[$price]->count() }})
                                </option>
                            @endforeach
                        </select>

                        <button id="resetFilters" class="inline-flex items-center px-3 py-1.5 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm">
                            <i class="bi bi-x-circle mr-1"></i>
                            {{ __('Réinitialiser') }}
                        </button>
                    </div>

                    <!-- Message résultats -->
                    <div id="price-summary" class="hidden mb-3 py-2 px-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-md border border-indigo-100 dark:border-indigo-800 text-sm">
                        <div class="flex justify-between items-center">
                            <span id="price-count" class="text-indigo-700 dark:text-indigo-300"></span>
                            <span id="price-total" class="font-medium text-indigo-800 dark:text-indigo-200"></span>
                        </div>
                    </div>

                    <div id="noProductResults" class="hidden py-2 px-3 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm rounded-md">
                        {{ __('Aucun produit ne correspond à votre recherche.') }}
                    </div>

                    <!-- Tableau des produits -->
                    <div class="overflow-x-auto mt-2">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Produit') }}
                                </th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Qté') }}
                                </th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Prix unitaire') }}
                                </th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Total') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($bill->items as $item)
                                <tr class="product-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    data-name="{{ $item->product ? strtolower($item->product->name) : strtolower($item->name ?? '') }}"
                                    data-price="{{ $item->unit_price }}">
                                    <td class="px-4 py-2">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            @if($item->product)
                                                <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center">
                                                    {{ $item->product->name }}
                                                    <i class="bi bi-box-arrow-up-right text-xs ml-1"></i>
                                                </a>
                                            @else
                                                {{ $item->name ?? 'Paiement complémentaire' }}
                                            @endif
                                        </div>
                                        @if($item->product && $item->product->type != 'service')
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                SKU: {{ $item->product->sku ?: 'N/A' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                            <span class="bg-blue-50 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium px-2 py-0.5 rounded">
                                                {{ $item->quantity }}
                                            </span>
                                    </td>
                                    <td class="px-4 py-2 text-right text-sm">
                                        <div class="price-display text-gray-700 dark:text-gray-300 font-medium">
                                            {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                        </div>
                                        @if($item->product && $item->product->default_price != $item->unit_price)
                                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                                    <span class="{{ $item->product->default_price > $item->unit_price ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $item->product->default_price > $item->unit_price ? '-' : '+' }}{{ number_format(abs(($item->product->default_price - $item->unit_price) / $item->product->default_price * 100), 0) }}%
                                                    </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100 font-medium">
                                        {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes et description -->
            @if($bill->description)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Description') }}</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3 text-gray-700 dark:text-gray-300 text-sm">
                        {{ $bill->description }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const statusForms = document.querySelectorAll('.status-update-form');
            const searchInput = document.getElementById('searchProduct');
            const priceFilter = document.getElementById('priceFilter');
            const productRows = document.querySelectorAll('.product-row');
            const noProductResults = document.getElementById('noProductResults');
            const resetButton = document.getElementById('resetFilters');
            const priceSummary = document.getElementById('price-summary');
            const priceCount = document.getElementById('price-count');
            const priceTotal = document.getElementById('price-total');

            // Formatter pour les nombres
            const formatter = new Intl.NumberFormat('fr-FR');

            // 1. Gestionnaire pour les formulaires de mise à jour de statut
            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    updateBillStatus(form);
                });
            });

            // 2. Fonctionnalité de recherche et filtre pour les produits
            if (searchInput) {
                searchInput.addEventListener('input', debounce(filterProducts, 300));
            }

            if (priceFilter) {
                priceFilter.addEventListener('change', filterProducts);
            }

            if (resetButton) {
                resetButton.addEventListener('click', resetFilters);
            }

            // Fonction pour mettre à jour le statut de la facture
            function updateBillStatus(form) {
                const formData = new FormData(form);
                const url = form.getAttribute('action');
                const newStatus = formData.get('status');

                // Désactiver tous les boutons pendant la requête
                document.querySelectorAll('.status-update-form button').forEach(btn => {
                    btn.disabled = true;
                });

                // Animation de chargement
                const submitButton = form.querySelector('button');
                const originalContent = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-1"></i> Traitement...';

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
                        return response.json();
                    })
                    .then(data => {
                        // 1. Mettre à jour le badge de statut dans l'en-tête
                        const statusBadge = document.querySelector('.text-2xl .px-3.py-1.text-sm.rounded-full');
                        if (statusBadge) {
                            statusBadge.className = `px-3 py-1 text-sm rounded-full ${
                                newStatus === 'paid' ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200' :
                                    newStatus === 'pending' ? 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200' :
                                        'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200'
                            }`;
                            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        }

                        // 2. Mettre à jour la barre de progression
                        const progressBar = document.querySelector('.bg-gray-200.dark\\:bg-gray-700.rounded-full.h-2 div');
                        if (progressBar) {
                            progressBar.className = `h-2 rounded-full ${
                                newStatus === 'paid' ? 'bg-green-600 dark:bg-green-500' :
                                    newStatus === 'pending' ? 'bg-yellow-400 dark:bg-yellow-500' :
                                        'bg-red-600 dark:bg-red-500'
                            }`;
                            progressBar.style.width = newStatus === 'paid' ? '100%' :
                                newStatus === 'pending' ? '50%' : '25%';
                        }

                        // 3. Mettre à jour les styles des boutons
                        updateStatusButtons(newStatus);

                        // 4. Afficher le message de succès
                        showNotification(data.message, 'success');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Une erreur est survenue lors de la mise à jour du statut', 'error');
                    })
                    .finally(() => {
                        // Restaurer le contenu original du bouton
                        submitButton.innerHTML = originalContent;
                        // Réactiver les boutons appropriés
                        document.querySelectorAll('.status-update-form button').forEach(btn => {
                            btn.disabled = false;
                        });
                    });
            }

            // Mise à jour des boutons de statut
            function updateStatusButtons(activeStatus) {
                document.querySelectorAll('.status-update-form').forEach(form => {
                    const status = form.querySelector('input[name="status"]').value;
                    const button = form.querySelector('button');
                    const isActive = status === activeStatus;

                    button.disabled = isActive;

                    // Définir les classes pour chaque bouton
                    let baseClasses = 'inline-flex items-center px-3 py-1.5 border rounded-md text-sm font-medium transition-colors duration-200';
                    let statusClasses = '';

                    if (status === 'paid') {
                        statusClasses = isActive
                            ? 'bg-green-600 dark:bg-green-700 text-white border-green-600 dark:border-green-700'
                            : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 hover:bg-green-600 dark:hover:bg-green-700 hover:text-white border-green-600 dark:border-green-700';
                    } else if (status === 'pending') {
                        statusClasses = isActive
                            ? 'bg-yellow-600 dark:bg-yellow-700 text-white border-yellow-600 dark:border-yellow-700'
                            : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 hover:bg-yellow-600 dark:hover:bg-yellow-700 hover:text-white border-yellow-600 dark:border-yellow-700';
                    } else if (status === 'cancelled') {
                        statusClasses = isActive
                            ? 'bg-red-600 dark:bg-red-700 text-white border-red-600 dark:border-red-700'
                            : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 hover:bg-red-600 dark:hover:bg-red-700 hover:text-white border-red-600 dark:border-red-700';
                    }

                    button.className = `${baseClasses} ${statusClasses}`;
                });
            }

            // Filtrage des produits
            function filterProducts() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedPrice = priceFilter ? priceFilter.value : '';
                let visibleCount = 0;
                let totalForPrice = 0;

                // Réinitialiser les styles de mise en évidence
                productRows.forEach(row => {
                    row.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/30', 'found-product');
                    const priceDisplay = row.querySelector('.price-display');
                    if (priceDisplay) {
                        priceDisplay.classList.remove('text-indigo-700', 'dark:text-indigo-400', 'font-bold');
                    }
                });

                // Filtrer les produits
                productRows.forEach(row => {
                    const name = row.dataset.name;
                    const price = row.dataset.price;

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesPrice = !selectedPrice || price === selectedPrice;

                    // Pour le calcul du total pour un prix spécifique
                    if (matchesPrice && price === selectedPrice) {
                        // Extraire le total du produit
                        const totalText = row.cells[3].textContent.trim().replace(/[^\d]/g, '');
                        totalForPrice += parseInt(totalText) || 0;
                    }

                    // Appliquer les filtres
                    if (matchesSearch && matchesPrice) {
                        row.style.display = '';
                        if (selectedPrice || searchTerm) {
                            row.classList.add('bg-indigo-50', 'dark:bg-indigo-900/30', 'found-product');

                            // Mise en évidence du prix si filtré par prix
                            if (selectedPrice) {
                                const priceDisplay = row.querySelector('.price-display');
                                if (priceDisplay) {
                                    priceDisplay.classList.add('text-indigo-700', 'dark:text-indigo-400', 'font-bold');
                                }
                            }
                        }
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Afficher le résumé pour le prix sélectionné
                if (selectedPrice && priceSummary) {
                    priceSummary.classList.remove('hidden');
                    if (priceCount) {
                        priceCount.textContent = `${visibleCount} produit(s) au prix de ${formatter.format(selectedPrice)} FCFA`;
                    }
                    if (priceTotal) {
                        priceTotal.textContent = `Total: ${formatter.format(totalForPrice)} FCFA`;
                    }
                } else if (priceSummary) {
                    priceSummary.classList.add('hidden');
                }

                // Gérer l'affichage du message "aucun résultat"
                if (noProductResults) {
                    if (visibleCount === 0 && productRows.length > 0 && (selectedPrice || searchTerm)) {
                        noProductResults.classList.remove('hidden');
                    } else {
                        noProductResults.classList.add('hidden');
                    }
                }
            }

            // Réinitialiser les filtres
            function resetFilters() {
                if (searchInput) searchInput.value = '';
                if (priceFilter) priceFilter.value = '';
                filterProducts();
            }

            // Fonction utilitaire pour le debounce de la recherche
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            // Fonction pour afficher les notifications
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 max-w-sm p-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-y-0 ${
                    type === 'success' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                        'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
                }`;

                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);

                // Animation d'entrée
                setTimeout(() => {
                    notification.classList.add('translate-y-0');
                    notification.classList.remove('-translate-y-8', 'opacity-0');
                }, 10);

                // Animation de sortie et suppression
                setTimeout(() => {
                    notification.classList.add('opacity-0', 'translate-y-4');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        });
    </script>
</x-app-layout>
