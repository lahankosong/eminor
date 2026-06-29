<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        // Live Community feed — fallback ke placeholder jika tabel belum ada
        $liveActivity = collect();
        try {
            $acts = collect();
            Post::with('user')->latest()->take(4)->get()->each(fn($p) => $acts->push([
                'icon' => '🎵',
                'user' => $p->user->name ?? 'Musisi',
                'text' => \Illuminate\Support\Str::limit($p->body, 65),
                'time' => $p->created_at->diffForHumans(),
                'ts'   => $p->created_at->timestamp,
            ]));
            \App\Models\GigPost::with('user')->where('status', 'open')->latest()->take(3)->get()->each(fn($g) => $acts->push([
                'icon' => '🥁',
                'user' => $g->user->name ?? 'Musisi',
                'text' => \Illuminate\Support\Str::limit($g->title, 65),
                'time' => $g->created_at->diffForHumans(),
                'ts'   => $g->created_at->timestamp,
            ]));
            $liveActivity = $acts->sortByDesc('ts')->take(5)->values();
        } catch (\Throwable $e) {}

        return view('aku', compact('liveActivity'));
    }

    /** sitemap.xml dinamis: homepage + semua lagu aktif, termasuk image:image untuk Google Image. */
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

        // Tools publik (SEO): pemotong lagu + penghapus vokal — gratis, client-side
        foreach (['tools.index', 'tools.potong-lagu', 'tools.hapus-vokal', 'tools.cover-art', 'tools.kartu-rilis', 'tools.countdown', 'tools.edit-metadata', 'tools.chord-builder', 'tools.bpm-kalkulator', 'tools.kalkulator-royalti', 'tools.rate-card', 'tools.transpose-kunci', 'tools.epk', 'tools.setlist', 'tools.release-planner', 'gig.board'] as $toolRoute) {
            try {
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars(route($toolRoute)) . '</loc>' . "\n";
                $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . "\n";
                $xml .= '    <changefreq>monthly</changefreq><priority>0.7</priority>' . "\n";
                $xml .= '  </url>' . "\n";
            } catch (\Throwable $e) {}
        }

        // Materi musik: halaman index + tiap artikel
        try {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars(route('library.materi')) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq><priority>0.8</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        } catch (\Throwable $e) {}

        try {
            foreach (\App\Models\Article::orderBy('batch')->orderBy('id')->get(['slug','updated_at']) as $art) {
                try { $loc = route('library.materi.show', $art->slug); } catch (\Throwable $e) { continue; }
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
                if ($art->updated_at) $xml .= '    <lastmod>' . $art->updated_at->toDateString() . '</lastmod>' . "\n";
                $xml .= '    <changefreq>monthly</changefreq><priority>0.7</priority>' . "\n";
                $xml .= '  </url>' . "\n";
            }
        } catch (\Throwable $e) {}

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