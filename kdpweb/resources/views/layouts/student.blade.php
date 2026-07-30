<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Portal') — KDP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #e0e7ff;
            --accent: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f1f5f9;
            --card: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-w: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ===== LAYOUT ===== */
        .shell {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(160deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1.75rem 1.25rem;
            gap: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 24px rgba(30, 27, 75, 0.25);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
        }

        .sidebar-brand .icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .sidebar-brand span {
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: -0.3px;
        }

        /* Student card */
        .student-card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 1.1rem;
            backdrop-filter: blur(10px);
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .student-card .name {
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.3;
            margin-bottom: 0.25rem;
        }

        .student-card .enroll {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.65);
            font-family: monospace;
            letter-spacing: 0.5px;
        }

        .student-card .dept {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.55);
            margin-top: 0.15rem;
        }

        /* Nav */
        .sidebar-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            padding: 0 0.25rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            opacity: 0.85;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(4px);
        }

        .sidebar-nav a.active {
            background: rgba(255,255,255,0.2);
            font-weight: 600;
        }

        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        .logout-form {
            margin-top: auto;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: rgba(239,68,68,0.2);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(239,68,68,0.35);
            color: white;
            transform: translateX(2px);
        }

        /* ===== MAIN ===== */
        .main-panel {
            flex: 1;
            padding: 2rem;
            max-width: calc(100vw - var(--sidebar-w));
            overflow-x: hidden;
        }

        .page-header {
            margin-bottom: 1.75rem;
        }

        .page-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .page-header p {
            color: var(--text-muted);
            margin-top: 0.35rem;
            font-size: 0.95rem;
        }

        /* Card */
        .card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(15, 23, 42, 0.07);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
        }

        /* Alerts */
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #22c55e;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.92rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.92rem;
        }

        /* Form */
        .form-group { margin-bottom: 1rem; }

        .form-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
        }

        @media (max-width: 768px) {
            .shell { flex-direction: column; }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                flex-direction: row;
                flex-wrap: wrap;
                padding: 1rem;
                gap: 0.75rem;
            }
            .sidebar-brand, .student-card { display: none; }
            .sidebar-label { display: none; }
            .sidebar-nav { flex-direction: row; flex-wrap: wrap; }
            .logout-form { margin-top: 0; }
            .main-panel { max-width: 100%; padding: 1rem; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="icon">🎓</div>
            <span>Student Portal</span>
        </div>

        <div class="student-card">
            <div class="student-avatar">{{ strtoupper(substr($student->student_name, 0, 1)) }}</div>
            <div class="name">{{ $student->student_name }}</div>
            <div class="enroll">{{ $student->enrollment_number }}</div>
            <div class="dept">{{ $student->department?->department_name ?? '—' }} · Sem {{ $student->semester }}</div>
        </div>

        <span class="sidebar-label">Menu</span>

        <nav class="sidebar-nav">
            <a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="{{ route('student.timetable') }}" class="{{ request()->routeIs('student.timetable') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i> View Timetable
            </a>
            <a href="{{ route('student.notifications') }}" class="{{ request()->routeIs('student.notifications') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i> Receive Notifications
            </a>
            <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user-pen"></i> Update Profile
            </a>
        </nav>

        <form action="{{ route('student.logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    <main class="main-panel">
        @yield('content')
    </main>
</div>
</body>
</html>
