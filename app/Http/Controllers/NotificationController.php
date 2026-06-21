<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Notif terbaru (untuk ditampilkan service worker saat push). */
    public function latest()
    {
        $n = AppNotification::where('user_id', Auth::id())->whereNull('read_at')->latest('id')->first()
          ?: AppNotification::where('user_id', Auth::id())->latest('id')->first();

        if (!$n) {
            return response()->json(['id' => 0, 'title' => 'Margonoandi', 'body' => 'Ada notifikasi baru', 'url' => url('/dia')]);
        }
        return response()->json([
            'id'    => $n->id,
            'title' => ($n->icon ? $n->icon . ' ' : '') . ($n->title ?: 'Margonoandi'),
            'body'  => $n->body ?: '',
            'url'   => $n->url ?: url('/'),
        ]);
    }

    public function index()
    {
        try {
            $notifications = AppNotification::where('user_id', Auth::id())
                ->with('fromUser')
                ->orderByDesc('created_at')
                ->take(30)
                ->get();

            $mapped = $notifications->map(fn($n) => array_merge($n->toArray(), [
                'created_at_diff' => $n->created_at?->diffForHumans() ?? '',
            ]));

            return response()->json([
                'notifications' => $mapped,
                'unread_count'  => $notifications->whereNull('read_at')->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }
    }

    public function markRead($id)
    {
        try {
            $notif = AppNotification::where('user_id', Auth::id())->findOrFail($id);
            $notif->update(['read_at' => now()]);
        } catch (\Throwable $e) {}
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        try {
            AppNotification::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {}
        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        try {
            $count = AppNotification::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->count();
        } catch (\Throwable $e) {
            $count = 0;
        }
        return response()->json(['count' => $count]);
    }
}