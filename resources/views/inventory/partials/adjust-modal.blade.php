<!-- Modal d'ajustement de stock -->
<div id="adjustModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white p-3">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-sm leading-6 font-medium text-gray-900" id="modal-title">
                            {{ __('Ajuster le stock') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-xs text-gray-500">
                                {{ __('Ajustez la quantité en stock pour ce produit. Utilisez des valeurs positives pour ajouter et négatives pour retirer.') }}
                            </p>
                            <form id="adjustForm" method="POST" action="" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="adjustment" class="block text-xs font-medium text-gray-700">{{ __('Quantité') }}</label>
                                    <input type="number" name="adjustment" id="adjustment" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50" required>
                                </div>
                                <div class="mt-2">
                                    <label for="reason" class="block text-xs font-medium text-gray-700">{{ __('Raison') }}</label>
                                    <select name="reason" id="reason" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50" required>
                                        <option value="restock">{{ __('Réapprovisionnement') }}</option>
                                        <option value="correction">{{ __('Correction') }}</option>
                                        <option value="damage">{{ __('Produit endommagé') }}</option>
                                        <option value="theft">{{ __('Vol') }}</option>
                                        <option value="other">{{ __('Autre') }}</option>
                                    </select>
                                </div>
                                <div class="mt-2">
                                    <label for="notes" class="block text-xs font-medium text-gray-700">{{ __('Notes') }}</label>
                                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="cancelAdjust()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        {{ __('Annuler') }}
                    </button>
                    <button type="button" onclick="submitAdjust()" class="inline-flex justify-center px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Ajuster') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmAdjust(productId) {
        document.getElementById('adjustForm').action = `/inventory/${productId}/adjust`;
        document.getElementById('adjustModal').classList.remove('hidden');
    }

    function cancelAdjust() {
        document.getElementById('adjustModal').classList.add('hidden');
    }

    function submitAdjust() {
        document.getElementById('adjustForm').submit();
    }
</script> 