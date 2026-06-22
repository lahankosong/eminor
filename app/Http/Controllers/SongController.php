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

        $desc = \Illuminate\Support\Str::limit(
            strip_tags($song->story_hook ?: $song->description ?: ('Lagu "' . $song->title . '" dari Margonoandi.')),
            160
        );
        $img = $song->youtube_id
            ? 'https://img.youtube.com/vi/' . $song->youtube_id . '/maxresdefault.jpg'
            : asset('images/Margonoandi.jpeg');
        $sameAs = array_values(array_filter([
            $song->spotify_url, $song->apple_music_url,
            $song->youtube_id ? 'https://youtu.be/' . $song->youtube_id : null,
        ]));

        $songUrl = route('song.show', $song->slug);
        $genre   = $song->era ? [$song->era, 'Indie', 'Pop Indonesia'] : ['Indie', 'Pop Indonesia'];

        $seo = [
            'title'       => $song->title . ' — Margonoandi' . ($song->era ? ' (' . $song->era . ')' : ''),
            'description' => $desc,
            'image'       => $img,
            'url'         => $songUrl,
            'type'        => 'music.song',
            'schema'      => [
                '@context' => 'https://schema.org',
                '@graph'   => [
                    [
                        '@type'         => 'MusicRecording',
                        'name'          => $song->title,
                        'url'           => $songUrl,
                        'image'         => $img,
                        'description'   => $desc,
                        'byArtist'      => ['@type' => 'MusicGroup', 'name' => 'Margonoandi', 'url' => url('/')],
                        'inLanguage'    => 'id',
                        'genre'         => $genre,
                        'datePublished' => $song->created_at?->toDateString(),
                        'sameAs'        => $sameAs,
                    ],
                    [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => url('/')],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => $song->title, 'item' => $songUrl],
                        ],
                    ],
                ],
            ],
        ];

        return view('songs.show', compact('song', 'comments', 'prevSong', 'nextSong', 'seo'));
    }

    public function play($id)
    {
        try {
            Song::where('id', $id)->increment('play_count');
        } catch (\Throwable $e) {}
        return response()->json(['ok' => true]);
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