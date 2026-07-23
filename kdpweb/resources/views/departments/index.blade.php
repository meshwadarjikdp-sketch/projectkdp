@extends('layouts.admin')

@section('title', 'Manage Departments')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Manage Departments</h1>
            <p class="subtitle">Academic departments</p>
        </div>
        <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
    </header>

    @if (session('success'))
        <div class="feedback">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="validation-errors">
            <strong>Please correct the department details.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel-card">
        <form action="{{ route('departments.index') }}" method="GET" style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:end;">
            <div>
                <label for="search" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Search department</label>
                <input id="search" name="search" value="{{ $search }}" list="department-options" placeholder="Select or type a department" style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
                <datalist id="department-options">
                    @foreach ($departmentOptions as $departmentOption)
                        <option value="{{ $departmentOption->department_code }}">{{ $departmentOption->department_name }}</option>
                        <option value="{{ $departmentOption->department_name }}">{{ $departmentOption->department_code }}</option>
                    @endforeach
                </datalist>
            </div>
            <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#334155;color:white;font:inherit;font-weight:700;cursor:pointer;">Search Department</button>
        </form>
    </section>

    <section class="panel-card">
        <form action="{{ route('departments.store') }}" method="POST" style="display:grid;grid-template-columns:repeat(3,1fr) auto;gap:1rem;align-items:end;">
            @csrf
            <div>
                <label for="department_code" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Department code</label>
                <input id="department_code" name="department_code" value="{{ old('department_code') }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="department_name" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Department name</label>
                <input id="department_name" name="department_name" value="{{ old('department_name') }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="hod_name" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">HOD name</label>
                <input id="hod_name" name="hod_name" value="{{ old('hod_name') }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#1f3c88;color:white;font:inherit;font-weight:700;cursor:pointer;">Add Department</button>
        </form>
    </section>

    <section class="panel-card" style="overflow-x:auto;">
        @if ($departments->isEmpty())
            <div style="padding:2rem;text-align:center;color:#64748b;">No departments match your search.</div>
        @else
            <table style="width:100%;border-collapse:collapse;min-width:560px;">
                <thead>
                    <tr>
                        <th style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Department code</th>
                        <th style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Department name</th>
                        <th style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">HOD name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $department->department_code }}</td>
                            <td style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $department->department_name }}</td>
                            <td style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $department->hod_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
