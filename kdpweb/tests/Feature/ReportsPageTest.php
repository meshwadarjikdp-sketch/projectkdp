<?php

use App\Models\User;

test('authenticated users can view the reports page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/reports');

    $response->assertStatus(200)
        ->assertSee('Reports Dashboard')
        ->assertSee('Available Reports');
});
