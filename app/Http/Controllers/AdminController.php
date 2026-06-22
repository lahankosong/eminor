<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\User;
use App\Models\Post;
use App\Models\MemberLog;
use App\Models\PageVisit;
use App\Models\AiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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

    /* ===================== ANALISIS KOMUNITAS (base data AI internal) ===================== */

    public function insights()
    {
        $src  = $this->gatherCommunityText(200);
        $freq = $this->topKeywords($src['texts'], 30);
        $ai = null; $seoTips = null;
        try { $ai = cache('insights_ai'); } catch (\Throwable $e) {}
        try { $seoTips = cache('insights_seo'); } catch (\Throwable $e) {}

        return view('admin.insights', [
            'freq'    => $freq,
            'counts'  => $src['counts'],
            'total'   => count($src['texts']),
            'ai'      => $ai,
            'seoTips' => $seoTips,
            'hasAi'   => AiProvider::where('enabled', true)->whereNotNull('api_key')->where('kind', 'text')->exists()
                      || AiProvider::where('enabled', true)->whereNotNull('api_key')->where('base_url', 'like', '%deepseek%')->exists(),
        ]);
    }

    public function analyzeInsights()
    {
        $provider = AiProvider::where('enabled', true)->whereNotNull('api_key')->where('base_url', 'like', '%deepseek%')->orderBy('id')->first()
                 ?: AiProvider::where('enabled', true)->whereNotNull('api_key')->where('kind', 'text')->orderBy('id')->first();
        if (!$provider || !$provider->api_key) {
            return back()->with('error', 'Belum ada provider AI teks aktif. Pasang DeepSeek di Pengaturan AI dulu.');
        }

        $src = $this->gatherCommunityText(150);
        if (empty($src['texts'])) {
            return back()->with('error', 'Belum ada postingan/komentar untuk dianalisa.');
        }

        $corpus = Str::limit(implode("\n", array_map(fn ($t) => '- ' . trim($t), $src['texts'])), 8000);
        $system = implode("\n", [
            'Kamu analis komunitas untuk fanbase musik Margonoandi.',
            'Dari kumpulan POSTINGAN & KOMENTAR publik komunitas di bawah, buat analisis ringkas dalam Bahasa Indonesia yang rapi (boleh pakai penomoran biasa).',
            'Struktur keluaran:',
            '1) TOPIK YANG SEDANG DIBICARAKAN — 5-7 tema utama (singkat per poin).',
            '2) SUASANA/MOOD komunitas secara umum (1-2 kalimat).',
            '3) IDE KONTEN/LAGU — 3 ide relevan dengan obrolan mereka.',
            'Padat, tanpa basa-basi, BERDASARKAN DATA yang diberikan saja. Jangan mengarang. Jangan pakai tabel.',
        ]);

        $text = '';
        try {
            if ($provider->format === 'anthropic') {
                $resp = Http::timeout(60)->withHeaders([
                    'x-api-key' => $provider->api_key, 'anthropic-version' => '2023-06-01', 'content-type' => 'application/json',
                ])->post(rtrim($provider->base_url, '/') . '/messages', [
                    'model' => $provider->model, 'max_tokens' => 900, 'system' => $system,
                    'messages' => [['role' => 'user', 'content' => $corpus]],
                ]);
                $text = $resp->successful() ? (string) $resp->json('content.0.text', '') : '';
            } else {
                $resp = Http::timeout(60)->withToken($provider->api_key)->post(rtrim($provider->base_url, '/') . '/chat/completions', [
                    'model' => $provider->model ?: 'deepseek-chat',
                    'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $corpus]],
                    'temperature' => 0.5, 'max_tokens' => 900,
                ]);
                $text = $resp->successful() ? (string) $resp->json('choices.0.message.content', '') : '';
            }
        } catch (\Throwable $e) {
            $text = '';
        }

        if (trim($text) === '') {
            return back()->with('error', 'Gagal memanggil AI. Cek API key / koneksi provider.');
        }

        try {
            cache(['insights_ai' => [
                'text' => trim($text),
                'at'   => now()->format('d M Y H:i'),
                'n'    => count($src['texts']),
            ]], now()->addDays(7));
        } catch (\Throwable $e) {}

        return back()->with('success', 'Analisis AI selesai.');
    }

    /** Saran SEO (Fase 3): dari kata kunci komunitas + lagu terpopuler -> AI bikin saran siap pakai. */
    public function seoSuggest()
    {
        $src = $this->gatherCommunityText(150);
        $kw  = $this->topKeywords($src['texts'], 25);
        $topSongs  = Song::where('is_active', true)->orderByDesc('play_count')->take(10)->get(['title', 'era', 'play_count']);
        $allTitles = Song::where('is_active', true)->orderBy('track_number')->pluck('title')->implode(', ');

        if (empty($kw) && $topSongs->isEmpty()) {
            return back()->with('error', 'Belum ada data (komentar/lagu) sebagai dasar saran.');
        }

        $data = 'KATA KUNCI KOMUNITAS (sering muncul): ' . (implode(', ', array_keys($kw)) ?: '-') . "\n\n"
              . "LAGU PALING DIPUTAR:\n" . ($topSongs->map(fn ($s) => '- ' . $s->title . ($s->era ? ' (' . $s->era . ')' : '') . ' - ' . (int) $s->play_count . 'x')->implode("\n") ?: '-') . "\n\n"
              . 'SEMUA JUDUL LAGU: ' . $allTitles;

        $system = implode("\n", [
            'Kamu SEO specialist untuk situs fanbase musik Margonoandi (margonoandi.my.id): lagu, lirik, chord, komunitas musisi Indonesia.',
            'Dari DATA INTERNAL di bawah, buat SARAN SEO PRAKTIS (Bahasa Indonesia) yang siap pakai. Struktur:',
            '1) META DESCRIPTION HOMEPAGE - 3 variasi (maks ~155 karakter, menarik).',
            '2) IDE KONTEN/HALAMAN - 5 ide (judul + 1 kalimat) yang menargetkan apa yang DICARI/DIBICARAKAN audiens.',
            '3) LAGU LAYAK DIDORONG - pilih dari lagu populer, beri angle/judul SEO-nya.',
            '4) FRASA KUNCI ALAMI - 8-12 frasa untuk disisipkan natural di judul/teks (bukan meta keyword).',
            'Pakai DATA saja, JANGAN mengarang judul lagu di luar daftar. Padat, tanpa tabel.',
        ]);

        $text = trim($this->aiTextComplete($system, $data, 1000));
        if ($text === '') {
            return back()->with('error', 'Belum ada provider AI aktif atau gagal memanggil AI. Pasang DeepSeek di Pengaturan AI.');
        }

        try {
            cache(['insights_seo' => ['text' => $text, 'at' => now()->format('d M Y H:i')]], now()->addDays(7));
        } catch (\Throwable $e) {}

        return back()->with('success', 'Saran SEO dibuat.');
    }

    /** Panggil provider AI teks (DeepSeek/OpenAI-compatible/Anthropic). Return '' jika gagal. */
    private function aiTextComplete(string $system, string $user, int $max = 900): string
    {
        $p = AiProvider::where('enabled', true)->whereNotNull('api_key')->where('base_url', 'like', '%deepseek%')->orderBy('id')->first()
           ?: AiProvider::where('enabled', true)->whereNotNull('api_key')->where('kind', 'text')->orderBy('id')->first();
        if (!$p || !$p->api_key) return '';

        try {
            if ($p->format === 'anthropic') {
                $resp = Http::timeout(60)->withHeaders([
                    'x-api-key' => $p->api_key, 'anthropic-version' => '2023-06-01', 'content-type' => 'application/json',
                ])->post(rtrim($p->base_url, '/') . '/messages', [
                    'model' => $p->model, 'max_tokens' => $max, 'system' => $system,
                    'messages' => [['role' => 'user', 'content' => $user]],
                ]);
                return $resp->successful() ? (string) $resp->json('content.0.text', '') : '';
            }
            $resp = Http::timeout(60)->withToken($p->api_key)->post(rtrim($p->base_url, '/') . '/chat/completions', [
                'model' => $p->model ?: 'deepseek-chat',
                'messages' => [['role' => 'system', 'content' => $system], ['role' => 'user', 'content' => $user]],
                'temperature' => 0.6, 'max_tokens' => $max,
            ]);
            return $resp->successful() ? (string) $resp->json('choices.0.message.content', '') : '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** Kumpulkan teks publik komunitas (Aku/Kita post + komentar). Chat & catatan privat DIKECUALIKAN. */
    private function gatherCommunityText(int $limit): array
    {
        $texts  = [];
        $counts = ['Post Aku' => 0, 'Post Kita' => 0, 'Komentar Aku' => 0, 'Komentar Kita' => 0];
        $grab = function ($class, $label) use (&$texts, &$counts, $limit) {
            try {
                $rows = $class::latest('id')->limit($limit)->pluck('body');
                foreach ($rows as $b) {
                    $b = trim((string) $b);
                    if ($b !== '') { $texts[] = $b; $counts[$label]++; }
                }
            } catch (\Throwable $e) {}
        };
        $grab(\App\Models\AkuPost::class,    'Post Aku');
        $grab(\App\Models\Post::class,       'Post Kita');
        $grab(\App\Models\AkuComment::class, 'Komentar Aku');
        $grab(\App\Models\PostComment::class,'Komentar Kita');
        return ['texts' => $texts, 'counts' => $counts];
    }

    /** Frekuensi kata kunci sederhana (tanpa AI) sebagai "base data" mentah. */
    private function topKeywords(array $texts, int $n): array
    {
        $stop = ['yang','dari','dan','atau','ini','itu','aku','kamu','dia','kita','saya','gua','gue','nya','aja','sih','kok','deh','dong','nih','gak','nggak','tidak','udah','sudah','belum','juga','biar','buat','sama','dengan','untuk','pada','ada','jadi','kalo','kalau','karena','tapi','tetapi','lebih','banget','bisa','mau','akan','lagi','masih','kayak','seperti','semua','apa','siapa','kenapa','gimana','bikin','terus','trus','dah','pun','agar','oleh','dalam','para','hai','halo','iya','engga','tau','tahu','orang','banyak','sangat','sekali','kak','min','bang','guys'];
        $count = [];
        foreach ($texts as $t) {
            $t = mb_strtolower(strip_tags((string) $t));
            $t = preg_replace('/[^\p{L}\s]+/u', ' ', $t);
            foreach (preg_split('/\s+/', $t, -1, PREG_SPLIT_NO_EMPTY) as $w) {
                if (mb_strlen($w) < 4) continue;
                if (in_array($w, $stop, true)) continue;
                $count[$w] = ($count[$w] ?? 0) + 1;
            }
        }
        arsort($count);
        return array_slice($count, 0, $n, true);
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