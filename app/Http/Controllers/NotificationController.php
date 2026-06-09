<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', Auth::id())
            ->with('fromUser')
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $notifications->whereNull('read_at')->count(),
        ]);
    }

    public function markRead($id)
    {
        $notif = AppNotification::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
        return response()->json(['count' => $count]);
    }
}