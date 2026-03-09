@extends('layouts.app')

@section('title', 'Congés - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Demandes de congés</h1>
            <p class="text-muted mb-0">Gestion des demandes de congés</p>
        </div>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nouvelle demande
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('leaves.index') }}">Tous</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'pending']) }}">En attente</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'approved']) }}">Approuvés</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}" href="{{ route('leaves.index', ['status' => 'rejected']) }}">Rejetés</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            @forelse($leaves as $leave)
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <img src="{{ $leave->user->avatar_url }}" class="avatar me-3" alt="">
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $leave->user->full_name }}</h6>
                    <small class="text-muted">
                        {{ $leave->type_label }} - {{ $leave->start_date->format('d/m/Y') }} au {{ $leave->end_date->format('d/m/Y') }}
                        ({{ $leave->days_count }} jour(s))
                    </small>
                    @if($leave->reason)
                    <p class="mb-0 text-muted small mt-1">{{ Str::limit($leave->reason, 100) }}</p>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($leave->status) }}
                    </span>
                    @if($leave->status === 'pending' && auth()->user()->hasRole(['manager', 'chef-dept']))
                    <div class="btn-group btn-group-sm">
                        <form action="{{ route('leaves.approve', $leave) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success" title="Approuver">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>
                        <form action="{{ route('leaves.reject', $leave) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" title="Rejeter">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1"></i>
                <p class="mt-2">Aucune demande de congé trouvée</p>
            </div>
            @endforelse
        </div>
    </div>

    {{ $leaves->links('pagination::bootstrap-5') }}
</div>
@endsection
