<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $songs = Song::orderBy('track_number')->get();
        return view('admin.index', compact('songs'));
    }

    public function edit($id)
    {
        $song = Song::findOrFail($id);
        return view('admin.edit', compact('song'));
    }

    public function update(Request $request, $id)
    {
        // 1. Cari data song terlebih dahulu
        $song = Song::findOrFail($id);

        // 2. Validasi input termasuk audio file
        $request->validate([
            'title'         => 'required|string|max:255',
            'youtube_id'    => 'required|string|max:20',
            'track_number'  => 'required|integer',
            'key_signature' => 'nullable|string|max:10',
            'tempo'         => 'nullable|integer',
            'audio_file'    => 'nullable|file|mimes:mp3,wav,ogg|max:10240', // Max 10MB
        ]);

        // 3. Proses upload audio file
        if ($request->hasFile('audio_file')) {
            // Hapus file lama jika ada
            if ($song->audio_file && file_exists(public_path($song->audio_file))) {
                unlink(public_path($song->audio_file));
            }
            
            $file     = $request->file('audio_file');
            $filename = 'audio_' . $song->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('audio'), $filename);
            $audioPath = 'audio/' . $filename;
        } else {
            $audioPath = $song->audio_file;
        }

        // 4. Update data termasuk audio_path
        $song->update([
            'title'          => $request->title,
            'youtube_id'     => $request->youtube_id,
            'spotify_url'    => $request->spotify_url,
            'apple_music_url'=> $request->apple_music_url,
            'description'    => $request->description,
            'story_hook'     => $request->story_hook,
            'lyrics'         => $request->lyrics,
            'chords'         => $request->chords,
            'key_signature'  => $request->key_signature,
            'tempo'          => $request->tempo,
            'track_number'   => $request->track_number,
            'is_active'      => $request->has('is_active') ? 1 : 0,
            'featured'       => $request->has('featured') ? 1 : 0,
            'era'            => $request->era,
            'era_story'      => $request->era_story,
            'audio_file'     => $audioPath, // ← TAMBAHKAN INI
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Lagu "' . $song->title . '" berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'youtube_id' => 'required|string|max:20',
        ]);

        $lastTrack = Song::max('track_number') ?? 0;

        Song::create([
            'title'          => $request->title,
            'youtube_id'     => $request->youtube_id,
            'spotify_url'    => $request->spotify_url,
            'apple_music_url'=> $request->apple_music_url,
            'description'    => $request->description,
            'lyrics'         => $request->lyrics,
            'chords'         => $request->chords,
            'key_signature'  => $request->key_signature,
            'tempo'          => $request->tempo,
            'track_number'   => $lastTrack + 1,
            'is_active'      => 1,
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Lagu baru berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $song = Song::findOrFail($id);
        $title = $song->title;
        $song->delete();

        return redirect()->route('admin.index')
            ->with('success', 'Lagu "' . $title . '" berhasil dihapus.');
    }

    public function create()
    {
        return view('admin.create');
    }
}