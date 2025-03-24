<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Préparation de l\'envoi') }}
            </h2>
            <a href="{{ route('campaigns.show', $campaign) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif de la campagne</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <p class="text-gray-600 font-medium mb-1">Nom:</p>
                                <p>{{ $campaign->name }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-gray-600 font-medium mb-1">Type:</p>
                                <p>
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
                            
                            <div class="mb-4">
                                <p class="text-gray-600 font-medium mb-1">Message:</p>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="whitespace-pre-wrap">{{ $campaign->message }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ strlen($campaign->message) }}/160 caractères</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100 mb-4">
                                <h4 class="font-medium text-indigo-800 mb-2">Informations d'envoi</h4>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-600">Nombre de destinataires:</span>
                                    <span class="font-semibold">{{ $smsCount }}</span>
                                </div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-600">SMS disponibles:</span>
                                    <span class="font-semibold {{ $subscription->sms_remaining < $smsCount ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $subscription->sms_remaining }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">SMS après envoi:</span>
                                    <span class="font-semibold">
                                        {{ max(0, $subscription->sms_remaining - $smsCount) }}
                                    </span>
                                </div>
                                
                                @if($subscription->sms_remaining < $smsCount)
                                    <div class="mt-3 bg-red-100 text-red-700 p-2 rounded text-sm">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Vous n'avez pas assez de SMS disponibles. 
                                        <a href="{{ route('subscriptions.recharge.form') }}" class="font-medium underline">Recharger des SMS</a>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-gray-600 font-medium mb-1">Clients ciblés:</p>
                                <div class="text-sm">
                                    @if($campaign->target_segments)
                                        <div class="mb-2">
                                            <span class="text-gray-500">Segments:</span> 
                                            <div class="flex flex-wrap gap-1 mt-1">
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
                                    
                                    <div class="mt-2">
                                        <p class="text-gray-600">Nombre total de clients ciblés: <span class="font-semibold">{{ $clients->count() }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <h4 class="font-medium text-gray-900 mb-4">Aperçu des destinataires ({{ min(5, $clients->count()) }} sur {{ $clients->count() }})</h4>
                        
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
                                            Message personnalisé
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients->take(5) as $client)
                                        <tr>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $client->name }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                @if($client->phones->count() > 0)
                                                    {{ $client->phones->first()->number }}
                                                @else
                                                    <span class="text-red-500">Aucun numéro</span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <div class="max-w-xs truncate">
                                                    {{ preg_replace(['/#nom#/', '/#date#/'], [$client->name, now()->format('d/m/Y')], $campaign->message) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('campaigns.show', $campaign) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Annuler
                        </a>
                        
                        <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                            @csrf
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="schedule_send" id="schedule_send" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Planifier l'envoi</span>
                                </label>
                                <div id="schedule_fields" class="hidden">
                                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" {{ $subscription->sms_remaining < $smsCount ? 'disabled' : '' }}
                                  @if($subscription->sms_remaining < $smsCount) disabled @endif
                                >
                                    {{ $campaign->scheduled_at ? 'Confirmer l\'envoi planifié' : 'Envoyer maintenant' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleToggle = document.getElementById('schedule_send');
            const scheduleFields = document.getElementById('schedule_fields');

            scheduleToggle.addEventListener('change', function() {
                if (this.checked) {
                    scheduleFields.classList.remove('hidden');
                } else {
                    scheduleFields.classList.add('hidden');
                    document.getElementById('scheduled_at').value = '';
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 