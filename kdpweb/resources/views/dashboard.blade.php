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
            color: #1f2937;
        }

        .dashboard-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 290px;
            background: linear-gradient(135deg, #1f3c88 0%, #3b82f6 100%);
            color: white;
            padding: 2rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .profile-name {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .profile-meta {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.7rem 0.9rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .sidebar-nav a {
            color: white;
            text-decoration: none;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.2s ease;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .main-panel {
            flex: 1;
            padding: 2rem;
        }

        .welcome-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 16px rgba(15, 23, 42, 0.08);
            margin-bottom: 2rem;
            text-align: center;
        }

        .welcome-card h1 {
            color: #111827;
            margin-bottom: 0.75rem;
            font-size: 2rem;
        }

        .welcome-card p {
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .dashboard-card {
            display: block;
            padding: 1.2rem;
            border-radius: 12px;
            background: white;
            color: #111827;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-weight: 600;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.16);
        }
    </style>
</head>
<body>
    <div class="dashboard-shell">
        <aside class="sidebar">
            <div class="profile-card">
                <div class="profile-name">{{ Auth::user()->adminProfile?->profile_name ?? 'Admin Profile' }}</div>
                <div class="profile-meta">{{ Auth::user()->email }}</div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('departments.index') }}">Manage Department</a>
                <a href="{{ route('faculties.index') }}">Manage Faculty</a>
                <a href="#">Manage Subjects</a>
                <a href="#">Manage Classrooms</a>
                <a href="#">Manage Students</a>
                <a href="#">Generate Timetable</a>
                <a href="{{ route('notifications.index') }}">Notification Management</a>
                <a href="#">Reports</a>
            </nav>
        </aside>

        <main class="main-panel">
            <div class="welcome-card">
                <h1>Welcome Admin</h1>
                <p>You have successfully logged in to the administration dashboard.</p>
            </div>
        </main>
    </div>
</body>
</html>
