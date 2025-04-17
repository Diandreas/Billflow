@extends('layouts.app')

@section('page_name', 'users-index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des utilisateurs</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Utilisateurs</li>
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
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtres
        </div>
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3">
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
                    <label for="role" class="form-label">Rôle</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="manager" {{ $role == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="vendeur" {{ $role == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ $search }}" placeholder="Nom ou email">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Liste des utilisateurs
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Boutiques</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->role == 'admin')
                                        <span class="badge bg-danger">Administrateur</span>
                                    @elseif ($user->role == 'manager')
                                        <span class="badge bg-primary">Manager</span>
                                    @elseif ($user->role == 'vendeur')
                                        <span class="badge bg-success">Vendeur</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $user->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach ($user->shops as $shop)
                                        <span class="badge bg-info">{{ $shop->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if (auth()->user()->isAdmin())
                                            <a href="{{ route('users.reset-email.form', $user) }}" class="btn btn-sm btn-warning" title="Réinitialiser l'email">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 