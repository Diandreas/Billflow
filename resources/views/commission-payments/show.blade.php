<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Détails du paiement') }} | {{ $payment->reference }}
        </h2>
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'route' => 'dashboard'],
            ['label' => 'Paiements de commissions', 'route' => 'commission-payments.index'],
            ['label' => $payment->reference]
        ]" />
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Détails du paiement -->
                        <div class="lg:col-span-2">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                    {{ __('Informations du paiement') }}
                                </h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Référence') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $payment->reference }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date de paiement') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $payment->paid_at->format('d/m/Y H:i') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Vendeur') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        <a href="{{ route('commission-payments.vendor-history', $payment->user) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            {{ $payment->user->name }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Boutique') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        <a href="{{ route('commission-payments.shop-history', $payment->shop) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            {{ $payment->shop->name }}
                                        </a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Méthode de paiement') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Payé par') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $payment->paidByUser->name ?? 'N/A' }}</p>
                                </div>
                                
                                @if($payment->payment_reference)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Référence de transaction') }}</p>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $payment->payment_reference }}</p>
                                </div>
                                @endif
                            </div>
                            
                            @if($payment->notes)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Notes') }}</p>
                                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $payment->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Montant et actions -->
                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="text-center mb-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Montant payé') }}</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                                
                                <div class="border-t border-gray-200 dark:border-gray-600 my-4 pt-4">
                                    <div class="flex flex-col items-center text-center bg-green-50 dark:bg-green-900/20 p-4 rounded-md">
                                        <div class="h-12 w-12 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-300 rounded-full flex items-center justify-center mb-3">
                                            <i class="bi bi-check-lg text-2xl"></i>
                                        </div>
                                        <h4 class="font-medium text-green-700 dark:text-green-300">{{ __('Paiement effectué') }}</h4>
                                        <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                            {{ $payment->paid_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('commission-payments.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                            <i class="bi bi-arrow-left mr-2"></i>
                                            {{ __('Retour à la liste') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commissions associées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        {{ __('Commissions associées') }}
                    </h3>
                    
                    @if($commissions->isEmpty())
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md p-4 my-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        {{ __('Aucune commission associée à ce paiement.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Référence') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Montant') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date de création') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($commissions as $commission)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                <a href="{{ route('commissions.show', $commission) }}">{{ $commission->reference }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($commission->type === 'vente') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300
                                                    @elseif($commission->type === 'troc') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300
                                                    @else bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 @endif">
                                                    {{ $commission->type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                                {{ number_format($commission->amount, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $commission->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                    <i class="bi bi-eye mr-1"></i> {{ __('Voir') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 