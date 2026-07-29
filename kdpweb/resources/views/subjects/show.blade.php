@extends('layouts.admin')

@section('title', 'View Subject')

@section('content')
    <div style="max-width:900px;margin:1.25rem auto;">
        <a class="btn-secondary" href="{{ route('subjects.index') }}">&larr; Back</a>
        <h1 style="margin-top:1rem;">Subject Details</h1>
        <div style="background:white;padding:1rem;border-radius:10px;margin-top:0.75rem;box-shadow:0 8px 20px rgba(2,6,23,0.06);">
            <p><strong>Subject Code:</strong> {{ $subject->subject_code }}</p>
            <p><strong>Subject Name:</strong> {{ $subject->subject_name }}</p>
            <p><strong>Department:</strong> {{ $subject->department?->department_name }}</p>
            <p><strong>Semester:</strong> {{ $subject->semester }}</p>
            <p><strong>Faculty:</strong> {{ $subject->faculty?->faculty_name }}</p>
            <p><strong>Credits:</strong> {{ $subject->credits }}</p>
            <p><strong>Hours/Week:</strong> {{ $subject->hours_per_week }}</p>
            <p><strong>Type:</strong> {{ $subject->subject_type }}</p>
            <p><strong>Elective:</strong> {{ $subject->elective ? 'Yes' : 'No' }}</p>
            <p><strong>Status:</strong> {{ $subject->status }}</p>
            <div style="margin-top:0.75rem;display:flex;gap:0.6rem;">
                <a class="btn-primary" href="{{ route('subjects.edit', $subject) }}">Edit</a>
                <a class="btn-secondary" href="{{ route('subjects.index') }}">Close</a>
            </div>
        </div>
    </div>
@endsection
