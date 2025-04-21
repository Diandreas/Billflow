<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Détail de la commission') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Informations détaillées sur la commission') }}
                </p>
            </div>
            <a href="{{ route('commissions.index') }}" class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Informations de base -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">{{ __('Informations générales') }}</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Vendeur') }}</p>
                                    <p class="font-medium">
                                        <a href="{{ route('commissions.vendor-report', $commission->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $commission->user->name }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Montant') }}</p>
                                    <p class="font-bold text-xl">{{ number_format($commission->amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Type') }}</p>
                                    <p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($commission->type === 'vente') bg-green-100 text-green-800
                                            @elseif($commission->type === 'troc') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $commission->type }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Statut') }}</p>
                                    <p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($commission->status === 'pending') bg-amber-100 text-amber-800
                                            @elseif($commission->status === 'approved') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            @if($commission->status === 'pending') {{ __('En attente') }}
                                            @elseif($commission->status === 'approved') {{ __('Approuvée') }}
                                            @else {{ __('Payée') }} @endif
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Date de création') }}</p>
                                    <p>{{ $commission->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Boutique') }}</p>
                                    <p>{{ $commission->shop->name ?? 'N/A' }}</p>
                                </div>
                                
                                @if($commission->bill)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Facture liée') }}</p>
                                    <p>
                                        <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $commission->bill->reference }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ __('Montant facture') }}</p>
                                    <p>{{ number_format($commission->bill->total_amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                                @endif
                                
                                @if($commission->notes)
                                <div class="col-span-2">
                                    <p class="text-sm font-medium text-gray-500">{{ __('Notes') }}</p>
                                    <p class="whitespace-pre-line">{{ $commission->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Informations de paiement -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">{{ __('Informations de paiement') }}</h3>
                            
                            @if($commission->status === 'paid')
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Date de paiement') }}</p>
                                        <p>{{ $commission->paid_at ? $commission->paid_at->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Méthode de paiement') }}</p>
                                        <p>{{ $commission->payment_method ?? 'N/A' }}</p>
                                    </div>
                                    
                                    @if($commission->payment_reference)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Référence de paiement') }}</p>
                                        <p>{{ $commission->payment_reference }}</p>
                                    </div>
                                    @endif
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ __('Payé par') }}</p>
                                        <p>{{ $commission->paid_by ? $commission->paid_by->name : 'N/A' }}</p>
                                    </div>
                                    
                                    @if($commission->payment_notes)
                                    <div class="col-span-2">
                                        <p class="text-sm font-medium text-gray-500">{{ __('Notes de paiement') }}</p>
                                        <p class="whitespace-pre-line">{{ $commission->payment_notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-amber-50 border border-amber-200 p-4 rounded-md">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-exclamation-triangle text-amber-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-amber-700 font-medium">{{ __('Commission non payée') }}</p>
                                            <p class="text-amber-600 text-sm">{{ __('Cette commission est en attente de paiement.') }}</p>
                                        </div>
                                    </div>
                                    
                                    @can('pay-commission', $commission)
                                        <div class="mt-4">
                                            <form action="{{ route('commissions.pay', $commission) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir marquer cette commission comme payée?') }}')" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                                    {{ __('Marquer comme payée') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('commissions.vendor-report', $commission->user) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Voir toutes les commissions du vendeur') }}
                </a>
                
                @if($commission->bill)
                <a href="{{ route('bills.show', $commission->bill) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Voir la facture associée') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 