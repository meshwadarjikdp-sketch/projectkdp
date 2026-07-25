@extends('layouts.admin')

@section('title', 'Manage Subjects')

@section('content')
    <style>
        .subjects-shell { display: grid; gap: 1.25rem; }
        .hero-card {
            background: linear-gradient(135deg, rgba(31,60,136,0.98) 0%, rgba(59,130,246,0.95) 100%);
            color: white;
            border-radius: 20px;
            padding: 1.4rem 1.5rem;
            box-shadow: 0 18px 40px rgba(31,60,136,0.18);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        .hero-card h1 { margin: 0 0 0.35rem; font-size: 1.8rem; }
        .hero-card p { margin: 0; color: rgba(255,255,255,0.86); }
        .hero-card .btn { background: rgba(255,255,255,0.18); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 0.75rem 1rem; border-radius: 999px; text-decoration: none; font-weight: 700; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
        .stat-card { background: white; border-radius: 16px; padding: 1rem 1.1rem; box-shadow: 0 12px 28px rgba(15,23,42,0.08); display: flex; gap: 0.85rem; align-items: center; }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; background: linear-gradient(135deg, #dbeafe, #eff6ff); color: #1f3c88; font-size: 1.1rem; }
        .stat-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
        .stat-value { font-size: 1.25rem; font-weight: 800; color: #111827; }
        .glass-card { background: white; border-radius: 18px; box-shadow: 0 10px 28px rgba(15,23,42,0.08); padding: 1.15rem; }
        .filters-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0.9rem; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 0.4rem; }
        .field label { font-size: 0.8rem; font-weight: 700; color: #475569; }
        .field input, .field select { border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.72rem 0.8rem; font: inherit; color: #0f172a; }
        .field input:focus, .field select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .btn-primary, .btn-secondary, .icon-btn { border: 0; border-radius: 10px; padding: 0.8rem 1rem; font: inherit; font-weight: 700; cursor: pointer; transition: all 0.25s ease; }
        .btn-primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-primary:hover, .btn-secondary:hover, .icon-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(15,23,42,0.12); }
        .form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
        .switch-row { display: flex; align-items: center; gap: 0.7rem; margin-top: 1.2rem; }
        .switch { position: relative; width: 54px; height: 30px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; inset: 0; background: #cbd5e1; border-radius: 999px; cursor: pointer; transition: 0.25s; }
        .slider:before { content: ''; position: absolute; width: 22px; height: 22px; left: 4px; top: 4px; background: white; border-radius: 50%; transition: 0.25s; }
        .switch input:checked + .slider { background: #2563eb; }
        .switch input:checked + .slider:before { transform: translateX(24px); }
        .chip { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.65rem; border-radius: 999px; font-size: 0.78rem; font-weight: 700; }
        .chip-active { background: #dcfce7; color: #166534; }
        .chip-inactive { background: #fee2e2; color: #991b1b; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 980px; border-collapse: collapse; }
        th, td { padding: 0.95rem 0.9rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; color: #475569; font-size: 0.73rem; letter-spacing: 0.08em; text-transform: uppercase; position: sticky; top: 0; }
        tbody tr:nth-child(odd) { background: #fbfdff; }
        .action-group { display: flex; gap: 0.45rem; flex-wrap: wrap; }
        .icon-btn { padding: 0.6rem 0.7rem; background: #eff6ff; color: #1d4ed8; }
        .icon-btn.danger { background: #fee2e2; color: #b91c1c; }
        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #64748b; }
        .empty-state .emoji { font-size: 2rem; display: block; margin-bottom: 0.6rem; }
        .pill { display: inline-flex; padding: 0.35rem 0.6rem; border-radius: 999px; background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.8rem; }
        @media (max-width: 1100px) { .filters-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 720px) { .hero-card { flex-direction: column; align-items: flex-start; } .stats-grid, .filters-grid, .form-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="subjects-shell">
        <section class="hero-card">
            <div>
                <h1>Manage Subjects</h1>
                <p>Manage academic subjects for timetable generation.</p>
            </div>
            <a class="btn" href="{{ route('dashboard') }}">Back to Dashboard</a>
        </section>

        @if (session('success'))
            <div class="feedback">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="validation-errors">
                <strong>Please correct the subject details.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div>
                    <div class="stat-title">Total Subjects</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧠</div>
                <div>
                    <div class="stat-title">Theory Subjects</div>
                    <div class="stat-value">{{ $stats['theory'] }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧪</div>
                <div>
                    <div class="stat-title">Lab Subjects</div>
                    <div class="stat-value">{{ $stats['lab'] }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚙️</div>
                <div>
                    <div class="stat-title">Practical Subjects</div>
                    <div class="stat-value">{{ $stats['practical'] }}</div>
                </div>
            </div>
        </section>

        <section class="glass-card">
            <form method="GET" class="filters-grid">
                <div class="field">
                    <label for="search">Search Subject</label>
                    <input id="search" name="search" value="{{ request('search') }}" placeholder="Search by code or subject" />
                </div>
                <div class="field">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id">
                        <option value="">All departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester">
                        <option value="">All semesters</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('semester') == (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="field">
                    <label for="faculty_id">Faculty</label>
                    <select id="faculty_id" name="faculty_id">
                        <option value="">All faculties</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->faculty_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="subject_type">Subject Type</label>
                    <select id="subject_type" name="subject_type">
                        <option value="">Any type</option>
                        <option value="Theory" {{ request('subject_type') == 'Theory' ? 'selected' : '' }}>Theory</option>
                        <option value="Practical" {{ request('subject_type') == 'Practical' ? 'selected' : '' }}>Practical</option>
                        <option value="Lab" {{ request('subject_type') == 'Lab' ? 'selected' : '' }}>Lab</option>
                    </select>
                </div>
                <div class="field">
                    <label>&nbsp;</label>
                    <div style="display:flex;gap:0.6rem;">
                        <button class="btn-primary" type="submit">Search</button>
                        <a class="btn-secondary" href="{{ route('subjects.index') }}" style="display:inline-block;text-decoration:none;text-align:center;">Reset</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="glass-card">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
                <div>
                    <h2 style="margin:0 0 0.25rem;font-size:1.1rem;color:#0f172a;">Add Subject</h2>
                    <p style="margin:0;color:#64748b;">Create subjects that will feed the AI timetable system.</p>
                </div>
            </div>

            <form action="{{ route('subjects.store') }}" method="POST" class="form-grid">
                @csrf
                <div class="field">
                    <label for="subject_code">Subject Code</label>
                    <input id="subject_code" name="subject_code" value="{{ old('subject_code') }}" required />
                </div>
                <div class="field">
                    <label for="subject_name">Subject Name</label>
                    <input id="subject_name" name="subject_name" value="{{ old('subject_name') }}" required />
                </div>
                <div class="field">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">Select semester</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="field">
                    <label for="faculty_id">Faculty</label>
                    <select id="faculty_id" name="faculty_id" required>
                        <option value="">Select faculty</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->faculty_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="credits">Credits</label>
                    <input id="credits" name="credits" type="number" min="1" value="{{ old('credits') }}" required />
                </div>
                <div class="field">
                    <label for="hours_per_week">Hours Per Week</label>
                    <input id="hours_per_week" name="hours_per_week" type="number" min="1" max="10" value="{{ old('hours_per_week') }}" required />
                </div>
                <div class="field">
                    <label>Subject Type</label>
                    <div style="display:flex;gap:0.8rem;flex-wrap:wrap;margin-top:0.3rem;">
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Theory" {{ old('subject_type') == 'Theory' ? 'checked' : '' }} /> Theory</label>
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Practical" {{ old('subject_type') == 'Practical' ? 'checked' : '' }} /> Practical</label>
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Lab" {{ old('subject_type') == 'Lab' ? 'checked' : '' }} /> Lab</label>
                    </div>
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <div class="switch-row">
                        <label class="switch">
                            <input type="checkbox" name="elective" value="1" {{ old('elective') ? 'checked' : '' }} />
                            <span class="slider"></span>
                        </label>
                        <span style="font-weight:700;color:#334155;">Elective</span>
                    </div>
                </div>
                <div class="field" style="grid-column: span 2; display:flex;gap:0.7rem;align-items:center;">
                    <button class="btn-primary" type="submit">Add Subject</button>
                    <a class="btn-secondary" href="{{ route('subjects.index') }}" style="display:inline-block;text-decoration:none;text-align:center;">Clear</a>
                </div>
            </form>
        </section>

        <section class="glass-card">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:0.7rem;">
                <div>
                    <h3 style="margin:0 0 0.25rem;color:#0f172a;">Subject Directory</h3>
                    <p style="margin:0;color:#64748b;">Responsive table with sorting, filtering, and action controls.</p>
                </div>
                <span class="pill">{{ $subjects->count() }} records</span>
            </div>
            <div class="table-wrap">
                @if ($subjects->isEmpty())
                    <div class="empty-state">
                        <span class="emoji">🧾</span>
                        <h4 style="margin:0 0 0.35rem;color:#0f172a;">No subjects found</h4>
                        <p>Click Add Subject to create your first subject.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Faculty</th>
                                <th>Credits</th>
                                <th>Hours/Week</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td><strong>{{ $subject->subject_code }}</strong></td>
                                    <td>{{ $subject->subject_name }}</td>
                                    <td>{{ $subject->department?->department_name }}</td>
                                    <td>{{ $subject->semester }}</td>
                                    <td>{{ $subject->faculty?->faculty_name }}</td>
                                    <td>{{ $subject->credits }}</td>
                                    <td>{{ $subject->hours_per_week }}</td>
                                    <td>{{ $subject->subject_type }}</td>
                                    <td>
                                        @if ($subject->status === 'Active')
                                            <span class="chip chip-active">Active</span>
                                        @else
                                            <span class="chip chip-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button class="icon-btn" type="button">View</button>
                                            <button class="icon-btn" type="button">Edit</button>
                                            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="icon-btn danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </div>
@endsection
