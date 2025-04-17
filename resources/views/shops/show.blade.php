@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la boutique</h5>
                    <div>
                        <a href="{{ route('shops.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('shops.edit', $shop) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
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

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if ($shop->logo_path)
                                <img src="{{ asset('storage/' . $shop->logo_path) }}" alt="{{ $shop->name }}" class="img-fluid rounded" style="max-height: 200px;">
                            @else
                                <div class="p-5 bg-light rounded">
                                    <i class="fas fa-store fa-5x text-secondary"></i>
                                    <p class="mt-2 text-muted">Aucun logo</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $shop->name }}</h3>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $shop->address }}
                            </p>
                            <p>
                                <i class="fas fa-phone"></i> {{ $shop->phone }}<br>
                                <i class="fas fa-envelope"></i> {{ $shop->email }}
                            </p>
                            <p>
                                <span class="badge {{ $shop->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $shop->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                            @if($shop->description)
                                <h5>Description</h5>
                                <p>{{ $shop->description }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Personnel assigné</h5>
                                <a href="{{ route('shops.manage-users', $shop) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-plus"></i> Gérer le personnel
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Statut</th>
                                            <th>Commission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($shop->users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @if($user->pivot->is_manager)
                                                        <span class="badge bg-info">Manager</span>
                                                    @else
                                                        <span class="badge bg-secondary">Vendeur</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->is_active)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->pivot->custom_commission_rate !== null)
                                                        {{ $user->pivot->custom_commission_rate }}%
                                                    @else
                                                        <span class="text-muted">Taux par défaut</span>
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
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Aucun personnel assigné à cette boutique</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Ventes du jour</h5>
                                    <h3 class="text-primary">{{ number_format($shop->salesToday(), 0, ',', ' ') }} F</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Ventes de la semaine</h5>
                                    <h3 class="text-primary">{{ number_format($shop->salesThisWeek(), 0, ',', ' ') }} F</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Ventes du mois</h5>
                                    <h3 class="text-primary">{{ number_format($shop->salesThisMonth(), 0, ',', ' ') }} F</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 