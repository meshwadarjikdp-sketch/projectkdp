<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $editingDepartment = $request->query('edit')
            ? Department::query()->findOrFail((int) $request->query('edit'))
            : null;

        $search = trim((string) $request->query('search'));
        $departmentOptions = Department::query()->orderBy('department_name')->get();
        $departments = Department::query()
            ->when($search !== '', function ($query) use ($search): void {
                $term = "%{$search}%";

                $query->where(function ($query) use ($term): void {
                    $query->where('department_code', 'like', $term)
                        ->orWhere('department_name', 'like', $term)
                        ->orWhere('hod_name', 'like', $term);
                });
            })
            ->orderBy('department_name')
            ->get();

        return view('departments.index', compact('departments', 'departmentOptions', 'search', 'editingDepartment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $department = $request->validate([
            'department_code' => ['required', 'string', 'max:30', 'unique:departments,department_code'],
            'department_name' => ['required', 'string', 'max:255'],
            'hod_name' => ['required', 'string', 'max:255'],
        ]);

        Department::create($department);

        return to_route('departments.index')->with('success', 'Department added successfully.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $departmentData = $request->validate([
            'department_code' => ['required', 'string', 'max:30', Rule::unique('departments', 'department_code')->ignore($department->id)],
            'department_name' => ['required', 'string', 'max:255'],
            'hod_name' => ['required', 'string', 'max:255'],
        ]);

        $department->update($departmentData);

        return to_route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return to_route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
