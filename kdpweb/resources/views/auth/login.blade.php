<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI College Timetable Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-5+2W0hV0RcRGCNo8jlwH5V2c+Fds8kAuQjXJ7fM+N+4GM2yAJ0mcaQzPgCHyaIndHcr2V8OuXfs8abInnfY8Pw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at top left, rgba(191, 219, 254, 0.75), rgba(248, 250, 252, 1) 45%);
            color: #0f172a;
        }
        button { font: inherit; }
        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }
        .auth-layout {
            display: grid;
            grid-template-columns: minmax(320px, 1.1fr) minmax(360px, 1fr);
            gap: 26px;
            width: min(100%, 1320px);
            min-height: 720px;
        }
        .sidebar {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
            background: linear-gradient(130deg, #0f172a 0%, #1d4ed8 45%, #60a5fa 100%);
            padding: 42px 34px;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 28px 90px rgba(15, 23, 42, 0.24);
            color: #f8fafc;
        }
        .sidebar::before,
        .sidebar::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(32px);
            opacity: 0.6;
        }
        .sidebar::before {
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.14);
            top: -40px;
            right: -40px;
        }
        .sidebar::after {
            width: 180px;
            height: 180px;
            background: rgba(59, 130, 246, 0.3);
            bottom: -40px;
            left: -20px;
        }
        .sidebar .badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(255,255,255,0.08);
            color: #e0f2fe;
            font-size: 0.86rem;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
        }
        .sidebar h1 {
            font-size: clamp(2.4rem, 3vw, 3.4rem);
            line-height: 1.04;
            letter-spacing: -0.04em;
            margin-top: 28px;
            max-width: 13ch;
        }
        .sidebar p {
            max-width: 38ch;
            margin-top: 18px;
            color: rgba(248,250,252,0.88);
            line-height: 1.75;
            font-size: 0.98rem;
        }
        .sidebar .feature-list {
            display: grid;
            gap: 12px;
            margin-top: 24px;
        }
        .sidebar .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
        }
        .sidebar .feature-item i {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            color: #e0f2fe;
        }
        .sidebar .feature-item span {
            font-size: 0.96rem;
            color: #e2e8f0;
            line-height: 1.5;
        }
        .sidebar .options {
            display: grid;
            gap: 12px;
            margin-top: 10px;
        }
        .sidebar .option-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 15px 18px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(255,255,255,0.08);
            color: #f8fafc;
            font-weight: 600;
            transition: transform 0.25s ease, background 0.25s ease, border-color 0.25s ease;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }
        .sidebar .option-btn.active,
        .sidebar .option-btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.28);
            transform: translateY(-1px);
        }
        .sidebar .option-btn i { color: #bfdbfe; }
        .sidebar .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.14);
            color: rgba(255,255,255,0.75);
            font-size: 0.92rem;
            line-height: 1.7;
        }
        .auth-panel {
            position: relative;
            background: #fff;
            border-radius: 25px;
            padding: 28px;
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.14);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 720px;
        }
        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.15), transparent 28%);
            pointer-events: none;
        }
        .top-nav {
            position: relative;
            z-index: 1;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 24px;
        }
        .top-nav .title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #0f172a;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .top-nav .title span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #2563eb;
        }
        .form-tabs {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            width: 100%;
        }
        .form-tabs button {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .form-tabs button.active {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
        }
        .form-card {
            position: relative;
            flex: 1;
            overflow: auto;
            margin-top: 12px;
            padding-top: 6px;
        }
        .panel {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateX(32px);
            transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
            pointer-events: none;
            overflow: auto;
        }
        .panel.active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }
        .panel.slide-out {
            opacity: 0;
            transform: translateX(-32px);
        }
        .panel-content {
            position: relative;
            z-index: 1;
            padding: 12px 0 4px;
        }
        .panel h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #0f172a;
        }
        .panel p {
            margin-bottom: 24px;
            color: #64748b;
            line-height: 1.75;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #334155;
            font-weight: 600;
        }
        .input-group {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            border-radius: 18px;
            padding: 14px 18px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-group:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.12);
            background: #fff;
        }
        .input-group i {
            color: #2563eb;
            min-width: 18px;
            text-align: center;
        }
        .input-group input,
        .input-group select {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 0.96rem;
            color: #0f172a;
        }
        .input-group select { appearance: none; }
        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            gap: 10px;
            padding: 15px 18px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.22);
        }
        .submit-btn:hover { transform: translateY(-2px); }
        .note-box {
            margin-top: 20px;
            padding: 18px 20px;
            border-radius: 20px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #334155;
        }
        .note-box strong { display: block; margin-bottom: 6px; color: #1d4ed8; }
        @media (max-width: 1024px) {
            .auth-layout { grid-template-columns: 1fr; min-height: auto; }
            .auth-panel { min-height: 640px; }
        }
        @media (max-width: 720px) {
            .sidebar { padding: 30px 22px; border-radius: 22px; }
            .auth-panel { padding: 24px; }
            .form-tabs { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-layout">
            <section class="sidebar">
                <div>
                    <div class="badge"><i class="fa-solid fa-robot"></i> AI College Timetable Management System</div>
                    <h1>Welcome To<br>K. D. POLYTECHNIC, PATAN</h1>
                </div>

                <div>
                    <div class="options">
                        <button class="option-btn active" data-panel-button="admin-login"><span>Admin Login</span><i class="fa-solid fa-angle-right"></i></button>
                        <button class="option-btn" data-panel-button="student-register"><span>Student Register</span><i class="fa-solid fa-angle-right"></i></button>
                        <button class="option-btn" data-panel-button="student-login"><span>Student Login</span><i class="fa-solid fa-angle-right"></i></button>
                        <button class="option-btn" data-panel-button="faculty-login"><span>Faculty Login</span><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                    <div class="sidebar-footer">
                        Press each option to switch forms smoothly. The layout is fully responsive and designed for fast campus access.
                    </div>
                </div>
            </section>

            <section class="auth-panel">
                <div class="top-nav">
                    <span class="title"><i class="fa-solid fa-right-to-bracket"></i> Sign In</span>
                    <div class="form-tabs">
                        <button class="active" type="button" data-panel-button="admin-login">Admin Login</button>
                        <button type="button" data-panel-button="student-register">Student Register</button>
                        <button type="button" data-panel-button="student-login">Student Login</button>
                        <button type="button" data-panel-button="faculty-login">Faculty Login</button>
                    </div>
                </div>

                <div class="form-card">
                    <div class="panel active" data-panel="admin-login">
                        <div class="panel-content">
                            <h2>Admin Login</h2>
                            <p>Login with your administrator email and password to access the dashboard.</p>
                            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                                @csrf
                                <div class="form-group">
                                    <label for="admin-email">Admin Email</label>
                                    <div class="input-group">
                                        <i class="fa-regular fa-envelope"></i>
                                        <input id="admin-email" type="email" name="email" placeholder="admin@kdp.edu" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="admin-password">Password</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-lock"></i>
                                        <input id="admin-password" type="password" name="password" placeholder="Enter password" required />
                                    </div>
                                </div>
                                <button type="submit" class="submit-btn">Login</button>
                            </form>
                            <div class="note-box">
                                <strong>Admin Access</strong>
                                Use admin credentials to manage schedules, departments, and campus reports.
                            </div>
                        </div>
                    </div>

                    <div class="panel" data-panel="student-register">
                        <div class="panel-content">
                            <h2>Student Register</h2>
                            <p>Create your student account with enrollment details and class information.</p>
                            <form method="POST" action="/student/register" class="space-y-4">
                                @csrf
                                <div class="form-group">
                                    <label for="student-name">Student Name</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-user"></i>
                                        <input id="student-name" type="text" name="student_name" placeholder="Enter full name" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="student-enrollment">Enrollment Number</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-id-card"></i>
                                        <input id="student-enrollment" type="text" name="enrollment_number" placeholder="Enter enrollment number" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="student-semester">Semester</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-book"></i>
                                        <select id="student-semester" name="semester" required>
                                            <option value="">Select semester</option>
                                            <option value="1">Semester 1</option>
                                            <option value="2">Semester 2</option>
                                            <option value="3">Semester 3</option>
                                            <option value="4">Semester 4</option>
                                            <option value="5">Semester 5</option>
                                            <option value="6">Semester 6</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="student-class">Class</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-school"></i>
                                        <select id="student-class" name="class" required>
                                            <option value="">Select class</option>
                                            <option value="A">Class A</option>
                                            <option value="B">Class B</option>
                                            <option value="C">Class C</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="form-group">
                                        <label for="student-password">Password</label>
                                        <div class="input-group">
                                            <i class="fa-solid fa-lock"></i>
                                            <input id="student-password" type="password" name="password" placeholder="Create password" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="student-password-confirm">Confirm Password</label>
                                        <div class="input-group">
                                            <i class="fa-solid fa-lock"></i>
                                            <input id="student-password-confirm" type="password" name="password_confirmation" placeholder="Confirm password" required />
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="submit-btn">Register</button>
                            </form>
                        </div>
                    </div>

                    <div class="panel" data-panel="student-login">
                        <div class="panel-content">
                            <h2>Student Login</h2>
                            <p>Login with your enrollment number and password to access your timetable.</p>
                            <form method="POST" action="/student/login" class="space-y-4">
                                <div class="form-group">
                                    <label for="student-login-enrollment">Enrollment Number</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-id-badge"></i>
                                        <input id="student-login-enrollment" type="text" name="enrollment_number" placeholder="Enter enrollment number" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="student-login-password">Password</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-lock"></i>
                                        <input id="student-login-password" type="password" name="password" placeholder="Enter password" required />
                                    </div>
                                </div>
                                <button type="submit" class="submit-btn">Login</button>
                            </form>
                        </div>
                    </div>

                    <div class="panel" data-panel="faculty-login">
                        <div class="panel-content">
                            <h2>Faculty Login</h2>
                            <p>Login using your faculty email and password for staff access.</p>
                            <form method="POST" action="/faculty/login" class="space-y-4">
                                <div class="form-group">
                                    <label for="faculty-email">Faculty Email</label>
                                    <div class="input-group">
                                        <i class="fa-regular fa-envelope"></i>
                                        <input id="faculty-email" type="email" name="email" placeholder="faculty@kdp.edu" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="faculty-password">Password</label>
                                    <div class="input-group">
                                        <i class="fa-solid fa-lock"></i>
                                        <input id="faculty-password" type="password" name="password" placeholder="Enter password" required />
                                    </div>
                                </div>
                                <button type="submit" class="submit-btn">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        const tabButtons = document.querySelectorAll('[data-panel-button]');
        const panels = document.querySelectorAll('.panel');
        let activePanel = 'admin-login';

        function setActivePanel(target) {
            if (target === activePanel) return;
            const prevPanel = document.querySelector(`.panel[data-panel="${activePanel}"]`);
            const nextPanel = document.querySelector(`.panel[data-panel="${target}"]`);

            prevPanel.classList.remove('active');
            prevPanel.classList.add('slide-out');
            nextPanel.classList.remove('slide-out');
            nextPanel.classList.add('active');

            tabButtons.forEach(button => {
                button.classList.toggle('active', button.dataset.panelButton === target);
            });

            activePanel = target;
            setTimeout(() => prevPanel.classList.remove('slide-out'), 600);
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => setActivePanel(button.dataset.panelButton));
        });
    </script>
</body>
</html>
