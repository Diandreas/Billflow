<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-500 py-3 px-4 rounded-lg shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-white">
                        {{ __('Commissions') }}
                    </h2>
                    <p class="mt-1 text-sm text-white text-opacity-90">
                        {{ __('Gestion des commissions des vendeurs') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('commissions.create') }}" class="inline-flex items-center px-3 py-2 text-sm bg-white bg-opacity-90 text-purple-700 rounded-md hover:bg-white">
                        <i class="bi bi-plus-lg mr-1"></i>
                        {{ __('Nouvelle Commission') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Statistiques des commissions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 mr-3">
                            <i class="bi bi-cash-stack text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Total Commissions</div>
                            <div class="text-lg font-semibold">{{ number_format($stats['total_commissions'], 2, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 mr-3">
                            <i class="bi bi-hourglass-split text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Commissions en attente</div>
                            <div class="text-lg font-semibold">{{ number_format($stats['pending_commissions'], 2, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 mr-3">
                            <i class="bi bi-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Commissions payées</div>
                            <div class="text-lg font-semibold">{{ number_format($stats['paid_commissions'], 2, ',', ' ') }} FCFA</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des boutiques avec statistiques -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-4">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-700">
                        <i class="bi bi-shop mr-1"></i>
                        {{ __('Boutiques et leurs commissions') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Boutique') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Commissions totales') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('En attente') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payées') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nb. vendeurs') }}</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($shops as $shop)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                @if ($shop->logo)
                                                    <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$shop->logo) }}" alt="{{ $shop->name }}">
                                                @else
                                                    <div class="h-6 w-6 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center">
                                                        <i class="bi bi-shop text-sm"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-xs">{{ $shop->name }}</div>
                                                @if ($shop->shop_id)
                                                    <div class="text-gray-500 text-xs">ID: {{ $shop->shop_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                        {{ number_format($shop->commission_stats['total'], 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-yellow-600">
                                        {{ number_format($shop->commission_stats['pending'], 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-green-600">
                                        {{ number_format($shop->commission_stats['paid'], 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                        {{ $shop->vendors_count ?? 0 }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-right">
                                        <a href="{{ route('commissions.shop-report', $shop->id) }}" class="inline-flex items-center px-2 py-1 bg-indigo-100 border border-transparent rounded text-xs font-medium text-indigo-700 hover:bg-indigo-200 mx-1">
                                            <i class="bi bi-eye-fill mr-1"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-3 text-center text-sm text-gray-500">{{ __('Aucune boutique trouvée') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-4">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">
                        <i class="bi bi-funnel mr-1"></i>
                        {{ __('Filtres') }}
                    </h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('commissions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="shop" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Boutique') }}</label>
                            <select name="shop_id" id="shop" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="">{{ __('Toutes les boutiques') }}</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="month" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Mois') }}</label>
                            <select name="month" id="month" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="">{{ __('Tous les mois') }}</option>
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                            <select name="status" id="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="">{{ __('Tous les statuts') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                            </select>
                        </div>
                        <div class="md:flex md:flex-col md:justify-end">
                            <button type="submit" class="mt-4 inline-flex justify-center items-center px-3 py-2 bg-purple-600 border border-transparent rounded text-xs font-medium text-white hover:bg-purple-700">
                                <i class="bi bi-search mr-1"></i>
                                {{ __('Filtrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des commissions -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">
                        <i class="bi bi-list-ul mr-1"></i>
                        {{ __('Liste des commissions') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Référence') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Boutique') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Vendeur') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Période') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Montant') }}</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($commissions as $commission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                        {{ $commission->reference ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                @if ($commission->shop->logo)
                                                    <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$commission->shop->logo) }}" alt="{{ $commission->shop->name }}">
                                                @else
                                                    <div class="h-6 w-6 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center">
                                                        <i class="bi bi-shop text-sm"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-xs">{{ $commission->shop->name }}</div>
                                                @if ($commission->shop->shop_id)
                                                    <div class="text-gray-500 text-xs">ID: {{ $commission->shop->shop_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                                        <div class="font-medium">{{ $commission->user->name }}</div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                        {{ $commission->period_month ?? 'N/A' }} {{ $commission->period_year ?? '' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-medium">
                                        {{ number_format($commission->amount, 2, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if ($commission->is_paid)
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1"></i>{{ __('Payée') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="bi bi-exclamation-circle-fill mr-1"></i>{{ __('En attente') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-right">
                                        <div class="flex justify-end space-x-1">
                                            <a href="{{ route('commissions.show', $commission) }}" class="text-purple-600 hover:text-purple-900 bg-purple-50 p-1 rounded" title="{{ __('Voir') }}">
                                                <i class="bi bi-eye text-xs"></i>
                                            </a>
                                            @if (!$commission->is_paid)
                                                <button type="button" onclick="confirmDelete('{{ $commission->id }}')" class="text-red-600 hover:text-red-900 bg-red-50 p-1 rounded" title="{{ __('Supprimer') }}">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            @endif
                                            @if (!$commission->is_paid)
                                                <a href="#" onclick="showPayModal('{{ $commission->id }}')" class="text-green-600 hover:text-green-900 bg-green-50 p-1 rounded" title="{{ __('Marquer comme payée') }}">
                                                    <i class="bi bi-cash text-xs"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-3 text-center text-sm text-gray-500">{{ __('Aucune commission trouvée') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t">
                    {{ $commissions->appends(request()->except('page'))->links() }}
                </div>
            </div>

            <!-- Modal de confirmation de suppression -->
            <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white p-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 sm:mx-0">
                                    <i class="bi bi-exclamation-triangle text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-sm font-medium text-gray-900" id="modal-title">
                                        {{ __('Confirmation de suppression') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500">
                                            {{ __('Êtes-vous sûr de vouloir supprimer cette commission ? Cette action est irréversible.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button" onclick="cancelDelete()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    {{ __('Annuler') }}
                                </button>
                                <form id="deleteForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        {{ __('Supprimer') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de paiement -->
            <div id="payModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white p-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-green-100 sm:mx-0">
                                    <i class="bi bi-cash text-green-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-sm font-medium text-gray-900" id="modal-title">
                                        {{ __('Marquer comme payée') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500">
                                            {{ __('Veuillez fournir les informations de paiement pour cette commission.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <form id="payForm" method="POST" action="" class="mt-4">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label for="payment_method" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Méthode de paiement') }}</label>
                                        <select id="payment_method" name="payment_method" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                            <option value="cash">{{ __('Espèces') }}</option>
                                            <option value="bank_transfer">{{ __('Virement bancaire') }}</option>
                                            <option value="check">{{ __('Chèque') }}</option>
                                            <option value="other">{{ __('Autre') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="payment_reference" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Référence de paiement') }}</label>
                                        <input type="text" id="payment_reference" name="payment_reference" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="notes" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                                        <textarea id="notes" name="notes" rows="2" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50"></textarea>
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end space-x-2">
                                    <button type="button" onclick="cancelPay()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        {{ __('Annuler') }}
                                    </button>
                                    <button type="submit" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-green-600 border border-transparent rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        {{ __('Marquer comme payée') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endpush

    <script>
        function confirmDelete(commissionId) {
            document.getElementById('deleteForm').action = `/commissions/${commissionId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function cancelDelete() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function showPayModal(commissionId) {
            document.getElementById('payForm').action = `/commissions/${commissionId}/pay`;
            document.getElementById('payModal').classList.remove('hidden');
        }

        function cancelPay() {
            document.getElementById('payModal').classList.add('hidden');
        }
    </script>
</x-app-layout>