@extends('layouts.app')

@section('title', 'Créer une alerte - Gestion Événements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Créer une alerte</h1>
            <p class="text-muted mb-0">Envoyer des notifications par email ou WhatsApp</p>
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

    <form action="{{ route('alerts.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Informations de l'alerte</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="event_id" class="form-label">Événement associé</label>
                            <select class="form-select select2 @error('event_id') is-invalid @enderror" 
                                    id="event_id" name="event_id">
                                <option value="">Sélectionner un événement (optionnel)</option>
                                @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}
                                        {{ request('event') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} - {{ $event->start_date->format('d/m/Y') }}
                                </option>
                                @endforeach
                            </select>
                            @error('event_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet *</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type d'alerte *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ old('type') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="both" {{ old('type') === 'both' ? 'selected' : '' }}>Email & WhatsApp</option>
                                </select>
                                @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="scheduled_at" class="form-label">Date d'envoi</label>
                                <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                       id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                                <small class="text-muted">Laisser vide pour un envoi immédiat</small>
                                @error('scheduled_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Destinataires</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Recherche de destinataires</label>
                            <input type="text" class="form-control" id="userSearch" placeholder="Rechercher par nom ou email...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sélectionner les destinataires</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                <label class="form-check-label fw-bold" for="selectAllUsers">
                                    Sélectionner tout
                                </label>
                            </div>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <div id="userList">
                                    @foreach($users as $user)
                                    <div class="form-check user-item" data-name="{{ strtolower($user->full_name) }}" data-email="{{ strtolower($user->email) }}" data-dept="{{ $user->department_id }}">
                                        <input class="form-check-input recipient-checkbox" type="checkbox" name="recipients[]" value="{{ $user->id }}" id="user_{{ $user->id }}">
                                        <label class="form-check-label" for="user_{{ $user->id }}">
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted" id="selectedCount">0 destinataire(s) sélectionné(s)</small>
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Ou sélectionner par département</label>
                            <div class="input-group">
                                <select class="form-select" id="departmentFilter">
                                    <option value="">Sélectionner un département</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-primary" type="button" id="selectDeptUsers">
                                    <i class="bi bi-check2-square me-1"></i>Sélectionner
                                </button>
                            </div>
                            <small class="text-muted">Cliquez sur "Sélectionner" pour ajouter tous les membres du département</small>
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
                                <i class="bi bi-send me-2"></i>Créer l'alerte
                            </button>
                            <a href="{{ route('alerts.index') }}" class="btn btn-outline-secondary">
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
        const checkboxes = document.querySelectorAll('.recipient-checkbox');
        const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('.user-item').style.display !== 'none');
        
        visibleCheckboxes.forEach(function(checkbox) {
            checkbox.checked = document.getElementById('selectAllUsers').checked;
        });
        updateSelectedCount();
    });

    // Select by department
    document.getElementById('selectDeptUsers').addEventListener('click', function() {
        const deptId = document.getElementById('departmentFilter').value;
        if (!deptId) {
            alert('Veuillez sélectionner un département');
            return;
        }
        
        const userItems = document.querySelectorAll('.user-item');
        let count = 0;
        
        userItems.forEach(function(item) {
            if (item.dataset.dept == deptId) {
                const checkbox = item.querySelector('.recipient-checkbox');
                checkbox.checked = true;
                count++;
            }
        });
        
        if (count === 0) {
            alert('Aucun utilisateur trouvé dans ce département');
        } else {
            updateSelectedCount();
        }
    });

    // Update selected count
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.recipient-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = checkedCount + ' destinataire(s) sélectionné(s)';
    }

    // Add change event to all checkboxes
    document.querySelectorAll('.recipient-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.recipient-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un destinataire');
        }
    });
</script>
@endpush
@endsection
