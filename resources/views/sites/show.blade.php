@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 80px;">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ $site->name }}</h2>
            <p class="text-muted">
                <i class="fas fa-map-marker-alt"></i> {{ $site->location }}
            </p>
        </div>
        <div class="col-md-6 text-end">
            @can('update', $site)
            <a href="{{ route('sites.edit', $site) }}" class="btn btn-warning rounded-pill px-4">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('sites.index') }}" class="btn btn-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Départements -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Départements</h5>
                    @can('create', App\Models\Department::class)
                    <a href="{{ route('departments.create') }}" class="btn btn-light btn-sm rounded-pill">
                        <i class="fas fa-plus"></i> Nouveau département
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Nombre de produits</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $department)
                                <tr>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->stocks_count }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-info rounded-pill">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $department)
                                            <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning rounded-pill">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucun département trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Utilisateurs du site</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($users as $user)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $user->name }}</h6>
                                <small>{{ $user->email }}</small>
                            </div>
                            <small class="text-muted">
                                @switch($user->role)
                                    @case('super_admin')
                                        <span class="badge bg-danger">Super Admin</span>
                                        @break
                                    @case('stock_manager')
                                        <span class="badge bg-primary">Gestionnaire</span>
                                        @break
                                    @case('consultant')
                                        <span class="badge bg-info">Consultant</span>
                                        @break
                                @endswitch
                            </small>
                        </div>
                        @empty
                        <div class="list-group-item text-center">
                            Aucun utilisateur assigné
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Départements
                            <span class="badge bg-primary rounded-pill">{{ $departments->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Utilisateurs
                            <span class="badge bg-info rounded-pill">{{ $users->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total des produits
                            <span class="badge bg-success rounded-pill">
                                {{ $departments->sum('stocks_count') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Ajout de transitions pour les boutons */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-group .btn {
        margin-right: 5px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 10px 15px;
    }

    .list-group-item:first-child {
        border-top: none;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    /* Style des badges */
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.8em;
    }
</style>
@endpush
