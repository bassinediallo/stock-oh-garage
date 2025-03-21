@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nouvel utilisateur</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                                <option value="">Sélectionner un rôle</option>
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                                    Super Administrateur
                                </option>
                                <option value="stock_manager" {{ old('role') == 'stock_manager' ? 'selected' : '' }}>
                                    Gestionnaire de stock
                                </option>
                                <option value="consultant" {{ old('role') == 'consultant' ? 'selected' : '' }}>
                                    Consultant
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site assigné</label>
                            <select class="form-select @error('site_id') is-invalid @enderror" 
                                id="site_id" name="site_id">
                                <option value="">Sélectionner un site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }} ({{ $site->location }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Laisser vide pour les Super Administrateurs qui ont accès à tous les sites
                            </div>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
    const roleSelect = document.getElementById('role');
    const siteSelect = document.getElementById('site_id');
    const siteFormText = siteSelect.nextElementSibling;

    function updateSiteVisibility() {
        if (roleSelect.value === 'super_admin') {
            siteSelect.value = '';
            siteSelect.disabled = true;
            siteFormText.style.display = 'block';
        } else {
            siteSelect.disabled = false;
            siteFormText.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', updateSiteVisibility);
    updateSiteVisibility();
});
</script>
@endpush
