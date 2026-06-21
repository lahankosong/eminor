<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\Post;
use App\Models\MemberLog;
use App\Models\PageVisit;
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

        // Top songs: paling banyak diputar
        $topSongs = $songs->where('is_active', 1)
            ->sortByDesc('play_count')->take(6)->values();
        $totalPlays = (int) $songs->sum('play_count');

        // Traffic: landing page vs masuk fanbase
        $traffic = ['homepage' => [], 'fanbase' => [], 'today_hp' => 0, 'today_fb' => 0, 'total_hp' => 0, 'total_fb' => 0];
        try {
            $traffic['today_hp'] = PageVisit::where('page', 'homepage')->whereDate('created_at', today())->count();
            $traffic['today_fb'] = PageVisit::where('page', 'fanbase')->whereDate('created_at', today())->count();
            $traffic['total_hp'] = PageVisit::where('page', 'homepage')->count();
            $traffic['total_fb'] = PageVisit::where('page', 'fanbase')->count();

            // Tren 7 hari: array ['date' => 'dd Mon', 'hp' => n, 'fb' => n]
            $days = collect(range(6, 0))->map(fn($d) => now()->subDays($d));
            $hpByDay = PageVisit::where('page', 'homepage')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')->pluck('total', 'date');
            $fbByDay = PageVisit::where('page', 'fanbase')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')->pluck('total', 'date');

            $traffic['trend'] = $days->map(fn($d) => [
                'label' => $d->locale('id')->isoFormat('D MMM'),
                'hp'    => $hpByDay[$d->toDateString()] ?? 0,
                'fb'    => $fbByDay[$d->toDateString()] ?? 0,
            ])->values()->toArray();
        } catch (\Throwable $e) {}

        return view('admin.index', compact(
            'songs', 'totalSongs', 'activeSongs',
            'totalMembers', 'dau', 'totalPosts',
            'recentActivity', 'topSongs', 'traffic', 'totalPlays'
        ));
    }

    /** Kirim pesan sambutan bot ke semua user lama yang belum menerimanya (idempoten). */
    public function blastWelcome()
    {
        $sent = 0;
        try {
            User::where('google_id', '!=', 'bot-margonoandi')
                ->orderBy('id')
                ->chunkById(100, function ($users) use (&$sent) {
                    foreach ($users as $u) {
                        try {
                            if (\App\Helpers\WelcomeBot::sendWelcome($u)) $sent++;
                        } catch (\Throwable $e) {}
                    }
                });
        } catch (\Throwable $e) {}

        return back()->with('success', "Blast sambutan selesai — terkirim ke {$sent} user baru (yang sudah pernah disambut dilewati).");
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