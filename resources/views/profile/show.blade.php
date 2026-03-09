@extends('layouts.app')

@section('title', 'Mon profil - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Mon profil</h1>
            <p class="text-muted mb-0">Gérer vos informations personnelles</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" 
                         class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4>{{ auth()->user()->full_name }}</h4>
                    <p class="text-muted">{{ auth()->user()->role->name ?? 'Utilisateur' }}</p>
                    @if(auth()->user()->department)
                    <p class="text-muted">{{ auth()->user()->department->name }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="bi bi-envelope me-2"></i>{{ auth()->user()->email }}
                    </div>
                    @if(auth()->user()->phone)
                    <div class="mb-2">
                        <i class="bi bi-telephone me-2"></i>{{ auth()->user()->phone }}
                    </div>
                    @endif
                    <div class="mb-2">
                        <i class="bi bi-calendar me-2"></i>Membre depuis {{ auth()->user()->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name', auth()->user()->first_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name', auth()->user()->last_name) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', auth()->user()->email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', auth()->user()->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Photo de profil</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Changer le mot de passe</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
