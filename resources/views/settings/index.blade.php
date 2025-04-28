<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Paramètres') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Configuration de votre entreprise et préférences système') }}
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50">
                    <i class="bi bi-arrow-left mr-2"></i>
                    {{ __('Retour au tableau de bord') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Configuration de l\'entreprise') }}</h3>
                
                    <form action="{{ route('settings.update', $settings) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Informations de l'entreprise</h3>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Nom de l'entreprise
                                    </label>
                                    <input type="text" name="company_name" value="{{ $settings->company_name }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Adresse
                                    </label>
                                    <textarea name="address" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300">{{ $settings->address }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        SIRET
                                    </label>
                                    <input type="text" name="siret" value="{{ $settings->siret }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Numéro de Contribuable
                                    </label>
                                    <input type="text" name="tax_number" value="{{ $settings->tax_number }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                    <p class="mt-1 text-xs text-gray-500">Ce numéro apparaîtra sur vos factures</p>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Informations de contact</h3>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Téléphone
                                    </label>
                                    <input type="text" name="phone" value="{{ $settings->phone }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Email
                                    </label>
                                    <input type="email" name="email" value="{{ $settings->email }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Site web
                                    </label>
                                    <input type="url" name="website" value="{{ $settings->website }}"
                                           class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('Logo de l\'entreprise') }}
                            </label>
                            <div class="mt-2 flex items-center">
                                @if($settings->logo_path)
                                    <div class="mr-4">
                                        <img src="{{ Storage::url($settings->logo_path) }}"
                                             alt="Logo" class="h-12 w-auto">
                                    </div>
                                    <p class="text-xs text-gray-500 mr-4">{{ __('Logo actuel') }}</p>
                                @else
                                    <p class="text-xs text-gray-500 mr-4">{{ __('Aucun logo téléchargé') }}</p>
                                @endif
                                <input type="file" name="logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Le logo apparaîtra sur vos factures. Format recommandé: PNG ou JPG, maximum 1 Mo.') }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="bi bi-info-circle mr-1"></i> 
                                {{ __('Les modifications prendront effet immédiatement') }}
                            </span>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-5 rounded-lg shadow-sm transition-colors duration-150">
                                <i class="bi bi-check-lg mr-2"></i>
                                {{ __('Enregistrer les modifications') }}
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
</x-app-layout>
