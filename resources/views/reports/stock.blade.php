@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Rapport de Stock</h2>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('reports.stock') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="site_id" class="form-label">Site</label>
                    <select name="site_id" id="site_id" class="form-select">
                        <option value="">Tous les sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" 
                                {{ $selectedSite == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="department_id" class="form-label">Département</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">Tous les départements</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                {{ $selectedDepartment == $department->id ? 'selected' : '' }}
                                data-site-id="{{ $department->site->id }}">
                                {{ $department->name }} ({{ $department->site->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="stock_level" class="form-label">Niveau de stock</label>
                    <select name="stock_level" id="stock_level" class="form-select">
                        <option value="">Tous les niveaux</option>
                        <option value="low" {{ request('stock_level') === 'low' ? 'selected' : '' }}>
                            Stock bas
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" name="search" id="search" class="form-control" 
                        value="{{ request('search') }}" 
                        placeholder="Nom ou référence du produit">
                </div>

                <div class="col-md-12 d-flex justify-content-between align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <a href="{{ route('reports.stock') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('reports.stock.export', request()->all()) }}" 
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exporter
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ route('reports.stock', array_merge(
                                    request()->all(), 
                                    ['sort' => 'reference', 'direction' => request('sort') === 'reference' && request('direction') === 'asc' ? 'desc' : 'asc']
                                )) }}" class="text-decoration-none text-dark">
                                    Référence
                                    @if(request('sort') === 'reference')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('reports.stock', array_merge(
                                    request()->all(), 
                                    ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc']
                                )) }}" class="text-decoration-none text-dark">
                                    Produit
                                    @if(request('sort') === 'name')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Site</th>
                            <th>Département</th>
                            <th>Stock actuel</th>
                            <th>Stock minimum</th>
                            <th>Statut</th>
                            <th>Dernier mouvement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @foreach($product->stocks as $stock)
                                <tr>
                                    <td>{{ $product->reference }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $stock->department->site->name }}</td>
                                    <td>{{ $stock->department->name }}</td>
                                    <td>{{ $stock->quantity }} {{ $product->unit }}</td>
                                    <td>{{ $product->minimum_stock }} {{ $product->unit }}</td>
                                    <td>
                                        @if($stock->quantity <= $product->minimum_stock)
                                            <span class="badge bg-danger">Stock bas</span>
                                        @else
                                            <span class="badge bg-success">Stock OK</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $lastMovement = $product->stockMovements()
                                                ->where('department_id', $stock->department_id)
                                                ->latest()
                                                ->first();
                                        @endphp
                                        @if($lastMovement)
                                            {{ $lastMovement->type === 'entry' ? 'Entrée' : 'Sortie' }} 
                                            de {{ $lastMovement->quantity }} {{ $product->unit }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $lastMovement->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucun produit trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mise à jour dynamique des départements en fonction du site sélectionné
    $('#site_id').change(function() {
        var siteId = $(this).val();
        var $departmentSelect = $('#department_id');
        
        // Réinitialiser la sélection du département
        $departmentSelect.val('');
        
        // Masquer/afficher les options de département en fonction du site
        if (siteId) {
            $departmentSelect.find('option').each(function() {
                var $option = $(this);
                if (!$option.val() || $option.data('site-id') == siteId) {
                    $option.show();
                } else {
                    $option.hide();
                }
            });
        } else {
            $departmentSelect.find('option').show();
        }
    });

    // Déclencher le changement au chargement si un site est sélectionné
    if ($('#site_id').val()) {
        $('#site_id').trigger('change');
    }
});
</script>
@endpush
