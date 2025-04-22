<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'BillFlow') }}</title>
    
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
    <!-- Alpine.js -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Intro.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/introjs.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/intro.min.js"></script>
    
    <!-- Charting Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Style pour x-cloak (cacher les éléments avant initialisation d'Alpine.js) -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Animation pulse pour le bouton d'aide */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        /* Styles pour IntroJS en mode sombre */
        .introjs-tooltip {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(209, 213, 219, 0.2) !important;
        }
        
        .dark .introjs-tooltip,
        .dark-tooltip {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
            border-color: #374151 !important;
        }
        
        .dark .introjs-tooltiptext,
        .dark-tooltip .introjs-tooltiptext {
            color: #f3f4f6 !important;
        }
        
        .dark .introjs-arrow.top,
        .dark-tooltip .introjs-arrow.top {
            border-bottom-color: #1f2937 !important;
        }
        
        .dark .introjs-arrow.right,
        .dark-tooltip .introjs-arrow.right {
            border-left-color: #1f2937 !important;
        }
        
        .dark .introjs-arrow.bottom,
        .dark-tooltip .introjs-arrow.bottom {
            border-top-color: #1f2937 !important;
        }
        
        .dark .introjs-arrow.left,
        .dark-tooltip .introjs-arrow.left {
            border-right-color: #1f2937 !important;
        }
        
        .dark .introjs-button,
        .dark-tooltip .introjs-button {
            background-color: #374151 !important;
            color: #f3f4f6 !important;
            border: 1px solid #4b5563 !important;
            text-shadow: none !important;
        }
        
        .dark .introjs-button:hover,
        .dark-tooltip .introjs-button:hover {
            background-color: #4b5563 !important;
        }
        
        .dark .introjs-button:focus,
        .dark .introjs-button:active,
        .dark-tooltip .introjs-button:focus,
        .dark-tooltip .introjs-button:active {
            background-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.4) !important;
        }
        
        .introjs-helperLayer {
            border-radius: 0.5rem !important;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5) !important;
        }
        
        /* Customizing the tooltip title */
        .introjs-tooltip-title {
            font-weight: 600 !important;
            font-size: 1.1rem !important;
            margin-bottom: 0.5rem !important;
            color: #4f46e5 !important;
        }
        
        .dark .introjs-tooltip-title,
        .dark-tooltip .introjs-tooltip-title {
            color: #818cf8 !important;
        }
        
        /* ScrollBar styling */
        ::-webkit-scrollbar {
            width: 5px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 20px;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body x-data="{ 
      searchOpen: false, 
      query: '', 
      results: [],
      darkMode: localStorage.getItem('dark-mode') === 'true',
      sidebarOpen: localStorage.getItem('sidebar-collapsed') !== 'true' && window.innerWidth >= 768,
      isRunningTutorial: false,
      toggleDarkMode() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('dark-mode', this.darkMode);
          
          if (this.darkMode) {
              document.documentElement.classList.add('dark');
          } else {
              document.documentElement.classList.remove('dark');
          }
          
          // Mettre à jour le style du tutoriel si en cours
          if (this.isRunningTutorial) {
              const tooltips = document.querySelectorAll('.introjs-tooltip');
              tooltips.forEach(tooltip => {
                  if (this.darkMode) {
                      tooltip.classList.add('dark-tooltip');
                  } else {
                      tooltip.classList.remove('dark-tooltip');
                  }
              });
          }
      },
      toggleSidebar() {
          this.sidebarOpen = !this.sidebarOpen;
          localStorage.setItem('sidebar-collapsed', !this.sidebarOpen);
      },
      startTutorial() {
          this.isRunningTutorial = true;
          initIntroJS();
      }
    }" 
    x-init="
      // Vérifiez d'abord la préférence système
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      
      // Ensuite vérifiez le localStorage (qui a priorité)
      darkMode = localStorage.getItem('dark-mode') === 'true' || 
                  (localStorage.getItem('dark-mode') === null && prefersDark);
      
      // Appliquez le mode directement au document
      if (darkMode) {
          document.documentElement.classList.add('dark');
          localStorage.setItem('dark-mode', 'true');
      } else {
          document.documentElement.classList.remove('dark');
          localStorage.setItem('dark-mode', 'false');
      }

      // Mobile handling
      if (window.innerWidth < 768) {
          sidebarOpen = false;
      }
    "
    class="m-0 font-sans text-base antialiased font-normal leading-normal bg-gray-50 text-gray-700 dark:bg-gray-900 dark:text-gray-300">
    
    <!-- Sidenav -->
    <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
           class="fixed inset-y-0 left-0 z-50 w-64 my-4 ml-4 overflow-y-auto bg-white rounded-xl shadow-lg transition-transform duration-300 dark:bg-gray-800 xl:translate-x-0"
           id="sidebar">
        <!-- Logo -->
        <div class="h-20 px-6 flex items-center" data-intro-id="logo" data-title="BillFlow" data-intro="Bienvenue dans BillFlow, votre application de gestion de factures et clients.">
            <button class="absolute right-4 top-4 p-2 rounded-md lg:hidden focus:outline-none" @click="sidebarOpen = false">
                <i class="fas fa-times text-gray-400"></i>
            </button>
            <a class="flex items-center space-x-3" href="{{ route('dashboard') }}">
                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">B</span>
                </div>
                <span class="text-lg font-semibold text-gray-800 dark:text-white">BillFlow</span>
            </a>
        </div>

        <hr class="h-px border-0 bg-gradient-to-r from-transparent via-gray-400 to-transparent opacity-25 dark:via-gray-600" />

        <!-- User profile -->
        <div class="p-4 mx-4 my-4 rounded-lg bg-gray-50 dark:bg-gray-700" data-intro-id="profile" data-title="Votre profil" data-intro="Ici, vous pouvez voir votre profil et votre rôle dans l'application.">
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                    {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0 ml-3">
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
        </div>
        
        <!-- Navigation -->
        <div class="overflow-y-auto h-full pb-20">
            <ul class="space-y-1 px-3">
                <!-- Dashboard -->
                <li>
                    <a class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('dashboard') }}" 
                       data-intro-id="dashboard" 
                       data-title="Tableau de bord" 
                       data-intro="Le tableau de bord vous donne une vue d'ensemble de votre activité avec des statistiques clés et graphiques.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-tachometer-alt text-lg {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Tableau de bord') }}</span>
                    </a>
                </li>
                
                <!-- Factures -->
                <li>
                    <a class="{{ request()->routeIs('bills.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('bills.index') }}" 
                       data-intro-id="bills" 
                       data-title="Factures" 
                       data-intro="Gérez toutes vos factures : création, modification, suivi des paiements et historique.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-file-invoice text-lg {{ request()->routeIs('bills.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Factures') }}</span>
                    </a>
                </li>
                
                <!-- Clients -->
                <li>
                    <a class="{{ request()->routeIs('clients.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('clients.index') }}" 
                       data-intro-id="clients" 
                       data-title="Clients" 
                       data-intro="Consultez et gérez votre base clients avec toutes les informations nécessaires.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-users text-lg {{ request()->routeIs('clients.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Clients') }}</span>
                    </a>
                </li>
                
                <!-- Produits et inventaire - Visible pour admin et manager -->
                @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                <li class="pt-5 pb-1">
                    <p class="px-3 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">{{ __('Produits') }}</p>
                </li>
                
                <!-- Catalogue -->
                <li>
                    <a class="{{ request()->routeIs('products.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('products.index') }}" 
                       data-intro-id="products" 
                       data-title="Catalogue produits" 
                       data-intro="Consultez et gérez votre catalogue de produits, prix et descriptions.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-box text-lg {{ request()->routeIs('products.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Catalogue') }}</span>
                    </a>
                </li>
                
                <!-- Inventaire -->
                <li>
                    <a class="{{ request()->routeIs('inventory.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('inventory.index') }}" 
                       data-intro-id="inventory" 
                       data-title="Inventaire" 
                       data-intro="Gérez vos niveaux de stock, entrées et sorties de produits.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-boxes text-lg {{ request()->routeIs('inventory.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Inventaire') }}</span>
                    </a>
                </li>
                @endif
                
                <!-- Commissions -->
                <li class="pt-5 pb-1">
                    <p class="px-3 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">{{ __('Commissions') }}</p>
                </li>
                
                @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                <li>
                    <a class="{{ request()->routeIs('commissions.index') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('commissions.index') }}" 
                       data-intro-id="commissions" 
                       data-title="Commissions" 
                       data-intro="Gérez les commissions de tous les vendeurs et suivez leurs performances.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-hand-holding-usd text-lg {{ request()->routeIs('commissions.index') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Toutes les commissions') }}</span>
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->role === 'vendeur')
                <li>
                    <a class="{{ request()->routeIs('commissions.vendor-report') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('commissions.vendor-report', Auth::id()) }}" 
                       data-intro-id="my-commissions" 
                       data-title="Mes commissions" 
                       data-intro="Consultez vos commissions personnelles et vos performances de vente.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-money-bill-wave text-lg {{ request()->routeIs('commissions.vendor-report') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Mes commissions') }}</span>
                    </a>
                </li>
                @endif
                
                <!-- Administration - Visible uniquement pour admin -->
                @if(Auth::user()->isAdmin())
                <li class="pt-5 pb-1">
                    <p class="px-3 text-xs font-bold uppercase text-gray-500 dark:text-gray-400">{{ __('Administration') }}</p>
                </li>
                
                <!-- Utilisateurs -->
                <li>
                    <a class="{{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('users.index') }}" 
                       data-intro-id="users" 
                       data-title="Utilisateurs" 
                       data-intro="Gérez les utilisateurs du système et leurs permissions.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-user-cog text-lg {{ request()->routeIs('users.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Utilisateurs') }}</span>
                    </a>
                </li>
                
                <!-- Boutiques -->
                <li>
                    <a class="{{ request()->routeIs('shops.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('shops.index') }}" 
                       data-intro-id="shops" 
                       data-title="Boutiques" 
                       data-intro="Gérez vos différentes boutiques ou points de vente.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-store text-lg {{ request()->routeIs('shops.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Boutiques') }}</span>
                    </a>
                </li>
                
                <!-- Paramètres -->
                <li>
                    <a class="{{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }} 
                          flex items-center p-3 rounded-lg transition-colors duration-200" 
                       href="{{ route('settings.index') }}" 
                       data-intro-id="settings" 
                       data-title="Paramètres" 
                       data-intro="Configurez les paramètres généraux de l'application.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-cog text-lg {{ request()->routeIs('settings.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Paramètres') }}</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        
        <!-- Sidebar footer -->
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Déconnexion') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <main class="relative min-h-screen transition-all duration-200 xl:ml-64">
        <!-- Navbar -->
        <nav class="bg-white dark:bg-gray-800 shadow-md rounded-xl mx-6 mt-6 px-6 py-3" data-intro-id="navbar" data-title="Navigation" data-intro="La barre de navigation vous permet d'accéder aux fonctionnalités principales et à votre profil.">
            <div class="flex items-center justify-between">
                <!-- Breadcrumb and page title -->
                <div class="flex flex-col">
                    <!-- Breadcrumb -->
                    <ol class="flex text-sm">
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-300">BillFlow</a>
                        </li>
                        <li class="mx-2 text-gray-500 dark:text-gray-400">/</li>
                        <li class="text-indigo-600 dark:text-indigo-300 font-medium">
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
                        </li>
                    </ol>
                    <!-- Page title -->
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white mt-1">
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

                <!-- Right side menu -->
                <div class="flex items-center space-x-4">
                    <!-- Search button -->
                    <button @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())" 
                            class="p-3 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center"
                            data-intro-id="search" 
                            data-title="Recherche globale" 
                            data-intro="Recherchez rapidement des factures, clients ou produits dans toute l'application. Utilisez le raccourci ⌘K.">
                        <i class="fas fa-search text-lg"></i>
                        <span class="ml-2 hidden md:inline">Recherche</span>
                    </button>
                    
                    <!-- Toggle dark mode - AMÉLIORATION : Ajouté un libellé et rendu plus visible -->
                    <button @click="toggleDarkMode()" 
                            class="p-3 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center"
                            data-intro-id="theme" 
                            data-title="Mode sombre/clair" 
                            data-intro="Basculez entre le mode sombre et clair selon vos préférences.">
                        <!-- Utilisez x-cloak pour éviter le clignotement -->
                        <i x-cloak x-show="!darkMode" class="fas fa-sun text-yellow-500 text-lg"></i>
                        <i x-cloak x-show="darkMode" class="fas fa-moon text-blue-400 text-lg"></i>
                        <span class="ml-2 hidden md:inline" x-text="darkMode ? 'Mode clair' : 'Mode sombre'">Mode</span>
                    </button>
                    
                    <!-- Toggle sidebar on mobile -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="p-3 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 xl:hidden">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <!-- User menu -->
                    <div x-data="{ open: false }" class="relative" data-intro-id="user-menu" data-title="Menu utilisateur" data-intro="Accédez à votre profil, paramètres et option de déconnexion.">
                        <button @click="open = !open" 
                                class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white font-bold focus:outline-none hover:bg-indigo-600 transition-colors">
                            {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                        </button>
                        
                        <div x-cloak x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700">
                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user-circle mr-2"></i> {{ __('Profil') }}
                                </a>
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-cog mr-2"></i> {{ __('Paramètres') }}
                                </a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Déconnexion') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page content -->
        <div class="p-6">
            <!-- Content -->
            {{ $slot ?? "" }}
            @yield('content')
            
            <!-- Footer -->
            <footer class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="w-full">
                    <div class="text-sm text-center text-gray-500 dark:text-gray-400">
                        ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        BillFlow - Tous droits réservés
                    </div>
                </div>
            </footer>
        </div>
    </main>
    
    <!-- Search modal -->
    <div x-cloak x-show="searchOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="searchOpen = false"
         @keydown.escape.window="searchOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-25 dark:bg-gray-900 dark:bg-opacity-50 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
        
        <div class="mx-auto max-w-2xl transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 transition-all">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400 dark:text-gray-500"></i>
                <input x-ref="searchInput" 
                       x-model="query" 
                       @keyup.debounce.300ms="searchResources(query)"
                       type="text" 
                       placeholder="Rechercher des factures, clients, produits..." 
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-0 sm:text-sm">
                <button x-show="query.length > 0" @click="query = ''" class="absolute right-3 top-3 text-gray-400 dark:text-gray-500">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
            
            <!-- Results -->
            <div x-show="query.length > 2" class="max-h-96 overflow-y-auto py-2">
                <template x-for="(group, type) in results" :key="type">
                    <div>
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" x-text="type"></div>
                        <template x-for="result in group" :key="result.id">
                            <a :href="result.url" class="block px-4 py-2 hover:bg-indigo-50 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <div x-show="type === 'Clients'" class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div x-show="type === 'Factures'" class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div x-show="type === 'Produits'" class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                                        <i class="fas fa-box"></i>
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
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900 dark:text-white">Nouvelle facture</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Créer une nouvelle facture</p>
                        </div>
                    </a>
                    <a href="{{ route('clients.create') }}" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">
                        <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900 dark:text-white">Nouveau client</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ajouter un nouveau client</p>
                        </div>
                    </a>
                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                    <a href="{{ route('products.create') }}" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md">
                        <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                            <i class="fas fa-box-open"></i>
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
    
    <!-- Floating Action Buttons - AMELIORATION : Rendu plus visible avec texte et animation -->
    <div class="fixed bottom-20 left-4 md:bottom-8 md:left-8 z-50">
        <button @click="startTutorial()" 
                class="group bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-xl shadow-lg hover:shadow-xl focus:outline-none transition-all duration-200 pulse-animation flex items-center gap-2"
                data-intro-id="help-button"
                data-title="Aide interactive"
                data-intro="Cliquez sur ce bouton à tout moment pour lancer le tutoriel interactif.">
            <i class="fas fa-question-circle text-xl"></i>
            <span class="hidden md:block font-medium">Aide</span>
            <!-- Infobulle au survol pour mobile -->
            <span class="absolute -top-10 left-0 bg-gray-800 text-white text-xs p-2 rounded opacity-0 group-hover:opacity-100 transition-opacity md:hidden whitespace-nowrap">
                Tutoriel interactif
            </span>
        </button>
    </div>
    
    <button @click="searchOpen = true; $nextTick(() => $refs.searchInput?.focus())" 
            class="fixed bottom-4 right-4 md:bottom-8 md:right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-xl shadow-lg hover:shadow-xl focus:outline-none transition-all duration-200 z-50 flex items-center gap-2" 
            title="Recherche (⌘K)">
        <i class="fas fa-search text-xl"></i>
        <span class="hidden md:block font-medium">Recherche</span>
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
            iconColor: '#4F46E5'
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
            iconColor: '#EF4444'
        });
    </script>
    @endif

    <script>
        function initIntroJS() {
            const intro = introJs();
            const isDarkMode = document.documentElement.classList.contains('dark');
            
            // Collectez tous les éléments avec des attributs data-intro-id
            const steps = [];
            document.querySelectorAll('[data-intro-id]').forEach(el => {
                steps.push({
                    element: el,
                    title: el.getAttribute('data-title'),
                    intro: el.getAttribute('data-intro'),
                    position: el.getAttribute('data-position') || 'right'
                });
            });
            
            // Ajoutez une étape d'introduction
            steps.unshift({
                title: "Bienvenue dans BillFlow",
                intro: "BillFlow est votre solution tout-en-un pour la gestion de facturation. Suivez ce guide rapide pour découvrir les principales fonctionnalités.",
            });
            
            // Ajoutez une étape finale
            steps.push({
                title: "Vous êtes prêt !",
                intro: "Vous connaissez maintenant les bases de BillFlow. N'hésitez pas à explorer davantage et à relancer le tutoriel à tout moment en cliquant sur le bouton d'aide.",
            });
            
            // Configurez IntroJS avec nos étapes et options
            intro.setOptions({
                steps: steps,
                nextLabel: 'Suivant',
                prevLabel: 'Précédent',
                skipLabel: 'Passer',
                doneLabel: 'Terminer',
                showStepNumbers: false,
                showBullets: true,
                exitOnOverlayClick: false,
                disableInteraction: false,
                tooltipClass: isDarkMode ? 'dark-tooltip' : '',
                highlightClass: 'intro-highlight'
            });
            
            // Personnalisation du HTML pour ajouter un titre à la boîte de dialogue
            intro.onbeforechange(function(targetElement) {
                // On vérifie si l'élément existe
                if (targetElement) {
                    // Obtenez l'étape actuelle
                    const currentStep = intro._currentStep;
                    const currentStepData = steps[currentStep];
                    
                    // Après un délai pour permettre à introjs de créer le tooltip
                    setTimeout(function() {
                        // Trouvez tous les tooltips
                        const tooltips = document.querySelectorAll('.introjs-tooltip');
                        tooltips.forEach(tooltip => {
                            // Vérifiez si le titre existe déjà
                            if (!tooltip.querySelector('.introjs-tooltip-title') && currentStepData.title) {
                                const tooltipText = tooltip.querySelector('.introjs-tooltiptext');
                                if (tooltipText) {
                                    const titleElement = document.createElement('h3');
                                    titleElement.className = 'introjs-tooltip-title';
                                    titleElement.textContent = currentStepData.title;
                                    tooltipText.parentNode.insertBefore(titleElement, tooltipText);
                                }
                            }
                            
                            // Appliquez le style dark si nécessaire
                            if (isDarkMode) {
                                tooltip.classList.add('dark-tooltip');
                            }
                        });
                    }, 50);
                }
            });
            
            intro.start();
            
            // Lorsque le tutoriel se termine
            intro.oncomplete(function() {
                window.isRunningTutorial = false;
            });
            
            intro.onexit(function() {
                window.isRunningTutorial = false;
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Notification pour le tutoriel (aide l'utilisateur à trouver le bouton)
            setTimeout(() => {
                Swal.fire({
                    icon: 'info',
                    title: 'Besoin d\'aide ?',
                    text: 'Cliquez sur le bouton "Aide" en bas à gauche pour démarrer le tutoriel interactif',
                    toast: true,
                    position: 'bottom-start',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    iconColor: '#4F46E5'
                });
            }, 2000);
            
            // Écouter les changements de préférence de mode sombre du système
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                if (localStorage.getItem('dark-mode') === null) {
                    if (event.matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
            
            // Keyboard shortcut for search (Cmd+K or Ctrl+K)
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('search-hotkey'));
                }
            });
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