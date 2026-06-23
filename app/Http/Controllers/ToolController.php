<?php

namespace App\Http\Controllers;

class ToolController extends Controller
{
    public function audioCutter()
    {
        return view('tools.audio-cutter', [
            'seo' => [
                'title'       => 'Pemotong Lagu Online Gratis — Potong MP3, WAV, OGG di Browser',
                'description' => 'Potong bagian lagu favoritmu secara online, gratis, tanpa upload ke server. Mendukung MP3, WAV, OGG, FLAC. Hasil langsung diunduh ke perangkatmu.',
                'canonical'   => url('/tools/potong-lagu'),
            ],
        ]);
    }
}
