@extends('layouts.app')

@section('title', 'Détails utilisateur - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">{{ $user->full_name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $user->full_name }}</h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" 
                         class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4>{{ $user->full_name }}</h4>
                    <p class="text-muted">{{ $user->role->name ?? 'Utilisateur' }}</p>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="bi bi-envelope me-2"></i>{{ $user->email }}
                    </div>
                    @if($user->phone)
                    <div class="mb-2">
                        <i class="bi bi-telephone me-2"></i>{{ $user->phone }}
                    </div>
                    @endif
                    @if($user->department)
                    <div class="mb-2">
                        <i class="bi bi-building me-2"></i>{{ $user->department->name }}
                    </div>
                    @endif
                    <div class="mb-2">
                        <i class="bi bi-calendar me-2"></i>Membre depuis {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Événements assignés</h5>
                </div>
                <div class="card-body">
                    @forelse($user->events as $event)
                    <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $event->title }}</h6>
                            <small class="text-muted">{{ $event->start_date->format('d/m/Y H:i') }}</small>
                        </div>
                        <span class="badge bg-{{ $event->status === 'published' ? 'success' : 'secondary' }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucun événement assigné</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Demandes de congés</h5>
                </div>
                <div class="card-body">
                    @forelse($user->leaves as $leave)
                    <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $leave->type_label }}</h6>
                            <small class="text-muted">{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}</small>
                        </div>
                        <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucune demande de congé</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
