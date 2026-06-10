<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkuLike extends Model
{
    protected $fillable = ['aku_post_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}