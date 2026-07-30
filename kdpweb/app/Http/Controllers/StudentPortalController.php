<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Student;
use App\Models\Timetable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    private function student(): Student
    {
        return Student::with('department')->findOrFail(session('student_id'));
    }

    public function dashboard(): View
    {
        $student = $this->student();

        return view('student.dashboard', compact('student'));
    }

    public function timetable(Request $request): View
    {
        $student = $this->student();

        $division = $request->query('division', 'A');
        $academicYear = $request->query('academic_year', date('Y').'-'.(date('Y') + 1));

        $timetables = Timetable::with(['subject', 'faculty', 'classroom'])
            ->where('department_id', $student->department_id)
            ->where('semester', $student->semester)
            ->where('division', $division)
            ->where('academic_year', $academicYear)
            ->orderBy('day_of_week')
            ->orderBy('slot_number')
            ->get()
            ->groupBy('day_of_week');

        $config = (object) [
            'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'total_slots' => 6,
            'lunch_slot' => 4,
            'lab_slots' => [],
        ];

        return view('student.timetable', compact('student', 'timetables', 'config', 'division', 'academicYear'));
    }

    public function notifications(): View
    {
        $student = $this->student();
        $notifications = Notification::orderByDesc('created_at')->get();

        return view('student.notifications', compact('student', 'notifications'));
    }

    public function profile(): View
    {
        $student = $this->student();

        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $student = $this->student();

        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $student->student_name = $validated['student_name'];

        if (! empty($validated['password'])) {
            $student->password = Hash::make($validated['password']);
        }

        $student->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
