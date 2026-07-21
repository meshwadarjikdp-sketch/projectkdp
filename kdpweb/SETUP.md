# Laravel Login Setup Guide

## Issue
The credentials are not working because:
1. MySQL database `kdptt` needs to exist
2. Migrations need to be run to create tables
3. Admin user needs to be seeded

## Step 1: Create MySQL Database

```sql
CREATE DATABASE IF NOT EXISTS kdptt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Step 2: Run Migrations

```bash
php artisan migrate
```

## Step 3: Seed the Admin User

```bash
php artisan db:seed:AdminUserSeeder
```

Or seed everything:
```bash
php artisan db:seed
```

## Step 4: Test Login

- URL: `http://localhost:8000/login`
- Email: `admin@example.com`
- Password: `admin`

## Alternative: Direct SQL Insert

If you can't run artisan commands, use MySQL directly:

```sql
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES (
    'Administrator',
    'admin@example.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG';
```

The password hash is: `$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG` (bcrypt hash of "admin")
