<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                    <span class="mr-2">{{ $user->name }}</span>
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ __('- Rapport de commissions') }}</span>
                </h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('commissions.export.user', ['user_id' => $user->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-download mr-1"></i>
                    {{ __('CSV') }}
                </a>
                <a href="{{ route('commissions.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left mr-1"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Carte d'information et statistiques -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-4">
                <!-- Profil du vendeur -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-indigo-500 dark:bg-indigo-600 flex items-center justify-center text-white text-lg font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Taux: {{ $user->commission_rate ?? '0' }}%
                                    </span>
                                </p>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Boutiques') }}:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->shops as $shop)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                                {{ $shop->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-500 dark:text-gray-400 italic">{{ __('Aucune') }}</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cartes de statistiques -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-3">
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="border-l-2 border-green-500 pl-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</p>
                                <p class="font-bold text-xl text-gray-800 dark:text-white">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} FCFA</p>
                            </div>

                            <div class="border-l-2 border-amber-500 pl-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('En attente') }}</p>
                                    @if($stats['pending_commissions'] > 0 && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                                        <button
                                            type="button"
                                            onclick="document.getElementById('pay-all-form').submit();"
                                            class="text-xs text-green-600 dark:text-green-500 hover:text-green-800 dark:hover:text-green-400 font-medium flex items-center"
                                        >
                                            <i class="bi bi-cash-coin"></i>
                                        </button>
                                        <form id="pay-all-form" action="{{ route('commissions.pay-batch') }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        </form>
                                    @endif
                                </div>
                                <p class="font-bold text-xl text-amber-600 dark:text-amber-500">{{ number_format($stats['pending_commissions'], 0, ',', ' ') }} FCFA</p>
                            </div>

                            <div class="border-l-2 border-blue-500 pl-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Payé') }}</p>
                                <p class="font-bold text-xl text-blue-600 dark:text-blue-500">{{ number_format($stats['paid_commissions'], 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if(isset($stats['last_payment']))
                                        {{ __('Dernier:') }} {{ $stats['last_payment']->paid_at->format('d/m/Y') }}
                                    @else
                                        {{ __('Aucun paiement') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres en accordéon -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div x-data="{ open: false }">
                    <div class="flex justify-between items-center p-4 cursor-pointer" @click="open = !open">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white flex items-center">
                            <i class="bi bi-funnel mr-2"></i>
                            {{ __('Filtres') }}
                            @if(request()->anyFilled(['status', 'type', 'shop_id', 'from_date', 'to_date']))
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ __('Actifs') }}
                                </span>
                            @endif
                        </h3>
                        <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                    </div>

                    <div x-show="open" class="p-4 pt-0 border-t border-gray-200 dark:border-gray-700">
                        <form action="{{ route('commissions.vendor-report', $user) }}" method="GET" class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                <div>
                                    <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Statut') }}</label>
                                    <select id="status" name="status" class="mt-1 block w-full rounded-md text-sm border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                        <option value="">{{ __('Tous') }}</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approuvée') }}</option>
                                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Type') }}</label>
                                    <select id="type" name="type" class="mt-1 block w-full rounded-md text-sm border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                        <option value="">{{ __('Tous') }}</option>
                                        <option value="vente" {{ request('type') === 'vente' ? 'selected' : '' }}>{{ __('Vente') }}</option>
                                        <option value="troc" {{ request('type') === 'troc' ? 'selected' : '' }}>{{ __('Troc') }}</option>
                                        <option value="surplus" {{ request('type') === 'surplus' ? 'selected' : '' }}>{{ __('Surplus') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="shop_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Boutique') }}</label>
                                    <select id="shop_id" name="shop_id" onchange="updateVendorsList(this.value)" class="mt-1 block w-full rounded-md text-sm border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                        <option value="">{{ __('Toutes') }}</option>
                                        @foreach($user->shops as $shop)
                                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="from_date" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Début') }}</label>
                                    <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}" class="mt-1 block w-full rounded-md text-sm border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label for="to_date" class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Fin') }}</label>
                                    <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}" class="mt-1 block w-full rounded-md text-sm border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <div>
                                    @if(request()->anyFilled(['status', 'type', 'shop_id', 'from_date', 'to_date']))
                                        <a href="{{ route('commissions.vendor-report', $user) }}" class="text-xs text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 flex items-center">
                                            <i class="bi bi-x-circle mr-1"></i>
                                            {{ __('Réinitialiser') }}
                                        </a>
                                    @endif
                                </div>
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <i class="bi bi-search mr-1"></i>
                                    {{ __('Filtrer') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Liste des commissions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Référence') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Boutique') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Type') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Montant') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Statut') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($commissions as $commission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        @if($commission->bill)
                                            <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                {{ $commission->bill->reference }}
                                            </a>
                                        @else
                                            {{ $commission->reference ?? 'N/A' }}
                                        @endif
                                        @if($commission->period_month && $commission->period_year)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Période: {{ $commission->period_month }}/{{ $commission->period_year }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $commission->shop->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($commission->type === 'vente') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300
                                            @elseif($commission->type === 'troc') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300
                                            @else bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 @endif">
                                            {{ $commission->type }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($commission->status === 'pending') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-300
                                            @elseif($commission->status === 'approved') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300
                                            @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                            @if($commission->status === 'pending') {{ __('En attente') }}
                                            @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                            @else {{ __('Payée') }} @endif
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $commission->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                {{ __('Détails') }}
                                            </a>

{{--                                            @if($commission->status !== 'paid' && !$commission->is_paid)--}}
{{--                                                <a href="{{ route('commissions.edit', $commission) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">--}}
{{--                                                    {{ __('Modifier') }}--}}
{{--                                                </a>--}}
{{--                                            @endif--}}

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
                                    <td colspan="7" class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
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
