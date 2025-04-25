<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Facture') }} : {{ $bill->reference }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails et informations de la facture') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('bills.download', $bill) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('Télécharger PDF') }}
                </a>
                <a href="{{ route('bills.edit', $bill) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('bills.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Section spéciale pour les factures de troc -->
            @if($bill->is_barter_bill && $bill->barter)
                <div class="bg-purple-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-purple-900">{{ __('Facture de Troc') }}</h3>
                            <span class="px-4 py-2 inline-flex items-center text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                            <i class="bi bi-arrow-left-right mr-2"></i>
                            {{ __('Associée à un troc') }}
                        </span>
                        </div>

                        <div class="bg-white rounded-lg p-4 border border-purple-200">
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('Cette facture a été générée automatiquement à partir d\'un troc. Elle représente le paiement complémentaire résultant de la différence de valeur entre les articles échangés.') }}
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Informations du troc') }}</h4>
                                    <p class="text-sm mb-1"><span class="font-medium">{{ __('Référence du troc:') }}</span> {{ $bill->barter->reference }}</p>
                                    <p class="text-sm mb-1"><span class="font-medium">{{ __('Type de troc:') }}</span>
                                        {{ $bill->barter->type == 'same_type' ? 'Même type' : 'Types différents' }}
                                    </p>
                                    <p class="text-sm mb-1"><span class="font-medium">{{ __('Date du troc:') }}</span>
                                        {{ $bill->barter->created_at->format('d/m/Y') }}
                                    </p>
                                    <a href="{{ route('barters.show', $bill->barter) }}" class="text-sm text-purple-600 hover:text-purple-800 inline-flex items-center mt-2">
                                        <i class="bi bi-arrow-up-right-square mr-1"></i>
                                        {{ __('Voir les détails du troc') }}
                                    </a>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Valeurs échangées') }}</h4>
                                    <p class="text-sm mb-1"><span class="font-medium">{{ __('Valeur donnée par le client:') }}</span>
                                        {{ number_format($bill->barter->value_given, 0, ',', ' ') }} FCFA
                                    </p>
                                    <p class="text-sm mb-1"><span class="font-medium">{{ __('Valeur reçue par le client:') }}</span>
                                        {{ number_format($bill->barter->value_received, 0, ',', ' ') }} FCFA
                                    </p>
                                    <p class="text-sm font-medium {{ $bill->barter->additional_payment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        <span class="font-medium">{{ __('Paiement complémentaire:') }}</span>
                                        {{ number_format(abs($bill->barter->additional_payment), 0, ',', ' ') }} FCFA
                                        ({{ $bill->barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Statistiques -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Résumé') }}</h3>
                        <span class="px-4 py-2 inline-flex items-center text-sm font-semibold rounded-full
                            {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                               ($bill->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-red-100 text-red-800') }}">
                            <i class="bi {{ $bill->status === 'paid' ? 'bi-check-circle' :
                                           ($bill->status === 'pending' ? 'bi-clock' :
                                            'bi-exclamation-circle') }} mr-2"></i>
                            {{ ucfirst($bill->status ?? 'pending') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-indigo-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-indigo-100 p-3 mr-4">
                                <i class="bi bi-receipt text-xl text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Référence') }}</p>
                                <p class="text-xl font-bold text-indigo-700">{{ $bill->reference }}</p>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-green-100 p-3 mr-4">
                                <i class="bi bi-calendar-date text-xl text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Date') }}</p>
                                <p class="text-xl font-bold text-green-700">{{ $bill->date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">
                                    @php
                                        $daysAgo = now()->diffInDays($bill->date);
                                        $isRecent = $daysAgo <= 7;
                                        $isOld = $daysAgo >= 30;
                                    @endphp
                                    @if($isRecent)
                                        <span class="text-green-600">{{ __('Récente') }}</span>
                                    @elseif($isOld)
                                        <span class="text-gray-600">{{ __('Il y a plus d\'un mois') }}</span>
                                    @else
                                        <span>{{ __('Il y a') }} {{ $daysAgo }} {{ __('jours') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-purple-100 p-3 mr-4">
                                <i class="bi bi-box-seam text-xl text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Produits') }}</p>
                                <p class="text-xl font-bold text-purple-700">{{ $bill->items->count() }}</p>
                                <a href="#products" class="text-xs text-purple-600 hover:text-purple-800 inline-flex items-center">
                                    {{ __('Voir le détail') }} <i class="bi bi-arrow-down ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="bi bi-cash-stack text-xl text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Total') }}</p>
                                <p class="text-xl font-bold text-blue-700">{{ number_format($bill->total, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500">
                                    {{ __('TVA incluse') }}: {{ number_format($bill->tax_amount, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Indicateur de statut de paiement avec barre de progression -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Statut de paiement') }}</h3>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Date d\'échéance') }}</p>
                            <p class="text-lg font-semibold">{{ $bill->due_date ? $bill->due_date->format('d/m/Y') : $bill->date->addDays(30)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        @if($bill->status === 'paid')
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: 100%"></div>
                        @elseif($bill->status === 'pending')
                            <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 50%"></div>
                        @else
                            <div class="bg-red-600 h-2.5 rounded-full" style="width: 25%"></div>
                        @endif
                    </div>

                    <div class="flex justify-between text-xs text-gray-600">
                        <span>{{ __('Émise') }}</span>
                        <span>{{ __('En attente') }}</span>
                        <span>{{ __('Payée') }}</span>
                    </div>

                    <!-- Boutons de changement de statut -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'paid' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-600 hover:text-white' }} border border-green-600 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'paid' ? 'disabled' : '' }}
                                    title="{{ __('Marquer cette facture comme payée') }}">
                                <i class="bi bi-check-circle-fill mr-1"></i>
                                {{ __('Marquer comme Payée') }}
                            </button>
                        </form>

                        <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-600 hover:text-white' }} border border-yellow-600 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'pending' ? 'disabled' : '' }}
                                    title="{{ __('Marquer cette facture comme en attente de paiement') }}">
                                <i class="bi bi-clock-fill mr-1"></i>
                                {{ __('Marquer comme En attente') }}
                            </button>
                        </form>

                        <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 {{ $bill->status === 'cancelled' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800 hover:bg-red-600 hover:text-white' }} border border-red-600 rounded-md text-sm font-medium transition-colors duration-200"
                                    {{ $bill->status === 'cancelled' ? 'disabled' : '' }}
                                    title="{{ __('Marquer cette facture comme annulée') }}">
                                <i class="bi bi-x-circle-fill mr-1"></i>
                                {{ __('Marquer comme Annulée') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- QR Code d'authenticité -->
                <div class="mb-8 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('QR Code d\'authenticité') }}</h3>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="p-4 bg-gray-50 rounded-lg inline-flex flex-col items-center">
                            @php
                                try {
                                    $qrCodeData = App::make(\App\Http\Controllers\BillController::class)->generateQrCode($bill);
                                    $error = null;
                                } catch (\Exception $e) {
                                    $qrCodeData = null;
                                    $error = $e->getMessage();
                                }
                            @endphp

                            @if($error)
                                <div class="text-red-500 mb-2">Erreur: {{ $error }}</div>
                            @endif

                            @if(session('qr_error'))
                                <div class="text-red-500 mb-2">{{ session('qr_error') }}</div>
                            @endif

                            @if($qrCodeData)
                                <img src="data:image/png;base64,{{ $qrCodeData }}" class="w-32 h-32" alt="QR Code">
                            @else
                                <div class="w-32 h-32 flex items-center justify-center bg-gray-200 text-gray-500">
                                    QR code non disponible
                                    @if(config('app.debug'))
                                        <div class="text-xs text-red-400 mt-2">
                                            Vérifiez les extensions PHP: GD ou Imagick
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 mt-2">{{ __('Scanner pour vérifier l\'authenticité') }}</p>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p class="mb-2">{{ __('Ce QR code contient les informations sécurisées de cette facture:') }}</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>{{ __('Référence') }}: {{ $bill->reference }}</li>
                                <li>{{ __('Date') }}: {{ $bill->date->format('d/m/Y') }}</li>
                                <li>{{ __('Client') }}: {{ $bill->client->name }}</li>
                                <li>{{ __('Montant') }}: {{ number_format($bill->total, 0, ',', ' ') }} FCFA</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Informations client et entreprise -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-500 mb-1">{{ __('Facturé à') }}</div>
                        <div class="text-lg font-bold mb-2 flex items-center">
                            <i class="bi bi-person-circle mr-2 text-indigo-500"></i>
                            {{ $bill->client->name }}
                            <a href="{{ route('clients.show', $bill->client) }}" class="ml-2 text-xs text-indigo-600 hover:text-indigo-800">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                        @if(isset($bill->client->address))
                            <div class="text-gray-700 mb-1 flex items-start">
                                <i class="bi bi-geo-alt mt-1 mr-2 text-gray-500"></i>
                                <span>{{ $bill->client->address }}</span>
                            </div>
                        @endif
                        @if($bill->client->phones && $bill->client->phones->count() > 0)
                            <div class="text-gray-700 mb-1 flex items-start">
                                <i class="bi bi-telephone mt-1 mr-2 text-gray-500"></i>
                                <span>{{ $bill->client->phones->first()->number }}</span>
                            </div>
                        @endif
                        @if(isset($bill->client->email))
                            <div class="text-gray-700 flex items-start">
                                <i class="bi bi-envelope mt-1 mr-2 text-gray-500"></i>
                                <span>{{ $bill->client->email }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @php $settings = \App\Models\Setting::first(); @endphp
                        @if($settings)
                            <div class="text-sm text-gray-500 mb-1 text-right">{{ __('Émis par') }}</div>
                            <div class="text-lg font-bold mb-2 text-right">{{ $settings->company_name }}</div>
                            @if($settings->address)
                                <div class="text-gray-700 mb-1 flex items-start justify-end">
                                    <span>{{ $settings->address }}</span>
                                    <i class="bi bi-geo-alt mt-1 ml-2 text-gray-500"></i>
                                </div>
                            @endif
                            @if($settings->phone)
                                <div class="text-gray-700 mb-1 flex items-start justify-end">
                                    <span>{{ $settings->phone }}</span>
                                    <i class="bi bi-telephone mt-1 ml-2 text-gray-500"></i>
                                </div>
                            @endif
                            @if($settings->email)
                                <div class="text-gray-700 mb-1 flex items-start justify-end">
                                    <span>{{ $settings->email }}</span>
                                    <i class="bi bi-envelope mt-1 ml-2 text-gray-500"></i>
                                </div>
                            @endif
                            @if($settings->siret)
                                <div class="text-gray-700 flex items-start justify-end">
                                    <span>SIRET: {{ $settings->siret }}</span>
                                    <i class="bi bi-building mt-1 ml-2 text-gray-500"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if($bill->description)
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                            {{ $bill->description }}
                        </div>
                    </div>
                @endif

                <!-- Tableau des produits -->
                <div class="mb-8" id="products">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Produits et services') }}</h3>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded inline-flex items-center">
                            <i class="bi bi-box-seam mr-1"></i>
                            {{ $bill->items->count() }} {{ __('articles') }}
                        </span>
                    </div>

                    <!-- Barre de recherche et filtres -->
                    <div class="mb-4 flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                        <input type="text" id="searchProduct" placeholder="Rechercher un produit..." class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">

                        <div class="flex items-center">
                            <span class="mr-2 text-sm text-gray-600">{{ __('Filtrer par prix:') }}</span>
                            <select id="priceFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">{{ __('Tous les prix') }}</option>
                                @php
                                    $uniquePrices = $bill->items->pluck('unit_price')->unique()->sort();
                                    $priceGroups = $bill->items->groupBy('unit_price');
                                @endphp
                                @foreach($uniquePrices as $price)
                                    <option value="{{ $price }}">
                                        {{ number_format($price, 0, ',', ' ') }} FCFA
                                        ({{ $priceGroups[$price]->count() }} produit(s))
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button id="resetFilters" class="inline-flex items-center px-3 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700 font-medium text-sm">
                            <i class="bi bi-x-circle mr-1"></i>
                            {{ __('Réinitialiser') }}
                        </button>
                    </div>

                    <!-- Récapitulatif des prix utilisés dans cette facture -->
                    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Récapitulatif des prix utilisés dans cette facture') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($priceGroups as $price => $items)
                                <div class="price-card bg-white p-3 rounded-md shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                     onclick="document.getElementById('priceFilter').value='{{ $price }}'; filterProducts();">
                                    <div class="text-lg font-bold text-indigo-700">{{ number_format($price, 0, ',', ' ') }} FCFA</div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-sm text-gray-600">{{ $items->count() }} produit(s)</span>
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">
                                            {{ number_format(($items->count() / $bill->items->count()) * 100, 0) }}% du total
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Total: {{ number_format($items->sum('total'), 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Compteur de totaux -->
                    <div id="price-summary" class="hidden mb-4 bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-sm font-medium text-indigo-700">{{ __('Analyse des prix') }}</h4>
                                <p id="price-count" class="text-sm text-indigo-600 mt-1"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">{{ __('Total pour ce prix:') }}</p>
                                <p id="price-total" class="text-lg font-bold text-indigo-700"></p>
                            </div>
                        </div>
                    </div>

                    <div id="noProductResults" class="text-gray-500 py-4 hidden">
                        <p>{{ __('Aucun produit ne correspond à votre recherche.') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Produit') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Quantité') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Prix unitaire') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Total') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bill->items as $item)
                                <tr class="product-row hover:bg-gray-50 transition-colors duration-150"
                                    data-name="{{ $item->product ? strtolower($item->product->name) : strtolower($item->name ?? '') }}"
                                    data-price="{{ $item->unit_price }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($item->product)
                                                <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                                    {{ $item->product->name }}
                                                    <i class="bi bi-box-arrow-up-right text-xs ml-1"></i>
                                                </a>
                                            @else
                                                {{ $item->name ?? 'Paiement complémentaire' }}
                                            @endif
                                        </div>
                                        @if($item->product && $item->product->type != 'service')
                                            <div class="text-xs text-gray-500">
                                                SKU: {{ $item->product->sku ?: 'N/A' }}
                                            </div>
                                        @endif
                                        @if(!$item->product && $item->description)
                                            <div class="text-xs text-gray-500">
                                                {{ $item->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        <span class="bg-blue-50 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        <div class="price-display font-medium">
                                            {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            @if($item->product && $item->product->default_price != $item->unit_price)
                                                <span title="Prix par défaut du produit">
                                                    Prix catalogue: {{ number_format($item->product->default_price, 0, ',', ' ') }} FCFA
                                                </span>
                                                @if($item->product->default_price > $item->unit_price)
                                                    <span class="text-green-600 block">
                                                        <i class="bi bi-arrow-down"></i>
                                                        -{{ number_format(($item->product->default_price - $item->unit_price) / $item->product->default_price * 100, 0) }}%
                                                    </span>
                                                @elseif($item->product->default_price < $item->unit_price)
                                                    <span class="text-red-600 block">
                                                        <i class="bi bi-arrow-up"></i>
                                                        +{{ number_format(($item->unit_price - $item->product->default_price) / $item->product->default_price * 100, 0) }}%
                                                    </span>
                                                @endif
                                            @else
                                                @if(!$item->product && isset($item->is_barter_item) && $item->is_barter_item)
                                                    <span>Produit échangé</span>
                                                @elseif(!$item->product)
                                                    <span>Paiement complémentaire</span>
                                                @else
                                                    <span>Prix standard</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-medium">
                                        {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Résumé des totaux -->
                <div class="flex justify-end">
                    <div class="w-full md:w-1/3">
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($bill->is_barter_bill && $bill->barter)
                                <div class="py-2 text-lg font-bold flex justify-between">
                                    <div>{{ __('Total paiement complémentaire') }}</div>
                                    <div class="text-indigo-700">{{ number_format($bill->total, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div class="py-1 text-sm text-gray-600">
                                    <div class="italic">
                                        {{ $bill->barter->additional_payment > 0 ? 'Paiement du client vers la boutique' : 'Remboursement de la boutique vers le client' }}
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-between py-2 text-sm">
                                    <div class="text-gray-600">{{ __('Sous-total') }}</div>
                                    <div class="font-medium">{{ number_format($bill->total - $bill->tax_amount, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div class="flex justify-between py-2 text-sm border-b border-gray-200">
                                    <div class="text-gray-600">{{ __('TVA') }} ({{ $bill->tax_rate }}%)</div>
                                    <div class="font-medium">{{ number_format($bill->tax_amount, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div class="flex justify-between py-2 text-lg font-bold">
                                    <div>{{ __('Total') }}</div>
                                    <div class="text-indigo-700">{{ number_format($bill->total, 0, ',', ' ') }} FCFA</div>
                                </div>
                            @endif

                            @if($bill->status !== 'paid')
                                <div class="mt-4">
                                    <form action="{{ route('bills.update-status', $bill) }}" method="POST" class="inline status-update-form w-full">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                                            <i class="bi bi-check-circle mr-2"></i>
                                            {{ __('Marquer comme payée') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestionnaire pour les formulaires de mise à jour de statut
            const statusForms = document.querySelectorAll('.status-update-form');

            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    const newStatus = formData.get('status');

                    // Désactiver tous les boutons pendant la requête
                    document.querySelectorAll('.status-update-form button').forEach(btn => {
                        btn.disabled = true;
                    });

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
                            if (!response.ok) {
                                throw new Error('Erreur réseau');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Mise à jour du badge de statut dans le résumé
                            const statusBadge = document.querySelector('.flex.justify-between.items-center.mb-4 span');

                            if (statusBadge) {
                                // Mise à jour des classes du badge
                                statusBadge.className = 'px-4 py-2 inline-flex items-center text-sm font-semibold rounded-full ' +
                                    (newStatus === 'paid' ? 'bg-green-100 text-green-800' :
                                        (newStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-red-100 text-red-800'));

                                // Mise à jour de l'icône
                                let iconClass = newStatus === 'paid' ? 'bi-check-circle' :
                                    (newStatus === 'pending' ? 'bi-clock' : 'bi-exclamation-circle');

                                statusBadge.innerHTML = `<i class="bi ${iconClass} mr-2"></i>${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
                            }

                            // Mise à jour de la barre de progression
                            const progressBar = document.querySelector('.w-full.bg-gray-200.rounded-full.h-2\.5.mb-2 div');

                            if (progressBar) {
                                progressBar.className = 'h-2.5 rounded-full ' +
                                    (newStatus === 'paid' ? 'bg-green-600' :
                                        (newStatus === 'pending' ? 'bg-yellow-400' : 'bg-red-600'));

                                progressBar.style.width = newStatus === 'paid' ? '100%' :
                                    (newStatus === 'pending' ? '50%' : '25%');
                            }

                            // Réactiver et mettre à jour le style des boutons
                            document.querySelectorAll('.status-update-form').forEach(form => {
                                const formStatus = form.querySelector('input[name="status"]').value;
                                const button = form.querySelector('button');

                                button.disabled = formStatus === newStatus;

                                if (formStatus === 'paid') {
                                    button.className = `inline-flex items-center px-3 py-1.5 ${formStatus === newStatus ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-600 hover:text-white'} border border-green-600 rounded-md text-sm font-medium transition-colors duration-200`;
                                } else if (formStatus === 'pending') {
                                    button.className = `inline-flex items-center px-3 py-1.5 ${formStatus === newStatus ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-600 hover:text-white'} border border-yellow-600 rounded-md text-sm font-medium transition-colors duration-200`;
                                } else {
                                    button.className = `inline-flex items-center px-3 py-1.5 ${formStatus === newStatus ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800 hover:bg-red-600 hover:text-white'} border border-red-600 rounded-md text-sm font-medium transition-colors duration-200`;
                                }
                            });

                            // Afficher un message de succès
                            const messagesContainer = document.createElement('div');
                            messagesContainer.className = 'p-4 mb-4 text-green-700 bg-green-100 rounded-lg fixed top-4 right-4 shadow-md animate-fade';
                            messagesContainer.style.zIndex = '9999';
                            messagesContainer.style.maxWidth = '300px';
                            messagesContainer.innerHTML = `
                        <div class="flex items-center">
                            <i class="bi bi-check-circle-fill mr-2 text-green-500"></i>
                            <span>${data.message}</span>
                        </div>
                    `;

                            document.body.appendChild(messagesContainer);

                            // Supprimer le message après 3 secondes
                            setTimeout(() => {
                                messagesContainer.remove();
                            }, 3000);
                        })
                        .catch(error => {
                            console.error('Error updating bill status:', error);
                            // Réactiver tous les boutons en cas d'erreur
                            document.querySelectorAll('.status-update-form button').forEach(btn => {
                                btn.disabled = false;
                            });

                            // Afficher un message d'erreur
                            const errorContainer = document.createElement('div');
                            errorContainer.className = 'p-4 mb-4 text-red-700 bg-red-100 rounded-lg fixed top-4 right-4 shadow-md';
                            errorContainer.style.zIndex = '9999';
                            errorContainer.style.maxWidth = '300px';
                            errorContainer.innerHTML = `
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-2 text-red-500"></i>
                            <span>Une erreur est survenue lors de la mise à jour du statut.</span>
                        </div>
                    `;

                            document.body.appendChild(errorContainer);

                            // Supprimer le message après 3 secondes
                            setTimeout(() => {
                                errorContainer.remove();
                            }, 3000);
                        });
                });
            });

            // Fonctionnalité de recherche pour les produits
            const searchInput = document.getElementById('searchProduct');
            const priceFilter = document.getElementById('priceFilter');
            const productRows = document.querySelectorAll('.product-row');
            const noProductResults = document.getElementById('noProductResults');
            const resetButton = document.getElementById('resetFilters');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    filterProducts();
                });
            }

            if (priceFilter) {
                priceFilter.addEventListener('change', function() {
                    filterProducts();
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (priceFilter) priceFilter.value = '';
                    filterProducts();
                });
            }

            function filterProducts() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedPrice = priceFilter ? priceFilter.value : '';
                let visibleCount = 0;
                let totalForPrice = 0;

                productRows.forEach(row => {
                    const name = row.dataset.name;
                    const price = row.dataset.price;

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesPrice = !selectedPrice || price === selectedPrice;

                    // Pour le calcul du total pour un prix spécifique
                    if (price === selectedPrice) {
                        // Extraire le total du produit de la cellule correspondante
                        const totalText = row.cells[3].textContent.trim().replace(/[^\d]/g, '');
                        totalForPrice += parseInt(totalText) || 0;
                    }

                    if (matchesSearch && matchesPrice) {
                        row.style.display = '';
                        row.classList.add('bg-indigo-50');
                        row.classList.add('found-product');

                        // Mise en évidence du prix si filtré par prix
                        if (selectedPrice) {
                            const priceDisplay = row.querySelector('.price-display');
                            if (priceDisplay) {
                                priceDisplay.classList.add('text-indigo-700', 'font-bold');
                            }
                        }

                        visibleCount++;
                    } else {
                        if (!selectedPrice && !searchTerm) {
                            // Si aucun filtre n'est appliqué, tout afficher normalement
                            row.style.display = '';
                            row.classList.remove('bg-indigo-50');
                            row.classList.remove('found-product');

                            // Réinitialiser la mise en évidence du prix
                            const priceDisplay = row.querySelector('.price-display');
                            if (priceDisplay) {
                                priceDisplay.classList.remove('text-indigo-700', 'font-bold');
                            }
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });

                // Afficher le récapitulatif des prix si un prix est sélectionné
                const priceSummary = document.getElementById('price-summary');
                if (selectedPrice && !searchTerm) {
                    priceSummary.classList.remove('hidden');

                    const priceCount = document.getElementById('price-count');
                    const priceTotal = document.getElementById('price-total');

                    // Formater les nombres
                    const formatter = new Intl.NumberFormat('fr-FR');

                    if (priceCount) {
                        priceCount.textContent = `${visibleCount} produit(s) au prix de ${formatter.format(selectedPrice)} FCFA`;
                    }

                    if (priceTotal) {
                        priceTotal.textContent = `${formatter.format(totalForPrice)} FCFA`;
                    }
                } else {
                    priceSummary.classList.add('hidden');
                }

                // Afficher un message si aucun résultat
                if (visibleCount === 0 && productRows.length > 0 && (selectedPrice || searchTerm)) {
                    noProductResults.classList.remove('hidden');
                } else {
                    noProductResults.classList.add('hidden');
                }

                // Mettre à jour les cartes de prix
                document.querySelectorAll('.price-card').forEach(card => {
                    card.classList.remove('ring-2', 'ring-indigo-500');
                    if (card.textContent.includes(selectedPrice)) {
                        card.classList.add('ring-2', 'ring-indigo-500');
                    }
                });

                // Afficher un résumé des résultats de recherche textuelle
                if (searchTerm && !selectedPrice) {
                    const summary = document.createElement('div');
                    summary.id = 'filter-summary';
                    summary.className = 'text-sm text-indigo-700 mb-2';
                    summary.innerHTML = `<strong>${visibleCount}</strong> produit(s) trouvé(s) contenant "<strong>${searchTerm}</strong>"`;

                    // Supprimer le résumé existant s'il y en a un
                    const existingSummary = document.getElementById('filter-summary');
                    if (existingSummary) {
                        existingSummary.remove();
                    }

                    // Ajouter le nouveau résumé
                    if (visibleCount > 0) {
                        noProductResults.insertAdjacentElement('beforebegin', summary);
                    }
                } else if (!selectedPrice) {
                    // Supprimer le résumé s'il n'y a pas de filtre
                    const existingSummary = document.getElementById('filter-summary');
                    if (existingSummary) {
                        existingSummary.remove();
                    }
                }
            }
        });
    </script>
@endpush
