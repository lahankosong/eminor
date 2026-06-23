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
}
