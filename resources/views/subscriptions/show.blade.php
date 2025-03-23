<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'abonnement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $subscription->plan->name }}</h3>
                            <p class="text-gray-500 mt-1">{{ $subscription->plan->description }}</p>
                        </div>
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                            $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                            ($subscription->status === 'cancelled' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                        }}">
                            {{ 
                                $subscription->status === 'active' ? 'Actif' : 
                                ($subscription->status === 'cancelled' ? 'Annulé' : 'Expiré') 
                            }}
                        </span>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Informations de l'abonnement</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Type de cycle</p>
                                        <p class="font-medium">{{ $subscription->plan->cycleText }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Montant payé</p>
                                        <p class="font-medium">{{ $subscription->formattedPricePaid }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date de début</p>
                                        <p class="font-medium">{{ $subscription->starts_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date de fin</p>
                                        <p class="font-medium">{{ $subscription->ends_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Référence transaction</p>
                                        <p class="font-medium">{{ $subscription->transaction_reference ?: 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Méthode de paiement</p>
                                        <p class="font-medium">{{ isset($subscription->payment_data['method']) ? ucfirst($subscription->payment_data['method']) : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Quotas et utilisation</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">SMS Marketing</span>
                                        <span class="text-gray-500">{{ $subscription->sms_remaining }} / {{ $subscription->plan->sms_quota }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $subscription->smsUsagePercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">SMS Personnels</span>
                                        <span class="text-gray-500">{{ $subscription->sms_personal_remaining }} / {{ $subscription->plan->sms_personal_quota }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-600 rounded-full" style="width: {{ $subscription->personalSmsUsagePercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">Campagnes</span>
                                        <span class="text-gray-500">{{ $subscription->campaigns_used }} / {{ $subscription->plan->campaigns_per_cycle }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-purple-600 rounded-full" style="width: {{ $subscription->campaignsUsagePercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold text-gray-700 mb-3">Autres caractéristiques</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Nombre max. de clients</p>
                                    <p class="font-medium">{{ $subscription->plan->max_clients }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Campagnes par cycle</p>
                                    <p class="font-medium">{{ $subscription->plan->campaigns_per_cycle }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Report SMS (%)</p>
                                    <p class="font-medium">{{ $subscription->plan->sms_rollover_percent }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                            Retour aux abonnements
                        </a>
                        
                        @if ($subscription->status === 'active')
                            <a href="{{ route('subscriptions.recharge.form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition">
                                Recharger des SMS
                            </a>
                            
                            <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition">
                                    Annuler cet abonnement
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 