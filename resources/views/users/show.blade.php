@extends('layouts.app')

@section('page_name', 'users-show')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de l'utilisateur</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item active">{{ $user->name }}</li>
    </ol>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations personnelles
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Nom :</div>
                        <div class="col-sm-8">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Email :</div>
                        <div class="col-sm-8">
                            {{ $user->email }}
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('users.reset-email.form', $user) }}" class="btn btn-warning btn-sm ms-2" title="Réinitialiser l'email">
                                    <i class="fas fa-envelope"></i> Réinitialiser
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Téléphone :</div>
                        <div class="col-sm-8">{{ $user->phone ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Rôle :</div>
                        <div class="col-sm-8">
                            @if ($user->role == 'admin')
                                <span class="badge bg-danger">Administrateur</span>
                            @elseif ($user->role == 'manager')
                                <span class="badge bg-primary">Manager</span>
                            @elseif ($user->role == 'vendeur')
                                <span class="badge bg-success">Vendeur</span>
                            @else
                                <span class="badge bg-secondary">{{ $user->role }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Date d'inscription :</div>
                        <div class="col-sm-8">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Dernière connexion :</div>
                        <div class="col-sm-8">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-store me-1"></i>
                    Boutiques assignées
                </div>
                <div class="card-body">
                    @if ($userShops->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Rôle</th>
                                        <th>Date d'assignation</th>
                                        <th>Commission</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userShops as $shop)
                                        <tr>
                                            <td>
                                                <a href="{{ route('shops.show', $shop) }}">{{ $shop->name }}</a>
                                            </td>
                                            <td>
                                                @if ($shop->pivot->is_manager ?? false)
                                                    <span class="badge bg-primary">Manager</span>
                                                @else
                                                    <span class="badge bg-success">Vendeur</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($shop->pivot->assigned_at ?? $shop->pivot->created_at)->format('d/m/Y') }}</td>
                                            <td>
                                                @if (isset($shop->pivot->custom_commission_rate))
                                                    {{ $shop->pivot->custom_commission_rate }}%
                                                    <span class="text-muted">(personnalisé)</span>
                                                @else
                                                    {{ $user->commission_rate }}%
                                                    <span class="text-muted">(par défaut)</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Cet utilisateur n'est assigné à aucune boutique.
                        </div>
                    @endif
                </div>
            </div>
            
            @if ($user->role == 'vendeur')
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Commissions
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('commissions.vendor-report', $user) }}" class="btn btn-primary">
                                <i class="fas fa-chart-bar me-1"></i> Voir le rapport de commissions
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection 