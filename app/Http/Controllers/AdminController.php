<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\Post;
use App\Models\MemberLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $songs = Song::orderBy('track_number')->get();

        // Metrics
        $totalSongs   = $songs->count();
        $activeSongs  = $songs->where('is_active', 1)->count();
        $totalMembers = 0;
        $dau          = 0;
        $totalPosts   = 0;

        try { $totalMembers = User::count(); } catch (\Throwable $e) {}
        try { $dau = User::where('last_seen', '>=', now()->subHours(24))->count(); } catch (\Throwable $e) {}
        try { $totalPosts = Post::count(); } catch (\Throwable $e) {}

        // Recent activity: merge posts + new members, sorted by time
        $recentActivity = collect();
        try {
            $recentPosts = Post::with('user')->latest()->take(8)->get()
                ->map(fn($p) => (object)[
                    'type'   => 'post',
                    'user'   => $p->user,
                    'text'   => Str::limit($p->body, 70),
                    'time'   => $p->created_at,
                    'time_h' => $p->created_at?->diffForHumans(),
                ]);

            $recentMembers = collect();
            try {
                $recentMembers = MemberLog::with('user')->latest()->take(5)->get()
                    ->map(fn($l) => (object)[
                        'type'   => 'member',
                        'user'   => $l->user,
                        'text'   => 'bergabung sebagai member baru',
                        'time'   => $l->created_at,
                        'time_h' => $l->created_at?->diffForHumans(),
                    ]);
            } catch (\Throwable $e) {}

            $recentActivity = $recentPosts->concat($recentMembers)
                ->sortByDesc('time')->take(10)->values();
        } catch (\Throwable $e) {}

        // Top songs: featured first, then active
        $topSongs = $songs->where('is_active', 1)
            ->sortByDesc('featured')->take(6)->values();

        return view('admin.index', compact(
            'songs', 'totalSongs', 'activeSongs',
            'totalMembers', 'dau', 'totalPosts',
            'recentActivity', 'topSongs'
        ));
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