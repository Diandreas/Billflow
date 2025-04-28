<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Fournisseurs') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Gérez vos fournisseurs et suivez leurs produits') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                    <i class="bi bi-plus-lg mr-2"></i>
                    {{ __('Nouveau fournisseur') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">{{ __('Total des fournisseurs') }}</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_suppliers'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">{{ __('Fournisseurs actifs') }}</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_suppliers'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">{{ __('Total des produits fournis') }}</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_products'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">{{ __('Fournisseurs avec produits') }}</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['suppliers_with_products'] }}</div>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form action="{{ route('suppliers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Rechercher') }}</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Nom, contact, email...') }}">
                        </div>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                        <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                            <option value="inactif" {{ request('status') === 'inactif' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-2 px-4 rounded-md inline-flex items-center transition-colors duration-150">
                            <i class="bi bi-funnel-fill mr-2"></i>
                            {{ __('Filtrer') }}
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md inline-flex items-center transition-colors duration-150">
                            <i class="bi bi-x-circle mr-2"></i>
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Liste des fournisseurs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($suppliers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Nom') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Contact') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Email / Téléphone') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Ville / Pays') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Produits') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Statut') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($suppliers as $supplier)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $supplier->name }}
                                                </a>
                                                @if($supplier->website)
                                                    <a href="{{ $supplier->website }}" target="_blank" class="ml-1 text-gray-400 hover:text-gray-500" title="{{ __('Visiter le site web') }}">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $supplier->contact_name ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($supplier->email)
                                                    <div class="flex items-center">
                                                        <i class="bi bi-envelope text-gray-400 mr-1"></i>
                                                        <a href="mailto:{{ $supplier->email }}" class="text-gray-600 hover:text-gray-900">
                                                            {{ $supplier->email }}
                                                        </a>
                                                    </div>
                                                @endif
                                                @if($supplier->phone)
                                                    <div class="flex items-center mt-1">
                                                        <i class="bi bi-telephone text-gray-400 mr-1"></i>
                                                        <a href="tel:{{ $supplier->phone }}" class="text-gray-600 hover:text-gray-900">
                                                            {{ $supplier->phone }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $supplier->city ?: '-' }}
                                                @if($supplier->city && $supplier->country)
                                                    <span class="text-gray-400 mx-1">/</span>
                                                @endif
                                                {{ $supplier->country ?: '' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $supplier->products_count }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $supplier->status === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $supplier->status === 'actif' ? __('Actif') : __('Inactif') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('Voir') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Éditer') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('Supprimer') }}" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce fournisseur ?') }}')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $suppliers->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-gray-400 text-5xl"></i>
                            <p class="mt-2 text-gray-500">{{ __('Aucun fournisseur trouvé') }}</p>
                            @if(request('search') || request('status'))
                                <a href="{{ route('suppliers.index') }}" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Réinitialiser les filtres') }}
                                </a>
                            @else
                                <a href="{{ route('suppliers.create') }}" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Créer un fournisseur') }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 