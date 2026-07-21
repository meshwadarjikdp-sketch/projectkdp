<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .error-message {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .form-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }

        .role-selector {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 25px;
        }

        .role-option {
            padding: 12px 14px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            background: #f8f9ff;
            color: #4b5563;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }

        .role-option.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.2);
        }

        .role-option small {
            display: block;
            font-size: 12px;
            font-weight: 500;
            margin-top: 3px;
            opacity: 0.8;
        }
        
        .credentials-hint {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
        
        .credentials-hint strong {
            color: #333;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 10px; color: #555;">KDP PATAN</h2>
        <h1>Login</h1>

        <div class="role-selector" aria-label="Login role selection">
            <button type="button" class="role-option active" data-role="admin">
                Admin Login
                <small>Access administration tools</small>
            </button>
            <button type="button" class="role-option" data-role="student">
                Student Login
                <small>Use student portal access</small>
            </button>
            <button type="button" class="role-option" data-role="faculty">
                Faculty Login
                <small>Enter faculty dashboard</small>
            </button>
        </div>
        
        @if ($errors->any())
            <div class="form-error">
                <strong>Login Failed!</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <input type="hidden" name="role" id="login-role" value="admin">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus
                    placeholder="admin@example.com"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="••••••••"
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="submit-btn">Login</button>
        </form>
        
        <div class="credentials-hint">
            <strong>Demo Account:</strong><br>
            Email: admin@example.com<br>
            Password: admin
        </div>
        <div class="auth-links">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.role-option').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('.role-option').forEach(function (item) {
                    item.classList.remove('active');
                });
                this.classList.add('active');
                document.getElementById('login-role').value = this.dataset.role;
            });
        });
    </script>
</body>
</html>
