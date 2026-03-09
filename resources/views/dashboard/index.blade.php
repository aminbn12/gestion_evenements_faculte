@extends('layouts.app')

@section('title', 'Dashboard - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted mb-0">Bienvenue, {{ auth()->user()->first_name }}!</p>
        </div>
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nouvel Événement
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Événements</h6>
                            <h2 class="mb-0">{{ $stats['total_events'] }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-event text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Événements à venir</h6>
                            <h2 class="mb-0">{{ $stats['upcoming_events'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Utilisateurs Actifs</h6>
                            <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Congés en attente</h6>
                            <h2 class="mb-0">{{ $stats['pending_leaves'] }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-x text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Events -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>Événements à venir
                    </h5>
                    <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($upcomingEvents as $event)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded p-2 text-center" style="min-width: 60px;">
                                <div class="fs-5 fw-bold">{{ $event->start_date->format('d') }}</div>
                                <div class="small">{{ $event->start_date->format('M') }}</div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                    {{ $event->title }}
                                </a>
                            </h6>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'Non défini' }}
                                <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $event->start_date->format('H:i') }}</span>
                            </p>
                        </div>
                        <div>
                            <span class="badge {{ $event->getPriorityBadgeClass() }}">{{ ucfirst($event->priority) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p class="mt-2">Aucun événement à venir</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-x me-2"></i>Congés en attente
                    </h5>
                    <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($pendingLeaves as $leave)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->full_name }}" 
                             class="avatar me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $leave->user->full_name }}</h6>
                            <small class="text-muted">
                                {{ $leave->type_label }} - {{ $leave->days_count }} jour(s)
                            </small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('leaves.approve', $leave) }}" 
                               class="btn btn-outline-success" title="Approuver">
                                <i class="bi bi-check"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1"></i>
                        <p class="mt-2">Aucune demande en attente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Événements par statut
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Événements par type
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Brouillon', 'Publié', 'Annulé', 'Terminé'],
            datasets: [{
                data: [
                    {{ $eventsByStatus['draft'] ?? 0 }},
                    {{ $eventsByStatus['published'] ?? 0 }},
                    {{ $eventsByStatus['cancelled'] ?? 0 }},
                    {{ $eventsByStatus['completed'] ?? 0 }}
                ],
                backgroundColor: ['#6c757d', '#1a3c5e', '#dc3545', '#28a745']
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

    // Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'bar',
        data: {
            labels: ['Conférence', 'Séminaire', 'Workshop', 'Réunion', 'Cérémonie', 'Autre'],
            datasets: [{
                label: 'Nombre d\'événements',
                data: [
                    {{ $eventsByType['conference'] ?? 0 }},
                    {{ $eventsByType['seminar'] ?? 0 }},
                    {{ $eventsByType['workshop'] ?? 0 }},
                    {{ $eventsByType['meeting'] ?? 0 }},
                    {{ $eventsByType['ceremony'] ?? 0 }},
                    {{ $eventsByType['other'] ?? 0 }}
                ],
                backgroundColor: '#1a3c5e'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
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
</script>
@endpush
@endsection
