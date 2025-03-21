@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Informations du profil -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mon profil</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Nouveau mot de passe
                                <small class="text-muted">(laisser vide pour ne pas modifier)</small>
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Préférences -->
            <!--div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Préférences</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.preferences') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label d-block">Notifications par email</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                    id="notify_low_stock" name="preferences[notify_low_stock]" 
                                    {{ $user->preferences['notify_low_stock'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_low_stock">
                                    M'alerter quand un produit est en stock faible
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                    id="notify_movements" name="preferences[notify_movements]" 
                                    {{ $user->preferences['notify_movements'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_movements">
                                    M'informer des mouvements de stock importants
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="default_view" class="form-label">Vue par défaut</label>
                            <select class="form-select" id="default_view" name="preferences[default_view]">
                                <option value="dashboard" 
                                    {{ ($user->preferences['default_view'] ?? '') == 'dashboard' ? 'selected' : '' }}>
                                    Tableau de bord
                                </option>
                                <option value="products" 
                                    {{ ($user->preferences['default_view'] ?? '') == 'products' ? 'selected' : '' }}>
                                    Liste des produits
                                </option>
                                <option value="movements" 
                                    {{ ($user->preferences['default_view'] ?? '') == 'movements' ? 'selected' : '' }}>
                                    Mouvements de stock
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-cog"></i> Enregistrer les préférences
                        </button>
                    </form>
                </div>
            </div-->

            <!-- Activité récente -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Mon activité récente</h5>
                </div>

                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentActivity as $activity)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    @if($activity->type === 'entry')
                                        <span class="badge bg-success">Entrée</span>
                                    @else
                                        <span class="badge bg-danger">Sortie</span>
                                    @endif
                                    {{ $activity->product->name }}
                                </h6>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                {{ $activity->quantity }} {{ $activity->product->unit }}
                                dans {{ $activity->department->name }}
                            </p>
                            @if($activity->reason)
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> {{ $activity->reason }}
                            </small>
                            @endif
                        </div>
                        @empty
                        <div class="list-group-item text-center">
                            Aucune activité récente
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.7em;
    }
</style>
@endpush
