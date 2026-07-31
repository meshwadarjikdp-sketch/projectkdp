@extends('layouts.admin')

@section('title', 'View Timetable')

@section('content')
<style>
    .timetable-grid th { background-color: #1e3c72; color: #fff; text-align: center; font-weight: 500; }
    .timetable-grid td { text-align: center; vertical-align: middle; padding: 10px; }
    .slot-lunch { background-color: #f8d7da !important; font-weight: bold; color: #721c24; letter-spacing: 2px; }
    .subject-box { background: #eef2f7; padding: 10px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #1e3c72; margin-bottom: 5px; }
    .subject-title { font-weight: bold; color: #1e3c72; font-size: 0.95rem; margin-bottom: 2px; }
    .subject-meta { font-size: 0.85rem; color: #333; }
    @media print {
        .no-print { display: none !important; }
        .timetable-grid th { color: #000 !important; background-color: #eee !important; -webkit-print-color-adjust: exact; }
        .subject-box { border-left-color: #333 !important; }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-primary fw-bold mb-0">Generated Timetable</h2>
            <p class="text-muted mb-0">Review, Export, or Print</p>
        </div>
        <div>
            <a href="{{ route('timetables.create') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Generator
            </a>
            @if(count($timetables) > 0)
                <button onclick="window.print()" class="btn btn-secondary me-2">
                    <i class="fas fa-print"></i> Print PDF
                </button>
                <form action="{{ route('timetables.exportCsv') }}" method="POST" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <input type="hidden" name="division" value="{{ $division }}">
                    <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export CSV
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm no-print" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 no-print">
        <div class="card-body bg-light rounded d-flex align-items-center justify-content-between">
            <div>
                <strong>Filters Applied:</strong>
                <span class="badge bg-primary ms-2">Dept: {{ App\Models\Department::find($departmentId)->department_name ?? 'N/A' }}</span>
                <span class="badge bg-primary ms-1">Sem: {{ $semester }}</span>
                <span class="badge bg-info ms-1">Div: {{ $division }}</span>
                <span class="badge bg-secondary ms-1">Year: {{ $academicYear }}</span>
            </div>
        </div>
    </div>

    @if(count($timetables) > 0 && $config)
        @php
            $flatEntries = $timetables->flatten();
            $theoryEntries = $flatEntries->filter(fn ($entry) => stripos($entry->subject->subject_type, 'Lab') === false);
            $labEntries = $flatEntries->filter(fn ($entry) => stripos($entry->subject->subject_type, 'Lab') !== false);
            $facultyLoadSummary = $flatEntries->groupBy('faculty_id')->map(function ($entries, $facultyId) {
                $faculty = $entries->first()->faculty;

                return [
                    'name' => $faculty?->faculty_name ?? 'Unknown',
                    'hours' => $entries->count(),
                ];
            })->values();
        @endphp

        <div class="card shadow-sm border-0 mb-4 no-print">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fas fa-chart-line text-primary me-2"></i> Generated Timetable Preview</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Total Entries</div>
                            <div class="fw-bold fs-4">{{ $flatEntries->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Theory Slots</div>
                            <div class="fw-bold fs-4">{{ $theoryEntries->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Lab Slots</div>
                            <div class="fw-bold fs-4">{{ $labEntries->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Faculty Load Groups</div>
                            <div class="fw-bold fs-4">{{ $facultyLoadSummary->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold mb-2">Predicted Faculty Load</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Scheduled Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facultyLoadSummary as $load)
                                    <tr>
                                        <td>{{ $load['name'] }}</td>
                                        <td>{{ $load['hours'] }} hrs</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 text-center text-uppercase" style="color: #1e3c72; letter-spacing: 1px;">
                    Timetable - Semester {{ $semester }} (Div {{ $division }})
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered timetable-grid mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Time / Day</th>
                                @foreach($config->working_days as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($slot = 1; $slot <= $config->total_slots; $slot++)
                                <tr>
                                    <th class="align-middle bg-light text-dark">Slot {{ $slot }}</th>
                                    @foreach($config->working_days as $day)
                                        @if($slot == $config->lunch_slot)
                                            <td class="slot-lunch align-middle">LUNCH<br>BREAK</td>
                                        @else
                                            <td>
                                                @php
                                                    $entries = collect();
                                                    if (isset($timetables[$day])) {
                                                        $entries = $timetables[$day]->where('slot_number', $slot);
                                                    }
                                                @endphp
                                                
                                                @if($entries->isNotEmpty())
                                                    @foreach($entries as $entry)
                                                        <div class="subject-box">
                                                            <div class="subject-title">{{ $entry->subject->subject_name }}</div>
                                                            <div class="subject-meta">{{ $entry->faculty->faculty_name }}</div>
                                                            <div class="subject-meta">{{ $entry->classroom->room_number }}</div>
                                                            <div class="subject-meta fw-bold mt-1 text-primary">
                                                                ({{ stripos($entry->subject->subject_type, 'Lab') !== false ? 'Lab' : 'Theory' }})
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-muted small text-center no-print">
                Generated with Strict AI Constraints (Max 2 Hrs/Day Load, Continuous Labs)
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="text-muted mb-3"><i class="fas fa-calendar-times fa-4x"></i></div>
            <h4>No Timetable Found</h4>
            <p>Generate a timetable using the AI Module first.</p>
        </div>
    @endif
</div>
@endsection
