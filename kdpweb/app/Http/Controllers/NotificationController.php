<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::query()
            ->orderByDesc('created_at')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->email === 'admin@example.com' || $user->adminProfile()->exists());

        if (! $isAdmin) {
            abort(403, 'Only the admin can publish notifications.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'message' => ['required', 'string'],
        ]);

        Notification::create($validated);

        return to_route('notifications.index')->with('success', 'Notification published successfully.');
    }
}
