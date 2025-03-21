@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nouveau mouvement de stock</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('stock-movements.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produit</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" 
                                id="product_id" name="product_id" required {{ request()->has('product') ? 'disabled' : '' }}>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        {{ (old('product_id') == $product->id || request('product') == $product->id) ? 'selected' : '' }}>
                                        {{ $product->reference }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(request()->has('product'))
                                <input type="hidden" name="product_id" value="{{ request('product') }}">
                            @endif
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id" required {{ request()->has('department') ? 'disabled' : '' }}>
                                <option value="">Sélectionner un département</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                        {{ (old('department_id') == $department->id || request('department') == $department->id) ? 'selected' : '' }}>
                                        {{ $department->site->name }} - {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(request()->has('department'))
                                <input type="hidden" name="department_id" value="{{ request('department') }}">
                            @endif
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type de mouvement</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required {{ request()->has('type') ? 'disabled' : '' }}>
                                <option value="entry" {{ (old('type') == 'entry' || request('type') == 'entry') ? 'selected' : '' }}>
                                    Entrée
                                </option>
                                <option value="exit" {{ (old('type') == 'exit' || request('type') == 'exit') ? 'selected' : '' }}>
                                    Sortie
                                </option>
                            </select>
                            @if(request()->has('type'))
                                <input type="hidden" name="type" value="{{ request('type') }}">
                            @endif
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Motif</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                id="reason" name="reason" rows="2">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="destination-group" style="display: none;">
                            <label for="destination" class="form-label">Destination</label>
                            <input type="text" class="form-control @error('destination') is-invalid @enderror" 
                                id="destination" name="destination" value="{{ old('destination') }}">
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mise à jour dynamique des stocks disponibles
    const productSelect = document.getElementById('product_id');
    const departmentSelect = document.getElementById('department_id');
    const typeSelect = document.getElementById('type');
    const quantityInput = document.getElementById('quantity');
    const destinationGroup = document.getElementById('destination-group');

    function updateQuantityMax() {
        if (!productSelect.value || !departmentSelect.value || typeSelect.value !== 'exit') {
            quantityInput.removeAttribute('max');
            return;
        }

        // Appel AJAX pour obtenir le stock disponible
        fetch(`/api/stock/${productSelect.value}/${departmentSelect.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.quantity) {
                    quantityInput.setAttribute('max', data.quantity);
                }
            });
    }

    function toggleDestination() {
        destinationGroup.style.display = typeSelect.value === 'exit' ? 'block' : 'none';
        const destinationInput = document.getElementById('destination');
        destinationInput.required = typeSelect.value === 'exit';
    }

    productSelect.addEventListener('change', updateQuantityMax);
    departmentSelect.addEventListener('change', updateQuantityMax);
    typeSelect.addEventListener('change', function() {
        updateQuantityMax();
        toggleDestination();
    });

    // Initialiser l'affichage du champ destination
    toggleDestination();
});
</script>
@endpush
