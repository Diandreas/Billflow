<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
</div>

<!-- Modal pour ajout rapide d'un fournisseur -->
<div id="supplier-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ __('Ajouter un fournisseur') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ __('Remplissez les informations pour créer rapidement un nouveau fournisseur.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="quick-supplier-name" class="block text-sm font-medium text-gray-700">{{ __('Nom du fournisseur') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="quick-supplier-name" id="quick-supplier-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Entreprise XYZ') }}">
                        <p id="quick-supplier-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                        <label for="quick-supplier-contact" class="block text-sm font-medium text-gray-700">{{ __('Nom du contact') }}</label>
                        <input type="text" name="quick-supplier-contact" id="quick-supplier-contact" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Jean Dupont') }}">
                    </div>
                    <div>
                        <label for="quick-supplier-email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input type="email" name="quick-supplier-email" id="quick-supplier-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: contact@entreprise.com') }}">
                        <p id="quick-supplier-email-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                        <label for="quick-supplier-phone" class="block text-sm font-medium text-gray-700">{{ __('Téléphone') }}</label>
                        <input type="text" name="quick-supplier-phone" id="quick-supplier-phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: +33 6 12 34 56 78') }}">
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-supplier-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('Enregistrer') }}
                </button>
                <button type="button" id="cancel-supplier-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('Annuler') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal pour ajout rapide de fournisseur
        const addSupplierBtn = document.getElementById('add-supplier-btn');
        const supplierModal = document.getElementById('supplier-modal');
        const saveSupplierBtn = document.getElementById('save-supplier-btn');
        const cancelSupplierBtn = document.getElementById('cancel-supplier-btn');
        const supplierSelect = document.getElementById('supplier_id');
        
        if (addSupplierBtn && supplierModal && saveSupplierBtn && cancelSupplierBtn && supplierSelect) {
            // Ouvrir le modal
            addSupplierBtn.addEventListener('click', function(e) {
                e.preventDefault();
                supplierModal.classList.remove('hidden');
            });
            
            // Fermer le modal
            cancelSupplierBtn.addEventListener('click', function() {
                supplierModal.classList.add('hidden');
                clearSupplierForm();
            });
            
            // Enregistrer le fournisseur via AJAX
            saveSupplierBtn.addEventListener('click', function() {
                const nameInput = document.getElementById('quick-supplier-name');
                const contactInput = document.getElementById('quick-supplier-contact');
                const emailInput = document.getElementById('quick-supplier-email');
                const phoneInput = document.getElementById('quick-supplier-phone');
                
                // Validation rapide
                let valid = true;
                
                if (!nameInput.value.trim()) {
                    document.getElementById('quick-supplier-name-error').textContent = "{{ __('Le nom du fournisseur est requis') }}";
                    document.getElementById('quick-supplier-name-error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('quick-supplier-name-error').classList.add('hidden');
                }
                
                if (emailInput.value.trim() && !isValidEmail(emailInput.value.trim())) {
                    document.getElementById('quick-supplier-email-error').textContent = "{{ __('Veuillez entrer une adresse email valide') }}";
                    document.getElementById('quick-supplier-email-error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('quick-supplier-email-error').classList.add('hidden');
                }
                
                if (!valid) return;
                
                // Créer un loader
                saveSupplierBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("Enregistrement...") }}';
                saveSupplierBtn.disabled = true;
                
                // Envoyer la requête AJAX
                fetch('{{ route("suppliers.quick-create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: nameInput.value.trim(),
                        contact_name: contactInput.value.trim(),
                        email: emailInput.value.trim(),
                        phone: phoneInput.value.trim()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ajouter le nouveau fournisseur à la liste déroulante
                        const option = new Option(data.supplier.name, data.supplier.id, true, true);
                        supplierSelect.appendChild(option);
                        
                        // Fermer le modal
                        supplierModal.classList.add('hidden');
                        clearSupplierForm();
                        
                        // Afficher un message de succès
                        alert("{{ __('Fournisseur créé avec succès !') }}");
                    } else {
                        // Afficher les erreurs
                        alert("{{ __('Une erreur est survenue lors de la création du fournisseur.') }}");
                        console.error(data.message || "{{ __('Erreur inconnue') }}");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("{{ __('Une erreur est survenue lors de la communication avec le serveur.') }}");
                })
                .finally(() => {
                    saveSupplierBtn.innerHTML = "{{ __('Enregistrer') }}";
                    saveSupplierBtn.disabled = false;
                });
            });
            
            // Vider le formulaire du modal
            function clearSupplierForm() {
                document.getElementById('quick-supplier-name').value = '';
                document.getElementById('quick-supplier-contact').value = '';
                document.getElementById('quick-supplier-email').value = '';
                document.getElementById('quick-supplier-phone').value = '';
                document.getElementById('quick-supplier-name-error').classList.add('hidden');
                document.getElementById('quick-supplier-email-error').classList.add('hidden');
            }
            
            // Validation d'email
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        }
    });
</script>
@endpush
