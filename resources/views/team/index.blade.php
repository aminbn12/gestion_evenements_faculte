@extends('layouts.app')

@section('title', 'Équipe - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Équipe</h1>
            <p class="text-muted mb-0">Gestion des équipes et groupes de travail</p>
        </div>
        <a href="{{ route('team.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nouveau groupe
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        @forelse($teamGroups as $group)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $group->name }}</h5>
                    <span class="badge bg-primary">{{ $group->members->count() }} membres</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $group->description }}</p>
                    
                    <h6 class="mb-2">Membres:</h6>
                    @forelse($group->members as $member)
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ $member->avatar_url }}" class="avatar me-2" alt="">
                        <div>
                            <h6 class="mb-0">{{ $member->full_name }}</h6>
                            <small class="text-muted">{{ $member->role->name ?? '' }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucun membre</p>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-diagram-3 fs-1"></i>
                    <p class="mt-2">Aucun groupe d'équipe trouvé</p>
                    <a href="{{ route('team.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Créer un groupe
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
