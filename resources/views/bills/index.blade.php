<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 py-3 px-3 rounded-lg shadow-sm mb-4">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">
                    {{ __('Factures') }}
                </h2>
                <a href="{{ route('bills.create') }}" class="inline-flex items-center px-3 py-1 text-xs bg-white text-teal-700 rounded-md hover:bg-teal-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Nouvelle Facture') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3">
                    @if (session('status'))
                        <div class="mb-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-2 text-sm rounded" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-3 bg-gray-50 p-2 rounded-lg">
                        <form action="{{ route('bills.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-2">
                            <div>
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('N° Facture, Magasin, Client...') }}" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="shop" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Magasin') }}</label>
                                <select name="shop" id="shop" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous les magasins') }}</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                                <select name="status" id="status" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Payée') }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('En retard') }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Annulée') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="period" class="block text-xs font-medium text-gray-700 mb-1">{{ __('Période') }}</label>
                                <select name="period" id="period" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    <option value="">{{ __('Toutes les périodes') }}</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>{{ __('Aujourd\'hui') }}</option>
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>{{ __('Cette semaine') }}</option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>{{ __('Ce mois') }}</option>
                                    <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>{{ __('Ce trimestre') }}</option>
                                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>{{ __('Cette année') }}</option>
                                </select>
                            </div>
                            <div class="md:flex md:flex-col md:justify-end">
                                <button type="submit" class="mt-4 inline-flex justify-center items-center px-3 py-1.5 bg-teal-600 border border-transparent rounded text-xs font-medium text-white hover:bg-teal-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Filtrer') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">{{ __('Facture') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">{{ __('Magasin') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">{{ __('Client') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">{{ __('Date') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">{{ __('Échéance') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">{{ __('Montant') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">{{ __('Statut') }}</th>
                                    <th scope="col" class="px-3 py-1.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bills as $bill)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs font-medium">
                                            {{ $bill->invoice_number }}
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                    @if ($bill->shop->logo)
                                                        <img class="h-6 w-6 rounded-md object-cover" src="{{ asset('storage/'.$bill->shop->logo) }}" alt="{{ $bill->shop->name }}">
                                                    @else
                                                        <div class="h-6 w-6 rounded-md bg-teal-100 text-teal-700 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    {{ $bill->shop->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                    <div class="h-6 w-6 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    {{ $bill->customer_name }}
                                                    <div class="text-gray-500 text-xs">{{ $bill->customer_email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $bill->bill_date }}
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                            {{ $bill->due_date}}
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs font-medium">
                                            <span class="text-teal-600">{{ number_format($bill->total_amount, 2) }} €</span>
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap">
                                            @if ($bill->status === 'paid')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">
                                                    {{ __('Payée') }}
                                                </span>
                                            @elseif ($bill->status === 'pending')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('En attente') }}
                                                </span>
                                            @elseif ($bill->status === 'overdue')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800">
                                                    {{ __('En retard') }}
                                                </span>
                                            @elseif ($bill->status === 'cancelled')
                                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ __('Annulée') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-1.5 whitespace-nowrap text-xs text-right">
                                            <div class="flex justify-end space-x-1">
                                                <a href="{{ route('bills.show', $bill) }}" class="text-teal-600 hover:text-teal-900" title="{{ __('Voir') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('bills.edit', $bill) }}" class="text-teal-600 hover:text-teal-900" title="{{ __('Modifier') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('bills.download', $bill) }}" class="text-teal-600 hover:text-teal-900" title="{{ __('Télécharger') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="confirmDelete('{{ $bill->id }}')" class="text-red-600 hover:text-red-900" title="{{ __('Supprimer') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-2 text-center text-gray-500 text-xs">{{ __('Aucune facture trouvée') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $bills->appends(request()->except('page'))->links() }}
                    </div>

                    <!-- Modal de confirmation de suppression -->
                    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white p-3">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-8 w-8 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-sm font-medium text-gray-900" id="modal-title">
                                                {{ __('Confirmation de suppression') }}
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Êtes-vous sûr de vouloir supprimer cette facture ? Cette action est irréversible.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" onclick="cancelDelete()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
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
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(billId) {
            document.getElementById('deleteForm').action = `/bills/${billId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function cancelDelete() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
