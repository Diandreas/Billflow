@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'équipement</h5>
                    <div>
                        <a href="{{ route('vendor-equipment.edit', $equipment) }}" class="btn btn-primary">Modifier</a>
                        <a href="{{ route('vendor-equipment.index') }}" class="btn btn-secondary">Retour à la liste</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Informations générales</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nom</th>
                                    <td>{{ $equipment->name }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ ucfirst($equipment->type) }}</td>
                                </tr>
                                <tr>
                                    <th>Numéro de série</th>
                                    <td>{{ $equipment->serial_number ?? 'Non spécifié' }}</td>
                                </tr>
                                <tr>
                                    <th>Quantité</th>
                                    <td>{{ $equipment->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        @if ($equipment->status === 'assigned')
                                            <span class="badge badge-primary">Assigné</span>
                                        @else
                                            <span class="badge badge-success">Retourné</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Informations d'attribution</h6>
                            <table class="table table-bordered">
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
                                <tr>
                                    <th>Attribué par</th>
                                    <td>{{ $equipment->assignedBy->name ?? 'Non spécifié' }}</td>
                                </tr>
                                <tr>
                                    <th>État à l'attribution</th>
                                    <td>{{ ucfirst($equipment->condition) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if ($equipment->notes)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Notes</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $equipment->notes }}
                            </div>
                        </div>
                    @endif
                    
                    @if ($equipment->status === 'returned')
                        <div class="row">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Informations de retour</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Date de retour</th>
                                        <td>{{ $equipment->returned_date->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>État au retour</th>
                                        <td>{{ ucfirst($equipment->return_condition) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Retourné à</th>
                                        <td>{{ $equipment->returnedTo->name ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Notes de retour</th>
                                        <td>{{ $equipment->return_notes ?? 'Aucune note' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <a href="{{ route('vendor-equipment.mark-returned', $equipment) }}" class="btn btn-success">
                                <i class="fas fa-check"></i> Marquer comme retourné
                            </a>
                        </div>
                    @endif
                    
                    <div class="mt-4 text-end">
                        <form action="{{ route('vendor-equipment.destroy', $equipment) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 