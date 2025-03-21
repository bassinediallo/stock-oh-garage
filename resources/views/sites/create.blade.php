@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 80px;"> <!-- Ajuste le padding-top si nécessaire -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded">
                <div class="card-header bg-primary text-white rounded-top">
                    <h5 class="mb-0">Nouveau site</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('sites.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du site</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required autofocus 
                                style="border-radius: 10px; border: 1px solid #ddd;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Localisation</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                id="location" name="location" value="{{ old('location') }}" required 
                                style="border-radius: 10px; border: 1px solid #ddd;">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sites.index') }}" class="btn btn-secondary rounded-pill">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill">
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

@push('styles')
<style>
    /* Ajout d'une bordure et ombre légère pour les boutons */
    .btn {
        border-radius: 25px;
    }

    .card-body {
        background-color: #f9f9f9;
    }

    .form-control {
        padding: 10px;
    }

    .form-label {
        font-weight: 500;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }
</style>
@endpush
