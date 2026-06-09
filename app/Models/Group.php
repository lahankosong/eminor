<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'description', 'created_by', 'last_message_at', 'last_message'
    ];

    protected $casts = ['last_message_at' => 'datetime'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class)->orderBy('created_at');
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->exists();
    }
}