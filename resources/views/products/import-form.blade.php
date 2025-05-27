<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Importer des produits') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Importez des produits en masse à partir d\'un fichier Excel ou CSV') }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Retour aux produits') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Formulaire d'importation -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Fichier à importer') }}</h3>
                                
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" 
                                    x-data="{ fileName: '', isFileSelected: false }"
                                    x-on:dragover.prevent="$el.classList.add('border-indigo-500', 'bg-indigo-50')"
                                    x-on:dragleave.prevent="$el.classList.remove('border-indigo-500', 'bg-indigo-50')"
                                    x-on:drop.prevent="
                                        $el.classList.remove('border-indigo-500', 'bg-indigo-50');
                                        if ($event.dataTransfer.files.length > 0) {
                                            document.getElementById('product_file').files = $event.dataTransfer.files;
                                            fileName = $event.dataTransfer.files[0].name;
                                            isFileSelected = true;
                                        }
                                    ">
                                    <div x-show="!isFileSelected">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32 10v6m0 0v6m0-6h6m-6 0h-6"></path>
                                        </svg>
                                        <p class="mt-4 text-sm text-gray-600">
                                            {{ __('Glissez-déposez un fichier ici ou ') }}
                                            <label for="product_file" class="text-indigo-600 font-medium cursor-pointer hover:text-indigo-700">
                                                {{ __('parcourez') }}
                                            </label>
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ __('Formats acceptés: CSV, Excel (.xlsx, .xls)') }}
                                        </p>
                                    </div>
                                    <div x-show="isFileSelected" class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm font-medium text-gray-800" x-text="fileName"></p>
                                        <button type="button" 
                                            class="mt-2 text-xs text-red-600 hover:text-red-800" 
                                            x-on:click="
                                                document.getElementById('product_file').value = '';
                                                fileName = '';
                                                isFileSelected = false;
                                            ">
                                            {{ __('Supprimer le fichier') }}
                                        </button>
                                    </div>
                                    <input type="file" id="product_file" name="product_file" class="sr-only" 
                                        accept=".csv,.xls,.xlsx"
                                        x-on:change="
                                            fileName = $event.target.files[0].name;
                                            isFileSelected = $event.target.files.length > 0;
                                        ">
                                </div>
                                @error('product_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="has_headers" name="has_headers" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="has_headers" class="ml-2 block text-sm text-gray-700">
                                        {{ __('La première ligne contient les en-têtes de colonnes') }}
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Cochez cette case si la première ligne de votre fichier contient les noms des colonnes') }}
                                </p>
                            </div>
                            
                            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Catégorie par défaut') }}</label>
                                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('Aucune catégorie par défaut') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('Sera utilisée si le fichier ne spécifie pas de catégorie') }}
                                    </p>
                                </div>
                                <div>
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fournisseur par défaut') }}</label>
                                    <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('Aucun fournisseur par défaut') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('Sera utilisé si le fichier ne spécifie pas de fournisseur') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                                <div>
                                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Marque par défaut') }}</label>
                                    <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">{{ __('Aucune marque par défaut') }}</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('Sera utilisée si le fichier ne spécifie pas de marque') }}
                                    </p>
                                </div>
                                <div>
                                    <div class="flex items-center mt-8">
                                        <input type="checkbox" id="create_missing_brands" name="create_missing_brands" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="create_missing_brands" class="ml-2 block text-sm text-gray-700">
                                            {{ __('Créer automatiquement les marques et modèles manquants') }}
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ __('Lorsqu\'une marque ou un modèle n\'existe pas, il sera créé automatiquement') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    {{ __('Analyser le fichier et mapper les colonnes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Aide et instructions -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Instructions') }}</h3>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span>{{ __('Préparez un fichier Excel ou CSV contenant les données de vos produits') }}</span>
                                </li>
                                <li class="flex">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    <span>{{ __('Après avoir téléchargé le fichier, vous pourrez mapper les colonnes selon vos besoins') }}</span>
                                </li>
                                <li class="flex">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ __('Seul le nom du produit est obligatoire, les autres champs sont optionnels') }}</span>
                                </li>
                                <li class="flex">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>{{ __('Les produits avec SKU existants seront mis à jour, les autres seront créés') }}</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Télécharger un modèle') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('Vous pouvez télécharger un modèle préformaté pour vous aider à structurer vos données.') }}
                            </p>
                            <a href="{{ route('products.import.template') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                {{ __('Télécharger le modèle') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Information sur le système de mappage -->
            <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <div class="flex">
                    <svg class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-blue-900 mb-1">{{ __('Nouveau système de mappage des colonnes') }}</h3>
                        <p class="text-sm text-blue-700">
                            {{ __('Notre système vous permet désormais de mapper manuellement les colonnes de votre fichier d\'importation aux champs du système, même si les en-têtes ne correspondent pas exactement. Vous pouvez également ignorer les colonnes non pertinentes et définir des valeurs par défaut.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 