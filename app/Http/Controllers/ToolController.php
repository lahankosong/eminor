<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ToolController extends Controller
{
    public function audioCutter()
    {
        $canonical = url('/tools/potong-lagu');
        return view('tools.audio-cutter', [
            'seo' => [
                'title'       => 'Pemotong Lagu Online Gratis — Potong MP3, WAV, OGG di Browser',
                'description' => 'Potong bagian lagu favoritmu secara online, gratis, tanpa upload ke server. Mendukung MP3, WAV, OGG, FLAC. Hasil langsung diunduh ke perangkatmu.',
                'url'         => $canonical,
                'schema'      => [
                    '@context'            => 'https://schema.org',
                    '@type'               => 'WebApplication',
                    'name'                => 'Pemotong Lagu Online',
                    'url'                 => $canonical,
                    'description'         => 'Potong bagian lagu favoritmu secara online, gratis, tanpa upload ke server.',
                    'applicationCategory' => 'MultimediaApplication',
                    'operatingSystem'     => 'Any',
                    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
                ],
            ],
        ]);
    }

    public function vocalRemover()
    {
        $canonical = url('/tools/hapus-vokal');
        return view('tools.vocal-remover', [
            'seo' => [
                'title'       => 'Penghapus Vokal Online Gratis — Bikin Karaoke / Minus One di Browser',
                'description' => 'Hapus vokal dari lagu untuk bikin karaoke / minus one, langsung di browser tanpa upload. Pisahkan instrumen & vokal, unduh MP3/WAV. Gratis, tanpa install.',
                'url'         => $canonical,
                'schema'      => [
                    '@context'            => 'https://schema.org',
                    '@type'               => 'WebApplication',
                    'name'                => 'Penghapus Vokal Online (Karaoke Maker)',
                    'url'                 => $canonical,
                    'description'         => 'Hapus vokal lagu untuk karaoke/minus one, gratis di browser tanpa upload ke server.',
                    'applicationCategory' => 'MultimediaApplication',
                    'operatingSystem'     => 'Any',
                    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
                ],
            ],
        ]);
    }

    public function coverMaker()
    {
        $canonical = url('/tools/cover-art');
        return view('tools.cover-maker', [
            'seo' => [
                'title'       => 'Buat Cover Lagu / Album Online Gratis — Cover Art Maker 1:1 (3000px)',
                'description' => 'Bikin cover art lagu/album persegi 1:1 untuk Spotify, Apple Music, YouTube — resolusi 1600/2000/3000 px. Tambah judul & nama artis, atur foto, unduh PNG/JPG. Gratis, tanpa upload.',
                'url'         => $canonical,
                'schema'      => [
                    '@context'            => 'https://schema.org',
                    '@type'               => 'WebApplication',
                    'name'                => 'Cover Art Maker (Buat Cover Lagu)',
                    'url'                 => $canonical,
                    'description'         => 'Buat cover art lagu/album 1:1 (3000px) untuk platform streaming, gratis di browser tanpa upload.',
                    'applicationCategory' => 'DesignApplication',
                    'operatingSystem'     => 'Any',
                    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
                ],
            ],
        ]);
    }

    public function releaseCard()
    {
        $canonical = url('/tools/kartu-rilis');
        return view('tools.release-card', [
            'seo' => [
                'title'       => 'Kartu Promo Rilis Lagu Online Gratis — Pra-Rilis, Rilis & Countdown',
                'description' => 'Buat kartu promo rilis lagu untuk Instagram/WhatsApp: pra-rilis (countdown hari rilis), rilis (out now + link/QR platform), pasca-rilis. Feed 1:1 & Story 9:16. Gratis, tanpa upload.',
                'url'         => $canonical,
                'schema'      => [
                    '@context'            => 'https://schema.org',
                    '@type'               => 'WebApplication',
                    'name'                => 'Kartu Promo Rilis Lagu (Countdown Maker)',
                    'url'                 => $canonical,
                    'description'         => 'Buat kartu promo rilis & countdown lagu untuk media sosial, gratis di browser tanpa upload.',
                    'applicationCategory' => 'DesignApplication',
                    'operatingSystem'     => 'Any',
                    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
                ],
            ],
        ]);
    }

    public function countdown(Request $request)
    {
        $canonical = url('/tools/countdown');
        $j = Str::limit(trim((string) $request->query('j', '')), 60, '');
        $a = Str::limit(trim((string) $request->query('a', '')), 40, '');
        $d = trim((string) $request->query('d', ''));
        $hasParams = $d !== '';

        if ($hasParams) {
            // Mode display (link dibagikan) — OG dinamis untuk preview cantik di WA/medsos
            $title = ($j !== '' ? $j : 'Rilis Baru') . ($a !== '' ? ' — ' . $a : '') . ' · Hitung Mundur Rilis';
            $desc  = 'Hitung mundur rilis ' . ($j !== '' ? '“' . $j . '”' : 'lagu') . ($a !== '' ? ' oleh ' . $a : '')
                   . '. Buka untuk lihat countdown langsung & dengarkan saat rilis.';
            $seo = ['title' => $title, 'description' => $desc, 'url' => $canonical, 'type' => 'website'];
        } else {
            $seo = [
                'title'       => 'Buat Countdown Rilis Lagu — Link Hitung Mundur untuk Bio & Story',
                'description' => 'Bikin link hitung mundur rilis lagu yang berdetak real-time untuk bio Instagram / link-in-bio / WhatsApp. Saat rilis otomatis jadi "Out Now". Gratis, tanpa daftar.',
                'url'         => $canonical,
                'schema'      => [
                    '@context'            => 'https://schema.org',
                    '@type'               => 'WebApplication',
                    'name'                => 'Countdown Rilis Lagu (Generator Link Hitung Mundur)',
                    'url'                 => $canonical,
                    'description'         => 'Buat link hitung mundur rilis lagu real-time untuk media sosial, gratis tanpa daftar.',
                    'applicationCategory' => 'UtilitiesApplication',
                    'operatingSystem'     => 'Any',
                    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
                ],
            ];
        }

        return view('tools.countdown', compact('seo', 'hasParams'));
    }
}
