<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar h2 {
            font-size: 24px;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .welcome-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .welcome-card h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .user-info {
            background: #f0f2f5;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
        
        .user-info p {
            margin: 0.5rem 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Dashboard</h2>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>Welcome, {{ Auth::user()->name }}!</h1>
            <p>You have successfully logged in to the application.</p>
            
            <div class="user-info">
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p><strong>User ID:</strong> {{ Auth::user()->id }}</p>
                <p><strong>Login Time:</strong> {{ date('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
