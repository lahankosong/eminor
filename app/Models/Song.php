<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'title', 'slug', 'youtube_id', 'audio_file', 'spotify_url', 'apple_music_url',
        'description', 'story_hook', 'lyrics', 'chords', 'key_signature',
        'tempo', 'track_number', 'is_active', 'era', 'era_story', 'featured',
    ];

    public function comments()
    {
        return $this->hasMany(SongComment::class)->where('is_approved', true)->latest();
    }

    public function getUrlAttribute()
    {
        return route('song.show', $this->slug);
    }
}