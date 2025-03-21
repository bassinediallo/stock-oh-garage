@extends('layouts.app')

@section('content')
<div class="container mt-5"> <!-- Ajout de la marge en haut -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Départements</h2>
        </div>
        <div class="col-md-6 text-end">
            @can('create', App\Models\Department::class)
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau département
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
                            <th>Nom</th>
                            <th>Site</th>
                            <th>Nombre de produits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $department->site->name }}</span>
                            </td>
                            <td>
                                @foreach($department->stocks as $stock)
                                    <div>{{ $stock->quantity }} {{ $stock->product->unit }}</div>
                                @endforeach
                            </td> 

                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('update', $department)
                                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $department)
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce département ?')"
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
                            <td colspan="5" class="text-center">Aucun département trouvé</td>
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
