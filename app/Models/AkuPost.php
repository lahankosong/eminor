<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkuPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'body', 'image',
        'mood', 'likes_count', 'comments_count', 'is_pinned'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(AkuComment::class)
                    ->whereNull('parent_id')
                    ->with('replies.user')
                    ->latest();
    }

    public function likes()
    {
        return $this->hasMany(AkuLike::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}