<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Song;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect('/aku');
        }
        $songs = Song::where('is_active', true)
                     ->orderBy('track_number')
                     ->get();

        $featuredSong = $songs->where('youtube_id', 'TG8oAcVRnzA')->first()
                     ?? $songs->first();

        $ctaSongs = Song::where('featured', true)
                        ->where('is_active', true)
                        ->take(3)
                        ->get();

        $settings = SiteSetting::all()->keyBy('key')->map(fn($s) => $s->value);

        $artistName = $settings['artist_name'] ?? 'Margonoandi';
        $heroDesc   = \Illuminate\Support\Str::limit(strip_tags($settings['hero_story'] ?? ''), 160)
                    ?: 'Dengarkan lagu Margonoandi, belajar chord & tuner, dan gabung komunitas musisi Indonesia.';
        $sameAs = array_values(array_filter([
            $settings['spotify_url'] ?? null,
            $settings['youtube_url'] ?? null,
            $settings['apple_music_url'] ?? null,
        ]));

        $seo = [
            'title'       => 'Margonoandi — Lagu, Chord & Komunitas Musik Indonesia',
            'description' => $heroDesc,
            'image'       => asset('images/Margonoandi.jpeg'),
            'url'         => url('/'),
            'type'        => 'music.musician',
            'schema'      => [
                '@context' => 'https://schema.org',
                '@graph'   => [
                    [
                        '@type'       => 'WebSite',
                        'name'        => $artistName,
                        'url'         => url('/'),
                        'description' => $heroDesc,
                        'inLanguage'  => 'id',
                    ],
                    [
                        '@type'       => 'MusicGroup',
                        'name'        => $artistName,
                        'url'         => url('/'),
                        'image'       => asset('images/Margonoandi.jpeg'),
                        'genre'       => ['Indie', 'Pop Indonesia'],
                        'description' => \Illuminate\Support\Str::limit(strip_tags($settings['hero_story'] ?? ''), 250),
                        'sameAs'      => $sameAs,
                        'track'       => $songs->take(5)->map(fn($s) => [
                            '@type' => 'MusicRecording',
                            'name'  => $s->title,
                            'url'   => route('song.show', $s->slug),
                        ])->values()->toArray(),
                    ],
                    [
                        '@type'           => 'ItemList',
                        'name'            => 'Lagu-lagu Margonoandi',
                        'url'             => url('/'),
                        'itemListElement' => $songs->take(10)->values()->map(fn($s, $i) => [
                            '@type'    => 'ListItem',
                            'position' => $i + 1,
                            'name'     => $s->title,
                            'url'      => route('song.show', $s->slug),
                        ])->toArray(),
                    ],
                ],
            ],
        ];

        // Teaser musisi untuk landing (HANYA field publik aman: nama/peran/lokasi/genre/bio.
        // Tanpa kontak/portofolio/tip — detail lengkap wajib login via /musisi/{id} yang auth-only).
        $musicians = collect();
        try {
            $musicians = \App\Models\MusicianProfile::with('user')
                ->where('is_active', true)
                ->whereNotNull('roles')->where('roles', '!=', '')
                ->latest('updated_at')->take(8)->get()
                ->map(fn ($p) => [
                    'name'     => $p->user->name ?? 'Musisi',
                    'avatar'   => $p->photoUrl(),
                    'roles'    => $p->rolesArray(),
                    'skill'    => $p->skill_level,
                    'location' => $p->location,
                    'genres'   => $p->genresArray(),
                    'bio'      => \Illuminate\Support\Str::limit(strip_tags((string) $p->bio), 130),
                ])->values();
        } catch (\Throwable $e) {}

        $previewPosts = collect();
        try {
            $previewPosts = Post::with('user')
                ->latest()
                ->take(3)
                ->get();
        } catch (\Throwable $e) {}

        return view('home', compact('songs', 'featuredSong', 'ctaSongs', 'settings', 'seo', 'musicians', 'previewPosts'));
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