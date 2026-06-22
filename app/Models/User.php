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
        'roles',
        'last_seen',
        'is_online',
    ];

    /** Pilihan peran untuk onboarding & profil (key => label berikon). */
    public static function roleOptions(): array
    {
        return [
            'gitaris'           => '🎸 Gitaris',
            'basis'             => '🎵 Basis',
            'drummer'           => '🥁 Drummer',
            'vokalis'           => '🎤 Vokalis',
            'keyboardis'        => '🎹 Keyboardis',
            'songwriter'        => '✍️ Songwriter',
            'arranger'          => '🎚️ Arranger',
            'produser'          => '🎛️ Produser',
            'event_organizer'   => '🎫 Event Organizer',
            'wedding_organizer' => '💍 Wedding Organizer',
            'promotor'          => '📣 Promotor',
            'penikmat'          => '🎧 Penikmat Musik',
        ];
    }

    public function rolesArray(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->roles))));
    }

    public function roleLabels(): array
    {
        $opt = self::roleOptions();
        return array_map(fn ($r) => $opt[$r] ?? $r, $this->rolesArray());
    }

    /** Slug peran (mis. "gitaris,vokalis") -> label bersih tanpa emoji, buang 'penikmat'.
        Dipakai untuk seed roles profil musisi dari onboarding. */
    public static function rolesToCleanLabels(?string $slugs): string
    {
        $opt = self::roleOptions();
        return collect(explode(',', (string) $slugs))
            ->map(fn ($r) => trim($r))
            ->filter(fn ($r) => $r !== '' && $r !== 'penikmat')
            ->map(fn ($r) => trim(preg_replace('/^\S+\s*/u', '', $opt[$r] ?? $r)))
            ->filter()
            ->unique()
            ->implode(', ');
    }

    protected $casts = [
        'last_seen' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function isOnline(): bool
    {
        try {
            $seen = $this->attributes['last_seen'] ?? null;
            if (!$seen) return false;
            return strtotime($seen) > (time() - 120);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function lastSeenLabel(): string
    {
        try {
            if ($this->isOnline()) return 'Online';
            $seen = $this->attributes['last_seen'] ?? null;
            if (!$seen) return 'Offline';
            return 'Aktif ' . date('H:i', strtotime($seen));
        } catch (\Throwable $e) {
            return 'Offline';
        }
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}