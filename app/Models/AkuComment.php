<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkuComment extends Model
{
    protected $fillable = ['aku_post_id', 'user_id', 'parent_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(AkuComment::class, 'parent_id')->latest();
    }

    public function parent()
    {
        return $this->belongsTo(AkuComment::class, 'parent_id');
    }
}