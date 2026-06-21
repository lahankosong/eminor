<?php

namespace App\Helpers;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Http;

/**
 * Web Push payloadless + VAPID (murni PHP/openssl, tanpa library composer & tanpa gmp).
 * Mengirim "tickle" tanpa data; service worker lalu fetch /notifications/latest untuk isinya.
 */
class WebPush
{
    public static function enabled(): bool
    {
        return !empty(config('services.vapid.public')) && !empty(config('services.vapid.private_pem_b64'));
    }

    /** Kirim push ke semua subscription milik user (best-effort). */
    public static function sendToUser($userId): void
    {
        if (!self::enabled()) return;
        try {
            $subs = PushSubscription::where('user_id', $userId)->get();
        } catch (\Throwable $e) {
            return;
        }
        foreach ($subs as $sub) {
            try {
                $status = self::sendOne($sub->endpoint);
                if (in_array($status, [404, 410], true)) {
                    try { $sub->delete(); } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {}
        }
    }

    /** Kirim satu push kosong ke endpoint. Return HTTP status (atau 0 jika gagal). */
    protected static function sendOne(string $endpoint): int
    {
        $aud = self::origin($endpoint);
        $jwt = self::vapidJwt($aud);
        $pub = config('services.vapid.public');

        $resp = Http::timeout(6)->withHeaders([
            'Authorization' => 'vapid t=' . $jwt . ', k=' . $pub,
            'TTL'           => '2419200',
            'Urgency'       => 'high',
        ])->post($endpoint);

        return $resp->status();
    }

    protected static function origin(string $url): string
    {
        $p = parse_url($url);
        return ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '');
    }

    /** Buat VAPID JWT (ES256) memakai private key PEM (base64 di .env). */
    protected static function vapidJwt(string $aud): string
    {
        $header  = self::b64url(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = self::b64url(json_encode([
            'aud' => $aud,
            'exp' => time() + 12 * 3600,
            'sub' => config('services.vapid.subject', 'mailto:admin@margonoandi.my.id'),
        ]));
        $signingInput = $header . '.' . $payload;

        $pem = base64_decode((string) config('services.vapid.private_pem_b64'));
        $key = openssl_pkey_get_private($pem);
        if (!$key) throw new \RuntimeException('VAPID private key tidak valid');

        $der = '';
        if (!openssl_sign($signingInput, $der, $key, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Gagal menandatangani VAPID JWT');
        }

        return $signingInput . '.' . self::b64url(self::derToRaw($der));
    }

    /** ECDSA DER (SEQUENCE{INTEGER r, INTEGER s}) -> raw 64 byte (r||s, 32 byte each). */
    protected static function derToRaw(string $der): string
    {
        $off = 0;
        if (ord($der[$off++]) !== 0x30) throw new \RuntimeException('DER tidak valid');
        $len = ord($der[$off++]);
        if ($len & 0x80) { $off += ($len & 0x7f); } // skip long-form length bytes

        $readInt = function () use (&$off, $der) {
            if (ord($der[$off++]) !== 0x02) throw new \RuntimeException('INTEGER DER tidak valid');
            $l = ord($der[$off++]);
            $v = substr($der, $off, $l);
            $off += $l;
            $v = ltrim($v, "\x00");
            return str_pad($v, 32, "\x00", STR_PAD_LEFT);
        };

        return $readInt() . $readInt();
    }

    protected static function b64url(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }
}
