<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function index(Request $request): View
    {
        $editingFaculty = $request->query('edit')
            ? Faculty::query()->findOrFail((int) $request->query('edit'))
            : null;

        $faculties = Faculty::query()
            ->with('department')
            ->orderBy('faculty_name')
            ->get();

        $departmentOptions = $this->departmentOptions();

        return view('faculties.index', compact('faculties', 'departmentOptions', 'editingFaculty'));
    }

    public function store(Request $request): RedirectResponse
    {
        $facultyData = $request->validate([
            'faculty_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'string', 'max:50', 'unique:faculties,faculty_id'],
            'department_id' => ['required'],
            'email' => ['required', 'email', 'max:255', 'unique:faculties,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'availability' => ['required', 'string', 'max:255'],
        ]);

        $facultyData['department_id'] = $this->resolveDepartmentId($facultyData['department_id']);
        $facultyData['password'] = Hash::make($facultyData['password']);

        Faculty::create($facultyData);

        return to_route('faculties.index')->with('success', 'Faculty added successfully.');
    }

    public function update(Request $request, Faculty $faculty): RedirectResponse
    {
        $facultyData = $request->validate([
            'faculty_name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'string', 'max:50', Rule::unique('faculties', 'faculty_id')->ignore($faculty->id)],
            'department_id' => ['required'],
            'email' => ['required', 'email', 'max:255', Rule::unique('faculties', 'email')->ignore($faculty->id)],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'availability' => ['required', 'string', 'max:255'],
        ]);

        $facultyData['department_id'] = $this->resolveDepartmentId($facultyData['department_id']);

        if ($request->filled('password')) {
            $facultyData['password'] = Hash::make($facultyData['password']);
        } else {
            unset($facultyData['password']);
        }

        $faculty->update($facultyData);

        return to_route('faculties.index')->with('success', 'Faculty updated successfully.');
    }

    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();

        return to_route('faculties.index')->with('success', 'Faculty deleted successfully.');
    }

    private function departmentOptions(): array
    {
        return [
            'Computer Engineering',
            'Civil Engineering',
            'Electrical Engineering',
            'Mechanical Engineering',
        ];
    }

    private function resolveDepartmentId(mixed $departmentIdentifier): int
    {
        if (is_numeric($departmentIdentifier)) {
            $department = Department::query()->find((int) $departmentIdentifier);

            if ($department) {
                return $department->id;
            }
        }

        $departmentName = (string) $departmentIdentifier;
        $department = Department::query()->where('department_name', $departmentName)->first();

        if ($department) {
            return $department->id;
        }

        $departmentCode = match ($departmentName) {
            'Computer Engineering' => 'CE',
            'Civil Engineering' => 'CIV',
            'Electrical Engineering' => 'EE',
            'Mechanical Engineering' => 'ME',
            default => 'ENG',
        };

        $department = Department::create([
            'department_code' => $departmentCode,
            'department_name' => $departmentName,
            'hod_name' => 'TBD',
        ]);

        return $department->id;
    }
}
