@extends('layouts.admin')

@section('title', 'Edit Subject')

@section('content')
    <div style="max-width:900px;margin:1.25rem auto;">
        <a class="btn-secondary" href="{{ route('subjects.index') }}">&larr; Back</a>
        <h1 style="margin-top:1rem;">Edit Subject</h1>

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

        <form action="{{ route('subjects.update', $subject) }}" method="POST" style="background:white;padding:1rem;border-radius:10px;margin-top:0.75rem;box-shadow:0 8px 20px rgba(2,6,23,0.06);">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem;">
                <div class="field">
                    <label for="subject_code">Subject Code</label>
                    <input id="subject_code" name="subject_code" value="{{ old('subject_code', $subject->subject_code) }}" required />
                </div>
                <div class="field">
                    <label for="subject_name">Subject Name</label>
                    <input id="subject_name" name="subject_name" value="{{ old('subject_name', $subject->subject_name) }}" required />
                </div>
                <div class="field">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $subject->department_id) == $department->id ? 'selected' : '' }}>{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="faculty_id">Assign Faculty</label>
                    <select id="faculty_id" name="faculty_id">
                        <option value="">Select faculty member</option>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" data-department-id="{{ $faculty->department_id }}" {{ old('faculty_id', $subject->faculty_id) == $faculty->id ? 'selected' : '' }}>{{ $faculty->faculty_name }} ({{ $faculty->department?->department_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">Select semester</option>
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semester', $subject->semester) == (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="field">
                    <label for="credits">Credits</label>
                    <input id="credits" name="credits" type="number" min="1" value="{{ old('credits', $subject->credits) }}" required />
                </div>
                <div class="field">
                    <label for="hours_per_week">Hours Per Week</label>
                    <input id="hours_per_week" name="hours_per_week" type="number" min="1" max="10" value="{{ old('hours_per_week', $subject->hours_per_week) }}" required />
                </div>
                <div class="field">
                    <label>Subject Type</label>
                    <div style="display:flex;gap:0.8rem;flex-wrap:wrap;margin-top:0.3rem;">
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Theory" {{ old('subject_type', $subject->subject_type) == 'Theory' ? 'checked' : '' }} /> Theory</label>
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Practical" {{ old('subject_type', $subject->subject_type) == 'Practical' ? 'checked' : '' }} /> Practical</label>
                        <label style="display:flex;align-items:center;gap:0.35rem;font-weight:600;color:#334155;"><input type="radio" name="subject_type" value="Lab" {{ old('subject_type', $subject->subject_type) == 'Lab' ? 'checked' : '' }} /> Lab</label>
                    </div>
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Active" {{ old('status', $subject->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $subject->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="field" style="grid-column: span 2; display:flex;align-items:center;gap:0.6rem;">
                    <label class="switch">
                        <input type="checkbox" name="elective" value="1" {{ old('elective', $subject->elective) ? 'checked' : '' }} />
                        <span class="slider"></span>
                    </label>
                    <span style="font-weight:700;color:#334155;">Elective</span>
                </div>
                <div style="grid-column: span 2; display:flex;gap:0.6rem;">
                    <button class="btn-primary" type="submit">Save Changes</button>
                    <a class="btn-secondary" href="{{ route('subjects.index') }}">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const departmentSelect = document.getElementById('department_id');
            const facultySelect = document.getElementById('faculty_id');

            if (!departmentSelect || !facultySelect) {
                return;
            }

            const facultyOptions = Array.from(facultySelect.querySelectorAll('option'));

            const filterFaculty = () => {
                const selectedDepartment = departmentSelect.value;
                const selectedValue = facultySelect.value;
                facultySelect.innerHTML = '';
                facultySelect.appendChild(new Option('Select faculty member', ''));

                facultyOptions.forEach(option => {
                    const deptId = option.dataset.departmentId;

                    if (!selectedDepartment || deptId === selectedDepartment) {
                        facultySelect.appendChild(option.cloneNode(true));
                    }
                });

                if (selectedValue) {
                    facultySelect.value = selectedValue;
                }
            };

            departmentSelect.addEventListener('change', filterFaculty);
            filterFaculty();
        });
    </script>
@endsection
