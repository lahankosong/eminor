<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MusicianProfile extends Model
{
    protected $fillable = [
        'user_id', 'roles', 'skill_level', 'genres', 'location', 'bio',
        'looking_for', 'spotify_url', 'youtube_url', 'instagram',
        'open_to_band', 'open_to_collab', 'is_active',
    ];

    protected $casts = [
        'user_id'        => 'integer',
        'open_to_band'   => 'boolean',
        'open_to_collab' => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rolesArray()
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->roles))));
    }

    public function genresArray()
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->genres))));
    }
}
