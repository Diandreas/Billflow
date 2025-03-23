<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clients') }}
            </h2>
            <button onclick="toggleModal('newClientModal')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouveau Client
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Si on vient d'un abonnement spécifique, afficher un bandeau d'info -->
            @if (isset($subscription))
                <div class="mb-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-800">Clients liés à l'abonnement</h3>
                            <p class="text-indigo-600">{{ $subscription->plan->name }} - {{ $subscription->starts_at->format('d/m/Y') }} à {{ $subscription->ends_at->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('subscriptions.show', $subscription) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                            Voir l'abonnement
                        </a>
                    </div>
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('clients.index') }}" class="flex gap-4">
                            @if(request()->has('subscription_id'))
                                <input type="hidden" name="subscription_id" value="{{ request('subscription_id') }}">
                            @endif
                            <div class="flex-1 relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       placeholder="Rechercher un client par nom, email ou téléphone...">
                                <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 bg-gray-50 rounded-r-md border-l">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                Rechercher
                            </button>
                            
                            @if(request()->has('search'))
                                <a href="{{ url()->current() }}{{ request()->has('subscription_id') ? '?subscription_id=' . request('subscription_id') : '' }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition">
                                    Réinitialiser
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Clients Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Genre
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date de Naissance
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Téléphones
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Factures
                                </th>
                                <th class="px-6 py-3 bg-gray-50"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clients as $client)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $client->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $client->sex ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $client->birth ? $client->birth->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @foreach($client->phones as $phone)
                                                <div class="text-sm">{{ $phone->number }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $client->bills_count }} factures
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end space-x-2">
                                            <button onclick="editClient({{ $client->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Modifier
                                            </button>
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="newClientModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                // Suite de resources/views/bills/create.blade.php (Modal Client)
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Nouveau Client</h3>
                <form id="newClientForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Genre</label>
                        <select name="sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Non spécifié</option>
                            <option value="M">Homme</option>
                            <option value="F">Femme</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                        <input type="date" name="birth"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div id="phonesContainer">
                        <label class="block text-sm font-medium text-gray-700">Téléphones</label>
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="phones[]"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <button type="button" onclick="addPhoneField()"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleModal('newClientModal')"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
