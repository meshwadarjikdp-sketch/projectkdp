<?php

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows an admin to create, update, and delete classrooms', function () {
    $admin = User::create([
        'name' => 'Administrator',
        'email' => 'classrooms@example.com',
        'password' => Hash::make('admin'),
    ]);

    $response = $this->actingAs($admin)->post('/classrooms', [
        'room_number' => '101',
        'capacity' => 40,
        'room_type' => 'Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    $response->assertRedirect('/classrooms');

    $this->assertDatabaseHas('classrooms', [
        'room_number' => '101',
        'capacity' => 40,
        'room_type' => 'Classroom',
        'floor' => 1,
        'availability' => 'Available',
    ]);

    $classroom = Classroom::query()->where('room_number', '101')->firstOrFail();

    $updateResponse = $this->actingAs($admin)->patch('/classrooms/'.$classroom->id, [
        'room_number' => '102',
        'capacity' => 50,
        'room_type' => 'Lab',
        'floor' => 2,
        'availability' => 'Booked',
    ]);

    $updateResponse->assertRedirect('/classrooms');

    $this->assertDatabaseHas('classrooms', [
        'id' => $classroom->id,
        'room_number' => '102',
        'capacity' => 50,
        'room_type' => 'Lab',
        'floor' => 2,
        'availability' => 'Booked',
    ]);

    $deleteResponse = $this->actingAs($admin)->delete('/classrooms/'.$classroom->id);

    $deleteResponse->assertRedirect('/classrooms');

    $this->assertDatabaseMissing('classrooms', [
        'id' => $classroom->id,
    ]);
});
