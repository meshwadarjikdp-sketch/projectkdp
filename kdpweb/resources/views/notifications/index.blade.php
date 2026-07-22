<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Management</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #0f172a;
        }
        .page-wrapper { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 16px; box-shadow: 0 2px 16px rgba(15, 23, 42, 0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .topbar h1 { margin: 0; }
        .back-link { text-decoration: none; color: #1d4ed8; font-weight: 600; }
        .flash { padding: 0.9rem 1rem; border-radius: 10px; margin-bottom: 1rem; background: #dcfce7; color: #166534; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
        label { display: block; font-size: 0.92rem; font-weight: 700; margin-bottom: 0.35rem; }
        input, select, textarea, button { width: 100%; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0.8rem; font: inherit; }
        textarea { min-height: 120px; resize: vertical; }
        button { background: #1d4ed8; color: white; border: none; font-weight: 700; cursor: pointer; }
        .list { display: grid; gap: 1rem; }
        .notification-item { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; background: #f8fafc; }
        .notification-item h3 { margin: 0 0 0.5rem; }
        .notification-meta { color: #475569; font-size: 0.9rem; margin-bottom: 0.6rem; }
        .empty-state { color: #64748b; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="topbar">
            <h1>Notification Management</h1>
            <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
        </div>

        @if (session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @if (Auth::user()?->adminProfile()->exists())
            <section class="card">
                <form action="{{ route('notifications.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div>
                            <label for="title">Title</label>
                            <input id="title" name="title" value="{{ old('title') }}" placeholder="New Timetable Published" required>
                        </div>
                        <div>
                            <label for="type">Type</label>
                            <select id="type" name="type" required>
                                <option value="">Select notice type</option>
                                <option value="timetable">Timetable</option>
                                <option value="cancelled">Class Cancelled</option>
                                <option value="holiday">Holiday Notice</option>
                                <option value="exam">Exam Schedule</option>
                            </select>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Write the notification details here..." required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <button type="submit">Publish Notification</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="card">
            <div class="list">
                @forelse ($notifications as $notification)
                    <article class="notification-item">
                        <h3>{{ $notification->title }}</h3>
                        <div class="notification-meta">
                            <strong>{{ ucfirst($notification->type) }}</strong>
                            &nbsp;•&nbsp;
                            {{ $notification->created_at->format('d M Y, h:i A') }}
                        </div>
                        <p>{{ $notification->message }}</p>
                    </article>
                @empty
                    <div class="empty-state">No notifications have been published yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</body>
</html>
