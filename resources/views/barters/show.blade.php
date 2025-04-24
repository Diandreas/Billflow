<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Détails du Troc #{{ $barter->id }}
                </h2>
                <div class="flex space-x-2">
                    <a href="{{ route('barters.edit', $barter) }}"
                        class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('barters.index') }}"
                        class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z"
                                clip-rule="evenodd" />
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    <!-- Informations de base -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Informations de base</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Client</p>
                                <p class="text-sm">{{ $barter->client->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Boutique</p>
                                <p class="text-sm">{{ $barter->shop->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Vendeur</p>
                                <p class="text-sm">{{ $barter->seller->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Type de troc</p>
                                <p class="text-sm">
                                    {{ $barter->type == 'same_type' ? 'Même type' : 'Types différents' }}
                                </p>
                            </div>
                        </div>
                        @if($barter->description)
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-700">Description</p>
                            <p class="text-sm">{{ $barter->description }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Articles donnés par le client -->
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <h3 class="text-md font-medium text-blue-700 mb-2">Articles donnés par le client</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-blue-200">
                                <thead class="bg-blue-100">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Valeur unitaire</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Valeur totale</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-blue-200">
                                    @foreach($barter->givenItems as $item)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value, 2) }} €</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value * $item->quantity, 2) }} €</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $item->description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-blue-50">
                                    <tr>
                                        <td colspan="3" class="px-3 py-2 text-sm font-medium text-blue-700 text-right">Total:</td>
                                        <td class="px-3 py-2 text-sm font-medium text-blue-700">
                                            {{ number_format($barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }), 2) }} €
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Articles reçus par le client -->
                    <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-100">
                        <h3 class="text-md font-medium text-green-700 mb-2">Articles reçus par le client</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-green-200">
                                <thead class="bg-green-100">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nom</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Valeur unitaire</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Valeur totale</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Produit</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-green-200">
                                    @foreach($barter->receivedItems as $item)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value, 2) }} €</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value * $item->quantity, 2) }} €</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->product ? $item->product->name : '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $item->description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-green-50">
                                    <tr>
                                        <td colspan="3" class="px-3 py-2 text-sm font-medium text-green-700 text-right">Total:</td>
                                        <td class="px-3 py-2 text-sm font-medium text-green-700">
                                            {{ number_format($barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }), 2) }} €
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Paiement complémentaire -->
                    @if($barter->payment_amount > 0)
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                        <h3 class="text-md font-medium text-yellow-700 mb-2">Paiement complémentaire</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm font-medium text-yellow-700">Montant</p>
                                <p class="text-sm">{{ number_format($barter->payment_amount, 2) }} €</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-700">Méthode de paiement</p>
                                <p class="text-sm">{{ $barter->payment_method }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-700">Direction</p>
                                <p class="text-sm">{{ $barter->payment_direction == 'client_to_shop' ? 'Client vers boutique' : 'Boutique vers client' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Images du troc -->
                    @if($barter->images->count() > 0)
                    <div class="mb-4 p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <h3 class="text-md font-medium text-purple-700 mb-2">Images du troc</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($barter->images as $image)
                            <div class="relative group">
                                <a href="{{ asset('storage/' . $image->path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Image du troc" class="w-full h-40 object-cover rounded-lg shadow-sm hover:shadow-md transition">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Résumé des valeurs -->
                    <div class="mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                        <h3 class="text-md font-medium text-indigo-700 mb-2">Résumé des valeurs</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Valeur totale donnée</p>
                                <p class="text-sm">{{ number_format($barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }), 2) }} €</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Valeur totale reçue</p>
                                <p class="text-sm">{{ number_format($barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }), 2) }} €</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Différence</p>
                                <p class="text-sm">
                                    {{ number_format(
                                        $barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }) - 
                                        $barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }), 
                                        2) 
                                    }} €
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Date et statut -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Créé le</p>
                                <p class="text-sm">{{ $barter->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Dernière mise à jour</p>
                                <p class="text-sm">{{ $barter->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Statut</p>
                                <p class="text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $barter->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($barter->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ $barter->status == 'completed' ? 'Complété' : 
                                           ($barter->status == 'pending' ? 'En attente' : 'Annulé') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
