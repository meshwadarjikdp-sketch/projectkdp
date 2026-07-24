@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <style>
        .report-card {
            animation: slideInUp 0.5s ease-out backwards;
        }

        .report-card:nth-child(1) { animation-delay: 0.3s; }
        .report-card:nth-child(2) { animation-delay: 0.35s; }
        .report-card:nth-child(3) { animation-delay: 0.4s; }
        .report-card:nth-child(4) { animation-delay: 0.45s; }
        .report-card:nth-child(5) { animation-delay: 0.5s; }
        .report-card:nth-child(6) { animation-delay: 0.55s; }
    </style>

    <header class="page-header">
        <div>
            <h1 class="page-title">Reports Dashboard</h1>
            <p class="subtitle">Access summary reports for faculty, attendance, classrooms, and departments.</p>
        </div>
        <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
    </header>

    <section class="panel-card">
        <div class="section-title">Available Reports</div>
        <div class="report-grid">
            <div class="report-card"><strong>Faculty Workload</strong><span>Track workload balance across faculty members.</span></div>
            <div class="report-card"><strong>Attendance Report</strong><span>Review attendance trends by department and class.</span></div>
            <div class="report-card"><strong>Classroom Utilization</strong><span>Inspect room occupancy and availability.</span></div>
            <div class="report-card"><strong>Student Attendance</strong><span>Monitor student participation and absences.</span></div>
            <div class="report-card"><strong>Timetable Report</strong><span>Check schedule coverage and recurring conflicts.</span></div>
            <div class="report-card"><strong>Department Report</strong><span>Summarize faculty, class, and activity levels for a department.</span></div>
        </div>
    </section>
@endsection
