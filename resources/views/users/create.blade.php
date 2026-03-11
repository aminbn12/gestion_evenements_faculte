@extends('layouts.app')

@section('title', 'Créer un utilisateur - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Créer un utilisateur</h1>
        </div>
    </div>

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

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer *</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enseignant/Professeur Profile -->
                <div class="card mb-4" id="professor-fields" style="display: none;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Profil Enseignant</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rank" class="form-label">Grade</label>
                                <select class="form-select" id="rank" name="rank">
                                    <option value="Pr">Pr (Professeur)</option>
                                    <option value="Dr">Dr (Docteur)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsible_promo" class="form-label">Responsable Promo</label>
                                <select class="form-select" id="responsible_promo" name="responsible_promo">
                                    <option value="">Sélectionner...</option>
                                    <option value="LTLP 1">LTLP 1</option>
                                    <option value="LTLP 2">LTLP 2</option>
                                    <option value="LTLP 3">LTLP 3</option>
                                    <option value="FM6MD 1">FM6MD 1</option>
                                    <option value="FM6MD 2">FM6MD 2</option>
                                    <option value="FM6MD 3">FM6MD 3</option>
                                    <option value="FM6MD 4">FM6MD 4</option>
                                    <option value="FM6MD 5">FM6MD 5</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Matière enseignée</label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   placeholder="ex: Anatomie, Physiologie, ...">
                        </div>
                    </div>
                </div>

                <!-- Resident Profile -->
                <div class="card mb-4" id="resident-fields" style="display: none;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Profil Résident</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Niveau</label>
                                <select class="form-select" id="level" name="level">
                                    <option value="1">A1 (1ère année)</option>
                                    <option value="2">A2 (2ème année)</option>
                                    <option value="3">A3 (3ème année)</option>
                                    <option value="4">A4 (4ème année)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="specialty" class="form-label">Spécialité</label>
                                <select class="form-select" id="specialty" name="specialty">
                                    <option value="">Sélectionner...</option>
                                    <option value="Pédodontie Prévention">Pédodontie Prévention</option>
                                    <option value="Parodontologie">Parodontologie</option>
                                    <option value="Orthopédie Dento-Faciale">Orthopédie Dento-Faciale</option>
                                    <option value="Odontologie Chirurgicale">Odontologie Chirurgicale</option>
                                    <option value="Prothèse Conjointe">Prothèse Conjointe</option>
                                    <option value="Prothèse Adjointe">Prothèse Adjointe</option>
                                    <option value="Odontologie Conservatrice">Odontologie Conservatrice</option>
                                </select>
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
                            <label for="role_id" class="form-label">Rôle *</label>
                            <select class="form-select" id="role_id" name="role_id" required onchange="toggleProfileFields()">
                                <option value="">Sélectionner...</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">Aucun</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="pending">En attente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Créer
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleProfileFields() {
    const roleSelect = document.getElementById('role_id');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption.text.toLowerCase();
    
    const professorFields = document.getElementById('professor-fields');
    const residentFields = document.getElementById('resident-fields');
    
    // Show/hide based on role name
    if (roleName.includes('enseignant') || roleName.includes('professeur')) {
        professorFields.style.display = 'block';
    } else {
        professorFields.style.display = 'none';
    }
    
    if (roleName.includes('résidanat') || roleName.includes('resident')) {
        residentFields.style.display = 'block';
    } else {
        residentFields.style.display = 'none';
    }
}

// Run on page load to handle old values
document.addEventListener('DOMContentLoaded', toggleProfileFields);
</script>
@endsection
