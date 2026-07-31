<?php

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;

it('returns analytics and predicted faculty load for timetable preview', function () {
    $user = User::factory()->create();
    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Rao',
    ]);

    $faculty = Faculty::create([
        'faculty_id' => 'FAC001',
        'faculty_name' => 'Prof. Sharma',
        'department_id' => $department->id,
        'email' => 'sharma@example.com',
        'password' => bcrypt('secret'),
        'subject' => 'Computer Science',
        'availability' => 'Available',
    ]);

    Classroom::create([
        'room_number' => '101',
        'capacity' => 60,
        'room_type' => 'Lecture Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    Classroom::create([
        'room_number' => 'LAB1',
        'capacity' => 30,
        'room_type' => 'Computer Lab',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    Subject::create([
        'subject_code' => 'CS101',
        'subject_name' => 'Data Structures',
        'department_id' => $department->id,
        'semester' => 3,
        'faculty_id' => $faculty->id,
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
        'faculty_id' => $faculty->id,
        'credits' => 2,
        'hours_per_week' => 2,
        'subject_type' => 'Lab',
        'elective' => false,
        'status' => 'Active',
    ]);

    $response = $this->actingAs($user)->getJson('/timetables/preview?department_id='.$department->id.'&semester=3');

    $response->assertOk();
    $response->assertJsonPath('analytics.subject_metrics.total_subjects', 2);
    $response->assertJsonPath('analytics.subject_metrics.total_theory_hours', 4);
    $response->assertJsonPath('analytics.subject_metrics.total_lab_hours', 2);
    $response->assertJsonPath('analytics.predicted_faculty_load.'.$faculty->id.'.weekly_hours', 6);
    $response->assertJsonPath('analytics.predicted_faculty_load.'.$faculty->id.'.estimated_daily_load', 2);
});
