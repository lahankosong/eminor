<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'user_id', 'body', 'media_url', 'media_type', 'read_at'];

    protected $casts = ['read_at' => 'datetime', 'user_id' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}