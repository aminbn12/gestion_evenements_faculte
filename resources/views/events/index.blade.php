@extends('layouts.app')

@section('title', 'Événements - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Événements</h1>
            <p class="text-muted mb-0">Gestion des événements de la faculté</p>
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="filter-status">
                        <option value="">Tous</option>
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                        <option value="cancelled">Annulé</option>
                        <option value="completed">Terminé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tous</option>
                        <option value="conference">Conférence</option>
                        <option value="seminar">Séminaire</option>
                        <option value="workshop">Workshop</option>
                        <option value="meeting">Réunion</option>
                        <option value="ceremony">Cérémonie</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priorité</label>
                    <select class="form-select" id="filter-priority">
                        <option value="">Toutes</option>
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Haute</option>
                        <option value="critical">Critique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Affichage</label>
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btn-list">
                            <i class="bi bi-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btn-calendar">
                            <i class="bi bi-calendar3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div id="events-list">
        <div class="card">
            <div class="card-body">
                @forelse($events as $event)
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
                            <span class="ms-2"><i class="bi bi-people me-1"></i>{{ $event->assignments_count ?? 0 }} participants</span>
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $event->getPriorityBadgeClass() }}">{{ ucfirst($event->priority) }}</span>
                        <span class="badge bg-{{ $event->status === 'published' ? 'success' : ($event->status === 'draft' ? 'secondary' : ($event->status === 'cancelled' ? 'danger' : 'primary')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('events.show', $event) }}"><i class="bi bi-eye me-2"></i>Voir</a></li>
                                <li><a class="dropdown-item" href="{{ route('events.edit', $event) }}"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-trash me-2"></i>Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">Aucun événement trouvé</p>
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Créer un événement
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        {{ $events->links('pagination::bootstrap-5') }}
    </div>

    <!-- Calendar View -->
    <div id="events-calendar" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle between list and calendar view
    document.getElementById('btn-list').addEventListener('click', function() {
        document.getElementById('events-list').style.display = 'block';
        document.getElementById('events-calendar').style.display = 'none';
        this.classList.add('active');
        document.getElementById('btn-calendar').classList.remove('active');
    });

    document.getElementById('btn-calendar').addEventListener('click', function() {
        document.getElementById('events-list').style.display = 'none';
        document.getElementById('events-calendar').style.display = 'block';
        this.classList.add('active');
        document.getElementById('btn-list').classList.remove('active');
        
        // Initialize calendar
        if (!window.calendarInitialized) {
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'fr',
                events: '{{ route("events.calendar") }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventClick: function(info) {
                    window.location.href = info.event.url;
                }
            });
            calendar.render();
            window.calendarInitialized = true;
        }
    });
</script>
@endpush
@endsection
