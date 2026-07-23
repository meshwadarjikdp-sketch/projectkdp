@extends('layouts.admin')

@section('title', 'Manage Faculties')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Manage Faculties</h1>
            <p class="subtitle">Faculty staff records and availability</p>
        </div>
        <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
    </header>

    @if (session('success'))
        <div class="feedback">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="validation-errors">
            <strong>Please correct the faculty details.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel-card">
        <form action="{{ $editingFaculty ? route('faculties.update', $editingFaculty) : route('faculties.store') }}" method="POST" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;align-items:end;">
            @csrf
            @if ($editingFaculty)
                @method('PATCH')
            @endif
            <div>
                <label for="faculty_name" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Faculty name</label>
                <input id="faculty_name" name="faculty_name" value="{{ old('faculty_name', $editingFaculty?->faculty_name) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="faculty_id" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Faculty ID</label>
                <input id="faculty_id" name="faculty_id" value="{{ old('faculty_id', $editingFaculty?->faculty_id) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="department_id" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Department</label>
                <select id="department_id" name="department_id" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
                    <option value="">Select department</option>
                    @foreach ($departmentOptions as $departmentOption)
                        <option value="{{ $departmentOption }}" {{ old('department_id', $editingFaculty?->department?->department_name) == $departmentOption ? 'selected' : '' }}>{{ $departmentOption }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="email" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $editingFaculty?->email) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="password" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Password</label>
                <input id="password" type="password" name="password" {{ $editingFaculty ? '' : 'required' }} style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="subject" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Subject</label>
                <input id="subject" name="subject" value="{{ old('subject', $editingFaculty?->subject) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="availability" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Availability</label>
                <input id="availability" name="availability" value="{{ old('availability', $editingFaculty?->availability) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#1f3c88;color:white;font:inherit;font-weight:700;cursor:pointer;">{{ $editingFaculty ? 'Update Faculty' : 'Add Faculty' }}</button>
                @if ($editingFaculty)
                    <a href="{{ route('faculties.index') }}" style="display:inline-block;"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Cancel</button></a>
                @endif
            </div>
        </form>
    </section>

    <section class="panel-card" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Faculty name</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Faculty ID</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Department</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Email</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Subject</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Availability</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faculties as $faculty)
                    <tr>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->faculty_name }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->faculty_id }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->department?->department_name }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->email }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->subject }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $faculty->availability }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">
                            <div style="display:flex;gap:0.5rem;">
                                <a href="{{ route('faculties.index', ['edit' => $faculty->id]) }}"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Edit</button></a>
                                <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" onsubmit="return confirm('Delete this faculty?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#1f3c88;color:white;font:inherit;font-weight:700;cursor:pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
