<?php

namespace App\Http\Controllers;

use App\Models\AkuPost;
use App\Models\AkuLike;
use App\Models\Post;
use App\Models\GigPost;
use App\Models\Song;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * LANDING PAGE — langsung tampilkan fanbase (fanbase/aku.blade)
     * Guest bisa lihat 3 menit, setelah itu wajib login
     */
    public function fanbaseLanding()
    {
        // Ambil data seperti di AkuController::index()
        $posts = AkuPost::with(['user', 'comments.user', 'comments.replies.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        $likedIds = [];
        $likersByPost = collect();
        $isNewMember = false;

        if (Auth::check()) {
            $likedIds = AkuLike::where('user_id', Auth::id())
                ->pluck('aku_post_id')->toArray();

            $likersByPost = AkuLike::whereIn('aku_post_id', $posts->pluck('id'))
                ->with('user')
                ->latest()
                ->get()
                ->groupBy('aku_post_id')
                ->map(fn($likes) => $likes->take(5)->map(fn($l) => [
                    'name'   => $l->user->name ?? '?',
                    'avatar' => $l->user->avatar ?? null,
                ])->values());

            $isNewMember = Auth::user()->created_at
                && Auth::user()->created_at->diffInDays(now()) <= 7;
        }

        // Live Activity untuk side panel
        $liveActivity = $this->getLiveActivity();

        // Kirim flag ke view
        $isLandingMode = true;

        // ============================================================
        // PERBAIKAN: arahkan ke fanbase/aku.blade
        // ============================================================
        return view('fanbase.aku', compact(
            'posts',
            'likedIds',
            'likersByPost',
            'isNewMember',
            'liveActivity',
            'isLandingMode'
        ));
    }

    /**
     * Homepage lama — alias ke fanbaseLanding
     */
    public function index()
    {
        return $this->fanbaseLanding();
    }

    /**
     * Ambil aktivitas live untuk side panel
     */
    private function getLiveActivity()
    {
        $acts = collect();
        try {
            Post::with('user')->latest()->take(4)->get()->each(fn($p) => $acts->push([
                'icon' => '🎵',
                'user' => $p->user->name ?? 'Musisi',
                'text' => \Illuminate\Support\Str::limit($p->body, 65),
                'time' => $p->created_at->diffForHumans(),
                'ts'   => $p->created_at->timestamp,
            ]));
            GigPost::with('user')->where('status', 'open')->latest()->take(3)->get()->each(fn($g) => $acts->push([
                'icon' => '🥁',
                'user' => $g->user->name ?? 'Musisi',
                'text' => \Illuminate\Support\Str::limit($g->title, 65),
                'time' => $g->created_at->diffForHumans(),
                'ts'   => $g->created_at->timestamp,
            ]));
            return $acts->sortByDesc('ts')->take(5)->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * sitemap.xml dinamis
     */
    public function sitemap()
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $homeImg = htmlspecialchars(asset('images/Margonoandi.jpeg'));
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars(url('/')) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>daily</changefreq><priority>1.0</priority>' . "\n";
        $xml .= '    <image:image><image:loc>' . $homeImg . '</image:loc><image:title>Margonoandi</image:title></image:image>' . "\n";
        $xml .= '  </url>' . "\n";

        // Tools publik
        $toolRoutes = [
            'tools.index', 'tools.potong-lagu', 'tools.hapus-vokal',
            'tools.cover-art', 'tools.kartu-rilis', 'tools.countdown',
            'tools.edit-metadata', 'tools.chord-builder', 'tools.bpm-kalkulator',
            'tools.kalkulator-royalti', 'tools.rate-card', 'tools.transpose-kunci',
            'tools.epk', 'tools.setlist', 'tools.release-planner', 'gig.board'
        ];
        foreach ($toolRoutes as $toolRoute) {
            try {
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars(route($toolRoute)) . '</loc>' . "\n";
                $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . "\n";
                $xml .= '    <changefreq>monthly</changefreq><priority>0.7</priority>' . "\n";
                $xml .= '  </url>' . "\n";
            } catch (\Throwable $e) {}
        }

        // Materi musik
        try {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars(route('library.materi')) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq><priority>0.8</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        } catch (\Throwable $e) {}

        try {
            foreach (Article::orderBy('batch')->orderBy('id')->get(['slug','updated_at']) as $art) {
                try { $loc = route('library.materi.show', $art->slug); } catch (\Throwable $e) { continue; }
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
                if ($art->updated_at) $xml .= '    <lastmod>' . $art->updated_at->toDateString() . '</lastmod>' . "\n";
                $xml .= '    <changefreq>monthly</changefreq><priority>0.7</priority>' . "\n";
                $xml .= '  </url>' . "\n";
            }
        } catch (\Throwable $e) {}

        // Lagu
        try {
            $songs = Song::where('is_active', true)
                ->whereNotNull('slug')->where('slug', '!=', '')
                ->get(['slug', 'title', 'youtube_id', 'updated_at']);
            foreach ($songs as $s) {
                if (empty($s->slug)) continue;
                try { $loc = route('song.show', $s->slug); } catch (\Throwable $e) { continue; }
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
                if ($s->updated_at) $xml .= '    <lastmod>' . $s->updated_at->toDateString() . '</lastmod>' . "\n";
                $xml .= '    <changefreq>weekly</changefreq><priority>0.8</priority>' . "\n";
                if ($s->youtube_id) {
                    $imgLoc   = 'https://img.youtube.com/vi/' . $s->youtube_id . '/maxresdefault.jpg';
                    $imgTitle = htmlspecialchars($s->title . ' — Margonoandi');
                    $xml .= '    <image:image><image:loc>' . $imgLoc . '</image:loc><image:title>' . $imgTitle . '</image:title></image:image>' . "\n";
                }
                $xml .= '  </url>' . "\n";
            }
        } catch (\Throwable $e) {}

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type'           => 'application/xml; charset=UTF-8',
            'Cache-Control'          => 'no-transform, public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}