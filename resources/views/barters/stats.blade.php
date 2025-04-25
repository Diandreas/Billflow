<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    Statistiques des Trocs
                </h2>
                <div class="flex space-x-2">
                    <a href="{{ route('barters.index') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                        </svg>
                        Liste des Trocs
                    </a>
                    <a href="{{ route('barters.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Nouveau Troc
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <!-- Carte des statistiques générales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques générales</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Nombre total de trocs -->
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <div class="flex items-center mb-2">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-blue-900">Nombre total de trocs</h4>
                            </div>
                            <p class="text-3xl font-bold text-blue-700">{{ array_sum($statusCounts) }}</p>
                            <div class="mt-2 text-sm text-blue-600">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                    </svg>
                                    Tous les statuts confondus
                                </div>
                            </div>
                        </div>

                        <!-- Valeur totale échangée -->
                        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                            <div class="flex items-center mb-2">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-green-900">Valeur totale échangée</h4>
                            </div>
                            <p class="text-3xl font-bold text-green-700">{{ number_format($totalGivenValue, 0, ',', ' ') }} FCFA</p>
                            <div class="mt-2 text-sm text-green-600">
                                <div class="flex justify-between">
                                    <span>Valeur donnée</span>
                                    <span>{{ number_format($totalGivenValue, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Valeur reçue</span>
                                    <span>{{ number_format($totalReceivedValue, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>

                        <!-- Répartition par statut -->
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <div class="flex items-center mb-2">
                                <div class="bg-purple-100 rounded-full p-2 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-purple-900">Répartition par statut</h4>
                            </div>
                            <div class="space-y-2 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-700">En attente</span>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $statusCounts['pending'] ?? 0 }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-700">Complétés</span>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $statusCounts['completed'] ?? 0 }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-700">Annulés</span>
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $statusCounts['cancelled'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Balance de valeur -->
                        <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                            <div class="flex items-center mb-2">
                                <div class="bg-amber-100 rounded-full p-2 mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.94 5.5c.944-.945 2.56-.276 2.56 1.06V8h5.75a.75.75 0 010 1.5H8.5v.938c0 1.337-1.616 2.005-2.56 1.06L3.22 8.79a.75.75 0 010-1.06L5.94 5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-amber-900">Balance de valeur</h4>
                            </div>
                            @php
                                $balance = $totalReceivedValue - $totalGivenValue;
                                $isPositive = $balance >= 0;
                            @endphp
                            <p class="text-3xl font-bold {{ $isPositive ? 'text-green-700' : 'text-red-700' }}">
                                {{ $isPositive ? '+' : '' }}{{ number_format($balance, 0, ',', ' ') }} FCFA
                            </p>
                            <div class="mt-2 text-sm {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $isPositive ? 'Bénéfice net sur les trocs' : 'Déficit net sur les trocs' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits les plus échangés -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Produits les plus échangés</h3>

                        @if($topProducts->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <p>Aucun produit n'a encore été échangé</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($topProducts as $item)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->product->name }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                <div class="text-sm text-gray-900">{{ $item->total_quantity }}</div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                                <a href="{{ route('products.show', $item->product) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                                <span class="mx-1">|</span>
                                                <a href="{{ route('barters.index', ['product_id' => $item->product_id]) }}" class="text-indigo-600 hover:text-indigo-900">Trocs</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Trocs récents -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Trocs récents</h3>

                        @if($recentBarters->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <p>Aucun troc n'a encore été effectué</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentBarters as $barter)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <a href="{{ route('barters.show', $barter) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $barter->reference }}
                                                </a>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $barter->client->name }}</div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-500">
                                                {{ $barter->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                @if($barter->status === 'pending')
                                                    <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                                @elseif($barter->status === 'completed')
                                                    <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">Complété</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">Annulé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 text-right">
                                <a href="{{ route('barters.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    Voir tous les trocs →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Graphiques et tendances -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tendances des trocs</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Ici on pourrait ajouter des graphiques ou des visualisations -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Évolution mensuelle</h4>
                            <div class="h-64 flex items-center justify-center text-gray-500">
                                <p>Graphique d'évolution mensuelle des trocs</p>
                                <!-- Dans un cas réel, on utiliserait une bibliothèque de graphiques comme Chart.js -->
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Répartition par type de troc</h4>
                            <div class="h-64 flex items-center justify-center text-gray-500">
                                <p>Graphique de répartition par type (même type vs différent)</p>
                                <!-- Dans un cas réel, on utiliserait une bibliothèque de graphiques comme Chart.js -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conseils et optimisations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Conseils d'optimisation</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <h4 class="text-md font-medium text-blue-800 mb-2">Produits populaires</h4>
                            <p class="text-sm text-blue-700 mb-2">
                                Assurez-vous d'avoir suffisamment de stock pour les produits les plus échangés.
                            </p>
                            <a href="{{ route('products.index') }}?stock=low" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                </svg>
                                Vérifier les stocks
                            </a>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                            <h4 class="text-md font-medium text-green-800 mb-2">Valeur des trocs</h4>
                            <p class="text-sm text-green-700 mb-2">
                                La valeur moyenne des trocs est de {{ number_format(($totalGivenValue + $totalReceivedValue) / (2 * max(1, array_sum($statusCounts))), 0, ',', ' ') }} FCFA.
                            </p>
                            <a href="{{ route('barters.create') }}" class="text-sm text-green-600 hover:text-green-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                </svg>
                                Créer un nouveau troc
                            </a>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <h4 class="text-md font-medium text-purple-800 mb-2">Conversion en factures</h4>
                            <p class="text-sm text-purple-700 mb-2">
                                Tous les trocs sont automatiquement convertis en factures pour un meilleur suivi.
                            </p>
                            <a href="{{ route('bills.index') }}?type=barter" class="text-sm text-purple-600 hover:text-purple-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                Voir les factures de troc
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
