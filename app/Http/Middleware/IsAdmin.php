<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('home')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $adminEmails = explode(',', env('ADMIN_EMAILS', ''));
        
        if (!in_array(auth()->user()->email, $adminEmails)) {
            return redirect()->route('home')
                ->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}