<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongComment extends Model
{
    protected $table = 'song_comments';

    protected $fillable = ['song_id', 'user_id', 'body', 'is_approved'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}