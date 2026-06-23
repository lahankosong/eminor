<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ToolController extends Controller
{
    /** Bangun $seo lengkap: title/desc/url + OG image per-tool + schema @graph (node utama + BreadcrumbList). */
    private function toolSeo(string $title, string $desc, string $slug, string $bcName, array $mainNode): array
    {
        $url = $slug === '' ? url('/tools') : url('/tools/' . $slug);
        $og  = $slug === '' ? 'studio' : $slug;

        $crumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda',     'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Alat Gratis', 'item' => route('tools.index')],
        ];
        if ($bcName !== '') {
            $crumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $bcName, 'item' => $url];
        }

        return [
            'title'       => $title,
            'description' => $desc,
            'url'         => $url,
            'image'       => asset('images/og/' . $og . '.png'),
            'schema'      => ['@context' => 'https://schema.org', '@graph' => [
                $mainNode,
                ['@type' => 'BreadcrumbList', 'itemListElement' => $crumbs],
            ]],
        ];
    }

    private function appNode(string $name, string $url, string $desc, string $category): array
    {
        return [
            '@type' => 'WebApplication', 'name' => $name, 'url' => $url, 'description' => $desc,
            'applicationCategory' => $category, 'operatingSystem' => 'Any',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
        ];
    }

    public function audioCutter()
    {
        $url = url('/tools/potong-lagu');
        $seo = $this->toolSeo(
            'Pemotong Lagu Online Gratis — Potong MP3, WAV, OGG di Browser',
            'Potong bagian lagu favoritmu secara online, gratis, tanpa upload ke server. Mendukung MP3, WAV, OGG, FLAC. Hasil langsung diunduh ke perangkatmu.',
            'potong-lagu', 'Pemotong Lagu',
            $this->appNode('Pemotong Lagu Online', $url, 'Potong bagian lagu favoritmu, gratis di browser tanpa upload.', 'MultimediaApplication')
        );
        return view('tools.audio-cutter', compact('seo'));
    }

    public function vocalRemover()
    {
        $url = url('/tools/hapus-vokal');
        $seo = $this->toolSeo(
            'Penghapus Vokal Online Gratis — Bikin Karaoke / Minus One di Browser',
            'Hapus vokal dari lagu untuk bikin karaoke / minus one, langsung di browser tanpa upload. Pisahkan instrumen & vokal, unduh MP3/WAV. Gratis, tanpa install.',
            'hapus-vokal', 'Hapus Vokal (Karaoke)',
            $this->appNode('Penghapus Vokal Online (Karaoke Maker)', $url, 'Hapus vokal lagu untuk karaoke/minus one, gratis di browser tanpa upload.', 'MultimediaApplication')
        );
        return view('tools.vocal-remover', compact('seo'));
    }

    public function coverMaker()
    {
        $url = url('/tools/cover-art');
        $seo = $this->toolSeo(
            'Buat Cover Lagu / Album Online Gratis — Cover Art Maker 1:1 (3000px)',
            'Bikin cover art lagu/album persegi 1:1 untuk Spotify, Apple Music, YouTube — resolusi 1600/2000/3000 px. Tambah judul & nama artis, atur foto, unduh PNG/JPG. Gratis, tanpa upload.',
            'cover-art', 'Buat Cover',
            $this->appNode('Cover Art Maker (Buat Cover Lagu)', $url, 'Buat cover art lagu/album 1:1 (3000px) untuk platform streaming, gratis tanpa upload.', 'DesignApplication')
        );
        return view('tools.cover-maker', compact('seo'));
    }

    public function releaseCard()
    {
        $url = url('/tools/kartu-rilis');
        $seo = $this->toolSeo(
            'Kartu Promo Rilis Lagu Online Gratis — Pra-Rilis, Rilis & Countdown',
            'Buat kartu promo rilis lagu untuk Instagram/WhatsApp: pra-rilis (countdown hari rilis), rilis (out now + link/QR platform), pasca-rilis. Feed 1:1 & Story 9:16. Gratis, tanpa upload.',
            'kartu-rilis', 'Kartu Promo Rilis',
            $this->appNode('Kartu Promo Rilis Lagu', $url, 'Buat kartu promo rilis & countdown lagu untuk media sosial, gratis tanpa upload.', 'DesignApplication')
        );
        return view('tools.release-card', compact('seo'));
    }

    public function countdown(Request $request)
    {
        $url = url('/tools/countdown');
        $j = Str::limit(trim((string) $request->query('j', '')), 60, '');
        $a = Str::limit(trim((string) $request->query('a', '')), 40, '');
        $d = trim((string) $request->query('d', ''));
        $hasParams = $d !== '';

        if ($hasParams) {
            // Mode display (link dibagikan): OG dinamis untuk preview cantik di WA, noindex (cegah duplikat tak terhingga)
            $title = ($j !== '' ? $j : 'Rilis Baru') . ($a !== '' ? ' — ' . $a : '') . ' · Hitung Mundur Rilis';
            $desc  = 'Hitung mundur rilis ' . ($j !== '' ? '“' . $j . '”' : 'lagu') . ($a !== '' ? ' oleh ' . $a : '')
                   . '. Buka untuk lihat countdown langsung & dengarkan saat rilis.';
            $seo = [
                'title' => $title, 'description' => $desc, 'url' => $url, 'type' => 'website',
                'image' => asset('images/og/countdown.png'), 'robots' => 'noindex, follow',
            ];
        } else {
            $seo = $this->toolSeo(
                'Buat Countdown Rilis Lagu — Link Hitung Mundur untuk Bio & Story',
                'Bikin link hitung mundur rilis lagu yang berdetak real-time untuk bio Instagram / link-in-bio / WhatsApp. Saat rilis otomatis jadi "Out Now". Gratis, tanpa daftar.',
                'countdown', 'Countdown Rilis',
                $this->appNode('Countdown Rilis Lagu (Generator Link Hitung Mundur)', $url, 'Buat link hitung mundur rilis lagu real-time untuk media sosial, gratis tanpa daftar.', 'UtilitiesApplication')
            );
        }

        return view('tools.countdown', compact('seo', 'hasParams'));
    }

    public function hub()
    {
        $tools = [
            ['icon' => '✂️', 'name' => 'Pemotong Lagu Online',       'desc' => 'Potong bagian lagu (MP3/WAV/OGG) langsung di browser.',         'route' => 'tools.potong-lagu'],
            ['icon' => '🎤', 'name' => 'Penghapus Vokal (Karaoke)',   'desc' => 'Pisah instrumen & vokal untuk karaoke / minus one.',            'route' => 'tools.hapus-vokal'],
            ['icon' => '🎨', 'name' => 'Buat Cover Lagu / Album',      'desc' => 'Cover art 1:1 hingga 3000px untuk Spotify, Apple, YouTube.',     'route' => 'tools.cover-art'],
            ['icon' => '🚀', 'name' => 'Kartu Promo Rilis',           'desc' => '3 fase (pra/rilis/pasca) + QR/platform, feed 1:1 & story 9:16.', 'route' => 'tools.kartu-rilis'],
            ['icon' => '⏳', 'name' => 'Countdown Rilis',             'desc' => 'Link hitung mundur real-time untuk bio Instagram / story.',      'route' => 'tools.countdown'],
        ];
        $items = [];
        foreach ($tools as $i => $t) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $t['name'], 'url' => route($t['route'])];
        }
        $seo = $this->toolSeo(
            'Alat Gratis untuk Musisi — Potong Lagu, Karaoke, Cover & Promo Rilis',
            'Kumpulan alat gratis untuk musisi: pemotong lagu, penghapus vokal (karaoke), pembuat cover art 1:1, kartu promo rilis & countdown rilis. Semua di browser, tanpa upload, tanpa daftar.',
            '', '',
            ['@type' => 'ItemList', 'name' => 'Alat Gratis Musisi — Margonoandi', 'itemListElement' => $items]
        );
        return view('tools.index', compact('seo', 'tools'));
    }
}
