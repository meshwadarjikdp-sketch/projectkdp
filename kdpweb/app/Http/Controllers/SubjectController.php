<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = Subject::query()
            ->with(['department', 'faculty'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = "%{$request->input('search')}%";
                $query->where(function ($q) use ($term): void {
                    $q->where('subject_code', 'like', $term)
                        ->orWhere('subject_name', 'like', $term)
                        ->orWhereHas('department', function ($d) use ($term): void {
                            $d->where('department_name', 'like', $term);
                        })
                        ->orWhereHas('faculty', function ($f) use ($term): void {
                            $f->where('faculty_name', 'like', $term);
                        });
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request): void {
                $query->where('department_id', $request->input('department_id'));
            })
            ->when($request->filled('semester'), function ($query) use ($request): void {
                $query->where('semester', $request->input('semester'));
            })
            ->when($request->filled('faculty_id'), function ($query) use ($request): void {
                $query->where('faculty_id', $request->input('faculty_id'));
            })
            ->when($request->filled('subject_type'), function ($query) use ($request): void {
                $query->where('subject_type', $request->input('subject_type'));
            })
            ->orderBy('subject_code')
            ->get();

        $departments = Department::orderBy('department_name')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        $stats = [
            'total' => Subject::count(),
            'theory' => Subject::where('subject_type', 'Theory')->count(),
            'lab' => Subject::where('subject_type', 'Lab')->count(),
            'practical' => Subject::where('subject_type', 'Practical')->count(),
        ];

        return view('subjects.index', compact('subjects', 'departments', 'faculties', 'stats'));
    }

    public function show(Subject $subject): View
    {
        $subject->load(['department', 'faculty']);
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject): View
    {
        $departments = Department::orderBy('department_name')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();
        return view('subjects.edit', compact('subject', 'departments', 'faculties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:30', 'unique:subjects,subject_code'],
            'subject_name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:6'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'credits' => ['required', 'integer', 'min:1'],
            'hours_per_week' => ['required', 'integer', 'between:1,10'],
            'subject_type' => ['required', 'in:Theory,Practical,Lab'],
            'elective' => ['nullable', 'boolean'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        Subject::create($validated + ['elective' => (bool) $request->boolean('elective')]);

        return to_route('subjects.index')->with('success', 'Subject added successfully.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:30', Rule::unique('subjects', 'subject_code')->ignore($subject->id)],
            'subject_name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:6'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'credits' => ['required', 'integer', 'min:1'],
            'hours_per_week' => ['required', 'integer', 'between:1,10'],
            'subject_type' => ['required', 'in:Theory,Practical,Lab'],
            'elective' => ['nullable', 'boolean'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $subject->update($validated + ['elective' => (bool) $request->boolean('elective')]);

        return to_route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return to_route('subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
