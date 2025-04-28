<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Exporter des produits') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Exportez votre catalogue de produits au format CSV') }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Retour aux produits') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-blue-600 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-base font-medium text-blue-800">{{ __('Instructions d\'exportation') }}</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>{{ __('Filtrez les produits que vous souhaitez exporter ou exportez l\'intégralité de votre catalogue.') }}</p>
                                    <p class="mt-2">{{ __('Le fichier CSV généré peut être ouvert dans Excel ou importé dans d\'autres systèmes.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('products.export') }}" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Type de produit') }}
                                </label>
                                <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('Tous les types') }}</option>
                                    <option value="physical">{{ __('Produits physiques') }}</option>
                                    <option value="service">{{ __('Services') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Statut') }}
                                </label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="actif">{{ __('Actif') }}</option>
                                    <option value="inactif">{{ __('Inactif') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('État du stock') }}
                                </label>
                                <select id="stock" name="stock" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('Tous les états') }}</option>
                                    <option value="available">{{ __('En stock') }}</option>
                                    <option value="low">{{ __('Stock bas') }}</option>
                                    <option value="out">{{ __('Épuisé') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Recherche par nom ou description') }}
                                </label>
                                <input type="text" id="search" name="search" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" placeholder="{{ __('Rechercher...') }}">
                            </div>
                        </div>

                        <div class="flex justify-between pt-4">
                            <a href="{{ route('products.export') }}" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                                <i class="bi bi-arrow-repeat mr-2"></i>
                                {{ __('Réinitialiser les filtres') }}
                            </a>
                            <div>
                                <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg mr-3 inline-flex items-center transition-colors duration-150">
                                    <i class="bi bi-x-lg mr-2"></i>
                                    {{ __('Annuler') }}
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                                    <i class="bi bi-download mr-2"></i>
                                    {{ __('Exporter maintenant') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Exportation rapide') }}</h3>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <a href="{{ route('products.export') }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-box-seam text-indigo-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Tous les produits') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Exporter tous les produits') }}</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('products.export', ['type' => 'physical']) }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-box text-green-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Produits physiques') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Uniquement les produits physiques') }}</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('products.export', ['type' => 'service']) }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-gear text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Services') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Uniquement les services') }}</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('products.export', ['stock' => 'low']) }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-exclamation-triangle text-amber-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Stock bas') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Produits à réapprovisionner') }}</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('products.export', ['stock' => 'out']) }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-x-circle text-red-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Épuisés') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Produits en rupture de stock') }}</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('products.export', ['status' => 'actif']) }}" class="bg-white border border-gray-200 rounded-md shadow-sm p-4 hover:bg-gray-50 flex items-center">
                                <i class="bi bi-check-circle text-green-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ __('Actifs') }}</h4>
                                    <p class="text-xs text-gray-500">{{ __('Produits actuellement actifs') }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 