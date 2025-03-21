@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Rapports de stock</h2>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" id="reportForm" class="row g-3">
                <div class="col-md-4">
                    <label for="site" class="form-label">Site</label>
                    <select class="form-select" id="site" name="site">
                        <option value="">Tous les sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="department" class="form-label">Département</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Tous les départements</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                data-site="{{ $department->site_id }}"
                                {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="period" class="form-label">Période</label>
                    <select class="form-select" id="period" name="period">
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                    </select>
                </div>

                <div class="col-md-4 custom-dates" style="display: none;">
                    <label for="start_date" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                        value="{{ request('start_date') }}">
                </div>

                <div class="col-md-4 custom-dates" style="display: none;">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                        value="{{ request('end_date') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Statistiques générales -->
        <div class="col-md-3">
            <div class="card shadow border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">Total mouvements</h6>
                            <h2 class="mb-0">{{ $stats['total_movements'] }}</h2>
                        </div>
                        <div class="bg-primary text-white rounded-circle p-3">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">Entrées</h6>
                            <h2 class="mb-0">{{ $stats['total_entries'] }}</h2>
                        </div>
                        <div class="bg-success text-white rounded-circle p-3">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">Sorties</h6>
                            <h2 class="mb-0">{{ $stats['total_exits'] }}</h2>
                        </div>
                        <div class="bg-danger text-white rounded-circle p-3">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">Produits concernés</h6>
                            <h2 class="mb-0">{{ $stats['products_count'] }}</h2>
                        </div>
                        <div class="bg-warning text-white rounded-circle p-3">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique des mouvements -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Évolution des mouvements</h5>
                </div>
                <div class="card-body">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top produits -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Top 5 des produits</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($topProducts as $product)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <span class="badge bg-primary">{{ $product->movements_count }} mvts</span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-arrow-up text-success"></i> {{ $product->entries_count }}
                                <i class="fas fa-arrow-down text-danger ms-2"></i> {{ $product->exits_count }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Répartition par département -->
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Répartition par département</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rounded-circle {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
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
    // Gestion des filtres
    const siteSelect = document.getElementById('site');
    const departmentSelect = document.getElementById('department');
    const periodSelect = document.getElementById('period');
    const customDates = document.querySelectorAll('.custom-dates');

    // Mise à jour des départements en fonction du site
    siteSelect.addEventListener('change', function() {
        const selectedSiteId = this.value;
        const departments = Array.from(departmentSelect.options);

        departmentSelect.innerHTML = '<option value="">Tous les départements</option>';

        if (!selectedSiteId) {
            departments.forEach(option => {
                if (option.value) {
                    departmentSelect.appendChild(option.cloneNode(true));
                }
            });
        } else {
            departments.forEach(option => {
                if (option.dataset.site === selectedSiteId) {
                    departmentSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    });

    // Affichage des dates personnalisées
    periodSelect.addEventListener('change', function() {
        customDates.forEach(el => {
            el.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    });

    // Initialisation de l'affichage des dates personnalisées
    if (periodSelect.value === 'custom') {
        customDates.forEach(el => el.style.display = 'block');
    }

    // Graphique des mouvements
    const movementsCtx = document.getElementById('movementsChart').getContext('2d');
    new Chart(movementsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [
                {
                    label: 'Entrées',
                    data: {!! json_encode($chartData['entries']) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Sorties',
                    data: {!! json_encode($chartData['exits']) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
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

    // Graphique des départements
    const departmentsCtx = document.getElementById('departmentsChart').getContext('2d');
    new Chart(departmentsCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($departmentsData['labels']) !!},
            datasets: [{
                data: {!! json_encode($departmentsData['data']) !!},
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#0dcaf0',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Fonction d'export
function exportReport(format) {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    formData.append('format', format);
    
    window.location.href = `${form.action}/export?${new URLSearchParams(formData)}`;
}
</script>
@endpush
