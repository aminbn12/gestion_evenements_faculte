@extends('layouts.app')

@section('title', 'Alertes - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Alertes</h1>
            <p class="text-muted mb-0">Gestion des alertes et notifications</p>
        </div>
        <a href="{{ route('alerts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nouvelle Alerte
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            @forelse($alerts as $alert)
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <div class="me-3">
                    @if($alert->type === 'email')
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-envelope text-info fs-4"></i>
                    </div>
                    @else
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-whatsapp text-success fs-4"></i>
                    </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">
                        @if($alert->event)
                        <a href="{{ route('events.show', $alert->event) }}" class="text-decoration-none">
                            {{ $alert->event->title }}
                        </a>
                        @else
                        Alerte #{{ $alert->id }}
                        @endif
                    </h6>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-people me-1"></i>{{ $alert->recipients_count ?? 0 }} destinataires
                        <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $alert->scheduled_at ? $alert->scheduled_at->format('d/m/Y H:i') : 'Immédiat' }}</span>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $alert->status === 'sent' ? 'success' : ($alert->status === 'pending' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($alert->status) }}
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('alerts.show', $alert) }}"><i class="bi bi-eye me-2"></i>Détails</a></li>
                            @if($alert->status === 'pending')
                            <li>
                                <form action="{{ route('alerts.send', $alert) }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-success" type="submit">
                                        <i class="bi bi-send me-2"></i>Envoyer maintenant
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('alerts.destroy', $alert) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-trash me-2"></i>Supprimer
                                    </button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1"></i>
                <p class="mt-2">Aucune alerte trouvée</p>
                <a href="{{ route('alerts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Créer une alerte
                </a>
            </div>
            @endforelse
        </div>
    </div>

    {{ $alerts->links('pagination::bootstrap-5') }}
</div>
@endsection
