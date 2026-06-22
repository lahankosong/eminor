<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
use App\Models\BandPost;
use App\Models\GigPost;
use App\Models\Follow;
use App\Models\User;
use App\Helpers\NotifHelper;
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

        $bandPosts = collect();
        try {
            $bandPosts = BandPost::with('user')
                ->orderByDesc('urgent')->orderByDesc('created_at')->get();
        } catch (\Throwable $e) {}

        $gigPosts = collect();
        try {
            $gigPosts = GigPost::with('user')->orderByDesc('created_at')->get();
        } catch (\Throwable $e) {}

        $gigTypes = GigPost::types();

        return view('fanbase.musisi.index', compact('profiles', 'myProfile', 'bandPosts', 'gigPosts', 'gigTypes'));
    }

    // Form profil sendiri
    public function edit()
    {
        $profile = $this->myProfile();

        // Pre-fill peran dari onboarding bila profil belum punya roles
        $defaultRoles = '';
        if (!$profile || empty($profile->roles)) {
            $defaultRoles = User::rolesToCleanLabels(Auth::user()->roles ?? '');
        }

        return view('fanbase.musisi.edit', compact('profile', 'defaultRoles'));
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
            'tip_url'      => 'nullable|string|max:255',
            'photo'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:3072',
        ], [
            'photo.image' => 'File foto harus berupa gambar (JPG/PNG/WEBP).',
            'photo.max'   => 'Ukuran foto maksimal 3 MB.',
        ]);
        unset($data['photo']);
        $data['open_to_band']   = $request->boolean('open_to_band');
        $data['open_to_collab'] = $request->boolean('open_to_collab');
        $data['is_active']      = true;

        $profile = MusicianProfile::firstOrNew(['user_id' => Auth::id()]);

        $isLocal = fn ($p) => $p && !\Illuminate\Support\Str::startsWith($p, ['http://', 'https://']);

        // Hapus foto (kembali ke avatar Google)
        if ($request->boolean('remove_photo')) {
            if ($isLocal($profile->photo)) @unlink(public_path($profile->photo));
            $profile->photo = null;
        }

        // Upload foto baru → override avatar Google
        if ($request->hasFile('photo')) {
            if ($isLocal($profile->photo)) @unlink(public_path($profile->photo));
            $file     = $request->file('photo');
            $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = 'musisi_' . Auth::id() . '_' . time() . '.' . $ext;
            $file->move(public_path('images/avatars'), $filename);
            $profile->photo = 'images/avatars/' . $filename;
        }

        $profile->fill($data);
        $profile->user_id = Auth::id();
        $profile->save();

        return redirect()->route('musisi.index')
            ->with('success', 'Profil musisimu tersimpan.');
    }

    // Detail musisi — guest lihat TEASER publik (untuk link share), member lihat lengkap
    public function show($id)
    {
        $profile = MusicianProfile::with('user')->findOrFail($id);

        if (!Auth::check()) {
            $u     = $profile->user;
            $roles = $profile->rolesArray();
            $desc  = \Illuminate\Support\Str::limit(strip_tags((string) $profile->bio), 150)
                  ?: ('Musisi' . ($roles ? ' — ' . implode(', ', array_slice($roles, 0, 3)) : '') . ' di komunitas Margonoandi.');
            $seo = [
                'title'       => ($u->name ?? 'Musisi') . ' — Musisi di Margonoandi',
                'description' => $desc,
                'image'       => $profile->photoUrl(),
                'url'         => route('musisi.show', $profile->id),
                'type'        => 'profile',
            ];
            return view('fanbase.musisi.public', compact('profile', 'seo'));
        }

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
            'avatar'       => ($profile && $profile->photo)
                ? (\Illuminate\Support\Str::startsWith($profile->photo, ['http://', 'https://']) ? $profile->photo : asset($profile->photo))
                : $user->avatar,
            'is_self'      => (int) $userId === Auth::id(),
            'city'         => $user->city ?? null,
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
            try {
                NotifHelper::send($userId, Auth::id(), 'follow',
                    (Auth::user()->name ?? 'Seseorang') . ' mulai mengikutimu',
                    null, route('musisi.index'));
            } catch (\Throwable $e) {}
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
