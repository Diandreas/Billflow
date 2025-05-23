<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Commissions Boutique') }} - {{ $shop->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Gestion des commissions par boutique') }}
                </p>
            </div>
            <div>
                <a href="{{ route('commissions.index') }}" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 inline-flex items-center">
                    <i class="bi bi-arrow-left mr-1"></i> {{ __('Retour aux commissions') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques globales -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex items-center">
                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                        <i class="bi bi-cash-stack text-xl text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Total des commissions') }}</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($totalStats['total_amount'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex items-center">
                    <div class="bg-amber-100 p-3 rounded-full mr-4">
                        <i class="bi bi-hourglass-split text-xl text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('À payer') }}</p>
                        <p class="text-2xl font-bold text-amber-600">{{ number_format($totalStats['pending_amount'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="bi bi-check-circle text-xl text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Déjà payé') }}</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($totalStats['paid_amount'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>

            <!-- Système d'onglets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex flex-wrap -mb-px" id="tabs-nav">
                        <button class="tab-button active px-5 py-3 border-b-2 border-indigo-500 text-indigo-600 font-medium text-sm" data-target="tab-vendors">
                            <i class="bi bi-people mr-1"></i> Par vendeur
                        </button>
                        <button class="tab-button px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-target="tab-pending">
                            <i class="bi bi-hourglass mr-1"></i> À payer
                            <span class="ml-1 bg-amber-100 text-amber-800 text-xs font-semibold px-2 rounded-full">{{ $pendingCommissions->total() }}</span>
                        </button>
                        <button class="tab-button px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-target="tab-payments">
                            <i class="bi bi-credit-card mr-1"></i> Paiements récents
                        </button>
                        <button class="tab-button px-5 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-target="tab-all">
                            <i class="bi bi-list-ul mr-1"></i> Toutes les commissions
                        </button>
                    </nav>
                </div>

                <!-- Contenu des onglets -->
                <div class="tab-content">
                    <!-- Onglet 1: Par vendeur -->
                    <div id="tab-vendors" class="tab-pane active block p-4">
                        <div class="mb-4">
                            <input type="text" id="vendor-search" placeholder="Rechercher un vendeur..." class="w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($vendors as $vendor)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($vendor->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="font-medium text-gray-900">{{ $vendor->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $vendor->email }}</p>
                                        </div>
                                    </div>

                                    <div class="my-3 border-t border-b border-gray-100 py-2">
                                        <div class="flex justify-between items-center my-1">
                                            <span class="text-sm text-gray-500">À payer:</span>
                                            <span class="font-medium {{ $vendorStats[$vendor->id]['pending_amount'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                            {{ number_format($vendorStats[$vendor->id]['pending_amount'], 0, ',', ' ') }} FCFA
                                        </span>
                                        </div>
                                        <div class="flex justify-between items-center my-1">
                                            <span class="text-sm text-gray-500">Payé:</span>
                                            <span class="font-medium text-green-600">
                                            {{ number_format($vendorStats[$vendor->id]['paid_amount'], 0, ',', ' ') }} FCFA
                                        </span>
                                        </div>
                                        <div class="flex justify-between items-center my-1">
                                            <span class="text-sm text-gray-500">Dernier paiement:</span>
                                            <span class="text-sm text-gray-700">
                                            @if(isset($vendorStats[$vendor->id]['last_payment']) && $vendorStats[$vendor->id]['last_payment'])
                                                    {{ $vendorStats[$vendor->id]['last_payment']->paid_at->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                        </span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-2 mt-2">
                                        <a href="{{ route('commissions.vendor-report', $vendor) }}" class="px-3 py-1 inline-flex items-center text-sm font-medium rounded text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                            <i class="bi bi-file-earmark-text mr-1"></i> Détails
                                        </a>
                                        @if($vendorStats[$vendor->id]['pending_amount'] > 0)
                                            <a href="#" class="pay-all-btn px-3 py-1 inline-flex items-center text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700" data-vendor-id="{{ $vendor->id }}" data-vendor-name="{{ $vendor->name }}" data-amount="{{ $vendorStats[$vendor->id]['pending_amount'] }}">
                                                <i class="bi bi-cash mr-1"></i> Payer
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Onglet 2: À payer -->
                    <div id="tab-pending" class="tab-pane hidden p-4">
                        <div class="bg-gray-50 rounded-lg p-3 mb-4 flex flex-wrap gap-3">
                            <select id="vendor-filter" class="border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tous les vendeurs</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>

                            <select id="date-filter" class="border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Toutes les dates</option>
                                <option value="week">Cette semaine</option>
                                <option value="month">Ce mois</option>
                                <option value="quarter">Ce trimestre</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            @foreach($pendingCommissions as $commission)
                                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden border border-gray-200">
                                    <div class="bg-gray-50 p-2 flex justify-between items-center">
                                    <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold">
                                        {{ $commission->type }}
                                    </span>
                                        <span class="text-xs text-gray-500">{{ $commission->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="p-3">
                                        <div class="flex items-center mb-2">
                                            <i class="bi bi-person-circle text-gray-500 mr-2"></i>
                                            <span class="font-medium">{{ $commission->user->name }}</span>
                                        </div>
                                        @if($commission->bill)
                                            <div class="flex items-center mb-2">
                                                <i class="bi bi-receipt text-gray-500 mr-2"></i>
                                                <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 hover:underline">
                                                    {{ $commission->bill->reference }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center mb-2">
                                                <i class="bi bi-info-circle text-gray-500 mr-2"></i>
                                                <span>{{ $commission->description }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <i class="bi bi-cash-coin text-gray-500 mr-2"></i>
                                            <span class="font-bold">{{ number_format($commission->amount, 0, ',', ' ') }} FCFA</span>
                                            <span class="text-xs text-gray-500 ml-2">({{ $commission->rate }}%)</span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-2 flex justify-end space-x-2">
                                        <a href="{{ route('commissions.show', $commission) }}" class="px-2 py-1 inline-flex items-center text-xs font-medium rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                            <i class="bi bi-eye mr-1"></i> Voir
                                        </a>
                                        <a href="#" class="pay-commission-btn px-2 py-1 inline-flex items-center text-xs font-medium rounded bg-green-600 text-white hover:bg-green-700"
                                           data-commission-id="{{ $commission->id }}"
                                           data-amount="{{ $commission->amount }}"
                                           data-vendor-name="{{ $commission->user->name }}">
                                            <i class="bi bi-cash mr-1"></i> Payer
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $pendingCommissions->links() }}
                        </div>
                    </div>

                    <!-- Onglet 3: Paiements récents -->
                    <div id="tab-payments" class="tab-pane hidden p-4">
                        <div class="relative pl-8 space-y-6">
                            @foreach($recentPayments as $payment)
                                <div class="relative mb-6">
                                    <!-- Élément de la timeline -->
                                    <div class="absolute -left-4 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="bi bi-check-lg text-white"></i>
                                    </div>
                                    <!-- Contenu du paiement -->
                                    <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs text-gray-500">{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                                            <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                                            {{ $payment->payment_method }}
                                        </span>
                                        </div>
                                        <div class="space-y-1">
                                            <h4 class="font-medium">{{ $payment->reference }}</h4>
                                            <div class="text-sm">
                                                <i class="bi bi-person text-gray-500 mr-1"></i> {{ $payment->vendor->name }}
                                            </div>
                                            <div class="font-bold text-green-600">
                                                {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <i class="bi bi-person-check mr-1"></i> Payé par {{ $payment->paidByUser->name }}
                                            </div>
                                            @if($payment->payment_reference)
                                                <div class="text-sm text-gray-500">
                                                    <i class="bi bi-hash mr-1"></i> {{ $payment->payment_reference }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Onglet 4: Toutes les commissions -->
                    <div id="tab-all" class="tab-pane hidden p-4">
                        <div class="bg-gray-50 rounded-lg p-3 mb-4 flex flex-wrap gap-3">
                            <select id="status-filter" class="border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tous les statuts</option>
                                <option value="paid">Payé</option>
                                <option value="pending">En attente</option>
                            </select>

                            <select id="all-vendor-filter" class="border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tous les vendeurs</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>

                            <input type="text" id="commission-search" placeholder="Rechercher..." class="border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Tableau compact des commissions -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendeur</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($allCommissions as $commission)
                                    <tr class="hover:bg-gray-50 {{ $commission->is_paid ? 'bg-green-50' : '' }}">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="font-medium text-sm">{{ $commission->reference }}</span>
                                            @if($commission->bill)
                                                <a href="{{ route('bills.show', $commission->bill) }}" class="block text-xs text-indigo-600 hover:underline">
                                                    <i class="bi bi-receipt"></i> {{ $commission->bill->reference }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $commission->user->name }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="text-sm">{{ $commission->created_at->format('d/m/Y') }}</span>
                                            @if($commission->is_paid && $commission->paid_at)
                                                <span class="block text-xs text-green-600">
                                                <i class="bi bi-check-circle"></i> {{ $commission->paid_at->format('d/m/Y') }}
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="text-sm font-medium">{{ number_format($commission->amount, 0, ',', ' ') }} FCFA</span>
                                            <span class="block text-xs text-gray-500">({{ $commission->rate }}%)</span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">
                                            @if($commission->is_paid)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 inline-flex items-center">
                                                    <i class="bi bi-check-circle mr-1"></i> Payée
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 inline-flex items-center">
                                                    <i class="bi bi-hourglass mr-1"></i> En attente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('commissions.show', $commission) }}" class="p-1 rounded inline-flex items-center justify-center bg-purple-100 text-purple-700 hover:bg-purple-200" title="{{ __('Voir') }}">
                                                    <i class="bi bi-eye text-xs"></i>
                                                </a>
                                                @if(!$commission->is_paid)
                                                    <a href="#" class="pay-commission-btn p-1 rounded inline-flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200"
                                                       data-commission-id="{{ $commission->id }}"
                                                       data-amount="{{ $commission->amount }}"
                                                       data-vendor-name="{{ $commission->user->name }}"
                                                       title="{{ __('Payer') }}">
                                                        <i class="bi bi-cash text-xs"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $allCommissions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de paiement de commission individuelle -->
    <div id="payCommissionModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="payCommissionForm" action="" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Payer la commission
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Vous allez payer une commission de <span id="commissionAmount"></span> FCFA pour <span id="commissionVendor"></span>.
                                    </p>
                                    <div class="mb-4">
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                                        <select id="payment_method" name="payment_method" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="cash">Espèces</option>
                                            <option value="bank_transfer">Virement bancaire</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="check">Chèque</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="payment_reference" class="block text-sm font-medium text-gray-700">Référence du paiement (optionnel)</label>
                                        <input type="text" name="payment_reference" id="payment_reference" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="mb-4">
                                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (optionnel)</label>
                                        <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmer le paiement
                        </button>
                        <button type="button" id="cancelPayment" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de paiement groupé -->
    <div id="payAllModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="payAllForm" action="{{ route('commissions.pay-batch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="vendor_id">
                    <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Payer toutes les commissions
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Vous allez payer toutes les commissions en attente pour <span id="vendor_name"></span>, pour un total de <span id="total_amount"></span> FCFA.
                                    </p>
                                    <div class="mb-4">
                                        <label for="batch_payment_method" class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                                        <select id="batch_payment_method" name="payment_method" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="cash">Espèces</option>
                                            <option value="bank_transfer">Virement bancaire</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="check">Chèque</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="batch_payment_reference" class="block text-sm font-medium text-gray-700">Référence du paiement (optionnel)</label>
                                        <input type="text" name="payment_reference" id="batch_payment_reference" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="mb-4">
                                        <label for="batch_notes" class="block text-sm font-medium text-gray-700">Notes (optionnel)</label>
                                        <textarea id="batch_notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmer le paiement groupé
                        </button>
                        <button type="button" id="cancelPayAll" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Désactiver tous les onglets
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('border-indigo-500');
                        btn.classList.remove('text-indigo-600');
                        btn.classList.add('border-transparent');
                        btn.classList.add('text-gray-500');
                    });

                    tabPanes.forEach(pane => pane.classList.add('hidden'));

                    // Activer l'onglet cliqué
                    this.classList.add('active');
                    this.classList.add('border-indigo-500');
                    this.classList.add('text-indigo-600');
                    this.classList.remove('border-transparent');
                    this.classList.remove('text-gray-500');

                    const target = this.getAttribute('data-target');
                    const pane = document.getElementById(target);
                    if (pane) {
                        pane.classList.remove('hidden');
                        pane.classList.add('block');
                    }
                });
            });

            // Gestion du paiement individuel
            const payButtons = document.querySelectorAll('.pay-commission-btn');
            const payCommissionModal = document.getElementById('payCommissionModal');
            const payCommissionForm = document.getElementById('payCommissionForm');
            const cancelPayment = document.getElementById('cancelPayment');
            const commissionAmount = document.getElementById('commissionAmount');
            const commissionVendor = document.getElementById('commissionVendor');

            payButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const commissionId = this.getAttribute('data-commission-id');
                    const amount = this.getAttribute('data-amount');
                    const vendorName = this.getAttribute('data-vendor-name');

                    payCommissionForm.action = `/commissions/${commissionId}/pay`;
                    commissionAmount.textContent = new Intl.NumberFormat('fr-FR').format(amount);
                    commissionVendor.textContent = vendorName;

                    payCommissionModal.classList.remove('hidden');
                });
            });

            cancelPayment.addEventListener('click', function() {
                payCommissionModal.classList.add('hidden');
            });

            // Gestion du paiement groupé
            const payAllButtons = document.querySelectorAll('.pay-all-btn');
            const payAllModal = document.getElementById('payAllModal');
            const payAllForm = document.getElementById('payAllForm');
            const cancelPayAll = document.getElementById('cancelPayAll');
            const vendorIdInput = document.getElementById('vendor_id');
            const vendorNameSpan = document.getElementById('vendor_name');
            const totalAmountSpan = document.getElementById('total_amount');

            payAllButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const vendorId = this.getAttribute('data-vendor-id');
                    const vendorName = this.getAttribute('data-vendor-name');
                    const amount = this.getAttribute('data-amount');

                    vendorIdInput.value = vendorId;
                    vendorNameSpan.textContent = vendorName;
                    totalAmountSpan.textContent = new Intl.NumberFormat('fr-FR').format(amount);

                    // Récupérer les commissions du vendeur avec AJAX
                    // Modifiez cette partie dans shop-report.blade.php
                    fetch(`/get-pending-commissions/${vendorId}?shop_id={{ $shop->id }}`)
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    console.error('Erreur du serveur:', data);
                                    throw new Error('Erreur du serveur: ' + response.status);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Supprimer les anciens champs de commission_ids s'il y en a
                            document.querySelectorAll('input[name="commission_ids[]"]').forEach(el => el.remove());

                            // Ajouter un champ caché pour chaque commission
                            data.commissions.forEach(commission => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'commission_ids[]';
                                input.value = commission.id;
                                payAllForm.appendChild(input);
                            });

                            payAllModal.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Erreur détaillée:', error);
                            alert('Erreur lors de la récupération des commissions. Veuillez réessayer.');
                        });
                });
            });

            cancelPayAll.addEventListener('click', function() {
                payAllModal.classList.add('hidden');
            });

            // Fonctionnalités de recherche et filtres
            const vendorSearch = document.getElementById('vendor-search');
            if (vendorSearch) {
                vendorSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const vendorCards = document.querySelectorAll('#tab-vendors .bg-white.border');

                    vendorCards.forEach(card => {
                        const vendorName = card.querySelector('.font-medium').textContent.toLowerCase();
                        const vendorEmail = card.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();

                        if (vendorName.includes(searchTerm) || vendorEmail.includes(searchTerm)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Filtres des commissions en attente
            const vendorFilter = document.getElementById('vendor-filter');
            const dateFilter = document.getElementById('date-filter');

            if (vendorFilter && dateFilter) {
                const applyFilters = function() {
                    const vendorId = vendorFilter.value;
                    const dateRange = dateFilter.value;

                    // Ici vous ajouteriez la logique pour filtrer les commissions
                    // Cette partie nécessiterait une implémentation AJAX ou une mise à jour de la page
                    console.log('Filtres appliqués:', { vendorId, dateRange });
                };

                vendorFilter.addEventListener('change', applyFilters);
                dateFilter.addEventListener('change', applyFilters);
            }

            // Filtres pour toutes les commissions
            const statusFilter = document.getElementById('status-filter');
            const allVendorFilter = document.getElementById('all-vendor-filter');
            const commissionSearch = document.getElementById('commission-search');

            if (statusFilter && allVendorFilter && commissionSearch) {
                const applyAllFilters = function() {
                    const status = statusFilter.value;
                    const vendorId = allVendorFilter.value;
                    const searchTerm = commissionSearch.value.toLowerCase();

                    const commissionRows = document.querySelectorAll('#tab-all tbody tr');

                    commissionRows.forEach(row => {
                        let showRow = true;

                        // Filtre par statut
                        if (status) {
                            const isPaid = row.classList.contains('bg-green-50');
                            if ((status === 'paid' && !isPaid) || (status === 'pending' && isPaid)) {
                                showRow = false;
                            }
                        }

                        // Filtre par vendeur
                        if (showRow && vendorId) {
                            // Cette partie nécessiterait un attribut de données sur chaque ligne
                            // pour identifier le vendeur, ou une requête AJAX
                        }

                        // Filtre par recherche
                        if (showRow && searchTerm) {
                            const rowText = row.textContent.toLowerCase();
                            if (!rowText.includes(searchTerm)) {
                                showRow = false;
                            }
                        }

                        row.style.display = showRow ? '' : 'none';
                    });
                };

                statusFilter.addEventListener('change', applyAllFilters);
                allVendorFilter.addEventListener('change', applyAllFilters);
                commissionSearch.addEventListener('input', applyAllFilters);
            }
        });
    </script>
</x-app-layout>
