<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function show($slug)
    {
        $song = Song::where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

        $comments = $song->comments()->with('user')->get();

        $prevSong = Song::where('track_number', '<', $song->track_number)
                        ->where('is_active', true)
                        ->orderBy('track_number', 'desc')
                        ->first();

        $nextSong = Song::where('track_number', '>', $song->track_number)
                        ->where('is_active', true)
                        ->orderBy('track_number', 'asc')
                        ->first();

        return view('songs.show', compact('song', 'comments', 'prevSong', 'nextSong'));
    }

    public function comment(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('google.login');
        }

        $song = Song::where('slug', $slug)->firstOrFail();

        $request->validate([
            'body' => 'required|string|min:3|max:1000',
        ]);

        // Filter kata negatif sederhana
        $badWords = ['bangsat', 'anjing', 'babi', 'goblok', 'tolol', 'idiot',
                     'bodoh', 'kontol', 'memek', 'bajingan', 'sialan', 'kampret',
                     'kafir', 'munafik', 'sesat'];

        $body = $request->body;
        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $body = preg_replace($pattern, str_repeat('*', strlen($word)), $body);
        }

        SongComment::create([
            'song_id' => $song->id,
            'user_id' => Auth::id(),
            'body'    => $body,
            'is_approved' => true,
        ]);

        return redirect()->route('song.show', $slug)
                         ->with('success', 'Komentar berhasil ditambahkan.');
    }
}