<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails de la boutique') }} - {{ $shop->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('shops.edit', $shop) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-pencil-square mr-1"></i>
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('shops.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-arrow-left mr-1"></i>
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Informations générales -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/4 flex flex-col items-center">
                        @if($shop->logo)
                            <img src="{{ asset('storage/' . $shop->logo) }}" alt="{{ $shop->name }}" class="w-full max-w-xs rounded-lg shadow">
                        @else
                            <div class="w-full aspect-square max-w-xs bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $shop->is_active ? __('Actif') : __('Inactif') }}
                            </span>
                        </div>
                    </div>

                    <div class="md:w-3/4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $shop->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('ID') }}: {{ $shop->shop_id ?? 'N/A' }}</p>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Adresse') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $shop->address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Téléphone') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $shop->phone }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Email') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $shop->email }}</p>
                                </div>
                            </div>

                            @if($shop->description)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Description') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $shop->description }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Créé le') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $shop->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Statistiques générales') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Ventes totales -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Ventes totales') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($shop->bills->sum('total'), 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nombre de factures -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Factures') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $shop->bills->count() }}</p>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Utilisateurs associés') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Managers -->
                        <div>
                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Gestionnaires') }}</h4>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nom') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Email') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Statistiques') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @forelse($shop->managers as $manager)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                            <span class="text-xs font-medium">{{ substr($manager->name, 0, 2) }}</span>
                                                        </div>
                                                        <div class="ml-3">{{ $manager->name }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $manager->email }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    <a href="{{ route('users.show', $manager) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                                        {{ __('Voir les statistiques') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ __('Aucun gestionnaire') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Vendeurs -->
                        <div>
                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Vendeurs') }}</h4>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nom') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Ventes') }}</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Statistiques') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @forelse($shop->vendors as $vendor)
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 flex-shrink-0 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                            <span class="text-xs font-medium">{{ substr($vendor->name, 0, 2) }}</span>
                                                        </div>
                                                        <div class="ml-3">{{ $vendor->name }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $vendor->bills->where('shop_id', $shop->id)->count() }} {{ __('factures') }}
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    <a href="{{ route('users.show', $vendor) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                                        {{ __('Voir les statistiques') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    {{ __('Aucun vendeur') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques des commissions par vendeur -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Commissions par vendeur') }}</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @forelse($shop->vendors as $vendor)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                            <span class="font-medium">{{ substr($vendor->name, 0, 2) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $vendor->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor->email }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('commissions.vendor-report', $vendor) }}" class="inline-flex items-center px-3 py-1 text-xs border border-transparent rounded-md font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800">
                                            {{ __('Détails') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2">
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('Commissions totales') }}</span>
                                        <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ number_format($vendor->commission_stats['total'], 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2">
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('En attente') }}</span>
                                        <span class="block text-sm font-semibold text-amber-600 dark:text-amber-400">
                                            {{ number_format($vendor->commission_stats['pending'], 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2">
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('Payées') }}</span>
                                        <span class="block text-sm font-semibold text-green-600 dark:text-green-400">
                                            {{ number_format($vendor->commission_stats['paid'], 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                </div>


                            </div>
                        @empty
                            <div class="col-span-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                                <p class="text-center text-gray-500 dark:text-gray-400">{{ __('Aucun vendeur associé à cette boutique') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('commissions.shop-report', $shop->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="bi bi-cash-stack mr-2"></i>
                            {{ __('Voir toutes les commissions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
