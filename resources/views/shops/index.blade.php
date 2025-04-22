<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Magasins') }}
                </h2>
                <a href="{{ route('shops.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouveau Magasin') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('status'))
                        <div class="mb-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-3 bg-gray-50 p-2 rounded-lg">
                        <form action="{{ route('shops.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div>
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('Nom, adresse, téléphone...') }}" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="sort" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Trier par') }}</label>
                                <select name="sort" id="sort" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Nom') }}</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('Date de création') }}</option>
                                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>{{ __('Statut') }}</option>
                                </select>
                            </div>
                            <div class="md:flex md:flex-col md:justify-end">
                                <button type="submit" class="mt-4 inline-flex justify-center items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded text-xs font-medium text-white hover:bg-indigo-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Filtrer') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @forelse($shops as $shop)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                <div class="relative">
                                    <div class="h-32 bg-indigo-50 flex items-center justify-center">
                                        @if($shop->logo)
                                            <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="h-32 w-full object-cover">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="absolute top-2 right-2">
                                        @if($shop->status === 'active')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Actif') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ __('Inactif') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-sm font-medium">{{ $shop->name }}</h3>
                                            <p class="text-xs text-gray-500 mt-0.5">ID: {{ $shop->shop_id }}</p>
                                        </div>
                                        <div class="flex space-x-1">
                                            <a href="{{ route('shops.edit', $shop) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('Modifier') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </a>
                                            <button type="button" onclick="confirmDelete('{{ $shop->id }}')" class="text-red-600 hover:text-red-900" title="{{ __('Supprimer') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-2 space-y-1">
                                        <div class="flex items-start text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 mt-0.5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-600 line-clamp-1">{{ $shop->address }}</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                            <span class="text-gray-600">{{ $shop->phone }}</span>
                                        </div>
                                        <div class="flex items-center text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            <span class="text-gray-600">{{ $shop->email }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center">
                                        <div class="text-xs text-gray-500">
                                            {{ __('Créé le') }} {{ $shop->created_at->format('d/m/Y') }}
                                        </div>
                                        <a href="{{ route('shops.show', $shop) }}" class="inline-flex items-center px-2 py-1 text-xs bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100">
                                            {{ __('Détails') }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-white p-3 text-center text-gray-500 text-sm rounded-lg border border-gray-200">
                                {{ __('Aucun magasin trouvé') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        {{ $shops->appends(request()->except('page'))->links() }}
                    </div>

                    <!-- Modal de confirmation de suppression -->
                    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white p-3">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-8 w-8 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-sm font-medium text-gray-900" id="modal-title">
                                                {{ __('Confirmation de suppression') }}
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Êtes-vous sûr de vouloir supprimer ce magasin ? Cette action est irréversible et supprimera toutes les données associées.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" onclick="cancelDelete()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ __('Annuler') }}
                                        </button>
                                        <form id="deleteForm" method="POST" action="">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                {{ __('Supprimer') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(shopId) {
            document.getElementById('deleteForm').action = `/shops/${shopId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function cancelDelete() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout> 