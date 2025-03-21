@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 80px;"> <!-- Ajout du padding-top ici -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Sites Oh Garage</h2>
        </div>
        <div class="col-md-6 text-end">
            @can('create', App\Models\Site::class)
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau site
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
                            <th>Localisation</th>
                            <th>Départements</th>
                            <th>Utilisateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sites as $site)
                        <tr>
                            <td>{{ $site->name }}</td>
                            <td>{{ $site->location }}</td>
                            <td>{{ $site->departments_count }}</td>
                            <td>{{ $site->users_count }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('update', $site)
                                    <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $site)
                                    <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce site ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun site trouvé</td>
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
</style>
@endpush
