<?php

namespace App\Http\Controllers;

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

        return view('home', compact('songs', 'featuredSong', 'ctaSongs', 'settings'));
    }
}