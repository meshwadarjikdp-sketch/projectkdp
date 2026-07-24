@extends('layouts.admin')

@section('title', 'Notification Management')

@section('content')
    <style>
        form {
            animation: slideInUp 0.6s ease-out;
        }
        
        input:focus, select:focus, textarea:focus {
            animation: pulse 0.3s ease-out;
        }

        button[type="submit"] {
            animation: fadeInScale 0.5s ease-out;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(-1px);
        }

        .panel-card:nth-child(3) > div > div {
            animation: slideInUp 0.5s ease-out backwards;
        }

        .panel-card:nth-child(3) > div > div:nth-child(1) { animation-delay: 0.3s; }
        .panel-card:nth-child(3) > div > div:nth-child(2) { animation-delay: 0.35s; }
        .panel-card:nth-child(3) > div > div:nth-child(3) { animation-delay: 0.4s; }
        .panel-card:nth-child(3) > div > div:nth-child(4) { animation-delay: 0.45s; }
        .panel-card:nth-child(3) > div > div:nth-child(5) { animation-delay: 0.5s; }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0); }
        }
    </style>

    <header class="page-header">
        <div>
            <h1 class="page-title">Notification Management</h1>
            <p class="subtitle">Publish notices for all users</p>
        </div>
        <a class="back-link" href="{{ route('dashboard') }}">Back to dashboard</a>
    </header>

    @if (session('success'))
        <div class="feedback">{{ session('success') }}</div>
    @endif

    @if (Auth::user()?->adminProfile()->exists())
        <section class="panel-card">
            <form action="{{ route('notifications.store') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                    <div>
                        <label for="title" style="display:block;font-size:0.92rem;font-weight:700;margin-bottom:0.35rem;">Title</label>
                        <input id="title" name="title" value="{{ old('title') }}" placeholder="New Timetable Published" required style="width:100%;border-radius:10px;border:1px solid #cbd5e1;padding:0.8rem;font:inherit;">
                    </div>
                    <div>
                        <label for="type" style="display:block;font-size:0.92rem;font-weight:700;margin-bottom:0.35rem;">Type</label>
                        <select id="type" name="type" required style="width:100%;border-radius:10px;border:1px solid #cbd5e1;padding:0.8rem;font:inherit;">
                            <option value="">Select notice type</option>
                            <option value="timetable">Timetable</option>
                            <option value="cancelled">Class Cancelled</option>
                            <option value="holiday">Holiday Notice</option>
                            <option value="exam">Exam Schedule</option>
                        </select>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <label for="message" style="display:block;font-size:0.92rem;font-weight:700;margin-bottom:0.35rem;">Message</label>
                        <textarea id="message" name="message" placeholder="Write the notification details here..." required style="width:100%;min-height:120px;resize:vertical;border-radius:10px;border:1px solid #cbd5e1;padding:0.8rem;font:inherit;">{{ old('message') }}</textarea>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <button type="submit" style="background:#1d4ed8;color:white;border:none;font-weight:700;cursor:pointer;border-radius:10px;padding:0.8rem;font:inherit;">Publish Notification</button>
                </div>
            </form>
        </section>
    @endif

    <section class="panel-card">
        <div style="display:grid;gap:1rem;">
            @forelse ($notifications as $notification)
                <article style="border:1px solid #e2e8f0;border-radius:12px;padding:1rem;background:#f8fafc;">
                    <h3 style="margin:0 0 0.5rem;">{{ $notification->title }}</h3>
                    <div style="color:#475569;font-size:0.9rem;margin-bottom:0.6rem;">
                        <strong>{{ ucfirst($notification->type) }}</strong>
                        &nbsp;•&nbsp;
                        {{ $notification->created_at->format('d M Y, h:i A') }}
                    </div>
                    <p>{{ $notification->message }}</p>
                </article>
            @empty
                <div style="color:#64748b;">No notifications have been published yet.</div>
            @endforelse
        </div>
    </section>
@endsection
