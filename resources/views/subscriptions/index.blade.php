<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes abonnements') }}
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

            <!-- Abonnement actif -->
            @if ($activeSubscription)
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Abonnement actif</h3>
                        
                        <div class="flex flex-col md:flex-row items-start justify-between">
                            <div>
                                <h4 class="text-xl font-bold">{{ $activeSubscription->plan->name }}</h4>
                                <p class="text-gray-500">{{ $activeSubscription->plan->description }}</p>
                                <p class="mt-2">
                                    <span class="font-semibold">Valide jusqu'au:</span> 
                                    {{ $activeSubscription->ends_at->format('d/m/Y') }}
                                </p>
                                <p class="mt-1">
                                    <span class="font-semibold">Statut:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activeSubscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $activeSubscription->status === 'active' ? 'Actif' : 'Annulé' }}
                                    </span>
                                </p>
                            </div>
                            
                            <div class="mt-4 md:mt-0">
                                <a href="{{ route('subscriptions.show', $activeSubscription) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                    Voir les détails
                                </a>
                                
                                @if ($activeSubscription->status === 'active')
                                    <form action="{{ route('subscriptions.cancel', $activeSubscription) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition">
                                            Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Utilisation -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">SMS Marketing</h5>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $activeSubscription->smsUsagePercent }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Restants: {{ $activeSubscription->sms_remaining }}</span>
                                    <span>Total: {{ $activeSubscription->plan->sms_quota }}</span>
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">SMS Personnels</h5>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-600 rounded-full" style="width: {{ $activeSubscription->personalSmsUsagePercent }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Restants: {{ $activeSubscription->sms_personal_remaining }}</span>
                                    <span>Total: {{ $activeSubscription->plan->sms_personal_quota }}</span>
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Campagnes</h5>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-600 rounded-full" style="width: {{ $activeSubscription->campaignsUsagePercent }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Utilisées: {{ $activeSubscription->campaigns_used }}</span>
                                    <span>Total: {{ $activeSubscription->plan->campaigns_per_cycle }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('subscriptions.recharge.form') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition">
                                Recharger des SMS
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 bg-yellow-50 border border-yellow-200 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Aucun abonnement actif</h3>
                    <p class="text-yellow-600 mb-4">Vous n'avez pas d'abonnement actif. Souscrivez à un abonnement pour accéder à toutes les fonctionnalités.</p>
                    <a href="{{ route('subscriptions.plans') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition">
                        Voir les plans
                    </a>
                </div>
            @endif

            <!-- Historique des abonnements -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Historique des abonnements</h3>
                        <a href="{{ route('subscriptions.plans') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Voir les plans
                        </a>
                    </div>
                    
                    <!-- Barre de recherche et filtres -->
                    <div class="mb-4 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" id="searchSubscription" placeholder="Rechercher un abonnement..." class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 bg-gray-50 rounded-r-md border-l">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <select id="filterSubscriptionStatus" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="cancelled">Annulé</option>
                                <option value="expired">Expiré</option>
                            </select>
                        </div>
                    </div>
                    
                    @if ($subscriptions->count() > 0)
                        <div class="text-center py-4 text-gray-500 hidden" id="noSubscriptionsResults">
                            <p>Aucun abonnement ne correspond à votre recherche.</p>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($subscriptions as $subscription)
                                        <tr class="subscription-row" 
                                           data-plan="{{ strtolower($subscription->plan->name) }}" 
                                           data-status="{{ $subscription->status }}">
                                            <td class="py-4 px-6 text-sm">
                                                <div class="font-medium text-gray-900">{{ $subscription->plan->name }}</div>
                                                <div class="text-gray-500">{{ $subscription->plan->cycleText }}</div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500">
                                                {{ $subscription->starts_at->format('d/m/Y') }} - {{ $subscription->ends_at->format('d/m/Y') }}
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500">
                                                {{ $subscription->formattedPricePaid }}
                                            </td>
                                            <td class="py-4 px-6 text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                    $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                    ($subscription->status === 'cancelled' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                                }}">
                                                    {{ 
                                                        $subscription->status === 'active' ? 'Actif' : 
                                                        ($subscription->status === 'cancelled' ? 'Annulé' : 'Expiré') 
                                                    }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('subscriptions.show', $subscription) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                                    <a href="{{ route('clients.index', ['subscription_id' => $subscription->id]) }}" class="text-green-600 hover:text-green-900">
                                                        <span class="inline-flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                                            </svg>
                                                            Clients
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $subscriptions->links() }}
                        </div>
                    @else
                        <div class="py-4 text-center text-gray-500">
                            Aucun historique d'abonnement trouvé.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchSubscription');
            const statusFilter = document.getElementById('filterSubscriptionStatus');
            const subscriptionRows = document.querySelectorAll('.subscription-row');
            const noSubscriptionsResults = document.getElementById('noSubscriptionsResults');
            
            function filterSubscriptions() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value;
                
                let visibleCount = 0;
                
                subscriptionRows.forEach(row => {
                    const planName = row.dataset.plan;
                    const status = row.dataset.status;
                    
                    const matchesSearch = planName.includes(searchTerm);
                    const matchesStatus = selectedStatus === '' || status === selectedStatus;
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Afficher un message si aucun résultat
                if (visibleCount === 0 && subscriptionRows.length > 0) {
                    noSubscriptionsResults.classList.remove('hidden');
                } else {
                    noSubscriptionsResults.classList.add('hidden');
                }
            }
            
            // Événements de recherche et de filtrage
            searchInput.addEventListener('input', filterSubscriptions);
            statusFilter.addEventListener('change', filterSubscriptions);
        });
    </script>
</x-app-layout> 