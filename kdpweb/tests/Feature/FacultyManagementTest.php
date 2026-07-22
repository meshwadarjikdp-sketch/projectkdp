<?php

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows an authenticated admin to create, update, and delete faculties', function () {
    $admin = User::factory()->create();
    Department::create([
        'department_code' => 'CE',
        'department_name' => 'Computer Engineering',
        'hod_name' => 'Dr. Jane Smith',
    ]);

    $this->actingAs($admin)
        ->post(route('faculties.store'), [
            'faculty_name' => 'Dr. John Doe',
            'faculty_id' => 'FAC-001',
            'department_id' => 'Computer Engineering',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'subject' => 'Algorithms',
            'availability' => 'Mon/Wed 10:00-12:00',
        ])
        ->assertRedirect(route('faculties.index'));

    $this->assertDatabaseHas('faculties', [
        'faculty_id' => 'FAC-001',
        'faculty_name' => 'Dr. John Doe',
        'email' => 'john@example.com',
    ]);

    $faculty = Faculty::where('faculty_id', 'FAC-001')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('faculties.update', $faculty), [
            'faculty_name' => 'Dr. John Smith',
            'faculty_id' => 'FAC-001',
            'department_id' => 'Electrical Engineering',
            'email' => 'john.smith@example.com',
            'password' => 'new-secret',
            'subject' => 'Data Structures',
            'availability' => 'Tue/Thu 14:00-16:00',
        ])
        ->assertRedirect(route('faculties.index'));

    $faculty->refresh();
    expect($faculty->faculty_name)->toBe('Dr. John Smith');
    expect($faculty->email)->toBe('john.smith@example.com');
    expect(Hash::check('new-secret', $faculty->password))->toBeTrue();

    $this->actingAs($admin)
        ->delete(route('faculties.destroy', $faculty))
        ->assertRedirect(route('faculties.index'));

    $this->assertDatabaseMissing('faculties', ['id' => $faculty->id]);
});
