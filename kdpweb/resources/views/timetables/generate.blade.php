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

    <!-- Sticky Filters & Action Bar -->
    <div class="sticky-action-bar">
        <div class="d-flex flex-wrap gap-3 align-items-center flex-grow-1">
            <select class="form-select" style="width: 200px;">
                <option>Computer Engineering</option>
                <option>Information Tech</option>
            </select>
            <select class="form-select" style="width: 140px;">
                <option>Semester 5</option>
                <option>Semester 6</option>
            </select>
            <select class="form-select" style="width: 140px;">
                <option>2026-2027</option>
            </select>
            <select class="form-select" style="width: 100px;">
                <option>Div A</option>
                <option>Div B</option>
            </select>
            <select class="form-select" style="width: 160px;">
                <option>Class Timetable</option>
                <option>Faculty Timetable</option>
            </select>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-premium btn-outline-soft"><i class="fas fa-save"></i> Save</button>
            <button class="btn btn-premium btn-outline-soft"><i class="fas fa-file-pdf text-danger"></i> PDF</button>
            <button class="btn btn-premium btn-outline-soft"><i class="fas fa-file-excel text-success"></i> Excel</button>
            <button class="btn btn-premium btn-outline-soft"><i class="fas fa-sync-alt"></i> Regenerate</button>
            <button class="btn btn-premium btn-primary-gradient"><i class="fas fa-magic"></i> Generate Timetable</button>
            <button class="btn btn-premium btn-outline-soft" style="background: #10b981; color: white; border: none;"><i class="fas fa-upload"></i> Publish</button>
        </div>
    </div>

    <div class="row gx-4">
        <!-- LEFT COLUMN: Configuration -->
        <div class="col-xl-8 col-lg-7">
            
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
                                <input class="form-check-input" type="checkbox" checked>
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

        <!-- RIGHT COLUMN: Analytics -->
        <div class="col-xl-4 col-lg-5">
            
            <!-- Summary Stats -->
            <div class="glass-card">
                <h5 class="fw-bold mb-4">Pre-Generation Analytics</h5>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card p-3">
                            <div class="stat-icon bg-blue-light"><i class="fas fa-book"></i></div>
                            <div>
                                <div class="stat-value">6</div>
                                <div class="stat-label">Subjects</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3">
                            <div class="stat-icon bg-purple-light"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div>
                                <div class="stat-value">22</div>
                                <div class="stat-label">Lectures</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3">
                            <div class="stat-icon bg-orange-light"><i class="fas fa-laptop-code"></i></div>
                            <div>
                                <div class="stat-value">4</div>
                                <div class="stat-label">Labs/Wk</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card p-3">
                            <div class="stat-icon bg-green-light"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="stat-value">98%</div>
                                <div class="stat-label">AI Score</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="stat-label">Faculty Utilization</span>
                        <span class="fw-bold" style="font-size:13px;">85%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 85%"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="stat-label">Classroom Utilization</span>
                        <span class="fw-bold" style="font-size:13px;">62%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 62%"></div>
                    </div>
                </div>
            </div>

            <!-- AI Status Checks -->
            <div class="glass-card">
                <h5 class="fw-bold mb-3">AI Diagnostic Status</h5>
                <ul class="ai-checklist">
                    <li><i class="fas fa-check-circle check-icon"></i> No Faculty Conflict Detected</li>
                    <li><i class="fas fa-check-circle check-icon"></i> No Classroom Conflict Detected</li>
                    <li><i class="fas fa-check-circle check-icon"></i> No Lab Conflict Detected</li>
                    <li><i class="fas fa-check-circle check-icon"></i> Balanced Subject Distribution</li>
                    <li><i class="fas fa-check-circle check-icon"></i> Faculty Load Optimized</li>
                    <li><i class="fas fa-star text-warning" style="font-size: 18px;"></i> Ready to Publish</li>
                </ul>
                <div class="mt-3 text-muted" style="font-size: 12px;">
                    <i class="fas fa-clock"></i> Generation Time: <strong>1.24s</strong>
                </div>
            </div>

            <!-- Faculty Load Table -->
            <div class="glass-card p-0 overflow-hidden">
                <div class="p-4 pb-2">
                    <h5 class="fw-bold mb-0">Predicted Faculty Load</h5>
                    <p class="text-muted small">Max 4 hours per day</p>
                </div>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="padding-left:24px;">Faculty</th>
                                <th>M</th>
                                <th>T</th>
                                <th>W</th>
                                <th>T</th>
                                <th>F</th>
                                <th>S</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding-left:24px; font-weight: 500;">Dr. Alan</td>
                                <td>3</td><td>2</td><td>3</td><td>2</td><td>3</td><td>0</td>
                                <td><span class="badge bg-light text-dark">13</span></td>
                            </tr>
                            <tr>
                                <td style="padding-left:24px; font-weight: 500;">Prof. Sarah</td>
                                <td>2</td><td>4</td><td>2</td><td>4</td><td>2</td><td>2</td>
                                <td><span class="badge bg-light text-dark">16</span></td>
                            </tr>
                            <tr>
                                <td style="padding-left:24px; font-weight: 500;">Dr. Smith</td>
                                <td><span class="text-danger-soft">5</span></td><td>2</td><td>3</td><td>2</td><td>2</td><td>0</td>
                                <td><span class="badge bg-danger">14</span></td>
                            </tr>
                            <tr>
                                <td style="padding-left:24px; font-weight: 500;">Prof. John</td>
                                <td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td>
                                <td><span class="badge bg-light text-dark">12</span></td>
                            </tr>
                            <tr>
                                <td style="padding-left:24px; font-weight: 500;">Prof. Mike</td>
                                <td>3</td><td>1</td><td>4</td><td>1</td><td>3</td><td>0</td>
                                <td><span class="badge bg-light text-dark">12</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Timetable Preview Grid Section -->
    <div class="mt-4 mb-5">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1"><i class="fas fa-calendar-alt text-primary me-2"></i> Generated Timetable Preview</h4>
                    <p class="text-muted small mb-0">Preview of the weekly schedule based on current AI configurations.</p>
                </div>
                <div>
                    <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Conflict-Free</span>
                </div>
            </div>
            <div class="p-4" style="overflow-x: auto;">
                <div class="timetable-preview" id="timetable-grid" style="position: relative; display: grid; grid-template-columns: 80px repeat(6, 1fr); grid-template-rows: 60px repeat(11, 100px); min-width: 900px; border-top: 1px solid #ECECEC; border-left: 1px solid #ECECEC; background: white; border-radius: 12px;">
                    
                    <!-- Headers -->
                    <div class="header-cell" style="grid-column: 1; grid-row: 1; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #64748b; background-color: #f8fafc; border-top-left-radius: 12px; z-index: 2;">Time</div>
                    <div class="header-cell" style="grid-column: 2; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; z-index: 2;">Monday</div>
                    <div class="header-cell" style="grid-column: 3; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; z-index: 2;">Tuesday</div>
                    <div class="header-cell" style="grid-column: 4; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; z-index: 2;">Wednesday</div>
                    <div class="header-cell" style="grid-column: 5; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; z-index: 2;">Thursday</div>
                    <div class="header-cell" style="grid-column: 6; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; z-index: 2;">Friday</div>
                    <div class="header-cell" style="grid-column: 7; grid-row: 1; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; color: #1e293b; background-color: #f8fafc; border-top-right-radius: 12px; z-index: 2;">Saturday</div>

                    <!-- Time Labels -->
                    <div class="time-label" style="grid-column: 1; grid-row: 2; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">08:00 AM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 3; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">09:00 AM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 4; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">10:00 AM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 5; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">11:00 AM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 6; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">12:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 7; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">01:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 8; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">02:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 9; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">03:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 10; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">04:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 11; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2;">05:00 PM</div>
                    <div class="time-label" style="grid-column: 1; grid-row: 12; font-weight: 600; font-size: 12px; color: #64748b; display: flex; align-items: center; justify-content: center; border-right: 1px solid #ECECEC; border-bottom: 1px solid #ECECEC; background-color: #fff; z-index: 2; border-bottom-left-radius: 12px;">06:00 PM</div>

                    <!-- Lunch Break Row -->
                    <div style="grid-column: 2 / 8; grid-row: 7; padding: 12px; z-index: 2;">
                        <div style="width: 100%; height: 100%; background-color: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: #16a34a; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); border: 1px dashed #bbf7d0;">
                            <i class="fas fa-utensils me-2"></i> Lunch Break
                        </div>
                    </div>

                    <!-- CSS for preview cards -->
                    <style>
                        .preview-card {
                            height: 100%; width: 100%; border-radius: 10px; padding: 12px;
                            display: flex; flex-direction: column; cursor: pointer;
                            transition: all 0.2s ease;
                            border: 1px solid rgba(255,255,255,0.5);
                        }
                        .preview-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
                        .pc-sub { font-weight: 700; font-size: 14px; color: #1e293b; margin-bottom: 4px; line-height: 1.2; }
                        .pc-fac { font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 2px; }
                        .pc-room { font-weight: 500; font-size: 12px; color: #64748b; }
                        .pc-time { font-weight: 600; font-size: 11px; color: #94a3b8; margin-top: auto; }
                        
                        .type-lecture { background-color: #e0f2fe; border-left: 4px solid #38bdf8; } /* Blue for Lecture */
                        .type-lab { background-color: #ffedd5; border-left: 4px solid #fb923c; } /* Orange for Lab */
                    </style>

                    <!-- Example Cards -->
                    <!-- Monday -->
                    <div style="grid-column: 2; grid-row: 2; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lecture">
                            <div class="pc-sub">Operating Systems</div>
                            <div class="pc-fac">Dr. Alan</div>
                            <div class="pc-room">Room 101</div>
                            <div class="pc-time">08:00 AM - 09:00 AM</div>
                        </div>
                    </div>
                    <div style="grid-column: 2; grid-row: 3; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lecture">
                            <div class="pc-sub">Data Structures</div>
                            <div class="pc-fac">Prof. Sarah</div>
                            <div class="pc-room">Room 102</div>
                            <div class="pc-time">09:00 AM - 10:00 AM</div>
                        </div>
                    </div>
                    <div style="grid-column: 2; grid-row: 4 / span 2; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lab">
                            <div class="pc-sub">Computer Networks Lab</div>
                            <div class="pc-fac">Dr. Smith</div>
                            <div class="pc-room">Lab 1</div>
                            <div class="pc-time">10:00 AM - 12:00 PM</div>
                        </div>
                    </div>
                    
                    <!-- Tuesday -->
                    <div style="grid-column: 3; grid-row: 2 / span 2; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lab">
                            <div class="pc-sub">Data Structures Lab</div>
                            <div class="pc-fac">Prof. Sarah</div>
                            <div class="pc-room">Lab 2</div>
                            <div class="pc-time">08:00 AM - 10:00 AM</div>
                        </div>
                    </div>
                    <div style="grid-column: 3; grid-row: 4; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lecture">
                            <div class="pc-sub">Database Systems</div>
                            <div class="pc-fac">Prof. Mike</div>
                            <div class="pc-room">Room 103</div>
                            <div class="pc-time">10:00 AM - 11:00 AM</div>
                        </div>
                    </div>
                    <div style="grid-column: 3; grid-row: 5; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lecture">
                            <div class="pc-sub">Operating Systems</div>
                            <div class="pc-fac">Dr. Alan</div>
                            <div class="pc-room">Room 101</div>
                            <div class="pc-time">11:00 AM - 12:00 PM</div>
                        </div>
                    </div>

                    <!-- Wednesday -->
                    <div style="grid-column: 4; grid-row: 5 / span 2; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lab">
                            <div class="pc-sub">Database Lab</div>
                            <div class="pc-fac">Prof. Mike</div>
                            <div class="pc-room">Lab 3</div>
                            <div class="pc-time">11:00 AM - 01:00 PM</div>
                        </div>
                    </div>
                    
                    <!-- Thursday -->
                    <div style="grid-column: 5; grid-row: 8 / span 2; padding: 8px; z-index: 2;">
                        <div class="preview-card type-lab">
                            <div class="pc-sub">Operating Systems Lab</div>
                            <div class="pc-fac">Dr. Alan</div>
                            <div class="pc-room">Lab 5</div>
                            <div class="pc-time">02:00 PM - 04:00 PM</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JS for Grid Borders
    document.addEventListener("DOMContentLoaded", () => {
        const gridContainer = document.getElementById('timetable-grid');
        for (let r = 2; r <= 12; r++) {
            for (let c = 2; c <= 7; c++) {
                const box = document.createElement('div');
                box.style.borderRight = '1px solid #ECECEC';
                box.style.borderBottom = '1px solid #ECECEC';
                box.style.gridColumn = c;
                box.style.gridRow = r;
                box.style.zIndex = 1;
                gridContainer.appendChild(box);
            }
        }
    });
</div>

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
@endsection
