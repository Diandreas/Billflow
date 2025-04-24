@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion du personnel - {{ $shop->name }}</h5>
                    <a href="{{ route('shops.show', $shop) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Personnel assigné
                                </div>
                                <div class="card-body">
                                    @if($assignedUsers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Rôle</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($assignedUsers as $user)
                                                        <tr>
                                                            <td>{{ $user->name }}</td>
                                                            <td>
                                                                @if($user->pivot->is_manager)
                                                                    <span class="badge bg-info">Manager</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Vendeur</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <form action="{{ route('shops.remove-user', [$shop, $user]) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir retirer cet utilisateur de la boutique?')">
                                                                        <i class="fas fa-user-minus"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-center text-muted my-3">Aucun personnel assigné à cette boutique</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    Ajouter du personnel
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('shops.assign-users', $shop) }}" method="POST">
                                        @csrf

                                        @if($users->count() > 0)
                                            <div class="mb-3">
                                                <label for="selectedUser" class="form-label">Sélectionner un utilisateur</label>
                                                <select class="form-select" id="selectedUser" name="selected_user" required>
                                                    <option value="">Choisir un utilisateur...</option>
                                                    @foreach($users as $user)
                                                        @if(!$assignedUsers->contains('id', $user->id))
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="isManager" name="users[0][is_manager]" value="1">
                                                    <label class="form-check-label" for="isManager">
                                                        Définir comme manager
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="commissionRate" class="form-label">Taux de commission personnalisé (%)</label>
                                                <input type="number" class="form-control" id="commissionRate" name="users[0][custom_commission_rate]" min="0" max="100" step="0.01">
                                                <div class="form-text">Laisser vide pour utiliser le taux par défaut</div>
                                            </div>

                                            <input type="hidden" id="userId" name="users[0][id]">

                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-user-plus"></i> Ajouter
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-center text-muted my-3">Tous les utilisateurs sont déjà assignés à cette boutique</p>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectedUserField = document.getElementById('selectedUser');
        const userIdField = document.getElementById('userId');

        if (selectedUserField && userIdField) {
            selectedUserField.addEventListener('change', function() {
                userIdField.value = this.value;
            });
        }
    });
</script>
@endsection
@endsection 