@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mouvements de stock</h5>
                    <div>
                        @can('create', App\Models\StockMovement::class)
                        <a href="{{ route('stock-movements.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Nouveau mouvement
                        </a>
                        @endcan
                        <a href="{{ route('stock-movements.export', request()->query()) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-file-export"></i> Exporter
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('stock-movements.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="site" class="form-label">Site</label>
                                <select name="site" id="site" class="form-select">
                                    <option value="">Tous les sites</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="department" class="form-label">Département</label>
                                <select name="department" id="department" class="form-select">
                                    <option value="">Tous les départements</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="entry" {{ request('type') === 'entry' ? 'selected' : '' }}>Entrée</option>
                                    <option value="exit" {{ request('type') === 'exit' ? 'selected' : '' }}>Sortie</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
                                <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Table des mouvements -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Site</th>
                                    <th>Département</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>Utilisateur</th>
                                    <th>Motif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockMovements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $movement->product) }}">
                                            {{ $movement->product->reference }} - {{ $movement->product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $movement->department->site->name }}</td>
                                    <td>{{ $movement->department->name }}</td>
                                    <td>
                                        @if($movement->type === 'entry')
                                            <span class="badge bg-success">Entrée</span>
                                        @else
                                            <span class="badge bg-danger">Sortie</span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->quantity }} {{ $movement->product->unit }}</td>
                                    <td>{{ $movement->user->name }}</td>
                                    <td>{{ $movement->reason ?: '-' }}</td>
                                    <td>
                                        <a href="{{ route('stock-movements.show', $movement) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Aucun mouvement trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $stockMovements->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('site').addEventListener('change', function() {
    // Réinitialiser le département si le site change
    document.getElementById('department').value = '';
    this.form.submit();
});
</script>
@endpush
