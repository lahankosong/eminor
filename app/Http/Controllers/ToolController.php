<?php

namespace App\Http\Controllers;

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
}
