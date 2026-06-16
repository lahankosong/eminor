<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiImage extends Model
{
    protected $table = 'ai_images';

    protected $fillable = [
        'user_id', 'song_id', 'prompt', 'provider',
        'url', 'public_id', 'ratio',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'song_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
