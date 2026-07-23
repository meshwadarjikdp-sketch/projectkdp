<?php

use App\Models\User;

it('shows the dashboard summary and report sections for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Total Students');
    $response->assertSee('Total Faculty');
    $response->assertSee('Total Departments');
    $response->assertSee('Today');
    $response->assertSee('Classes');
    $response->assertSee('Notifications');
    $response->assertDontSee('Open Reports');
    $response->assertDontSee('Reports');
});
