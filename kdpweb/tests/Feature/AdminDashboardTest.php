<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an admin profile on admin login and shows dashboard options', function () {
    $admin = User::create([
        'name' => 'Administrator',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin'),
    ]);

    $response = $this->post('/login', [
        'email' => 'admin@example.com',
        'password' => 'admin',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('admin_profiles', [
        'user_id' => $admin->id,
        'profile_name' => 'Administrator Profile',
    ]);

    $dashboard = $this->actingAs($admin)->get('/dashboard');
    $dashboard->assertStatus(200)
        ->assertSee('Manage Departments')
        ->assertSee('Manage Faculty')
        ->assertSee('Manage Subjects')
        ->assertSee('Manage Classrooms')
        ->assertSee('Manage Students')
        ->assertSee('Generate Timetable')
        ->assertSee('Notification Management')
        ->assertSee('Reports');
});
