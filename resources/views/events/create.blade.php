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

                        <!-- Search Users -->
                        <div class="mb-3">
                            <label for="user_search" class="form-label">Rechercher un utilisateur</label>
                            <input type="text" class="form-control" id="user_search" placeholder="Tapez un nom...">
                            <div class="mt-2" id="user_search_results" style="max-height: 120px; overflow-y: auto; display: none;">
                            </div>
                        </div>

                        <!-- Selected Users from Search -->
                        <div class="mb-3">
                            <label class="form-label">Utilisateurs sélectionnés</label>
                            <div id="selected_users_display" class="d-flex flex-wrap gap-2">
                            </div>
                            <input type="hidden" name="selected_users_list" id="selected_users_list" value="">
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

                        <!-- Department Users Checkbox List -->
                        <div class="mb-3" id="department_users_section" style="display: none;">
                            <label class="form-label">Membres du département</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="department_users_list">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label">Participants max</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="{{ old('capacity') }}" min="1">
                        </div>
                    </div>
                </div>

                <!-- Alert Recipients Section -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Destinataires des alertes</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="recipient_type" class="form-label">Type de destinataires</label>
                            <select class="form-select" id="recipient_type" name="recipient_type">
                                <option value="all">Tous les utilisateurs</option>
                                <option value="role">Par rôle</option>
                                <option value="department">Par département</option>
                                <option value="users">Personnes spécifiques</option>
                            </select>
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-3 recipient-option" id="role-options" style="display: none;">
                            <label for="selected_roles" class="form-label">Sélectionner les rôles</label>
                            <select class="form-select select2" id="selected_roles" name="selected_roles[]" multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department Selection -->
                        <div class="mb-3 recipient-option" id="department-options" style="display: none;">
                            <label for="alert_department_id" class="form-label">Sélectionner le département</label>
                            <select class="form-select" id="alert_department_id" name="alert_department_id">
                                <option value="">Sélectionner...</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            
                            <!-- Department Users Card -->
                            <div id="department-users-card" class="mt-3" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Membres du département</h6>
                                    </div>
                                    <div class="card-body" id="department-users-list">
                                        <!-- Users will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Selection -->
                        <div class="mb-3 recipient-option" id="users-options" style="display: none;">
                            <label for="user-search" class="form-label">Rechercher un utilisateur</label>
                            <input type="text" class="form-control" id="user-search" placeholder="Tapez un nom...">
                            
                            <div class="mt-2" id="search-results" style="max-height: 150px; overflow-y: auto;">
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Utilisateurs sélectionnés</label>
                                <div id="selected-users-list" class="d-flex flex-wrap gap-2">
                                </div>
                                <input type="hidden" name="selected_users[]" id="selected-users-input" value="">
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_email_alert" name="send_email_alert" value="1">
                                <label class="form-check-label" for="send_email_alert">
                                    Envoyer une alerte par email
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_reminder_enabled" name="auto_reminder_enabled" value="1">
                                <label class="form-check-label" for="auto_reminder_enabled">
                                    Activer les rappels automatiques
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="reminder-days-container" style="display: none;">
                            <label for="reminder_days_before" class="form-label">Rappeler combien de jours avant ?</label>
                            <select class="form-select" id="reminder_days_before" name="reminder_days_before">
                                <option value="1">1 jour</option>
                                <option value="3">3 jours</option>
                                <option value="7">1 semaine</option>
                                <option value="10">10 jours</option>
                                <option value="30">1 mois</option>
                            </select>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientType = document.getElementById('recipient_type');
    const roleOptions = document.getElementById('role-options');
    const departmentOptions = document.getElementById('department-options');
    const usersOptions = document.getElementById('users-options');
    const alertDepartmentId = document.getElementById('alert_department_id');
    const departmentUsersCard = document.getElementById('department-users-card');
    const departmentUsersList = document.getElementById('department-users-list');
    const userSearch = document.getElementById('user-search');
    const searchResults = document.getElementById('search-results');
    const selectedUsersList = document.getElementById('selected-users-list');
    const selectedUsersInput = document.getElementById('selected-users-input');
    const autoReminderEnabled = document.getElementById('auto_reminder_enabled');
    const reminderDaysContainer = document.getElementById('reminder-days-container');
    
    // Paramètres section elements
    const userSearchInput = document.getElementById('user_search');
    const userSearchResults = document.getElementById('user_search_results');
    const selectedUsersDisplay = document.getElementById('selected_users_display');
    const selectedUsersListInput = document.getElementById('selected_users_list');
    const departmentSelect = document.getElementById('department_id');
    const departmentUsersSection = document.getElementById('department_users_section');
    const departmentUsersListContainer = document.getElementById('department_users_list');

    let selectedUsers = [];
    let allUsers = @json($users);

    // ========== PARAMÈTRES SECTION ==========
    
    // User search in Paramètres section
    userSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            userSearchResults.style.display = 'none';
            return;
        }

        const filteredUsers = allUsers.filter(u => 
            (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm) &&
            !selectedUsers.includes(u.id)
        );

        if (filteredUsers.length > 0) {
            userSearchResults.style.display = 'block';
            userSearchResults.innerHTML = filteredUsers.map(u => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${u.id}" 
                           data-name="${u.first_name} ${u.last_name}" 
                           onchange="toggleParamUser(this)">
                    <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                </div>
            `).join('');
        } else {
            userSearchResults.innerHTML = '<small class="text-muted">Aucun utilisateur trouvé</small>';
        }
    });

    // Toggle user selection in Paramètres section
    window.toggleParamUser = function(checkbox) {
        const userId = parseInt(checkbox.value);
        const userName = checkbox.dataset.name;

        if (checkbox.checked) {
            selectedUsers.push(userId);
            selectedUsersDisplay.innerHTML += `<span class="badge bg-primary" id="param-user-badge-${userId}">
                ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeParamUser(${userId})"></i>
            </span>`;
        } else {
            selectedUsers = selectedUsers.filter(id => id !== userId);
            const badge = document.getElementById(`param-user-badge-${userId}`);
            if (badge) badge.remove();
        }

        updateParamUsersInput();
    };

    // Remove user from Paramètres selection
    window.removeParamUser = function(userId) {
        selectedUsers = selectedUsers.filter(id => id !== userId);
        const badge = document.getElementById(`param-user-badge-${userId}`);
        if (badge) badge.remove();

        // Uncheck the checkbox in search results
        const checkbox = userSearchResults.querySelector(`input[value="${userId}"]`);
        if (checkbox) checkbox.checked = false;

        updateParamUsersInput();
    };

    // Update hidden input for Paramètres
    function updateParamUsersInput() {
        selectedUsersListInput.value = selectedUsers.join(',');
    }

    // Show department users when department is selected (Paramètres section)
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            const deptUsers = allUsers.filter(u => u.department_id == departmentId);
            
            if (deptUsers.length > 0) {
                departmentUsersSection.style.display = 'block';
                departmentUsersListContainer.innerHTML = deptUsers.map(u => {
                    const isChecked = selectedUsers.includes(u.id) ? 'checked' : '';
                    return `
                        <div class="form-check">
                            <input class="form-check-input dept-user-checkbox" type="checkbox" 
                                   value="${u.id}" data-name="${u.first_name} ${u.last_name}" 
                                   ${isChecked} onchange="toggleDeptUser(this)">
                            <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                        </div>
                    `;
                }).join('');
            } else {
                departmentUsersSection.style.display = 'none';
            }
        } else {
            departmentUsersSection.style.display = 'none';
        }
    });

    // Toggle department user
    window.toggleDeptUser = function(checkbox) {
        const userId = parseInt(checkbox.value);
        const userName = checkbox.dataset.name;

        if (checkbox.checked) {
            if (!selectedUsers.includes(userId)) {
                selectedUsers.push(userId);
                selectedUsersDisplay.innerHTML += `<span class="badge bg-primary" id="param-user-badge-${userId}">
                    ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeParamUser(${userId})"></i>
                </span>`;
            }
        } else {
            selectedUsers = selectedUsers.filter(id => id !== userId);
            const badge = document.getElementById(`param-user-badge-${userId}`);
            if (badge) badge.remove();
        }

        updateParamUsersInput();
    };

    // ========== ALERTES SECTION ==========

    // Show/hide recipient options based on type
    recipientType.addEventListener('change', function() {
        roleOptions.style.display = 'none';
        departmentOptions.style.display = 'none';
        usersOptions.style.display = 'none';

        if (this.value === 'role') {
            roleOptions.style.display = 'block';
        } else if (this.value === 'department') {
            departmentOptions.style.display = 'block';
        } else if (this.value === 'users') {
            usersOptions.style.display = 'block';
        }
    });

    // Show department users when department is selected (Alertes section)
    alertDepartmentId.addEventListener('change', function() {
        const departmentId = this.value;
        if (departmentId) {
            const departmentUsers = allUsers.filter(u => u.department_id == departmentId);
            
            if (departmentUsers.length > 0) {
                departmentUsersCard.style.display = 'block';
                departmentUsersList.innerHTML = departmentUsers.map(u => 
                    `<span class="badge bg-primary me-1 mb-1">${u.first_name} ${u.last_name}</span>`
                ).join('');
            } else {
                departmentUsersCard.style.display = 'none';
            }
        } else {
            departmentUsersCard.style.display = 'none';
        }
    });

    // Search users by name
    userSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        const filteredUsers = allUsers.filter(u => 
            (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm) &&
            !selectedUsers.includes(u.id)
        );

        if (filteredUsers.length > 0) {
            searchResults.innerHTML = filteredUsers.map(u => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${u.id}" 
                           data-name="${u.first_name} ${u.last_name}" 
                           onchange="toggleUser(this)">
                    <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                </div>
            `).join('');
        } else {
            searchResults.innerHTML = '<small class="text-muted">Aucun utilisateur trouvé</small>';
        }
    });

    // Toggle user selection
    window.toggleUser = function(checkbox) {
        const userId = parseInt(checkbox.value);
        const userName = checkbox.dataset.name;

        if (checkbox.checked) {
            selectedUsers.push(userId);
            selectedUsersList.innerHTML += `<span class="badge bg-info" id="user-badge-${userId}">
                ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeUser(${userId})"></i>
            </span>`;
        } else {
            selectedUsers = selectedUsers.filter(id => id !== userId);
            const badge = document.getElementById(`user-badge-${userId}`);
            if (badge) badge.remove();
        }

        updateSelectedUsersInput();
    };

    // Remove user from selection
    window.removeUser = function(userId) {
        selectedUsers = selectedUsers.filter(id => id !== userId);
        const badge = document.getElementById(`user-badge-${userId}`);
        if (badge) badge.remove();

        // Uncheck the checkbox in search results
        const checkbox = searchResults.querySelector(`input[value="${userId}"]`);
        if (checkbox) checkbox.checked = false;

        updateSelectedUsersInput();
    };

    // Update hidden input
    function updateSelectedUsersInput() {
        selectedUsersInput.value = selectedUsers.join(',');
    }

    // Show/hide reminder days
    autoReminderEnabled.addEventListener('change', function() {
        reminderDaysContainer.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endsection
