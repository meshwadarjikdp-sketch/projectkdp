@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
    <div class="page-header">
        <h1>Notifications 🔔</h1>
        <p>Stay up to date with the latest campus announcements.</p>
    </div>

    @php
        $typeColors = [
            'timetable' => ['bg' => '#ede9fe', 'text' => '#5b21b6', 'border' => '#8b5cf6', 'icon' => '📅'],
            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#ef4444', 'icon' => '❌'],
            'holiday'   => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#22c55e', 'icon' => '🌴'],
            'exam'      => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#f59e0b', 'icon' => '📝'],
        ];
    @endphp

    @if ($notifications->isEmpty())
        <div class="card" style="text-align:center;padding:3rem 2rem;">
            <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
            <div style="font-weight:700;font-size:1.1rem;color:#0f172a;margin-bottom:0.5rem;">No Notifications Yet</div>
            <div style="color:#64748b;">Check back later for campus announcements.</div>
        </div>
    @else
        <div style="display:grid;gap:1rem;">
            @foreach ($notifications as $notification)
                @php
                    $style = $typeColors[$notification->type] ?? ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#94a3b8', 'icon' => '📢'];
                @endphp
                <div class="card" style="border-left:4px solid {{ $style['border'] }};margin-bottom:0;padding:1.25rem 1.5rem;">
                    <div style="display:flex;align-items:flex-start;gap:1rem;">
                        <div style="font-size:1.75rem;flex-shrink:0;margin-top:0.1rem;">{{ $style['icon'] }}</div>
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:0.5rem;">
                                <span style="font-weight:700;font-size:1.05rem;color:#0f172a;">{{ $notification->title }}</span>
                                <span style="font-size:0.78rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:999px;background:{{ $style['bg'] }};color:{{ $style['text'] }};">
                                    {{ ucfirst($notification->type) }}
                                </span>
                            </div>
                            <p style="color:#334155;margin:0 0 0.6rem;line-height:1.6;">{{ $notification->message }}</p>
                            <div style="font-size:0.8rem;color:#94a3b8;">
                                <i class="fa-regular fa-clock" style="margin-right:0.3rem;"></i>
                                {{ $notification->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
