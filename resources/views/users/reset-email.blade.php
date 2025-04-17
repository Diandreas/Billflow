@extends('layouts.app')

@section('page_name', 'users-reset-email')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Réinitialiser l'email de {{ $user->name }}</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item active">Réinitialiser l'email</li>
    </ol>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-envelope me-1"></i>
                    Formulaire de réinitialisation d'email
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
                    
                    <form action="{{ route('users.reset-email', $user) }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention:</strong> Cette action est réservée aux administrateurs. La réinitialisation de l'email enverra une notification à l'utilisateur.
                        </div>
                        
                        <div class="mb-3">
                            <label for="current_email" class="form-label">Email actuel</label>
                            <input type="email" id="current_email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_email" class="form-label">Nouvel email</label>
                            <input type="email" name="new_email" id="new_email" class="form-control" value="{{ old('new_email') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmez votre mot de passe</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <div class="form-text">Pour des raisons de sécurité, veuillez entrer votre mot de passe.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Réinitialiser l'email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations de l'utilisateur
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Nom :</div>
                        <div class="col-sm-8">{{ $user->name }}</div>
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
                        <div class="col-sm-4 fw-bold">Boutiques :</div>
                        <div class="col-sm-8">
                            @forelse ($userShops as $shop)
                                <span class="badge bg-info">{{ $shop->name }}</span>
                            @empty
                                <span class="text-muted">Aucune boutique</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Inscrit le :</div>
                        <div class="col-sm-8">{{ $user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 