<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

/**
 * Upload/hapus media ke Cloudinary (storage eksternal — tidak membebani hosting).
 * Kredensial disimpan di tabel site_settings (api_secret terenkripsi).
 */
class CloudinaryService
{
    protected string $cloud;
    protected string $key;
    protected string $secret;

    public function __construct()
    {
        $this->cloud  = (string) SiteSetting::get('cloudinary_cloud', '');
        $this->key    = (string) SiteSetting::get('cloudinary_key', '');
        $secret       = (string) SiteSetting::get('cloudinary_secret', '');
        // secret disimpan terenkripsi
        try {
            $this->secret = $secret ? Crypt::decryptString($secret) : '';
        } catch (\Throwable $e) {
            $this->secret = $secret; // fallback bila tersimpan plain (kompatibilitas)
        }
    }

    public function configured(): bool
    {
        return $this->cloud !== '' && $this->key !== '' && $this->secret !== '';
    }

    /**
     * Upload dari bytes (raw image data) ke Cloudinary.
     * @return array{url:string, public_id:string}
     */
    public function uploadBytes(string $bytes, string $folder = 'margonoandi/ai'): array
    {
        $dataUri = 'data:image/png;base64,' . base64_encode($bytes);
        return $this->upload($dataUri, $folder);
    }

    /**
     * Upload (file boleh berupa data URI atau remote URL — Cloudinary yang fetch).
     * @return array{url:string, public_id:string}
     */
    public function upload(string $file, string $folder = 'margonoandi/ai'): array
    {
        if (!$this->configured()) {
            throw new \Exception('Cloudinary belum dikonfigurasi (Pengaturan AI).');
        }

        $timestamp = time();
        // signature: param non-file (kecuali api_key & file) diurut alfabet, lalu + api_secret, sha1
        $signParams = ['folder' => $folder, 'timestamp' => $timestamp];
        ksort($signParams);
        $toSign = urldecode(http_build_query($signParams));
        $signature = sha1($toSign . $this->secret);

        $resp = Http::timeout(120)->asMultipart()->post(
            "https://api.cloudinary.com/v1_1/{$this->cloud}/image/upload",
            [
                ['name' => 'file',      'contents' => $file],
                ['name' => 'api_key',   'contents' => $this->key],
                ['name' => 'timestamp', 'contents' => (string) $timestamp],
                ['name' => 'folder',    'contents' => $folder],
                ['name' => 'signature', 'contents' => $signature],
            ]
        );

        if (!$resp->successful()) {
            throw new \Exception('Cloudinary upload gagal (' . $resp->status() . '): ' . $resp->body());
        }

        return [
            'url'       => $resp->json('secure_url', ''),
            'public_id' => $resp->json('public_id', ''),
        ];
    }

    /**
     * Hapus aset di Cloudinary berdasarkan public_id (signed destroy).
     */
    public function destroy(string $publicId): bool
    {
        if (!$this->configured() || $publicId === '') return false;

        $timestamp = time();
        $signParams = ['public_id' => $publicId, 'timestamp' => $timestamp];
        ksort($signParams);
        $toSign = urldecode(http_build_query($signParams));
        $signature = sha1($toSign . $this->secret);

        $resp = Http::timeout(60)->asForm()->post(
            "https://api.cloudinary.com/v1_1/{$this->cloud}/image/destroy",
            [
                'public_id' => $publicId,
                'api_key'   => $this->key,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]
        );

        return $resp->successful() && $resp->json('result') === 'ok';
    }
}
