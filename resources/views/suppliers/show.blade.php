@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du fournisseur</h5>
                    <div>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                        <form id="delete-form" action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informations générales</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Nom</dt>
                                <dd class="col-sm-8">{{ $supplier->name }}</dd>

                                <dt class="col-sm-4">Contact</dt>
                                <dd class="col-sm-8">{{ $supplier->contact_person ?: 'Non spécifié' }}</dd>

                                <dt class="col-sm-4">Email</dt>
                                <dd class="col-sm-8">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                    @else
                                        Non spécifié
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Téléphone</dt>
                                <dd class="col-sm-8">
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                                    @else
                                        Non spécifié
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Adresse et notes</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Adresse</dt>
                                <dd class="col-sm-8">{{ $supplier->address ?: 'Non spécifiée' }}</dd>

                                <dt class="col-sm-4">Notes</dt>
                                <dd class="col-sm-8">{{ $supplier->notes ?: 'Aucune note' }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Produits fournis</h6>
                        @if($supplier->products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supplier->products as $product)
                                            <tr>
                                                <td>{{ $product->reference }}</td>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ Str::limit($product->description, 50) }}</td>
                                                <td>
                                                    <a href="{{ route('products.show', $product) }}" 
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Aucun produit associé à ce fournisseur.</p>
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
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
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
