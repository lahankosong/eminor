<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MusicianController extends Controller
{
    // Direktori musisi
    public function index()
    {
        $profiles = collect();
        try {
            $profiles = MusicianProfile::with('user')
                ->where('is_active', true)
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            // tabel belum ada — jalankan fixdb.php
        }
        $myProfile = $this->myProfile();
        return view('fanbase.musisi.index', compact('profiles', 'myProfile'));
    }

    // Form profil sendiri
    public function edit()
    {
        $profile = $this->myProfile();
        return view('fanbase.musisi.edit', compact('profile'));
    }

    // Simpan profil sendiri
    public function save(Request $request)
    {
        $data = $request->validate([
            'roles'        => 'nullable|string|max:255',
            'skill_level'  => 'nullable|string|max:30',
            'genres'       => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:120',
            'bio'          => 'nullable|string|max:2000',
            'looking_for'  => 'nullable|string|max:255',
            'spotify_url'  => 'nullable|string|max:255',
            'youtube_url'  => 'nullable|string|max:255',
            'instagram'    => 'nullable|string|max:120',
        ]);
        $data['open_to_band']   = $request->boolean('open_to_band');
        $data['open_to_collab'] = $request->boolean('open_to_collab');
        $data['is_active']      = true;

        MusicianProfile::updateOrCreate(['user_id' => Auth::id()], $data);

        return redirect()->route('musisi.index')
            ->with('success', 'Profil musisimu tersimpan.');
    }

    // Detail musisi
    public function show($id)
    {
        $profile = MusicianProfile::with('user')->findOrFail($id);
        return view('fanbase.musisi.show', compact('profile'));
    }

    // Data popup (klik badge di Kita) — musisi ATAU audiens/calon fans
    public function card($userId)
    {
        $user = User::find($userId);
        if (!$user) return response()->json(['error' => 'not found'], 404);

        $profile = null;
        $isFollowing = false;
        $followers = 0;
        try {
            $profile = MusicianProfile::where('user_id', $userId)->first();
            $isFollowing = Follow::where('follower_id', Auth::id())->where('following_id', $userId)->exists();
            $followers = Follow::where('following_id', $userId)->count();
        } catch (\Throwable $e) {}

        return response()->json([
            'user_id'      => (int) $userId,
            'name'         => $user->name,
            'avatar'       => $user->avatar,
            'is_self'      => (int) $userId === Auth::id(),
            'is_musician'  => (bool) $profile,
            'profile_id'   => $profile->id ?? null,
            'roles'        => $profile ? $profile->rolesArray() : [],
            'genres'       => $profile ? $profile->genresArray() : [],
            'skill_level'  => $profile->skill_level ?? null,
            'location'     => $profile->location ?? null,
            'looking_for'  => $profile->looking_for ?? null,
            'is_following' => $isFollowing,
            'followers'    => $followers,
        ]);
    }

    // Toggle follow/ikuti
    public function toggleFollow($userId)
    {
        if ((int) $userId === Auth::id()) {
            return response()->json(['error' => 'Tidak bisa mengikuti diri sendiri'], 422);
        }
        if (!User::whereKey($userId)->exists()) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        $existing = Follow::where('follower_id', Auth::id())->where('following_id', $userId)->first();
        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Follow::create(['follower_id' => Auth::id(), 'following_id' => $userId]);
            $following = true;
        }
        $followers = Follow::where('following_id', $userId)->count();
        return response()->json(['following' => $following, 'followers' => $followers]);
    }

    protected function myProfile()
    {
        try {
            return MusicianProfile::where('user_id', Auth::id())->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
