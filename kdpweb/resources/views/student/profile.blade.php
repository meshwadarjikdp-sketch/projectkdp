@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
    <div class="page-header">
        <h1>Update Profile</h1>
        <p>View and update your personal information.</p>
    </div>

    @if (session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Info Card -->
    <div class="card" style="margin-bottom:1.25rem;">
        <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1.5rem;">
            <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:white;flex-shrink:0;">
                {{ strtoupper(substr($student->student_name, 0, 1)) }}
            </div>
            <div>
                <div style="font-size:1.3rem;font-weight:800;color:#0f172a;">{{ $student->student_name }}</div>
                <div style="font-family:monospace;color:#6366f1;font-weight:600;font-size:0.95rem;margin-top:0.2rem;">{{ $student->enrollment_number }}</div>
                <div style="color:#64748b;font-size:0.88rem;margin-top:0.15rem;">
                    {{ $student->department?->department_name ?? '—' }} &nbsp;·&nbsp; Semester {{ $student->semester }} &nbsp;·&nbsp; Class {{ $student->class }}
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;padding:1rem;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
            <div>
                <div style="font-size:0.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Enrollment Number</div>
                <div style="font-family:monospace;font-weight:700;color:#1e1b4b;font-size:1rem;">{{ $student->enrollment_number }}</div>
            </div>
            <div>
                <div style="font-size:0.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Full Name</div>
                <div style="font-weight:600;color:#0f172a;">{{ $student->student_name }}</div>
            </div>
            <div>
                <div style="font-size:0.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Department</div>
                <div style="font-weight:600;color:#0f172a;">{{ $student->department?->department_name ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:0.78rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Semester & Class</div>
                <div style="font-weight:600;color:#0f172a;">Sem {{ $student->semester }}, Class {{ $student->class }}</div>
            </div>
        </div>
    </div>

    <!-- Update Form Card -->
    <div class="card">
        <div class="card-title">Edit Profile</div>
        <form action="{{ route('student.profile.update') }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
                <div class="form-group">
                    <label class="form-label" for="student_name">Full Name</label>
                    <input class="form-control" id="student_name" type="text" name="student_name"
                           value="{{ old('student_name', $student->student_name) }}" required>
                </div>
                <div class="form-group" style="align-self:end;">
                    <label class="form-label">Enrollment Number</label>
                    <input class="form-control" type="text" value="{{ $student->enrollment_number }}" disabled
                           style="background:#f1f5f9;color:#64748b;cursor:not-allowed;">
                    <small style="color:#64748b;font-size:0.8rem;">Enrollment number cannot be changed.</small>
                </div>
            </div>

            <div style="margin:0.5rem 0 1rem;border-top:1px solid #e2e8f0;padding-top:1rem;">
                <div style="font-weight:700;font-size:0.95rem;color:#0f172a;margin-bottom:0.75rem;">Change Password <span style="font-weight:400;color:#64748b;font-size:0.85rem;">(leave blank to keep current)</span></div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                    <div class="form-group">
                        <label class="form-label" for="password">New Password</label>
                        <input class="form-control" id="password" type="password" name="password" placeholder="Min 8 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk" style="margin-right:0.5rem;"></i> Save Changes
            </button>
        </form>
    </div>
@endsection
