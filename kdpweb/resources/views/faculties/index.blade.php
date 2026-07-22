<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculties</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; color: #1f2937; }
        .page-shell { min-height: 100vh; padding: 2rem; }
        .page-header, .faculty-form, .faculty-table { max-width: 1280px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
        .back-link { color: #1f3c88; font-weight: 600; text-decoration: none; }
        h1 { margin: 0 0 0.35rem; color: #111827; }
        .subtitle { margin: 0; color: #64748b; }
        .faculty-form, .faculty-table { background: white; border-radius: 12px; box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08); }
        .faculty-form { padding: 1.25rem; margin-bottom: 1.25rem; }
        form { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; align-items: end; }
        label { display: block; margin-bottom: 0.4rem; color: #374151; font-size: 0.88rem; font-weight: 700; }
        input, select { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 0.7rem 0.8rem; font: inherit; }
        input:focus, select:focus { outline: 2px solid rgba(59, 130, 246, 0.25); border-color: #3b82f6; }
        button { border: 0; border-radius: 7px; padding: 0.7rem 0.9rem; background: #1f3c88; color: white; font: inherit; font-weight: 700; cursor: pointer; }
        .secondary-btn { background: #64748b; }
        .faculty-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th, td { padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; color: #475569; font-size: 0.8rem; letter-spacing: 0.04em; text-transform: uppercase; }
        .actions { display: flex; gap: 0.5rem; }
        .feedback { max-width: 1280px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #dcfce7; color: #166534; }
        .validation-errors { max-width: 1280px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #fee2e2; color: #991b1b; }
        @media (max-width: 900px) { form { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 700px) { .page-shell { padding: 1rem; } form { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <div>
                <h1>Manage Faculties</h1>
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

        <section class="faculty-form">
            <form action="{{ $editingFaculty ? route('faculties.update', $editingFaculty) : route('faculties.store') }}" method="POST">
                @csrf
                @if ($editingFaculty)
                    @method('PATCH')
                @endif
                <div>
                    <label for="faculty_name">Faculty name</label>
                    <input id="faculty_name" name="faculty_name" value="{{ old('faculty_name', $editingFaculty?->faculty_name) }}" required>
                </div>
                <div>
                    <label for="faculty_id">Faculty ID</label>
                    <input id="faculty_id" name="faculty_id" value="{{ old('faculty_id', $editingFaculty?->faculty_id) }}" required>
                </div>
                <div>
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select department</option>
                        @foreach ($departmentOptions as $departmentOption)
                            <option value="{{ $departmentOption }}" {{ old('department_id', $editingFaculty?->department?->department_name) == $departmentOption ? 'selected' : '' }}>{{ $departmentOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $editingFaculty?->email) }}" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" {{ $editingFaculty ? '' : 'required' }}>
                </div>
                <div>
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" value="{{ old('subject', $editingFaculty?->subject) }}" required>
                </div>
                <div>
                    <label for="availability">Availability</label>
                    <input id="availability" name="availability" value="{{ old('availability', $editingFaculty?->availability) }}" required>
                </div>
                <div>
                    <button type="submit">{{ $editingFaculty ? 'Update Faculty' : 'Add Faculty' }}</button>
                    @if ($editingFaculty)
                        <a href="{{ route('faculties.index') }}" style="display:inline-block;margin-top:0.5rem;"><button type="button" class="secondary-btn">Cancel</button></a>
                    @endif
                </div>
            </form>
        </section>

        <section class="faculty-table">
            <table>
                <thead>
                    <tr>
                        <th>Faculty name</th>
                        <th>Faculty ID</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faculties as $faculty)
                        <tr>
                            <td>{{ $faculty->faculty_name }}</td>
                            <td>{{ $faculty->faculty_id }}</td>
                            <td>{{ $faculty->department?->department_name }}</td>
                            <td>{{ $faculty->email }}</td>
                            <td>{{ $faculty->subject }}</td>
                            <td>{{ $faculty->availability }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('faculties.index', ['edit' => $faculty->id]) }}"><button type="button" class="secondary-btn">Edit</button></a>
                                    <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" onsubmit="return confirm('Delete this faculty?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
