@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des Boutiques</h5>
                    <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle Boutique
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Contact</th>
                                    <th>Managers</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shops as $shop)
                                    <tr>
                                        <td>
                                            @if ($shop->logo_path)
                                                <img src="{{ asset('storage/' . $shop->logo_path) }}" alt="{{ $shop->name }}" width="50">
                                            @else
                                                <span class="badge bg-secondary">Aucun logo</span>
                                            @endif
                                        </td>
                                        <td>{{ $shop->name }}</td>
                                        <td>{{ $shop->address }}</td>
                                        <td>
                                            {{ $shop->phone }}<br>
                                            {{ $shop->email }}
                                        </td>
                                        <td>
                                            @forelse ($shop->managers as $manager)
                                                <span class="badge bg-info">{{ $manager->name }}</span>
                                            @empty
                                                <span class="badge bg-warning">Aucun manager</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if ($shop->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('shops.show', $shop) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('shops.edit', $shop) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteShopModal{{ $shop->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteShopModal{{ $shop->id }}" tabindex="-1" aria-labelledby="deleteShopModalLabel{{ $shop->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteShopModalLabel{{ $shop->id }}">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la boutique <strong>{{ $shop->name }}</strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('shops.destroy', $shop) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune boutique enregistrée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 