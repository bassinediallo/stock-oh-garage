@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Gestion des utilisateurs</h2>
        </div>
        <div class="col-md-6 text-end">
            @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nouvel utilisateur
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
                            <th>Email</th>
                            <th>Site</th>
                            <th>Rôle</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->site)
                                    <span class="badge bg-info">{{ $user->site->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                @switch($user->role)
                                    @case('super_admin')
                                        <span class="badge bg-danger">Super Admin</span>
                                        @break
                                    @case('stock_manager')
                                        <span class="badge bg-primary">Gestionnaire</span>
                                        @break
                                    @case('consultant')
                                        <span class="badge bg-success">Consultant</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Jamais connecté</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view', $user)
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
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
                            <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
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
