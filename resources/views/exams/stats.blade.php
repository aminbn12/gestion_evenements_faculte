@extends('layouts.app')

@section('title', 'Équité des Surveillances')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-bar-chart"></i> Équité des Surveillances
            </h2>
            <p class="text-muted mb-0">Statistiques et公平 распределение</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('exams.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-ul"></i> Liste
            </a>
            <a href="{{ route('exams.planning') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-check"></i> Planning
            </a>
            <a href="{{ route('exams.stats') }}" class="btn btn-outline-primary {{ request()->routeIs('exams.stats') ? 'active' : '' }}">
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

    <div class="row">
        <!-- Professors Stats -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Statistiques des Enseignants</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Enseignant</th>
                                    <th>Grade</th>
                                    <th>Surveillances</th>
                                    <th>Absences</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($professors as $professor)
                                <tr>
                                    <td>{{ $professor->name }}</td>
                                    <td><span class="badge bg-info">{{ $professor->rank }}</span></td>
                                    <td>
                                        <span class="badge bg-success">{{ $professor->assignments->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $professor->absences->count() }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun enseignant</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Residents Stats -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Statistiques des Résidents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Résident</th>
                                    <th>Niveau</th>
                                    <th>Surveillances</th>
                                    <th>Absences</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($residents as $resident)
                                <tr>
                                    <td>{{ $resident->name }}</td>
                                    <td><span class="badge bg-warning">{{ $resident->formatted_level }}</span></td>
                                    <td>
                                        <span class="badge bg-success">{{ $resident->assignments->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $resident->absences->count() }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucun résident</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Examens</h6>
                            <h2 class="mb-0">{{ $professors->first() ? $professors->first()->assignments->count() + $residents->sum(fn($r) => $r->assignments->count()) : 0 }}</h2>
                        </div>
                        <i class="bi bi-file-earmark-text" style="font-size: 2rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Enseignants</h6>
                            <h2 class="mb-0">{{ $professors->count() }}</h2>
                        </div>
                        <i class="bi bi-person-badge" style="font-size: 2rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Résidents</h6>
                            <h2 class="mb-0">{{ $residents->count() }}</h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 2rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Absences</h6>
                            <h2 class="mb-0">{{ $professors->sum(fn($p) => $p->absences->count()) + $residents->sum(fn($r) => $r->absences->count()) }}</h2>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem; opacity: 0.5;"></i>
                    </div>
                </div>
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
