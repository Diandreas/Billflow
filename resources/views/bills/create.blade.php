<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Nouvelle Facture Intelligente') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Créez et gérez vos factures avec assistance intelligente') }}
                </p>
            </div>
            <a href="{{ route('bills.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Messages d'erreur --}}
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm animate-fadeIn" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm animate-fadeIn" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ __('Veuillez corriger les erreurs suivantes:') }}</p>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Barre de progression intelligente --}}
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-medium text-gray-700">{{ __('Progression de la facture') }}</h3>
                    <span id="progressPercentage" class="text-sm text-gray-500">25%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: 25%"></div>
                </div>
                <div class="mt-3 grid grid-cols-4 gap-4 text-xs">
                    <div id="step1" class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                        <span class="text-green-700">{{ __('Informations de base') }}</span>
                    </div>
                    <div id="step2" class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                        <span class="text-gray-500">{{ __('Client') }}</span>
                    </div>
                    <div id="step3" class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                        <span class="text-gray-500">{{ __('Produits') }}</span>
                    </div>
                    <div id="step4" class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                        <span class="text-gray-500">{{ __('Finalisation') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Formulaire principal --}}
                <div class="lg:col-span-2">
                    <form id="smartBillForm" action="{{ route('bills.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Section Client avec validation intelligente --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Sélection du Client') }}
                                    </h3>
                                    <button type="button"
                                            id="newClientBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        {{ __('Nouveau Client') }}
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    {{-- Recherche client avec intelligence --}}
                                    <div>
                                        <label for="client_search" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Client') }} <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text"
                                                   id="client_search"
                                                   autocomplete="off"
                                                   class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                   placeholder="Rechercher un client par nom, téléphone ou email...">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div id="clientSearchLoader" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                                <div class="spinner"></div>
                                            </div>
                                            <input type="hidden" id="client_id" name="client_id" required>
                                            <div id="clientSearchResults" class="hidden absolute z-20 mt-1 w-full bg-white shadow-lg rounded-md max-h-60 overflow-auto border border-gray-200"></div>
                                        </div>
                                    </div>

                                    {{-- Client sélectionné avec statistiques --}}
                                    <div id="selectedClientCard" class="hidden mt-3 bg-blue-50 rounded-md p-4 border border-blue-200">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold" id="clientInitials"></div>
                                                <div class="ml-3 flex-1">
                                                    <div class="text-lg font-medium text-gray-900" id="selectedClientName"></div>
                                                    <div class="text-sm text-gray-600 mt-1" id="selectedClientDetails"></div>
                                                    <div class="text-xs text-gray-500 mt-1" id="clientStats"></div>
                                                </div>
                                            </div>
                                            <button type="button" id="changeClientBtn" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                                {{ __('Changer') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section Produits avec gestion intelligente du stock --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Produits et Services') }}
                                    </h3>
                                    <button type="button"
                                            id="addProductBtn"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ __('Ajouter des Produits') }}
                                    </button>
                                </div>

                                {{-- Recherche rapide de produits --}}
                                <div class="mb-4">
                                    <div class="relative">
                                        <input type="text" 
                                               id="productQuickSearch" 
                                               placeholder="Scan code-barres ou recherche rapide..." 
                                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                        </div>
                                        <div id="quickProductSuggestions" class="hidden absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border max-h-40 overflow-auto"></div>
                                    </div>
                                </div>

                                {{-- État vide et table des produits --}}
                                <div id="emptyProductsState" class="py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucun produit') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par ajouter des produits à votre facture.') }}</p>
                                    <div class="mt-6">
                                        <button type="button"
                                                onclick="document.getElementById('addProductBtn').click()"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('Ajouter des produits') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Table des produits --}}
                                <div id="productsTableContainer" class="hidden overflow-x-auto">
                                    <table id="productsTable" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Produit') }}
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                                {{ __('Stock') }}
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                                {{ __('Quantité') }}
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                                {{ __('Prix unitaire') }}
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                                {{ __('Total') }}
                                            </th>
                                            <th class="relative px-6 py-3 w-20">
                                                <span class="sr-only">{{ __('Actions') }}</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="productsContainer" class="bg-white divide-y divide-gray-200"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Informations de la facture --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    {{ __('Informations de la Facture') }}
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="date" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Date') }}
                                        </label>
                                        <input type="date"
                                               id="date"
                                               name="date"
                                               value="{{ old('date', date('Y-m-d')) }}"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>

                                    <div>
                                        <label for="bill_reference" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Référence') }}
                                        </label>
                                        <input type="text"
                                               id="bill_reference"
                                               name="reference"
                                               value="{{ old('reference', 'FACT-' . date('Ymd') . '-' . rand(1000, 9999)) }}"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               readonly>
                                    </div>

                                    <div>
                                        <label for="tax_rate" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('TVA (%)') }}
                                        </label>
                                        <input type="number"
                                               id="tax_rate"
                                               name="tax_rate"
                                               value="{{ old('tax_rate', 18) }}"
                                               min="0"
                                               max="100"
                                               step="0.01"
                                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>

                                    <div>
                                        <label for="payment_method" class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ __('Méthode de paiement') }}
                                        </label>
                                        <select id="payment_method" name="payment_method" class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="espèces">{{ __('Espèces') }}</option>
                                            <option value="carte">{{ __('Carte bancaire') }}</option>
                                            <option value="virement">{{ __('Virement bancaire') }}</option>
                                            <option value="chèque">{{ __('Chèque') }}</option>
                                            <option value="mobile_money">{{ __('Mobile Money') }}</option>
                                            <option value="autre">{{ __('Autre') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="notes" class="block mb-1 text-sm font-medium text-gray-700">
                                        {{ __('Notes') }}
                                    </label>
                                    <textarea id="notes"
                                              name="notes"
                                              rows="3"
                                              class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                              placeholder="{{ __('Notes supplémentaires...') }}">{{ old('notes', 'Merci pour votre confiance! La facture est payable sous 30 jours.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Boutique et Vendeur --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Boutique --}}
                                    <div>
                                        <label for="shop_id" class="block text-sm font-medium text-gray-700">{{ __('Boutique') }} <span class="text-red-500">*</span></label>
                                        @if(Auth::user()->role === 'vendeur')
                                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $shops->first()->name ?? '' }}" readonly>
                                            <input type="hidden" name="shop_id" id="shop_id" value="{{ $defaultShopId ?? '' }}">
                                        @else
                                            <select id="shop_id" name="shop_id" onchange="updateVendorsList(this.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                <option value="">{{ __('Sélectionner une boutique') }}</option>
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}" {{ old('shop_id', $defaultShopId ?? '') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @error('shop_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Vendeur --}}
                                    <div>
                                        <label for="user_id" class="block text-sm font-medium text-gray-700">{{ __('Vendeur') }} <span class="text-red-500">*</span></label>
                                        @if(Auth::user()->role === 'vendeur')
                                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ Auth::user()->name }}" readonly>
                                            <input type="hidden" name="seller_id" id="user_id" value="{{ $defaultSellerId ?? Auth::user()->id }}">
                                        @else
                                            <select id="user_id" name="seller_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                <option value="">{{ __('Sélectionner un vendeur') }}</option>
                                                @foreach ($sellers as $seller)
                                                    <option value="{{ $seller->id }}" {{ old('seller_id', $defaultSellerId ?? '') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @error('seller_id')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex justify-between items-center">
                            <button type="button" id="saveDraftBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                {{ __('Enregistrer comme brouillon') }}
                            </button>
                            <button type="submit"
                                    id="submitBtn"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 disabled:opacity-50"
                                    disabled>
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Créer la Facture') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Panneau de récapitulatif intelligent --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ __('Récapitulatif Intelligent') }}
                            </h3>

                            {{-- Informations générales --}}
                            <div class="bg-gray-50 rounded-md p-4 mb-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('Client:') }}</span>
                                        <span id="summaryClientName" class="text-sm font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('Référence:') }}</span>
                                        <span id="summaryReference" class="text-sm font-medium">{{ 'FACT-' . date('Ymd') . '-' . rand(1000, 9999) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('Date:') }}</span>
                                        <span id="summaryDate" class="text-sm font-medium">{{ date('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('Articles:') }}</span>
                                        <span id="summaryItems" class="text-sm font-medium">0</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Alertes intelligentes --}}
                            <div id="smartAlerts" class="space-y-2 mb-6"></div>

                            {{-- Détails des montants --}}
                            <div>
                                <h4 class="font-medium text-sm text-gray-700 mb-2">{{ __('Détails du montant') }}</h4>
                                <div class="space-y-1 text-sm mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('Sous-total:') }}</span>
                                        <span id="summarySubtotal" class="font-medium">0 FCFA</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('TVA') }} (<span id="summaryTaxRate">18</span>%):</span>
                                        <span id="summaryTaxAmount" class="font-medium">0 FCFA</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('Remise:') }}</span>
                                        <span id="summaryDiscount" class="font-medium">0 FCFA</span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">{{ __('Total:') }}</span>
                                        <span id="summaryTotal" class="text-xl font-bold text-indigo-600">0 FCFA</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Suggestions intelligentes --}}
                            <div id="smartSuggestions" class="mt-6">
                                <h4 class="font-medium text-sm text-gray-700 mb-2">{{ __('Suggestions') }}</h4>
                                <div id="suggestionsContainer" class="space-y-2">
                                    <p class="text-xs text-gray-500">{{ __('Les suggestions apparaîtront automatiquement...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de nouveau client --}}
    <div id="newClientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 transform transition-all">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Ajouter un nouveau client') }}</h3>
                <button type="button" onclick="toggleNewClientModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="quickClientForm" class="p-4 space-y-4">
                @csrf
                <div>
                    <label for="quick_client_name" class="block text-sm font-medium text-gray-700">{{ __('Nom') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="quick_client_name" name="name" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input type="email" id="quick_client_email" name="email" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_phone" class="block text-sm font-medium text-gray-700">{{ __('Téléphone') }} <span class="text-red-500">*</span></label>
                    <input type="text" id="quick_client_phone" name="phone" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_address" class="block text-sm font-medium text-gray-700">{{ __('Adresse') }}</label>
                    <textarea id="quick_client_address" name="address" rows="2" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-3 border-t">
                    <button type="button" onclick="toggleNewClientModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('Annuler') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Ajouter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de sélection de produits --}}
    <div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-medium">{{ __('Sélectionner des Produits') }}</h3>
                <button type="button" id="closeProductModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                {{-- Recherche et filtres --}}
                <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" id="modalProductSearch" placeholder="{{ __('Rechercher des produits...') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <select id="productTypeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('Tous les types') }}</option>
                            <option value="physical">{{ __('Produits physiques') }}</option>
                            <option value="service">{{ __('Services') }}</option>
                        </select>
                    </div>
                    <div>
                        <select id="stockFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('Tout stock') }}</option>
                            <option value="available">{{ __('En stock') }}</option>
                            <option value="low">{{ __('Stock faible') }}</option>
                            <option value="out">{{ __('Rupture de stock') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Grille de produits --}}
                <div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto mb-6">
                    {{-- Les produits seront générés par JavaScript --}}
                </div>

                {{-- Actions du modal --}}
                <div class="flex justify-between items-center border-t pt-4">
                    <span id="selectedCount" class="text-sm text-gray-600">{{ __('0 produit(s) sélectionné(s)') }}</span>
                    <div class="space-x-3">
                        <button type="button" id="cancelProductSelection" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            {{ __('Annuler') }}
                        </button>
                        <button type="button" id="confirmProductSelection" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            {{ __('Ajouter à la facture') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Container pour les notifications toast --}}
    <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    @push('styles')
        <style>
            /* Animations personnalisées */
            .animate-fadeIn { animation: fadeIn 0.3s ease-in-out; }
            .animate-scaleIn { animation: scaleIn 0.2s ease-out; }
            .animate-highlight { animation: highlight 1s ease-in-out; }
            .animate-pulse-error { animation: pulseError 0.5s ease-in-out; }

            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            @keyframes scaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
            @keyframes highlight { 0% { background-color: rgba(79, 70, 229, 0.1); } 100% { background-color: transparent; } }
            @keyframes pulseError { 0%, 100% { border-color: #ef4444; } 50% { border-color: #f87171; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); } }

            /* Styles pour les états de stock */
            .stock-high { color: #059669; }
            .stock-medium { color: #d97706; }
            .stock-low { color: #dc2626; }
            .stock-out { color: #7f1d1d; background-color: #fef2f2; }

            /* Styles pour les produits */
            .product-card { transition: all 0.2s ease; border: 2px solid transparent; }
            .product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
            .product-card.selected { border-color: #4F46E5; background-color: rgba(79, 70, 229, 0.05); }
            .product-card.disabled { opacity: 0.5; cursor: not-allowed; }

            /* Indicateurs de validation */
            .field-valid { border-color: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1); }
            .field-invalid { border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1); }

            /* Loading spinner */
            .spinner { border: 2px solid #f3f3f3; border-top: 2px solid #4F46E5; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

            /* Suggestion dropdown */
            .suggestion-item { transition: background-color 0.15s ease; }
            .suggestion-item.highlighted { background-color: #e0e7ff; }

            /* Stock indicator */
            .stock-indicator { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 5px; }
            .stock-indicator.high { background-color: #10b981; }
            .stock-indicator.medium { background-color: #f59e0b; }
            .stock-indicator.low { background-color: #ef4444; }
            .stock-indicator.out { background-color: #7f1d1d; }

            /* Input number styling */
            input[type="number"] { -moz-appearance: textfield; }
            input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Variables globales pour Laravel/Blade
            const clients = @json($clients ?? []);
            const products = @json($products ?? []);
            let selectedClient = null;
            let invoiceProducts = [];
            let selectedProductsInModal = [];
            let hasClientSelected = false;
            let hasProductsAdded = false;

            // Initialisation
            document.addEventListener('DOMContentLoaded', function() {
                initializeSmartInvoice();
                setupEventListeners();
                updateProgressBar();
            });

            function initializeSmartInvoice() {
                // Initialiser la référence dans le récapitulatif
                document.getElementById('summaryReference').textContent = document.getElementById('bill_reference').value;
                
                // Mettre à jour la date dans le récapitulatif
                const dateInput = document.getElementById('date');
                const dateObj = new Date(dateInput.value);
                document.getElementById('summaryDate').textContent = dateObj.toLocaleDateString('fr-FR');

                // Initialiser le taux de TVA
                document.getElementById('summaryTaxRate').textContent = document.getElementById('tax_rate').value;

                updateSummary();
                checkEmptyProductsState();
            }

            function setupEventListeners() {
                // Recherche de client
                const clientSearch = document.getElementById('client_search');
                clientSearch.addEventListener('input', debounce(searchClients, 300));
                clientSearch.addEventListener('focus', () => {
                    if (clientSearch.value.length >= 2) {
                        searchClients();
                    }
                });

                // Cacher les suggestions quand on clique ailleurs
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#client_search') && !e.target.closest('#clientSearchResults')) {
                        document.getElementById('clientSearchResults').classList.add('hidden');
                    }
                    if (!e.target.closest('#productQuickSearch') && !e.target.closest('#quickProductSuggestions')) {
                        document.getElementById('quickProductSuggestions').classList.add('hidden');
                    }
                });

                // Changer de client
                document.getElementById('changeClientBtn').addEventListener('click', clearSelectedClient);

                // Nouveau client
                document.getElementById('newClientBtn').addEventListener('click', toggleNewClientModal);

                // Recherche rapide de produits
                const productQuickSearch = document.getElementById('productQuickSearch');
                productQuickSearch.addEventListener('input', debounce(quickProductSearch, 200));
                productQuickSearch.addEventListener('keydown', handleProductQuickSearchKeydown);

                // Modal de produits
                document.getElementById('addProductBtn').addEventListener('click', openProductModal);
                document.getElementById('closeProductModal').addEventListener('click', closeProductModal);
                document.getElementById('cancelProductSelection').addEventListener('click', closeProductModal);
                document.getElementById('confirmProductSelection').addEventListener('click', addSelectedProductsToInvoice);

                // Filtres dans le modal
                document.getElementById('modalProductSearch').addEventListener('input', debounce(filterProductsInModal, 200));
                document.getElementById('productTypeFilter').addEventListener('change', filterProductsInModal);
                document.getElementById('stockFilter').addEventListener('change', filterProductsInModal);

                // Champs de la facture
                document.getElementById('tax_rate').addEventListener('input', () => {
                    document.getElementById('summaryTaxRate').textContent = document.getElementById('tax_rate').value;
                    updateSummary();
                });

                document.getElementById('date').addEventListener('change', function() {
                    const dateObj = new Date(this.value);
                    document.getElementById('summaryDate').textContent = dateObj.toLocaleDateString('fr-FR');
                });

                document.getElementById('bill_reference').addEventListener('input', function() {
                    document.getElementById('summaryReference').textContent = this.value;
                });

                // Formulaire de client rapide
                document.getElementById('quickClientForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    addQuickClient();
                });

                // Validation du formulaire
                document.getElementById('smartBillForm').addEventListener('submit', function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                    }
                });

                // Sauvegarde brouillon
                document.getElementById('saveDraftBtn').addEventListener('click', function() {
                    saveDraft();
                });

                // Raccourcis clavier
                document.addEventListener('keydown', handleKeyboardShortcuts);
            }

            // Gestion des raccourcis clavier
            function handleKeyboardShortcuts(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 's':
                            e.preventDefault();
                            if (e.shiftKey) {
                                saveDraft();
                            } else {
                                document.getElementById('smartBillForm').submit();
                            }
                            break;
                        case 'p':
                            e.preventDefault();
                            openProductModal();
                            break;
                        case 'f':
                            e.preventDefault();
                            document.getElementById('client_search').focus();
                            break;
                    }
                }
            }

            // Recherche de clients avec intelligence
            function searchClients() {
                const query = document.getElementById('client_search').value.trim();
                const loader = document.getElementById('clientSearchLoader');
                const results = document.getElementById('clientSearchResults');

                if (query.length < 2) {
                    results.classList.add('hidden');
                    return;
                }

                // Afficher le loader
                loader.classList.remove('hidden');

                // Filtrer les clients
                const filteredClients = clients.filter(client => {
                    const nameMatch = client.name.toLowerCase().includes(query.toLowerCase());
                    const emailMatch = client.email && client.email.toLowerCase().includes(query.toLowerCase());
                    const phoneMatch = client.phones && client.phones.some(phone =>
                        phone.number.toLowerCase().includes(query.toLowerCase())
                    );
                    return nameMatch || emailMatch || phoneMatch;
                });

                // Simuler un délai de recherche pour l'effet
                setTimeout(() => {
                    displayClientSuggestions(filteredClients);
                    loader.classList.add('hidden');
                }, 200);
            }

            function displayClientSuggestions(clientsList) {
                const results = document.getElementById('clientSearchResults');
                results.innerHTML = '';

                if (clientsList.length === 0) {
                    results.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-gray-500 text-sm">{{ __('Aucun client trouvé') }}</p>
                            <button type="button" onclick="toggleNewClientModal()" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                {{ __('Créer un nouveau client') }}
                            </button>
                        </div>
                    `;
                } else {
                    clientsList.forEach((client, index) => {
                        const div = document.createElement('div');
                        div.className = `suggestion-item p-3 cursor-pointer border-b border-gray-100 hover:bg-gray-50 ${index === 0 ? 'highlighted' : ''}`;
                        
                        // Calculer les statistiques du client
                        const totalOrders = client.total_orders || 0;
                        const totalAmount = client.total_amount || 0;
                        const avgOrder = totalOrders > 0 ? totalAmount / totalOrders : 0;
                        const lastOrder = client.last_order || null;
                        
                        const phoneNumbers = client.phones && client.phones.length > 0
                            ? client.phones.map(p => p.number).join(', ')
                            : 'Pas de téléphone';
                        
                        div.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold">${client.name.split(' ').map(n => n[0]).join('')}</span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="font-medium text-gray-900">${client.name}</h4>
                                        <p class="text-sm text-gray-600">${phoneNumbers}</p>
                                        ${totalOrders > 0 ? `<p class="text-xs text-gray-500">${totalOrders} commandes • Moy. ${formatCurrency(avgOrder)}</p>` : ''}
                                    </div>
                                </div>
                                ${totalAmount > 0 ? `
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">${formatCurrency(totalAmount)}</div>
                                    ${lastOrder ? `<div class="text-xs text-gray-500">${formatDate(lastOrder)}</div>` : ''}
                                </div>
                                ` : ''}
                            </div>
                        `;

                        div.addEventListener('click', () => selectClient(client));
                        results.appendChild(div);
                    });
                }

                results.classList.remove('hidden');
            }

            function selectClient(client) {
                selectedClient = client;
                hasClientSelected = true;

                // Mettre à jour l'affichage
                document.getElementById('client_id').value = client.id;
                document.getElementById('client_search').value = '';
                document.getElementById('clientSearchResults').classList.add('hidden');
                
                const selectedDiv = document.getElementById('selectedClientCard');
                document.getElementById('clientInitials').textContent = client.name.split(' ').map(n => n[0]).join('');
                document.getElementById('selectedClientName').textContent = client.name;
                
                const phoneNumbers = client.phones && client.phones.length > 0
                    ? client.phones.map(p => p.number).join(', ')
                    : 'Pas de téléphone';
                document.getElementById('selectedClientDetails').textContent = `${phoneNumbers} • ${client.email || 'Pas d\'email'}`;
                
                // Afficher les statistiques si disponibles
                const totalOrders = client.total_orders || 0;
                const totalAmount = client.total_amount || 0;
                if (totalOrders > 0) {
                    const avgOrder = totalAmount / totalOrders;
                    document.getElementById('clientStats').textContent = 
                        `${totalOrders} commandes • Moyenne ${formatCurrency(avgOrder)} • Total ${formatCurrency(totalAmount)}`;
                }
                
                selectedDiv.classList.remove('hidden');
                selectedDiv.classList.add('animate-fadeIn');

                // Cacher le champ de recherche
                document.getElementById('client_search').classList.add('hidden');

                // Mettre à jour le récapitulatif
                updateSummary();
                updateProgressBar();

                // Analyser le client et afficher des alertes
                analyzeClientAndShowAlerts(client);

                showToast(`{{ __('Client sélectionné:') }} ${client.name}`, 'success');
            }

            function clearSelectedClient() {
                selectedClient = null;
                hasClientSelected = false;
                document.getElementById('client_id').value = '';
                document.getElementById('selectedClientCard').classList.add('hidden');
                document.getElementById('client_search').classList.remove('hidden');
                document.getElementById('client_search').focus();
                updateSummary();
                updateProgressBar();
                clearSmartAlerts();
            }

            // Nouveau client
            function toggleNewClientModal() {
                const modal = document.getElementById('newClientModal');
                if (modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    document.getElementById('quickClientForm').reset();
                    setTimeout(() => {
                        document.getElementById('quick_client_name').focus();
                    }, 100);
                } else {
                    modal.classList.add('hidden');
                }
            }

            function addQuickClient() {
                const name = document.getElementById('quick_client_name').value;
                const email = document.getElementById('quick_client_email').value;
                const phone = document.getElementById('quick_client_phone').value;
                const address = document.getElementById('quick_client_address').value;

                // Créer le client avec un appel AJAX
                fetch('{{ route("clients.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        phone: phone,
                        address: address,
                        quick_create: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ajouter le client à la liste locale
                        clients.push(data.client);
                        
                        // Sélectionner le nouveau client
                        selectClient(data.client);
                        
                        // Fermer le modal
                        toggleNewClientModal();
                        
                        showToast(`{{ __('Client créé avec succès:') }} ${name}`, 'success');
                    } else {
                        showToast(data.message || '{{ __("Erreur lors de la création du client") }}', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('{{ __("Erreur lors de la création du client") }}', 'error');
                });
            }

            // Recherche rapide de produits
            function quickProductSearch() {
                const query = document.getElementById('productQuickSearch').value.trim();
                const suggestions = document.getElementById('quickProductSuggestions');

                if (query.length < 2) {
                    suggestions.classList.add('hidden');
                    return;
                }

                const filteredProducts = products.filter(product => {
                    return product.name.toLowerCase().includes(query.toLowerCase()) ||
                           (product.reference && product.reference.toLowerCase().includes(query.toLowerCase())) ||
                           (product.barcode && product.barcode.includes(query));
                });

                if (filteredProducts.length === 0) {
                    suggestions.classList.add('hidden');
                    return;
                }

                suggestions.innerHTML = '';
                filteredProducts.slice(0, 5).forEach(product => {
                    const div = document.createElement('div');
                    div.className = `p-2 cursor-pointer hover:bg-gray-50 border-b border-gray-100 ${getStockClass(product)}`;
                    
                    const stockInfo = getStockInfo(product);
                    div.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium">${product.name}</span>
                                <span class="ml-2 text-sm text-gray-500">${formatCurrency(product.default_price || product.price)}</span>
                            </div>
                            <div class="text-sm ${stockInfo.class}">
                                ${stockInfo.text}
                            </div>
                        </div>
                    `;

                    if (product.type === 'physical' && (product.stock_quantity === 0 || product.stock === 0)) {
                        div.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        div.addEventListener('click', () => addProductQuickly(product));
                    }

                    suggestions.appendChild(div);
                });

                suggestions.classList.remove('hidden');
            }

            function handleProductQuickSearchKeydown(e) {
                const suggestions = document.getElementById('quickProductSuggestions');
                if (e.key === 'Enter' && !suggestions.classList.contains('hidden')) {
                    const firstSuggestion = suggestions.querySelector('div');
                    if (firstSuggestion && !firstSuggestion.classList.contains('cursor-not-allowed')) {
                        firstSuggestion.click();
                    }
                }
            }

            function addProductQuickly(product) {
                const stockQuantity = product.stock_quantity || product.stock || 0;
                
                if (product.type === 'physical' && stockQuantity === 0) {
                    showToast('{{ __("Ce produit est en rupture de stock") }}', 'error');
                    return;
                }

                // Vérifier si le produit est déjà dans la facture
                const existingProduct = invoiceProducts.find(p => p.id === product.id);
                if (existingProduct) {
                    // Vérifier le stock disponible
                    if (product.type === 'physical' && existingProduct.quantity >= stockQuantity) {
                        showToast(`{{ __("Stock insuffisant. Stock disponible:") }} ${stockQuantity}`, 'error');
                        return;
                    }
                    existingProduct.quantity += 1;
                    updateProductRow(existingProduct);
                } else {
                    const invoiceProduct = {
                        ...product,
                        quantity: 1,
                        unitPrice: product.default_price || product.price,
                        stock: stockQuantity
                    };
                    invoiceProducts.push(invoiceProduct);
                    addProductRow(invoiceProduct);
                }

                // Nettoyer la recherche
                document.getElementById('productQuickSearch').value = '';
                document.getElementById('quickProductSuggestions').classList.add('hidden');

                hasProductsAdded = true;
                updateSummary();
                updateProgressBar();
                analyzeProductsAndShowAlerts();
            }

            // Modal de sélection de produits
            function openProductModal() {
                selectedProductsInModal = [];
                document.getElementById('productModal').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('modalProductSearch').focus();
                }, 100);
                renderProductsInModal();
            }

            function closeProductModal() {
                document.getElementById('productModal').classList.add('hidden');
                selectedProductsInModal = [];
            }

            function renderProductsInModal() {
                const grid = document.getElementById('productGrid');
                const searchTerm = document.getElementById('modalProductSearch').value.toLowerCase();
                const typeFilter = document.getElementById('productTypeFilter').value;
                const stockFilter = document.getElementById('stockFilter').value;

                let filteredProducts = products.filter(product => {
                    const stockQuantity = product.stock_quantity || product.stock || 0;
                    
                    // Filtre de recherche
                    const matchesSearch = product.name.toLowerCase().includes(searchTerm) ||
                                        (product.reference && product.reference.toLowerCase().includes(searchTerm));
                    
                    // Filtre de type
                    const matchesType = !typeFilter || product.type === typeFilter;
                    
                    // Filtre de stock
                    let matchesStock = true;
                    if (stockFilter === 'available' && product.type === 'physical') {
                        matchesStock = stockQuantity > 0;
                    } else if (stockFilter === 'low' && product.type === 'physical') {
                        const lowThreshold = product.low_stock_threshold || 5;
                        matchesStock = stockQuantity > 0 && stockQuantity <= lowThreshold;
                    } else if (stockFilter === 'out' && product.type === 'physical') {
                        matchesStock = stockQuantity === 0;
                    }

                    return matchesSearch && matchesType && matchesStock;
                });

                grid.innerHTML = '';

                if (filteredProducts.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-8 text-center text-gray-500">{{ __("Aucun produit trouvé") }}</div>';
                    return;
                }

                filteredProducts.forEach(product => {
                    const card = createProductCard(product);
                    grid.appendChild(card);
                });

                updateSelectedCount();
            }

            function createProductCard(product) {
                const isSelected = selectedProductsInModal.includes(product.id);
                const stockQuantity = product.stock_quantity || product.stock || 0;
                const isOutOfStock = product.type === 'physical' && stockQuantity === 0;
                const stockInfo = getStockInfo(product);

                const card = document.createElement('div');
                card.className = `product-card p-4 border rounded-lg cursor-pointer ${isSelected ? 'selected' : ''} ${isOutOfStock ? 'disabled' : ''}`;
                card.dataset.productId = product.id;

                card.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-medium text-gray-900">${product.name}</h3>
                        <span class="px-2 py-1 text-xs rounded-full ${product.type === 'physical' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">
                            ${product.type === 'physical' ? '{{ __("Produit") }}' : '{{ __("Service") }}'}
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-3">${product.description || '{{ __("Aucune description") }}'}</p>
                    
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-semibold text-indigo-600">${formatCurrency(product.default_price || product.price)}</span>
                        ${product.type === 'physical' ? `
                            <div class="flex items-center text-sm">
                                <span class="stock-indicator ${stockInfo.level}"></span>
                                <span class="${stockInfo.class}">${stockInfo.text}</span>
                            </div>
                        ` : ''}
                    </div>
                    
                    ${isSelected ? `
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Quantité") }}</label>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="quantity-btn minus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center" 
                                        ${isOutOfStock ? 'disabled' : ''}>-</button>
                                <input type="number" class="quantity-input w-16 text-center border border-gray-300 rounded" 
                                       value="1" min="1" ${product.type === 'physical' ? `max="${stockQuantity}"` : ''} 
                                       ${isOutOfStock ? 'disabled' : ''}>
                                <button type="button" class="quantity-btn plus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center"
                                        ${isOutOfStock ? 'disabled' : ''}>+</button>
                            </div>
                            ${product.type === 'physical' && stockQuantity > 0 ? `
                                <p class="text-xs text-gray-500 mt-1">{{ __("Max:") }} ${stockQuantity}</p>
                            ` : ''}
                        </div>
                    ` : ''}
                `;

                // Event listeners
                if (!isOutOfStock) {
                    card.addEventListener('click', (e) => {
                        if (e.target.closest('.quantity-btn') || e.target.classList.contains('quantity-input')) {
                            return;
                        }
                        toggleProductSelection(product.id, card);
                    });

                    setupQuantityControlsInCard(card, product);
                }

                return card;
            }

            function setupQuantityControlsInCard(card, product) {
                const minusBtn = card.querySelector('.minus');
                const plusBtn = card.querySelector('.plus');
                const quantityInput = card.querySelector('.quantity-input');

                if (minusBtn) {
                    minusBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const input = card.querySelector('.quantity-input');
                        const currentValue = parseInt(input.value);
                        if (currentValue > 1) {
                            input.value = currentValue - 1;
                        }
                    });
                }

                if (plusBtn) {
                    plusBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const input = card.querySelector('.quantity-input');
                        const currentValue = parseInt(input.value);
                        const stockQuantity = product.stock_quantity || product.stock || 0;
                        const maxValue = product.type === 'physical' ? stockQuantity : Infinity;
                        if (currentValue < maxValue) {
                            input.value = currentValue + 1;
                        } else {
                            showToast(`{{ __("Stock maximum atteint:") }} ${stockQuantity}`, 'warning');
                        }
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', (e) => {
                        const value = parseInt(e.target.value) || 1;
                        const stockQuantity = product.stock_quantity || product.stock || 0;
                        const maxValue = product.type === 'physical' ? stockQuantity : Infinity;
                        
                        if (value > maxValue) {
                            e.target.value = maxValue;
                            showToast(`{{ __("Quantité maximale:") }} ${maxValue}`, 'warning');
                        } else if (value < 1) {
                            e.target.value = 1;
                        }
                    });
                }
            }

            function toggleProductSelection(productId, cardElement) {
                const index = selectedProductsInModal.indexOf(productId);
                
                if (index > -1) {
                    // Désélectionner
                    selectedProductsInModal.splice(index, 1);
                    cardElement.classList.remove('selected');
                    // Supprimer les contrôles de quantité
                    const quantityDiv = cardElement.querySelector('.mt-3');
                    if (quantityDiv) quantityDiv.remove();
                } else {
                    // Sélectionner
                    selectedProductsInModal.push(productId);
                    cardElement.classList.add('selected');
                    // Ajouter les contrôles de quantité
                    addQuantityControlsToCard(cardElement, productId);
                }
                
                updateSelectedCount();
            }

            function addQuantityControlsToCard(cardElement, productId) {
                const product = products.find(p => p.id === productId);
                const stockQuantity = product.stock_quantity || product.stock || 0;
                const isOutOfStock = product.type === 'physical' && stockQuantity === 0;
                
                const quantityDiv = document.createElement('div');
                quantityDiv.className = 'mt-3';
                quantityDiv.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Quantité") }}</label>
                    <div class="flex items-center space-x-2">
                        <button type="button" class="quantity-btn minus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center" 
                                ${isOutOfStock ? 'disabled' : ''}>-</button>
                        <input type="number" class="quantity-input w-16 text-center border border-gray-300 rounded" 
                               value="1" min="1" ${product.type === 'physical' ? `max="${stockQuantity}"` : ''} 
                               ${isOutOfStock ? 'disabled' : ''}>
                        <button type="button" class="quantity-btn plus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center"
                                ${isOutOfStock ? 'disabled' : ''}>+</button>
                    </div>
                    ${product.type === 'physical' && stockQuantity > 0 ? `
                        <p class="text-xs text-gray-500 mt-1">{{ __("Max:") }} ${stockQuantity}</p>
                    ` : ''}
                `;
                
                cardElement.appendChild(quantityDiv);
                setupQuantityControlsInCard(quantityDiv, product);
            }

            function updateSelectedCount() {
                document.getElementById('selectedCount').textContent = 
                    `${selectedProductsInModal.length} {{ __("produit(s) sélectionné(s)") }}`;
            }

            function filterProductsInModal() {
                renderProductsInModal();
            }

            function addSelectedProductsToInvoice() {
                if (selectedProductsInModal.length === 0) {
                    showToast('{{ __("Veuillez sélectionner au moins un produit") }}', 'warning');
                    return;
                }

                selectedProductsInModal.forEach(productId => {
                    const product = products.find(p => p.id === productId);
                    const card = document.querySelector(`[data-product-id="${productId}"]`);
                    const quantityInput = card.querySelector('.quantity-input');
                    const quantity = parseInt(quantityInput?.value) || 1;
                    const stockQuantity = product.stock_quantity || product.stock || 0;

                    // Vérifier si le produit est déjà dans la facture
                    const existingProduct = invoiceProducts.find(p => p.id === productId);
                    if (existingProduct) {
                        // Vérifier le stock disponible
                        if (product.type === 'physical') {
                            const newQuantity = existingProduct.quantity + quantity;
                            if (newQuantity > stockQuantity) {
                                showToast(`{{ __("Stock insuffisant pour") }} ${product.name}. {{ __("Stock disponible:") }} ${stockQuantity}`, 'error');
                                return;
                            }
                        }
                        existingProduct.quantity += quantity;
                        updateProductRow(existingProduct);
                    } else {
                        const invoiceProduct = {
                            ...product,
                            quantity: quantity,
                            unitPrice: product.default_price || product.price,
                            stock: stockQuantity
                        };
                        invoiceProducts.push(invoiceProduct);
                        addProductRow(invoiceProduct);
                    }
                });

                closeProductModal();
                hasProductsAdded = true;
                updateSummary();
                updateProgressBar();
                analyzeProductsAndShowAlerts();
                showToast(`${selectedProductsInModal.length} {{ __("produit(s) ajouté(s) à la facture") }}`, 'success');
            }

            function addProductRow(product) {
                const tableBody = document.getElementById('productsContainer');
                const row = document.createElement('tr');
                row.dataset.productId = product.id;
                row.className = 'animate-fadeIn';

                const stockInfo = getStockInfo(product);
                
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <span class="text-gray-600 font-medium">${product.name.charAt(0)}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${product.name}</div>
                                <div class="text-sm text-gray-500">${product.type === 'physical' ? '{{ __("Produit") }}' : '{{ __("Service") }}'}</div>
                            </div>
                        </div>
                        <input type="hidden" name="products[${invoiceProducts.length - 1}][id]" value="${product.id}">
                    </td>
                    <td class="px-6 py-4 text-sm">
                        ${product.type === 'physical' ? `
                            <div class="flex items-center">
                                <span class="stock-indicator ${stockInfo.level}"></span>
                                <span class="${stockInfo.class}">${stockInfo.text}</span>
                            </div>
                        ` : '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <button type="button" class="quantity-decrease w-8 h-8 rounded bg-gray-200 flex items-center justify-center hover:bg-gray-300">-</button>
                            <input type="number" class="quantity-input w-16 text-center border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500" 
                                   name="products[${invoiceProducts.length - 1}][quantity]"
                                   value="${product.quantity}" min="1" ${product.type === 'physical' ? `max="${product.stock}"` : ''}>
                            <button type="button" class="quantity-increase w-8 h-8 rounded bg-gray-200 flex items-center justify-center hover:bg-gray-300">+</button>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <input type="number" class="price-input w-32 border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500" 
                               name="products[${invoiceProducts.length - 1}][price]"
                               value="${product.unitPrice}" min="0" step="0.01">
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 line-total">
                        ${formatCurrency(product.quantity * product.unitPrice)}
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" class="remove-product text-red-600 hover:text-red-900">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                `;

                // Event listeners pour cette ligne
                setupProductRowEvents(row, product);

                tableBody.appendChild(row);
                
                // Afficher le tableau s'il était caché
                checkEmptyProductsState();
            }

            function setupProductRowEvents(row, product) {
                const quantityInput = row.querySelector('.quantity-input');
                const priceInput = row.querySelector('.price-input');
                const decreaseBtn = row.querySelector('.quantity-decrease');
                const increaseBtn = row.querySelector('.quantity-increase');
                const removeBtn = row.querySelector('.remove-product');

                // Gestion de la quantité
                quantityInput.addEventListener('input', (e) => {
                    const newQuantity = parseInt(e.target.value) || 1;
                    
                    // Validation du stock pour les produits physiques
                    if (product.type === 'physical' && newQuantity > product.stock) {
                        e.target.value = product.stock;
                        showToast(`{{ __("Stock maximum atteint:") }} ${product.stock}`, 'warning');
                        product.quantity = product.stock;
                    } else if (newQuantity < 1) {
                        e.target.value = 1;
                        product.quantity = 1;
                    } else {
                        product.quantity = newQuantity;
                    }
                    
                    updateLineTotal(row, product);
                    updateSummary();
                    analyzeProductsAndShowAlerts();
                });

                decreaseBtn.addEventListener('click', () => {
                    if (product.quantity > 1) {
                        product.quantity--;
                        quantityInput.value = product.quantity;
                        updateLineTotal(row, product);
                        updateSummary();
                        analyzeProductsAndShowAlerts();
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    if (product.type === 'physical' && product.quantity >= product.stock) {
                        showToast(`{{ __("Stock maximum atteint:") }} ${product.stock}`, 'warning');
                        return;
                    }
                    product.quantity++;
                    quantityInput.value = product.quantity;
                    updateLineTotal(row, product);
                    updateSummary();
                    analyzeProductsAndShowAlerts();
                });

                // Gestion du prix
                priceInput.addEventListener('input', (e) => {
                    const newPrice = parseFloat(e.target.value) || 0;
                    product.unitPrice = newPrice;
                    updateLineTotal(row, product);
                    updateSummary();
                });

                // Suppression du produit
                removeBtn.addEventListener('click', () => {
                    removeProductFromInvoice(product.id, row);
                });
            }

            function updateLineTotal(row, product) {
                const total = product.quantity * product.unitPrice;
                row.querySelector('.line-total').textContent = formatCurrency(total);
            }

            function updateProductRow(product) {
                const row = document.querySelector(`tr[data-product-id="${product.id}"]`);
                if (row) {
                    row.querySelector('.quantity-input').value = product.quantity;
                    updateLineTotal(row, product);
                }
            }

            function removeProductFromInvoice(productId, row) {
                // Animation de suppression
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';

                setTimeout(() => {
                    row.remove();
                    
                    // Supprimer du tableau des produits
                    const index = invoiceProducts.findIndex(p => p.id === productId);
                    if (index > -1) {
                        invoiceProducts.splice(index, 1);
                    }

                    // Vérifier s'il faut afficher l'état vide
                    hasProductsAdded = invoiceProducts.length > 0;
                    checkEmptyProductsState();
                    updateSummary();
                    updateProgressBar();
                    analyzeProductsAndShowAlerts();
                }, 300);
            }

            // Fonctions utilitaires pour le stock
            function getStockInfo(product) {
                if (product.type === 'service') {
                    return { text: '{{ __("Service") }}', class: 'text-gray-500', level: 'high' };
                }

                const stockQuantity = product.stock_quantity || product.stock || 0;
                const lowThreshold = product.low_stock_threshold || 5;

                if (stockQuantity === 0) {
                    return { text: '{{ __("Rupture") }}', class: 'text-red-600', level: 'out' };
                } else if (stockQuantity <= lowThreshold) {
                    return { text: `${stockQuantity} {{ __("restant") }}`, class: 'text-orange-600', level: 'low' };
                } else if (stockQuantity <= lowThreshold * 2) {
                    return { text: `${stockQuantity} {{ __("en stock") }}`, class: 'text-yellow-600', level: 'medium' };
                } else {
                    return { text: `${stockQuantity} {{ __("en stock") }}`, class: 'text-green-600', level: 'high' };
                }
            }

            function getStockClass(product) {
                if (product.type === 'service') return '';
                
                const stockQuantity = product.stock_quantity || product.stock || 0;
                const lowThreshold = product.low_stock_threshold || 5;
                
                if (stockQuantity === 0) return 'stock-out';
                if (stockQuantity <= lowThreshold) return 'stock-low';
                if (stockQuantity <= lowThreshold * 2) return 'stock-medium';
                return 'stock-high';
            }

            // Vérifier l'état vide des produits
            function checkEmptyProductsState() {
                const emptyState = document.getElementById('emptyProductsState');
                const tableContainer = document.getElementById('productsTableContainer');

                if (invoiceProducts.length === 0) {
                    emptyState.classList.remove('hidden');
                    tableContainer.classList.add('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    tableContainer.classList.remove('hidden');
                }
            }

            // Mise à jour du récapitulatif
            function updateSummary() {
                const subtotal = invoiceProducts.reduce((sum, product) => sum + (product.quantity * product.unitPrice), 0);
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = subtotal * (taxRate / 100);
                const total = subtotal + taxAmount;

                document.getElementById('summaryClientName').textContent = selectedClient ? selectedClient.name : '-';
                document.getElementById('summaryItems').textContent = invoiceProducts.reduce((sum, product) => sum + product.quantity, 0);
                document.getElementById('summarySubtotal').textContent = formatCurrency(subtotal);
                document.getElementById('summaryTaxAmount').textContent = formatCurrency(taxAmount);
                document.getElementById('summaryTotal').textContent = formatCurrency(total);

                // Activer/désactiver le bouton de soumission
                const canSubmit = hasClientSelected && hasProductsAdded;
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = !canSubmit;
                
                if (canSubmit) {
                    submitBtn.classList.remove('opacity-50');
                } else {
                    submitBtn.classList.add('opacity-50');
                }
            }

            // Barre de progression intelligente
            function updateProgressBar() {
                let progress = 25; // Base

                if (hasClientSelected) progress += 25;
                if (hasProductsAdded) progress += 25;
                if (hasClientSelected && hasProductsAdded) progress += 25;

                document.getElementById('progressBar').style.width = `${progress}%`;
                document.getElementById('progressPercentage').textContent = `${progress}%`;

                // Mettre à jour les étapes
                updateStepStatus('step2', hasClientSelected);
                updateStepStatus('step3', hasProductsAdded);
                updateStepStatus('step4', progress === 100);
            }

            function updateStepStatus(stepId, completed) {
                const step = document.getElementById(stepId);
                const circle = step.querySelector('div');
                const text = step.querySelector('span');

                if (completed) {
                    circle.className = 'w-3 h-3 rounded-full bg-green-500 mr-2';
                    text.className = 'text-green-700';
                } else {
                    circle.className = 'w-3 h-3 rounded-full bg-gray-300 mr-2';
                    text.className = 'text-gray-500';
                }
            }

            // Alertes intelligentes
            function analyzeClientAndShowAlerts(client) {
                const alerts = [];
                
                const totalAmount = client.total_amount || 0;
                const totalOrders = client.total_orders || 0;
                const lastOrder = client.last_order;
                
                // Client VIP
                if (totalAmount > 3000000) {
                    alerts.push({
                        type: 'info',
                        message: '👑 {{ __("Client VIP - Traitement prioritaire") }}',
                        color: 'bg-purple-50 border-purple-200 text-purple-800'
                    });
                }

                // Client inactif
                if (lastOrder) {
                    const daysSinceLastOrder = Math.floor((new Date() - new Date(lastOrder)) / (1000 * 60 * 60 * 24));
                    if (daysSinceLastOrder > 30) {
                        alerts.push({
                            type: 'warning',
                            message: `⚠️ {{ __("Client inactif depuis") }} ${daysSinceLastOrder} {{ __("jours") }}`,
                            color: 'bg-yellow-50 border-yellow-200 text-yellow-800'
                        });
                    }
                }

                // Historique de commandes
                if (totalOrders > 20) {
                    alerts.push({
                        type: 'success',
                        message: '✨ {{ __("Client fidèle - Considérer une remise") }}',
                        color: 'bg-green-50 border-green-200 text-green-800'
                    });
                }

                displaySmartAlerts(alerts);
            }

            function analyzeProductsAndShowAlerts() {
                const alerts = [];

                // Vérifier les stocks faibles
                const lowStockProducts = invoiceProducts.filter(p => {
                    const stockQuantity = p.stock_quantity || p.stock || 0;
                    const lowThreshold = p.low_stock_threshold || 5;
                    return p.type === 'physical' && stockQuantity <= lowThreshold && stockQuantity > 0;
                });

                if (lowStockProducts.length > 0) {
                    alerts.push({
                        type: 'warning',
                        message: `📦 ${lowStockProducts.length} {{ __("produit(s) en stock faible") }}`,
                        color: 'bg-orange-50 border-orange-200 text-orange-800'
                    });
                }

                // Calculer la marge (simulation)
                const totalCost = invoiceProducts.reduce((sum, p) => sum + (p.quantity * p.unitPrice * 0.7), 0); // Simulation coût à 70% du prix
                const totalRevenue = invoiceProducts.reduce((sum, p) => sum + (p.quantity * p.unitPrice), 0);
                
                if (totalRevenue > 0) {
                    const margin = ((totalRevenue - totalCost) / totalRevenue) * 100;

                    if (margin < 20) {
                        alerts.push({
                            type: 'error',
                            message: '💰 {{ __("Marge faible détectée") }} (' + margin.toFixed(1) + '%)',
                            color: 'bg-red-50 border-red-200 text-red-800'
                        });
                    } else if (margin > 50) {
                        alerts.push({
                            type: 'success',
                            message: '💎 {{ __("Excellente marge") }} (' + margin.toFixed(1) + '%)',
                            color: 'bg-green-50 border-green-200 text-green-800'
                        });
                    }
                }

                displaySmartAlerts(alerts);
            }

            function displaySmartAlerts(alerts) {
                const container = document.getElementById('smartAlerts');
                container.innerHTML = '';

                alerts.forEach(alert => {
                    const div = document.createElement('div');
                    div.className = `p-3 rounded-md border text-sm ${alert.color}`;
                    div.textContent = alert.message;
                    container.appendChild(div);
                });
            }

            function clearSmartAlerts() {
                document.getElementById('smartAlerts').innerHTML = '';
            }

            // Validation du formulaire
            function validateForm() {
                let isValid = true;

                // Vérifier si un client est sélectionné
                if (!hasClientSelected) {
                    showToast('{{ __("Veuillez sélectionner un client") }}', 'error');
                    document.getElementById('client_search').focus();
                    isValid = false;
                }

                // Vérifier si des produits sont ajoutés
                if (!hasProductsAdded) {
                    showToast('{{ __("Veuillez ajouter au moins un produit") }}', 'error');
                    isValid = false;
                }

                // Validation finale des stocks
                const invalidProducts = invoiceProducts.filter(p => {
                    const stockQuantity = p.stock_quantity || p.stock || 0;
                    return p.type === 'physical' && p.quantity > stockQuantity;
                });

                if (invalidProducts.length > 0) {
                    showToast('{{ __("Stock insuffisant pour certains produits") }}', 'error');
                    isValid = false;
                }

                return isValid;
            }

            // Sauvegarde brouillon
            function saveDraft() {
                const formData = new FormData(document.getElementById('smartBillForm'));
                
                // Ajouter les produits au FormData
                invoiceProducts.forEach((product, index) => {
                    formData.append(`products[${index}][id]`, product.id);
                    formData.append(`products[${index}][quantity]`, product.quantity);
                    formData.append(`products[${index}][price]`, product.unitPrice);
                });
                
                formData.append('is_draft', '1');

                fetch('{{ route("bills.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('💾 {{ __("Brouillon sauvegardé avec succès") }}', 'success');
                    } else {
                        showToast(data.message || '{{ __("Erreur lors de la sauvegarde") }}', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showToast('{{ __("Erreur lors de la sauvegarde") }}', 'error');
                });
            }

            // Mise à jour de la liste des vendeurs en fonction de la boutique
            function updateVendorsList(shopId) {
                if (!shopId) return;

                fetch(`/api/shops/${shopId}/vendors`)
                    .then(response => response.json())
                    .then(data => {
                        const vendorSelect = document.getElementById('user_id');
                        const currentValue = vendorSelect.value;

                        vendorSelect.innerHTML = '<option value="">{{ __("Sélectionner un vendeur") }}</option>';

                        data.forEach(vendor => {
                            const option = document.createElement('option');
                            option.value = vendor.id;
                            option.textContent = vendor.name;
                            vendorSelect.appendChild(option);
                        });

                        if (currentValue && [...vendorSelect.options].find(opt => opt.value === currentValue)) {
                            vendorSelect.value = currentValue;
                        }
                    })
                    .catch(error => console.error('{{ __("Erreur lors de la récupération des vendeurs:") }}', error));
            }

            // Fonctions utilitaires
            function formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('fr-FR');
            }

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            function showToast(message, type = 'info', duration = 3000) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                
                const colors = {
                    success: 'bg-green-50 border-green-200 text-green-800',
                    error: 'bg-red-50 border-red-200 text-red-800',
                    warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    info: 'bg-blue-50 border-blue-200 text-blue-800'
                };

                toast.className = `p-4 rounded-md border shadow-lg transform transition-all duration-300 opacity-0 translate-y-2 ${colors[type]}`;
                
                // Ajouter une icône selon le type
                const icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                toast.innerHTML = `
                    <div class="flex items-center">
                        <span class="mr-2">${icons[type]}</span>
                        <span>${message}</span>
                    </div>
                `;

                container.appendChild(toast);

                // Animation d'entrée
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                    toast.classList.add('opacity-100', 'translate-y-0');
                }, 10);

                // Suppression automatique
                if (duration > 0) {
                    setTimeout(() => hideToast(toast), duration);
                }

                return toast;
            }

            function hideToast(toast) {
                if (toast && toast.parentNode) {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }

            // Exécuter au chargement de la page si une boutique est déjà sélectionnée
            document.addEventListener('DOMContentLoaded', function() {
                const shopSelect = document.getElementById('shop_id');
                if (shopSelect && shopSelect.value) {
                    updateVendorsList(shopSelect.value);
                }
            });

            // Suggestions intelligentes
            function generateSmartSuggestions() {
                const suggestions = [];
                const subtotal = invoiceProducts.reduce((sum, product) => sum + (product.quantity * product.unitPrice), 0);

                // Suggestions basées sur le montant
                if (subtotal > 500000) {
                    suggestions.push('💡 {{ __("Montant élevé - Proposer un paiement échelonné") }}');
                }

                if (subtotal < 50000) {
                    suggestions.push('🎯 {{ __("Petite commande - Proposer des produits complémentaires") }}');
                }

                // Suggestions basées sur les produits
                const hasPhysicalProducts = invoiceProducts.some(p => p.type === 'physical');
                const hasServices = invoiceProducts.some(p => p.type === 'service');

                if (hasPhysicalProducts && !hasServices) {
                    suggestions.push('🛠️ {{ __("Produits physiques uniquement - Proposer un service de maintenance") }}');
                }

                if (hasServices && !hasPhysicalProducts) {
                    suggestions.push('📦 {{ __("Services uniquement - Proposer des produits associés") }}');
                }

                // Suggestions basées sur le client
                if (selectedClient) {
                    const totalOrders = selectedClient.total_orders || 0;
                    if (totalOrders > 10) {
                        suggestions.push('🏆 {{ __("Client fidèle - Appliquer une remise de fidélité") }}');
                    }
                }

                displaySuggestions(suggestions);
            }

            function displaySuggestions(suggestions) {
                const container = document.getElementById('suggestionsContainer');
                container.innerHTML = '';

                if (suggestions.length === 0) {
                    container.innerHTML = '<p class="text-xs text-gray-500">{{ __("Aucune suggestion pour le moment") }}</p>';
                    return;
                }

                suggestions.forEach(suggestion => {
                    const div = document.createElement('div');
                    div.className = 'p-2 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800';
                    div.textContent = suggestion;
                    container.appendChild(div);
                });
            }

            // Mettre à jour les suggestions quand la facture change
            function updateSummaryWithSuggestions() {
                updateSummary();
                generateSmartSuggestions();
            }

            // Remplacer les appels updateSummary() par updateSummaryWithSuggestions() dans les événements pertinents
            document.addEventListener('DOMContentLoaded', function() {
                // Mettre à jour les event listeners pour inclure les suggestions
                const originalUpdateSummary = updateSummary;
                updateSummary = function() {
                    originalUpdateSummary();
                    generateSmartSuggestions();
                };
            });
        </script>
    @endpush

    {{-- Meta tag pour CSRF token --}}
    @section('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endsection

</x-app-layout>