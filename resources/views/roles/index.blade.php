@extends('layouts.app')

@section('title', 'Rôles et permissions - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Rôles et permissions</h1>
            <p class="text-muted mb-0">Gestion des rôles et permissions</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        @foreach($roles as $role)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $role->name }}</h5>
                    <span class="badge bg-primary">{{ $role->permissions->count() }} permissions</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $role->description }}</p>
                    <h6 class="mb-2">Permissions:</h6>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @forelse($role->permissions as $permission)
                        <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                        @empty
                        <span class="text-muted">Aucune permission</span>
                        @endforelse
                    </div>
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i>Modifier les permissions
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
