<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = ['follower_id', 'following_id'];

    protected $casts = [
        'follower_id'  => 'integer',
        'following_id' => 'integer',
    ];
}
