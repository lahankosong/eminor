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
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name'   => $googleUser->getName(),
                    'email'  => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                    'google_id' => $googleUser->getId(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                MemberLog::create(['user_id' => $user->id]);
            }

            Auth::login($user, true);

            return redirect()->route('aku');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Login gagal, silakan coba lagi.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}