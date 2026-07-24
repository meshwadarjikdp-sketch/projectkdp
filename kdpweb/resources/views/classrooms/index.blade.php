@extends('layouts.admin')

@section('title', 'Manage Classrooms')

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

        .panel-card:nth-child(3) table {
            animation: fadeInScale 0.7s ease-out;
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
            <h1 class="page-title">Manage Classrooms</h1>
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

    <section class="panel-card">
        <form action="{{ $editingClassroom ? route('classrooms.update', $editingClassroom) : route('classrooms.store') }}" method="POST" style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:1rem;align-items:end;">
            @csrf
            @if ($editingClassroom)
                @method('PATCH')
            @endif
            <div>
                <label for="room_number" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Room number</label>
                <input id="room_number" name="room_number" value="{{ old('room_number', $editingClassroom?->room_number) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="capacity" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Capacity</label>
                <input id="capacity" type="number" name="capacity" value="{{ old('capacity', $editingClassroom?->capacity) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="room_type" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Lab / Classroom</label>
                <select id="room_type" name="room_type" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
                    <option value="">Select type</option>
                    @foreach (['Lab / Classroom', 'Staff Room', 'HOD Office', 'Classroom', 'Server Room', 'Electric Room', 'Seminar Room', 'Department Library', 'Conference Room'] as $roomType)
                        <option value="{{ $roomType }}" {{ old('room_type', $editingClassroom?->room_type) == $roomType ? 'selected' : '' }}>{{ $roomType }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="floor" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Floor</label>
                <input id="floor" type="number" name="floor" value="{{ old('floor', $editingClassroom?->floor) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div>
                <label for="availability" style="display:block;margin-bottom:0.4rem;color:#374151;font-size:0.88rem;font-weight:700;">Availability</label>
                <input id="availability" name="availability" value="{{ old('availability', $editingClassroom?->availability) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:0.7rem 0.8rem;font:inherit;">
            </div>
            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                <button type="submit" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#1f3c88;color:white;font:inherit;font-weight:700;cursor:pointer;">{{ $editingClassroom ? 'Update Classroom' : 'Add Classroom' }}</button>
                @if ($editingClassroom)
                    <a href="{{ route('classrooms.index') }}" style="display:inline-block;"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Cancel</button></a>
                @endif
            </div>
        </form>
    </section>

    <section class="panel-card" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Room number</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Capacity</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Lab / Classroom</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Floor</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Availability</th>
                    <th style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#475569;font-size:0.8rem;letter-spacing:0.04em;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classrooms as $classroom)
                    <tr>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $classroom->room_number }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $classroom->capacity }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $classroom->room_type }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $classroom->floor }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">{{ $classroom->availability }}</td>
                        <td style="padding:0.9rem 1rem;text-align:left;border-bottom:1px solid #e5e7eb;">
                            <div style="display:flex;gap:0.5rem;">
                                <a href="{{ route('classrooms.index', ['edit' => $classroom->id]) }}"><button type="button" style="border:0;border-radius:7px;padding:0.7rem 0.9rem;background:#64748b;color:white;font:inherit;font-weight:700;cursor:pointer;">Edit</button></a>
                                <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" onsubmit="return confirm('Delete this classroom?');">
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
