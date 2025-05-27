<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Mappage des colonnes pour l\'importation') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Associez les colonnes de votre fichier aux champs du système') }}
                </p>
            </div>
            <a href="{{ route('products.import.form') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Instructions -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 text-sm text-blue-700">
                            <h3 class="font-medium">{{ __('Comment utiliser le mappage des colonnes') }}</h3>
                            <ul class="mt-1 list-disc list-inside space-y-1">
                                <li>{{ __('Pour chaque colonne de votre fichier, sélectionnez le champ correspondant dans le système') }}</li>
                                <li>{{ __('Les colonnes non mappées seront ignorées lors de l\'importation') }}</li>
                                <li>{{ __('Le champ "Nom du produit" est obligatoire') }}</li>
                                <li>{{ __('Les produits existants seront mis à jour s\'ils ont le même SKU') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Aperçu des données -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Aperçu des données') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach($headers as $index => $header)
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $header }}
                                            <span class="block text-gray-400 font-normal">(Colonne {{ $index + 1 }})</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sampleData as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 truncate max-w-xs">
                                                {{ is_null($cell) ? '' : $cell }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Aperçu limité aux 5 premières lignes du fichier') }}</p>
                </div>

                <!-- Formulaire de mappage -->
                <form action="{{ route('products.process-mapping') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Mappage des colonnes') }}</h3>
                        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            @foreach($headers as $index => $header)
                                <div class="border border-gray-200 rounded-md p-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $header }}
                                        <span class="text-gray-400">(Colonne {{ $index + 1 }})</span>
                                    </label>
                                    <select name="column_mapping[{{ $index }}]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('Ignorer cette colonne') }}</option>
                                        @foreach($expectedFields as $field => $label)
                                            <option value="{{ $field }}" {{ (isset($suggestedMapping[$index]) && $suggestedMapping[$index] === $field) ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Catégorie par défaut') }}</label>
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('Aucune catégorie par défaut') }}</option>
                                @foreach(\App\Models\ProductCategory::orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}" {{ $defaultCategoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sera utilisée si aucune catégorie n\'est spécifiée dans le fichier') }}</p>
                        </div>
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Fournisseur par défaut') }}</label>
                            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('Aucun fournisseur par défaut') }}</option>
                                @foreach(\App\Models\Supplier::orderBy('name')->get() as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $defaultSupplierId == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sera utilisé si aucun fournisseur n\'est spécifié dans le fichier') }}</p>
                        </div>
                    </div>
                    
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                        <div>
                            <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Marque par défaut') }}</label>
                            <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('Aucune marque par défaut') }}</option>
                                @foreach(\App\Models\Brand::orderBy('name')->get() as $brand)
                                    <option value="{{ $brand->id }}" {{ (isset($defaultBrandId) && $defaultBrandId == $brand->id) ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Sera utilisée si aucune marque n\'est spécifiée dans le fichier') }}</p>
                        </div>
                        <div>
                            <div class="flex items-center h-full mt-8">
                                <label class="flex items-center">
                                    <input type="checkbox" name="create_missing_brands" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Créer automatiquement les marques et modèles manquants') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 flex justify-end">
                        <a href="{{ route('products.import.form') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md inline-flex items-center mr-3">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Lancer l\'importation') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour mettre à jour la couleur de fond des sélecteurs
            function updateSelectBackgrounds() {
                const selects = document.querySelectorAll('select[name^="column_mapping"]');
                const selectedValues = new Map();
                
                // Compter les occurrences de chaque valeur
                selects.forEach(select => {
                    const value = select.value;
                    if (value) {
                        selectedValues.set(value, (selectedValues.get(value) || 0) + 1);
                    }
                });
                
                // Appliquer des classes selon si la valeur est unique ou dupliquée
                selects.forEach(select => {
                    const value = select.value;
                    
                    // Réinitialiser les classes
                    select.classList.remove('bg-red-50', 'border-red-300', 'text-red-900', 'bg-green-50', 'border-green-300', 'text-green-900');
                    
                    if (value && value !== '') {
                        if (selectedValues.get(value) > 1) {
                            // Valeur dupliquée -> rouge
                            select.classList.add('bg-red-50', 'border-red-300', 'text-red-900');
                        } else {
                            // Valeur unique -> vert
                            select.classList.add('bg-green-50', 'border-green-300', 'text-green-900');
                        }
                    }
                });
                
                // Vérifier si le champ "name" est mappé
                const nameFieldMapped = Array.from(selects).some(select => select.value === 'name');
                const submitButton = document.querySelector('button[type="submit"]');
                
                if (!nameFieldMapped) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    
                    // Ajouter un avertissement s'il n'existe pas déjà
                    if (!document.getElementById('name-warning')) {
                        const warning = document.createElement('div');
                        warning.id = 'name-warning';
                        warning.className = 'mt-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm';
                        warning.innerHTML = '<strong>Attention :</strong> Le champ "Nom du produit" est obligatoire pour l\'importation.';
                        submitButton.parentNode.insertBefore(warning, submitButton);
                    }
                } else {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    
                    // Supprimer l'avertissement s'il existe
                    const warning = document.getElementById('name-warning');
                    if (warning) {
                        warning.remove();
                    }
                }
            }
            
            // Ajouter des écouteurs d'événements pour tous les sélecteurs
            const selects = document.querySelectorAll('select[name^="column_mapping"]');
            selects.forEach(select => {
                select.addEventListener('change', updateSelectBackgrounds);
            });
            
            // Initialiser les couleurs
            updateSelectBackgrounds();
        });
    </script>
    @endpush
</x-app-layout> 