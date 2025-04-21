<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Rapport des commissions de :') }} {{ $vendor->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Bilan détaillé des commissions perçues par le vendeur') }}
                </p>
            </div>
            <a href="{{ route('commissions.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Carte de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm font-medium text-gray-500">{{ __('Commissions totales') }}</p>
                    <p class="mt-2 font-bold text-3xl text-gray-800">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Toutes périodes confondues') }}</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm font-medium text-gray-500">{{ __('Commissions en attente') }}</p>
                    <p class="mt-2 font-bold text-3xl text-amber-600">{{ number_format($stats['pending'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $stats['pending_count'] }} {{ __('commissions') }}</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm font-medium text-gray-500">{{ __('Commissions payées') }}</p>
                    <p class="mt-2 font-bold text-3xl text-green-600">{{ number_format($stats['paid'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $stats['paid_count'] }} {{ __('commissions') }}</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm font-medium text-gray-500">{{ __('Dernier paiement') }}</p>
                    <p class="mt-2 font-bold text-2xl text-gray-800">
                        @if($stats['last_paid'])
                            {{ number_format($stats['last_paid_amount'], 0, ',', ' ') }} FCFA
                        @else
                            -
                        @endif
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($stats['last_paid'])
                            {{ $stats['last_paid']->format('d/m/Y') }}
                        @else
                            {{ __('Aucun paiement') }}
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Filtrer les commissions') }}</h3>
                    
                    <form action="{{ route('commissions.vendor-report', $vendor) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Statut') }}</label>
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approuvée') }}</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">{{ __('Tous les types') }}</option>
                                    <option value="vente" {{ request('type') === 'vente' ? 'selected' : '' }}>{{ __('Vente') }}</option>
                                    <option value="troc" {{ request('type') === 'troc' ? 'selected' : '' }}>{{ __('Troc') }}</option>
                                    <option value="livraison" {{ request('type') === 'livraison' ? 'selected' : '' }}>{{ __('Livraison') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="shop_id" class="block text-sm font-medium text-gray-700">{{ __('Boutique') }}</label>
                                <select id="shop_id" name="shop_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">{{ __('Toutes les boutiques') }}</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="from_date" class="block text-sm font-medium text-gray-700">{{ __('Date de début') }}</label>
                                <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label for="to_date" class="block text-sm font-medium text-gray-700">{{ __('Date de fin') }}</label>
                                <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('commissions.vendor-report', $vendor) }}" class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 mr-2">
                                {{ __('Réinitialiser') }}
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ __('Filtrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Actions groupées -->
            @if($commissions->count() > 0 && $commissions->where('status', 'pending')->count() > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-exclamation-triangle text-amber-500"></i>
                            </div>
                            <div>
                                <p class="text-amber-700 font-medium">{{ __('Commissions en attente de paiement') }}</p>
                                <p class="text-amber-600 text-sm">{{ $commissions->where('status', 'pending')->count() }} {{ __('commissions en attente pour un total de') }} {{ number_format($commissions->where('status', 'pending')->sum('amount'), 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                        
                        @can('pay-commission')
                            <form action="{{ route('commissions.pay-selected') }}" method="POST">
                                @csrf
                                <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                <button type="submit" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir marquer toutes les commissions en attente comme payées?') }}')" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Payer toutes les commissions en attente') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endif
            
            <!-- Liste des commissions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Référence') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Boutique') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Montant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($commissions as $commission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if($commission->bill)
                                                <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $commission->bill->reference }}
                                                </a>
                                            @else
                                                {{ $commission->reference ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $commission->shop->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($commission->type === 'vente') bg-green-100 text-green-800
                                                @elseif($commission->type === 'troc') bg-purple-100 text-purple-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $commission->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($commission->status === 'pending') bg-amber-100 text-amber-800
                                                @elseif($commission->status === 'approved') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                @if($commission->status === 'pending') {{ __('En attente') }}
                                                @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                                @else {{ __('Payée') }} @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $commission->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                {{ __('Détails') }}
                                            </a>
                                            
                                            @if($commission->status !== 'paid' && auth()->user()->can('pay-commission', $commission))
                                                <form action="{{ route('commissions.pay', $commission) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir marquer cette commission comme payée?') }}')" class="text-green-600 hover:text-green-900">
                                                        {{ __('Payer') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('Aucune commission trouvée pour ce vendeur.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $commissions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 