<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messages Promotionnels') }}
            </h2>
            <a href="{{ route('campaigns.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                <i class="bi bi-plus"></i> Nouvelle Campagne
            </a>
        </div>
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

            @if (!auth()->user()->hasActiveSubscription())
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <div class="font-bold">Aucun abonnement actif</div>
                    <p>Vous devez souscrire à un abonnement pour utiliser les fonctionnalités de messages promotionnels.</p>
                    <a href="{{ route('subscriptions.plans') }}" class="underline font-bold">Voir les plans disponibles</a>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Vos Campagnes SMS</h3>
                        
                        <div class="flex space-x-2">
                            <div>
                                <select id="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">Tous les statuts</option>
                                    <option value="draft">Brouillons</option>
                                    <option value="scheduled">Planifiés</option>
                                    <option value="sent">Envoyés</option>
                                </select>
                            </div>
                            
                            <div class="relative">
                                <input type="text" id="searchCampaign" placeholder="Rechercher..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-8 text-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($campaigns->isEmpty())
                        <div class="text-center py-8 text-gray-500" id="noCampaignsMessage">
                            <p class="mb-4">Vous n'avez pas encore créé de campagne SMS.</p>
                            <a href="{{ route('campaigns.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="bi bi-plus"></i> Créer votre première campagne
                            </a>
                        </div>
                        <div class="text-center py-8 text-gray-500 hidden" id="noSearchResultsMessage">
                            <p>Aucune campagne ne correspond à votre recherche.</p>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 hidden" id="noSearchResultsMessage">
                            <p>Aucune campagne ne correspond à votre recherche.</p>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Nom
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Date d'envoi
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            SMS
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($campaigns as $campaign)
                                        <tr class="campaign-row" 
                                            data-name="{{ strtolower($campaign->name) }}" 
                                            data-type="{{ $campaign->type }}" 
                                            data-status="{{ $campaign->status }}">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <a href="{{ route('campaigns.show', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $campaign->name }}
                                                </a>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold 
                                                    @if($campaign->type == 'birthday') 
                                                        bg-pink-100 text-pink-800
                                                    @elseif($campaign->type == 'holiday')
                                                        bg-green-100 text-green-800
                                                    @elseif($campaign->type == 'promotion')
                                                        bg-blue-100 text-blue-800
                                                    @else
                                                        bg-gray-100 text-gray-800
                                                    @endif
                                                ">
                                                    {{ ucfirst($campaign->type) }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold 
                                                    @if($campaign->status == 'sent') 
                                                        bg-green-100 text-green-800
                                                    @elseif($campaign->status == 'scheduled')
                                                        bg-blue-100 text-blue-800
                                                    @elseif($campaign->status == 'draft')
                                                        bg-gray-100 text-gray-800
                                                    @else
                                                        bg-red-100 text-red-800
                                                    @endif
                                                ">
                                                    {{ ucfirst($campaign->status) }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                @if($campaign->sent_at)
                                                    {{ $campaign->sent_at->format('d/m/Y H:i') }}
                                                @elseif($campaign->scheduled_at)
                                                    <span class="text-yellow-500">
                                                        {{ $campaign->scheduled_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                @if($campaign->sms_count > 0)
                                                    <span class="text-gray-900">{{ $campaign->sms_sent }}/{{ $campaign->sms_count }}</span>
                                                    @if($campaign->status == 'sent' && $campaign->sms_sent > 0)
                                                        <span class="ml-1 text-xs text-green-600">({{ $campaign->delivery_rate }}%)</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('campaigns.show', $campaign) }}" class="text-indigo-600 hover:text-indigo-900" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    @if($campaign->status == 'draft')
                                                        <a href="{{ route('campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-900" title="Modifier">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <a href="{{ route('campaigns.prepare', $campaign) }}" class="text-green-600 hover:text-green-900" title="Envoyer">
                                                            <i class="bi bi-send"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $campaigns->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchCampaign');
            const statusFilter = document.getElementById('filterStatus');
            const campaignRows = document.querySelectorAll('.campaign-row');
            const noSearchResultsMessage = document.getElementById('noSearchResultsMessage');
            const noCampaignsMessage = document.getElementById('noCampaignsMessage');
            
            function filterCampaigns() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value;
                
                let visibleCount = 0;
                
                campaignRows.forEach(row => {
                    const campaignName = row.dataset.name;
                    const campaignStatus = row.dataset.status;
                    
                    const matchesSearch = campaignName.includes(searchTerm);
                    const matchesStatus = selectedStatus === '' || campaignStatus === selectedStatus;
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Afficher un message si aucun résultat
                if (visibleCount === 0 && campaignRows.length > 0) {
                    noSearchResultsMessage.classList.remove('hidden');
                } else {
                    noSearchResultsMessage.classList.add('hidden');
                }
            }
            
            // Événements de recherche et de filtrage
            searchInput.addEventListener('input', filterCampaigns);
            statusFilter.addEventListener('change', filterCampaigns);
        });
    </script>
</x-app-layout> 