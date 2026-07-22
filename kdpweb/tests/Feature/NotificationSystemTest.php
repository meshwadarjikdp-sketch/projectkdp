<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows an admin to publish a notification and any authenticated user can read it', function () {
    $admin = User::create([
        'name' => 'Administrator',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin'),
    ]);

    $this->actingAs($admin)->post('/notifications', [
        'title' => 'New Timetable Published',
        'type' => 'timetable',
        'message' => 'The updated timetable is now available for all departments.',
    ])->assertRedirect('/notifications');

    $this->assertDatabaseHas('notifications', [
        'title' => 'New Timetable Published',
        'type' => 'timetable',
    ]);

    $viewer = User::create([
        'name' => 'Student User',
        'email' => 'student@example.com',
        'password' => Hash::make('student'),
    ]);

    $response = $this->actingAs($viewer)->get('/notifications');

    $response->assertStatus(200)
        ->assertSee('New Timetable Published')
        ->assertSee('The updated timetable is now available for all departments.');
});
