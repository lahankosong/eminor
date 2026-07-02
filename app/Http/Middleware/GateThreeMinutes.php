<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GateThreeMinutes
{
    /**
     * Handle an incoming request.
     * Guest hanya bisa mengakses selama 3 menit, setelah itu redirect ke login.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            $key = 'gate_expires_' . session()->getId();
            $expires = session()->get($key, 0);

            // Jika belum punya timer, mulai
            if ($expires === 0) {
                session()->put($key, time() + 180);
                $expires = time() + 180;
            }

            // Jika timer habis, redirect ke halaman login
            if (time() > $expires) {
                return redirect()->route('google.login')
                    ->with('error', 'Sesi eksplorasi 3 menit habis. Login untuk melanjutkan.');
            }
        }

        return $next($request);
    }
}