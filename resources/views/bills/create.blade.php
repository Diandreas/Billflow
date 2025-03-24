<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Nouvelle Facture') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Créez et gérez vos factures professionnelles') }}
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
            <div class="mb-5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <!-- Progress bar -->
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2">
                        <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Client</span>
                        <span>Produits</span>
                        <span>Finalisation</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main form -->
                <div class="lg:col-span-2">
                    <form id="billForm" action="{{ route('bills.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Client Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Informations Client') }}
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
                                    <!-- Client Selection with enhanced search -->
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
                                            <input type="hidden" id="client_id" name="client_id" required>
                                            <div id="clientSearchResults" class="hidden absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md max-h-60 overflow-auto border border-gray-200"></div>
                                        </div>
                                    </div>

                                    <!-- Client info card (appears when client is selected) -->
                                    <div id="selectedClientCard" class="hidden mt-3 bg-gray-50 rounded-md p-4 border border-gray-200">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold"></div>
                                            <div class="ml-3 flex-1">
                                                <div class="text-sm font-medium text-gray-900" id="selectedClientName"></div>
                                                <div class="text-sm text-gray-500 mt-1" id="selectedClientDetails"></div>
                                            </div>
                                            <button type="button" id="changeClientBtn" class="text-sm text-indigo-600 hover:text-indigo-800">
                                                Changer
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Basic Info Row (date, tax, etc) -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                        <div>
                                            <label for="date" class="block mb-1 text-sm font-medium text-gray-700">
                                                {{ __('Date') }}
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <input type="date"
                                                    id="date"
                                                    name="date"
                                                    value="{{ old('date', date('Y-m-d')) }}"
                                                    class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="tax_rate" class="block mb-1 text-sm font-medium text-gray-700">
                                                {{ __('TVA (%)') }}
                                            </label>
                                            <div class="relative">
                                                <input type="number"
                                                    id="tax_rate"
                                                    name="tax_rate"
                                                    value="{{ old('tax_rate', 18) }}"
                                                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    onchange="calculateTotals()">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="bill_reference" class="block mb-1 text-sm font-medium text-gray-700">
                                                {{ __('Référence') }}
                                            </label>
                                            <input type="text"
                                                id="bill_reference"
                                                name="reference"
                                                value="{{ old('reference', 'FACT-' . date('Ymd') . '-' . rand(1000, 9999)) }}"
                                                class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ __('Produits et Services') }}
                                    </h3>
                                    <div class="flex space-x-2">
                                        <div class="relative" x-data="{ open: false }">
                                            <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Options') }}
                                            </button>
                                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-10">
                                                <div class="py-1">
                                                    <button type="button" onclick="applyDiscount()" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left">
                                                        <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a1 1 0 011-1h5a.997.997 0 01.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                        </svg>
                                                        {{ __('Appliquer une remise') }}
                                                    </button>
                                                </div>
                                                <div class="py-1">
                                                    <button type="button" onclick="importProducts()" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left">
                                                        <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                                            <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                                                        </svg>
                                                        {{ __('Importer depuis modèle') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="button"
                                                onclick="addProductRow()"
                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('Ajouter un Produit') }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Empty state when no products -->
                                <div id="emptyProductsState" class="hidden py-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucun produit') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par ajouter des produits à votre facture.') }}</p>
                                    <div class="mt-6">
                                        <button type="button"
                                                onclick="addProductRow()"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ __('Ajouter un produit') }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Products Table -->
                                <div id="productsTableContainer" class="overflow-x-auto">
                                    <table id="productsTable" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ __('Produit') }}
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

                                <!-- Add discount row if needed -->
                                <div id="discountRow" class="hidden mt-4 bg-yellow-50 rounded-md p-4 border border-yellow-200 animate-fadeIn">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-yellow-600 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a1 1 0 011-1h5a.997.997 0 01.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-yellow-800">Remise appliquée</span>
                                        </div>

                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <input type="number" 
                                                       id="discountPercent" 
                                                       name="discount_percent" 
                                                       min="0" 
                                                       max="100" 
                                                       value="0" 
                                                       class="w-16 rounded border-gray-300 text-yellow-800 bg-yellow-100 focus:border-yellow-500 focus:ring focus:ring-yellow-200"
                                                       onchange="calculateTotals()">
                                                <span class="ml-1 text-yellow-800">%</span>
                                            </div>
                                            <span class="text-yellow-600">ou</span>
                                            <div class="flex items-center">
                                                <input type="number" 
                                                       id="discountAmount" 
                                                       name="discount_amount" 
                                                       min="0" 
                                                       value="0" 
                                                       class="w-24 rounded border-gray-300 text-yellow-800 bg-yellow-100 focus:border-yellow-500 focus:ring focus:ring-yellow-200"
                                                       onchange="calculateTotals()">
                                                <span class="ml-1 text-yellow-800">FCFA</span>
                                            </div>
                                            <button type="button" onclick="removeDiscount()" class="text-yellow-600 hover:text-yellow-800">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        {{ __('Notes et informations supplémentaires') }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ __('Ces informations apparaîtront en bas de votre facture.') }}
                                    </p>
                                </div>
                                <div>
                                    <textarea id="notes"
                                              name="notes"
                                              rows="3"
                                              class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                              placeholder="Informations de paiement, conditions, etc.">{{ old('notes', 'Merci pour votre confiance! La facture est payable sous 30 jours.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-between items-center">
                            <button type="button" id="saveDraftBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                {{ __('Enregistrer comme brouillon') }}
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Créer la Facture') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Preview column -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ __('Récapitulatif') }}
                            </h3>
                            
                            <!-- Quick info card -->
                            <div class="bg-gray-50 rounded-md p-4 mb-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Client:</span>
                                        <span id="summaryClientName" class="text-sm font-medium">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Référence:</span>
                                        <span id="summaryReference" class="text-sm font-medium">{{ 'FACT-' . date('Ymd') . '-' . rand(1000, 9999) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Date:</span>
                                        <span id="summaryDate" class="text-sm font-medium">{{ date('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Articles:</span>
                                        <span id="summaryItems" class="text-sm font-medium">0</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Totals -->
                            <div>
                                <h4 class="font-medium text-sm text-gray-700 mb-2">Détails du montant</h4>
                                <div class="space-y-1 text-sm mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Sous-total:</span>
                                        <span id="summarySubtotal" class="font-medium">0 FCFA</span>
                                    </div>
                                    <div id="discountSummaryRow" class="flex justify-between hidden">
                                        <span class="text-gray-500">Remise:</span>
                                        <span id="summaryDiscount" class="font-medium text-yellow-600">0 FCFA</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">TVA (<span id="summaryTaxRate">18</span>%):</span>
                                        <span id="summaryTaxAmount" class="font-medium">0 FCFA</span>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total:</span>
                                        <span id="summaryTotal" class="text-xl font-bold text-indigo-600">0 FCFA</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Invoice progress -->
                            <div class="mt-6">
                                <h4 class="font-medium text-sm text-gray-700 mb-2">Progression</h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <span class="ml-2 text-sm font-medium text-gray-700">Informations de base</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center" id="clientStepStatus">
                                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                                                <span class="text-xs font-medium">2</span>
                                            </div>
                                            <span class="ml-2 text-sm font-medium text-gray-500">Client sélectionné</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center" id="productsStepStatus">
                                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                                                <span class="text-xs font-medium">3</span>
                                            </div>
                                            <span class="ml-2 text-sm font-medium text-gray-500">Produits ajoutés</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Client Quick Modal -->
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
                <div>
                    <label for="quick_client_name" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                    <input type="text" id="quick_client_name" name="name" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="quick_client_email" name="email" class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_phone" class="block text-sm font-medium text-gray-700">Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" id="quick_client_phone" name="phone" required class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="quick_client_address" class="block text-sm font-medium text-gray-700">Adresse</label>
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

    <!-- Product quick search modal -->
    <div id="productSearchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 transform transition-all">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Recherche Rapide de Produits') }}</h3>
                <button type="button" onclick="toggleProductSearchModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" id="quickProductSearch" autofocus placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div id="quickProductResults" class="max-h-60 overflow-y-auto">
                    <!-- Results will be populated here -->
                </div>
                <div class="mt-4 flex justify-between items-center pt-3 border-t">
                    <div>
                        <button type="button" id="addNewProductBtn" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            {{ __('+ Ajouter un nouveau produit') }}
                        </button>
                    </div>
                    <button type="button" onclick="toggleProductSearchModal()" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-200">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Animation de transition pour les éléments */
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-in-out;
            }
            
            .animate-scaleIn {
                animation: scaleIn 0.2s ease-out;
            }
            
            .animate-highlight {
                animation: highlight 1s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes scaleIn {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            
            @keyframes highlight {
                0% { background-color: rgba(79, 70, 229, 0.1); }
                100% { background-color: transparent; }
            }
            
            /* Styles pour les résultats de recherche */
            .search-result {
                transition: all 0.2s;
            }
            
            .search-result:hover {
                background-color: #F3F4F6;
            }
            
            /* Transition pour le hover des boutons */
            button {
                transition: all 0.2s;
            }
            
            /* Ajout d'une ombre intérieure sur focus pour les champs de recherche */
            input:focus {
                box-shadow: inset 0 0 0 2px rgba(79, 70, 229, 0.1);
            }
            
            /* Style pour les input number */
            input[type="number"] {
                -moz-appearance: textfield;
            }
            
            input[type="number"]::-webkit-outer-spin-button,
            input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Données des clients et produits
            const clients = @json($clients);
            const products = @json($products);
            let rowCounter = 0;
            
            // Variables pour stocker les état de la facture
            let hasClientSelected = false;
            let hasProductsAdded = false;
            
            // Initialisation
            document.addEventListener('DOMContentLoaded', function() {
                initClientSearch();
                updateProgressBar();
                checkEmptyProductsState();
                document.getElementById('summaryReference').textContent = document.getElementById('bill_reference').value;
                
                // Mise à jour dynamique de la référence
                document.getElementById('bill_reference').addEventListener('input', function() {
                    document.getElementById('summaryReference').textContent = this.value;
                });
                
                // Mise à jour de la date dans le récapitulatif
                document.getElementById('date').addEventListener('change', function() {
                    const dateObj = new Date(this.value);
                    const formattedDate = dateObj.toLocaleDateString('fr-FR');
                    document.getElementById('summaryDate').textContent = formattedDate;
                });
                
                // Mise à jour du taux de TVA
                document.getElementById('tax_rate').addEventListener('input', function() {
                    document.getElementById('summaryTaxRate').textContent = this.value;
                    calculateTotals();
                });
                
                // Sauvegarde automatique en brouillon
                let autoSaveTimeout;
                const formInputs = document.querySelectorAll('#billForm input, #billForm select, #billForm textarea');
                formInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        clearTimeout(autoSaveTimeout);
                        autoSaveTimeout = setTimeout(saveAsDraft, 3000);
                    });
                });
                
                // Gestionnaire pour le bouton de sauvegarde en brouillon
                document.getElementById('saveDraftBtn').addEventListener('click', function() {
                    saveAsDraft(true); // true indique que c'est une sauvegarde manuelle
                });
                
                // Soumission du formulaire avec validation
                document.getElementById('billForm').addEventListener('submit', function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                    }
                });
                
                // Gestion du formulaire de client rapide
                document.getElementById('quickClientForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    addQuickClient();
                });
                
                // Bouton pour changer de client
                document.getElementById('changeClientBtn').addEventListener('click', function() {
                    document.getElementById('selectedClientCard').classList.add('hidden');
                    document.getElementById('client_search').classList.remove('hidden');
                    document.getElementById('client_search').focus();
                });
                
                // Initialiser l'affichage des prix formatés
                const formatPrice = (price) => {
                    return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
                };
            });
            
            // Fonction pour initialiser la recherche de client
            function initClientSearch() {
                const searchInput = document.getElementById('client_search');
                const resultsContainer = document.getElementById('clientSearchResults');
                
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    if (searchTerm.length < 2) {
                        resultsContainer.classList.add('hidden');
                        return;
                    }
                    
                    const filteredClients = clients.filter(client => {
                        const nameMatch = client.name.toLowerCase().includes(searchTerm);
                        const emailMatch = client.email && client.email.toLowerCase().includes(searchTerm);
                        const phoneMatch = client.phones && client.phones.some(phone => 
                            phone.number.toLowerCase().includes(searchTerm)
                        );
                        
                        return nameMatch || emailMatch || phoneMatch;
                    });
                    
                    renderClientSearchResults(filteredClients);
                });
                
                // Cacher les résultats quand on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                        resultsContainer.classList.add('hidden');
                    }
                });
                
                // Affichage au focus
                searchInput.addEventListener('focus', function() {
                    if (this.value.length >= 2) {
                        resultsContainer.classList.remove('hidden');
                    }
                });
            }
            
            // Afficher les résultats de recherche de clients
            function renderClientSearchResults(clients) {
                const resultsContainer = document.getElementById('clientSearchResults');
                
                // Effacer les résultats précédents
                resultsContainer.innerHTML = '';
                
                if (clients.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-sm text-gray-500">Aucun client trouvé</p>
                            <button type="button" onclick="toggleNewClientModal()" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Ajouter un nouveau client
                            </button>
                        </div>
                    `;
                    resultsContainer.classList.remove('hidden');
                    return;
                }
                
                // Créer les résultats
                clients.forEach(client => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                    
                    const phoneNumbers = client.phones && client.phones.length > 0 
                        ? client.phones.map(p => p.number).join(', ') 
                        : 'Pas de téléphone';
                    
                    resultItem.innerHTML = `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                ${client.name.substring(0, 2).toUpperCase()}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${client.name}</p>
                                <p class="text-xs text-gray-500">${phoneNumbers}</p>
                            </div>
                        </div>
                    `;
                    
                    resultItem.addEventListener('click', function() {
                        selectClient(client);
                    });
                    
                    resultsContainer.appendChild(resultItem);
                });
                
                resultsContainer.classList.remove('hidden');
            }
            
            // Sélectionner un client
            function selectClient(client) {
                document.getElementById('client_id').value = client.id;
                
                // Mettre à jour l'affichage du client sélectionné
                const clientCard = document.getElementById('selectedClientCard');
                clientCard.classList.remove('hidden');
                clientCard.classList.add('animate-fadeIn');
                
                // Définir les infos du client
                const initialDiv = clientCard.querySelector('.flex-shrink-0');
                initialDiv.textContent = client.name.substring(0, 2).toUpperCase();
                
                document.getElementById('selectedClientName').textContent = client.name;
                
                const phoneNumbers = client.phones && client.phones.length > 0 
                    ? client.phones.map(p => p.number).join(', ') 
                    : 'Pas de téléphone';
                document.getElementById('selectedClientDetails').textContent = phoneNumbers;
                
                // Cacher l'input de recherche
                document.getElementById('client_search').value = '';
                document.getElementById('clientSearchResults').classList.add('hidden');
                document.getElementById('client_search').classList.add('hidden');
                
                // Mettre à jour le récapitulatif
                document.getElementById('summaryClientName').textContent = client.name;
                
                // Mettre à jour le statut du client
                hasClientSelected = true;
                updateClientStatus();
                updateProgressBar();
                
                // Animation de succès
                showToast('Client sélectionné: ' + client.name, 'success');
            }
            
            // Ajouter un client rapidement
            function addQuickClient() {
                const name = document.getElementById('quick_client_name').value;
                const email = document.getElementById('quick_client_email').value;
                const phone = document.getElementById('quick_client_phone').value;
                
                // Simuler l'ajout d'un client (normalement ce serait un appel AJAX)
                // Pour la démonstration, nous allons simplement créer un objet client et l'utiliser
                const newClient = {
                    id: 'new_' + Date.now(),
                    name: name,
                    email: email,
                    phones: [{ number: phone }]
                };
                
                // Sélectionner le nouveau client
                selectClient(newClient);
                
                // Fermer le modal
                toggleNewClientModal();
                
                // Animation de succès
                showToast('Client créé avec succès: ' + name, 'success');
            }
            
            // Ajouter une ligne de produit
            function addProductRow() {
                rowCounter++;
                const container = document.getElementById('productsContainer');
                const rowId = `product-row-${rowCounter}`;
                
                const row = document.createElement('tr');
                row.id = rowId;
                row.className = 'animate-fadeIn';
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <button type="button" onclick="openProductSearch('${rowId}')" class="w-full text-left flex items-center">
                            <div id="${rowId}-product-info" class="hidden">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900 product-name"></span>
                                    <span class="ml-2 text-xs text-gray-500 product-ref"></span>
                                </div>
                            </div>
                            <div id="${rowId}-product-placeholder" class="text-gray-500 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Sélectionner un produit
                            </div>
                        </button>
                        <input type="hidden" name="products[]" id="${rowId}-product-id" required>
                    </td>
                    <td class="px-6 py-4">
                        <input type="number"
                               name="quantities[]"
                               id="${rowId}-quantity"
                               value="1"
                               min="1"
                               required
                               class="quantity w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               onchange="calculateRowTotal('${rowId}')">
                    </td>
                    <td class="px-6 py-4">
                        <div class="relative">
                            <input type="number"
                                   name="prices[]"
                                   id="${rowId}-price"
                                   class="price w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   required
                                   step="100"
                                   min="0"
                                   onchange="calculateRowTotal('${rowId}')">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">FCFA</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span id="${rowId}-total" class="row-total font-medium">0 FCFA</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button type="button"
                                onclick="removeRow('${rowId}')"
                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                `;
                
                container.appendChild(row);
                
                // Cacher l'état vide
                checkEmptyProductsState();
                
                // Ouvrir la recherche de produit immédiatement
                openProductSearch(rowId);
                
                return rowId;
            }
            
            // Ouvrir la recherche rapide de produit
            function openProductSearch(rowId) {
                // Stocker l'ID de la ligne courante
                window.currentRowId = rowId;
                
                // Afficher le modal
                toggleProductSearchModal();
                
                // Se concentrer sur le champ de recherche
                setTimeout(() => {
                    const searchInput = document.getElementById('quickProductSearch');
                    searchInput.value = '';
                    searchInput.focus();
                    
                    // Montrer tous les produits
                    renderQuickProductResults(products);
                    
                    // Configurer l'événement de recherche
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        
                        if (searchTerm.length === 0) {
                            renderQuickProductResults(products);
                            return;
                        }
                        
                        const filteredProducts = products.filter(product => {
                            const nameMatch = product.name.toLowerCase().includes(searchTerm);
                            const refMatch = product.reference && product.reference.toLowerCase().includes(searchTerm);
                            
                            return nameMatch || refMatch;
                        });
                        
                        renderQuickProductResults(filteredProducts);
                    });
                }, 100);
            }
            
            // Afficher les résultats de recherche de produits
            function renderQuickProductResults(products) {
                const resultsContainer = document.getElementById('quickProductResults');
                resultsContainer.innerHTML = '';
                
                if (products.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-sm text-gray-500">Aucun produit trouvé</p>
                        </div>
                    `;
                    return;
                }
                
                // Créer une grille pour les résultats
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-1 md:grid-cols-2 gap-2';
                
                products.forEach(product => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result p-3 hover:bg-gray-50 cursor-pointer border border-gray-200 rounded-md';
                    
                    resultItem.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900">${product.name}</p>
                                <p class="text-xs text-gray-500">${product.reference || 'Sans référence'}</p>
                            </div>
                            <span class="text-sm font-semibold text-indigo-600">${formatPrice(product.default_price)}</span>
                        </div>
                    `;
                    
                    resultItem.addEventListener('click', function() {
                        selectProduct(product);
                    });
                    
                    grid.appendChild(resultItem);
                });
                
                resultsContainer.appendChild(grid);
            }
            
            // Sélectionner un produit
            function selectProduct(product) {
                const rowId = window.currentRowId;
                
                // Mettre à jour les champs
                document.getElementById(`${rowId}-product-id`).value = product.id;
                document.getElementById(`${rowId}-price`).value = product.default_price;
                
                // Mettre à jour l'affichage
                const infoDiv = document.getElementById(`${rowId}-product-info`);
                infoDiv.classList.remove('hidden');
                infoDiv.querySelector('.product-name').textContent = product.name;
                infoDiv.querySelector('.product-ref').textContent = product.reference || '';
                
                // Cacher le placeholder
                document.getElementById(`${rowId}-product-placeholder`).classList.add('hidden');
                
                // Calculer le total
                calculateRowTotal(rowId);
                
                // Fermer le modal
                toggleProductSearchModal();
                
                // Animation de succès
                const row = document.getElementById(rowId);
                row.classList.add('animate-highlight');
                setTimeout(() => {
                    row.classList.remove('animate-highlight');
                }, 1000);
                
                // Mettre à jour le statut des produits
                updateProductsStatus();
            }
            
            // Calculer le total d'une ligne
            function calculateRowTotal(rowId) {
                const quantity = parseFloat(document.getElementById(`${rowId}-quantity`).value) || 0;
                const price = parseFloat(document.getElementById(`${rowId}-price`).value) || 0;
                const total = quantity * price;
                
                document.getElementById(`${rowId}-total`).textContent = formatPrice(total);
                
                // Calculer tous les totaux
                calculateTotals();
            }
            
            // Calculer tous les totaux
            function calculateTotals() {
                let subtotal = 0;
                let itemCount = 0;
                const productRows = document.querySelectorAll('#productsContainer tr');
                
                productRows.forEach(row => {
                    const rowId = row.id;
                    const quantity = parseFloat(document.getElementById(`${rowId}-quantity`).value) || 0;
                    const price = parseFloat(document.getElementById(`${rowId}-price`).value) || 0;
                    subtotal += quantity * price;
                    itemCount += quantity;
                });
                
                // Gérer les remises
                let discountValue = 0;
                const discountPercentInput = document.getElementById('discountPercent');
                const discountAmountInput = document.getElementById('discountAmount');
                
                if (discountPercentInput && discountPercentInput.value > 0) {
                    const discountPercent = parseFloat(discountPercentInput.value) || 0;
                    discountValue = subtotal * (discountPercent / 100);
                    
                    // Désactiver l'autre champ de remise
                    if (discountAmountInput) {
                        discountAmountInput.value = 0;
                    }
                }
                else if (discountAmountInput && discountAmountInput.value > 0) {
                    discountValue = parseFloat(discountAmountInput.value) || 0;
                    
                    // Désactiver l'autre champ de remise
                    if (discountPercentInput) {
                        discountPercentInput.value = 0;
                    }
                }
                
                // Calculer le montant après remise
                const afterDiscount = subtotal - discountValue;
                
                // Calculer la TVA
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = afterDiscount * (taxRate / 100);
                
                // Calculer le total
                const total = afterDiscount + taxAmount;
                
                // Mettre à jour l'affichage des totaux
                document.getElementById('summarySubtotal').textContent = formatPrice(subtotal);
                document.getElementById('summaryTaxAmount').textContent = formatPrice(taxAmount);
                document.getElementById('summaryTotal').textContent = formatPrice(total);
                document.getElementById('summaryItems').textContent = itemCount;
                
                // Afficher ou masquer la ligne de remise
                const discountRow = document.getElementById('discountSummaryRow');
                if (discountValue > 0) {
                    discountRow.classList.remove('hidden');
                    document.getElementById('summaryDiscount').textContent = '-' + formatPrice(discountValue);
                } else {
                    discountRow.classList.add('hidden');
                }
                
                // Mise à jour du statut des produits
                hasProductsAdded = productRows.length > 0 && subtotal > 0;
                updateProductsStatus();
                updateProgressBar();
            }
            
            // Supprimer une ligne
            function removeRow(rowId) {
                // Animation de suppression
                const row = document.getElementById(rowId);
                row.style.transition = 'all 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(10px)';
                
                setTimeout(() => {
                    row.remove();
                    calculateTotals();
                    checkEmptyProductsState();
                }, 300);
            }
            
            // Vérifier si le tableau des produits est vide
            function checkEmptyProductsState() {
                const productRows = document.querySelectorAll('#productsContainer tr');
                const emptyState = document.getElementById('emptyProductsState');
                const tableContainer = document.getElementById('productsTableContainer');
                
                if (productRows.length === 0) {
                    emptyState.classList.remove('hidden');
                    tableContainer.classList.add('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    tableContainer.classList.remove('hidden');
                }
            }
            
            // Appliquer une remise
            function applyDiscount() {
                const discountRow = document.getElementById('discountRow');
                discountRow.classList.remove('hidden');
                discountRow.classList.add('animate-scaleIn');
                
                // Mettre le focus sur le pourcentage de remise
                setTimeout(() => {
                    document.getElementById('discountPercent').focus();
                }, 300);
            }
            
            // Supprimer la remise
            function removeDiscount() {
                const discountRow = document.getElementById('discountRow');
                discountRow.style.transition = 'all 0.3s';
                discountRow.style.opacity = '0';
                discountRow.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    discountRow.classList.add('hidden');
                    discountRow.style.opacity = '1';
                    discountRow.style.transform = 'scale(1)';
                    
                    // Réinitialiser les valeurs
                    document.getElementById('discountPercent').value = 0;
                    document.getElementById('discountAmount').value = 0;
                    
                    // Recalculer les totaux
                    calculateTotals();
                }, 300);
            }
            
            // Gérer l'importation de produits
            function importProducts() {
                // Simuler l'importation de produits depuis un modèle
                const templateProducts = [
                    { id: products[0].id, name: products[0].name, price: products[0].default_price, quantity: 1 },
                    { id: products[1].id, name: products[1].name, price: products[1].default_price, quantity: 1 }
                ];
                
                // Ajouter chaque produit
                templateProducts.forEach(product => {
                    const rowId = addProductRow();
                    
                    // Simuler un court délai pour l'animation
                    setTimeout(() => {
                        // Mettre à jour les champs
                        document.getElementById(`${rowId}-product-id`).value = product.id;
                        document.getElementById(`${rowId}-quantity`).value = product.quantity;
                        document.getElementById(`${rowId}-price`).value = product.price;
                        
                        // Mettre à jour l'affichage
                        const infoDiv = document.getElementById(`${rowId}-product-info`);
                        infoDiv.classList.remove('hidden');
                        infoDiv.querySelector('.product-name').textContent = product.name;
                        
                        // Cacher le placeholder
                        document.getElementById(`${rowId}-product-placeholder`).classList.add('hidden');
                        
                        // Calculer le total
                        calculateRowTotal(rowId);
                    }, 300);
                });
                
                // Notification de succès
                showToast('Produits importés avec succès', 'success');
            }
            
            // Fonction pour formater les prix
            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
            }
            
            // Gestion de la barre de progression
            function updateProgressBar() {
                let progress = 30; // 30% pour les informations de base
                
                if (hasClientSelected) progress += 35;
                if (hasProductsAdded) progress += 35;
                
                const progressBar = document.getElementById('progressBar');
                progressBar.style.width = `${progress}%`;
            }
            
            // Mettre à jour le statut du client
            function updateClientStatus() {
                const clientStep = document.getElementById('clientStepStatus');
                
                if (hasClientSelected) {
                    clientStep.innerHTML = `
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Client sélectionné</span>
                    `;
                } else {
                    clientStep.innerHTML = `
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                            <span class="text-xs font-medium">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Client sélectionné</span>
                    `;
                }
            }
            
            // Mettre à jour le statut des produits
            function updateProductsStatus() {
                const productsStep = document.getElementById('productsStepStatus');
                
                if (hasProductsAdded) {
                    productsStep.innerHTML = `
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Produits ajoutés</span>
                    `;
                } else {
                    productsStep.innerHTML = `
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-400">
                            <span class="text-xs font-medium">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Produits ajoutés</span>
                    `;
                }
            }
            
            // Afficher une notification toast
            function showToast(message, type = 'info') {
                // Créer le toast s'il n'existe pas
                if (!document.getElementById('toast-container')) {
                    const toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'fixed bottom-4 right-4 z-50 space-y-2';
                    document.body.appendChild(toastContainer);
                }
                
                const container = document.getElementById('toast-container');
                
                // Créer le toast
                const toast = document.createElement('div');
                toast.className = 'rounded-md p-4 max-w-xs shadow-lg transform transition-all duration-300 opacity-0 translate-y-2';
                
                // Définir le style en fonction du type
                if (type === 'success') {
                    toast.classList.add('bg-green-50', 'border', 'border-green-100');
                } else if (type === 'error') {
                    toast.classList.add('bg-red-50', 'border', 'border-red-100');
                } else {
                    toast.classList.add('bg-blue-50', 'border', 'border-blue-100');
                }
                
                // Contenu du toast
                toast.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            ${type === 'success' ? 
                                '<svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>' : 
                                (type === 'error' ? 
                                    '<svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>' : 
                                    '<svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
                                )
                            }
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium ${type === 'success' ? 'text-green-800' : (type === 'error' ? 'text-red-800' : 'text-blue-800')}">
                                ${message}
                            </p>
                        </div>
                    </div>
                `;
                
                // Ajouter au container
                container.appendChild(toast);
                
                // Animation d'entrée
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                    toast.classList.add('opacity-100', 'translate-y-0');
                }, 10);
                
                // Supprimer après un délai
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }
            
            // Sauvegarder comme brouillon
            function saveAsDraft(isManual = false) {
                // Dans un cas réel, ce serait un appel AJAX pour sauvegarder en base de données
                // Ici on simule la sauvegarde
                
                // Afficher une notification uniquement si c'est une sauvegarde manuelle
                if (isManual) {
                    showToast('Brouillon sauvegardé avec succès', 'success');
                }
            }
            
            // Valider le formulaire avant l'envoi
            function validateForm() {
                let isValid = true;
                
                // Vérifier si un client est sélectionné
                if (!hasClientSelected) {
                    showToast('Veuillez sélectionner un client', 'error');
                    document.getElementById('client_search').focus();
                    isValid = false;
                }
                
                // Vérifier si des produits sont ajoutés
                if (!hasProductsAdded) {
                    showToast('Veuillez ajouter au moins un produit', 'error');
                    isValid = false;
                }
                
                return isValid;
            }
            
            // Toggle modals
            function toggleNewClientModal() {
                const modal = document.getElementById('newClientModal');
                modal.classList.toggle('hidden');
                
                if (!modal.classList.contains('hidden')) {
                    // Reset form
                    document.getElementById('quickClientForm').reset();
                    // Focus on first field
                    setTimeout(() => {
                        document.getElementById('quick_client_name').focus();
                    }, 100);
                }
            }
            
            function toggleProductSearchModal() {
                const modal = document.getElementById('productSearchModal');
                modal.classList.toggle('hidden');
            }
        </script>
    @endpush
</x-app-layout>