@extends('layouts.app')

@section('title', 'Planning des Surveillances')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-calendar-check"></i> Planning des Surveillances
            </h2>
            <p class="text-muted mb-0">Affectation des surveillants aux examens</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('exams.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-ul"></i> Liste
            </a>
            <a href="{{ route('exams.planning') }}" class="btn btn-outline-primary {{ request()->routeIs('exams.planning') ? 'active' : '' }}">
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

    <!-- Add Assignment Button -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Nouvelle Affectation</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('exams.assignments.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Examen</label>
                    <select name="exam_id" class="form-select select2" required>
                        <option value="">Sélectionner...</option>
                        @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">
                            {{ $exam->date }} - {{ $exam->subject }} ({{ $exam->promo }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Salle</label>
                    <select name="room_id" class="form-select select2" required>
                        <option value="">Sélectionner...</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Enseignants (surveillants)</label>
                    <select name="prof_ids[]" class="form-select select2" multiple>
                        @foreach($professors as $professor)
                        <option value="{{ $professor->id }}">{{ $professor->name }} ({{ $professor->rank }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Résidents (surveillants)</label>
                    <select name="resident_ids[]" class="form-select select2" multiple>
                        @foreach($residents as $resident)
                        <option value="{{ $resident->id }}">{{ $resident->name }} (A{{ $resident->level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Liste des Affectations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Matière</th>
                            <th>Promo</th>
                            <th>Salle</th>
                            <th>Enseignants</th>
                            <th>Résidents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($assignment->exam->date)->format('d/m/Y') }}</td>
                            <td>{{ $assignment->exam->time }}</td>
                            <td>{{ $assignment->exam->subject }}</td>
                            <td><span class="badge bg-primary">{{ $assignment->exam->promo }}</span></td>
                            <td>{{ $assignment->room->name }}</td>
                            <td>
                                @forelse($assignment->professors as $prof)
                                <span class="badge bg-info me-1">{{ $prof->name }}</span>
                                @empty
                                <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse($assignment->residents as $res)
                                <span class="badge bg-warning me-1">{{ $res->name }}</span>
                                @empty
                                <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td>
                                <form action="{{ route('exams.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
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
                            <td colspan="8" class="text-center text-muted">
                                <i class="bi bi-calendar-x d-block mb-2" style="font-size: 2rem;"></i>
                                Aucune affectation trouvée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
