<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:500',
            'p256dh'   => 'nullable|string|max:255',
            'auth'     => 'nullable|string|max:255',
        ]);

        try {
            PushSubscription::updateOrCreate(
                ['endpoint' => $data['endpoint']],
                [
                    'user_id' => Auth::id(),
                    'p256dh'  => $data['p256dh'] ?? null,
                    'auth'    => $data['auth'] ?? null,
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 200);
        }

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $endpoint = (string) $request->input('endpoint');
        if ($endpoint !== '') {
            try {
                PushSubscription::where('endpoint', $endpoint)
                    ->where('user_id', Auth::id())->delete();
            } catch (\Throwable $e) {}
        }
        return response()->json(['ok' => true]);
    }
}
