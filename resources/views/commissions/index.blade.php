<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Commissions des vendeurs') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Gérez les commissions des vendeurs') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="bi bi-cash-stack text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Total des commissions') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-4">
                            <i class="bi bi-clock-history text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('En attente') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_commissions'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                            <i class="bi bi-check2-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Payées') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['paid_commissions'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="mb-4">
                <form action="{{ route('commissions.index') }}" method="GET" class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[200px]">
                        <select name="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Tous les vendeurs') }}</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('user_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="shop_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Toutes les boutiques') }}</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approuvée') }}</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <input type="date" name="period_start" value="{{ request('period_start') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        <input type="date" name="period_end" value="{{ request('period_end') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        {{ __('Filtrer') }}
                    </button>
                    
                    @if(request()->anyFilled(['user_id', 'shop_id', 'status', 'period_start', 'period_end']))
                        <a href="{{ route('commissions.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Réinitialiser') }}
                        </a>
                    @endif
                </form>
            </div>

            <!-- Liste des commissions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-sm">
                                <th class="py-3 px-6 text-left">{{ __('Vendeur') }}</th>
                                <th class="py-3 px-6 text-left">{{ __('Facture') }}</th>
                                <th class="py-3 px-6 text-left">{{ __('Boutique') }}</th>
                                <th class="py-3 px-6 text-right">{{ __('Montant') }}</th>
                                <th class="py-3 px-6 text-center">{{ __('Type') }}</th>
                                <th class="py-3 px-6 text-center">{{ __('Statut') }}</th>
                                <th class="py-3 px-6 text-center">{{ __('Date') }}</th>
                                <th class="py-3 px-6 text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 divide-y divide-gray-200">
                            @forelse($commissions as $commission)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <a href="{{ route('commissions.vendor-report', $commission->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $commission->user->name }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($commission->bill)
                                            <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $commission->bill->reference }}
                                            </a>
                                        @else
                                            <span class="text-gray-500 italic">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        {{ $commission->shop->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-medium">
                                        {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($commission->type === 'vente') bg-green-100 text-green-800
                                            @elseif($commission->type === 'troc') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $commission->type }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($commission->status === 'pending') bg-amber-100 text-amber-800
                                            @elseif($commission->status === 'approved') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            @if($commission->status === 'pending') {{ __('En attente') }}
                                            @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                            @else {{ __('Payée') }} @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        {{ $commission->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 hover:text-indigo-900 mx-1">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($commission->status === 'pending' && Gate::allows('pay-commission', $commission))
                                            <form action="{{ route('commissions.pay', $commission) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir marquer cette commission comme payée?')" class="text-green-600 hover:text-green-900 mx-1">
                                                    <i class="bi bi-cash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 px-6 text-center text-gray-500">
                                        {{ __('Aucune commission trouvée') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $commissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 