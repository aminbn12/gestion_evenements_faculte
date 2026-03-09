@extends('layouts.app')

@section('title', 'Détails de l\'événement - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item active">{{ $event->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $event->title }}</h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Modifier
            </a>
            @if($event->status === 'draft')
            <form action="{{ route('events.publish', $event) }}" method="POST" class="d-inline">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send me-2"></i>Publier
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Event Details -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Type</h6>
                            <p>{{ ucfirst($event->type) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Statut</h6>
                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : ($event->status === 'draft' ? 'secondary' : ($event->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Date de début</h6>
                            <p><i class="bi bi-calendar me-2"></i>{{ $event->start_date->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Date de fin</h6>
                            <p><i class="bi bi-calendar me-2"></i>{{ $event->end_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Lieu</h6>
                            <p><i class="bi bi-geo-alt me-2"></i>{{ $event->location ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Priorité</h6>
                            <span class="badge {{ $event->getPriorityBadgeClass() }}">{{ ucfirst($event->priority) }}</span>
                        </div>
                    </div>

                    @if($event->description)
                    <div class="mb-3">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $event->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Participants assignés</h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-plus-lg"></i> Assigner
                    </button>
                </div>
                <div class="card-body">
                    @forelse($event->assignments as $assignment)
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ $assignment->user->avatar_url }}" class="avatar me-2" alt="">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $assignment->user->full_name }}</h6>
                            <small class="text-muted">{{ $assignment->role ?? 'Participant' }}</small>
                        </div>
                        <span class="badge bg-{{ $assignment->status === 'confirmed' ? 'success' : 'warning' }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucun participant assigné</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('alerts.create', ['event' => $event->id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-bell me-2"></i>Créer une alerte
                        </a>
                        @if($event->status !== 'cancelled')
                        <form action="{{ route('events.cancel', $event) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Annuler cet événement?')">
                                <i class="bi bi-x-circle me-2"></i>Annuler l'événement
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants</span>
                        <strong>{{ $event->assignments->count() }}</strong>
                    </div>
                    @if($event->max_participants)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Max participants</span>
                        <strong>{{ $event->max_participants }}</strong>
                    </div>
                    @endif
                    @if($event->budget)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Budget</span>
                        <strong>{{ number_format($event->budget, 2) }} MAD</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner des participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('events.assign', $event) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Utilisateurs</label>
                        <select name="user_ids[]" class="form-select select2" multiple required>
                            @foreach(\App\Models\User::where('status', 'active')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="participant">Participant</option>
                            <option value="organizer">Organisateur</option>
                            <option value="speaker">Intervenant</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
