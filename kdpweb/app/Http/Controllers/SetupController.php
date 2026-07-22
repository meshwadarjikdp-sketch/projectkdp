<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SetupController extends Controller
{
    /**
     * Initialize database and create admin user
     */
    public function initialize()
    {
        try {
            // Create users table if it doesn't exist
            if (!Schema::hasTable('users')) {
                DB::statement("
                    CREATE TABLE IF NOT EXISTS users (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        email_verified_at TIMESTAMP NULL,
                        password VARCHAR(255) NOT NULL,
                        remember_token VARCHAR(100),
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        INDEX idx_email (email)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            // Delete existing admin user if exists
            User::where('email', 'admin@example.com')->delete();

            // Create admin user
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
            ]);

            AdminProfile::firstOrCreate([
                'user_id' => $admin->id,
            ], [
                'profile_name' => 'Administrator Profile',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Database initialized and admin user created!',
                'credentials' => [
                    'email' => 'admin@example.com',
                    'password' => 'admin',
                ],
                'next_step' => 'Visit /login to sign in',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
