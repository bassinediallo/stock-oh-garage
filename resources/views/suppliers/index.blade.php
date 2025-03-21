@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-auto">
            <h1>Liste des fournisseurs</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau fournisseur
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->phone)
                                        <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('suppliers.show', $supplier) }}" 
                                            class="btn btn-sm btn-info" 
                                            title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" 
                                            class="btn btn-sm btn-warning" 
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $supplier->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $supplier->id }}" 
                                            action="{{ route('suppliers.destroy', $supplier) }}" 
                                            method="POST" 
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(supplierId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
        document.getElementById('delete-form-' + supplierId).submit();
    }
}
</script>
@endpush
