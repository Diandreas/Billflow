@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Marquer l'équipement comme retourné</h5>
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
                    
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Informations sur l'équipement</h6>
                        <table class="table">
                            <tr>
                                <th width="30%">Nom</th>
                                <td>{{ $equipment->name }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ ucfirst($equipment->type) }}</td>
                            </tr>
                            <tr>
                                <th>Vendeur</th>
                                <td>{{ $equipment->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Magasin</th>
                                <td>{{ $equipment->shop->name }}</td>
                            </tr>
                            <tr>
                                <th>Date d'attribution</th>
                                <td>{{ $equipment->assigned_date->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <form action="{{ route('vendor-equipment.mark-returned-store', $equipment) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="returned_date">Date de retour <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('returned_date') is-invalid @enderror" id="returned_date" name="returned_date" value="{{ old('returned_date', date('Y-m-d')) }}" required>
                            @error('returned_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="return_condition">État de l'équipement au retour <span class="text-danger">*</span></label>
                            <select class="form-control @error('return_condition') is-invalid @enderror" id="return_condition" name="return_condition" required>
                                <option value="">Sélectionner un état</option>
                                <option value="neuf" {{ old('return_condition') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                <option value="très bon" {{ old('return_condition') == 'très bon' ? 'selected' : '' }}>Très bon</option>
                                <option value="bon" {{ old('return_condition') == 'bon' ? 'selected' : '' }}>Bon</option>
                                <option value="moyen" {{ old('return_condition') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                <option value="mauvais" {{ old('return_condition') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                                <option value="endommagé" {{ old('return_condition') == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
                                <option value="inutilisable" {{ old('return_condition') == 'inutilisable' ? 'selected' : '' }}>Inutilisable</option>
                            </select>
                            @error('return_condition')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="return_notes">Notes de retour</label>
                            <textarea class="form-control @error('return_notes') is-invalid @enderror" id="return_notes" name="return_notes" rows="3" placeholder="Décrivez l'état de l'équipement, les dommages éventuels, etc.">{{ old('return_notes') }}</textarea>
                            @error('return_notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Confirmer le retour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 