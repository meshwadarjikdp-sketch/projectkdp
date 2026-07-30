<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Timetable;
use App\Services\TimetableGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public function create(Request $request): View
    {
        $departments = Department::orderBy('department_name')->get();

        return view('timetables.generate', [
            'departments' => $departments,
            'selectedDepartment' => $request->query('department_id', old('department_id')),
            'selectedSemester' => $request->query('semester', old('semester')),
        ]);
    }

    public function preview(Request $request)
    {
        $departmentId = $request->query('department_id');
        $semester = $request->query('semester');

        if (!$departmentId || !$semester) {
            return Response::json([
                'success' => false,
                'message' => 'Department and semester are required to preview timetable data.',
            ], 422);
        }

        $department = Department::find($departmentId);
        if (!$department) {
            return Response::json([
                'success' => false,
                'message' => 'Selected department was not found.',
            ], 404);
        }

        $subjects = Subject::with('faculty')
            ->where('department_id', $departmentId)
            ->where('semester', $semester)
            ->where('status', 'Active')
            ->orderBy('subject_code')
            ->get();

        $facultyCount = Faculty::where('department_id', $departmentId)->count();
        $availableClassrooms = Classroom::where('availability', 'Available')->get();
        $theoryRoomCount = $availableClassrooms->filter(fn ($room) => str_contains($room->room_type, 'Classroom') || str_contains($room->room_type, 'Theory') || str_contains($room->room_type, 'Lecture'))->count();
        $labRoomCount = $availableClassrooms->filter(fn ($room) => str_contains($room->room_type, 'Lab'))->count();

        return Response::json([
            'success' => true,
            'department' => $department->department_name,
            'subject_count' => $subjects->count(),
            'subjects' => $subjects->map(fn ($subject) => [
                'id' => $subject->id,
                'subject_code' => $subject->subject_code,
                'subject_name' => $subject->subject_name,
                'subject_type' => $subject->subject_type,
                'hours_per_week' => $subject->hours_per_week,
                'faculty_assigned' => $subject->faculty?->faculty_name,
            ]),
            'faculty_count' => $facultyCount,
            'theory_room_count' => $theoryRoomCount,
            'lab_room_count' => $labRoomCount,
        ]);
    }

    public function generate(Request $request, TimetableGenerator $generator)
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'division' => ['required', 'string', 'max:1'],
            'academic_year' => ['required', 'string', 'max:20'],
            'total_slots' => ['required', 'integer', 'min:3', 'max:10'],
            'lunch_slot' => ['required', 'integer', 'min:1', 'max:10'],
            'working_days' => ['required', 'array', 'min:3'],
            'working_days.*' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'lecture_slots' => ['nullable', 'array'],
            'lecture_slots.*' => ['integer', 'min:1', 'max:10'],
            'lab_slots' => ['nullable', 'array'],
            'lab_slots.*' => ['integer', 'min:1', 'max:10'],
        ]);

        $lectureSlots = array_values(array_unique(array_filter($request->input('lecture_slots', []), fn ($slot) => is_numeric($slot))));
        $labSlots = array_values(array_unique(array_filter($request->input('lab_slots', []), fn ($slot) => is_numeric($slot))));

        if (in_array($validated['lunch_slot'], $labSlots, true)) {
            return back()->withInput()->with('error', 'Lunch break cannot be selected as a laboratory slot.');
        }

        $result = $generator->generate(
            $validated['department_id'],
            $validated['semester'],
            $validated['division'],
            $validated['academic_year'],
            $validated['working_days'],
            $validated['total_slots'],
            $validated['lunch_slot'],
            $lectureSlots,
            $labSlots,
            true,
            true,
            true,
        );

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()->route('timetables.show', array_merge($validated, [
            'working_days' => $validated['working_days'],
            'lab_slots' => $labSlots,
        ]))->with('success', $result['message']);
    }

    public function show(Request $request): View
    {
        $departmentId = $request->query('department_id');
        $semester = $request->query('semester');
        $division = $request->query('division');
        $academicYear = $request->query('academic_year');

        $timetables = collect();
        $config = null;

        if ($departmentId && $semester && $division && $academicYear) {
            $timetables = Timetable::with(['subject', 'faculty', 'classroom'])
                ->where('department_id', $departmentId)
                ->where('semester', $semester)
                ->where('division', $division)
                ->where('academic_year', $academicYear)
                ->orderBy('day_of_week')
                ->orderBy('slot_number')
                ->get()
                ->groupBy('day_of_week');

            $config = (object) [
                'working_days' => $request->query('working_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
                'total_slots' => (int) $request->query('total_slots', 6),
                'lunch_slot' => (int) $request->query('lunch_slot', 4),
                'lab_slots' => array_map('intval', $request->query('lab_slots', [])),
            ];
        }

        return view('timetables.view', [
            'timetables' => $timetables,
            'config' => $config,
            'departmentId' => $departmentId,
            'semester' => $semester,
            'division' => $division,
            'academicYear' => $academicYear,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'division' => ['required', 'string', 'max:1'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        $department = Department::find($request->input('department_id'));
        $timetables = Timetable::with(['subject', 'faculty', 'classroom'])
            ->where('department_id', $request->input('department_id'))
            ->where('semester', $request->input('semester'))
            ->where('division', $request->input('division'))
            ->where('academic_year', $request->input('academic_year'))
            ->orderBy('day_of_week')
            ->orderBy('slot_number')
            ->get();

        $filename = sprintf('timetable_%s_sem%d_div%s_%s.csv',
            strtolower($department?->department_code ?? 'department'),
            $request->input('semester'),
            strtoupper($request->input('division')),
            str_replace([' ', '/'], ['_', '-'], $request->input('academic_year'))
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($timetables, $department) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Department', 'Semester', 'Division', 'Academic Year', 'Day', 'Slot', 'Subject Name', 'Faculty Name', 'Type', 'Classroom']);

            foreach ($timetables as $entry) {
                fputcsv($file, [
                    $department?->department_name,
                    $entry->semester,
                    $entry->division,
                    $entry->academic_year,
                    $entry->day_of_week,
                    $entry->slot_number,
                    $entry->subject?->subject_name,
                    $entry->faculty?->faculty_name,
                    $entry->subject?->subject_type,
                    $entry->classroom?->room_number,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'division' => ['required', 'string', 'max:1'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        $department = Department::find($request->input('department_id'));
        $timetables = Timetable::with(['subject', 'faculty', 'classroom'])
            ->where('department_id', $request->input('department_id'))
            ->where('semester', $request->input('semester'))
            ->where('division', $request->input('division'))
            ->where('academic_year', $request->input('academic_year'))
            ->orderBy('day_of_week')
            ->orderBy('slot_number')
            ->get();

        $filename = sprintf('timetable_%s_sem%d_div%s_%s.xls',
            strtolower($department?->department_code ?? 'department'),
            $request->input('semester'),
            strtoupper($request->input('division')),
            str_replace([' ', '/'], ['_', '-'], $request->input('academic_year'))
        );

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($timetables, $department) {
            echo '<table border="1">';
            echo '<tr><th>Department</th><th>Semester</th><th>Division</th><th>Academic Year</th><th>Day</th><th>Slot</th><th>Subject Name</th><th>Faculty Name</th><th>Type</th><th>Classroom</th></tr>';

            foreach ($timetables as $entry) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($department?->department_name ?? '', ENT_QUOTES, 'UTF-8').'</td>';
                echo '<td>'.$entry->semester.'</td>';
                echo '<td>'.$entry->division.'</td>';
                echo '<td>'.$entry->academic_year.'</td>';
                echo '<td>'.$entry->day_of_week.'</td>';
                echo '<td>'.$entry->slot_number.'</td>';
                echo '<td>'.htmlspecialchars($entry->subject?->subject_name ?? '', ENT_QUOTES, 'UTF-8').'</td>';
                echo '<td>'.htmlspecialchars($entry->faculty?->faculty_name ?? '', ENT_QUOTES, 'UTF-8').'</td>';
                echo '<td>'.htmlspecialchars($entry->subject?->subject_type ?? '', ENT_QUOTES, 'UTF-8').'</td>';
                echo '<td>'.htmlspecialchars($entry->classroom?->room_number ?? '', ENT_QUOTES, 'UTF-8').'</td>';
                echo '</tr>';
            }

            echo '</table>';
        };

        return Response::stream($callback, 200, $headers);
    }
}
