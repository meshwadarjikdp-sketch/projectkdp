<?php

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('stores a student under the selected department and shows them in management view', function () {
    $admin = User::factory()->create();
    $department = Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Ada Lovelace',
    ]);

    $this->post(route('students.register'), [
        'student_name' => 'Asha Patel',
        'enrollment_number' => 'ENR-1001',
        'department_id' => $department->id,
        'semester' => 3,
        'class' => 'A',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('success', 'Student registered successfully.');

    $student = Student::where('enrollment_number', 'ENR-1001')->firstOrFail();

    expect($student->department_id)->toBe($department->id)
        ->and(Hash::check('secret123', $student->password))->toBeTrue();

    $this->actingAs($admin)
        ->get(route('students.index'))
        ->assertOk()
        ->assertSee('Asha Patel')
        ->assertSee('Computer Science')
        ->assertSee('Class A');
});
