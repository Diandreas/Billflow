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
                    <!-- Facture associée au troc (si elle existe) -->
                    @if($barter->bill)
                        <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-100">
                            <h3 class="text-md font-medium text-green-700 mb-2">Facture générée</h3>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-green-800">
                                        Une facture a été générée automatiquement pour ce troc.
                                    </p>
                                    <p class="text-sm">
                                        <strong>Référence:</strong> {{ $barter->bill->reference }}
                                        <span class="mx-2">|</span>
                                        <strong>Montant:</strong> {{ number_format(abs($barter->additional_payment), 0, ',', ' ') }} FCFA
                                        <span class="mx-2">|</span>
                                        <strong>Statut:</strong>
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $barter->bill->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $barter->bill->status == 'paid' ? 'Payée' : 'En attente' }}
                                        </span>
                                    </p>
                                    @if($barter->additional_payment != 0)
                                        <p class="text-sm mt-1">
                                            <strong>Direction du paiement:</strong>
                                            <span class="{{ $barter->additional_payment > 0 ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('bills.show', $barter->bill) }}"
                                       class="inline-flex items-center px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd" />
                                        </svg>
                                        Voir la facture
                                    </a>
                                    <a href="{{ route('barters.print-bill', $barter) }}"
                                       class="inline-flex items-center px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                                        </svg>
                                        Imprimer
                                    </a>
                                    <a href="{{ route('barters.download-bill', $barter) }}"
                                       class="inline-flex items-center px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Télécharger PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                            <h3 class="text-md font-medium text-yellow-700 mb-2">Facture</h3>
                            <div class="flex justify-between items-center">
                                <p class="text-sm text-yellow-800">
                                    Aucune facture n'a encore été générée pour ce troc.
                                    @if($barter->additional_payment != 0)
                                        Une facture doit être générée pour le paiement complémentaire de {{ number_format(abs($barter->additional_payment), 0, ',', ' ') }} FCFA
                                        ({{ $barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }}).
                                    @endif
                                </p>
                                <a href="{{ route('barters.generate-bill', $barter) }}"
                                   class="inline-flex items-center px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                    </svg>
                                    Générer une facture
                                </a>
                            </div>
                        </div>
                    @endif

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
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $item->description }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-blue-50">
                                <tr>
                                    <td colspan="3" class="px-3 py-2 text-sm font-medium text-blue-700 text-right">Total:</td>
                                    <td class="px-3 py-2 text-sm font-medium text-blue-700">
                                        {{ number_format($barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }), 0, ',', ' ') }} FCFA
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
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ number_format($item->value * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->product ? $item->product->name : '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $item->description }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-green-50">
                                <tr>
                                    <td colspan="3" class="px-3 py-2 text-sm font-medium text-green-700 text-right">Total:</td>
                                    <td class="px-3 py-2 text-sm font-medium text-green-700">
                                        {{ number_format($barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }), 0, ',', ' ') }} FCFA
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Paiement complémentaire -->
                    @if($barter->additional_payment != 0)
                        <div class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                            <h3 class="text-md font-medium text-yellow-700 mb-2">Paiement complémentaire</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-yellow-700">Montant</p>
                                    <p class="text-sm">{{ number_format(abs($barter->additional_payment), 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-yellow-700">Méthode de paiement</p>
                                    <p class="text-sm">{{ $barter->payment_method ?? 'Non spécifiée' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-yellow-700">Direction</p>
                                    <p class="text-sm {{ $barter->additional_payment > 0 ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }}
                                    </p>
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
                                <p class="text-sm">{{ number_format($barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }), 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Valeur totale reçue</p>
                                <p class="text-sm">{{ number_format($barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }), 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Différence</p>
                                <p class="text-sm {{ $barter->additional_payment != 0 ? ($barter->additional_payment > 0 ? 'text-green-700' : 'text-red-700') : '' }}">
                                    {{ number_format(
                                        $barter->receivedItems->sum(function($item) { return $item->value * $item->quantity; }) -
                                        $barter->givenItems->sum(function($item) { return $item->value * $item->quantity; }),
                                        0, ',', ' ')
                                    }} FCFA
                                    @if($barter->additional_payment != 0)
                                        ({{ $barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }})
                                    @endif
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
                                    <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full
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

                    <!-- Actions -->
                    <div class="flex justify-end space-x-2">
                        @if($barter->status === 'pending')
                            <form action="{{ route('barters.complete', $barter) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Marquer comme complété') }}
                                </button>
                            </form>
                            <form action="{{ route('barters.cancel', $barter) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce troc ? Cette action restaurera le stock des produits.')"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Annuler') }}
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('barters.destroy', $barter) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce troc ? Cette action est irréversible.')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
