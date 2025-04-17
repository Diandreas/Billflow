@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'équipement</h5>
                    <a href="{{ route('vendor-equipment.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('vendor-equipment.update', $equipment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="name">Nom de l'équipement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $equipment->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="type">Type d'équipement <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="phone" {{ old('type', $equipment->type) == 'phone' ? 'selected' : '' }}>Téléphone</option>
                                <option value="tablet" {{ old('type', $equipment->type) == 'tablet' ? 'selected' : '' }}>Tablette</option>
                                <option value="laptop" {{ old('type', $equipment->type) == 'laptop' ? 'selected' : '' }}>Ordinateur portable</option>
                                <option value="printer" {{ old('type', $equipment->type) == 'printer' ? 'selected' : '' }}>Imprimante</option>
                                <option value="pos" {{ old('type', $equipment->type) == 'pos' ? 'selected' : '' }}>Terminal de paiement</option>
                                <option value="other" {{ old('type', $equipment->type) == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Vendeur <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Sélectionner un vendeur</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('user_id', $equipment->user_id) == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_id">Magasin <span class="text-danger">*</span></label>
                                    <select class="form-control @error('shop_id') is-invalid @enderror" id="shop_id" name="shop_id" required>
                                        <option value="">Sélectionner un magasin</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}" {{ old('shop_id', $equipment->shop_id) == $shop->id ? 'selected' : '' }}>
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shop_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serial_number">Numéro de série</label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}">
                                    @error('serial_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Quantité <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $equipment->quantity) }}" min="1" required>
                                    @error('quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assigned_date">Date d'attribution <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('assigned_date') is-invalid @enderror" id="assigned_date" name="assigned_date" value="{{ old('assigned_date', $equipment->assigned_date->format('Y-m-d')) }}" required>
                                    @error('assigned_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="condition">État de l'équipement <span class="text-danger">*</span></label>
                                    <select class="form-control @error('condition') is-invalid @enderror" id="condition" name="condition" required>
                                        <option value="">Sélectionner un état</option>
                                        <option value="neuf" {{ old('condition', $equipment->condition) == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="très bon" {{ old('condition', $equipment->condition) == 'très bon' ? 'selected' : '' }}>Très bon</option>
                                        <option value="bon" {{ old('condition', $equipment->condition) == 'bon' ? 'selected' : '' }}>Bon</option>
                                        <option value="moyen" {{ old('condition', $equipment->condition) == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                        <option value="mauvais" {{ old('condition', $equipment->condition) == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                                    </select>
                                    @error('condition')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="status">Statut <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="assigned" {{ old('status', $equipment->status) == 'assigned' ? 'selected' : '' }}>Assigné</option>
                                <option value="returned" {{ old('status', $equipment->status) == 'returned' ? 'selected' : '' }}>Retourné</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $equipment->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        @if($equipment->status === 'returned' || old('status') === 'returned')
                            <div class="return-details" id="return-details">
                                <h5 class="mb-3">Détails du retour</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="returned_date">Date de retour <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('returned_date') is-invalid @enderror" id="returned_date" name="returned_date" value="{{ old('returned_date', $equipment->returned_date ? $equipment->returned_date->format('Y-m-d') : date('Y-m-d')) }}">
                                    @error('returned_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="return_condition">État au retour <span class="text-danger">*</span></label>
                                    <select class="form-control @error('return_condition') is-invalid @enderror" id="return_condition" name="return_condition">
                                        <option value="">Sélectionner un état</option>
                                        <option value="neuf" {{ old('return_condition', $equipment->return_condition) == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="très bon" {{ old('return_condition', $equipment->return_condition) == 'très bon' ? 'selected' : '' }}>Très bon</option>
                                        <option value="bon" {{ old('return_condition', $equipment->return_condition) == 'bon' ? 'selected' : '' }}>Bon</option>
                                        <option value="moyen" {{ old('return_condition', $equipment->return_condition) == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                        <option value="mauvais" {{ old('return_condition', $equipment->return_condition) == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                                        <option value="endommagé" {{ old('return_condition', $equipment->return_condition) == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
                                        <option value="inutilisable" {{ old('return_condition', $equipment->return_condition) == 'inutilisable' ? 'selected' : '' }}>Inutilisable</option>
                                    </select>
                                    @error('return_condition')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="return_notes">Notes de retour</label>
                                    <textarea class="form-control @error('return_notes') is-invalid @enderror" id="return_notes" name="return_notes" rows="3">{{ old('return_notes', $equipment->return_notes) }}</textarea>
                                    @error('return_notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Mettre à jour l'équipement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const returnDetails = document.getElementById('return-details');
        
        // Si return-details n'existe pas encore, créer le conteneur
        let returnDetailsContainer = returnDetails;
        if (!returnDetailsContainer) {
            returnDetailsContainer = document.createElement('div');
            returnDetailsContainer.id = 'return-details';
            returnDetailsContainer.className = 'return-details';
            statusSelect.closest('form').insertBefore(returnDetailsContainer, document.querySelector('.d-grid'));
        }
        
        const toggleReturnFields = function() {
            if (statusSelect.value === 'returned') {
                if (!returnDetails) {
                    // Créer les champs de retour s'ils n'existent pas
                    returnDetailsContainer.innerHTML = `
                        <h5 class="mb-3">Détails du retour</h5>
                        
                        <div class="form-group mb-3">
                            <label for="returned_date">Date de retour <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="returned_date" name="returned_date" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="return_condition">État au retour <span class="text-danger">*</span></label>
                            <select class="form-control" id="return_condition" name="return_condition">
                                <option value="">Sélectionner un état</option>
                                <option value="neuf">Neuf</option>
                                <option value="très bon">Très bon</option>
                                <option value="bon">Bon</option>
                                <option value="moyen">Moyen</option>
                                <option value="mauvais">Mauvais</option>
                                <option value="endommagé">Endommagé</option>
                                <option value="inutilisable">Inutilisable</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="return_notes">Notes de retour</label>
                            <textarea class="form-control" id="return_notes" name="return_notes" rows="3"></textarea>
                        </div>
                    `;
                }
                returnDetailsContainer.style.display = 'block';
            } else {
                if (returnDetailsContainer) {
                    returnDetailsContainer.style.display = 'none';
                }
            }
        };
        
        // Exécuter au chargement
        toggleReturnFields();
        
        // Ajouter un écouteur d'événements pour les changements
        statusSelect.addEventListener('change', toggleReturnFields);
    });
</script>
@endpush
@endsection 