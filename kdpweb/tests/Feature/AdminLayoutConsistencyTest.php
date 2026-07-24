<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('shows the admin sidebar and consistent layout across protected management pages', function () {
    $admin = User::create([
        'name' => 'Administrator',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin'),
    ]);

    $dashboard = $this->actingAs($admin)->get('/dashboard');
    $dashboard->assertStatus(200)
        ->assertSee('Manage Department')
        ->assertSee('Manage Faculty')
        ->assertSee('Notification Management');

    $departmentsPage = $this->actingAs($admin)->get('/departments');
    $departmentsPage->assertStatus(200)
        ->assertSee('Manage Department')
        ->assertSee('Manage Faculty')
        ->assertSee('Notification Management');

    $facultiesPage = $this->actingAs($admin)->get('/faculties');
    $facultiesPage->assertStatus(200)
        ->assertSee('Manage Faculty')
        ->assertSee('Faculty name')
        ->assertSee('Faculty ID')
        ->assertSee('Department')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Subject')
        ->assertSee('Availability');

    $notificationsPage = $this->actingAs($admin)->get('/notifications');
    $notificationsPage->assertStatus(200)
        ->assertSee('Manage Department')
        ->assertSee('Manage Faculty')
        ->assertSee('Notification Management');
});
