@extends('layouts.admin')

@section('title', 'View Timetable')

@section('content')
<style>
    .timetable-wrapper { border: 1px solid #000; padding: 0; background: #fff; color: #000; margin-bottom: 20px; }
    .timetable-header { text-align: center; padding: 10px; border-bottom: 1px solid #000; }
    .timetable-header h3 { margin: 0; font-size: 18px; font-weight: bold; }
    .timetable-header h4 { margin: 5px 0; font-size: 16px; font-weight: bold; }
    .timetable-header h5 { margin: 0; font-size: 14px; font-weight: normal; }
    .timetable-sub-header { display: flex; justify-content: space-between; padding: 10px 15px; font-size: 12px; font-weight: bold; }
    .timetable-sub-header .right-info { text-align: right; font-size: 10px; font-weight: normal; }
    .timetable-grid { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .timetable-grid th, .timetable-grid td { border: 1px solid #000; text-align: center; vertical-align: middle; padding: 5px; font-size: 12px; }
    .timetable-grid th { font-weight: bold; text-transform: uppercase; }
    .slot-lunch { font-weight: bold; letter-spacing: 2px; }
    .entry-box { margin-bottom: 4px; }
    .entry-box:last-child { margin-bottom: 0; }
    .timetable-footer { padding: 15px; font-size: 12px; font-weight: bold; }
    .timetable-footer-signature { margin-top: 40px; text-align: right; padding-right: 30px; }
    
    @media print {
        .no-print { display: none !important; }
        body { background: #fff; }
        .container-fluid { padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .timetable-wrapper { border: 2px solid #000; }
        .timetable-grid th, .timetable-grid td { border: 1px solid #000 !important; }
        @page { size: A4 landscape; margin: 10mm; }
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

        <div class="timetable-wrapper">
            <div class="timetable-header">
                <h3>K. D. Polytechnic, Patan</h3>
                <h4>Time Table (Term: {{ $academicYear }})</h4>
                <h5>Department of {{ App\Models\Department::find($departmentId)->department_name ?? 'Computer Engineering' }}</h5>
            </div>
            <div class="timetable-sub-header">
                <div>
                    Class: {{ $semester }}{{ $division }}
                </div>
                <div class="right-info">
                    Term Dates: 24-JUL-2026 to 31-DEC-2026 (SEM-1)<br>
                    13-JUL-2026 to 03-DEC-2026 (SEM-3)<br>
                    15-JUN-2026 to 30-OCT-2026 (SEM-5)<br>
                    WEF: 29-JUN-2026
                </div>
            </div>
            <table class="timetable-grid">
                <thead>
                    <tr>
                        <th style="width: 12%;">TIME</th>
                        @foreach($config->working_days as $day)
                            <th>{{ strtoupper($day) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $timeSlots = [
                            1 => '08:30 - 09:30',
                            2 => '09:30 - 10:30',
                            3 => '10:30 - 11:30',
                            4 => '11:30 - 12:30',
                            5 => '01:00 - 02:00',
                            6 => '02:00 - 03:00',
                            7 => '03:10 - 04:10',
                            8 => '04:10 - 05:10',
                            9 => '05:10 - 06:10',
                            10 => '06:10 - 07:10',
                        ];
                    @endphp
                    @for($slot = 1; $slot <= $config->total_slots; $slot++)
                        @if($slot == $config->lunch_slot)
                            <tr>
                                <th>{{ $timeSlots[$slot] ?? '12:30 - 01:00' }}</th>
                                <td colspan="{{ count($config->working_days) }}" class="slot-lunch">RECESS</td>
                            </tr>
                        @else
                            <tr>
                                <th>{{ $timeSlots[$slot] ?? "Slot $slot" }}</th>
                                @foreach($config->working_days as $day)
                                    <td>
                                        @php
                                            $entries = collect();
                                            if (isset($timetables[$day])) {
                                                $entries = $timetables[$day]->where('slot_number', $slot);
                                            }
                                        @endphp
                                        
                                        @if($entries->isNotEmpty())
                                            @foreach($entries as $index => $entry)
                                                <div class="entry-box">
                                                    {{ $entry->subject->subject_code ?? $entry->subject->subject_name }}-{{ $entry->faculty->faculty_name ?? 'N/A' }}-{{ $entry->classroom->room_number ?? 'N/A' }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endfor
                </tbody>
            </table>
            <div class="timetable-footer">
                <div style="text-align: center; font-weight: normal;">^ = Tutorial</div>
                <div style="margin-top: 10px;">Recess-1: 12:30 PM - 01:00 PM</div>
                <div style="margin-bottom: 20px;">Recess-2: 03:00 PM - 03:10 PM</div>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div style="border: 1px solid #000; padding: 5px; display: inline-block;">
                        Skill Based Training: 15-JUN-2026 to 28-JUN-2026 (SEM-5)
                    </div>
                    <div class="timetable-footer-signature">
                        HOD<br>
                        Department of {{ App\Models\Department::find($departmentId)->department_name ?? 'Computer Engineering' }}
                    </div>
                </div>
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
