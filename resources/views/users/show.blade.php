@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ $user->name }}</h2>
            <p class="text-muted">
                <i class="fas fa-envelope"></i> {{ $user->email }}
                <br>
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
            </p>
        </div>
        <div class="col-md-6 text-end">
            @can('update', $user)
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations de l'utilisateur -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Site assigné
                            @if($user->site)
                                <span class="badge bg-info">{{ $user->site->name }}</span>
                            @else
                                <span class="badge bg-secondary">Non assigné</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dernière connexion
                            <span class="text-muted">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Compte créé le
                            <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Statistiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Mouvements totaux
                            <span class="badge bg-primary rounded-pill">{{ $totalMovements }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Entrées
                            <span class="badge bg-success rounded-pill">{{ $entryMovements }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sorties
                            <span class="badge bg-danger rounded-pill">{{ $exitMovements }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ce mois
                            <span class="badge bg-info rounded-pill">{{ $monthlyMovements }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Activité récente</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentMovements as $movement)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <a href="{{ route('products.show', $movement->product) }}">
                                        {{ $movement->product->name }}
                                    </a>
                                </h6>
                                <small class="text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <p class="mb-1">
                                @if($movement->type === 'entry')
                                    <span class="badge bg-success">Entrée</span>
                                @else
                                    <span class="badge bg-danger">Sortie</span>
                                @endif
                                {{ $movement->quantity }} {{ $movement->product->unit }}
                                dans {{ $movement->department->name }}
                            </p>
                            @if($movement->reason)
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> {{ $movement->reason }}
                            </small>
                            @endif
                        </div>
                        @empty
                        <div class="list-group-item text-center">
                            Aucune activité récente
                        </div>
                        @endforelse
                    </div>

                    <!-- Graphique d'activité -->
                    <div class="mt-4">
                        <canvas id="activityChart"></canvas>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($activityChartData['labels']) !!},
            datasets: [
                {
                    label: 'Entrées',
                    data: {!! json_encode($activityChartData['entries']) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Sorties',
                    data: {!! json_encode($activityChartData['exits']) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Activité des 30 derniers jours'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
