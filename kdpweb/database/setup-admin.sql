-- Create admin user directly in database
-- Run this if migrations have been executed and users table exists

INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES (
    'Administrator',
    'admin@example.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG';

-- Password hash is for 'admin' using bcrypt (BCRYPT_ROUNDS=12)
-- Verify with: Hash::check('admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5EQiFDfQU5sDG')
