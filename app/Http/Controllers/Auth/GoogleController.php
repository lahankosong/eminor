<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
                // State OAuth hilang (umum di cPanel/TWA: sesi tak terbawa balik dari Google)
                // -> ambil profil tanpa verifikasi state.
                $googleUser = Socialite::driver('google')->stateless()->user();
            }

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'avatar'    => $googleUser->getAvatar(),
                    'google_id' => $googleUser->getId(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                try {
                    MemberLog::create(['user_id' => $user->id]);
                } catch (\Throwable $e) {}
                // Bot sambutan: kirim pesan selamat datang + ajakan dukungan via chat (Dia)
                try {
                    \App\Helpers\WelcomeBot::sendWelcome($user);
                } catch (\Throwable $e) {}
            }

            Auth::login($user, true);

            session()->flash('ga_login', true);
            if ($user->wasRecentlyCreated) {
                session()->flash('ga_new_user', true);
            }

            return redirect()->route('aku');

        } catch (\Throwable $e) {
            // Catat error asli ke storage/logs (sebelumnya tertutup karena route 'login' tak ada)
            report($e);
            // 'login' BUKAN nama route yang valid (login = google.login) -> arahkan ke home dgn pesan
            return redirect()->route('home')
                ->with('error', 'Login gagal, silakan coba lagi.');
        }
    }

    /**
     * Google One Tap: terima ID token (JWT) dari popup "Lanjut sebagai ...",
     * verifikasi SERVER-SIDE via endpoint resmi Google (tokeninfo) lalu login.
     * Aman: cek tanda tangan (oleh Google), audience (client_id kita), & issuer.
     */
    public function oneTap(Request $request)
    {
        $credential = (string) $request->input('credential', '');
        if ($credential === '') {
            return response()->json(['ok' => false], 422);
        }

        try {
            $resp = Http::timeout(8)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $credential,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'error' => 'verify_failed'], 200);
        }

        if (!$resp->ok()) {
            return response()->json(['ok' => false, 'error' => 'invalid_token'], 200);
        }

        $p        = $resp->json();
        $clientId = config('services.google.client_id');

        // WAJIB: token harus diterbitkan untuk APP KITA (cegah token app lain) & oleh Google
        if (empty($clientId) || ($p['aud'] ?? null) !== $clientId) {
            return response()->json(['ok' => false, 'error' => 'aud'], 200);
        }
        if (!in_array($p['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true)) {
            return response()->json(['ok' => false, 'error' => 'iss'], 200);
        }
        if (empty($p['sub'])) {
            return response()->json(['ok' => false, 'error' => 'sub'], 200);
        }

        try {
            $user = User::updateOrCreate(
                ['google_id' => $p['sub']],
                [
                    'name'      => $p['name'] ?? ($p['email'] ?? 'Musisi'),
                    'email'     => $p['email'] ?? null,
                    'avatar'    => $p['picture'] ?? null,
                    'google_id' => $p['sub'],
                ]
            );

            if ($user->wasRecentlyCreated) {
                try { MemberLog::create(['user_id' => $user->id]); } catch (\Throwable $e) {}
                try { \App\Helpers\WelcomeBot::sendWelcome($user); } catch (\Throwable $e) {}
            }

            Auth::login($user, true);
            session()->flash('ga_login', true);
            if ($user->wasRecentlyCreated) {
                session()->flash('ga_new_user', true);
            }

            return response()->json(['ok' => true, 'redirect' => route('aku')]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'error' => 'server'], 200);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}