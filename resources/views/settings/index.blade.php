<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paramètres') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
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
                                Logo de l'entreprise
                            </label>
                            <div class="mt-2 flex items-center">
                                @if($settings->logo_path)
                                    <div class="mr-4">
                                        <img src="{{ Storage::url($settings->logo_path) }}"
                                             alt="Logo" class="h-12 w-auto">
                                    </div>
                                    <p class="text-xs text-gray-500 mr-4">Logo actuel</p>
                                @else
                                    <p class="text-xs text-gray-500 mr-4">Aucun logo téléchargé</p>
                                @endif
                                <input type="file" name="logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Le logo apparaîtra sur vos factures. Format recommandé: PNG ou JPG, maximum 1 Mo.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
