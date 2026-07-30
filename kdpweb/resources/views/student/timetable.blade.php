@extends('layouts.student')

@section('title', 'View Timetable')

@section('content')
    <style>
        .tt-table { width:100%; border-collapse:collapse; font-size:0.88rem; }
        .tt-table th { background:linear-gradient(135deg,#1e1b4b,#4338ca); color:white; padding:0.75rem 0.6rem; text-align:center; font-weight:600; }
        .tt-table td { border:1px solid #e2e8f0; padding:0.55rem 0.5rem; text-align:center; vertical-align:middle; min-width:120px; }
        .tt-table tr:nth-child(even) td { background:#f8fafc; }
        .slot-header { background:#f1f5f9; font-weight:700; color:#1e1b4b; white-space:nowrap; }
        .lunch-cell { background:#fef3c7 !important; color:#92400e; font-weight:800; letter-spacing:2px; font-size:0.75rem; }
        .subject-box { background:#ede9fe; border-left:3px solid #6366f1; border-radius:8px; padding:0.5rem 0.6rem; text-align:left; }
        .subject-name { font-weight:700; color:#1e1b4b; font-size:0.88rem; }
        .subject-meta { color:#475569; font-size:0.78rem; margin-top:0.15rem; }
        .type-lab { color:#6366f1; font-weight:700; font-size:0.75rem; }
        .type-theory { color:#059669; font-weight:700; font-size:0.75rem; }
        .empty-slot { color:#cbd5e1; font-size:0.8rem; }
        @media print { .no-print { display:none !important; } }
    </style>

    <div class="page-header">
        <h1>View Timetable 📅</h1>
        <p>Your class schedule for Semester {{ $student->semester }}, {{ $student->department?->department_name }}</p>
    </div>

    <!-- Filter form -->
    <div class="card no-print" style="margin-bottom:1.25rem;">
        <form action="{{ route('student.timetable') }}" method="GET" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;">
            <div>
                <label class="form-label">Division</label>
                <select name="division" class="form-control" style="width:auto;min-width:120px;">
                    @foreach(['A','B','C'] as $div)
                        <option value="{{ $div }}" @selected($division === $div)>Division {{ $div }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Academic Year</label>
                <input type="text" name="academic_year" class="form-control" value="{{ $academicYear }}" placeholder="2025-2026" style="width:140px;">
            </div>
            <div>
                <button type="submit" class="btn-primary" style="padding:0.65rem 1.25rem;">
                    <i class="fa-solid fa-magnifying-glass" style="margin-right:0.4rem;"></i> Load
                </button>
            </div>
        </form>
    </div>

    <!-- Timetable -->
    @if($timetables->isNotEmpty())
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-weight:800;font-size:1.05rem;color:#1e1b4b;">
                        Semester {{ $student->semester }} — Division {{ $division }}
                    </div>
                    <div style="color:#64748b;font-size:0.85rem;">Academic Year: {{ $academicYear }}</div>
                </div>
                <button onclick="window.print()" class="no-print btn-primary" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);padding:0.6rem 1.1rem;font-size:0.88rem;">
                    <i class="fa-solid fa-print" style="margin-right:0.4rem;"></i> Print
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table class="tt-table">
                    <thead>
                        <tr>
                            <th style="min-width:70px;">Slot</th>
                            @foreach($config->working_days as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for($slot = 1; $slot <= $config->total_slots; $slot++)
                            <tr>
                                <td class="slot-header">Slot {{ $slot }}</td>
                                @foreach($config->working_days as $day)
                                    @if($slot == $config->lunch_slot)
                                        <td class="lunch-cell">☕ LUNCH</td>
                                    @else
                                        <td>
                                            @php
                                                $entries = isset($timetables[$day])
                                                    ? $timetables[$day]->where('slot_number', $slot)
                                                    : collect();
                                            @endphp
                                            @if($entries->isNotEmpty())
                                                @foreach($entries as $entry)
                                                    <div class="subject-box">
                                                        <div class="subject-name">{{ $entry->subject?->subject_name }}</div>
                                                        <div class="subject-meta">{{ $entry->faculty?->faculty_name }}</div>
                                                        <div class="subject-meta">{{ $entry->classroom?->room_number }}</div>
                                                        @if($entry->subject)
                                                            <div class="{{ stripos($entry->subject->subject_type, 'lab') !== false ? 'type-lab' : 'type-theory' }}">
                                                                {{ stripos($entry->subject->subject_type, 'lab') !== false ? '⚗️ Lab' : '📖 Theory' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="empty-slot">—</span>
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
    @else
        <div class="card" style="text-align:center;padding:3rem 2rem;">
            <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
            <div style="font-weight:700;font-size:1.1rem;color:#0f172a;margin-bottom:0.5rem;">No Timetable Found</div>
            <div style="color:#64748b;">No timetable has been generated yet for Division {{ $division }}, Academic Year {{ $academicYear }}.</div>
            <div style="color:#64748b;margin-top:0.5rem;font-size:0.9rem;">Please contact your administrator to generate the timetable.</div>
        </div>
    @endif
@endsection
