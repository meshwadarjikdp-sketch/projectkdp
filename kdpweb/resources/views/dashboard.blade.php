@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <style>
        @keyframes shimmerLoad {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .stat-card, .report-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before, .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: translateX(-100%);
            animation: shimmerLoad 2s infinite;
        }
    </style>

    <header class="page-header">
        <div>
            <h1 class="page-title">Welcome Admin</h1>
            <p class="subtitle">Dashboard Overview</p>
        </div>
    </header>

    <section class="panel-card">
        <div class="section-title">📊 Dashboard</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Students</div>
                <div class="value">{{ $stats['students'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Faculty</div>
                <div class="value">{{ $stats['faculty'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Departments</div>
                <div class="value">{{ $stats['departments'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Today's Classes</div>
                <div class="value">{{ $stats['classes'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Notifications</div>
                <div class="value">{{ $stats['notifications'] ?? 0 }}</div>
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="section-title">⚡ Quick Access</div>
        <div class="report-grid">
            <div class="report-card"><strong>Faculty Workload</strong><span>Track faculty teaching and load distribution.</span></div>
            <div class="report-card"><strong>Classroom Utilization</strong><span>Review occupancy trends and available rooms.</span></div>
            <div class="report-card"><strong>Timetable Report</strong><span>Inspect schedule coverage and class planning.</span></div>
            <div class="report-card"><strong>Department Report</strong><span>Summarize department activity and performance.</span></div>
        </div>
    </section>
@endsection
