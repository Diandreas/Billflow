<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recharge de SMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Acheter des SMS supplémentaires</h3>
                        <p class="text-gray-500 mt-1">Les SMS achetés seront ajoutés à votre quota disponible sur votre abonnement actif.</p>
                        
                        <div class="mt-4 bg-indigo-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-indigo-800">Votre abonnement actuel : {{ $activeSubscription->plan->name }}</h4>
                            <p class="text-indigo-600 mt-1">SMS marketing restants : {{ $activeSubscription->sms_remaining }} / {{ $activeSubscription->plan->sms_quota }}</p>
                            <p class="text-indigo-600">SMS personnels restants : {{ $activeSubscription->sms_personal_remaining }} / {{ $activeSubscription->plan->sms_personal_quota }}</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('subscriptions.recharge') }}" class="mt-8">
                        @csrf
                        
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Quantité de SMS à acheter</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('sms_amount') == 100 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('sms_amount_100').checked = true; updateSmsAmount(100);">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="sms_amount_100" name="sms_amount" value="100" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('sms_amount') == 100 ? 'checked' : '' }}>
                                        <label for="sms_amount_100" class="ml-2 block text-md font-medium text-gray-700">100 SMS</label>
                                    </div>
                                    <p class="text-gray-900 font-semibold">1 000 FCFA</p>
                                    <p class="text-gray-500 text-sm">Idéal pour les petites campagnes</p>
                                </div>
                                
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('sms_amount') == 500 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('sms_amount_500').checked = true; updateSmsAmount(500);">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="sms_amount_500" name="sms_amount" value="500" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('sms_amount') == 500 ? 'checked' : '' }}>
                                        <label for="sms_amount_500" class="ml-2 block text-md font-medium text-gray-700">500 SMS</label>
                                    </div>
                                    <p class="text-gray-900 font-semibold">5 000 FCFA</p>
                                    <p class="text-gray-500 text-sm">Pour les campagnes moyennes</p>
                                </div>
                                
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('sms_amount') == 1000 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('sms_amount_1000').checked = true; updateSmsAmount(1000);">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="sms_amount_1000" name="sms_amount" value="1000" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('sms_amount') == 1000 ? 'checked' : '' }}>
                                        <label for="sms_amount_1000" class="ml-2 block text-md font-medium text-gray-700">1 000 SMS</label>
                                    </div>
                                    <p class="text-gray-900 font-semibold">10 000 FCFA</p>
                                    <p class="text-gray-500 text-sm">Pour les campagnes importantes</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label for="sms_amount_custom" class="block text-sm font-medium text-gray-700 mb-1">Ou spécifiez un montant personnalisé (entre 100 et 5000 SMS)</label>
                                <div class="flex items-center">
                                    <input type="number" id="sms_amount_custom" name="sms_amount_custom" min="100" max="5000" step="100" value="{{ old('sms_amount_custom', 100) }}" class="form-input rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="updateCustomAmount(this.value)">
                                    <span class="ml-2 text-gray-600">SMS</span>
                                    <span class="ml-4 text-gray-700 font-medium" id="custom_price">= 1 000 FCFA</span>
                                </div>
                            </div>
                            
                            @error('sms_amount')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Méthode de paiement</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('payment_method') == 'mobile_money' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('payment_method_mobile_money').checked = true;">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="payment_method_mobile_money" name="payment_method" value="mobile_money" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('payment_method', 'mobile_money') == 'mobile_money' ? 'checked' : '' }}>
                                        <label for="payment_method_mobile_money" class="ml-2 block text-md font-medium text-gray-700">Mobile Money</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Paiement via Orange Money, MTN Mobile Money ou autres opérateurs mobiles</p>
                                </div>
                                
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('payment_method') == 'credit_card' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('payment_method_credit_card').checked = true;">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="payment_method_credit_card" name="payment_method" value="credit_card" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                        <label for="payment_method_credit_card" class="ml-2 block text-md font-medium text-gray-700">Carte bancaire</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Paiement sécurisé par carte VISA, Mastercard ou autres cartes bancaires</p>
                                </div>
                                
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('payment_method') == 'bank_transfer' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('payment_method_bank_transfer').checked = true;">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="payment_method_bank_transfer" name="payment_method" value="bank_transfer" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                        <label for="payment_method_bank_transfer" class="ml-2 block text-md font-medium text-gray-700">Virement bancaire</label>
                                    </div>
                                    <p class="text-gray-500 text-sm">Paiement par virement bancaire - instructions envoyées par email</p>
                                </div>
                            </div>
                            
                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-8 flex items-center justify-end">
                            <a href="{{ route('subscriptions.index') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                                Annuler
                            </a>
                            
                            <button type="submit" id="submit_button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                Confirmer et payer <span id="total_price">1 000 FCFA</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSmsAmount(amount) {
            // Mettre à jour la valeur radio
            document.querySelector('input[name="sms_amount"][value="' + amount + '"]').checked = true;
            
            // Mettre à jour le champ personnalisé
            document.getElementById('sms_amount_custom').value = amount;
            
            // Calculer et mettre à jour le prix
            updatePrice(amount);
        }
        
        function updateCustomAmount(amount) {
            // Déchocher les boutons radio
            document.querySelectorAll('input[name="sms_amount"]').forEach(input => {
                input.checked = false;
            });
            
            // Mettre à jour le champ caché pour le formulaire
            document.querySelector('input[name="sms_amount"]').value = amount;
            
            // Calculer et mettre à jour le prix
            updatePrice(amount);
        }
        
        function updatePrice(amount) {
            // Calculer le prix (1000 FCFA pour 100 SMS)
            const price = Math.round(amount / 100) * 1000;
            const formattedPrice = price.toLocaleString('fr-FR') + ' FCFA';
            
            // Mettre à jour les éléments d'affichage
            document.getElementById('custom_price').textContent = '= ' + formattedPrice;
            document.getElementById('total_price').textContent = formattedPrice;
        }
    </script>
</x-app-layout> 