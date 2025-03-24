<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du numéro de téléphone') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('phones.edit', $phone) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i> {{ __('Modifier') }}
                </a>
                <a href="{{ route('phones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">{{ __('Informations du numéro') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">ID</p>
                            <p class="font-medium">{{ $phone->id }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Numéro</p>
                            <p class="font-medium">{{ $phone->number }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Créé le</p>
                            <p class="font-medium">{{ $phone->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Dernière modification</p>
                            <p class="font-medium">{{ $phone->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mt-8 mb-4">{{ __('Clients associés') }}</h3>
                    
                    @if($phone->clients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">ID</th>
                                        <th class="py-2 px-4 border-b text-left">Nom</th>
                                        <th class="py-2 px-4 border-b text-left">Email</th>
                                        <th class="py-2 px-4 border-b text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($phone->clients as $client)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">{{ $client->id }}</td>
                                            <td class="py-2 px-4 border-b">{{ $client->name }}</td>
                                            <td class="py-2 px-4 border-b">{{ $client->email }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucun client associé à ce numéro</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 