<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberLog;
use Illuminate\Support\Facades\Auth;
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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}