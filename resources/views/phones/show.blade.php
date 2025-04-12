<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Numéro') }}: {{ $phone->number }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Détails du numéro de téléphone et clients associés') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('phones.edit', $phone) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">
                    <i class="bi bi-pencil mr-2"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('phones.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informations du numéro') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">{{ __('ID') }}</p>
                            <p class="font-medium">{{ $phone->id }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">{{ __('Numéro') }}</p>
                            <p class="font-medium text-xl">{{ $phone->number }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">{{ __('Créé le') }}</p>
                            <p class="font-medium">{{ $phone->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">{{ __('Dernière modification') }}</p>
                            <p class="font-medium">{{ $phone->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Clients associés') }}</h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $phone->clients->count() }} {{ __('clients') }}
                        </span>
                    </div>
                    
                    @if($phone->clients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nom') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($phone->clients as $client)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $client->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $client->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $client->email ?: '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    <i class="bi bi-eye mr-1"></i> {{ __('Voir') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="bi bi-exclamation-circle text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">{{ __('Aucun client associé à ce numéro') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush
</x-app-layout> 