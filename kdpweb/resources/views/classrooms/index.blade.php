<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classrooms</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; color: #1f2937; }
        .page-shell { min-height: 100vh; padding: 2rem; }
        .page-header, .classroom-form, .classroom-table { max-width: 1280px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem; }
        .back-link { color: #1f3c88; font-weight: 600; text-decoration: none; }
        h1 { margin: 0 0 0.35rem; color: #111827; }
        .subtitle { margin: 0; color: #64748b; }
        .classroom-form, .classroom-table { background: white; border-radius: 12px; box-shadow: 0 2px 14px rgba(15, 23, 42, 0.08); }
        .classroom-form { padding: 1.25rem; margin-bottom: 1.25rem; }
        form { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; align-items: end; }
        label { display: block; margin-bottom: 0.4rem; color: #374151; font-size: 0.88rem; font-weight: 700; }
        input, select { width: 100%; border: 1px solid #cbd5e1; border-radius: 7px; padding: 0.7rem 0.8rem; font: inherit; }
        input:focus, select:focus { outline: 2px solid rgba(59, 130, 246, 0.25); border-color: #3b82f6; }
        button { border: 0; border-radius: 7px; padding: 0.7rem 0.9rem; background: #1f3c88; color: white; font: inherit; font-weight: 700; cursor: pointer; }
        .secondary-btn { background: #64748b; }
        .classroom-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th, td { padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; color: #475569; font-size: 0.8rem; letter-spacing: 0.04em; text-transform: uppercase; }
        .actions { display: flex; gap: 0.5rem; }
        .feedback { max-width: 1280px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #dcfce7; color: #166534; }
        .validation-errors { max-width: 1280px; margin: 0 auto 1.25rem; padding: 0.85rem 1rem; border-radius: 7px; background: #fee2e2; color: #991b1b; }
        @media (max-width: 1000px) { form { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 700px) { .page-shell { padding: 1rem; } form { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <div>
                <h1>Manage Classrooms</h1>
                <p class="subtitle">Add, edit, and remove rooms for classes and labs</p>
            </div>
            <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
        </header>

        @if (session('success'))
            <div class="feedback">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="validation-errors">
                <strong>Please correct the classroom details.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="classroom-form">
            <form action="{{ $editingClassroom ? route('classrooms.update', $editingClassroom) : route('classrooms.store') }}" method="POST">
                @csrf
                @if ($editingClassroom)
                    @method('PATCH')
                @endif
                <div>
                    <label for="room_number">Room number</label>
                    <input id="room_number" name="room_number" value="{{ old('room_number', $editingClassroom?->room_number) }}" required>
                </div>
                <div>
                    <label for="capacity">Capacity</label>
                    <input id="capacity" type="number" name="capacity" value="{{ old('capacity', $editingClassroom?->capacity) }}" required>
                </div>
                <div>
                    <label for="room_type">Lab / Classroom</label>
                    <select id="room_type" name="room_type" required>
                        <option value="">Select type</option>
                        <option value="Classroom" {{ old('room_type', $editingClassroom?->room_type) == 'Classroom' ? 'selected' : '' }}>Classroom</option>
                        <option value="Lab" {{ old('room_type', $editingClassroom?->room_type) == 'Lab' ? 'selected' : '' }}>Lab</option>
                    </select>
                </div>
                <div>
                    <label for="floor">Floor</label>
                    <input id="floor" type="number" name="floor" value="{{ old('floor', $editingClassroom?->floor) }}" required>
                </div>
                <div>
                    <label for="availability">Availability</label>
                    <input id="availability" name="availability" value="{{ old('availability', $editingClassroom?->availability) }}" required>
                </div>
                <div>
                    <button type="submit">{{ $editingClassroom ? 'Update Classroom' : 'Add Classroom' }}</button>
                    @if ($editingClassroom)
                        <a href="{{ route('classrooms.index') }}" style="display:inline-block;margin-top:0.5rem;"><button type="button" class="secondary-btn">Cancel</button></a>
                    @endif
                </div>
            </form>
        </section>

        <section class="classroom-table">
            <table>
                <thead>
                    <tr>
                        <th>Room number</th>
                        <th>Capacity</th>
                        <th>Lab / Classroom</th>
                        <th>Floor</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classrooms as $classroom)
                        <tr>
                            <td>{{ $classroom->room_number }}</td>
                            <td>{{ $classroom->capacity }}</td>
                            <td>{{ $classroom->room_type }}</td>
                            <td>{{ $classroom->floor }}</td>
                            <td>{{ $classroom->availability }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('classrooms.index', ['edit' => $classroom->id]) }}"><button type="button" class="secondary-btn">Edit</button></a>
                                    <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Delete this classroom?');">
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
