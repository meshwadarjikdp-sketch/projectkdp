<?php

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;

it('returns subjects, faculties, and classrooms via the preview endpoint', function () {
    $admin = User::factory()->create();
    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. HOD',
    ]);

    $faculty = Faculty::create([
        'faculty_name' => 'Dr. Jane',
        'faculty_id' => 'FAC-100',
        'department_id' => $department->id,
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'subject' => 'Algorithms',
        'availability' => 'Full',
    ]);

    $subject = Subject::create([
        'subject_code' => 'CS102',
        'subject_name' => 'Data Structures',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => $faculty->id,
        'credits' => 3,
        'hours_per_week' => 4,
        'subject_type' => 'Theory',
        'elective' => 0,
        'status' => 'Active',
    ]);

    $classroom = Classroom::create([
        'room_number' => '104',
        'capacity' => 50,
        'room_type' => 'Theory Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    $response = $this->actingAs($admin)->getJson("/timetables/preview?department_id={$department->id}&semester=3");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonFragment(['subject_code' => 'CS102'])
        ->assertJsonFragment(['faculty_name' => 'Dr. Jane'])
        ->assertJsonFragment(['room_number' => '104']);
});

it('updates faculty assignments and restricts generation to mapped classrooms', function () {
    $admin = User::factory()->create();

    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. HOD',
    ]);

    $faculty = Faculty::create([
        'faculty_name' => 'Dr. Jane',
        'faculty_id' => 'FAC-100',
        'department_id' => $department->id,
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'subject' => 'Algorithms',
        'availability' => 'Full',
    ]);

    $subject = Subject::create([
        'subject_code' => 'CS102',
        'subject_name' => 'Data Structures',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => null, // initially null
        'credits' => 3,
        'hours_per_week' => 4,
        'subject_type' => 'Theory',
        'elective' => 0,
        'status' => 'Active',
    ]);

    $classroom = Classroom::create([
        'room_number' => '104',
        'capacity' => 50,
        'room_type' => 'Theory Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    // Send generation request mapping the faculty and the preferred classroom
    $response = $this->actingAs($admin)->post('/timetables/generate', [
        'department_id' => $department->id,
        'semester' => 3,
        'division' => 'A',
        'academic_year' => '2026-2027',
        'total_slots' => 6,
        'lunch_slot' => 4,
        'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'subject_faculties' => [
            $subject->id => $faculty->id,
        ],
        'subject_classrooms' => [
            $subject->id => $classroom->id,
        ],
    ]);

    // Should redirect to timetable show view
    $response->assertRedirect();

    // Subject's faculty in DB should be updated
    $this->assertDatabaseHas('subjects', [
        'id' => $subject->id,
        'faculty_id' => $faculty->id,
    ]);

    // Scheduled timetable should strictly use the manual classroom
    $this->assertDatabaseHas('timetables', [
        'department_id' => $department->id,
        'semester' => 3,
        'division' => 'A',
        'subject_id' => $subject->id,
        'classroom_id' => $classroom->id,
    ]);
});

it('fails generation if an active subject is not assigned a faculty', function () {
    $admin = User::factory()->create();

    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. HOD',
    ]);

    $subject = Subject::create([
        'subject_code' => 'CS102',
        'subject_name' => 'Data Structures',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => null, // no faculty
        'credits' => 3,
        'hours_per_week' => 4,
        'subject_type' => 'Theory',
        'elective' => 0,
        'status' => 'Active',
    ]);

    $classroom = Classroom::create([
        'room_number' => '104',
        'capacity' => 50,
        'room_type' => 'Theory Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    $response = $this->actingAs($admin)->from('/timetables/generate')->post('/timetables/generate', [
        'department_id' => $department->id,
        'semester' => 3,
        'division' => 'A',
        'academic_year' => '2026-2027',
        'total_slots' => 6,
        'lunch_slot' => 4,
        'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'subject_faculties' => [
            $subject->id => '', // unassigned
        ],
    ]);

    $response->assertRedirect('/timetables/generate');
    $response->assertSessionHas('error');
    expect(session('error'))->toContain('must have an assigned faculty member');
});
