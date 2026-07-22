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
        .page-header, .toolbar, .department-table { max-width: 1180px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .back-link { color: #1f3c88; font-weight: 600; text-decoration: none; }
        h1 { margin: 0 0 0.35rem; color: #111827; }
        .subtitle { margin: 0; color: #64748b; }
        .toolbar, .department-table { background: white; border-radius: 12px; box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08); }
        .toolbar { display: grid; grid-template-columns: 1fr auto; gap: 1.25rem; padding: 1.25rem; margin-bottom: 1.25rem; }
        form { margin: 0; }
        label { display: block; margin-bottom: 0.4rem; color: #374151; font-size: 0.88rem; font-weight: 700; }
        input { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 0.7rem 0.8rem; font: inherit; }
        input:focus { outline: 2px solid rgba(59, 130, 246, 0.25); border-color: #3b82f6; }
        button { border: 0; border-radius: 7px; padding: 0.7rem 0.9rem; font: inherit; font-weight: 700; cursor: pointer; }
        .primary-button { align-self: end; background: #1f3c88; color: white; }
        .secondary-button { background: #e2e8f0; color: #1e293b; }
        .danger-button { background: #fee2e2; color: #b91c1c; }
        .department-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { padding: 1rem 1.25rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; color: #475569; font-size: 0.8rem; letter-spacing: 0.04em; text-transform: uppercase; }
        tr:last-child td { border-bottom: 0; }
        .actions { display: flex; gap: 0.5rem; }
        .edit-form { display: none; }
        .feedback { max-width: 1180px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #dcfce7; color: #166534; }
        .validation-errors { max-width: 1180px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #fee2e2; color: #991b1b; }
        .empty-state { padding: 2rem; text-align: center; color: #64748b; }
        @media (max-width: 700px) { .page-shell { padding: 1rem; } .page-header, .toolbar { display: block; } .back-link { display: inline-block; margin-top: 1rem; } .primary-button { width: 100%; margin-top: 1rem; } }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <div>
                <h1>Manage Departments</h1>
                <p class="subtitle">Add, edit, delete, and search academic departments.</p>
            </div>
            <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
        </header>

        @if (session('success'))
            <div class="feedback">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="validation-errors">
                <strong>Please correct the highlighted details.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="toolbar" aria-label="Department tools">
            <form class="search-form" action="{{ route('departments.index') }}" method="GET">
                <label for="search">Search departments</label>
                <input id="search" name="search" value="{{ $search }}" placeholder="Search by code, department name, or HOD name">
            </form>
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <label for="department_code">Department code</label>
                <input id="department_code" name="department_code" value="{{ old('department_code') }}" required>
                <label for="department_name">Department name</label>
                <input id="department_name" name="department_name" value="{{ old('department_name') }}" required>
                <label for="hod_name">HOD name</label>
                <input id="hod_name" name="hod_name" value="{{ old('hod_name') }}" required>
                <button class="primary-button" type="submit">Add Department</button>
            </form>
        </section>

        <section class="department-table">
            @if ($departments->isEmpty())
                <div class="empty-state">No departments match your search.</div>
            @else
                <table>
                    <thead>
                        <tr><th>Department code</th><th>Department name</th><th>HOD name</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($departments as $department)
                            <tr>
                                <td>{{ $department->department_code }}</td>
                                <td>{{ $department->department_name }}</td>
                                <td>{{ $department->hod_name }}</td>
                                <td class="actions">
                                    <button class="secondary-button" type="button" onclick="toggleEdit({{ $department->id }})">Edit</button>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-{{ $department->id }}" class="edit-form">
                                <td colspan="4">
                                    <form action="{{ route('departments.update', $department) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <label for="edit-code-{{ $department->id }}">Department code</label>
                                        <input id="edit-code-{{ $department->id }}" name="department_code" value="{{ $department->department_code }}" required>
                                        <label for="edit-name-{{ $department->id }}">Department name</label>
                                        <input id="edit-name-{{ $department->id }}" name="department_name" value="{{ $department->department_name }}" required>
                                        <label for="edit-hod-{{ $department->id }}">HOD name</label>
                                        <input id="edit-hod-{{ $department->id }}" name="hod_name" value="{{ $department->hod_name }}" required>
                                        <button class="primary-button" type="submit">Save Changes</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
    <script>
        function toggleEdit(id) {
            const row = document.getElementById('edit-' + id);
            row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
        }
    </script>
</body>
</html>
