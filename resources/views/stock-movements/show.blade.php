@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du mouvement de stock</h5>
                    <div>
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Informations du produit</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Produit</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('products.show', $stockMovement->product) }}">
                                        {{ $stockMovement->product->name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-4">Référence</dt>
                                <dd class="col-sm-8">{{ $stockMovement->product->reference }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Informations du département</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Site</dt>
                                <dd class="col-sm-8">{{ $stockMovement->department->site->name }}</dd>

                                <dt class="col-sm-4">Département</dt>
                                <dd class="col-sm-8">{{ $stockMovement->department->name }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Détails du mouvement</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Type</dt>
                                <dd class="col-sm-8">
                                    @if($stockMovement->type === 'entry')
                                        <span class="badge bg-success">Entrée</span>
                                    @else
                                        <span class="badge bg-danger">Sortie</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Quantité</dt>
                                <dd class="col-sm-8">
                                    {{ $stockMovement->quantity }} {{ $stockMovement->product->unit }}
                                </dd>

                                <dt class="col-sm-4">Date</dt>
                                <dd class="col-sm-8">
                                    {{ $stockMovement->created_at->format('d/m/Y H:i') }}
                                </dd>

                                <dt class="col-sm-4">Utilisateur</dt>
                                <dd class="col-sm-8">{{ $stockMovement->user->name }}</dd>

                                @if($stockMovement->reason)
                                    <dt class="col-sm-4">Motif</dt>
                                    <dd class="col-sm-8">{{ $stockMovement->reason }}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">État du stock</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Stock actuel</dt>
                                <dd class="col-sm-8">
                                    {{ $stockMovement->department->getCurrentStock($stockMovement->product) }}
                                    {{ $stockMovement->product->unit }}
                                </dd>

                                <dt class="col-sm-4">Stock minimum</dt>
                                <dd class="col-sm-8">
                                    {{ $stockMovement->product->minimum_stock }}
                                    {{ $stockMovement->product->unit }}
                                </dd>

                                <dt class="col-sm-4">Statut</dt>
                                <dd class="col-sm-8">
                                    @php
                                        $currentStock = $stockMovement->department->getCurrentStock($stockMovement->product);
                                    @endphp
                                    @if($currentStock <= $stockMovement->product->minimum_stock)
                                        <span class="badge bg-danger">Stock faible</span>
                                    @else
                                        <span class="badge bg-success">Stock normal</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
