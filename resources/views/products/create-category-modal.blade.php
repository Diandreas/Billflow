<!-- Modal pour ajout rapide d'une catégorie -->
<div id="category-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ __('Ajouter une catégorie') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ __('Remplissez les informations pour créer rapidement une nouvelle catégorie de produits.') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="quick-category-name" class="block text-sm font-medium text-gray-700">{{ __('Nom de la catégorie') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="quick-category-name" id="quick-category-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Ex: Équipements informatiques') }}">
                        <p id="quick-category-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                        <label for="quick-category-description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                        <textarea name="quick-category-description" id="quick-category-description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ __('Description optionnelle de la catégorie') }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-category-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('Enregistrer') }}
                </button>
                <button type="button" id="cancel-category-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('Annuler') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal pour ajout rapide de catégorie
        const addCategoryBtn = document.getElementById('add-category-btn');
        const categoryModal = document.getElementById('category-modal');
        const saveCategoryBtn = document.getElementById('save-category-btn');
        const cancelCategoryBtn = document.getElementById('cancel-category-btn');
        const categorySelect = document.getElementById('category_id');
        
        if (addCategoryBtn && categoryModal && saveCategoryBtn && cancelCategoryBtn && categorySelect) {
            // Ouvrir le modal
            addCategoryBtn.addEventListener('click', function(e) {
                e.preventDefault();
                categoryModal.classList.remove('hidden');
            });
            
            // Fermer le modal
            cancelCategoryBtn.addEventListener('click', function() {
                categoryModal.classList.add('hidden');
                clearCategoryForm();
            });
            
            // Enregistrer la catégorie via AJAX
            saveCategoryBtn.addEventListener('click', function() {
                const nameInput = document.getElementById('quick-category-name');
                const descriptionInput = document.getElementById('quick-category-description');
                
                // Validation rapide
                let valid = true;
                
                if (!nameInput.value.trim()) {
                    document.getElementById('quick-category-name-error').textContent = "{{ __('Le nom de la catégorie est requis') }}";
                    document.getElementById('quick-category-name-error').classList.remove('hidden');
                    valid = false;
                } else {
                    document.getElementById('quick-category-name-error').classList.add('hidden');
                }
                
                if (!valid) return;
                
                // Créer un loader
                saveCategoryBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("Enregistrement...") }}';
                saveCategoryBtn.disabled = true;
                
                // Envoyer la requête AJAX
                fetch('{{ route("product-categories.quick-create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: nameInput.value.trim(),
                        description: descriptionInput.value.trim()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ajouter la nouvelle catégorie à la liste déroulante
                        const option = new Option(data.category.name, data.category.id, true, true);
                        categorySelect.appendChild(option);
                        
                        // Fermer le modal
                        categoryModal.classList.add('hidden');
                        clearCategoryForm();
                        
                        // Afficher un message de succès
                        alert("{{ __('Catégorie créée avec succès !') }}");
                    } else {
                        // Afficher les erreurs
                        alert("{{ __('Une erreur est survenue lors de la création de la catégorie.') }}");
                        console.error(data.message || "{{ __('Erreur inconnue') }}");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("{{ __('Une erreur est survenue lors de la communication avec le serveur.') }}");
                })
                .finally(() => {
                    saveCategoryBtn.innerHTML = "{{ __('Enregistrer') }}";
                    saveCategoryBtn.disabled = false;
                });
            });
            
            // Vider le formulaire du modal
            function clearCategoryForm() {
                document.getElementById('quick-category-name').value = '';
                document.getElementById('quick-category-description').value = '';
                document.getElementById('quick-category-name-error').classList.add('hidden');
            }
        }
    });
</script>
@endpush 