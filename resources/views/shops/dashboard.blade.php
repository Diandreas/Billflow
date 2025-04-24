@extends('layouts.app')

@section('page_name', 'shop-dashboard')

@section('title', 'Tableau de bord - ' . $shop->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord : {{ $shop->name }}</h1>
    
    <div class="row mb-3">
        <div class="col-md-8">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shops.index') }}">Boutiques</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shops.show', $shop) }}">{{ $shop->name }}</a></li>
                <li class="breadcrumb-item active">Tableau de bord</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="btn-group float-end">
                <a href="{{ route('shops.dashboard', ['shop' => $shop, 'period' => 'day']) }}" class="btn btn-sm {{ $period == 'day' ? 'btn-primary' : 'btn-outline-primary' }}">Aujourd'hui</a>
                <a href="{{ route('shops.dashboard', ['shop' => $shop, 'period' => 'week']) }}" class="btn btn-sm {{ $period == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Cette semaine</a>
                <a href="{{ route('shops.dashboard', ['shop' => $shop, 'period' => 'month']) }}" class="btn btn-sm {{ $period == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Ce mois</a>
                <a href="{{ route('shops.dashboard', ['shop' => $shop, 'period' => 'year']) }}" class="btn btn-sm {{ $period == 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Cette année</a>
            </div>
        </div>
    </div>
    
    <!-- Résumé des ventes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total des ventes</div>
                            <div class="fs-4">{{ number_format($salesData['totalSales'], 2, ',', ' ') }} FCFA</div>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">Période: {{ $period == 'day' ? 'Aujourd\'hui' : ($period == 'week' ? 'Cette semaine' : ($period == 'month' ? 'Ce mois' : 'Cette année')) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Nombre de factures</div>
                            <div class="fs-4">{{ $salesData['billCount'] }}</div>
                        </div>
                        <i class="fas fa-file-invoice fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">Moyenne : {{ number_format($salesData['averageBillValue'], 2, ',', ' ') }} FCFA / facture</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Produits en stock faible</div>
                            <div class="fs-4">{{ count($stockData['lowStockProducts']) }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#stock-section">Voir les détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Produits en rupture</div>
                            <div class="fs-4">{{ count($stockData['outOfStockProducts']) }}</div>
                        </div>
                        <i class="fas fa-times-circle fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#stock-section">Voir les détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique des ventes -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Évolution des ventes
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Ventes par catégorie
                </div>
                <div class="card-body">
                    @if(count($salesData['salesByCategory']) > 0)
                        <canvas id="categoriesChart" width="100%" height="40"></canvas>
                    @else
                        <div class="alert alert-info m-0">
                            Aucune donnée disponible pour cette période.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Performance des vendeurs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    Performance des vendeurs
                </div>
                <div class="card-body">
                    @if(count($vendorPerformance) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Vendeur</th>
                                        <th>Ventes totales</th>
                                        <th>Nombre de factures</th>
                                        <th>Produits vendus</th>
                                        <th>Panier moyen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendorPerformance as $vendor)
                                        <tr>
                                            <td>{{ $vendor['name'] }}</td>
                                            <td>{{ number_format($vendor['sales'], 2, ',', ' ') }} FCFA</td>
                                            <td>{{ $vendor['billCount'] }}</td>
                                            <td>{{ $vendor['productCount'] }}</td>
                                            <td>{{ number_format($vendor['averageBillValue'], 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-0">
                            Aucune donnée de performance pour cette période.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produits les plus vendus et état des stocks -->
    <div class="row" id="stock-section">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-trophy me-1"></i>
                    Produits les plus vendus
                </div>
                <div class="card-body">
                    @if(count($salesData['topProducts']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>CA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesData['topProducts'] as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->total_quantity }}</td>
                                            <td>{{ number_format($product->total_sales, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-0">
                            Aucune donnée de vente pour cette période.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Alertes de stock
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="stockTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="low-stock-tab" data-bs-toggle="tab" data-bs-target="#low-stock" type="button" role="tab" aria-controls="low-stock" aria-selected="true">Stock faible ({{ count($stockData['lowStockProducts']) }})</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="out-of-stock-tab" data-bs-toggle="tab" data-bs-target="#out-of-stock" type="button" role="tab" aria-controls="out-of-stock" aria-selected="false">Rupture ({{ count($stockData['outOfStockProducts']) }})</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="stockTabsContent">
                        <div class="tab-pane fade show active" id="low-stock" role="tabpanel" aria-labelledby="low-stock-tab">
                            @if(count($stockData['lowStockProducts']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Stock actuel</th>
                                                <th>Seuil d'alerte</th>
                                                <th>État</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockData['lowStockProducts'] as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->stock }}</td>
                                                    <td>{{ $product->alert_threshold }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            @php
                                                                $ratio = $product->stock / $product->alert_threshold * 100;
                                                                $color = $ratio < 30 ? 'danger' : ($ratio < 70 ? 'warning' : 'success');
                                                            @endphp
                                                            <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $ratio }}%;" aria-valuenow="{{ $ratio }}" aria-valuemin="0" aria-valuemax="100">{{ round($ratio) }}%</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-success m-0">
                                    Tous les produits ont un stock suffisant.
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="out-of-stock" role="tabpanel" aria-labelledby="out-of-stock-tab">
                            @if(count($stockData['outOfStockProducts']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Référence</th>
                                                <th>Catégorie</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockData['outOfStockProducts'] as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->reference }}</td>
                                                    <td>{{ $product->category->name ?? 'Non catégorisé' }}</td>
                                                    <td>
                                                        <a href="{{ route('inventory.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-plus-circle"></i> Ajouter stock
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-success m-0">
                                    Aucun produit en rupture de stock.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des ventes
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            const salesData = @json($salesData['salesChartData']);
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Ventes (€)',
                        data: salesData.data,
                        backgroundColor: 'rgba(0, 97, 242, 0.4)',
                        borderColor: 'rgba(0, 97, 242, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Graphique des catégories
        const categoriesCtx = document.getElementById('categoriesChart');
        if (categoriesCtx) {
            const categoriesData = @json($salesData['salesByCategory']);
            if (categoriesData.length > 0) {
                const colors = [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#858796', '#6f42c1', '#20c9a6', '#fd7e14'
                ];
                
                new Chart(categoriesCtx, {
                    type: 'pie',
                    data: {
                        labels: categoriesData.map(cat => cat.name),
                        datasets: [{
                            data: categoriesData.map(cat => cat.total_sales),
                            backgroundColor: colors.slice(0, categoriesData.length),
                            hoverBackgroundColor: colors.slice(0, categoriesData.length).map(color => color + 'dd')
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endsection 