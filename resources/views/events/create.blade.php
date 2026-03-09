@extends('layouts.app')

@section('title', 'Créer un événement - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Créer un événement</h1>
            <p class="text-muted mb-0">Planifier un nouvel événement</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="conference" {{ old('type') === 'conference' ? 'selected' : '' }}>Conférence</option>
                                    <option value="seminar" {{ old('type') === 'seminar' ? 'selected' : '' }}>Séminaire</option>
                                    <option value="workshop" {{ old('type') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                                    <option value="meeting" {{ old('type') === 'meeting' ? 'selected' : '' }}>Réunion</option>
                                    <option value="ceremony" {{ old('type') === 'ceremony' ? 'selected' : '' }}>Cérémonie</option>
                                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priorité *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                                    <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critique</option>
                                </select>
                                @error('priority')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}">
                            @error('location')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Date et heure</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date de début *</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin *</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" value="1"
                                       {{ old('is_all_day') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_all_day">
                                    Toute la journée
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Paramètres</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft">Brouillon</option>
                                <option value="published">Publié</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select select2" id="department_id" name="department_id">
                                <option value="">Tous les départements</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="max_participants" class="form-label">Participants max</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                   value="{{ old('max_participants') }}" min="1">
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget (MAD)</label>
                            <input type="number" class="form-control" id="budget" name="budget" 
                                   value="{{ old('budget') }}" step="0.01" min="0">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Créer l'événement
                            </button>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
