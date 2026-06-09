<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KamuNote extends Model {
    protected $fillable = ['user_id','title','body','color','is_pinned'];
    public function user() { return $this->belongsTo(User::class); }
}