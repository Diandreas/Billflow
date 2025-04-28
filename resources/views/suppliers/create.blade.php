<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Nouveau fournisseur') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Créer un nouveau fournisseur dans le système') }}
                </p>
            </div>
            <a href="{{ route('suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Retour aux fournisseurs') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Informations principales -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">{{ __('Informations principales') }}</h3>
                                
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom du fournisseur') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Entreprise XYZ') }}">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom du contact') }}</label>
                                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Jean Dupont') }}">
                                    @error('contact_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: contact@entreprise.com') }}">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Téléphone') }}</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: +33 6 12 34 56 78') }}">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Site web') }}</label>
                                    <input type="url" name="website" id="website" value="{{ old('website') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: https://www.entreprise.com') }}">
                                    @error('website')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="actif" {{ old('status') === 'actif' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactif" {{ old('status') === 'inactif' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Adresse et informations supplémentaires -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">{{ __('Adresse et informations supplémentaires') }}</h3>
                                
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adresse') }}</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: 123 Rue Principale') }}">
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Ville') }}</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Paris') }}">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Code postal') }}</label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: 75001') }}">
                                    @error('postal_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Pays') }}</label>
                                    <input type="text" name="country" id="country" value="{{ old('country') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: France') }}">
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Numéro de TVA / SIRET') }}</label>
                                    <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: FR12345678901') }}">
                                    @error('tax_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                                    <textarea name="notes" id="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Informations supplémentaires...') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 flex justify-end border-t">
                            <a href="{{ route('suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg mr-3 inline-flex items-center transition-colors duration-150">
                                <i class="bi bi-x-lg mr-2"></i>
                                {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                                <i class="bi bi-save mr-2"></i>
                                {{ __('Enregistrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 