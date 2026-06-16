<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = ['user_id', 'post_id', 'parent_id', 'body', 'likes_count'];

    protected $casts = [
        'user_id'     => 'integer',
        'post_id'     => 'integer',
        'parent_id'   => 'integer',
        'likes_count' => 'integer',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(PostComment::class, 'parent_id')->with('user')->oldest(); }
    public function commentLikes() { return $this->hasMany(PostCommentLike::class, 'comment_id'); }
}
