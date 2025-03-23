@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Nouvelle Checklist Mensuelle</h2>
    
    <form action="{{ route('checklists.store') }}" method="POST">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="form-group">
                    <label for="date_verification">Date de vérification</label>
                    <input type="date" class="form-control" id="date_verification" name="date_verification" required>
                </div>
            </div>
        </div>

        <!-- Zone de travail et Atelier -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>1. Zone de travail et Atelier</h4>
            </div>
            <div class="card-body">
                @foreach ([
                    'ponts_elevateurs' => ['Ponts élévateurs', ['Fonctionnels', 'Défectueux']],
                    'ecran_tv' => ['Écran TV (diffusion vidéos Oh Garage)', ['Fonctionnel', 'Défectueux']],
                    'lampes' => ['Lampes (Atelier et Réception)', ['Fonctionnelles', 'Défectueuses']],
                    'extincteur' => ['Extincteur', ['Vérifié', 'À recharger']],
                    'cameras' => ['Caméras', ['Fonctionnelles', 'Défectueuses']],
                    'telephones' => ['Téléphone fixe et téléphones professionnels', ['Fonctionnels', 'Défectueux']],
                    'ordinateurs' => ['Ordinateurs et tablettes', ['Fonctionnels', 'Défectueux']],
                    'imprimante' => ['Imprimante et consommables', ['Suffisants', 'Manquants']],
                    'fontaine_eau' => ['Fontaine d’eau', ['Propre', 'Sale']],
                    'cafe_equipements' => ['Café, sucre, cuillères, tasses propres', ['Disponibles', 'Manquants']],
                    'magazines' => ['Magazines (bien rangés et récents)', ['Bien rangés', 'En désordre']],
                    'odeur_generale' => ['Odeur générale de la salle', ['Agréable', 'Mauvaise']],
                    'proprete_generale' => ['Propreté générale du sol et des murs', ['Propre', 'Sale']],
                ] as $field => [$label, $options])
                    <div class="form-group mb-4">
                        <label>{{ $label }}</label>
                        <div class="d-flex gap-3 mb-2">
                            @foreach ($options as $option)
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="{{ $field }}" value="{{ $option }}">
                                    <label class="form-check-label">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                        <textarea class="form-control" name="{{ $field }}_remarques" placeholder="Remarques"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Zone technique -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>2. Zone technique et Gestion des huiles</h4>
            </div>
            <div class="card-body">
                @foreach ([
                    'cuve_huile' => ['Cuve de récupération d’huile', ['Vérifiée', 'Fuite détectée']],
                    'niveau_cuves' => ['Niveau de remplissage des cuves', ['Normal', 'Trop bas', 'Trop haut']],
                    'proprete_technique' => ['Propreté de l’espace technique', ['Propre', 'Sale']],
                    'pieces_moteurs' => ['Pièces et moteurs en attente', ['Rangés dans le local technique', 'Éparpillés']],
                ] as $field => [$label, $options])
                    <div class="form-group mb-4">
                        <label>{{ $label }}</label>
                        <div class="d-flex gap-3 mb-2">
                            @foreach ($options as $option)
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="{{ $field }}" value="{{ $option }}">
                                    <label class="form-check-label">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                        <textarea class="form-control" name="{{ $field }}_remarques" placeholder="Remarques"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Parking et Façade -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>3. Parking extérieur et Façade</h4>
            </div>
            <div class="card-body">
                @foreach ([
                    'proprete_parking' => ['Propreté du sol du parking', ['Propre', 'Sale']],
                    'etat_voitures' => ['État des voitures stationnées', ['Propres', 'Sales']],
                    'etat_jardin' => ['État du jardin et des fleurs', ['Bien entretenu', 'Négligé']],
                    'agent_securite' => ['Présence et tenue de l’agent de sécurité', ['Présent', 'Absent']],
                    'facade_vitres' => ['Façade extérieure et vitres', ['Propre', 'Sale']],
                    'enseigne' => ['Lumière et propreté de l’enseigne', ['Éclairée', 'Défectueuse']],
                    'signalisation' => ['Signalisation "Parking réservé clients"', ['Visible', 'Effacée']],
                    'parcours_client' => ['Parcours client (dégagé et propre)', ['Dégagé', 'Encombré']],
                ] as $field => [$label, $options])
                    <div class="form-group mb-4">
                        <label>{{ $label }}</label>
                        <div class="d-flex gap-3 mb-2">
                            @foreach ($options as $option)
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="{{ $field }}" value="{{ $option }}">
                                    <label class="form-check-label">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                        <textarea class="form-control" name="{{ $field }}_remarques" placeholder="Remarques"></textarea>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Contrôle final -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> Contrôle final et observations générales</h4>
            </div>
            <div class="card-body">
                @foreach ([
                    'proprete_finale' => ['Propreté générale du garage', ['Acceptable', 'À améliorer']],
                    'securite_finale' => ['Respect des consignes de sécurité', ['Oui', 'Non']],
                ] as $field => [$label, $options])
                    <div class="form-group mb-4">
                        <label>{{ $label }}</label>
                        <div class="d-flex gap-3 mb-2">
                            @foreach ($options as $option)
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="{{ $field }}" value="{{ $option }}" required>
                                    <label class="form-check-label">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="form-group mb-4">
                    <label>Matériel manquant ou à remplacer</label>
                    <textarea class="form-control" name="materiel_manquant" rows="3" placeholder="Listez le matériel manquant ou à remplacer"></textarea>
                </div>

                <div class="form-group mb-4">
                    <label>Recommandations et actions à entreprendre</label>
                    <textarea class="form-control" name="recommandations" rows="3" placeholder="Notez vos recommandations et les actions nécessaires"></textarea>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer la checklist
            </button>
            <a href="{{ route('checklists.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
