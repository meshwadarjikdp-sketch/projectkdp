<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        $editingClassroom = $request->query('edit')
            ? Classroom::query()->findOrFail((int) $request->query('edit'))
            : null;

        $classrooms = Classroom::query()
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get();

        return view('classrooms.index', compact('classrooms', 'editingClassroom'));
    }

    public function store(Request $request): RedirectResponse
    {
        $classroomData = $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:classrooms,room_number'],
            'capacity' => ['required', 'integer', 'min:1'],
            'room_type' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'integer', 'min:0'],
            'availability' => ['required', 'string', 'max:50'],
        ]);

        Classroom::create($classroomData);

        return to_route('classrooms.index')->with('success', 'Classroom added successfully.');
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $classroomData = $request->validate([
            'room_number' => ['required', 'string', 'max:50', Rule::unique('classrooms', 'room_number')->ignore($classroom->id)],
            'capacity' => ['required', 'integer', 'min:1'],
            'room_type' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'integer', 'min:0'],
            'availability' => ['required', 'string', 'max:50'],
        ]);

        $classroom->update($classroomData);

        return to_route('classrooms.index')->with('success', 'Classroom updated successfully.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();

        return to_route('classrooms.index')->with('success', 'Classroom deleted successfully.');
    }
}
