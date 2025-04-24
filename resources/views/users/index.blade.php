<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Gestion des Utilisateurs') }}
                </h2>
                <a href="{{ route('users.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-indigo-700 rounded-md hover:bg-indigo-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouvel Utilisateur') }}
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
                        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-2">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('Nom, email, rôle...') }}" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="role" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Rôle') }}</label>
                                <select name="role" id="role" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Utilisateur') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rôle') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Dernière connexion') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6">
                                                    @if($user->profile_photo_path)
                                                        <img class="h-6 w-6 rounded-full object-cover" src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="{{ $user->name }}">
                                                    @else
                                                        <div class="h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-xs font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ __('Depuis le') }} {{ $user->created_at->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            @if($user->is_active)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                                                    {{ __('Actif') }}
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">
                                                    {{ __('Inactif') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : __('Jamais') }}
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-right">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Voir') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('Modifier') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                @if(auth()->id() != $user->id)
                                                <button type="button" onclick="confirmDelete('{{ $user->id }}')" class="text-red-600 hover:text-red-900" title="{{ __('Supprimer') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-2 text-center text-gray-500 text-xs">{{ __('Aucun utilisateur trouvé') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $users->appends(request()->except('page'))->links() }}
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
                                                    {{ __('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.') }}
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
        function confirmDelete(userId) {
            document.getElementById('deleteForm').action = `/users/${userId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function cancelDelete() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout> 