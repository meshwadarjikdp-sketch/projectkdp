<?php

use App\Models\User;

it('allows an authenticated admin to view and add departments', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('departments.store'), [
            'department_code' => 'CSE',
            'department_name' => 'Computer Science',
            'hod_name' => 'Dr. Ada Lovelace',
        ])
        ->assertRedirect(route('departments.index'))
        ->assertSessionHas('success', 'Department added successfully.');

    $this->assertDatabaseHas('departments', [
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Ada Lovelace',
    ]);

    $this->actingAs($admin)
        ->get(route('departments.index'))
        ->assertOk()
        ->assertSee('Computer Science')
        ->assertSee('CSE')
        ->assertSee('Dr. Ada Lovelace')
        ->assertSee('Search department')
        ->assertSee('department-options')
        ->assertSee('Select or type a department')
        ->assertDontSee('Edit')
        ->assertDontSee('Delete');
});

it('filters departments through the department search options', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('departments.store'), [
        'department_code' => 'CSE',
        'department_name' => 'Computer Science',
        'hod_name' => 'Dr. Ada Lovelace',
    ]);

    $this->actingAs($admin)->post(route('departments.store'), [
        'department_code' => 'EEE',
        'department_name' => 'Electrical Engineering',
        'hod_name' => 'Dr. Alan Turing',
    ]);

    $this->actingAs($admin)
        ->get(route('departments.index', ['search' => 'EEE']))
        ->assertOk()
        ->assertSee('Electrical Engineering')
        ->assertSee('<td>Electrical Engineering</td>', false)
        ->assertDontSee('<td>Computer Science</td>', false);
});
