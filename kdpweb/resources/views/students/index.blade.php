@extends('layouts.admin')

@section('title', 'Manage Students')

@section('content')
    <style>
        .folder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .folder-card {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            border: 1px solid #dbeafe;
            border-radius: 14px;
            padding: 1rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .folder-card h4 {
            margin: 0 0 0.5rem;
            color: #1e3a8a;
        }

        .semester-list {
            display: grid;
            gap: 0.6rem;
            margin-top: 0.75rem;
        }

        .semester-folder {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.7rem;
            background: white;
        }

        .semester-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .student-list {
            display: grid;
            gap: 0.5rem;
        }

        .student-pill {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.7rem;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
    </style>

    <header class="page-header">
        <div>
            <h1 class="page-title">Manage Students</h1>
            <p class="subtitle">Students grouped by department folders</p>
        </div>
        <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
    </header>

    @if (session('success'))
        <div class="feedback">{{ session('success') }}</div>
    @endif

    <section class="panel-card">
        <h3 style="margin:0 0 0.75rem; color:#1e293b;">Department folders</h3>
        <p style="margin:0 0 1rem; color:#64748b;">Each department folder contains semester folders with the students inside them.</p>

        <div class="folder-grid">
            @foreach ($departments as $department)
                <div class="folder-card">
                    <h4>📁 {{ $department->department_name }}</h4>
                    <div class="semester-list">
                        @php $departmentStudents = $students->where('department_id', $department->id); @endphp
                        @if ($departmentStudents->isEmpty())
                            <div style="color:#64748b;">No students registered yet.</div>
                        @else
                            @for ($semester = 1; $semester <= 6; $semester++)
                                @php $semesterStudents = $departmentStudents->where('semester', $semester); @endphp
                                @if ($semesterStudents->isNotEmpty())
                                    <div class="semester-folder">
                                        <div class="semester-title">📂 Semester {{ $semester }}</div>
                                        <div class="student-list">
                                            @foreach ($semesterStudents as $student)
                                                <div class="student-pill">
                                                    <span>{{ $student->student_name }}</span>
                                                    <small>Class {{ $student->class }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
