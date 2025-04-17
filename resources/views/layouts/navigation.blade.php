<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="bi bi-house-door mr-1"></i> {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bills.index')" :active="request()->routeIs('bills.*')">
                         <i class="bi bi-receipt mr-1"></i> {{ __('Factures') }}
                    </x-nav-link>
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                         <i class="bi bi-people mr-1"></i> {{ __('Clients') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                         <i class="bi bi-box-seam mr-1"></i> {{ __('Produits') }}
                    </x-nav-link>
                    <x-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')">
                         <i class="bi bi-archive mr-1"></i> {{ __('Inventaire') }}
                    </x-nav-link>
                    <x-nav-link :href="route('shops.index')" :active="request()->routeIs('shops.*')">
                         <i class="bi bi-shop mr-1"></i> {{ __('Boutiques') }}
                    </x-nav-link>
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                         <i class="bi bi-gear mr-1"></i> {{ __('Paramètres') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Notifications and Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                {{-- Temporairement désactivé
                 <div x-data="{
                    notificationsOpen: false, 
                    notifications: [], 
                    unreadCount: 0,
                    fetchNotifications() {
                        fetch('{{ route("notifications.index") }}', { headers: { 'Accept': 'application/json' } })
                            .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok.'))
                            .then(data => {
                                if(data && data.notifications) {
                                    this.notifications = data.notifications.data; 
                                    this.unreadCount = data.unreadCount;
                                } else {
                                    console.error('Invalid data structure received:', data);
                                    this.notifications = [];
                                    this.unreadCount = 0;
                                }
                            }).catch(error => {
                                console.error('Fetch notifications error:', error);
                                this.notifications = [];
                                this.unreadCount = 0;
                            });
                    },
                    markAsRead(notificationId, event) {
                        fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(response => response.ok ? response.json() : Promise.reject('Failed to mark as read'))
                          .then(data => {
                              if(data.success) {
                                  this.unreadCount = data.unreadCount;
                                  let notif = this.notifications.find(n => n.id === notificationId);
                                  if (notif) notif.read_at = new Date().toISOString();
                              }
                          }).catch(error => console.error('Mark as read error:', error));
                    },
                    markAllAsRead() {
                        fetch('{{ route("notifications.read-all") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }).then(response => response.ok ? response.json() : Promise.reject('Failed to mark all as read'))
                          .then(data => {
                              if(data.success) {
                                  this.unreadCount = 0;
                                  this.notifications.forEach(n => n.read_at = new Date().toISOString());
                              }
                          }).catch(error => console.error('Mark all as read error:', error));
                    },
                    formatDate(dateString) {
                        if (!dateString) return '';
                        try {
                            // Essayer de déterminer si c'est 'il y a x temps' ou une date
                            // Simple heuristique: si ça ne contient pas ':', c'est probablement déjà formaté
                            if (!dateString.includes(':') && isNaN(Date.parse(dateString))) {
                                return dateString; // Probablement déjà 'diffForHumans'
                            }
                            const date = new Date(dateString);
                            const now = new Date();
                            const diffSeconds = Math.round((now - date) / 1000);
                            const diffMinutes = Math.round(diffSeconds / 60);
                            const diffHours = Math.round(diffMinutes / 60);
                            const diffDays = Math.round(diffHours / 24);

                            if (diffSeconds < 60) return `à l'instant`;
                            if (diffMinutes < 60) return `il y a ${diffMinutes} min`;
                            if (diffHours < 24) return `il y a ${diffHours} h`;
                            if (diffDays === 1) return `hier`;
                            if (diffDays < 7) return `il y a ${diffDays} j`;
                            
                            const options = { year: 'numeric', month: 'short', day: 'numeric' };
                            return date.toLocaleDateString('fr-FR', options);
                        } catch (e) {
                            console.error('Error formatting date:', dateString, e);
                            return dateString; // Retourne la chaîne originale en cas d'erreur
                        }
                    },
                    getNotificationIcon(type) {
                        if (!type) return 'bi-info-circle text-gray-600';
                        if (type.includes('LowStockNotification')) return 'bi-exclamation-triangle text-yellow-600';
                        if (type.includes('PaymentReceivedNotification')) return 'bi-check-circle text-green-600';
                        if (type.includes('NewBillNotification')) return 'bi-receipt text-blue-600';
                        return 'bi-info-circle text-gray-600';
                    },
                    getNotificationColor(type) {
                        if (!type) return 'bg-gray-100';
                        if (type.includes('LowStockNotification')) return 'bg-yellow-100';
                        if (type.includes('PaymentReceivedNotification')) return 'bg-green-100';
                        if (type.includes('NewBillNotification')) return 'bg-blue-100';
                        return 'bg-gray-100';
                    }
                 }"
                 x-init="fetchNotifications(); setInterval(fetchNotifications, 60000)" > 
                 
                <!-- Notifications Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="96">
                        <x-slot name="trigger">
                            <button @click="notificationsOpen = !notificationsOpen" 
                                    class="relative inline-flex items-center p-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <i class="bi bi-bell text-xl"></i>
                                <template x-if="unreadCount > 0">
                                    <span x-text="unreadCount" 
                                          class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"></span>
                                </template>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                           <!-- Contenu du dropdown -->
                        </x-slot>
                    </x-dropdown>
                </div>
                --}}

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                 {{-- Icône mobile des notifications - Temporairement désactivé
                 <button @click="notificationsOpen = !notificationsOpen; fetchNotifications()" 
                         class="relative inline-flex items-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out mr-2">
                    <i class="bi bi-bell text-xl"></i>
                    <template x-if="unreadCount > 0">
                        <span x-text="unreadCount" 
                              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"></span>
                    </template>
                </button>
                 --}}
                
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Liens de navigation responsive existants -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bills.index')" :active="request()->routeIs('bills.*')">
                {{ __('Factures') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                {{ __('Clients') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Produits') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')">
                {{ __('Inventaire') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('shops.index')" :active="request()->routeIs('shops.*')">
                {{ __('Boutiques') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                {{ __('Paramètres') }}
            </x-responsive-nav-link>
        </div>

        <!-- Options de langue responsive -->
        <div class="mt-4 text-center">
            <a href="{{ route('language.switch', 'en') }}" class="inline-block px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 mr-2">English</a>
            <a href="{{ route('language.switch', 'fr') }}" class="inline-block px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">Français</a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 mt-4">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
