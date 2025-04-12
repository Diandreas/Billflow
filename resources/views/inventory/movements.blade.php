<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historique des mouvements de stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filtres -->
                    <form action="{{ route('inventory.movements') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Produit') }}</label>
                                <select id="product_id" name="product_id" class="w-full rounded-md border-gray-300">
                                    <option value="">{{ __('Tous les produits') }}</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type de mouvement') }}</label>
                                <select id="type" name="type" class="w-full rounded-md border-gray-300">
                                    <option value="">{{ __('Tous les types') }}</option>
                                    <option value="entrée" {{ request('type') == 'entrée' ? 'selected' : '' }}>{{ __('Entrée') }}</option>
                                    <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>{{ __('Sortie') }}</option>
                                    <option value="ajustement" {{ request('type') == 'ajustement' ? 'selected' : '' }}>{{ __('Ajustement') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de début') }}</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de fin') }}</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300">
                            </div>
                            
                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    {{ __('Filtrer') }}
                                </button>
                                @if(request()->anyFilled(['product_id', 'type', 'date_from', 'date_to']))
                                    <a href="{{ route('inventory.movements') }}" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                                        {{ __('Réinitialiser') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des mouvements -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Produit') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Quantité') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Stock avant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Stock après') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Référence') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Par') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $movement->product ? $movement->product->name : 'Produit inconnu' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $movement->product ? $movement->product->sku : '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $movement->type == 'entrée' ? 'bg-green-100 text-green-800' : 
                                                   ($movement->type == 'sortie' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                                            {{ $movement->type == 'entrée' ? 'text-green-600' : 
                                               ($movement->type == 'sortie' ? 'text-red-600' : 'text-blue-600') }}">
                                            {{ $movement->type == 'entrée' ? '+' : ($movement->type == 'sortie' ? '-' : '') }}{{ abs($movement->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->stock_before }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->stock_after }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($movement->bill_id)
                                                <a href="{{ route('bills.show', $movement->bill_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $movement->reference ?? 'Facture #' . $movement->bill_id }}
                                                </a>
                                            @else
                                                {{ $movement->reference ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->user ? $movement->user->name : 'Système' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ __('Aucun mouvement de stock trouvé') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 