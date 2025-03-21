@extends('layouts.app')

@section('content')
<div class="container mt-5"> <!-- Ajout de la marge en haut -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Catalogue des produits</h2>
        </div>
        <div class="col-md-6 text-end">
            @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau produit
            </a>
            @endcan
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Stock total</th>
                            <th>Stock minimum</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->reference }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded me-2"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-box text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->category }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $totalStock = $product->getTotalStock();
                                @endphp
                                <span class="badge bg-{{ $product->status === 'Stock faible' ? 'danger' : 'success' }}">
                                    {{ $totalStock }}
                                </span>
                            </td>
                            <td class="text-center">{{ $product->minimum_stock }}</td>
                            <td>
                                <span class="badge bg-{{ $product->status === 'Stock faible' ? 'danger' : 'success' }}">
                                    {{ $product->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('update', $product)
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $product)
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun produit trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
</style>
@endpush
