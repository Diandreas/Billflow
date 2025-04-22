<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    <span class="mr-2">{{ $user->name }}</span>
                    <span class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('- Rapport de commissions') }}</span>
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Bilan détaillé des commissions perçues par le vendeur') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('commissions.export', ['user_id' => $user->id]) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-download mr-2"></i>
                    {{ __('Exporter CSV') }}
                </a>
                <a href="{{ route('commissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profil du vendeur -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center">
                        <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                            <div class="h-20 w-20 rounded-full bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">{{ $user->name }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Téléphone') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->phone ?? __('Non renseigné') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Taux de commission') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->commission_rate ?? '0' }}%</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Boutiques associées') }}</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @forelse($user->shops as $shop)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                            {{ $shop->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400 italic">{{ __('Aucune boutique associée') }}</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions totales') }}</p>
                    <p class="mt-2 font-bold text-3xl text-gray-800 dark:text-white">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} FCFA</p>
                    <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <i class="bi bi-calculator mr-1"></i>
                        <span>{{ __('Toutes périodes confondues') }}</span>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-amber-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions en attente') }}</p>
                    <p class="mt-2 font-bold text-3xl text-amber-600 dark:text-amber-500">{{ number_format($stats['pending_commissions'], 0, ',', ' ') }} FCFA</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="bi bi-hourglass-split mr-1"></i>
                            <span>{{ __('À payer') }}</span>
                        </span>
                        @if($stats['pending_commissions'] > 0 && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                            <button 
                                type="button" 
                                onclick="document.getElementById('pay-all-form').submit();"
                                class="text-xs text-green-600 dark:text-green-500 hover:text-green-800 dark:hover:text-green-400 font-medium flex items-center"
                            >
                                <i class="bi bi-cash mr-1"></i>
                                {{ __('Payer tout') }}
                            </button>
                            <form id="pay-all-form" action="{{ route('commissions.pay-batch') }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                            </form>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions payées') }}</p>
                    <p class="mt-2 font-bold text-3xl text-blue-600 dark:text-blue-500">{{ number_format($stats['paid_commissions'], 0, ',', ' ') }} FCFA</p>
                    <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <i class="bi bi-check-circle mr-1"></i>
                        <span>{{ __('Déjà réglées') }}</span>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dernier paiement') }}</p>
                    <p class="mt-2 font-bold text-2xl text-gray-800 dark:text-white">
                        @if(isset($stats['last_payment']))
                            {{ number_format($stats['last_payment']->amount, 0, ',', ' ') }} FCFA
                        @else
                            -
                        @endif
                    </p>
                    <div class="flex items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <i class="bi bi-calendar mr-1"></i>
                        <span>
                            @if(isset($stats['last_payment']))
                                {{ $stats['last_payment']->paid_at->format('d/m/Y') }}
                            @else
                                {{ __('Aucun paiement') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Filtres -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Filtrer les commissions') }}</h3>
                        @if(request()->anyFilled(['status', 'type', 'shop_id', 'from_date', 'to_date']))
                            <a href="{{ route('commissions.vendor-report', $user) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center">
                                <i class="bi bi-arrow-repeat mr-1"></i>
                                {{ __('Réinitialiser') }}
                            </a>
                        @endif
                    </div>
                    
                    <form action="{{ route('commissions.vendor-report', $user) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Statut') }}</label>
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approuvée') }}</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                                </select>
                            </div>
        
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Type') }}</label>
                                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                    <option value="">{{ __('Tous les types') }}</option>
                                    <option value="vente" {{ request('type') === 'vente' ? 'selected' : '' }}>{{ __('Vente') }}</option>
                                    <option value="troc" {{ request('type') === 'troc' ? 'selected' : '' }}>{{ __('Troc') }}</option>
                                    <option value="surplus" {{ request('type') === 'surplus' ? 'selected' : '' }}>{{ __('Surplus') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="shop_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Boutique') }}</label>
                                <select id="shop_id" name="shop_id" onchange="updateVendorsList(this.value)" class="form-control @error('shop_id') is-invalid @enderror">
                                    <option value="">{{ __('Toutes les boutiques') }}</option>
                                    @foreach($user->shops as $shop)
                                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date de début') }}</label>
                                <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                            </div>
                    
                            <div>
                                <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date de fin') }}</label>
                                <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <i class="bi bi-search mr-2"></i>
                                {{ __('Filtrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Liste des commissions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Référence') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Boutique') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Montant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($commissions as $commission)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @if($commission->bill)
                                                <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                    {{ $commission->bill->reference }}
                                                </a>
                                            @else
                                                {{ $commission->reference ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $commission->shop->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($commission->type === 'vente') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300
                                                @elseif($commission->type === 'troc') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300
                                                @else bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 @endif">
                                                {{ $commission->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($commission->status === 'pending') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-300
                                                @elseif($commission->status === 'approved') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300
                                                @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                                @if($commission->status === 'pending') {{ __('En attente') }}
                                                @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                                @else {{ __('Payée') }} @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $commission->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                    {{ __('Détails') }}
                                                </a>
                                                
                                                @if($commission->status !== 'paid' && auth()->user()->can('pay-commission', $commission))
                                                    <form action="{{ route('commissions.pay', $commission) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir marquer cette commission comme payée?') }}')" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                                                            {{ __('Payer') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            <div class="flex flex-col items-center py-6">
                                                <svg class="h-12 w-12 text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-lg font-medium">{{ __('Aucune commission trouvée pour ce vendeur') }}</p>
                                                <p class="text-sm mt-1">{{ __('Essayez de modifier vos critères de filtrage') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination améliorée -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Affichage de') }} {{ $commissions->firstItem() ?? 0 }} {{ __('à') }} {{ $commissions->lastItem() ?? 0 }} 
                                {{ __('sur') }} {{ $commissions->total() }} {{ __('commissions') }}
                            </p>
                            {{ $commissions->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    function updateVendorsList(shopId) {
        if (!shopId) return;
        
        // Faire une requête AJAX pour obtenir les vendeurs de cette boutique
        fetch(`/api/shops/${shopId}/vendors`)
            .then(response => response.json())
            .then(data => {
                // Récupérer le select des vendeurs
                const vendorSelect = document.getElementById('user_id');
                
                // Sauvegarder la valeur actuelle si elle existe
                const currentValue = vendorSelect.value;
                
                // Vider le select
                vendorSelect.innerHTML = '<option value="">Sélectionner un vendeur</option>';
                
                // Ajouter les vendeurs à la liste
                data.forEach(vendor => {
                    const option = document.createElement('option');
                    option.value = vendor.id;
                    option.textContent = vendor.name;
                    vendorSelect.appendChild(option);
                });
                
                // Restaurer la valeur précédente si possible
                if (currentValue && [...vendorSelect.options].find(opt => opt.value === currentValue)) {
                    vendorSelect.value = currentValue;
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des vendeurs:', error));
    }
    
    // Exécuter au chargement de la page si une boutique est déjà sélectionnée
    document.addEventListener('DOMContentLoaded', function() {
        const shopSelect = document.getElementById('shop_id');
        if (shopSelect && shopSelect.value) {
            updateVendorsList(shopSelect.value);
        }
    });
</script>
@endpush 