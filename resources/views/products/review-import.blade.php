@extends('layouts.app')

@section('title', 'Revue des doublons d\'importation')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="m-0 h4">Revue des doublons d'importation</h1>
        </div>
        <div class="card-body">
            @if(count($duplicates) > 0)
                <div class="alert alert-info">
                    <p><i class="fas fa-info-circle"></i> Nous avons trouvé <strong>{{ count($duplicates) }}</strong> produits similaires existants dans la base de données. Veuillez indiquer comment traiter chacun d'eux.</p>
                </div>

                <form action="{{ route('products.process-reviewed-import') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Ligne</th>
                                    <th>Nom dans le fichier</th>
                                    <th>Produit(s) similaire(s) existant(s)</th>
                                    <th>Taux de similarité</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duplicates as $index => $duplicate)
                                    <tr>
                                        <td>{{ $duplicate['row_index'] }}</td>
                                        <td class="text-primary font-weight-bold">
                                            {{ $duplicate['product']->name }}
                                        </td>
                                        <td>
                                            <div class="product-match">
                                                <div class="d-flex justify-content-between">
                                                    <span class="font-weight-bold">{{ $duplicate['product']->name }}</span>
                                                    <span class="badge badge-info">ID: {{ $duplicate['product']->id }}</span>
                                                </div>
                                                <div class="small text-muted">
                                                    @if($duplicate['product']->description)
                                                        {{ Str::limit($duplicate['product']->description, 100) }}
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="badge badge-{{ $duplicate['product']->type === 'physical' ? 'success' : 'warning' }}">
                                                        {{ $duplicate['product']->type === 'physical' ? 'Produit physique' : 'Service' }}
                                                    </span>
                                                    <span class="badge badge-dark">Prix: {{ number_format($duplicate['product']->default_price, 2) }} €</span>
                                                    @if($duplicate['product']->type === 'physical')
                                                        <span class="badge badge-{{ $duplicate['product']->stock_quantity > 0 ? 'success' : 'danger' }}">
                                                            Stock: {{ $duplicate['product']->stock_quantity }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress">
                                                <div class="progress-bar {{ $duplicate['similarity'] > 80 ? 'bg-danger' : 'bg-warning' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $duplicate['similarity'] }}%" 
                                                     aria-valuenow="{{ $duplicate['similarity'] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                     {{ number_format($duplicate['similarity'], 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="duplicates[{{ $index }}][row_index]" value="{{ $duplicate['row_index'] }}">
                                            
                                            <div class="form-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" 
                                                           id="action_create_{{ $index }}" 
                                                           name="duplicates[{{ $index }}][action]" 
                                                           value="create" 
                                                           class="custom-control-input" required>
                                                    <label class="custom-control-label" for="action_create_{{ $index }}">
                                                        <span class="text-success">Créer un nouveau produit</span>
                                                    </label>
                                                </div>
                                                
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" 
                                                           id="action_update_{{ $index }}" 
                                                           name="duplicates[{{ $index }}][action]" 
                                                           value="update" 
                                                           class="custom-control-input">
                                                    <label class="custom-control-label" for="action_update_{{ $index }}">
                                                        <span class="text-warning">Mettre à jour le produit existant</span>
                                                    </label>
                                                    <input type="hidden" 
                                                           name="duplicates[{{ $index }}][product_id]" 
                                                           value="{{ $duplicate['product']->id }}">
                                                </div>
                                                
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" 
                                                           id="action_skip_{{ $index }}" 
                                                           name="duplicates[{{ $index }}][action]" 
                                                           value="skip" 
                                                           class="custom-control-input">
                                                    <label class="custom-control-label" for="action_skip_{{ $index }}">
                                                        <span class="text-danger">Ignorer cette ligne</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('products.import') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à l'importation
                        </a>
                        <div>
                            <button type="submit" name="action" value="select_all_create" class="btn btn-success mr-2">
                                <i class="fas fa-plus-circle"></i> Tout créer comme nouveaux
                            </button>
                            <button type="submit" name="action" value="select_all_update" class="btn btn-warning mr-2">
                                <i class="fas fa-sync-alt"></i> Tout mettre à jour
                            </button>
                            <button type="submit" name="action" value="select_all_skip" class="btn btn-danger mr-2">
                                <i class="fas fa-ban"></i> Tout ignorer
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Valider mes choix
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    <p>Aucun doublon potentiel n'a été trouvé. <a href="{{ route('products.index') }}">Retour à la liste des produits</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle bulk action buttons
    document.querySelector('button[value="select_all_create"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('input[id^="action_create_"]').forEach(function(radio) {
            radio.checked = true;
        });
    });
    
    document.querySelector('button[value="select_all_update"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('input[id^="action_update_"]').forEach(function(radio) {
            radio.checked = true;
        });
    });
    
    document.querySelector('button[value="select_all_skip"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('input[id^="action_skip_"]').forEach(function(radio) {
            radio.checked = true;
        });
    });
});
</script>
@endpush 