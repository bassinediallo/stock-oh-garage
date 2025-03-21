@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- En-tête du tableau de bord -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2>Tableau de bord</h2>
            <p class="text-muted">Bienvenue, {{ auth()->user()->name }}</p>
        </div>
        <div>
            @can('create', App\Models\StockMovement::class)
            <a href="{{ route('stock-movements.create') }}" class="btn btn-lg btn-gradient text-white">
                <i class="fas fa-plus-circle"></i> Nouveau mouvement
            </a>
            @endcan
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        @foreach (['Produits', 'Mouvements', 'Stock faible', 'Départements'] as $index => $title)
        <div class="col-md-3 mb-4">
            <div class="card shadow-lg border-{{ ['primary', 'success', 'warning', 'info'][$index] }}">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">{{ $title }}</h6>
                        <h2 class="mb-0">
                            @if($index == 0)
                                {{ App\Models\Product::count() }}
                            @elseif($index == 1)
                                {{ App\Models\StockMovement::whereDate('created_at', today())->count() }}
                            @elseif($index == 2)
                                {{ App\Models\Product::where('status', 'Stock faible')->count() }}
                            @else
                                {{ App\Models\Department::count() }}
                            @endif
                        </h2>
                    </div>
                    <div class="icon-container bg-{{ ['primary', 'success', 'warning', 'info'][$index] }} text-white rounded-circle p-3">
                        <i class="fas fa-{{ ['box', 'exchange-alt', 'exclamation-triangle', 'building'][$index] }} fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <!-- Section des produits en stock faible -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Produits en stock faible
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Stock total</th>
                                    <th>Stock minimum</th>
                                    <th>Départements</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $item)
                                <tr>
                                    <td>{{ $item['product']->name }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $item['total_stock'] }}
                                        </span>
                                    </td>
                                    <td>{{ $item['product']->minimum_stock }}</td>
                                    <td>
                                        @foreach($item['departments'] as $dept)
                                            <div>{{ $dept['name'] }}: {{ $dept['quantity'] }}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.show', $item['product']) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Voir le produit">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('create', App\Models\StockMovement::class)
                                            <a href="{{ route('stock-movements.create', ['product' => $item['product']->id]) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Ajouter du stock">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Aucun produit en stock faible
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers mouvements -->
        <div class="col-md-6">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Derniers mouvements</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentMovements as $movement)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">
                                    <a href="{{ route('products.show', $movement->product) }}" class="text-decoration-none">
                                        {{ $movement->product->name }}
                                    </a>
                                </h6>
                                <small class="text-muted">{{ $movement->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                <span class="badge bg-{{ $movement->type == 'entry' ? 'success' : 'danger' }}">
                                    {{ $movement->type == 'entry' ? 'Entrée' : 'Sortie' }}
                                </span>
                                {{ $movement->quantity }} {{ $movement->product->unit }} dans {{ $movement->department->name }}
                            </p>
                            <small>
                                <i class="fas fa-user"></i> {{ $movement->user->name }}
                                @if($movement->reason)
                                    <br><i class="fas fa-info-circle"></i> {{ $movement->reason }}
                                @endif
                            </small>
                        </div>
                        @empty
                        <div class="list-group-item text-center">Aucun mouvement récent</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Mouvements par jour (7 derniers jours)</h5>
                </div>
                <div class="card-body">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition des stocks par site</h5>
                </div>
                <div class="card-body">
                    <canvas id="stocksChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- État des stocks par département -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building"></i> État des stocks par département</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Département</th>
                                    <th class="text-center">Nombre de produits</th>
                                    <th class="text-center">Stock faible</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                    <tr>
                                        <td>{{ $department->site->name }}</td>
                                        <td>{{ $department->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $department->products_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($department->low_stock_count > 0)
                                                <button type="button" 
                                                    class="btn btn-danger btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#lowStockModal-{{ $department->id }}">
                                                    {{ $department->low_stock_count }} produit(s)
                                                </button>

                                                <!-- Modal pour les stocks faibles -->
                                                <div class="modal fade" id="lowStockModal-{{ $department->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">
                                                                    Produits en stock faible - {{ $department->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Référence</th>
                                                                                <th>Produit</th>
                                                                                <th class="text-center">Stock actuel</th>
                                                                                <th class="text-center">Stock minimum</th>
                                                                                <th class="text-end">Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($department->stocks()
                                                                                ->join('products', 'department_stocks.product_id', '=', 'products.id')
                                                                                ->where('quantity', '>', 0)
                                                                                ->whereColumn('quantity', '<=', 'products.minimum_stock')
                                                                                ->select('products.*', 'department_stocks.quantity as current_stock')
                                                                                ->get() as $product)
                                                                                <tr>
                                                                                    <td>{{ $product->reference }}</td>
                                                                                    <td>{{ $product->name }}</td>
                                                                                    <td class="text-center text-danger">
                                                                                        {{ $product->current_stock }} {{ $product->unit }}
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        {{ $product->minimum_stock }} {{ $product->unit }}
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        <a href="{{ route('stock-movements.create', ['product_id' => $product->id, 'department_id' => $department->id]) }}" 
                                                                                            class="btn btn-success btn-sm">
                                                                                            <i class="fas fa-plus"></i> Ajouter du stock
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-success">Aucun</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('reports.stock', ['department_id' => $department->id]) }}" 
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Voir les stocks
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin-right: 5px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.7em;
    }
    .table-sm td {
        vertical-align: middle;
    }
    .card-header .fas {
        margin-right: 0.5rem;
    }
    .table-responsive {
        min-height: 200px;
    }
    .btn-gradient {
        background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
        border: none;
    }
    .icon-container {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .card-header {
        font-size: 1.1em;
        font-weight: bold;
    }
    .list-group-item:hover {
        background-color: #f1f1f1;
    }
    .card {
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const movementsCtx = document.getElementById('movementsChart').getContext('2d');
    new Chart(movementsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($movementsChartData['labels']) !!},
            datasets: [
                {
                    label: 'Entrées',
                    data: {!! json_encode($movementsChartData['entries']) !!},
                    backgroundColor: '#198754',
                },
                {
                    label: 'Sorties',
                    data: {!! json_encode($movementsChartData['exits']) !!},
                    backgroundColor: '#dc3545',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    const stocksCtx = document.getElementById('stocksChart').getContext('2d');
    new Chart(stocksCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($stocksChartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($stocksChartData['data']) !!},
                backgroundColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
