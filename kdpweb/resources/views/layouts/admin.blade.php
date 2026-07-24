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

        /* ========== ANIMATIONS ========== */
        @keyframes fadeInSlideRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 2px 16px rgba(15, 23, 42, 0.08);
            }
            50% {
                box-shadow: 0 2px 24px rgba(59, 130, 246, 0.15);
            }
        }

        /* ========== GENERAL STYLES ========== */
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
            animation: fadeInSlideRight 0.6s ease-out;
            box-shadow: 2px 0 20px rgba(31, 60, 136, 0.2);
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            animation: fadeInScale 0.7s ease-out 0.1s backwards;
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .logout-btn:active {
            transform: scale(0.95);
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
            transition: all 0.3s ease;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.5s ease-out backwards;
        }

        .sidebar-nav a:nth-child(1) { animation-delay: 0.15s; }
        .sidebar-nav a:nth-child(2) { animation-delay: 0.2s; }
        .sidebar-nav a:nth-child(3) { animation-delay: 0.25s; }
        .sidebar-nav a:nth-child(4) { animation-delay: 0.3s; }
        .sidebar-nav a:nth-child(5) { animation-delay: 0.35s; }
        .sidebar-nav a:nth-child(6) { animation-delay: 0.4s; }
        .sidebar-nav a:nth-child(7) { animation-delay: 0.45s; }
        .sidebar-nav a:nth-child(8) { animation-delay: 0.5s; }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .main-panel {
            flex: 1;
            padding: 2rem;
            animation: fadeInSlideRight 0.8s ease-out;
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
            animation: fadeInSlideDown 0.6s ease-out;
        }

        .page-title {
            margin: 0 0 0.35rem;
            color: #111827;
            font-size: 1.95rem;
            animation: fadeInScale 0.7s ease-out;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            animation: fadeInScale 0.8s ease-out;
        }

        .back-link {
            color: #1f3c88;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            text-decoration: underline;
            transform: translateX(-3px);
        }

        .panel-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(15, 23, 42, 0.08);
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            animation: slideInUp 0.6s ease-out backwards;
            transition: all 0.3s ease;
        }

        .panel-card:nth-of-type(1) { animation-delay: 0.2s; }
        .panel-card:nth-of-type(2) { animation-delay: 0.4s; }

        .panel-card:hover {
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            transform: translateY(-4px);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #111827;
            animation: fadeInScale 0.6s ease-out;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .stat-card {
            padding: 1rem 1.1rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            border: 1px solid #dbeafe;
            animation: slideInUp 0.5s ease-out backwards;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:nth-child(1) { animation-delay: 0.3s; }
        .stat-card:nth-child(2) { animation-delay: 0.35s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
        .stat-card:nth-child(4) { animation-delay: 0.45s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.55s; }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.15);
            background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        }

        .stat-card .label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.35rem;
        }

        .stat-card .value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111827;
            animation: slideInUp 0.5s ease-out;
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .report-card {
            padding: 1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #111827;
            animation: slideInUp 0.5s ease-out backwards;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .report-card:nth-child(1) { animation-delay: 0.4s; }
        .report-card:nth-child(2) { animation-delay: 0.45s; }
        .report-card:nth-child(3) { animation-delay: 0.5s; }
        .report-card:nth-child(4) { animation-delay: 0.55s; }
        .report-card:nth-child(5) { animation-delay: 0.6s; }
        .report-card:nth-child(6) { animation-delay: 0.65s; }

        .report-card:hover {
            transform: translateY(-6px);
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.1);
        }

        .report-card strong {
            display: block;
            margin-bottom: 0.35rem;
            animation: fadeInScale 0.5s ease-out;
        }

        .report-card span {
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.4;
        }

        .feedback,
        .validation-errors {
            max-width: 1280px;
            margin: 0 auto 1.25rem;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            animation: slideInUp 0.5s ease-out;
        }

        .feedback {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #22c55e;
        }

        .validation-errors {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        @media (max-width: 900px) {
            .dashboard-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
                animation: fadeInSlideDown 0.6s ease-out;
            }

            .main-panel {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stat-card {
                animation: slideInUp 0.5s ease-out backwards;
            }

            .report-card {
                animation: slideInUp 0.5s ease-out backwards;
            }
        }

        /* ========== TABLE ANIMATIONS ========== */
        table {
            width: 100%;
            animation: slideInUp 0.6s ease-out;
        }

        tbody tr {
            animation: slideInUp 0.5s ease-out backwards;
            transition: all 0.3s ease;
        }

        tbody tr:nth-child(1) { animation-delay: 0.3s; }
        tbody tr:nth-child(2) { animation-delay: 0.35s; }
        tbody tr:nth-child(3) { animation-delay: 0.4s; }
        tbody tr:nth-child(4) { animation-delay: 0.45s; }
        tbody tr:nth-child(5) { animation-delay: 0.5s; }

        tbody tr:hover {
            background-color: #f0f7ff;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        /* ========== BUTTON ANIMATIONS ========== */
        button, a.btn, input[type="submit"], input[type="button"] {
            transition: all 0.3s ease;
        }

        button:hover, a.btn:hover, input[type="submit"]:hover, input[type="button"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        button:active, a.btn:active, input[type="submit"]:active, input[type="button"]:active {
            transform: translateY(0);
        }

        /* ========== FORM ANIMATIONS ========== */
        input, textarea, select {
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* ========== LIST ANIMATIONS ========== */
        ul li, ol li {
            animation: slideInUp 0.4s ease-out backwards;
        }

        ul li:nth-child(1) { animation-delay: 0.2s; }
        ul li:nth-child(2) { animation-delay: 0.25s; }
        ul li:nth-child(3) { animation-delay: 0.3s; }
        ul li:nth-child(4) { animation-delay: 0.35s; }
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
                <a href="{{ route('reports.index') }}">Report Dashboard</a>
                <a href="#">Manage Subjects</a>
                <a href="#">Manage Students</a>
                <a href="#">Generate Timetable</a>
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
