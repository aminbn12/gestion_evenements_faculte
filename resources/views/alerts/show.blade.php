@extends('layouts.app')

@section('title', 'Détails de l\'alerte - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('alerts.index') }}">Alertes</a></li>
                    <li class="breadcrumb-item active">Détails</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Détails de l'alerte</h1>
        </div>
        @if($alert->status === 'pending')
        <form action="{{ route('alerts.send', $alert) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-send me-2"></i>Envoyer maintenant
            </button>
        </form>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Type</h6>
                            <span class="badge bg-{{ $alert->type === 'email' ? 'info' : 'success' }}">
                                {{ $alert->type === 'email' ? 'Email' : ($alert->type === 'whatsapp' ? 'WhatsApp' : 'Email & WhatsApp') }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Statut</h6>
                            <span class="badge bg-{{ $alert->status === 'sent' ? 'success' : ($alert->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($alert->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">Sujet</h6>
                        <p>{{ $alert->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted">Message</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $alert->message }}
                        </div>
                    </div>

                    @if($alert->event)
                    <div class="mb-3">
                        <h6 class="text-muted">Événement associé</h6>
                        <a href="{{ route('events.show', $alert->event) }}" class="btn btn-outline-primary btn-sm">
                            {{ $alert->event->title }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Logs d'envoi</h5>
                </div>
                <div class="card-body">
                    @forelse($alert->logs as $log)
                    <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                        <img src="{{ $log->recipient->avatar_url }}" class="avatar me-2" alt="">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $log->recipient->full_name }}</h6>
                            <small class="text-muted">{{ $log->recipient->email }}</small>
                        </div>
                        <span class="badge bg-{{ $log->status === 'sent' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucun log disponible</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Destinataires</span>
                        <strong>{{ $alert->logs->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Envoyés</span>
                        <strong class="text-success">{{ $alert->logs->where('status', 'sent')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Échoués</span>
                        <strong class="text-danger">{{ $alert->logs->where('status', 'failed')->count() }}</strong>
                    </div>
                    @if($alert->scheduled_at)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Planifié pour</span>
                        <strong>{{ $alert->scheduled_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @endif
                    @if($alert->sent_at)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Envoyé le</span>
                        <strong>{{ $alert->sent_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
