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
            <div class="flex space-x-2">
                <a href="{{ route('commissions.index') }}" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">
                    {{ __('Retour aux commissions') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques globales -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-2 text-gray-800">{{ __('Total des commissions') }}</h3>
                    <p class="text-3xl font-bold text-indigo-600">{{ number_format($totalStats['total'], 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-2 text-gray-800">{{ __('À payer') }}</h3>
                    <p class="text-3xl font-bold text-amber-600">{{ number_format($totalStats['pending'], 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-2 text-gray-800">{{ __('Déjà payé') }}</h3>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($totalStats['paid'], 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            <!-- Commissions par vendeur -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-lg mb-4 text-gray-800">{{ __('Commissions par vendeur') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Vendeur') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('À payer') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Déjà payé') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Dernier paiement') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($commissionsByVendor as $vendorId => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $data['vendor']->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $data['vendor']->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium {{ $data['stats']['pending_amount'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                                {{ number_format($data['stats']['pending_amount'], 0, ',', ' ') }} FCFA
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $data['stats']['pending_count'] }} commission(s)</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-green-600">
                                                {{ number_format($data['stats']['paid_amount'], 0, ',', ' ') }} FCFA
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $data['stats']['paid_count'] }} commission(s)</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ number_format($data['stats']['total_amount'], 0, ',', ' ') }} FCFA
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                @if($data['stats']['last_paid'])
                                                    {{ $data['stats']['last_paid']->paid_at->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('commissions.vendor-report', $data['vendor']) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                {{ __('Détails') }}
                                            </a>
                                            @if($data['stats']['pending_amount'] > 0)
                                                <a href="#" class="pay-all-btn text-green-600 hover:text-green-900" data-vendor-id="{{ $data['vendor']->id }}" data-vendor-name="{{ $data['vendor']->name }}" data-amount="{{ $data['stats']['pending_amount'] }}">
                                                    {{ __('Payer tout') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Commissions en attente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-lg mb-4 text-gray-800">{{ __('Commissions en attente') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Vendeur') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Montant') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingCommissions as $commission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $commission->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($commission->bill)
                                                    <a href="{{ route('bills.show', $commission->bill) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $commission->bill->reference }}
                                                    </a>
                                                @else
                                                    {{ $commission->description }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $commission->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($commission->amount, 0, ',', ' ') }} FCFA</div>
                                            <div class="text-xs text-gray-500">{{ $commission->rate }}% sur {{ number_format($commission->base_amount, 0, ',', ' ') }} FCFA</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $commission->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                {{ __('Voir') }}
                                            </a>
                                            <a href="#" class="pay-commission-btn text-green-600 hover:text-green-900" 
                                               data-commission-id="{{ $commission->id }}" 
                                               data-amount="{{ $commission->amount }}"
                                               data-vendor-name="{{ $commission->user->name }}">
                                                {{ __('Payer') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $pendingCommissions->links() }}
                    </div>
                </div>
            </div>

            <!-- Derniers paiements effectués -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4 text-gray-800">{{ __('Derniers paiements effectués') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Vendeur') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Montant') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Méthode') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payé par') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->reference }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $payment->vendor->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $payment->payment_method }}
                                            </span>
                                            @if($payment->payment_reference)
                                                <div class="text-xs text-gray-500">{{ $payment->payment_reference }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $payment->paid_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $payment->paidByUser->name }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

    <!-- Scripts specifiques à cette page -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    fetch(`/api/commissions/${vendorId}?shop_id={{ $shop->id }}`)
                        .then(response => response.json())
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
                            console.error('Erreur lors de la récupération des commissions:', error);
                            alert('Erreur lors de la récupération des commissions. Veuillez réessayer.');
                        });
                });
            });

            cancelPayAll.addEventListener('click', function() {
                payAllModal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout> 