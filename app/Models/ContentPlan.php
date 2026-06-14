<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPlan extends Model
{
    protected $fillable = [
        'plan_date', 'song_id', 'platforms', 'content_type', 'title', 'status', 'notes',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'song_id'   => 'integer',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
