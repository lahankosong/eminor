<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'last_seen',
        'is_online',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function isOnline(): bool
    {
        return $this->last_seen !== null && $this->last_seen->gt(now()->subMinutes(2));
    }

    public function lastSeenLabel(): string
    {
        if ($this->isOnline()) return 'Online';
        if (!$this->last_seen) return 'Offline';
        return 'Aktif ' . $this->last_seen->format('H:i');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}