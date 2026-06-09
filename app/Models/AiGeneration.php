<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneration extends Model
{   protected $table = 'ai_generations';
    protected $fillable = [
        'song_id', 'user_id', 'topics', 'scripts',
        'visual_sequences', 'dreamina_prompts',
        'shorts_description', 'hashtags',
        'selected_topic_id', 'selected_variation_id',
        'selected_hook', 'selected_caption', 'selected_prompt',
    ];
}
