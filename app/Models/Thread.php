<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = [
        'user_id', 'title', 'body', 'category',
        'is_pinned', 'is_locked', 'replies_count',
        'views_count', 'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
        'is_pinned'     => 'boolean',
        'is_locked'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ThreadReply::class)->latest();
    }

    public function latestReply()
    {
        return $this->hasOne(ThreadReply::class)->latestOfMany();
    }
}