<?php

namespace App\Http\Controllers;

use App\Models\Song;

class PromoTemplateController extends Controller
{
    public function index()
    {
        $songs = Song::orderBy('track_number')->get();
        return view('admin.promo', compact('songs'));
    }
}
