@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Liste des Checklists</h2>
        <a href="{{ route('checklists.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Checklist
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Zone de travail</th>
                    <th>Zone technique</th>
                    <th>Parking/Façade</th>
                    <th>État général</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklists as $checklist)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($checklist->date_verification)->format('d/m/Y') }}</td>
                    
                    <td>
                        @php
                            $zoneCount = 0;
                            $zoneOk = 0;
                            foreach(['ponts_elevateurs', 'ecran_tv', 'lampes', 'extincteur', 'cameras', 'telephones', 'ordinateurs', 'imprimante', 'fontaine_eau', 'cafe_equipements', 'magazines', 'odeur_generale', 'proprete_generale'] as $field) {
                                if ($checklist->$field) {
                                    $zoneCount++;
                                    if (in_array($checklist->$field, ['Fonctionnels', 'Fonctionnel', 'Fonctionnelles', 'Vérifié', 'Suffisants', 'Propre', 'Disponibles', 'Bien rangés', 'Agréable'])) {
                                        $zoneOk++;
                                    }
                                }
                            }
                        @endphp
                        <div class="d-flex align-items-center">
                            @if($zoneCount > 0)
                                @if($zoneOk == $zoneCount)
                                    <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
                                @elseif($zoneOk >= $zoneCount/2)
                                    <span class="badge bg-warning me-2"><i class="fas fa-exclamation"></i></span>
                                @else
                                    <span class="badge bg-danger me-2"><i class="fas fa-times"></i></span>
                                @endif
                                {{ $zoneOk }}/{{ $zoneCount }}
                            @else
                                <span class="badge bg-secondary me-2"><i class="fas fa-minus"></i></span>
                                N/A
                            @endif
                        </div>
                    </td>

                    <td>
                        @php
                            $zoneCount = 0;
                            $zoneOk = 0;
                            foreach(['cuve_huile', 'niveau_cuves', 'proprete_technique', 'pieces_moteurs'] as $field) {
                                if ($checklist->$field) {
                                    $zoneCount++;
                                    if (in_array($checklist->$field, ['Vérifiée', 'Normal', 'Propre', 'Rangés dans le local technique'])) {
                                        $zoneOk++;
                                    }
                                }
                            }
                        @endphp
                        <div class="d-flex align-items-center">
                            @if($zoneCount > 0)
                                @if($zoneOk == $zoneCount)
                                    <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
                                @elseif($zoneOk >= $zoneCount/2)
                                    <span class="badge bg-warning me-2"><i class="fas fa-exclamation"></i></span>
                                @else
                                    <span class="badge bg-danger me-2"><i class="fas fa-times"></i></span>
                                @endif
                                {{ $zoneOk }}/{{ $zoneCount }}
                            @else
                                <span class="badge bg-secondary me-2"><i class="fas fa-minus"></i></span>
                                N/A
                            @endif
                        </div>
                    </td>

                    <td>
                        @php
                            $zoneCount = 0;
                            $zoneOk = 0;
                            foreach(['proprete_parking', 'etat_voitures', 'etat_jardin', 'agent_securite', 'facade_vitres', 'enseigne', 'signalisation', 'parcours_client'] as $field) {
                                if ($checklist->$field) {
                                    $zoneCount++;
                                    if (in_array($checklist->$field, ['Propre', 'Propres', 'Bien entretenu', 'Présent', 'Éclairée', 'Visible', 'Dégagé'])) {
                                        $zoneOk++;
                                    }
                                }
                            }
                        @endphp
                        <div class="d-flex align-items-center">
                            @if($zoneCount > 0)
                                @if($zoneOk == $zoneCount)
                                    <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
                                @elseif($zoneOk >= $zoneCount/2)
                                    <span class="badge bg-warning me-2"><i class="fas fa-exclamation"></i></span>
                                @else
                                    <span class="badge bg-danger me-2"><i class="fas fa-times"></i></span>
                                @endif
                                {{ $zoneOk }}/{{ $zoneCount }}
                            @else
                                <span class="badge bg-secondary me-2"><i class="fas fa-minus"></i></span>
                                N/A
                            @endif
                        </div>
                    </td>

                    <td>
                        @php
                            $final = 0;
                            if ($checklist->proprete_finale === 'Acceptable') $final++;
                            if ($checklist->securite_finale === 'Oui') $final++;
                        @endphp
                        <div class="d-flex align-items-center">
                            @if($final == 2)
                                <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
                            @elseif($final == 1)
                                <span class="badge bg-warning me-2"><i class="fas fa-exclamation"></i></span>
                            @elseif($final == 0)
                                <span class="badge bg-danger me-2"><i class="fas fa-times"></i></span>
                            @endif
                            {{ $final }}/2
                        </div>
                    </td>

                    <td>
                        <div class="btn-group">
                            <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('checklists.destroy', $checklist) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette checklist ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
