@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du produit</h5>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                        <form id="delete-form" action="{{ route('products.destroy', $product) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                    alt="{{ $product->name }}" 
                                    class="img-fluid rounded mb-3">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif

                            <!-- Statistiques -->
                            <div class="card mt-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Statistiques</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-7">Stock total</dt>
                                        <dd class="col-sm-5 text-end">
                                            {{ $product->getTotalStock() }} {{ $product->unit }}
                                        </dd>

                                        <dt class="col-sm-7">Stock minimum</dt>
                                        <dd class="col-sm-5 text-end">
                                            {{ $product->minimum_stock }} {{ $product->unit }}
                                        </dd>

                                        <dt class="col-sm-7">État global</dt>
                                        <dd class="col-sm-5 text-end">
                                            @if($product->hasLowStock())
                                                <span class="badge bg-danger">Stock faible</span>
                                            @else
                                                <span class="badge bg-success">Stock normal</span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h4>{{ $product->name }}</h4>
                            <dl class="row">
                                <dt class="col-sm-3">Référence</dt>
                                <dd class="col-sm-9">{{ $product->reference }}</dd>

                                <dt class="col-sm-3">Description</dt>
                                <dd class="col-sm-9">{{ $product->description ?: 'Aucune description' }}</dd>

                                <dt class="col-sm-3">Unité</dt>
                                <dd class="col-sm-9">{{ $product->unit }}</dd>

                                <dt class="col-sm-3">Stock minimum</dt>
                                <dd class="col-sm-9">{{ $product->minimum_stock }}</dd>

                                <dt class="col-sm-3">Fournisseurs</dt>
                                <dd class="col-sm-9">
                                    @if($product->suppliers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Référence</th>
                                                        <th>Prix unitaire</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->suppliers as $supplier)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('suppliers.show', $supplier) }}">
                                                                    {{ $supplier->name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $supplier->pivot->reference ?: '-' }}</td>
                                                            <td>
                                                                @if($supplier->pivot->unit_price)
                                                                    {{ number_format($supplier->pivot->unit_price, 2) }} €
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <span class="text-muted">Aucun fournisseur associé</span>
                                    @endif
                                </dd>
                            </dl>

                            <!-- Stock par département -->
                            <h5 class="mt-4 mb-3">État des stocks par département</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Site</th>
                                            <th>Département</th>
                                            <th>Quantité</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                            @php
                                                $currentStock = $department->getCurrentStock($product);
                                            @endphp
                                            <tr>
                                                <td>{{ $department->site->name }}</td>
                                                <td>{{ $department->name }}</td>
                                                <td>{{ $currentStock }} {{ $product->unit }}</td>
                                                <td>
                                                    @if($currentStock <= $product->minimum_stock)
                                                        <span class="badge bg-danger">Stock faible</span>
                                                    @else
                                                        <span class="badge bg-success">Stock normal</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('stock-movements.create', ['product' => $product->id, 'department' => $department->id]) }}" 
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-exchange-alt"></i> Mouvement
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

                <div class="card-footer">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
