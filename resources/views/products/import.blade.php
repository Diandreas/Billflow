<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Importer des produits') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Importez vos produits à partir d\'un fichier Excel ou CSV') }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                <i class="bi bi-arrow-left mr-2"></i>
                {{ __('Retour aux produits') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded-md">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-circle text-red-600 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">{{ __('Des erreurs sont survenues :') }}</h3>
                                    <ul class="mt-1 text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-3 text-sm text-red-700">
                                        <p>{{ __('Conseils de dépannage :') }}</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li>{{ __('Assurez-vous que votre fichier a bien l\'extension .xlsx, .xls ou .csv') }}</li>
                                            <li>{{ __('Vérifiez que votre fichier n\'est pas corrompu') }}</li>
                                            <li>{{ __('Pour les fichiers CSV, assurez-vous qu\'ils sont encodés en UTF-8') }}</li>
                                            <li>{{ __('Essayez de télécharger notre modèle et de le remplir') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-600 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-info-circle text-blue-600 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-base font-medium text-blue-800">{{ __('Instructions d\'importation') }}</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>{{ __('Veuillez suivre ces étapes pour importer vos produits :') }}</p>
                                    <ol class="list-decimal pl-5 mt-2 space-y-1">
                                        <li>{{ __('Téléchargez le modèle pour voir le format attendu') }}</li>
                                        <li>{{ __('Remplissez vos données dans le modèle') }}</li>
                                        <li>{{ __('Enregistrez le fichier au format .xlsx, .xls ou .csv') }}</li>
                                        <li>{{ __('Téléchargez le fichier en utilisant le formulaire ci-dessous') }}</li>
                                        <li>{{ __('Choisissez comment gérer les produits existants') }}</li>
                                        <li>{{ __('Cliquez sur Importer pour démarrer le processus') }}</li>
                                    </ol>
                                    <p class="mt-2">
                                        <strong>{{ __('Note :') }}</strong> {{ __('Le seul champ obligatoire est : Nom. Tous les autres champs sont optionnels.') }}
                                    </p>
                                </div>
                                <a href="{{ route('products.import.template') }}" class="mt-3 inline-flex items-center px-4 py-2 border border-blue-600 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="bi bi-download mr-2"></i>
                                    {{ __('Télécharger le modèle') }}
                                </a>
                                <a href="{{ route('products.export.form') }}" class="mt-3 ml-2 inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="bi bi-file-earmark-arrow-down mr-2"></i>
                                    {{ __('Exporter des produits') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="bi bi-file-earmark-spreadsheet mr-2"></i>
                                {{ __('Choisir un fichier') }} (.xlsx, .xls, .csv)
                            </label>
                            <div class="mt-1">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 flex justify-center items-center bg-gray-50 hover:bg-gray-100 transition duration-150 cursor-pointer" id="dropzone">
                                    <input id="file" name="file" type="file" required
                                        class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10"
                                        accept=".xlsx,.xls,.csv" onchange="updateFileDetails(this)">
                                    <div class="text-center" id="upload-prompt">
                                        <i class="bi bi-cloud-arrow-up text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-sm text-gray-600">
                                            {{ __('Glissez-déposez un fichier ici ou cliquez pour parcourir') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ __('Formats acceptés: Excel (.xlsx, .xls) et CSV (.csv)') }}
                                        </p>
                                    </div>
                                    <div class="hidden text-center" id="file-details">
                                        <i class="bi bi-file-earmark-check text-green-500 text-3xl mb-2"></i>
                                        <p class="text-sm font-medium text-gray-700" id="file-name"></p>
                                        <p class="text-xs text-gray-500 mt-1" id="file-size"></p>
                                        <button type="button" onclick="resetFileInput()" class="mt-2 text-xs text-red-600 hover:text-red-800">
                                            {{ __('Supprimer') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="import_mode" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="bi bi-gear mr-2"></i>
                                {{ __('Mode d\'importation') }}
                            </label>
                            <select id="import_mode" name="import_mode"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="create_only">{{ __('Créer uniquement de nouveaux produits') }}</option>
                                <option value="update_only">{{ __('Mettre à jour uniquement les produits existants') }}</option>
                                <option value="create_and_update">{{ __('Créer et mettre à jour les produits') }}</option>
                                <option value="replace_all">{{ __('Remplacer tous les produits (attention)') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('Choisissez comment gérer les produits existants qui correspondent par SKU ou nom.') }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="similarity_threshold"><i class="fas fa-percentage"></i> Seuil de similarité (%)</label>
                            <input type="range" class="custom-range" id="similarity_threshold" name="similarity_threshold" min="0" max="100" value="50" oninput="updateSliderValue(this.value)">
                            <div class="d-flex justify-content-between">
                                <span>0% (Aucune vérification)</span>
                                <span id="similarity_value">50%</span>
                                <span>100% (Correspondance exacte)</span>
                            </div>
                            <small class="form-text text-muted">Détermine à partir de quel pourcentage de similarité de nom un produit est considéré comme potentiellement identique</small>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0 h6">Valeurs par défaut (optionnel)</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="category_id"><i class="fas fa-folder"></i> Catégorie par défaut</label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option value="">-- Aucune catégorie par défaut --</option>
                                        @foreach(\App\Models\ProductCategory::orderBy('name')->get() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Si le fichier ne précise pas de catégorie</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="supplier_id"><i class="fas fa-truck"></i> Fournisseur par défaut</label>
                                    <select class="form-control" id="supplier_id" name="supplier_id">
                                        <option value="">-- Aucun fournisseur par défaut --</option>
                                        @foreach(\App\Models\Supplier::orderBy('name')->get() as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Si le fichier ne précise pas de fournisseur</small>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg mr-3 inline-flex items-center transition-colors duration-150">
                                <i class="bi bi-x-lg mr-2"></i>
                                {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-150">
                                <i class="bi bi-upload mr-2"></i>
                                {{ __('Importer les produits') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('file');
            
            // Ajout des effets pour le drag & drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropzone.classList.add('border-indigo-500');
                dropzone.classList.add('bg-indigo-50');
            }
            
            function unhighlight() {
                dropzone.classList.remove('border-indigo-500');
                dropzone.classList.remove('bg-indigo-50');
            }
            
            // Gestion du drop
            dropzone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileDetails(fileInput);
                }
            }
        });
        
        // Affiche les détails du fichier sélectionné
        function updateFileDetails(input) {
            const uploadPrompt = document.getElementById('upload-prompt');
            const fileDetails = document.getElementById('file-details');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                
                // Vérifier si l'extension est autorisée
                if (!['xlsx', 'xls', 'csv'].includes(extension)) {
                    alert("{{ __('Format de fichier non supporté. Veuillez utiliser .xlsx, .xls ou .csv') }}");
                    resetFileInput();
                    return;
                }
                
                // Afficher les détails du fichier
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                
                uploadPrompt.classList.add('hidden');
                fileDetails.classList.remove('hidden');
                
                // Changer l'apparence de la dropzone
                document.getElementById('dropzone').classList.add('border-green-500');
                document.getElementById('dropzone').classList.add('bg-green-50');
            } else {
                resetFileInput();
            }
        }
        
        // Réinitialiser le champ de fichier
        function resetFileInput() {
            const fileInput = document.getElementById('file');
            fileInput.value = '';
            
            document.getElementById('upload-prompt').classList.remove('hidden');
            document.getElementById('file-details').classList.add('hidden');
            document.getElementById('dropzone').classList.remove('border-green-500', 'bg-green-50');
        }
        
        // Formatage de la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function updateSliderValue(value) {
            document.getElementById('similarity_value').textContent = value + '%';
        }
    </script>
    @endpush
</x-app-layout> 