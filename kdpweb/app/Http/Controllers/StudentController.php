<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $departments = Department::orderBy('department_name')->get();
        $students = Student::with('department')->orderBy('student_name')->get();

        return view('students.index', compact('departments', 'students'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'enrollment_number' => ['required', 'string', 'max:50', 'unique:students,enrollment_number'],
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:6'],
            'class' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Student::create([
            'student_name' => $validated['student_name'],
            'enrollment_number' => $validated['enrollment_number'],
            'department_id' => $validated['department_id'],
            'semester' => $validated['semester'],
            'class' => $validated['class'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('success', 'Student registered successfully.');
    }
}
