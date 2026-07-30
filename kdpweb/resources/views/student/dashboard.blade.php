@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1>Welcome back, {{ explode(' ', $student->student_name)[0] }}! 👋</h1>
        <p>Here's a quick overview of your student portal.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:1.5rem;">
        <div class="card" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;margin-bottom:0;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">📅</div>
            <div style="font-size:0.85rem;opacity:0.85;margin-bottom:0.25rem;">Your Semester</div>
            <div style="font-size:1.8rem;font-weight:800;">{{ $student->semester }}</div>
        </div>
        <div class="card" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4);color:white;margin-bottom:0;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">🏫</div>
            <div style="font-size:0.85rem;opacity:0.85;margin-bottom:0.25rem;">Class</div>
            <div style="font-size:1.8rem;font-weight:800;">{{ $student->class }}</div>
        </div>
        <div class="card" style="background:linear-gradient(135deg,#10b981,#059669);color:white;margin-bottom:0;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">🏛️</div>
            <div style="font-size:0.85rem;opacity:0.85;margin-bottom:0.25rem;">Department</div>
            <div style="font-size:1rem;font-weight:700;margin-top:0.25rem;">{{ $student->department?->department_name ?? '—' }}</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.25rem;">
        <a href="{{ route('student.timetable') }}" style="text-decoration:none;">
            <div class="card" style="cursor:pointer;transition:all 0.2s ease;border:2px solid transparent;margin-bottom:0;" onmouseover="this.style.borderColor='#6366f1';this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)';">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;">📊</div>
                <div style="font-weight:700;font-size:1.05rem;color:#1e1b4b;margin-bottom:0.35rem;">View Timetable</div>
                <div style="color:#64748b;font-size:0.9rem;">Check your weekly class schedule.</div>
            </div>
        </a>
        <a href="{{ route('student.notifications') }}" style="text-decoration:none;">
            <div class="card" style="cursor:pointer;transition:all 0.2s ease;border:2px solid transparent;margin-bottom:0;" onmouseover="this.style.borderColor='#8b5cf6';this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)';">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;">🔔</div>
                <div style="font-weight:700;font-size:1.05rem;color:#1e1b4b;margin-bottom:0.35rem;">Receive Notifications</div>
                <div style="color:#64748b;font-size:0.9rem;">Stay updated with campus announcements.</div>
            </div>
        </a>
        <a href="{{ route('student.profile') }}" style="text-decoration:none;">
            <div class="card" style="cursor:pointer;transition:all 0.2s ease;border:2px solid transparent;margin-bottom:0;" onmouseover="this.style.borderColor='#0ea5e9';this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)';">
                <div style="font-size:2.5rem;margin-bottom:0.75rem;">✏️</div>
                <div style="font-weight:700;font-size:1.05rem;color:#1e1b4b;margin-bottom:0.35rem;">Update Profile</div>
                <div style="color:#64748b;font-size:0.9rem;">Manage your name and password.</div>
            </div>
        </a>
    </div>
@endsection
