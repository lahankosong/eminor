<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    protected $fillable = [
        'name', 'base_url', 'api_key', 'model', 'format', 'kind', 'enabled',
    ];

    protected $casts = [
        'api_key' => 'encrypted',  // otomatis enkripsi saat simpan, dekripsi saat baca
        'enabled' => 'boolean',
    ];
}
