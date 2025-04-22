<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Inventaire') }}
                </h2>
                <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-orange-700 rounded-md hover:bg-orange-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouveau Produit') }}
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
                        <form action="{{ route('inventory.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-2">
                            <div class="md:col-span-2">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('Nom, référence, code barre...') }}" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="category" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Catégorie') }}</label>
                                <select name="category" id="category" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Toutes') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous') }}</option>
                                    <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>{{ __('En stock') }}</option>
                                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>{{ __('Stock faible') }}</option>
                                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Rupture') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="sort" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Trier par') }}</label>
                                <select name="sort" id="sort" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Nom') }}</option>
                                    <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>{{ __('Stock') }}</option>
                                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>{{ __('Prix') }}</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('Date') }}</option>
                                </select>
                            </div>
                            <div class="md:flex md:items-end md:pb-0">
                                <button type="submit" class="mt-4 inline-flex justify-center w-full items-center px-3 py-1.5 bg-orange-600 border border-transparent rounded text-xs font-medium text-white hover:bg-orange-700">
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
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Image') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Produit') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Prix') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                                    <th scope="col" class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1.5 whitespace-nowrap text-xs">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                @if ($product->image)
                                                    <img class="h-8 w-8 rounded-md object-cover" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-md bg-orange-100 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ Str::limit($product->description, 30) }}</div>
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            <div>{{ $product->sku }}</div>
                                            @if ($product->barcode)
                                                <div class="text-xs text-gray-400">{{ $product->barcode }}</div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $product->category->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">{{ number_format($product->price, 2) }} €</div>
                                            @if ($product->compare_price > 0)
                                                <div class="text-xs text-gray-500 line-through">{{ number_format($product->compare_price, 2) }} €</div>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap">
                                            @if ($product->stock <= 0)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">
                                                    {{ __('Rupture') }}
                                                </span>
                                            @elseif ($product->stock <= $product->low_stock_threshold)
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $product->stock }} {{ __('(Faible)') }}
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                                                    {{ $product->stock }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-1.5 whitespace-nowrap text-right text-xs">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('inventory.show', $product) }}" class="text-orange-600 hover:text-orange-900" title="{{ __('Voir') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('inventory.edit', $product) }}" class="text-orange-600 hover:text-orange-900" title="{{ __('Modifier') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="confirmAdjust('{{ $product->id }}')" class="text-blue-600 hover:text-blue-900" title="{{ __('Ajuster le stock') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="confirmDelete('{{ $product->id }}')" class="text-red-600 hover:text-red-900" title="{{ __('Supprimer') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-2 py-2 text-center text-sm text-gray-500">{{ __('Aucun produit trouvé') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $products->appends(request()->except('page'))->links() }}
                    </div>

                    <!-- Modal d'ajustement de stock -->
                    <div id="adjustModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white p-3">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-sm leading-6 font-medium text-gray-900" id="modal-title">
                                                {{ __('Ajuster le stock') }}
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Ajustez la quantité en stock pour ce produit. Utilisez des valeurs positives pour ajouter et négatives pour retirer.') }}
                                                </p>
                                                <form id="adjustForm" method="POST" action="" class="mt-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div>
                                                        <label for="adjustment" class="block text-xs font-medium text-gray-700">{{ __('Quantité') }}</label>
                                                        <input type="number" name="adjustment" id="adjustment" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50" required>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label for="reason" class="block text-xs font-medium text-gray-700">{{ __('Raison') }}</label>
                                                        <select name="reason" id="reason" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50" required>
                                                            <option value="restock">{{ __('Réapprovisionnement') }}</option>
                                                            <option value="correction">{{ __('Correction') }}</option>
                                                            <option value="damage">{{ __('Produit endommagé') }}</option>
                                                            <option value="theft">{{ __('Vol') }}</option>
                                                            <option value="other">{{ __('Autre') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label for="notes" class="block text-xs font-medium text-gray-700">{{ __('Notes') }}</label>
                                                        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" onclick="cancelAdjust()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            {{ __('Annuler') }}
                                        </button>
                                        <button type="button" onclick="submitAdjust()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            {{ __('Ajuster') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                                    {{ __('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" onclick="cancelDelete()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
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
        function confirmAdjust(productId) {
            document.getElementById('adjustForm').action = `/inventory/${productId}/adjust`;
            document.getElementById('adjustModal').classList.remove('hidden');
        }

        function cancelAdjust() {
            document.getElementById('adjustModal').classList.add('hidden');
        }

        function submitAdjust() {
            document.getElementById('adjustForm').submit();
        }

        function confirmDelete(productId) {
            document.getElementById('deleteForm').action = `/inventory/${productId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function cancelDelete() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout> 