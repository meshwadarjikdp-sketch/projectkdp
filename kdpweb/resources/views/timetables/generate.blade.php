@extends('layouts.admin')

@section('title', 'AI Generate Timetable Dashboard')

@section('content')
<style>
    :root {
        --primary-blue: #2563eb;
        --light-blue: #eff6ff;
        --card-bg: rgba(255, 255, 255, 0.7);
        --card-border: rgba(255, 255, 255, 0.4);
        --text-main: #1e293b;
        --text-muted: #64748b;
    }
    
    body {
        background: #f1f5f9; /* Clean subtle background */
        font-family: 'Poppins', sans-serif;
    }

    /* Glassmorphism Cards */
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        box-shadow: 0 10px 32px rgba(0,0,0,0.08);
    }

    /* Sticky Action Bar */
    .sticky-action-bar {
        position: sticky;
        top: 70px; /* Offset for admin topbar if any */
        z-index: 100;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 16px 24px;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        border: 1px solid var(--card-border);
    }

    .form-select, .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        box-shadow: none;
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn-premium {
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary-gradient {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border: none;
    }
    
    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);
        color: white;
    }

    .btn-outline-soft {
        background: white;
        border: 1px solid #cbd5e1;
        color: var(--text-main);
    }
    .btn-outline-soft:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    /* Working Days Table */
    .day-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .day-row:last-child { border-bottom: none; }
    .day-name { width: 120px; font-weight: 600; font-size: 15px; }
    
    /* Custom Toggle Switch */
    .form-switch .form-check-input {
        width: 40px;
        height: 20px;
        background-color: #cbd5e1;
        border: none;
    }
    .form-switch .form-check-input:checked {
        background-color: #10b981;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid #e2e8f0;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .bg-blue-light { background: #eff6ff; color: #2563eb; }
    .bg-green-light { background: #f0fdf4; color: #16a34a; }
    .bg-purple-light { background: #faf5ff; color: #9333ea; }
    .bg-orange-light { background: #fff7ed; color: #ea580c; }

    .stat-value { font-size: 24px; font-weight: 700; color: var(--text-main); line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }

    /* Faculty Load Table */
    .table-modern {
        width: 100%;
        font-size: 13px;
    }
    .table-modern th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-modern td {
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }
    .text-danger-soft { color: #ef4444; font-weight: 600; background: #fef2f2; padding: 2px 8px; border-radius: 6px; }

    /* Checklist */
    .ai-checklist { list-style: none; padding: 0; margin: 0; }
    .ai-checklist li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        font-size: 14px;
        color: #334155;
        font-weight: 500;
    }
    .check-icon { color: #10b981; font-size: 18px; }

    /* Tags/Checkboxes */
    .custom-checkbox-btn {
        display: inline-block;
    }
    .custom-checkbox-btn input[type="checkbox"] { display: none; }
    .custom-checkbox-btn label {
        background: white;
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        color: #475569;
    }
    .custom-checkbox-btn input[type="checkbox"]:checked + label {
        background: #eff6ff;
        border-color: #2563eb;
        color: #2563eb;
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-3">
        <h2 class="fw-bold mb-1" style="color: #0f172a;">AI Timetable Dashboard</h2>
        <p class="text-muted">Configure strict constraints and let AI orchestrate the perfect schedule.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="timetable-form" action="{{ route('timetables.generate') }}" method="POST">
        @csrf
        <input type="hidden" name="total_slots" value="6">
        <input type="hidden" name="lunch_slot" value="4">
        <input type="hidden" name="lecture_slots[]" value="1">
        <input type="hidden" name="lecture_slots[]" value="2">
        <input type="hidden" name="lecture_slots[]" value="3">
        <input type="hidden" name="lecture_slots[]" value="5">
        <input type="hidden" name="lecture_slots[]" value="6">
        <input type="hidden" name="lab_slots[]" value="1">
        <input type="hidden" name="lab_slots[]" value="2">
        <input type="hidden" name="lab_slots[]" value="3">
        <input type="hidden" name="lab_slots[]" value="5">
        <input type="hidden" name="lab_slots[]" value="6">

        <!-- Sticky Filters & Action Bar -->
        <div class="sticky-action-bar">
            <div class="d-flex flex-wrap gap-3 align-items-center flex-grow-1">
                <select id="department_id" name="department_id" class="form-select" style="width: 200px;" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected($selectedDepartment == $dept->id)>{{ $dept->department_name }}</option>
                    @endforeach
                </select>
                <select id="semester" name="semester" class="form-select" style="width: 140px;" required>
                    <option value="">Select Semester</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" @selected($selectedSemester == $s)>Semester {{ $s }}</option>
                    @endfor
                </select>
                <select name="academic_year" class="form-select" style="width: 140px;" required>
                    @foreach(['2025-2026', '2026-2027', '2027-2028'] as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                <select name="division" class="form-select" style="width: 100px;" required>
                    @foreach(['A', 'B', 'C', 'D'] as $div)
                        <option value="{{ $div }}">Div {{ $div }}</option>
                    @endforeach
                </select>
                <select class="form-select" style="width: 160px;">
                    <option>Class Timetable</option>
                    <option>Faculty Timetable</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-premium btn-outline-soft"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn btn-premium btn-outline-soft"><i class="fas fa-file-pdf text-danger"></i> PDF</button>
                <button type="button" class="btn btn-premium btn-outline-soft"><i class="fas fa-file-excel text-success"></i> Excel</button>
                <button type="button" class="btn btn-premium btn-outline-soft"><i class="fas fa-sync-alt"></i> Regenerate</button>
                <button type="submit" class="btn btn-premium btn-primary-gradient"><i class="fas fa-magic"></i> Generate Timetable</button>
                <button type="button" class="btn btn-premium btn-outline-soft" style="background: #10b981; color: white; border: none;"><i class="fas fa-upload"></i> Publish</button>
            </div>
        </div>

    <div class="row gx-4">
        <!-- LEFT COLUMN: Configuration -->
        <div class="col-12">
            
            <!-- Working Days Configuration -->
            <div class="glass-card">
                <h5 class="fw-bold mb-4"><i class="far fa-calendar-alt text-primary me-2"></i> Working Days & Time Configuration</h5>
                
                <div class="table-responsive">
                    <div class="day-row fw-bold text-muted" style="font-size: 13px;">
                        <div class="day-name">Day</div>
                        <div style="width: 80px;">Status</div>
                        <div style="width: 140px;">Start Time</div>
                        <div style="width: 140px;">End Time</div>
                        <div style="width: 140px;">Break Start</div>
                        <div style="width: 140px;">Break End</div>
                    </div>
                    
                    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                    @foreach($days as $day)
                    <div class="day-row">
                        <div class="day-name">{{ $day }}</div>
                        <div style="width: 80px;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="working_days[]" value="{{ $day }}" checked>
                            </div>
                        </div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="08:00"></div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="17:00"></div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="13:00"></div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="14:00"></div>
                    </div>
                    @endforeach
                    <!-- Sunday Optional -->
                    <div class="day-row">
                        <div class="day-name">Sunday</div>
                        <div style="width: 80px;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="09:00" disabled></div>
                        <div style="width: 140px;"><input type="time" class="form-control" value="13:00" disabled></div>
                        <div style="width: 140px;"><input type="time" class="form-control" disabled></div>
                        <div style="width: 140px;"><input type="time" class="form-control" disabled></div>
                    </div>
                </div>
            </div>

            <!-- Subject Mapping & Assignments Card -->
            <div class="glass-card">
                <h5 class="fw-bold mb-4"><i class="fas fa-user-tie text-primary me-2"></i> Subject Mapping & Faculty/Classroom Assignments</h5>
                <div id="subject-mapping-container">
                    <p class="text-muted text-center py-4">Please select a Department and Semester to configure subject mappings.</p>
                </div>
            </div>

            <!-- Durations & Limits -->
            <div class="row">
                <div class="col-md-12">
                    <div class="glass-card">
                        <h5 class="fw-bold mb-4"><i class="fas fa-sliders-h text-primary me-2"></i> Lecture & Constraint Settings</h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold small">Lecture Duration</label>
                                <select class="form-select"><option>1 Hour</option><option>1.5 Hours</option></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold small">Lab Duration</label>
                                <select class="form-select"><option>2 Continuous Hours</option><option>3 Continuous Hours</option></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold small">Max Faculty Load (Day)</label>
                                <select class="form-select"><option>4 Hours</option><option>5 Hours</option><option>6 Hours</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold small">Max Lectures Per Day (Student)</label>
                                <input type="number" class="form-control" value="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold small">Max Labs Per Day (Student)</label>
                                <input type="number" class="form-control" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classrooms & Labs -->
            <div class="glass-card">
                <h5 class="fw-bold mb-4"><i class="fas fa-door-open text-primary me-2"></i> Available Classrooms & Labs</h5>
                
                <h6 class="fw-bold small text-muted mb-2">Theory Classrooms</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @for($i=101; $i<=105; $i++)
                    <div class="custom-checkbox-btn">
                        <input type="checkbox" id="room{{$i}}" checked>
                        <label for="room{{$i}}">Room {{$i}}</label>
                    </div>
                    @endfor
                </div>

                <h6 class="fw-bold small text-muted mb-2">Computer & Technical Labs</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @for($i=1; $i<=6; $i++)
                    <div class="custom-checkbox-btn">
                        <input type="checkbox" id="lab{{$i}}" checked>
                        <label for="lab{{$i}}">Lab {{$i}}</label>
                    </div>
                    @endfor
                </div>
                
                <h6 class="fw-bold small text-muted mb-2">Workshop Labs</h6>
                <div class="d-flex flex-wrap gap-2">
                    <div class="custom-checkbox-btn"><input type="checkbox" id="w1" checked><label for="w1">Workshop A</label></div>
                    <div class="custom-checkbox-btn"><input type="checkbox" id="w2"><label for="w2">Workshop B</label></div>
                </div>
            </div>

            <!-- AI Strict Rules -->
            <div class="glass-card">
                <h5 class="fw-bold mb-3"><i class="fas fa-robot text-primary me-2"></i> Strict AI Scheduling Rules Enforced</h5>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="ai-checklist">
                            <li><i class="fas fa-check-circle check-icon"></i> Exactly 1-hour standard lectures</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Labs scheduled as 2 continuous hours</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Distributed theory lectures (No same day repeats)</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Theory and Lab of same subject on different days</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Strict Zero Faculty Clash policy</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Strict Zero Classroom/Lab Clash policy</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="ai-checklist">
                            <li><i class="fas fa-check-circle check-icon"></i> Faculty max load respected (4 hrs/day)</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Labs allocated strictly to Lab Rooms</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Avoid long continuous student fatigue</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Balanced subject distribution across week</li>
                            <li><i class="fas fa-check-circle check-icon"></i> Strictly respect fixed lunch break</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

<script>
    // Minimal JS to handle toggling Sunday disabled state
    document.addEventListener('DOMContentLoaded', () => {
        const switches = document.querySelectorAll('.form-switch input[type="checkbox"]');
        switches.forEach(sw => {
            sw.addEventListener('change', (e) => {
                const inputs = e.target.closest('.day-row').querySelectorAll('input[type="time"]');
                inputs.forEach(input => {
                    input.disabled = !e.target.checked;
                });
            });
        });
    });
</script>

<script>
    // JS for dynamic preview of subjects, faculties and classrooms
    document.addEventListener("DOMContentLoaded", () => {
        const deptSelect = document.getElementById('department_id');
        const semSelect = document.getElementById('semester');
        const container = document.getElementById('subject-mapping-container');

        function fetchPreviewData() {
            const deptId = deptSelect.value;
            const sem = semSelect.value;
            const selectedDays = Array.from(document.querySelectorAll('input[name="working_days[]"]:checked'))
                .map(input => input.value)
                .join(',');

            if (!deptId || !sem) {
                container.innerHTML = '<p class="text-muted text-center py-4">Please select a Department and Semester to configure subject mappings.</p>';
                return;
            }

            container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2">Loading subjects...</p></div>';

            fetch(`/timetables/preview?department_id=${deptId}&semester=${sem}&working_days=${encodeURIComponent(selectedDays)}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        container.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        return;
                    }

                    if (data.subjects.length === 0) {
                        container.innerHTML = '<p class="text-muted text-center py-4">No active subjects found for this selection.</p>';
                        return;
                    }

                    const metrics = data.analytics?.subject_metrics ?? {};
                    const facultyLoad = data.analytics?.predicted_faculty_load ? Object.values(data.analytics.predicted_faculty_load) : [];

                    let html = `
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-blue-light"><i class="fas fa-book-open"></i></div>
                                    <div>
                                        <div class="stat-value">${metrics.total_subjects ?? 0}</div>
                                        <div class="stat-label">Subjects</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-green-light"><i class="fas fa-chalkboard-user"></i></div>
                                    <div>
                                        <div class="stat-value">${metrics.total_theory_hours ?? 0}</div>
                                        <div class="stat-label">Theory Hours/Wk</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-purple-light"><i class="fas fa-flask"></i></div>
                                    <div>
                                        <div class="stat-value">${metrics.total_lab_hours ?? 0}</div>
                                        <div class="stat-label">Lab Hours/Wk</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-orange-light"><i class="fas fa-user-check"></i></div>
                                    <div>
                                        <div class="stat-value">${metrics.assigned_faculty_count ?? 0}</div>
                                        <div class="stat-label">Faculty Assigned</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-3 mb-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-primary me-2"></i> Pre-Generation Analytics</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small">Theory Subjects</div>
                                    <div class="fw-bold">${metrics.theory_subjects ?? 0}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">Lab Subjects</div>
                                    <div class="fw-bold">${metrics.lab_subjects ?? 0}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">Avg Hours / Subject</div>
                                    <div class="fw-bold">${metrics.average_hours_per_subject ?? 0}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-users text-primary me-2"></i> Predicted Faculty Load</h6>
                            <div class="table-responsive">
                                <table class="table table-modern table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Faculty</th>
                                            <th>Weekly Hours</th>
                                            <th>Est. Daily Load</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    if (facultyLoad.length > 0) {
                        facultyLoad.forEach(item => {
                            html += `
                                <tr>
                                    <td>${item.faculty_name}</td>
                                    <td>${item.weekly_hours} hrs</td>
                                    <td>${item.estimated_daily_load} hrs/day</td>
                                </tr>
                            `;
                        });
                    } else {
                        html += '<tr><td colspan="3" class="text-muted text-center">No faculty load forecast available yet.</td></tr>';
                    }

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Hours/Wk</th>
                                        <th>Assign Faculty</th>
                                        <th>Preferred Classroom</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.subjects.forEach(subject => {
                        const isLab = subject.subject_type.toLowerCase().includes('lab');

                        let facultyOptions = '<option value="">-- Select Faculty --</option>';
                        data.faculties.forEach(fac => {
                            const selected = (subject.faculty_id == fac.id) ? 'selected' : '';
                            facultyOptions += `<option value="${fac.id}" ${selected}>${fac.faculty_name}</option>`;
                        });

                        let classroomOptions = '<option value="">Auto-Assign Room</option>';
                        data.classrooms.forEach(room => {
                            const isRoomLab = room.room_type.toLowerCase().includes('lab');
                            const label = isRoomLab ? '🧪 Lab' : '📖 Theory';
                            classroomOptions += `<option value="${room.id}">${room.room_number} (${label})</option>`;
                        });

                        html += `
                            <tr>
                                <td>
                                    <div class="fw-bold">${subject.subject_name}</div>
                                    <small class="text-muted">${subject.subject_code}</small>
                                </td>
                                <td>
                                    <span class="badge ${isLab ? 'bg-warning text-dark' : 'bg-success text-white'} px-2 py-1">
                                        ${isLab ? 'Lab' : 'Theory'}
                                    </span>
                                </td>
                                <td>${subject.hours_per_week} hrs</td>
                                <td>
                                    <select name="subject_faculties[${subject.id}]" class="form-select form-select-sm" style="min-width: 180px;" required>
                                        ${facultyOptions}
                                    </select>
                                </td>
                                <td>
                                    <select name="subject_classrooms[${subject.id}]" class="form-select form-select-sm" style="min-width: 180px;">
                                        ${classroomOptions}
                                    </select>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error(error);
                    container.innerHTML = '<div class="alert alert-danger">Error loading timetable options. Please try again.</div>';
                });
        }

        deptSelect.addEventListener('change', fetchPreviewData);
        semSelect.addEventListener('change', fetchPreviewData);
        document.querySelectorAll('input[name="working_days[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', fetchPreviewData);
        });

        if (deptSelect.value && semSelect.value) {
            fetchPreviewData();
        }
    });
</script>
@endsection
