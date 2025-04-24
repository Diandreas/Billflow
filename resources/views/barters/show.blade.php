<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Troc: {{ $barter->reference }}
                </h2>
                <div class="flex space-x-2">
                    @if($barter->status === 'pending')
                        <a href="{{ route('barters.edit', $barter) }}"
                           class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Modifier
                        </a>
                    @endif
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
            <!-- Statut et actions -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                <div class="p-3 flex justify-between items-center border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $barter->status == 'completed' ? 'bg-green-100 text-green-800' :
                               ($barter->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                               'bg-red-100 text-red-800') }}">
                            {{ $barter->status == 'completed' ? 'Complété' :
                               ($barter->status == 'pending' ? 'En attente' : 'Annulé') }}
                        </span>
                        <span class="text-xs text-gray-500">Créé le {{ $barter->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    @if($barter->status === 'pending')
                        <div class="flex space-x-2">
                            <form action="{{ route('barters.complete', $barter) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Compléter
                                </button>
                            </form>

                            <form action="{{ route('barters.cancel', $barter) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce troc?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Annuler
                                </button>
                            </form>

                            <form action="{{ route('barters.destroy', $barter) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce troc?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Informations générales -->
                <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div>
                        <p class="font-medium text-gray-700">Client</p>
                        <p class="text-gray-800">
                            <a href="{{ route('clients.show', $barter->client) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $barter->client->name }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Boutique</p>
                        <p class="text-gray-800">{{ $barter->shop->name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Vendeur</p>
                        <p class="text-gray-800">{{ $barter->seller->name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Type de troc</p>
                        <p class="text-gray-800">
                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ $barter->type == 'same_type' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $barter->type == 'same_type' ? 'Même type' : 'Types différents' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Référence</p>
                        <p class="text-gray-800">{{ $barter->reference }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Date du troc</p>
                        <p class="text-gray-800">{{ $barter->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($barter->description)
                    <div class="px-3 pb-3 text-xs">
                        <p class="font-medium text-gray-700">Description</p>
                        <p class="text-gray-800">{{ $barter->description }}</p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Articles donnés par le client -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                    <div class="bg-blue-50 px-3 py-2 border-b border-blue-100">
                        <h3 class="text-sm font-medium text-blue-700">Articles donnés par le client</h3>
                    </div>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-blue-200 text-xs">
                                <thead class="bg-blue-50">
                                <tr>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Prix</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Qté</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">Total</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-blue-100">
                                @foreach($barter->givenItems as $item)
                                    <tr class="hover:bg-blue-50">
                                        <td class="px-2 py-1.5 whitespace-nowrap text-gray-800">
                                            {{ $item->name }}
                                            @if($item->description)
                                                <p class="text-gray-500 text-xs italic">{{ Str::limit($item->description, 30) }}</p>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap text-gray-800">{{ number_format($item->value, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap text-gray-800">{{ $item->quantity }}</td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap font-medium text-gray-800">{{ number_format($item->value * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-blue-50">
                                <tr>
                                    <td colspan="3" class="px-2 py-1.5 text-right font-medium text-blue-700">Total:</td>
                                    <td class="px-2 py-1.5 text-right font-medium text-blue-700">
                                        {{ number_format($barter->value_given, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Articles reçus par le client -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                    <div class="bg-green-50 px-3 py-2 border-b border-green-100">
                        <h3 class="text-sm font-medium text-green-700">Articles reçus par le client</h3>
                    </div>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-green-200 text-xs">
                                <thead class="bg-green-50">
                                <tr>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-green-700 uppercase tracking-wider">Prix</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-green-700 uppercase tracking-wider">Qté</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-green-700 uppercase tracking-wider">Total</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-green-100">
                                @foreach($barter->receivedItems as $item)
                                    <tr class="hover:bg-green-50">
                                        <td class="px-2 py-1.5 whitespace-nowrap text-gray-800">
                                            @if($item->product_id)
                                                <a href="{{ route('products.show', $item->product_id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    {{ $item->name }}
                                                </a>
                                            @else
                                                {{ $item->name }}
                                            @endif
                                            @if($item->description)
                                                <p class="text-gray-500 text-xs italic">{{ Str::limit($item->description, 30) }}</p>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap text-gray-800">{{ number_format($item->value, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap text-gray-800">{{ $item->quantity }}</td>
                                        <td class="px-2 py-1.5 text-right whitespace-nowrap font-medium text-gray-800">{{ number_format($item->value * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="bg-green-50">
                                <tr>
                                    <td colspan="3" class="px-2 py-1.5 text-right font-medium text-green-700">Total:</td>
                                    <td class="px-2 py-1.5 text-right font-medium text-green-700">
                                        {{ number_format($barter->value_received, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé et paiement -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                <div class="bg-indigo-50 px-3 py-2 border-b border-indigo-100">
                    <h3 class="text-sm font-medium text-indigo-700">Résumé et paiement</h3>
                </div>
                <div class="p-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="bg-blue-50 p-2 rounded border border-blue-100">
                            <p class="font-medium text-blue-700">Total donné par le client</p>
                            <p class="text-xl font-bold text-blue-800">{{ number_format($barter->value_given, 0, ',', ' ') }} FCFA</p>
                        </div>

                        <div class="bg-green-50 p-2 rounded border border-green-100">
                            <p class="font-medium text-green-700">Total reçu par le client</p>
                            <p class="text-xl font-bold text-green-800">{{ number_format($barter->value_received, 0, ',', ' ') }} FCFA</p>
                        </div>

                        <div class="bg-{{ $barter->additional_payment > 0 ? 'yellow' : 'gray' }}-50 p-2 rounded border border-{{ $barter->additional_payment > 0 ? 'yellow' : 'gray' }}-100">
                            <p class="font-medium text-{{ $barter->additional_payment > 0 ? 'yellow' : 'gray' }}-700">
                                {{ $barter->additional_payment > 0 ? 'Paiement complémentaire client' : 'Équilibrage' }}
                            </p>
                            <p class="text-xl font-bold text-{{ $barter->additional_payment > 0 ? 'yellow' : 'gray' }}-800">
                                {{ number_format(abs($barter->additional_payment), 0, ',', ' ') }} FCFA
                            </p>
                            @if($barter->payment_method && $barter->additional_payment > 0)
                                <p class="text-xs mt-1 text-gray-600">Mode: {{ $barter->payment_method }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images du troc -->
            @if(isset($barter->images) && $barter->images->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                    <div class="bg-purple-50 px-3 py-2 border-b border-purple-100">
                        <h3 class="text-sm font-medium text-purple-700">Images du troc</h3>
                    </div>
                    <div class="p-3">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                            @foreach($barter->images as $image)
                                <div class="relative group">
                                    <a href="{{ asset('storage/' . $image->path) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Image du troc" class="w-full h-28 object-cover rounded shadow hover:shadow-md transition">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </a>
                                    @if($barter->status === 'pending')
                                        <form action="{{ route('barters.deleteImage', $image) }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <div class="text-xs mt-1">
                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ $image->type == 'given' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $image->type == 'given' ? 'Donné' : 'Reçu' }}
                                </span>
                                        @if($image->description)
                                            <p class="truncate mt-0.5">{{ $image->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ajouter des images -->
            @if($barter->status === 'pending')
                <div class="bg-white rounded-lg shadow overflow-hidden mb-4">
                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-100">
                        <h3 class="text-sm font-medium text-gray-700">Ajouter des images</h3>
                    </div>
                    <div class="p-3">
                        <form action="{{ route('barters.addImages', $barter) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Sélectionner des images</label>
                                    <input type="file" name="images[]" multiple accept="image/*"
                                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPEG, PNG, GIF. Max 5MB par image.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Type d'images</label>
                                    <select name="types[]" class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="given">Articles donnés par le client</option>
                                        <option value="received">Articles reçus par le client</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                Ajouter les images
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
