<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BillFlow') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/introjs.min.css">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/intro.min.js"></script>

    <!-- Charting Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div x-data="{ 
        searchOpen: false, 
        query: '', 
        results: [],
        darkMode: localStorage.getItem('dark-mode') === 'true',
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('dark-mode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
        }
    }" 
    x-init="document.documentElement.classList.toggle('dark', darkMode)" 
    class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        <div x-data="{ collapsed: localStorage.getItem('sidebar-collapsed') === 'true' }" 
            :class="{'w-64': !collapsed, 'w-20': collapsed}"
            class="sidebar bg-white dark:bg-gray-800 min-h-screen shadow-md fixed left-0 top-0 transition-all duration-300 ease-in-out z-30" 
            id="sidebar">
            
            <!-- Toggle button -->
            <button @click="collapsed = !collapsed; localStorage.setItem('sidebar-collapsed', collapsed)"
                class="absolute right-0 top-16 -mr-3 h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                <svg :class="collapsed ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            
            <div class="p-4 flex flex-col h-full">
                <!-- Logo et titre -->
                <div class="flex items-center justify-between mb-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <span :class="{'opacity-0 w-0': collapsed}" class="text-xl font-bold text-gray-800 dark:text-gray-200 transition-all duration-300">BillFlow</span>
                    </a>
                    <button id="closeSidebar" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none" aria-label="Close sidebar">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Profil utilisateur -->
                <div :class="{'p-3': !collapsed, 'p-1': collapsed}" class="flex items-center mb-8 bg-gray-50 dark:bg-gray-700 rounded-lg transition-all">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                            {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div :class="{'opacity-0 w-0': collapsed}" class="flex-1 min-w-0 ml-3 transition-all duration-300">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            @if(Auth::user()->isAdmin())
                                {{ __('Administrateur') }}
                            @elseif(Auth::user()->isManager())
                                {{ __('Manager') }}
                            @elseif(Auth::user()->role === 'vendeur')
                                {{ __('Vendeur') }}
                            @else
                                {{ Auth::user()->role }}
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Menu principal -->
                <nav class="flex-1 overflow-y-auto space-y-1">
                    <!-- Principal -->
                    <p :class="{'opacity-0 h-0 mt-0': collapsed}" class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-all mb-2">
                        {{ __('Principal') }}
                    </p>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Visualisez vos statistiques et données clés">
                        <i class="bi bi-house-door text-xl {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Tableau de bord') }}</span>
                    </a>
                    
                    <!-- Ventes - Visible pour tous -->
                    <p :class="{'opacity-0 h-0 mt-0': collapsed}" class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-all mb-2">
                        {{ __('Ventes') }}
                    </p>

                    <a href="{{ route('bills.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('bills.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Gérez vos factures de vente">
                        <i class="bi bi-receipt text-xl {{ request()->routeIs('bills.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Factures') }}</span>
                    </a>
                    
                    <a href="{{ route('clients.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('clients.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Consultez et gérez votre base clients">
                        <i class="bi bi-person text-xl {{ request()->routeIs('clients.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Clients') }}</span>
                    </a>
                    
                    <!-- Produits et inventaire - Visible pour admin et manager -->
                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                    <p :class="{'opacity-0 h-0 mt-0': collapsed}" class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-all mb-2">
                        {{ __('Produits') }}
                    </p>
                    
                    <a href="{{ route('products.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('products.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Consultez et gérez votre catalogue de produits">
                        <i class="bi bi-box text-xl {{ request()->routeIs('products.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Catalogue') }}</span>
                    </a>
                    
                    <a href="{{ route('inventory.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('inventory.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Gérez votre inventaire et stock">
                        <i class="bi bi-boxes text-xl {{ request()->routeIs('inventory.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Inventaire') }}</span>
                    </a>
                    @endif
                    
                    <!-- Commissions - Visible pour admin, manager et vendeurs -->
                    <p :class="{'opacity-0 h-0 mt-0': collapsed}" class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-all mb-2">
                        {{ __('Commissions') }}
                    </p>
                    
                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                    <a href="{{ route('commissions.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('commissions.index') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Consultez les commissions de tous les vendeurs">
                        <i class="bi bi-cash-stack text-xl {{ request()->routeIs('commissions.index') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Toutes les commissions') }}</span>
                    </a>
                    @endif
                    
                    @if(Auth::user()->role === 'vendeur')
                    <a href="{{ route('commissions.vendor-report', Auth::id()) }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('commissions.vendor-report') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Consultez vos commissions personnelles">
                        <i class="bi bi-cash text-xl {{ request()->routeIs('commissions.vendor-report') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Mes commissions') }}</span>
                    </a>
                    @endif
                    
                    <!-- Administration - Visible uniquement pour admin -->
                    @if(Auth::user()->isAdmin())
                    <p :class="{'opacity-0 h-0 mt-0': collapsed}" class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-all mb-2">
                        {{ __('Administration') }}
                    </p>
                    
                    <a href="{{ route('users.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('users.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Gérez les utilisateurs et leurs permissions">
                        <i class="bi bi-people text-xl {{ request()->routeIs('users.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Utilisateurs') }}</span>
                    </a>
                    
                    <a href="{{ route('shops.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('shops.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Gérez vos différentes boutiques">
                        <i class="bi bi-shop text-xl {{ request()->routeIs('shops.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Boutiques') }}</span>
                    </a>
                    
                    <a href="{{ route('settings.index') }}" class="flex items-center py-3 px-2 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition" data-intro="Configurez les paramètres de l'application">
                        <i class="bi bi-gear text-xl {{ request()->routeIs('settings.*') ? 'text-indigo-600 dark:text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Paramètres') }}</span>
                    </a>
                    @endif
                </nav>
                
                <!-- Pied de la barre latérale -->
                <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center py-3 px-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                            <i class="bi bi-box-arrow-right text-xl text-gray-500 dark:text-gray-400"></i>
                            <span :class="{'opacity-0 w-0': collapsed}" class="ml-3 transition-all duration-300">{{ __('Déconnexion') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex-1 transition-all duration-300" :class="{'md:ml-64': !collapsed, 'md:ml-20': collapsed}">
            <div class="sticky top-0 z-40">
                <!-- Navigation bar -->
                <nav x-data="{ open: false }" 
                    @search-hotkey.window="$dispatch('toggle-search')"
                    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-20">
                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo - Only visible on mobile when sidebar is hidden -->
                                <div class="shrink-0 flex items-center md:hidden">
                                    <a href="{{ route('dashboard') }}">
                                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-white" />
                                    </a>
                                </div>

                                <!-- Page Title - Dynamically shows current page -->
                                <div class="hidden sm:flex items-center ml-4">
                                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">
                                        @if(request()->routeIs('dashboard'))
                                            {{ __('Tableau de bord') }}
                                        @elseif(request()->routeIs('bills.*'))
                                            {{ __('Factures') }}
                                        @elseif(request()->routeIs('clients.*'))
                                            {{ __('Clients') }}
                                        @elseif(request()->routeIs('products.*'))
                                            {{ __('Catalogue produits') }}
                                        @elseif(request()->routeIs('inventory.*'))
                                            {{ __('Gestion d\'inventaire') }}
                                        @elseif(request()->routeIs('commissions.*'))
                                            {{ __('Commissions') }}
                                        @elseif(request()->routeIs('users.*'))
                                            {{ __('Utilisateurs') }}
                                        @elseif(request()->routeIs('shops.*'))
                                            {{ __('Boutiques') }}
                                        @elseif(request()->routeIs('settings.*'))
                                            {{ __('Paramètres') }}
                                        @elseif(request()->routeIs('profile.*'))
                                            {{ __('Profil') }}
                                        @else
                                            {{ config('app.name', 'BillFlow') }}
                                        @endif
                                    </h1>
                                </div>
                            </div>

                            <!-- Right Side Options -->
                            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                                <!-- Search Button -->
                                <button @click="$dispatch('toggle-search')" 
                                        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                                        title="Recherche (⌘K)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            
                                <!-- Theme Toggle -->
                                <button @click="toggleDarkMode()" 
                                        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                                        title="Basculer mode sombre/clair">
                                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </button>

                                <!-- Settings Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" 
                                            class="flex items-center p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                        <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                            {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    </button>

                                    <div x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                                        style="display: none;">
                                        <!-- User Info -->
                                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->email }}</p>
                                            <div class="flex items-center mt-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                                                    @if(Auth::user()->isAdmin())
                                                        {{ __('Administrateur') }}
                                                    @elseif(Auth::user()->isManager())
                                                        {{ __('Manager') }}
                                                    @elseif(Auth::user()->role === 'vendeur')
                                                        {{ __('Vendeur') }}
                                                    @else
                                                        {{ Auth::user()->role }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Profile Options -->
                                        <div class="py-1">
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ __('Profil') }}
                                                </div>
                                            </a>
                                            <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ __('Paramètres') }}
                                                </div>
                                            </a>
                                        </div>
                                        
                                        <!-- Logout -->
                                        <div class="border-t border-gray-100 dark:border-gray-700">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <div class="flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                        </svg>
                                                        {{ __('Déconnexion') }}
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hamburger (Mobile Menu) -->
                            <div class="flex items-center sm:hidden">
                                <!-- Mobile Search Button -->
                                <button @click="$dispatch('toggle-search')" 
                                        class="p-2 mr-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                
                                <!-- Mobile Theme Toggle -->
                                <button @click="toggleDarkMode()" 
                                        class="p-2 mr-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-300 focus:outline-none">
                                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </button>
                                
                                <!-- Mobile Menu Button -->
                                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
                        <!-- User Profile -->
                        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                        {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            
                            <div class="mt-3 space-y-1 px-2">
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ __('Profil') }}
                                </a>
                                <a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ __('Paramètres') }}
                                </a>
                                
                                <!-- Logout option -->
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            {{ __('Déconnexion') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-4 px-4 sm:px-6 lg:px-8">
                {{ $slot??"" }}
                @yield('content')
            </main>
        </div>
        
        <!-- Search modal -->
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="searchOpen = false"
             @keydown.escape.window="searchOpen = false"
             class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20" 
             style="display: none;">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" aria-hidden="true"></div>
            
            <div class="mx-auto max-w-2xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-3.5 text-gray-400 dark:text-gray-500 text-lg"></i>
                    <input x-ref="searchInput" 
                           x-model="query" 
                           @keyup.debounce.300ms="searchResources(query)"
                           type="text" 
                           placeholder="Rechercher des factures, clients, produits..." 
                           class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-0 sm:text-sm">
                    <button x-show="query.length > 0" @click="query = ''" class="absolute right-3 top-3 text-gray-400 dark:text-gray-500">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                
                <!-- Results -->
                <div x-show="query.length > 2" class="max-h-96 scroll-py-2 overflow-y-auto py-2">
                    <template x-for="(group, type) in results" :key="type">
                        <div>
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" x-text="type"></div>
                            <template x-for="result in group" :key="result.id">
                                <a :href="result.url" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-900">
                                    <div class="flex items-center">
                                        <div x-show="type === 'Clients'" class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div x-show="type === 'Factures'" class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div x-show="type === 'Produits'" class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                                            <i class="bi bi-box"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="result.title"></p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="result.subtitle"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    
                    <div x-show="query.length > 2 && Object.keys(results).length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        Aucun résultat trouvé pour "<span x-text="query"></span>"
                    </div>
                </div>
                
                <!-- Quick Access Shortcuts -->
                <div x-show="query.length === 0" class="py-4 px-6">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Accès rapide</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <a href="{{ route('bills.create') }}" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">
                            <div class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">Nouvelle facture</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Créer une nouvelle facture</p>
                            </div>
                        </a>
                        <a href="{{ route('clients.create') }}" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">
                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">Nouveau client</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ajouter un nouveau client</p>
                            </div>
                        </a>
                        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                        <a href="{{ route('products.create') }}" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">
                            <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">Nouveau produit</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ajouter un nouveau produit</p>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton de recherche rapide (touche K) -->
    <button @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())" 
            class="fixed bottom-20 right-4 md:bottom-4 md:right-4 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-300 p-3 rounded-full shadow-lg focus:outline-none hover:shadow-xl transition z-40 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span class="absolute -top-10 right-0 whitespace-nowrap bg-gray-800 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition">
            Search <span class="ml-1 px-1.5 py-0.5 bg-gray-700 rounded">⌘K</span>
        </span>
    </button>

    <!-- Bouton pour afficher la barre latérale sur mobile -->
    <button id="sidebarToggle" class="fixed bottom-4 right-4 md:hidden bg-indigo-600 text-white p-3 rounded-full shadow-lg focus:outline-none z-40" aria-label="Toggle sidebar">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Lien vers le tutoriel interactif -->
    <button id="startTutorial" class="fixed bottom-20 left-4 md:bottom-4 bg-indigo-600 text-white p-3 rounded-full shadow-lg focus:outline-none hover:bg-indigo-700 transition z-40" title="Démarrer le tutoriel interactif">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: "{{ session('success') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            iconColor: '#4F46E5',
            customClass: {
                popup: 'colored-toast'
            }
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ session('error') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            iconColor: '#EF4444',
            customClass: {
                popup: 'colored-toast'
            }
        });
    </script>
    @endif

    <style>
        .colored-toast {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 1rem !important;
            padding: 1rem 1.5rem !important;
        }
        
        /* Dark mode styles */
        .dark .colored-toast {
            background-color: #1f2937 !important;
            color: #ffffff !important;
        }
        
        /* Make sure the sidebar is properly fixed */
        @media (min-width: 768px) {
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                height: 100vh;
                overflow-y: auto;
            }
            
            .md\:ml-64 {
                margin-left: 16rem;
            }
            
            .md\:ml-20 {
                margin-left: 5rem;
            }
        }
        
        /* Fix mobile sidebar */
        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 50;
            }
            
            #sidebar.translate-x-0 {
                transform: translateX(0);
            }
        }
        
        /* IntroJS customizations */
        .introjs-tooltip {
            border-radius: 0.5rem !important;
        }
        
        .dark .introjs-tooltip {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
        }
        
        .dark .introjs-tooltiptext {
            color: #f3f4f6 !important;
        }
        
        .dark .introjs-button {
            color: #f3f4f6 !important;
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }
        
        .dark .introjs-button:hover {
            background-color: #4b5563 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dark mode based on localStorage
            const darkModeEnabled = localStorage.getItem('dark-mode') === 'true';
            document.documentElement.classList.toggle('dark', darkModeEnabled);
            
            // Mobile sidebar
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebar.classList.toggle('translate-x-0');
                });
            }
            
            if (closeSidebar) {
                closeSidebar.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                });
            }
            
            // Fermer la barre latérale par défaut sur mobile
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            }
            
            // Keyboard shortcut for search (Cmd+K or Ctrl+K)
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('search-hotkey'));
                }
            });
            
            // Initialize IntroJS tutorial
            const startTutorial = document.getElementById('startTutorial');
            if (startTutorial) {
                startTutorial.addEventListener('click', function() {
                    const intro = introJs();
                    intro.setOptions({
                        steps: [
                            {
                                intro: "Bienvenue dans BillFlow ! Découvrez les fonctionnalités principales de l'application."
                            },
                            {
                                element: document.querySelector('[data-intro="Visualisez vos statistiques et données clés"]'),
                                intro: "Visualisez vos statistiques et données clés sur le tableau de bord."
                            },
                            {
                                element: document.querySelector('[data-intro="Gérez vos factures de vente"]'),
                                intro: "Gérez vos factures de vente, créez de nouvelles factures et suivez les paiements."
                            },
                            {
                                element: document.querySelector('[data-intro="Consultez et gérez votre base clients"]'),
                                intro: "Consultez et gérez votre base clients avec toutes les informations nécessaires."
                            }
                        ],
                        nextLabel: 'Suivant',
                        prevLabel: 'Précédent',
                        skipLabel: 'Passer',
                        doneLabel: 'Terminer',
                        showStepNumbers: false,
                        showBullets: true,
                        disableInteraction: false
                    });
                    
                    // Adapt intro colors to dark mode
                    if (document.documentElement.classList.contains('dark')) {
                        intro.setOption('tooltipClass', 'introjs-tooltip-dark');
                    }
                    
                    intro.start();
                });
            }
        });
        
        // Search function
        function searchResources(query) {
            if (query.length < 3) return [];
            
            // Fetch search results from API
            fetch(`/api/search?q=${encodeURIComponent(query)}`)
                .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok.'))
                .then(data => {
                    this.results = data;
                }).catch(error => {
                    console.error('Search error:', error);
                    this.results = {};
                });
        }
    </script>

    @stack('scripts')
</body>
</html>