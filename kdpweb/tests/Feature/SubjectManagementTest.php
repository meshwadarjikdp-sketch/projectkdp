<?php

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;

it('allows an authenticated admin to create, update, and delete subjects', function () {
    $admin = User::factory()->create();
    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Ada Lovelace',
    ]);
    $faculty = Faculty::create([
        'faculty_name' => 'Dr. John Doe',
        'faculty_id' => 'FAC-001',
        'department_id' => $department->id,
        'email' => 'john@example.com',
        'password' => 'secret123',
        'subject' => 'Algorithms',
        'availability' => 'Mon/Wed',
    ]);

    $this->actingAs($admin)
        ->post(route('subjects.store'), [
            'subject_code' => 'CS101',
            'subject_name' => 'Algorithms',
            'department_id' => $department->id,
            'semester' => 3,
            'faculty_id' => $faculty->id,
            'credits' => 3,
            'hours_per_week' => 4,
            'subject_type' => 'Theory',
            'elective' => 1,
            'status' => 'Active',
        ])
        ->assertRedirect(route('subjects.index'))
        ->assertSessionHas('success', 'Subject added successfully.');

    $this->assertDatabaseHas('subjects', [
        'subject_code' => 'CS101',
        'subject_name' => 'Algorithms',
        'status' => 'Active',
    ]);

    $subject = Subject::where('subject_code', 'CS101')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('subjects.index'))
        ->assertOk()
        ->assertSee('Manage Subjects')
        ->assertSee('Add Subject')
        ->assertSee('CS101')
        ->assertSee('Algorithms');

    $this->actingAs($admin)
        ->patch(route('subjects.update', $subject), [
            'subject_code' => 'CS101',
            'subject_name' => 'Advanced Algorithms',
            'department_id' => $department->id,
            'semester' => 4,
            'faculty_id' => $faculty->id,
            'credits' => 4,
            'hours_per_week' => 5,
            'subject_type' => 'Lab',
            'elective' => 0,
            'status' => 'Inactive',
        ])
        ->assertRedirect(route('subjects.index'));

    $subject->refresh();
    expect($subject->subject_name)->toBe('Advanced Algorithms');
    expect($subject->status)->toBe('Inactive');

    $this->actingAs($admin)
        ->delete(route('subjects.destroy', $subject))
        ->assertRedirect(route('subjects.index'));

    $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
});
