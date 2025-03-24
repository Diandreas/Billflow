<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $campaign->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('campaigns.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                @if($campaign->status == 'draft')
                    <a href="{{ route('campaigns.edit', $campaign) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                @endif
            </div>
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

            <!-- Détails de la campagne -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Statistiques de la campagne -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques de la campagne</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-purple-100 p-3 mr-3">
                                        <i class="bi bi-people text-lg text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Destinataires</p>
                                        <p class="text-xl font-bold text-purple-700">{{ count($campaign->recipients ?? []) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-blue-100 p-3 mr-3">
                                        <i class="bi bi-chat-dots text-lg text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Messages</p>
                                        <p class="text-xl font-bold text-blue-700">{{ $campaign->messages_count ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-green-100 p-3 mr-3">
                                        <i class="bi bi-check-circle text-lg text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Taux de succès</p>
                                        <p class="text-xl font-bold text-green-700">
                                            @if(($campaign->messages_count ?? 0) > 0)
                                                {{ round(($campaign->delivered_count ?? 0) / ($campaign->messages_count ?? 1) * 100) }}%
                                            @else
                                                0%
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="rounded-full bg-red-100 p-3 mr-3">
                                        <i class="bi bi-x-circle text-lg text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Échecs</p>
                                        <p class="text-xl font-bold text-red-700">
                                            @if(($campaign->messages_count ?? 0) > 0)
                                                {{ ($campaign->messages_count ?? 0) - ($campaign->delivered_count ?? 0) }}
                                            @else
                                                0
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Détails de la campagne</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600 mb-1">Type:</p>
                                    <p class="font-semibold">
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
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 mb-1">Statut:</p>
                                    <p class="font-semibold">
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
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 mb-1">Date de création:</p>
                                    <p class="font-semibold">{{ $campaign->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 mb-1">
                                        @if($campaign->sent_at)
                                            Date d'envoi:
                                        @elseif($campaign->scheduled_at)
                                            Date planifiée:
                                        @else
                                            Date d'envoi:
                                        @endif
                                    </p>
                                    <p class="font-semibold">
                                        @if($campaign->sent_at)
                                            {{ $campaign->sent_at->format('d/m/Y H:i') }}
                                        @elseif($campaign->scheduled_at)
                                            {{ $campaign->scheduled_at->format('d/m/Y H:i') }}
                                            <span class="text-xs text-yellow-600">(planifié)</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            @if($campaign->status == 'draft')
                                <a href="{{ route('campaigns.prepare', $campaign) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="bi bi-send"></i> Envoyer
                                </a>
                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette campagne ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            @elseif($campaign->status == 'scheduled')
                                <form action="{{ route('campaigns.cancel', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette campagne planifiée ?');">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="bi bi-x-circle"></i> Annuler l'envoi
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Message</h4>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="whitespace-pre-wrap">{{ $campaign->message }}</p>
                            <p class="mt-2 text-xs text-gray-500">{{ strlen($campaign->message) }}/160 caractères</p>
                        </div>
                    </div>
                    
                    @if($campaign->target_segments)
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Ciblage</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($campaign->target_segments as $segment)
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                        @if($segment == 'all')
                                            Tous les clients
                                        @elseif($segment == 'recent')
                                            Clients récents
                                        @elseif($segment == 'birthday_this_month')
                                            Anniversaires du mois
                                        @else
                                            {{ $segment }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques d'envoi si la campagne a été envoyée -->
            @if($campaign->status == 'sent' || $campaign->status == 'sending')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques d'envoi</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-blue-600">Total envoyé</p>
                                <p class="text-2xl font-bold">{{ $campaign->messages->count() }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-green-600">Délivrés</p>
                                <p class="text-2xl font-bold">{{ $campaign->messages->where('status', 'delivered')->count() }}</p>
                                <p class="text-xs mt-1">
                                    @if($campaign->messages->count() > 0)
                                        {{ round(($campaign->messages->where('status', 'delivered')->count() / $campaign->messages->count()) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-yellow-600">En attente</p>
                                <p class="text-2xl font-bold">{{ $campaign->messages->whereIn('status', ['pending', 'sent'])->count() }}</p>
                                <p class="text-xs mt-1">
                                    @if($campaign->messages->count() > 0)
                                        {{ round(($campaign->messages->whereIn('status', ['pending', 'sent'])->count() / $campaign->messages->count()) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm text-red-600">Échecs</p>
                                <p class="text-2xl font-bold">{{ $campaign->messages->where('status', 'failed')->count() }}</p>
                                <p class="text-xs mt-1">
                                    @if($campaign->messages->count() > 0)
                                        {{ round(($campaign->messages->where('status', 'failed')->count() / $campaign->messages->count()) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($campaign->messages->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 mb-2">Détail des messages</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full leading-normal">
                                        <thead>
                                            <tr>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Client
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Numéro
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Statut
                                                </th>
                                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Date d'envoi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($campaign->messages->take(10) as $message)
                                                <tr>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        {{ $message->client->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        {{ $message->phone_number ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold 
                                                            @if($message->status == 'delivered') 
                                                                bg-green-100 text-green-800
                                                            @elseif($message->status == 'sent' || $message->status == 'pending')
                                                                bg-yellow-100 text-yellow-800
                                                            @elseif($message->status == 'failed')
                                                                bg-red-100 text-red-800
                                                            @else
                                                                bg-gray-100 text-gray-800
                                                            @endif
                                                        ">
                                                            {{ ucfirst($message->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                        {{ $message->sent_at ? $message->sent_at->format('d/m/Y H:i') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    
                                    @if($campaign->messages->count() > 10)
                                        <div class="mt-2 text-center">
                                            <span class="text-sm text-gray-500">Affichage des 10 premiers messages sur {{ $campaign->messages->count() }}.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 