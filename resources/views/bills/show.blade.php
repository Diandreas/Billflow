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
                                <p class="text-xl font-bold text-purple-700">{{ $bill->products->count() }}</p>
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
                        @if($bill->client->phones->count() > 0)
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
                            {{ $bill->products->count() }} {{ __('articles') }}
                        </span>
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
                                @foreach($bill->products as $product)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                                {{ $product->name }}
                                                <i class="bi bi-box-arrow-up-right text-xs ml-1"></i>
                                            </a>
                                        </div>
                                        @if($product->type != 'service')
                                            <div class="text-xs text-gray-500">
                                                SKU: {{ $product->sku ?: 'N/A' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        <span class="bg-blue-50 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            {{ $product->pivot->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        {{ number_format($product->pivot->unit_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 font-medium">
                                        {{ number_format($product->pivot->total, 0, ',', ' ') }} FCFA
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
                            @if($bill->status !== 'paid')
                                <div class="mt-4">
                                    <a href="#" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                                        <i class="bi bi-check-circle mr-2"></i>
                                        {{ __('Marquer comme payée') }}
                                    </a>
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
