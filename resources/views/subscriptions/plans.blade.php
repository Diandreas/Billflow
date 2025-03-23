<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Plans d\'abonnement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (isset($activeSubscription))
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Votre abonnement actuel</h3>
                        
                        <div class="flex flex-col md:flex-row items-start justify-between">
                            <div>
                                <h4 class="text-xl font-bold">{{ $activeSubscription->plan->name }}</h4>
                                <p class="text-gray-500">{{ $activeSubscription->plan->description }}</p>
                                <p class="mt-2">
                                    <span class="font-semibold">Valide jusqu'au:</span> 
                                    {{ $activeSubscription->ends_at->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            <div class="mt-4 md:mt-0">
                                <p class="mb-1">
                                    <span class="font-semibold">SMS restants:</span> 
                                    <span class="text-blue-600 font-bold">{{ $activeSubscription->sms_remaining }}</span>
                                </p>
                                <p class="mb-1">
                                    <span class="font-semibold">SMS personnels restants:</span>
                                    <span class="text-purple-600 font-bold">{{ $activeSubscription->sms_personal_remaining }}</span>
                                </p>
                                <p>
                                    <span class="font-semibold">Campagnes utilisées:</span>
                                    <span class="text-green-600 font-bold">{{ $activeSubscription->campaigns_used }}/{{ $activeSubscription->plan->campaigns_per_cycle }}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex space-x-4">
                            <a href="{{ route('subscriptions.show', $activeSubscription) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Détails de l'abonnement
                            </a>
                            <a href="{{ route('subscriptions.recharge.form') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Recharger des SMS
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Plans mensuels -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Plans Mensuels</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($plans['monthly'] ?? [] as $plan)
                            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="p-6 bg-indigo-50 border-b">
                                    <h4 class="text-xl font-bold text-indigo-900">{{ $plan->name }}</h4>
                                    <p class="text-2xl font-bold mt-2">{{ $plan->formatted_price }}</p>
                                    <p class="text-indigo-700 font-medium">par mois</p>
                                </div>
                                <div class="p-6">
                                    <p class="text-gray-500 mb-4">{{ $plan->description }}</p>
                                    <ul class="space-y-2 mb-6">
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Jusqu'à {{ $plan->max_clients }} clients
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->campaigns_per_cycle }} campagnes par mois
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->sms_quota }} SMS au total
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->sms_personal_quota }} SMS personnels
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Report: {{ $plan->sms_rollover_percent }}% des SMS non utilisés
                                        </li>
                                    </ul>
                                    <a href="{{ route('subscriptions.create', $plan) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Souscrire
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Plans annuels -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Plans Annuels</h3>
                        <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">Économisez jusqu'à 15%</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($plans['yearly'] ?? [] as $plan)
                            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="p-6 bg-indigo-50 border-b">
                                    <h4 class="text-xl font-bold text-indigo-900">{{ $plan->name }}</h4>
                                    <p class="text-2xl font-bold mt-2">{{ $plan->formatted_price }}</p>
                                    <p class="text-indigo-700 font-medium">par an</p>
                                </div>
                                <div class="p-6">
                                    <p class="text-gray-500 mb-4">{{ $plan->description }}</p>
                                    <ul class="space-y-2 mb-6">
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Jusqu'à {{ $plan->max_clients }} clients
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->campaigns_per_cycle }} campagnes par an
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->sms_quota }} SMS au total
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $plan->sms_personal_quota }} SMS personnels
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Report: {{ $plan->sms_rollover_percent }}% des SMS non utilisés
                                        </li>
                                    </ul>
                                    <a href="{{ route('subscriptions.create', $plan) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Souscrire
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Options complémentaires -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Options complémentaires</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Option 1: Recharge SMS -->
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6 bg-green-50 border-b">
                                <h4 class="text-xl font-bold text-green-900">Recharge SMS</h4>
                                <p class="text-2xl font-bold mt-2">1.000 FCFA</p>
                                <p class="text-green-700 font-medium">pour 100 SMS supplémentaires</p>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-500 mb-4">Besoin de plus de SMS pour vos campagnes marketing ou messages personnels ? Rechargez facilement votre compte !</p>
                                <ul class="space-y-2 mb-6">
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Achat rapide via Mobile Money
                                    </li>
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Crédit disponible immédiatement
                                    </li>
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Pas d'expiration des SMS supplémentaires
                                    </li>
                                </ul>
                                <a href="{{ route('subscriptions.recharge.form') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                    Recharger des SMS
                                </a>
                            </div>
                        </div>
                        
                        <!-- Option 2: Extension clients -->
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6 bg-blue-50 border-b">
                                <h4 class="text-xl font-bold text-blue-900">Extension clients</h4>
                                <p class="text-2xl font-bold mt-2">2.000 FCFA</p>
                                <p class="text-blue-700 font-medium">pour 100 clients supplémentaires</p>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-500 mb-4">Votre entreprise se développe ? Augmentez la capacité de votre base de données clients.</p>
                                <ul class="space-y-2 mb-6">
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Augmentez votre limite de clients
                                    </li>
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Gestion illimitée des contacts
                                    </li>
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Extension valable pendant toute la durée de l'abonnement
                                    </li>
                                </ul>
                                <a href="#" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                    Étendre ma base clients
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gestion des SMS épuisés -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Gestion des SMS épuisés</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h4 class="font-semibold text-lg">Notification automatique</h4>
                            </div>
                            <p class="text-gray-600">Vous recevrez une notification automatique lorsque vous aurez utilisé 80% de votre quota de SMS.</p>
                        </div>
                        
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h4 class="font-semibold text-lg">Achat rapide</h4>
                            </div>
                            <p class="text-gray-600">Option d'achat rapide de SMS supplémentaires via Mobile Money directement depuis votre tableau de bord.</p>
                        </div>
                        
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h4 class="font-semibold text-lg">Mise en pause</h4>
                            </div>
                            <p class="text-gray-600">Possibilité de mettre en pause certaines campagnes pour prioriser les messages importants et optimiser l'utilisation de vos SMS.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 