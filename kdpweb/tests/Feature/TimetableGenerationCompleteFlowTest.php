<?php

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;

it('generates a complete timetable with all constraints when generate button is clicked', function () {
    $user = User::factory()->create();
    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Rao',
    ]);

    // Create faculty members
    $fac1 = Faculty::create([
        'faculty_id' => 'FAC001',
        'faculty_name' => 'Prof. Sharma',
        'department_id' => $department->id,
        'email' => 'sharma@example.com',
        'password' => bcrypt('secret'),
        'subject' => 'Data Structures',
        'availability' => 'Available',
    ]);

    $fac2 = Faculty::create([
        'faculty_id' => 'FAC002',
        'faculty_name' => 'Prof. Patel',
        'department_id' => $department->id,
        'email' => 'patel@example.com',
        'password' => bcrypt('secret'),
        'subject' => 'Database Systems',
        'availability' => 'Available',
    ]);

    // Create theory and lab classrooms
    Classroom::create([
        'room_number' => 'A101',
        'capacity' => 60,
        'room_type' => 'Lecture Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    Classroom::create([
        'room_number' => 'A102',
        'capacity' => 60,
        'room_type' => 'Theory Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    Classroom::create([
        'room_number' => 'LAB1',
        'capacity' => 30,
        'room_type' => 'Computer Lab',
        'floor' => 2,
        'availability' => 'Available',
    ]);

    Classroom::create([
        'room_number' => 'LAB2',
        'capacity' => 30,
        'room_type' => 'Computer Lab',
        'floor' => 2,
        'availability' => 'Available',
    ]);

    // Create subjects
    Subject::create([
        'subject_code' => 'CS101',
        'subject_name' => 'Data Structures',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => $fac1->id,
        'credits' => 4,
        'hours_per_week' => 4,
        'subject_type' => 'Theory',
        'elective' => false,
        'status' => 'Active',
    ]);

    Subject::create([
        'subject_code' => 'CS102',
        'subject_name' => 'DBMS Lab',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => $fac2->id,
        'credits' => 2,
        'hours_per_week' => 2,
        'subject_type' => 'Lab',
        'elective' => false,
        'status' => 'Active',
    ]);

    Subject::create([
        'subject_code' => 'CS103',
        'subject_name' => 'Database Systems',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => $fac2->id,
        'credits' => 4,
        'hours_per_week' => 4,
        'subject_type' => 'Theory',
        'elective' => false,
        'status' => 'Active',
    ]);

    // Submit the form to generate timetable
    $response = $this->actingAs($user)->post(route('timetables.generate'), [
        'department_id' => $department->id,
        'semester' => 3,
        'division' => 'A',
        'academic_year' => '2026-2027',
        'total_slots' => 6,
        'lunch_slot' => 4,
        'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'lecture_slots' => [1, 2, 3, 5, 6],
        'lab_slots' => [1, 2, 3, 5, 6],
        'subject_faculties' => [
            // Faculty assignments already in subjects
        ],
        'subject_classrooms' => [
            // Auto-assign rooms
        ],
    ]);

    // Assert successful generation
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Timetable Generated Successfully! All constraints satisfied.');

    // Verify timetable entries exist
    $timetables = Timetable::where('department_id', $department->id)
        ->where('semester', 3)
        ->where('division', 'A')
        ->where('academic_year', '2026-2027')
        ->get();

    expect($timetables)->not()->toBeEmpty();
    expect($timetables->count())->toBeGreaterThan(0);

    // Verify constraints
    foreach ($timetables as $entry) {
        // Check lunch slot is not used
        expect($entry->slot_number)->not()->toBe(4);

        // Check slot is in allowed range
        expect($entry->slot_number)->toBeGreaterThanOrEqual(1);
        expect($entry->slot_number)->toBeLessThanOrEqual(6);

        // Check faculty exists
        expect($entry->faculty_id)->not()->toBeNull();

        // Check classroom exists
        expect($entry->classroom_id)->not()->toBeNull();

        // Check subject exists
        expect($entry->subject_id)->not()->toBeNull();
    }

    // Verify no faculty has more than 4 hours per day (one slot per faculty per day max)
    $facultyDailyLoads = $timetables->groupBy(['faculty_id', 'day_of_week'])->map(function ($dayGroup) {
        return $dayGroup->map(function ($entryGroup) {
            return $entryGroup->count(); // Count of slots per day
        });
    });

    foreach ($facultyDailyLoads as $facultyId => $days) {
        foreach ($days as $dayName => $slotCount) {
            expect($slotCount)->toBeLessThanOrEqual(4); // Max 4 hours per day
        }
    }

    // Verify no classroom has overlap on same day/slot
    $roomConflicts = $timetables->groupBy(['classroom_id', 'day_of_week', 'slot_number'])
        ->filter(fn ($group) => $group->count() > 1);

    expect($roomConflicts)->toBeEmpty();

    // Verify no faculty has overlap on same day/slot
    $facultyConflicts = $timetables->groupBy(['faculty_id', 'day_of_week', 'slot_number'])
        ->filter(fn ($group) => $group->count() > 1);

    expect($facultyConflicts)->toBeEmpty();
});
