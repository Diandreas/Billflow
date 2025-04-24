<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Gestion des Trocs
                </h2>
                <a href="{{ route('barters.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nouveau
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('success'))
                        <div class="mb-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-2 bg-red-100 border-l-4 border-red-500 text-red-700 p-2 text-sm rounded" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="mb-3 bg-gray-50 p-2 rounded-lg text-sm">
                        <form action="{{ route('barters.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div>
                                <label for="client_id" class="block text-xs font-medium text-gray-700 mb-1">Client</label>
                                <select name="client_id" id="client_id" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Tous les clients</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="shop_id" class="block text-xs font-medium text-gray-700 mb-1">Boutique</label>
                                <select name="shop_id" id="shop_id" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Toutes</option>
                                    @foreach ($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                            {{ $shop->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="seller_id" class="block text-xs font-medium text-gray-700 mb-1">Vendeur</label>
                                <select name="seller_id" id="seller_id" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Tous</option>
                                    @foreach ($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                            {{ $seller->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:flex md:flex-col md:justify-end">
                                <button type="submit" class="mt-4 inline-flex justify-center items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded text-xs font-medium text-white hover:bg-indigo-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    Filtrer
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Boutique</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendeur</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-3 py-1.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($barters as $barter)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">{{ $barter->reference }}</td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">{{ $barter->client->name }}</td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">{{ $barter->shop->name }}</td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">{{ $barter->seller->name }}</td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ $barter->type == 'same_type' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $barter->type == 'same_type' ? 'Même type' : 'Différent' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            @if ($barter->status === 'pending')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                            @elseif ($barter->status === 'completed')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">Complété</span>
                                            @else
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">Annulé</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">{{ $barter->created_at->format('d/m/Y') }}</td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-right">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('barters.show', $barter) }}" class="text-blue-600 hover:text-blue-900" title="Détails">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                @if ($barter->status === 'pending')
                                                    <a href="{{ route('barters.edit', $barter) }}" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-2 text-center text-gray-500 text-xs">Aucun troc enregistré</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $barters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
