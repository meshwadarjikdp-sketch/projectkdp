<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
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
            position: sticky;
            top: 0;
            align-self: stretch;
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
            font-weight: 700;
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
            font-weight: 600;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .main-panel {
            flex: 1;
            padding: 2rem;
        }

        .page-wrapper {
            max-width: 1280px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .page-title {
            margin: 0 0 0.35rem;
            color: #111827;
            font-size: 1.95rem;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
        }

        .back-link {
            color: #1f3c88;
            font-weight: 700;
            text-decoration: none;
        }

        .panel-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(15, 23, 42, 0.08);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .feedback,
        .validation-errors {
            max-width: 1280px;
            margin: 0 auto 1.25rem;
            padding: 0.85rem 1rem;
            border-radius: 10px;
        }

        .feedback {
            background: #dcfce7;
            color: #166534;
        }

        .validation-errors {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 900px) {
            .dashboard-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
            }

            .main-panel {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
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
                <a href="{{ route('classrooms.index') }}">Manage Classrooms</a>
                <a href="{{ route('notifications.index') }}">Notification Management</a>
                <a href="#">Manage Subjects</a>
                <a href="#">Manage Students</a>
                <a href="#">Generate Timetable</a>
                <a href="#">Reports</a>
            </nav>
        </aside>

        <main class="main-panel">
            <div class="page-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
