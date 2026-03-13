@extends('layouts.app')

@section('title', 'Détails de l\'événement - Gestion Événements')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item active">{{ $event->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $event->title }}</h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Modifier
            </a>
            @if($event->status === 'draft')
            <form action="{{ route('events.publish', $event) }}" method="POST" class="d-inline">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-send me-2"></i>Publier
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Event Details -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Type</h6>
                            <p>{{ ucfirst($event->type) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Statut</h6>
                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : ($event->status === 'draft' ? 'secondary' : ($event->status === 'cancelled' ? 'danger' : 'primary')) }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Date de début</h6>
                            <p><i class="bi bi-calendar me-2"></i>{{ $event->start_date->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Date de fin</h6>
                            <p><i class="bi bi-calendar me-2"></i>{{ $event->end_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Lieu</h6>
                            <p><i class="bi bi-geo-alt me-2"></i>{{ $event->location ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Priorité</h6>
                            <span class="badge {{ $event->getPriorityBadgeClass() }}">{{ ucfirst($event->priority) }}</span>
                        </div>
                    </div>

                    @if($event->description)
                    <div class="mb-3">
                        <h6 class="text-muted">Description</h6>
                        <p>{{ $event->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Participants assignés</h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="bi bi-plus-lg"></i> Assigner
                    </button>
                </div>
                <div class="card-body">
                    @forelse($event->assignments as $assignment)
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ $assignment->user->avatar_url }}" class="avatar me-2" alt="">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $assignment->user->full_name }}</h6>
                            <small class="text-muted">{{ $assignment->role ?? 'Participant' }}</small>
                        </div>
                        <span class="badge bg-{{ $assignment->status === 'confirmed' ? 'success' : 'warning' }}">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Aucun participant assigné</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('alerts.create', ['event' => $event->id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-bell me-2"></i>Créer une alerte
                        </a>
                        @if($event->status !== 'cancelled')
                        <form action="{{ route('events.cancel', $event) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Annuler cet événement?')">
                                <i class="bi bi-x-circle me-2"></i>Annuler l'événement
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants</span>
                        <strong>{{ $event->assignments->count() }}</strong>
                    </div>
                    @if($event->max_participants)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Max participants</span>
                        <strong>{{ $event->max_participants }}</strong>
                    </div>
                    @endif
                    @if($event->budget)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Budget</span>
                        <strong>{{ number_format($event->budget, 2) }} MAD</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner des participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('events.assign', $event) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Search Users -->
                    <div class="mb-3">
                        <label for="modal_user_search" class="form-label">Rechercher un utilisateur</label>
                        <input type="text" class="form-control" id="modal_user_search" placeholder="Tapez un nom...">
                        <div class="mt-2" id="modal_search_results" style="max-height: 120px; overflow-y: auto; display: none;">
                        </div>
                    </div>

                    <!-- Selected Users -->
                    <div class="mb-3">
                        <label class="form-label">Utilisateurs sélectionnés</label>
                        <div id="modal_selected_users" class="d-flex flex-wrap gap-2">
                        </div>
                        <input type="hidden" name="user_ids" id="modal_user_ids" value="">
                    </div>

                    <!-- Department Filter -->
                    <div class="mb-3">
                        <label for="modal_department_filter" class="form-label">Filtrer par département</label>
                        <select class="form-select" id="modal_department_filter">
                            <option value="">Tous les départements</option>
                            @foreach(\App\Models\Department::where('is_active', true)->get() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- All Users List with Checkboxes -->
                    <div class="mb-3">
                        <label class="form-label">Tous les utilisateurs</label>
                        <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto;" id="modal_users_list">
                            @php
                            $eventUserIds = $event->users->pluck('id')->toArray();
                            @endphp
                            @foreach(\App\Models\User::where('status', 'active')->with('department')->get() as $user)
                            <div class="form-check dept-user-item" data-dept-id="{{ $user->department_id }}">
                                <input class="form-check-input modal-user-checkbox" type="checkbox" 
                                       value="{{ $user->id }}" 
                                       data-name="{{ $user->full_name }}"
                                       {{ in_array($user->id, $eventUserIds) ? 'checked' : '' }}
                                       onchange="toggleModalUser(this)">
                                <label class="form-check-label">{{ $user->full_name }} ({{ $user->department?->name ?? 'Sans département' }})</label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="participant">Participant</option>
                            <option value="organizer">Organisateur</option>
                            <option value="speaker">Intervenant</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden list of all users for search -->
<div id="all_modal_users_list" style="display: none;">
    @foreach(\App\Models\User::where('status', 'active')->with('department')->get() as $user)
    <div class="modal-all-user-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->full_name }}" data-user-dept="{{ $user->department_id }}"></div>
    @endforeach
</div>

<script>
// Global variables for modal
let selectedModalUsers = [];
let allModalUsers = [];
let modalUserSearch, modalSearchResults, modalSelectedUsers, modalUserIds;
let modalDepartmentFilter, modalUsersList;

document.addEventListener('DOMContentLoaded', function() {
    // Collect all users from the hidden DOM section
    const allUserItems = document.querySelectorAll('.modal-all-user-item');
    allUserItems.forEach(item => {
        const name = item.dataset.userName || '';
        allModalUsers.push({
            id: parseInt(item.dataset.userId),
            first_name: name.split(' ')[0] || '',
            last_name: name.split(' ').slice(1).join(' ') || '',
            department_id: item.dataset.userDept
        });
    });
    
    // Initialize selected users from existing assignments
    const existingCheckboxes = document.querySelectorAll('.modal-user-checkbox:checked');
    existingCheckboxes.forEach(cb => {
        selectedModalUsers.push(parseInt(cb.value));
    });

    modalUserSearch = document.getElementById('modal_user_search');
    modalSearchResults = document.getElementById('modal_search_results');
    modalSelectedUsers = document.getElementById('modal_selected_users');
    modalUserIds = document.getElementById('modal_user_ids');
    modalDepartmentFilter = document.getElementById('modal_department_filter');
    modalUsersList = document.getElementById('modal_users_list');

    updateModalUserIds();

    // Search users
    modalUserSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            modalSearchResults.style.display = 'none';
            return;
        }

        const filteredUsers = allModalUsers.filter(u => 
            (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm) &&
            !selectedModalUsers.includes(u.id)
        );

        if (filteredUsers.length > 0) {
            modalSearchResults.style.display = 'block';
            modalSearchResults.innerHTML = filteredUsers.map(u => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${u.id}" 
                           data-name="${u.first_name} ${u.last_name}" 
                           onchange="toggleModalUser(this)">
                    <label class="form-check-label">${u.first_name} ${u.last_name}</label>
                </div>
            `).join('');
        } else {
            modalSearchResults.innerHTML = '<small class="text-muted">Aucun utilisateur trouvé</small>';
        }
    });

    // Filter by department
    modalDepartmentFilter.addEventListener('change', function() {
        const deptId = this.value;
        const items = document.querySelectorAll('.dept-user-item');
        items.forEach(item => {
            if (!deptId || item.dataset.deptId == deptId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

window.toggleModalUser = function(checkbox) {
    const userId = parseInt(checkbox.value);
    const userName = checkbox.dataset.name;

    if (checkbox.checked) {
        if (!selectedModalUsers.includes(userId)) {
            selectedModalUsers.push(userId);
            modalSelectedUsers.innerHTML += `<span class="badge bg-primary" id="modal-badge-${userId}">
                ${userName} <i class="bi bi-x-circle cursor-pointer" onclick="removeModalUser(${userId})"></i>
            </span>`;
        }
    } else {
        selectedModalUsers = selectedModalUsers.filter(id => id !== userId);
        const badge = document.getElementById(`modal-badge-${userId}`);
        if (badge) badge.remove();
    }

    updateModalUserIds();
};

window.removeModalUser = function(userId) {
    selectedModalUsers = selectedModalUsers.filter(id => id !== userId);
    const badge = document.getElementById(`modal-badge-${userId}`);
    if (badge) badge.remove();
    
    const checkbox = document.querySelector(`.modal-user-checkbox[value="${userId}"]`);
    if (checkbox) checkbox.checked = false;
    
    const searchCheckbox = modalSearchResults.querySelector(`input[value="${userId}"]`);
    if (searchCheckbox) searchCheckbox.checked = false;

    updateModalUserIds();
};

function updateModalUserIds() {
    modalUserIds.value = selectedModalUsers.join(',');
}
</script>
@endsection
