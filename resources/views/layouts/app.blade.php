<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BillFlow') }}</title>

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Font Awesome Icons (CDN direct au lieu du kit) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js (inclusion directe) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Intro.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/introjs.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.0.1/intro.min.js"></script>

    <!-- Charting Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Style pour x-cloak et améliorations de design -->
    <style>
        [x-cloak] { display: none !important; }

        /* Animation pulse améliorée pour le bouton d'aide */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(37, 99, 235, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 1);
        }

        /* Améliorations de l'interface */
        .nav-link {
            @apply flex items-center p-3 rounded-lg transition-colors duration-200;
        }

        .nav-link.active {
            @apply bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300;
        }

        .nav-link:not(.active) {
            @apply text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700;
        }

        .card {
            @apply bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300;
        }

        /* Effet de survol pour les cartes */
        .hover-lift {
            transition: transform 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
        }

        /* Amélioration des boutons */
        .btn-primary {
            @apply bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }

        .btn-secondary {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
        }

        /* Styles pour IntroJS en mode sombre */
        .introjs-tooltip {
            padding: 20px;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(209, 213, 219, 0.2) !important;
        }
        .introjs-overlay {

            transform: scale(2);
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

        /* User dropdown animation */
        .animate-dropdown-enter {
            transform-origin: top right;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body
    class="h-full m-0 font-sans text-base antialiased font-normal leading-normal bg-gray-50 text-gray-700 dark:bg-gray-900 dark:text-gray-300">

<div x-data="{
        sidebarOpen: localStorage.getItem('sidebar-collapsed') !== 'true' && window.innerWidth >= 768,
        isRunningTutorial: false,

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebar-collapsed', !this.sidebarOpen ? 'true' : 'false');
        },

        startTutorial() {
            this.isRunningTutorial = true;
            initIntroJS();
        }
    }"
     x-init="
        $nextTick(() => {
            // Gestion du sidebar sur mobile
            if (window.innerWidth < 768) {
                sidebarOpen = false;
            }
        });
    ">

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

                <!-- Trocs -->
                <li>
                    <a class="{{ request()->routeIs('barters.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}
                          flex items-center p-3 rounded-lg transition-colors duration-200"
                       href="{{ route('barters.index') }}"
                       data-intro-id="barters"
                       data-title="Trocs"
                       data-intro="Gérez les échanges et trocs avec vos clients.">
                        <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                            <i class="fas fa-exchange-alt text-lg {{ request()->routeIs('barters.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        </div>
                        <span>{{ __('Trocs') }}</span>
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

                    <li>
                        <a class="{{ request()->routeIs('commission-payments.index') || request()->routeIs('commission-payments.show') || (request()->routeIs('commission-payments.shop-history') && !request()->is('*vendor*')) ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}
                          flex items-center p-3 rounded-lg transition-colors duration-200"
                           href="{{ route('commission-payments.index') }}"
                           data-intro-id="commission-payments"
                           data-title="Paiements de commissions"
                           data-intro="Consultez l'historique de tous les paiements de commissions effectués.">
                            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                                <i class="fas fa-receipt text-lg {{ request()->routeIs('commission-payments.index') || request()->routeIs('commission-payments.show') || (request()->routeIs('commission-payments.shop-history') && !request()->is('*vendor*')) ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                            </div>
                            <span>{{ __('Historique des paiements') }}</span>
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

                    <li>
                        <a class="{{ request()->routeIs('commission-payments.vendor-history') ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 dark:text-indigo-300' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}
                          flex items-center p-3 rounded-lg transition-colors duration-200"
                           href="{{ route('commission-payments.vendor-history', Auth::id()) }}"
                           data-intro-id="my-payments"
                           data-title="Mes paiements"
                           data-intro="Consultez l'historique de tous vos paiements reçus.">
                            <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-white dark:bg-gray-800 shadow-md">
                                <i class="fas fa-receipt text-lg {{ request()->routeIs('commission-payments.vendor-history') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-500 dark:text-gray-400' }}"></i>
                            </div>
                            <span>{{ __('Mes paiements') }}</span>
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
        <div class="relative  w-full p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
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
        <nav class="bg-white dark:bg-gray-800 shadow-md rounded-xl mx-6 mt-6 px-6 py-3 hover:shadow-lg transition-shadow duration-300" data-intro-id="navbar" data-title="Navigation" data-intro="La barre de navigation vous permet d'accéder aux fonctionnalités principales et à votre profil.">
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
                            @elseif(request()->routeIs('barters.*'))
                                {{ __('Trocs') }}
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
                        @elseif(request()->routeIs('barters.*'))
                            {{ __('Trocs') }}
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
                    <!-- Toggle dark mode avec effet amélioré -->
                    <button id="themeToggleBtn"
                            class="p-3 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center"
                            data-intro-id="theme"
                            data-title="Mode sombre/clair"
                            data-intro="Basculez entre le mode sombre et clair selon vos préférences.">
                        <i id="sunIcon" class="fas fa-sun text-yellow-500 text-lg"></i>
                        <i id="moonIcon" class="fas fa-moon text-blue-400 text-lg"></i>
                        <span id="themeText" class="ml-2 hidden md:inline">Mode</span>
                    </button>

                    <!-- Toggle sidebar on mobile -->
                    <button @click="toggleSidebar()"
                            class="p-3 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 xl:hidden">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- User menu - REPLACED Alpine.js with Vanilla JS -->
                    <div id="userMenuContainer" class="relative" data-intro-id="user-menu" data-title="Menu utilisateur" data-intro="Accédez à votre profil, paramètres et option de déconnexion.">
                        <button id="userMenuBtn" class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white font-bold focus:outline-none hover:bg-indigo-600 transition-colors">
                            {{ Auth::user()->initials ?? substr(Auth::user()->name, 0, 1) }}
                        </button>

                        <div id="userDropdown" class="hidden absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700">
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
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('system.export-import') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-database mr-2"></i> {{ __('Export/Import Système') }}
                                </a>
                                @endif
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

        <!-- Header conditionnel -->
        @if (isset($header))
            <div class="py-4 px-6 bg-white dark:bg-gray-800 shadow-md rounded-xl mx-6 mt-4">
                {{ $header }}
            </div>
        @endif

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

    <!-- Bouton d'aide -->
    <div class="fixed right-6 bottom-6 z-50">
        <button @click="startTutorial()"
                class="group bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg hover:shadow-xl focus:outline-none transition-all duration-200 pulse-animation flex items-center gap-2"
                data-intro-id="help-button"
                data-title="Aide interactive"
                data-intro="Cliquez sur ce bouton à tout moment pour lancer le tutoriel interactif.">
            <i class="fas fa-question-circle text-xl"></i>
            <span class="hidden md:block font-medium ml-1">Aide</span>
        </button>
    </div>

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
            // Initialisation du mode sombre
            const isDarkMode = localStorage.getItem('dark-mode') === 'true' ||
                (localStorage.getItem('dark-mode') === null &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches);

            // Appliquer l'état initial
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Mettre à jour l'affichage des icônes et du texte
            updateThemeDisplay(isDarkMode);

            // Configurer le bouton de bascule
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const currentDarkMode = document.documentElement.classList.contains('dark');
                    toggleDarkMode(!currentDarkMode);
                });
            }

            // User menu dropdown functionality
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');

            if (userMenuBtn && userDropdown) {
                let isOpen = false;

                // Toggle dropdown
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();

                    if (!isOpen) {
                        // Open dropdown
                        openDropdown();
                    } else {
                        // Close dropdown
                        closeDropdown();
                    }
                });

                // Close when clicking outside
                document.addEventListener('click', function(e) {
                    if (isOpen && !userDropdown.contains(e.target) && e.target !== userMenuBtn) {
                        closeDropdown();
                    }
                });

                // Function to open dropdown with animation
                function openDropdown() {
                    isOpen = true;
                    userDropdown.classList.remove('hidden');

                    // Add enter transition classes
                    userDropdown.classList.add('animate-dropdown-enter');
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'scale(0.95)';

                    // Trigger reflow
                    void userDropdown.offsetWidth;

                    // Start animation
                    userDropdown.style.transition = 'opacity 200ms ease-out, transform 200ms ease-out';
                    userDropdown.style.opacity = '1';
                    userDropdown.style.transform = 'scale(1)';
                }

                // Function to close dropdown with animation
                function closeDropdown() {
                    isOpen = false;

                    // Add leave transition
                    userDropdown.style.transition = 'opacity 100ms ease-in, transform 100ms ease-in';
                    userDropdown.style.opacity = '0';
                    userDropdown.style.transform = 'scale(0.95)';

                    // Hide after animation completes
                    setTimeout(() => {
                        userDropdown.classList.add('hidden');
                        userDropdown.classList.remove('animate-dropdown-enter');
                    }, 100);
                }
            }

            // Vérifiez si l'utilisateur a déjà vu le tutorial
            const hasSeenHelp = localStorage.getItem('has-seen-help-toast');

            if (!hasSeenHelp) {
                // Notification pour le tutoriel
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Besoin d\'aide ?',
                        text: 'Cliquez sur le bouton "Aide" en bas à droite pour démarrer le tutoriel interactif',
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: true,
                        confirmButtonText: 'Ne plus afficher',
                        timer: 8000,
                        timerProgressBar: true,
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                        iconColor: '#4F46E5'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.setItem('has-seen-help-toast', 'true');
                        }
                    });
                }, 2000);
            }

            // Écouter les changements de préférence de mode sombre du système
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                if (localStorage.getItem('dark-mode') === null) {
                    toggleDarkMode(event.matches);
                }
            });
        });

        // Fonction pour basculer le mode sombre
        function toggleDarkMode(enableDark) {
            if (enableDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Sauvegarder dans localStorage
            localStorage.setItem('dark-mode', enableDark ? 'true' : 'false');

            // Mettre à jour l'affichage
            updateThemeDisplay(enableDark);

            // Mettre à jour les tooltips IntroJS si le tutoriel est en cours
            if (window.isRunningTutorial) {
                const tooltips = document.querySelectorAll('.introjs-tooltip');
                tooltips.forEach(tooltip => {
                    if (enableDark) {
                        tooltip.classList.add('dark-tooltip');
                    } else {
                        tooltip.classList.remove('dark-tooltip');
                    }
                });
            }
        }

        // Fonction pour mettre à jour l'affichage des icônes et du texte
        function updateThemeDisplay(isDark) {
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');
            const themeText = document.getElementById('themeText');

            if (sunIcon && moonIcon && themeText) {
                if (isDark) {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                    themeText.textContent = 'Mode clair';
                } else {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                    themeText.textContent = 'Mode sombre';
                }
            }
        }
    </script>

    @stack('scripts')
</div>
</body>
</html>
