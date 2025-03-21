@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nouveau produit</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                id="reference" name="reference" value="{{ old('reference') }}" required>
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Unité</label>
                            <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                <option value="">Sélectionner une unité</option>
                                <option value="pièce" {{ old('unit') == 'pièce' ? 'selected' : '' }}>Pièce</option>
                                <option value="litre" {{ old('unit') == 'litre' ? 'selected' : '' }}>Litre</option>
                                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogramme</option>
                                <option value="mètre" {{ old('unit') == 'mètre' ? 'selected' : '' }}>Mètre</option>
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="minimum_stock" class="form-label">Stock minimum</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0" required>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total_stock" class="form-label">Stock initial</label>
                            <input type="number" class="form-control @error('total_stock') is-invalid @enderror" 
                                id="total_stock" name="total_stock" value="{{ old('total_stock', 0) }}" min="0" required>
                            <div class="form-text">Quantité initiale en stock lors de la création du produit</div>
                            @error('total_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id" required>
                                <option value="">Sélectionner un département</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }} ({{ $department->site->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="suppliers" class="form-label">Fournisseurs</label>
                            <div class="input-group">
                                <select class="form-select select2-suppliers @error('suppliers') is-invalid @enderror" 
                                    id="suppliers" name="suppliers[]" multiple>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                            {{ (collect(old('suppliers'))->contains($supplier->id)) ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @error('suppliers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
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

<!-- Modal d'ajout rapide de fournisseur -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Nouveau fournisseur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickSupplierForm">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="supplier_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_contact" class="form-label">Personne à contacter</label>
                        <input type="text" class="form-control" id="supplier_contact" name="contact_person">
                    </div>
                    <div class="mb-3">
                        <label for="supplier_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="supplier_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="supplier_phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="supplier_phone" name="phone">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveSupplierBtn">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    console.log('jQuery est bien chargé'); // Debugging
});
</script>
<script>
$(document).ready(function() {
    // Initialisation de Select2 pour les fournisseurs
    $('.select2-suppliers').select2({
        theme: 'bootstrap-5',
        placeholder: 'Sélectionner un ou plusieurs fournisseurs',
        allowClear: true,
        ajax: {
            url: '{{ route("suppliers.search") }}',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });

    // Gestion de l'ajout rapide de fournisseur
    $('#saveSupplierBtn').click(function() {
        const form = $('#quickSupplierForm');
        const formData = {
            name: form.find('[name="name"]').val(),
            contact_person: form.find('[name="contact_person"]').val(),
            email: form.find('[name="email"]').val(),
            phone: form.find('[name="phone"]').val()
        };

        $.ajax({
            url: '{{ route("suppliers.quick-store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Créer une nouvelle option
                    const newOption = new Option(response.supplier.text, response.supplier.id, true, true);
                    $('.select2-suppliers').append(newOption).trigger('change');
                    
                    // Fermer le modal et réinitialiser le formulaire
                    $('#addSupplierModal').modal('hide');
                    form[0].reset();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(key => {
                    const input = form.find(`[name="${key}"]`);
                    input.addClass('is-invalid');
                    input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                });
            }
        });
    });

    // Réinitialiser les erreurs lors de la saisie
    $('#quickSupplierForm input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
@endpush
