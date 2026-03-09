@extends('layouts.app')

@section('title', 'Modifier le groupe - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Modifier le groupe</h1>
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

    <form action="{{ route('team.groups.update', $group) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations du groupe</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du groupe *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $group->name) }}" required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $group->description) }}</textarea>
                            @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="research" {{ old('type', $group->type) === 'research' ? 'selected' : '' }}>Recherche</option>
                                <option value="project" {{ old('type', $group->type) === 'project' ? 'selected' : '' }}>Projet</option>
                                <option value="committee" {{ old('type', $group->type) === 'committee' ? 'selected' : '' }}>Comité</option>
                                <option value="unit" {{ old('type', $group->type) === 'unit' ? 'selected' : '' }}>Unité</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                    id="department_id" name="department_id">
                                <option value="">Sélectionner...</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $group->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leader_id" class="form-label">Responsable</label>
                            <select class="form-select @error('leader_id') is-invalid @enderror" 
                                    id="leader_id" name="leader_id">
                                <option value="">Sélectionner...</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('leader_id', $group->leader_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} ({{ $user->role->name ?? 'N/A' }})
                                </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="is_active" name="is_active" 
                                   {{ old('is_active', $group->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Membres</h5>
                    </div>
                    <div class="card-body">
                        @php
                        $groupMemberIds = $group->members->pluck('id')->toArray();
                        @endphp
                        <div class="mb-3">
                            <label class="form-label">Recherche de membres</label>
                            <input type="text" class="form-control" id="userSearch" placeholder="Rechercher par nom ou email...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sélectionner les membres</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                <label class="form-check-label fw-bold" for="selectAllUsers">
                                    Sélectionner tout
                                </label>
                            </div>
                            <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                <div id="userList">
                                    @foreach($users as $user)
                                    <div class="form-check user-item" data-name="{{ strtolower($user->full_name) }}" data-email="{{ strtolower($user->email) }}">
                                        <input class="form-check-input member-checkbox" type="checkbox" name="member_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ in_array($user->id, $groupMemberIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="user_{{ $user->id }}">
                                            {{ $user->full_name }} ({{ $user->role->name ?? 'N/A' }})
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted" id="selectedCount">{{ count($groupMemberIds) }} membre(s) sélectionné(s)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('team.groups') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('userSearch').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const userItems = document.querySelectorAll('.user-item');
        
        userItems.forEach(function(item) {
            const name = item.dataset.name;
            const email = item.dataset.email;
            
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Select all functionality
    document.getElementById('selectAllUsers').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.member-checkbox');
        const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('.user-item').style.display !== 'none');
        
        visibleCheckboxes.forEach(function(checkbox) {
            checkbox.checked = document.getElementById('selectAllUsers').checked;
        });
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.member-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = checkedCount + ' membre(s) sélectionné(s)';
    }

    // Add change event to all checkboxes
    document.querySelectorAll('.member-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateSelectedCount);
    });
</script>
@endpush
@endsection
