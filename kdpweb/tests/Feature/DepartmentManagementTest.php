<?php

use App\Models\Department;
use App\Models\User;

it('allows an authenticated admin to add, search, edit, and delete departments', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('departments.store'), [
            'department_code' => 'CSE',
            'department_name' => 'Computer Science',
            'hod_name' => 'Dr. Ada Lovelace',
        ])
        ->assertRedirect(route('departments.index'))
        ->assertSessionHas('success', 'Department added successfully.');

    $department = Department::first();

    $this->actingAs($admin)
        ->get(route('departments.index', ['search' => 'Lovelace']))
        ->assertOk()
        ->assertSee('Computer Science')
        ->assertSee('CSE');

    $this->actingAs($admin)
        ->put(route('departments.update', $department), [
            'department_code' => 'IT',
            'department_name' => 'Information Technology',
            'hod_name' => 'Dr. Grace Hopper',
        ])
        ->assertRedirect(route('departments.index'));

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'department_code' => 'IT',
        'department_name' => 'Information Technology',
        'hod_name' => 'Dr. Grace Hopper',
    ]);

    $this->actingAs($admin)
        ->delete(route('departments.destroy', $department))
        ->assertRedirect(route('departments.index'))
        ->assertSessionHas('success', 'Department deleted successfully.');

    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
});

it('requires unique department codes', function () {
    $admin = User::factory()->create();
    Department::create([
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Ada Lovelace',
    ]);

    $this->actingAs($admin)
        ->post(route('departments.store'), [
            'department_code' => 'CSE',
            'department_name' => 'Another Department',
            'hod_name' => 'Dr. Grace Hopper',
        ])
        ->assertSessionHasErrors('department_code');
});
