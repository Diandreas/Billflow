@extends('layouts.app')

@section('page_name', 'commissions-index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des commissions</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Commissions</li>
    </ol>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-money-bill-wave me-1"></i>
            Récapitulatif des commissions
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Total des commissions</div>
                                    <div class="fs-4">{{ number_format($stats['total_commissions'], 2, ',', ' ') }} €</div>
                                </div>
                                <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Commissions payées</div>
                                    <div class="fs-4">{{ number_format($stats['paid_commissions'], 2, ',', ' ') }} €</div>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Commissions en attente</div>
                                    <div class="fs-4">{{ number_format($stats['pending_commissions'], 2, ',', ' ') }} €</div>
                                </div>
                                <i class="fas fa-clock fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Nombre de commissions</div>
                                    <div class="fs-4">{{ $stats['total_count'] }}</div>
                                </div>
                                <i class="fas fa-list fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtres
        </div>
        <div class="card-body">
            <form action="{{ route('commissions.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="vendor_id" class="form-label">Vendeur</label>
                    <select name="vendor_id" id="vendor_id" class="form-select">
                        <option value="">Tous les vendeurs</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ $vendorId == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="shop_id" class="form-label">Boutique</label>
                    <select name="shop_id" id="shop_id" class="form-select">
                        <option value="">Toutes les boutiques</option>
                        @foreach ($shops as $shop)
                            <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Payé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Date de début</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('commissions.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                    <a href="{{ route('commissions.export', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-file-csv"></i> Exporter
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Liste des commissions
            </div>
            <button type="button" class="btn btn-success btn-sm" id="markSelectedAsPaid" disabled>
                <i class="fas fa-check-circle"></i> Marquer comme payées
            </button>
        </div>
        <div class="card-body">
            <form id="bulk-actions-form" action="{{ route('commissions.mark-as-paid') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="commissionsTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>ID</th>
                                <th>Vendeur</th>
                                <th>Facture</th>
                                <th>Montant</th>
                                <th>Source</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Boutique</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($commissions as $commission)
                                <tr>
                                    <td>
                                        @if ($commission->status == 'pending')
                                            <input type="checkbox" name="commission_ids[]" value="{{ $commission->id }}" class="form-check-input commission-checkbox">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $commission->id }}</td>
                                    <td>
                                        <a href="{{ route('commissions.vendor-report', $commission->user) }}">
                                            {{ $commission->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($commission->bill)
                                            <a href="{{ route('bills.show', $commission->bill) }}">
                                                {{ $commission->bill->reference }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($commission->amount, 2, ',', ' ') }} €</td>
                                    <td>{{ $commission->source }}</td>
                                    <td>
                                        @if ($commission->status == 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif ($commission->status == 'paid')
                                            <span class="badge bg-success">Payé</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $commission->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $commission->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($commission->bill && $commission->bill->shop)
                                            {{ $commission->bill->shop->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Aucune commission trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Modal pour marquer comme payées -->
                <div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Marquer les commissions comme payées</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="selected-count" class="alert alert-info mb-3">
                                    0 commissions sélectionnées
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Date de paiement</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Méthode de paiement</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="bank_transfer">Virement bancaire</option>
                                        <option value="check">Chèque</option>
                                        <option value="cash">Espèces</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_reference" class="form-label">Référence de paiement</label>
                                    <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="Référence optionnelle">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notes optionnelles"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success">Marquer comme payées</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="mt-3">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const commissionCheckboxes = document.querySelectorAll('.commission-checkbox');
        const markSelectedButton = document.getElementById('markSelectedAsPaid');
        const selectedCountElement = document.getElementById('selected-count');
        
        // Gestionnaire pour la case à cocher "Tout sélectionner"
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            commissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateButtonState();
        });
        
        // Gestionnaire pour les cases à cocher individuelles
        commissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonState);
        });
        
        // Gestionnaire pour le bouton "Marquer comme payées"
        markSelectedButton.addEventListener('click', function() {
            const selectedCount = getSelectedCount();
            selectedCountElement.textContent = `${selectedCount} commission${selectedCount > 1 ? 's' : ''} sélectionnée${selectedCount > 1 ? 's' : ''}`;
            
            const modal = new bootstrap.Modal(document.getElementById('markAsPaidModal'));
            modal.show();
        });
        
        // Mettre à jour l'état du bouton
        function updateButtonState() {
            const selectedCount = getSelectedCount();
            markSelectedButton.disabled = selectedCount === 0;
        }
        
        // Obtenir le nombre de commissions sélectionnées
        function getSelectedCount() {
            return document.querySelectorAll('.commission-checkbox:checked').length;
        }
    });
</script>
@endsection 