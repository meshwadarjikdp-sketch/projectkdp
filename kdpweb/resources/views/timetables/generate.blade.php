@extends('layouts.admin')

@section('title', 'AI Timetable Generator')

@section('content')
<style>
    .card-modern {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .bg-gradient-blue {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .text-blue { color: #1e3c72; }
    .form-label { font-weight: 600; color: #495057; }
    .btn-ai {
        background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 99%, #FECFEF 100%);
        color: #333;
        font-weight: bold;
        border: none;
    }
    .btn-ai:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }
    #loadingOverlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .spinner {
        width: 60px;
        height: 60px;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #1e3c72;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div id="loadingOverlay">
    <div class="spinner mb-3"></div>
    <h3 class="text-blue">AI is Generating Timetable...</h3>
    <p class="text-muted">Strictly validating Faculty limits (max 2 hrs/day), Continuous Labs, and Classroom Availability.</p>
</div>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-blue fw-bold mb-0">Generate Timetable</h2>
            <p class="text-muted mb-0">Strict AI-Powered Scheduling Module</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('timetables.generate') }}" method="POST" id="generateForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-modern mb-4">
                    <div class="card-header bg-gradient-blue text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Base Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    @for($i=1; $i<=8; $i++)
                                        <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Division</label>
                                <select name="division" class="form-select" required>
                                    <option value="A" {{ old('division') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('division') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ old('division') == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Academic Year</label>
                                <select name="academic_year" class="form-select" required>
                                    <option value="2026-2027" {{ old('academic_year') == '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                                    <option value="2027-2028" {{ old('academic_year') == '2027-2028' ? 'selected' : '' }}>2027-2028</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-blue fw-bold mb-3">Time Slot Configuration</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Total Slots per Day (Max hours)</label>
                                <input type="number" name="total_slots" class="form-control" value="{{ old('total_slots', 6) }}" min="3" max="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lunch Break Slot Index (Fixed)</label>
                                <input type="number" name="lunch_slot" class="form-control" value="{{ old('lunch_slot', 4) }}" min="1" max="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lecture Time Slots</label>
                                <select name="lecture_slots[]" class="form-select" multiple size="4">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ in_array($i, old('lecture_slots', [])) ? 'selected' : '' }}>Slot {{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple lecture slots.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Laboratory Time Slots</label>
                                <select name="lab_slots[]" class="form-select" multiple size="4">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ in_array($i, old('lab_slots', [])) ? 'selected' : '' }}>Slot {{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Choose the one-hour starting slots for lab sessions.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Working Days</label><br>
                                @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                                @foreach($days as $day)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="working_days[]" value="{{ $day }}" id="day_{{ $day }}" 
                                        {{ (is_array(old('working_days')) && in_array($day, old('working_days'))) || empty(old('working_days')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $day }}">{{ substr($day, 0, 3) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12">
                                <div id="previewPanel" class="card card-modern p-3 mt-3" style="display:none;">
                                    <h6 class="mb-2">Selected Department / Semester Preview</h6>
                                    <div id="previewContent" class="small text-muted">Select a department and semester to load subject, faculty, and classroom summary.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-modern mb-4">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-brain me-2"></i> Strict AI Rules</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2 small shadow-sm mb-3">
                            <strong>Note:</strong> All rules below will be strictly applied. If any rule fails, generation will fail.
                        </div>
                        <ul class="list-group list-group-flush mb-3 small">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Max Faculty Load = 2 Hrs/Day</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Labs = Exactly 2 Continuous Hrs</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Theory = 1 Hr/Day Max</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Fixed Lunch Break</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Faculty Match Department</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Zero Faculty/Room Clashes</li>
                        </ul>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill" onclick="showLoading()">
                                <i class="fas fa-cogs me-1"></i> Generate Strict Timetable
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function showLoading() {
        const form = document.getElementById('generateForm');
        if(form.checkValidity()) {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
    }

    function fetchPreview() {
        const departmentId = document.querySelector('select[name="department_id"]').value;
        const semester = document.querySelector('select[name="semester"]').value;
        const previewPanel = document.getElementById('previewPanel');
        const previewContent = document.getElementById('previewContent');

        if (!departmentId || !semester) {
            previewPanel.style.display = 'none';
            return;
        }

        previewPanel.style.display = 'block';
        previewContent.textContent = 'Loading preview...';

        fetch(`{{ route('timetables.preview') }}?department_id=${departmentId}&semester=${semester}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                previewContent.innerHTML = `<span class="text-danger">${data.message}</span>`;
                return;
            }

            const subjectRows = data.subjects.map(subject => `<li>${subject.subject_code} - ${subject.subject_name} (${subject.subject_type}, ${subject.hours_per_week}h) - ${subject.faculty_assigned ?? 'Unassigned'}</li>`).join('');
            previewContent.innerHTML = `
                <strong>Department:</strong> ${data.department}<br>
                <strong>Subjects:</strong> ${data.subject_count} <br>
                <strong>Faculty available:</strong> ${data.faculty_count} <br>
                <strong>Theory rooms:</strong> ${data.theory_room_count} <br>
                <strong>Lab rooms:</strong> ${data.lab_room_count} <br>
                <div style="margin-top:0.75rem;"><strong>Subjects loaded:</strong></div>
                <ul style="margin: 0.4rem 0 0 1rem;">${subjectRows}</ul>
            `;
        })
        .catch(() => {
            previewContent.innerHTML = '<span class="text-danger">Unable to load preview. Try again later.</span>';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const departmentSelect = document.querySelector('select[name="department_id"]');
        const semesterSelect = document.querySelector('select[name="semester"]');

        if (departmentSelect && semesterSelect) {
            departmentSelect.addEventListener('change', fetchPreview);
            semesterSelect.addEventListener('change', fetchPreview);
            fetchPreview();
        }
    });
