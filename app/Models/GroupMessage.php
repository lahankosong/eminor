<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = ['group_id', 'user_id', 'body', 'media_url', 'media_type'];

    protected $casts = ['user_id' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}