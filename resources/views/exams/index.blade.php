@extends('layouts.app')

@section('title', 'Surveillance des Examens')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-clipboard-check"></i> Surveillance des Examens
            </h2>
            <p class="text-muted mb-0">Gestion des examens et des surveillances</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('exams.template') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Modèle
            </a>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importExamModal">
                <i class="bi bi-upload me-1"></i>Importer
            </button>
            <a href="{{ route('exams.export') }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Exporter
            </a>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-primary {{ request()->routeIs('exams.index') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i> Liste
            </a>
            <a href="{{ route('exams.planning') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-check"></i> Planning
            </a>
            <a href="{{ route('exams.stats') }}" class="btn btn-outline-primary">
                <i class="bi bi-bar-chart"></i> Équité
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Signaler une Absence -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-dash"></i> Signaler une Absence</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('exams.absences.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Enseignant</label>
                    <select name="professor_id" class="form-select select2">
                        <option value="">Sélectionner...</option>
                        @foreach($professors as $professor)
                        <option value="{{ $professor->id }}">{{ $professor->user ? $professor->user->full_name : $professor->name }} ({{ $professor->rank }}) - {{ $professor->subject ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ou Résident</label>
                    <select name="resident_id" class="form-select select2">
                        <option value="">Sélectionner...</option>
                        @foreach($residents as $resident)
                        <option value="{{ $resident->id }}">{{ $resident->user ? $resident->user->full_name : $resident->name }} (A{{ $resident->level }} - {{ $resident->specialty }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Raison</label>
                    <input type="text" name="reason" class="form-control" placeholder="Optionnel">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-exclamation-triangle"></i> Signaler l'absence
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs for different sections -->
    <ul class="nav nav-tabs mb-4" id="examTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button">
                <i class="bi bi-file-earmark-text"></i> Examens
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button">
                <i class="bi bi-door-open"></i> Salles
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="professors-tab" data-bs-toggle="tab" data-bs-target="#professors" type="button">
                <i class="bi bi-person-badge"></i> Enseignants
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="residents-tab" data-bs-toggle="tab" data-bs-target="#residents" type="button">
                <i class="bi bi-people"></i> Résidents
            </button>
        </li>
    </ul>

    <div class="tab-content" id="examTabsContent">
        <!-- Exams Tab -->
        <div class="tab-pane fade show active" id="exams" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Liste des Examens</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExamModal">
                        <i class="bi bi-plus-circle"></i> Ajouter un Examen
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Durée</th>
                                    <th>Promo</th>
                                    <th>Matière</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $exam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($exam->date)->format('d/m/Y') }}</td>
                                    <td>{{ $exam->time }}</td>
                                    <td>{{ $exam->duration }} min</td>
                                    <td><span class="badge bg-primary">{{ $exam->promo }}</span></td>
                                    <td>{{ $exam->subject }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editExamModal{{ $exam->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun examen trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Tab -->
        <div class="tab-pane fade" id="rooms" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> Liste des Salles</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="bi bi-plus-circle"></i> Ajouter une Salle
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Capacité Profs</th>
                                    <th>Capacité Résidents</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                <tr>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $room->prof_capacity }}</td>
                                    <td>{{ $room->resident_capacity }}</td>
                                    <td><span class="badge bg-success">{{ $room->total_capacity }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucune salle trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professors Tab -->
        <div class="tab-pane fade" id="professors" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Liste des Enseignants</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                        <i class="bi bi-plus-circle"></i> Ajouter un Enseignant
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Grade</th>
                                    <th>Responsable Promo</th>
                                    <th>Matière</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($professors as $professor)
                                <tr>
                                    <td>{{ $professor->user ? $professor->user->full_name : $professor->name }}</td>
                                    <td><span class="badge bg-info">{{ $professor->rank }}</span></td>
                                    <td>{{ $professor->responsible_promo ?? '-' }}</td>
                                    <td>{{ $professor->subject ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun enseignant trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Residents Tab -->
        <div class="tab-pane fade" id="residents" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Liste des Résidents</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                        <i class="bi bi-plus-circle"></i> Ajouter un Résident
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Niveau</th>
                                    <th>Spécialité</th>
                                    <th>Département</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($residents as $resident)
                                <tr>
                                    <td>{{ $resident->user ? $resident->user->full_name : $resident->name }}</td>
                                    <td><span class="badge bg-warning">A{{ $resident->level }}</span></td>
                                    <td>{{ $resident->specialty }}</td>
                                    <td>{{ $resident->user && $resident->user->department ? $resident->user->department->name : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun résident trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Exam Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Examen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Heure</label>
                        <input type="time" name="time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durée (minutes)</label>
                        <input type="number" name="duration" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Promotion</label>
                        <select name="promo" class="form-select" required>
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
                    <div class="mb-3">
                        <label class="form-label">Matière</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Salle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('exams.rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacité Profs</label>
                        <input type="number" name="prof_capacity" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacité Résidents</label>
                        <input type="number" name="resident_capacity" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Professor Modal -->
<div class="modal fade" id="addProfessorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('exams.professors.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select name="rank" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="Pr">Pr (Professeur)</option>
                            <option value="Dr">Dr (Docteur)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Promotion Responsable (optionnel)</label>
                        <select name="responsible_promo" class="form-select">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Résident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('exams.residents.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Niveau</label>
                        <select name="level" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="1">A1</option>
                            <option value="2">A2</option>
                            <option value="3">A3</option>
                            <option value="4">A4</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialty" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Exam Modal -->
<div class="modal fade" id="importExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importer des examens</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('exams.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Téléchargez d'abord le modèle pour voir les colonnes requises.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Importer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Sélectionner...'
        });
    });
</script>
@endpush
@endsection
