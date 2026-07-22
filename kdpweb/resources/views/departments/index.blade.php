<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; color: #1f2937; }
        .page-shell { min-height: 100vh; padding: 2rem; }
        .page-header, .search-department, .add-department, .department-table { max-width: 1180px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .back-link { color: #1f3c88; font-weight: 600; text-decoration: none; }
        h1 { margin: 0 0 0.35rem; color: #111827; }
        .subtitle { margin: 0; color: #64748b; }
        .search-department, .add-department, .department-table { background: white; border-radius: 12px; box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08); }
        .search-department { padding: 1.25rem; margin-bottom: 1.25rem; }
        .search-department form { display: grid; grid-template-columns: 1fr auto; align-items: end; gap: 1rem; }
        .search-department button { background: #334155; }
        .add-department, .department-table { background: white; border-radius: 12px; box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08); }
        .add-department { padding: 1.25rem; margin-bottom: 1.25rem; }
        form { display: grid; grid-template-columns: repeat(3, 1fr) auto; align-items: end; gap: 1rem; }
        label { display: block; margin-bottom: 0.4rem; color: #374151; font-size: 0.88rem; font-weight: 700; }
        input { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 0.7rem 0.8rem; font: inherit; }
        input:focus { outline: 2px solid rgba(59, 130, 246, 0.25); border-color: #3b82f6; }
        button { border: 0; border-radius: 7px; padding: 0.7rem 0.9rem; background: #1f3c88; color: white; font: inherit; font-weight: 700; cursor: pointer; }
        .department-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 560px; }
        th, td { padding: 1rem 1.25rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; color: #475569; font-size: 0.8rem; letter-spacing: 0.04em; text-transform: uppercase; }
        tr:last-child td { border-bottom: 0; }
        .feedback { max-width: 1180px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #dcfce7; color: #166534; }
        .validation-errors { max-width: 1180px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #fee2e2; color: #991b1b; }
        .empty-state { padding: 2rem; text-align: center; color: #64748b; }
        @media (max-width: 700px) { .page-shell { padding: 1rem; } .back-link { display: inline-block; margin-top: 1rem; } form { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <div>
                <h1>Manage Departments</h1>
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

        <section class="search-department">
            <form action="{{ route('departments.index') }}" method="GET">
                <div>
                    <label for="search">Search department</label>
                    <input id="search" name="search" value="{{ $search }}" list="department-options" placeholder="Select or type a department">
                    <datalist id="department-options">
                        @foreach ($departmentOptions as $departmentOption)
                            <option value="{{ $departmentOption->department_code }}">{{ $departmentOption->department_name }}</option>
                            <option value="{{ $departmentOption->department_name }}">{{ $departmentOption->department_code }}</option>
                        @endforeach
                    </datalist>
                </div>
                <button type="submit">Search Department</button>
            </form>
        </section>

        <section class="add-department">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div>
                    <label for="department_code">Department code</label>
                    <input id="department_code" name="department_code" value="{{ old('department_code') }}" required>
                </div>
                <div>
                    <label for="department_name">Department name</label>
                    <input id="department_name" name="department_name" value="{{ old('department_name') }}" required>
                </div>
                <div>
                    <label for="hod_name">HOD name</label>
                    <input id="hod_name" name="hod_name" value="{{ old('hod_name') }}" required>
                </div>
                <button type="submit">Add Department</button>
            </form>
        </section>

        <section class="department-table">
            @if ($departments->isEmpty())
                <div class="empty-state">No departments match your search.</div>
            @else
                <table>
                    <thead>
                        <tr><th>Department code</th><th>Department name</th><th>HOD name</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($departments as $department)
                            <tr>
                                <td>{{ $department->department_code }}</td>
                                <td>{{ $department->department_name }}</td>
                                <td>{{ $department->hod_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
</body>
</html>
