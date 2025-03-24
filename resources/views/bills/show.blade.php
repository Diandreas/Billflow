<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Facture') }} : {{ $bill->reference }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails de la facture') }}
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Résumé') }}</h3>
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
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-purple-100 p-3 mr-4">
                                <i class="bi bi-box-seam text-xl text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Produits') }}</p>
                                <p class="text-xl font-bold text-purple-700">{{ $bill->products->count() }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 flex items-start">
                            <div class="rounded-full bg-blue-100 p-3 mr-4">
                                <i class="bi bi-cash-stack text-xl text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ __('Total') }}</p>
                                <p class="text-xl font-bold text-blue-700">{{ number_format($bill->total, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Status de la facture -->
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center">
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
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Date de la facture') }}</p>
                        <p class="text-lg font-semibold">{{ $bill->date->format('d/m/Y') }}</p>
                    </div>
                </div>

                <!-- Informations client et entreprise -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">{{ __('Facturé à') }}</div>
                        <div class="text-lg font-bold mb-2">{{ $bill->client->name }}</div>
                        @if(isset($bill->client->address))
                            <div class="text-gray-700 mb-1">{{ $bill->client->address }}</div>
                        @endif
                        @if($bill->client->phones->count() > 0)
                            <div class="text-gray-700 mb-1">
                                <i class="bi bi-telephone mr-1 text-gray-500"></i>
                                {{ $bill->client->phones->first()->number }}
                            </div>
                        @endif
                        @if(isset($bill->client->email))
                            <div class="text-gray-700">
                                <i class="bi bi-envelope mr-1 text-gray-500"></i>
                                {{ $bill->client->email }}
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        @php $settings = \App\Models\Setting::first(); @endphp
                        @if($settings)
                            <div class="text-lg font-bold mb-2">{{ $settings->company_name }}</div>
                            @if($settings->address)
                                <div class="text-gray-700 mb-1">{{ $settings->address }}</div>
                            @endif
                            @if($settings->phone)
                                <div class="text-gray-700 mb-1">
                                    <i class="bi bi-telephone mr-1 text-gray-500"></i>
                                    {{ $settings->phone }}
                                </div>
                            @endif
                            @if($settings->email)
                                <div class="text-gray-700 mb-1">
                                    <i class="bi bi-envelope mr-1 text-gray-500"></i>
                                    {{ $settings->email }}
                                </div>
                            @endif
                            @if($settings->siret)
                                <div class="text-gray-700">
                                    <i class="bi bi-building mr-1 text-gray-500"></i>
                                    SIRET: {{ $settings->siret }}
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
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Produits et services') }}</h3>
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
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $product->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $product->pivot->quantity }}
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
