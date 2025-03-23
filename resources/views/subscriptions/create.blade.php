<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Souscrire à un abonnement') }}
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
                        <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                        <p class="text-gray-500 mt-1">{{ $plan->description }}</p>
                        
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Prix</p>
                                <p class="text-2xl font-bold text-indigo-900">{{ $plan->formatted_price }}</p>
                                <p class="text-indigo-700 font-medium">{{ $plan->billing_cycle === 'monthly' ? 'par mois' : 'par an' }}</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">SMS inclus</p>
                                <p class="text-2xl font-bold text-green-900">{{ number_format($plan->sms_quota, 0, ',', ' ') }}</p>
                                <p class="text-green-700 font-medium">Marketing + {{ number_format($plan->sms_personal_quota, 0, ',', ' ') }} personnels</p>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm font-medium text-gray-600">Campagnes</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $plan->campaigns_per_cycle }}</p>
                                <p class="text-purple-700 font-medium">par {{ $plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('subscriptions.store', $plan) }}" class="mt-8">
                        @csrf
                        
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">Méthode de paiement</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition {{ old('payment_method') == 'mobile_money' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}" onclick="document.getElementById('payment_method_mobile_money').checked = true;">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" id="payment_method_mobile_money" name="payment_method" value="mobile_money" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('payment_method') == 'mobile_money' ? 'checked' : '' }}>
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
                        
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="terms_accepted" name="terms_accepted" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ old('terms_accepted') ? 'checked' : '' }}>
                                <label for="terms_accepted" class="ml-2 block text-sm text-gray-700">
                                    Je reconnais avoir lu et j'accepte les <a href="#" class="text-indigo-600 hover:text-indigo-500">conditions générales d'utilisation</a> et la <a href="#" class="text-indigo-600 hover:text-indigo-500">politique de confidentialité</a>.
                                </label>
                            </div>
                            
                            @error('terms_accepted')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-8 flex items-center justify-end">
                            <a href="{{ route('subscriptions.plans') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                                Annuler
                            </a>
                            
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                Confirmer et payer {{ $plan->formatted_price }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 