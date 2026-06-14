<?php

namespace App\Http\Controllers;

use App\Models\MusicianProfile;
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

    protected function myProfile()
    {
        try {
            return MusicianProfile::where('user_id', Auth::id())->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
