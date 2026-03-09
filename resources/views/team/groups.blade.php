@extends('layouts.app')

@section('title', 'Groupes de travail - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Groupes de travail</h1>
            <p class="text-muted mb-0">Gérer les groupes et équipes de la faculté</p>
        </div>
        <a href="{{ route('team.groups.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nouveau groupe
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Groups List -->
    <div class="card">
        <div class="card-body">
            @forelse($groups as $group)
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <div class="me-3">
                    <div class="bg-info text-white rounded p-2 text-center" style="min-width: 50px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">
                        <a href="#" class="text-decoration-none">{{ $group->name }}</a>
                    </h6>
                    <p class="mb-0 text-muted small">
                        <span class="badge bg-secondary">{{ ucfirst($group->type) }}</span>
                        @if($group->department)
                        <span class="ms-2"><i class="bi bi-building me-1"></i>{{ $group->department->name }}</span>
                        @endif
                        <span class="ms-2"><i class="bi bi-people me-1"></i>{{ $group->members_count ?? $group->members->count() }} membres</span>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('team.groups.edit', $group) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('team.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1"></i>
                <p class="mt-2">Aucun groupe de travail trouvé</p>
                <a href="{{ route('team.groups.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Créer un groupe
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
