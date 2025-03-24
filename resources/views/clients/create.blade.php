<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Nouveau Client') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Ajouter un nouveau client à votre base') }}
                </p>
            </div>
            <a href="{{ route('clients.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-5 rounded-lg shadow-sm inline-flex items-center transition-colors duration-150">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom complet') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                       required>
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700">{{ __('Sexe') }}</label>
                                <select name="sex" id="sex"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Non spécifié') }}</option>
                                    <option value="M" {{ old('sex') == 'M' ? 'selected' : '' }}>{{ __('Homme') }}</option>
                                    <option value="F" {{ old('sex') == 'F' ? 'selected' : '' }}>{{ __('Femme') }}</option>
                                    <option value="Other" {{ old('sex') == 'Other' ? 'selected' : '' }}>{{ __('Autre') }}</option>
                                </select>
                                @error('sex')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="birth" class="block text-sm font-medium text-gray-700">{{ __('Date de naissance') }}</label>
                                <input type="date" name="birth" id="birth" value="{{ old('birth') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Numéros de téléphone') }}</label>
                            
                            <div id="phone-container" class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="phones[]" placeholder="{{ __('Ex: +1234567890') }}"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           required>
                                    <button type="button" onclick="addPhoneField()" class="py-2 px-3 bg-indigo-100 text-indigo-600 rounded-md hover:bg-indigo-200">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                            
                            @error('phones.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">{{ __('Adresse') }}</label>
                            <textarea name="address" id="address" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                      placeholder="{{ __('Adresse complète') }}">{{ old('address') }}</textarea>
                            @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                      placeholder="{{ __('Informations supplémentaires') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Créer le client') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    @push('scripts')
    <script>
        function addPhoneField() {
            const container = document.getElementById('phone-container');
            const phoneFields = container.querySelectorAll('input[name="phones[]"]');
            
            // Limiter à 3 numéros de téléphone
            if (phoneFields.length >= 3) {
                return;
            }
            
            const phoneFieldWrapper = document.createElement('div');
            phoneFieldWrapper.className = 'flex items-center space-x-2';
            
            phoneFieldWrapper.innerHTML = `
                <input type="text" name="phones[]" placeholder="{{ __('Ex: +1234567890') }}"
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <button type="button" class="py-2 px-3 bg-red-100 text-red-600 rounded-md hover:bg-red-200 remove-phone">
                    <i class="bi bi-dash-lg"></i>
                </button>
            `;
            
            container.appendChild(phoneFieldWrapper);
            
            // Ajouter l'event listener pour supprimer le champ
            phoneFieldWrapper.querySelector('.remove-phone').addEventListener('click', function() {
                phoneFieldWrapper.remove();
            });
        }
    </script>
    @endpush
</x-app-layout>
