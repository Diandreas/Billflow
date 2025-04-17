@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un équipement de vendeur</h5>
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

                    <form action="{{ route('vendor-equipment.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="name">Nom de l'équipement <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="type">Type d'équipement <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Téléphone</option>
                                <option value="tablet" {{ old('type') == 'tablet' ? 'selected' : '' }}>Tablette</option>
                                <option value="laptop" {{ old('type') == 'laptop' ? 'selected' : '' }}>Ordinateur portable</option>
                                <option value="printer" {{ old('type') == 'printer' ? 'selected' : '' }}>Imprimante</option>
                                <option value="pos" {{ old('type') == 'pos' ? 'selected' : '' }}>Terminal de paiement</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Autre</option>
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
                                            <option value="{{ $vendor->id }}" {{ old('user_id') == $vendor->id ? 'selected' : '' }}>
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
                                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
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
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                                    @error('serial_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Quantité <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
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
                                    <input type="date" class="form-control @error('assigned_date') is-invalid @enderror" id="assigned_date" name="assigned_date" value="{{ old('assigned_date', date('Y-m-d')) }}" required>
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
                                        <option value="neuf" {{ old('condition') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="très bon" {{ old('condition') == 'très bon' ? 'selected' : '' }}>Très bon</option>
                                        <option value="bon" {{ old('condition') == 'bon' ? 'selected' : '' }}>Bon</option>
                                        <option value="moyen" {{ old('condition') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                        <option value="mauvais" {{ old('condition') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                                    </select>
                                    @error('condition')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Enregistrer l'équipement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 