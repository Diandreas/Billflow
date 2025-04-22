<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Commission n°') }} {{ $commission->id }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Informations détaillées sur la commission') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if($commission->bill)
                <a href="{{ route('bills.show', $commission->bill) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-receipt mr-2"></i>
                    {{ __('Voir la facture') }}
                </a>
                @endif
                <a href="{{ route('commissions.vendor-report', $commission->user) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="bi bi-person mr-2"></i>
                    {{ __('Commissions du vendeur') }}
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Carte principale avec les informations -->
                        <div class="lg:col-span-2">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                                    {{ __('Informations générales') }}
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                        @if($commission->status === 'pending') bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-300
                                        @elseif($commission->status === 'approved') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300
                                        @else bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 @endif">
                                        @if($commission->status === 'pending') {{ __('En attente') }}
                                        @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                        @else {{ __('Payée') }} @endif
                                    </span>
                                </h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Vendeur') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        <a href="{{ route('commissions.vendor-report', $commission->user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            {{ $commission->user->name }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Boutique') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $commission->shop->name ?? 'N/A' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Type') }}</p>
                                    <p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($commission->type === 'vente') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300
                                            @elseif($commission->type === 'troc') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300
                                            @else bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 @endif">
                                            {{ $commission->type }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date de création') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $commission->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                
                                @if($commission->bill)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Facture liée') }}</p>
                                    <p>
                                        <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            {{ $commission->bill->reference }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Montant facture') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ number_format($commission->bill->total, 0, ',', ' ') }} FCFA</p>
                                </div>
                                @endif
                                
                                @if($commission->rate)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Taux') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $commission->rate }}%</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Montant de base') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ number_format($commission->base_amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                                @endif
                            </div>
                            
                            @if($commission->description)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $commission->description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Carte de montant et paiement -->
                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="text-center mb-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Montant de la commission') }}</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                                
                                <div class="border-t border-gray-200 dark:border-gray-600 my-4 pt-4">
                                    @if($commission->status === 'paid')
                                        <div class="flex flex-col items-center text-center bg-green-50 dark:bg-green-900/20 p-4 rounded-md">
                                            <div class="h-12 w-12 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 rounded-full flex items-center justify-center mb-3">
                                                <i class="bi bi-check-lg text-2xl"></i>
                                            </div>
                                            <h4 class="font-medium text-green-700 dark:text-green-300">{{ __('Commission payée') }}</h4>
                                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                                {{ __('Payée le') }} {{ $commission->paid_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        
                                        <div class="mt-4 grid grid-cols-1 gap-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Payé par') }}</p>
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $commission->paid_by_user ? $commission->paid_by_user->name : 'N/A' }}</p>
                                            </div>
                                            
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Méthode de paiement') }}</p>
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $commission->payment_method ?? 'N/A' }}</p>
                                            </div>
                                            
                                            @if($commission->payment_reference)
                                            <div>
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Référence du paiement') }}</p>
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $commission->payment_reference }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        @can('pay-commission', $commission)
                                            <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-md mb-4">
                                                <div class="flex items-center mb-3">
                                                    <div class="h-8 w-8 bg-amber-100 dark:bg-amber-800 text-amber-600 dark:text-amber-300 rounded-full flex items-center justify-center mr-3">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                    </div>
                                                    <h4 class="font-medium text-amber-700 dark:text-amber-300">{{ __('Commission non payée') }}</h4>
                                                </div>
                                                <p class="text-sm text-amber-600 dark:text-amber-400">
                                                    {{ __('Cette commission est en attente de paiement.') }}
                                                </p>
                                            </div>
                                            
                                            <form action="{{ route('commissions.pay', $commission) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ __('Méthode de paiement') }}
                                                    </label>
                                                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white">
                                                        <option value="espèces">{{ __('Espèces') }}</option>
                                                        <option value="virement">{{ __('Virement bancaire') }}</option>
                                                        <option value="mobile_money">{{ __('Mobile Money') }}</option>
                                                        <option value="autre">{{ __('Autre') }}</option>
                                                    </select>
                                                </div>
                                                
                                                <div>
                                                    <label for="payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ __('Référence (facultatif)') }}
                                                    </label>
                                                    <input type="text" id="payment_reference" name="payment_reference" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white" placeholder="{{ __('Numéro de transaction, etc.') }}">
                                                </div>
                                                
                                                <div>
                                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ __('Notes de paiement (facultatif)') }}
                                                    </label>
                                                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-700 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white" placeholder="{{ __('Informations complémentaires sur le paiement') }}"></textarea>
                                                </div>
                                                
                                                <div class="pt-2">
                                                    <button type="submit" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir marquer cette commission comme payée?') }}')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                                        <i class="bi bi-cash-coin mr-2"></i>
                                                        {{ __('Marquer comme payée') }}
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md text-center">
                                                <p class="text-gray-500 dark:text-gray-400">
                                                    {{ __('Vous n\'avez pas les permissions nécessaires pour marquer cette commission comme payée.') }}
                                                </p>
                                            </div>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 