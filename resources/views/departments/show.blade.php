@extends('layouts.app')

@section('content')
<div class="container  mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ $department->name }}</h2>
            <p class="text-muted">
                <i class="fas fa-building"></i> Site: {{ $department->site->name }}
                <br>
                <i class="fas fa-map-marker-alt"></i> {{ $department->site->location }}
            </p>
        </div>
        <div class="col-md-6 text-end">
            @can('update', $department)
            <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Stock actuel -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock actuel</h5>
                    @can('create', App\Models\StockMovement::class)
                    <a href="{{ route('stock-movements.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Nouveau mouvement
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Référence</th>
                                    <th>Quantité</th>
                                    <th>Stock minimum</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                <tr>
                                    <td>
                                        <a href="{{ route('products.show', $stock->product) }}">
                                            {{ $stock->product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $stock->product->reference }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>{{ $stock->product->minimum_stock }}</td>
                                    <td>
                                        @if($stock->quantity <= $stock->product->minimum_stock)
                                            <span class="badge bg-danger">Stock faible</span>
                                        @else
                                            <span class="badge bg-success">Stock normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucun produit en stock</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mouvements récents -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Derniers mouvements</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentMovements as $movement)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $movement->product->name }}</h6>
                                <small class="text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <p class="mb-1">
                                @if($movement->type === 'entry')
                                    <span class="badge bg-success">Entrée</span>
                                @else
                                    <span class="badge bg-danger">Sortie</span>
                                @endif
                                {{ $movement->quantity }} unités
                            </p>
                            <small>
                                <i class="fas fa-user"></i> {{ $movement->user->name }}
                                @if($movement->reason)
                                    <br><i class="fas fa-info-circle"></i> {{ $movement->reason }}
                                @endif
                            </small>
                        </div>
                        @empty
                        <div class="list-group-item text-center">
                            Aucun mouvement récent
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Produits en stock
                            <span class="badge bg-primary rounded-pill">{{ $stocks->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Produits en stock faible
                            <span class="badge bg-danger rounded-pill">
                                {{ $stocks->filter(function($stock) {
                                    return $stock->quantity <= $stock->product->minimum_stock;
                                })->count() }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Mouvements aujourd'hui
                            <span class="badge bg-info rounded-pill">
                                {{ $department->stockMovements()
                                    ->whereDate('created_at', today())
                                    ->count() }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.7em;
    }
</style>
@endpush
