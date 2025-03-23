@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Checklist du {{ \Carbon\Carbon::parse($checklist->date_verification)->format('d/m/Y') }}</h2>
        <div>
            <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('checklists.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Zone de travail et Atelier -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-tools"></i> Zone de travail et Atelier</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5>Ponts élévateurs</h5>
                        <div class="badge {{ $checklist->ponts_elevateurs === 'Fonctionnels' ? 'bg-success' : 'bg-danger' }} mb-2">
                            {{ $checklist->ponts_elevateurs }}
                        </div>
                        @if($checklist->ponts_elevateurs_remarques)
                            <p class="text-muted"><strong>Remarques:</strong> {{ $checklist->ponts_elevateurs_remarques }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5>Écran TV</h5>
                        <div class="badge {{ $checklist->ecran_tv === 'Fonctionnel' ? 'bg-success' : 'bg-danger' }} mb-2">
                            {{ $checklist->ecran_tv }}
                        </div>
                        @if($checklist->ecran_tv_remarques)
                            <p class="text-muted"><strong>Remarques:</strong> {{ $checklist->ecran_tv_remarques }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Continuer avec les autres éléments de la même manière -->
        </div>
    </div>

    <!-- Zone technique -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-oil-can"></i> Zone technique et Gestion des huiles</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5>Cuve de récupération d'huile</h5>
                        <div class="badge {{ $checklist->cuve_huile === 'Vérifiée' ? 'bg-success' : 'bg-danger' }} mb-2">
                            {{ $checklist->cuve_huile }}
                        </div>
                        @if($checklist->cuve_huile_remarques)
                            <p class="text-muted"><strong>Remarques:</strong> {{ $checklist->cuve_huile_remarques }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parking et Façade -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-parking"></i> Parking extérieur et Façade</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Éléments du parking -->
            </div>
        </div>
    </div>

    <!-- Contrôle final -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> Contrôle final</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>État général</h5>
                    <div class="badge {{ $checklist->proprete_finale === 'Acceptable' ? 'bg-success' : 'bg-warning' }} mb-3">
                        {{ $checklist->proprete_finale }}
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Sécurité</h5>
                    <div class="badge {{ $checklist->securite_finale === 'Oui' ? 'bg-success' : 'bg-danger' }} mb-3">
                        {{ $checklist->securite_finale }}
                    </div>
                </div>
            </div>

            @if($checklist->materiel_manquant)
                <div class="alert alert-warning">
                    <h5>Matériel manquant</h5>
                    <p>{{ $checklist->materiel_manquant }}</p>
                </div>
            @endif

            @if($checklist->recommandations)
                <div class="alert alert-info">
                    <h5>Recommandations</h5>
                    <p>{{ $checklist->recommandations }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
