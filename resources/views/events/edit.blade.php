@extends('layouts.app')

@section('title', 'Modifier l\'événement - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Modifier l'événement</h1>
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

    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf @method('PUT')
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
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5">{{ old('description', $event->description) }}</textarea>
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
                                    <option value="conference" {{ old('type', $event->type) === 'conference' ? 'selected' : '' }}>Conférence</option>
                                    <option value="seminar" {{ old('type', $event->type) === 'seminar' ? 'selected' : '' }}>Séminaire</option>
                                    <option value="workshop" {{ old('type', $event->type) === 'workshop' ? 'selected' : '' }}>Workshop</option>
                                    <option value="meeting" {{ old('type', $event->type) === 'meeting' ? 'selected' : '' }}>Réunion</option>
                                    <option value="ceremony" {{ old('type', $event->type) === 'ceremony' ? 'selected' : '' }}>Cérémonie</option>
                                    <option value="other" {{ old('type', $event->type) === 'other' ? 'selected' : '' }}>Autre</option>
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
                                    <option value="low" {{ old('priority', $event->priority) === 'low' ? 'selected' : '' }}>Basse</option>
                                    <option value="medium" {{ old('priority', $event->priority) === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="high" {{ old('priority', $event->priority) === 'high' ? 'selected' : '' }}>Haute</option>
                                    <option value="critical" {{ old('priority', $event->priority) === 'critical' ? 'selected' : '' }}>Critique</option>
                                </select>
                                @error('priority')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Lieu</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location', $event->location) }}">
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
                                       id="start_date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin *</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" required>
                                @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" value="1"
                                       {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
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
                                <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="published" {{ $event->status === 'published' ? 'selected' : '' }}>Publié</option>
                                <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Terminé</option>
                            </select>
                        </div>

                        <!-- Search Users -->
                        <div class="mb-3">
                            <label for="edit_user_search" class="form-label">Rechercher un utilisateur</label>
                            <input type="text" class="form-control" id="edit_user_search" placeholder="Tapez un nom...">
                            <div class="mt-2" id="edit_search_results" style="max-height: 120px; overflow-y: auto; display: none;">
                            </div>
                        </div>

                        <!-- Selected Users from Search -->
                        <div class="mb-3">
                            <label class="form-label">Utilisateurs sélectionnés</label>
                            <div id="edit_selected_users_display" class="d-flex flex-wrap gap-2">
                            </div>
                            <input type="hidden" name="edit_selected_users_list" id="edit_selected_users_list" value="">
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select select2" id="department_id" name="department_id">
                                <option value="">Tous les départements</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $event->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department Users Checkbox List -->
                        <div class="mb-3" id="edit_department_users_section" style="display: none;">
                            <label class="form-label">Membres du département</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="edit_department_users_list">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label">Participants max</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="{{ old('capacity', $event->capacity) }}" min="1">
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
                                <i class="bi bi-check-lg me-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden list of all users for search -->
                <div id="all_users_list" style="display: none;">
                    @foreach($users as $user)
                    <div class="edit-all-user-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->full_name }}" data-user-dept="{{ $user->department_id }}"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Global variables for edit form
let editSelectedUsers = [];
let editAllUsers = [];
let editUserSearch, editSearchResults, editSelectedUsersDisplay, editSelectedUsersList;
let editDepartmentSelect, editDepartmentUsersSection, editDepartmentUsersList;

document.addEventListener('DOMContentLoaded', function() {
    // Collect all users from the hidden DOM section
    const allUserItems = document.querySelectorAll('.edit-all-user-item');
    allUserItems.forEach(item => {
        const name = item.dataset.userName || '';
        editAllUsers.push({
            id: parseInt(item.dataset.userId),
            first_name: name.split(' ')[0] || '',
            last_name: name.split(' ').slice(1).join(' ') || '',
            department_id: item.dataset.userDept
        });
    });
    
    // Pre-select existing users
    @if($event->users->count() > 0)
    editSelectedUsers = @json($event->users->pluck('id')->toArray());
    @endif

    editUserSearch = document.getElementById('edit_user_search');
    editSearchResults = document.getElementById('edit_search_results');
    editSelectedUsersDisplay = document.getElementById('edit_selected_users_display');
    editSelectedUsersList = document.getElementById('edit_selected_users_list');
    editDepartmentSelect = document.getElementById('department_id');
    editDepartmentUsersSection = document.getElementById('edit_department_users_section');
    editDepartmentUsersList = document.getElementById('edit_department_users_list');

    // Initialize display
    updateEditSelectedUsersDisplay();

    // Check initial department
    if (editDepartmentSelect.value) {
        loadEditDepartmentUsers(editDepartmentSelect.value);
    }

    // User search
    editUserSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            editSearchResults.style.display = 'none';
            return;
        }

        const filteredUsers = editAllUsers.filter(u => 
            (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm) &&
            !editSelectedUsers.includes(u.id)
        );

        if (filteredUsers.length > 0) {
            editSearchResults.style.display = 'block';
            editSearchResults.innerHTML = filteredUsers.map(u => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${u.id}" 
                           data-name="${u.first_name} ${u.last_name}" 
                           onchange="toggleEditUser(this)">
                    <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                </div>
            `).join('');
        } else {
            editSearchResults.innerHTML = '<small class="text-muted">Aucun utilisateur trouvé</small>';
        }
    });

    // Department selection
    editDepartmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            loadEditDepartmentUsers(departmentId);
        } else {
            editDepartmentUsersSection.style.display = 'none';
        }
    });
});

function loadEditDepartmentUsers(departmentId) {
    const deptUsers = editAllUsers.filter(u => u.department_id == departmentId);
    
    if (deptUsers.length > 0) {
        editDepartmentUsersSection.style.display = 'block';
        editDepartmentUsersList.innerHTML = deptUsers.map(u => {
            const isChecked = editSelectedUsers.includes(u.id) ? 'checked' : '';
            return `
                <div class="form-check">
                    <input class="form-check-input edit-dept-user-checkbox" type="checkbox" 
                           value="${u.id}" data-name="${u.first_name} ${u.last_name}" 
                           ${isChecked} onchange="toggleEditDeptUser(this)">
                    <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                </div>
            `;
        }).join('');
    } else {
        editDepartmentUsersSection.style.display = 'none';
    }
}

window.toggleEditUser = function(checkbox) {
    const userId = parseInt(checkbox.value);
    const userName = checkbox.dataset.name;

    if (checkbox.checked) {
        if (!editSelectedUsers.includes(userId)) {
            editSelectedUsers.push(userId);
            editSelectedUsersDisplay.innerHTML += `<span class="badge bg-primary" id="edit-user-badge-${userId}">
                ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeEditUser(${userId})"></i>
            </span>`;
        }
    } else {
        editSelectedUsers = editSelectedUsers.filter(id => id !== userId);
        const badge = document.getElementById(`edit-user-badge-${userId}`);
        if (badge) badge.remove();
    }

    updateEditSelectedUsersInput();
};

window.toggleEditDeptUser = function(checkbox) {
    const userId = parseInt(checkbox.value);
    const userName = checkbox.dataset.name;

    if (checkbox.checked) {
        if (!editSelectedUsers.includes(userId)) {
            editSelectedUsers.push(userId);
            editSelectedUsersDisplay.innerHTML += `<span class="badge bg-primary" id="edit-user-badge-${userId}">
                ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeEditUser(${userId})"></i>
            </span>`;
        }
    } else {
        editSelectedUsers = editSelectedUsers.filter(id => id !== userId);
        const badge = document.getElementById(`edit-user-badge-${userId}`);
        if (badge) badge.remove();
    }

    updateEditSelectedUsersInput();
};

window.removeEditUser = function(userId) {
    editSelectedUsers = editSelectedUsers.filter(id => id !== userId);
    const badge = document.getElementById(`edit-user-badge-${userId}`);
    if (badge) badge.remove();
    
    const checkbox = editSearchResults.querySelector(`input[value="${userId}"]`);
    if (checkbox) checkbox.checked = false;
    
    const deptCheckbox = editDepartmentUsersList.querySelector(`input[value="${userId}"]`);
    if (deptCheckbox) deptCheckbox.checked = false;

    updateEditSelectedUsersInput();
};

function updateEditSelectedUsersInput() {
    editSelectedUsersList.value = editSelectedUsers.join(',');
}

function updateEditSelectedUsersDisplay() {
    editSelectedUsers.forEach(userId => {
        const user = editAllUsers.find(u => u.id === userId);
        if (user && !document.getElementById(`edit-user-badge-${userId}`)) {
            editSelectedUsersDisplay.innerHTML += `<span class="badge bg-primary" id="edit-user-badge-${userId}">
                ${user.first_name} ${user.last_name} <i class="bi bi-x-circle cursor-pointer" onclick="removeEditUser(${userId})"></i>
            </span>`;
        }
    });
    updateEditSelectedUsersInput();
}
</script>
@endsection
