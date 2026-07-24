@extends('layouts.admin')

@section('title', 'Manage Departments')

@section('content')
    <style>
        form {
            animation: slideInUp 0.6s ease-out;
        }
        
        input:focus, select:focus {
            animation: pulse 0.3s ease-out;
        }

        button[type="submit"], button[type="button"] {
            animation: fadeInScale 0.5s ease-out;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(-1px);
        }

        tbody tr {
            animation: slideInUp 0.5s ease-out backwards;
            transition: all 0.3s ease;
        }

        tbody tr:nth-child(1) { animation-delay: 0.3s; }
        tbody tr:nth-child(2) { animation-delay: 0.35s; }
        tbody tr:nth-child(3) { animation-delay: 0.4s; }
        tbody tr:nth-child(4) { animation-delay: 0.45s; }
        tbody tr:nth-child(5) { animation-delay: 0.5s; }

        tbody tr:hover {
            background-color: #f0f7ff;
            box-shadow: inset 0 0 0 1px #dbeafe;
            transform: scale(1.01);
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0); }
        }
    </style>

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
        <form action="{{ $editingDepartment ? route('departments.update', $editingDepartment) : route('departments.store') }}" method="POST" style="display:grid;grid-template-columns:repeat(3,1fr) auto;gap:1rem;align-items:end;">
            @csrf
            @if ($editingDepartment)
                @method('PATCH')
            @endif
            <div>
                <label for="department_code" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Department code</label>
                <input id="department_code" name="department_code" value="{{ old('department_code', $editingDepartment?->department_code) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="department_name" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Department name</label>
                <input id="department_name" name="department_name" value="{{ old('department_name', $editingDepartment?->department_name) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="hod_name" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">HOD name</label>
                <input id="hod_name" name="hod_name" value="{{ old('hod_name', $editingDepartment?->hod_name) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#1f3c88;color:white;font:inherit;font-weight:700;cursor:pointer;">{{ $editingDepartment ? 'Update Department' : 'Add Department' }}</button>
                @if ($editingDepartment)
                    <a href="{{ route('departments.index') }}" style="display:inline-block;"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Cancel</button></a>
                @endif
            </div>
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
                        <th style="padding:1rem 1.25rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>{{ $department->department_code }}</td>
                            <td>{{ $department->department_name }}</td>
                            <td>{{ $department->hod_name }}</td>
                            <td>
                                <div style="display:flex;gap:0.5rem;">
                                    <a href="{{ route('departments.index', ['edit' => $department->id]) }}"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Edit</button></a>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Delete this department?');">
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
        @endif
    </section>
@endsection
