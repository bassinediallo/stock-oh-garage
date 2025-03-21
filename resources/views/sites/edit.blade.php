@extends('layouts.app')

@section('content')
<div class="container mt-5"> <!-- Ajoutez une marge en haut -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Modifier le site - {{ $site->name }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('sites.update', $site) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du site</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $site->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Localisation</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                id="location" name="location" value="{{ old('location', $site->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
