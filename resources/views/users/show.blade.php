<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-2 md:space-y-0">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Détails de l\'utilisateur') }} - {{ $user->name }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="bi bi-pencil-square mr-1"></i>
                    {{ __('Modifier') }}
                </a>
                @endif
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Informations personnelles') }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Nom') }}:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Email') }}:</span>
                                <div class="flex items-center">
                                    <span class="text-gray-800 dark:text-gray-200">{{ $user->email }}</span>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('users.reset-email.form', $user) }}" class="ml-2 text-xs px-2 py-1 bg-amber-100 text-amber-800 rounded-full hover:bg-amber-200 transition">
                                            <i class="bi bi-envelope mr-1"></i> {{ __('Réinitialiser') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Téléphone') }}:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $user->phone ?? __('Non renseigné') }}</span>
                            </div>
                            
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Rôle') }}:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($user->role == 'admin') bg-red-100 text-red-800
                                    @elseif($user->role == 'manager') bg-blue-100 text-blue-800
                                    @elseif($user->role == 'vendeur') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($user->role == 'admin') {{ __('Administrateur') }}
                                    @elseif($user->role == 'manager') {{ __('Manager') }}
                                    @elseif($user->role == 'vendeur') {{ __('Vendeur') }}
                                    @else {{ $user->role }} @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Date d\'inscription') }}:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Dernière connexion') }}:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : __('Jamais') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Boutiques assignées -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Boutiques assignées') }}
                        </h3>
                        
                        @if ($userShops->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nom') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Rôle') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date d\'assignation') }}</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Commission') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($userShops as $shop)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('shops.show', $shop) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">{{ $shop->name }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if ($shop->pivot->is_manager ?? false)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ __('Manager') }}</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('Vendeur') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($shop->pivot->assigned_at ?? $shop->pivot->created_at)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    @if (isset($shop->pivot->custom_commission_rate))
                                                        {{ $shop->pivot->custom_commission_rate }}%
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ __('personnalisé') }})</span>
                                                    @else
                                                        {{ $user->commission_rate }}%
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ __('par défaut') }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="rounded-md bg-blue-50 dark:bg-blue-900 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700 dark:text-blue-200">
                                            {{ __('Cet utilisateur n\'est assigné à aucune boutique.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                        </svg>
                        {{ __('Statistiques générales') }}
                    </h3>
                    
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
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($user->bills->sum('total'), 0, ',', ' ') }} FCFA</p>
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
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->bills->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Commissions totales -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($user->commissions->sum('amount'), 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Commissions en attente -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions en attente') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($user->commissions->where('is_paid', false)->sum('amount'), 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions pour les vendeurs -->
            @if($user->role === 'vendeur')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Actions') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('commissions.vendor-report', $user) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('Rapport de commissions') }}
                        </a>
                        
                        <a href="{{ route('bills.index', ['seller_id' => $user->id]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('Factures émises') }}
                        </a>
                        
                        <a href="{{ route('vendor-equipment.index', ['user_id' => $user->id]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('Équipement') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Factures de l'utilisateur -->
    @if($user->role === 'vendeur')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Factures émises') }}
                    </h3>
                    
                    <!-- Filtres de recherche -->
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="billSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Recherche') }}</label>
                            <input type="text" id="billSearch" placeholder="{{ __('Rechercher par client, référence...') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>
                        <div>
                            <label for="dateFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Période') }}</label>
                            <select id="dateFilter" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="all">{{ __('Toutes les périodes') }}</option>
                                <option value="today">{{ __('Aujourd\'hui') }}</option>
                                <option value="yesterday">{{ __('Hier') }}</option>
                                <option value="thisWeek">{{ __('Cette semaine') }}</option>
                                <option value="thisMonth">{{ __('Ce mois-ci') }}</option>
                                <option value="lastMonth">{{ __('Le mois dernier') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Statut') }}</label>
                            <select id="statusFilter" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                <option value="all">{{ __('Tous les statuts') }}</option>
                                <option value="pending">{{ __('En attente') }}</option>
                                <option value="completed">{{ __('Terminé') }}</option>
                                <option value="cancelled">{{ __('Annulée') }}</option>
                            </select>
                        </div>
                    </div>
                    
                    @if(count($user->bills) > 0)
                        <div class="overflow-x-auto">
                            <table id="billsTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="ref">
                                            {{ __('Référence') }} <span class="sort-icon">↕</span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="date">
                                            {{ __('Date') }} <span class="sort-icon">↕</span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="client">
                                            {{ __('Client') }} <span class="sort-icon">↕</span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="total">
                                            {{ __('Total') }} <span class="sort-icon">↕</span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="status">
                                            {{ __('Statut') }} <span class="sort-icon">↕</span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="billsTableBody">
                                    @foreach($user->bills as $bill)
                                    <tr class="bill-row" 
                                        data-ref="{{ $bill->reference }}" 
                                        data-date="{{ $bill->created_at->format('Y-m-d') }}" 
                                        data-client="{{ $bill->client ? $bill->client->name : 'N/A' }}" 
                                        data-total="{{ $bill->total }}" 
                                        data-status="{{ $bill->status }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $bill->reference }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $bill->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $bill->client ? $bill->client->name : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($bill->total, 0, '.', ' ') }} FCFA</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($bill->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($bill->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($bill->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                @if($bill->status === 'completed') {{ __('Terminé') }}
                                                @elseif($bill->status === 'pending') {{ __('En attente') }}
                                                @elseif($bill->status === 'cancelled') {{ __('Annulée') }}
                                                @else {{ $bill->status }} @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <a href="{{ route('bills.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="flex justify-between items-center mt-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Affichage de') }} <span id="startIndex">1</span> {{ __('à') }} <span id="endIndex">{{ min(10, count($user->bills)) }}</span> {{ __('sur') }} <span id="totalItems">{{ count($user->bills) }}</span> {{ __('factures') }}
                            </div>
                            <div class="flex space-x-2" id="pagination">
                                <button id="prevPage" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Précédent') }}
                                </button>
                                <button id="nextPage" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Suivant') }}
                                    <svg class="h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="rounded-md bg-yellow-50 dark:bg-yellow-900 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        {{ __('Cet utilisateur n\'a émis aucune facture.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsPerPage = 10;
            let currentPage = 1;
            let filteredItems = [];
            
            const billRows = document.querySelectorAll('.bill-row');
            const searchInput = document.getElementById('billSearch');
            const dateFilter = document.getElementById('dateFilter');
            const statusFilter = document.getElementById('statusFilter');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const startIndexElem = document.getElementById('startIndex');
            const endIndexElem = document.getElementById('endIndex');
            const totalItemsElem = document.getElementById('totalItems');
            const tableHeaders = document.querySelectorAll('th[data-sort]');
            
            let sortConfig = {
                column: 'date',
                direction: 'desc'
            };
            
            // Initialize
            filterAndDisplayItems();
            
            // Event listeners
            searchInput.addEventListener('input', filterAndDisplayItems);
            dateFilter.addEventListener('change', filterAndDisplayItems);
            statusFilter.addEventListener('change', filterAndDisplayItems);
            prevPageBtn.addEventListener('click', goToPrevPage);
            nextPageBtn.addEventListener('click', goToNextPage);
            
            // Add sort event listeners
            tableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    
                    // Toggle direction if same column clicked again
                    if (sortConfig.column === column) {
                        sortConfig.direction = sortConfig.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortConfig.column = column;
                        sortConfig.direction = 'asc';
                    }
                    
                    // Update sort icons
                    updateSortIcons();
                    
                    // Re-filter and display
                    filterAndDisplayItems();
                });
            });
            
            function updateSortIcons() {
                tableHeaders.forEach(header => {
                    const icon = header.querySelector('.sort-icon');
                    if (header.dataset.sort === sortConfig.column) {
                        icon.textContent = sortConfig.direction === 'asc' ? '↑' : '↓';
                    } else {
                        icon.textContent = '↕';
                    }
                });
            }
            
            function filterAndDisplayItems() {
                // Reset pagination
                currentPage = 1;
                
                // Get filter values
                const searchValue = searchInput.value.toLowerCase();
                const dateValue = dateFilter.value;
                const statusValue = statusFilter.value;
                
                // Filter items
                filteredItems = Array.from(billRows).filter(row => {
                    const ref = row.dataset.ref.toLowerCase();
                    const client = row.dataset.client.toLowerCase();
                    const status = row.dataset.status.toLowerCase();
                    const date = new Date(row.dataset.date);
                    
                    // Search filter
                    const matchesSearch = ref.includes(searchValue) || client.includes(searchValue);
                    
                    // Status filter
                    const matchesStatus = statusValue === 'all' || status === statusValue;
                    
                    // Date filter
                    let matchesDate = true;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    
                    const thisWeekStart = new Date(today);
                    thisWeekStart.setDate(today.getDate() - today.getDay());
                    
                    const thisMonthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                    
                    const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                    
                    switch(dateValue) {
                        case 'today':
                            matchesDate = date >= today;
                            break;
                        case 'yesterday':
                            matchesDate = date >= yesterday && date < today;
                            break;
                        case 'thisWeek':
                            matchesDate = date >= thisWeekStart;
                            break;
                        case 'thisMonth':
                            matchesDate = date >= thisMonthStart;
                            break;
                        case 'lastMonth':
                            matchesDate = date >= lastMonthStart && date <= lastMonthEnd;
                            break;
                        default:
                            matchesDate = true;
                    }
                    
                    return matchesSearch && matchesStatus && matchesDate;
                });
                
                // Sort items
                filteredItems.sort((a, b) => {
                    let aValue = a.dataset[sortConfig.column];
                    let bValue = b.dataset[sortConfig.column];
                    
                    if (sortConfig.column === 'total') {
                        aValue = parseFloat(aValue);
                        bValue = parseFloat(bValue);
                    } else if (sortConfig.column === 'date') {
                        aValue = new Date(aValue).getTime();
                        bValue = new Date(bValue).getTime();
                    }
                    
                    if (aValue < bValue) return sortConfig.direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return sortConfig.direction === 'asc' ? 1 : -1;
                    return 0;
                });
                
                displayItems();
                updatePaginationControls();
            }
            
            function displayItems() {
                const tableBody = document.getElementById('billsTableBody');
                
                // Calculate start and end indices for current page
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, filteredItems.length);
                
                // Update pagination info
                startIndexElem.textContent = filteredItems.length > 0 ? startIndex + 1 : 0;
                endIndexElem.textContent = endIndex;
                totalItemsElem.textContent = filteredItems.length;
                
                // Clear table
                tableBody.innerHTML = '';
                
                // No results found
                if (filteredItems.length === 0) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Aucune facture trouvée.') }}
                        </td>
                    `;
                    tableBody.appendChild(noResultsRow);
                    return;
                }
                
                // Add items for current page
                const currentPageItems = filteredItems.slice(startIndex, endIndex);
                currentPageItems.forEach(row => {
                    tableBody.appendChild(row);
                });
            }
            
            function updatePaginationControls() {
                const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
            }
            
            function goToPrevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    displayItems();
                    updatePaginationControls();
                }
            }
            
            function goToNextPage() {
                const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    displayItems();
                    updatePaginationControls();
                }
            }
            
            // Initial sort icon update
            updateSortIcons();
        });
    </script>
    @endpush
    @endif

    <!-- Gestion des commissions -->
    @if($user->role === 'vendeur')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Gestion des commissions') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('Commissions en attente') }}</h4>
                            <p class="text-xl font-bold text-amber-600 dark:text-amber-400">
                                {{ number_format($user->pendingCommissions, 0, '.', ' ') }} FCFA
                            </p>
                            @if($user->pendingCommissions > 0)
                                <a href="{{ route('commissions.pending', $user) }}" class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ __('Voir le détail') }}
                                    <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ __('Commissions payées') }}</h4>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($user->paidCommissions, 0, '.', ' ') }} FCFA
                            </p>
                            <a href="{{ route('commissions.user-history', $user) }}" class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                {{ __('Historique des paiements') }}
                                <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    @can('manage-commissions')
                    <div class="mt-4 flex justify-end">
                        <form method="POST" action="{{ route('commissions.vendor-pay', $user) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                {{ __('Payer les commissions') }}
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout> 