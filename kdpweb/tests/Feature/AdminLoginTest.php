<?php

describe('admin login experience', function () {
    it('shows the admin purpose and management features on the login page', function () {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Admin Login');
    });
});
