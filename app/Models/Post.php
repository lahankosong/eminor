<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'body', 'location',
        'likes_count', 'comments_count', 'is_pinned'
    ];

    protected $casts = [
        'user_id'        => 'integer',
        'likes_count'    => 'integer',
        'comments_count' => 'integer',
        'is_pinned'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->with('user')->latest();
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}