@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Équipements des Vendeurs</h5>
                    <a href="{{ route('vendor-equipment.create') }}" class="btn btn-primary">Ajouter un équipement</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('vendor-equipment.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigné</option>
                                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Retourné</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shop_id">Magasin</label>
                                    <select name="shop_id" id="shop_id" class="form-control">
                                        <option value="">Tous les magasins</option>
                                        @foreach($shops as $shop)
                                            <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                                                {{ $shop->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_id">Vendeur</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Tous les vendeurs</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ request('user_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="{{ route('vendor-equipment.index') }}" class="btn btn-secondary ml-2">Réinitialiser</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Vendeur</th>
                                    <th>Magasin</th>
                                    <th>Date d'attribution</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equipment as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ $item->shop->name }}</td>
                                        <td>{{ $item->assigned_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($item->status === 'assigned')
                                                <span class="badge badge-primary">Assigné</span>
                                            @else
                                                <span class="badge badge-success">Retourné</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('vendor-equipment.show', $item) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('vendor-equipment.edit', $item) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($item->status === 'assigned')
                                                    <a href="{{ route('vendor-equipment.mark-returned', $item) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Marquer comme retourné
                                                    </a>
                                                @endif
                                                <form action="{{ route('vendor-equipment.destroy', $item) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun équipement trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $equipment->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 