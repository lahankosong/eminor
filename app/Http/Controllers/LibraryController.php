<?php

namespace App\Http\Controllers;

use App\Models\Song;

class LibraryController extends Controller
{
    // Kurasi manual: genre, mood, theme per slug
    // Update di sini kalau ada lagu baru atau ingin ubah mood/genre
    private array $meta = [
        'bersamamu'             => ['genre' => 'Acoustic Pop',   'mood' => 'emosional',    'theme' => 'Menemukan kebersamaan di tengah jarak'],
        'peri-kecil'            => ['genre' => 'Indie Folk',     'mood' => 'emosional',    'theme' => 'Cerita cinta yang lembut dan tulus'],
        'meski-tanpa-aku'       => ['genre' => 'Acoustic Ballad','mood' => 'emosional',    'theme' => 'Ikhlas melepas demi kebaikan orang lain'],
        'sampai-lupa-mandi'     => ['genre' => 'Indie Pop',      'mood' => 'energetik',    'theme' => 'Jatuh cinta yang bikin lupa segalanya'],
        'hanya-kau-satu'        => ['genre' => 'Pop Rock',       'mood' => 'emosional',    'theme' => 'Kesetiaan dan cinta yang tak tergantikan'],
        'kawan-peri-kecil'      => ['genre' => 'Acoustic Pop',   'mood' => 'emosional',    'theme' => 'Persahabatan sejati yang menemani'],
        'masihkah-ada'          => ['genre' => 'Indie Folk',     'mood' => 'introspektif', 'theme' => 'Kerinduan dan pertanyaan yang tersisa'],
        'angan-tersisa'         => ['genre' => 'Acoustic Ballad','mood' => 'introspektif', 'theme' => 'Mimpi yang belum selesai, kenangan yang menggantung'],
        'apa-kau-gila'          => ['genre' => 'Pop Rock',       'mood' => 'energetik',    'theme' => 'Kejutan dan keajaiban cinta'],
        'dan-taklukan-duniamu'  => ['genre' => 'Rock Anthem',    'mood' => 'energetik',    'theme' => 'Semangat meraih mimpi dan menaklukkan dunia'],
        'opo-kowe-ra-kroso'     => ['genre' => 'Jawa Pop',       'mood' => 'emosional',    'theme' => 'Rindu dan perasaan dalam bahasa Jawa'],
        'selamat-ulang-tahun'   => ['genre' => 'Acoustic Pop',   'mood' => 'emosional',    'theme' => 'Ucapan penuh makna di hari yang berarti'],
        'sadarkah-engkau'       => ['genre' => 'Indie Pop',      'mood' => 'introspektif', 'theme' => 'Permohonan untuk diperhatikan dan disadari'],
        'renungkan-baiknya'     => ['genre' => 'Acoustic Folk',  'mood' => 'introspektif', 'theme' => 'Refleksi dan perenungan tentang kehidupan'],
        'jiwaku'                => ['genre' => 'Pop Rock',       'mood' => 'emosional',    'theme' => 'Ungkapan terdalam dari dalam jiwa'],
        'jiwaku-japanese-version' => ['genre' => 'J-Pop',        'mood' => 'emosional',    'theme' => 'Jiwaku dalam nuansa dan bahasa Jepang'],
    ];

    public function index()
    {
        $dbSongs = Song::where('is_active', true)
            ->orderBy('track_number')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'spotify_url', 'story_hook', 'description', 'era']);

        $songs = $dbSongs->map(function ($s) {
            $meta = $this->meta[$s->slug] ?? ['genre' => 'Indie', 'mood' => 'emosional', 'theme' => ''];
            $theme = $meta['theme'] ?: ($s->story_hook ?: $s->description ?: '');

            // Tahun: ambil 4 digit dari era, fallback ke created_at
            $year = '';
            if ($s->era && preg_match('/\b(20\d{2})\b/', $s->era, $m)) {
                $year = $m[1];
            }

            return [
                'title'   => $s->title,
                'slug'    => $s->slug,
                'year'    => $year,
                'genre'   => $meta['genre'],
                'mood'    => $meta['mood'],
                'theme'   => $theme,
                'spotify' => $s->spotify_url ?: null,
            ];
        })->toArray();

        $moodLabels = [
            'energetik'    => 'Energetik',
            'emosional'    => 'Emosional',
            'introspektif' => 'Introspektif',
            'spiritual'    => 'Spiritual',
        ];

        $genres = collect($songs)->pluck('genre')->unique()->count();

        $seo = [
            'title'       => 'Diskografi Margonoandi — Semua Lagu di Spotify & Apple Music',
            'description' => 'Jelajahi semua lagu Margonoandi: pop rock, indie acoustic, Jawa pop, acoustic ballad. Filter berdasarkan mood — energetik, emosional, atau introspektif.',
            'url'         => url('/library'),
            'image'       => asset('images/Margonoandi.jpeg'),
            'schema'      => [
                '@context' => 'https://schema.org',
                '@graph'   => [
                    [
                        '@type'       => 'MusicGroup',
                        'name'        => 'Margonoandi',
                        'url'         => url('/library'),
                        'description' => 'Musisi indie Indonesia dengan lagu pop rock, acoustic, Jawa pop, dan indie folk.',
                        'genre'       => ['Pop Rock', 'Indie', 'Acoustic', 'Jawa Pop'],
                    ],
                    [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => url('/')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Library', 'item' => url('/library')],
                        ],
                    ],
                ],
            ],
        ];

        return view('library.index', compact('songs', 'moodLabels', 'genres', 'seo'));
    }
}
