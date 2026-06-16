<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\AiGeneration;
use App\Models\AiProvider;
use App\Models\AiImage;
use App\Models\ContentPlan;
use App\Models\SiteSetting;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class AiAgentController extends Controller
{
    public function index()
    {
        $songs = Song::orderBy('track_number')->get();
        $providers = collect();
        $saved = [];          // song_id => ['niche'=>..., 'topics'=>[...]]
        $lastSongId = null;   // generasi terakhir → ditampilkan saat halaman dibuka

        try {
            $providers = AiProvider::orderBy('name')->get();
        } catch (\Throwable $e) {
            // tabel belum ada — jalankan fixdb.php
        }

        try {
            $gens = AiGeneration::where('user_id', auth()->id())
                ->orderByDesc('updated_at')->get();
            foreach ($gens as $g) {
                $topics = json_decode($g->topics, true);
                $long   = json_decode($g->scripts, true);
                $umum   = json_decode($g->visual_sequences, true);
                $hasShort = is_array($topics) && !empty($topics[0]['narrations']);
                $hasLong  = is_array($long) && !empty($long['narration']);
                $hasUmum  = is_array($umum) && !empty($umum[0]['theme']);
                if (!$hasShort && !$hasLong && !$hasUmum) continue;
                $saved[$g->song_id] = [
                    'niche'     => $g->shorts_description,
                    'topics'    => $hasShort ? $topics : null,
                    'long_form' => $hasLong ? $long : null,
                    'umum'      => $hasUmum ? $umum : null,
                ];
                if ($lastSongId === null) $lastSongId = $g->song_id;
            }
        } catch (\Throwable $e) {
            // tabel belum ada / kolom beda — abaikan
        }

        // Pengaturan Cloudinary (untuk panel penyimpanan gambar)
        $cloudinary = [
            'cloud'      => (string) SiteSetting::get('cloudinary_cloud', ''),
            'key'        => (string) SiteSetting::get('cloudinary_key', ''),
            'secret_set' => (bool) SiteSetting::get('cloudinary_secret'),
        ];

        return view('admin.ai-agent', compact('songs', 'providers', 'saved', 'lastSongId', 'cloudinary'));
    }

    /* ===================== Halaman Pengaturan AI ===================== */

    public function aiSettings()
    {
        $providers = collect();
        try {
            $providers = AiProvider::orderBy('name')->get();
        } catch (\Throwable $e) {
            // tabel belum ada — jalankan fixdb.php
        }

        $cloudinary = [
            'cloud'      => (string) SiteSetting::get('cloudinary_cloud', ''),
            'key'        => (string) SiteSetting::get('cloudinary_key', ''),
            'secret_set' => (bool) SiteSetting::get('cloudinary_secret'),
        ];

        return view('admin.ai-settings', compact('providers', 'cloudinary'));
    }

    /* ===================== Provider CRUD ===================== */

    public function storeProvider(Request $request)
    {
        $kind = $request->input('kind', 'text');

        if ($kind === 'image') {
            $data = $request->validate([
                'name'     => 'required|string|max:100',
                'format'   => 'required|in:pollinations,dalle,imagen',
                'base_url' => 'nullable|string|max:255',
                'model'    => 'nullable|string|max:120',
                'api_key'  => 'nullable|string|max:300',
            ]);
            $data['kind']     = 'image';
            $data['base_url'] = $data['base_url'] ?? '';
            $data['model']    = $data['model'] ?? '';
        } else {
            $data = $request->validate([
                'name'     => 'required|string|max:100',
                'base_url' => 'required|string|max:255',
                'model'    => 'required|string|max:120',
                'format'   => 'required|in:openai,anthropic',
                'api_key'  => 'nullable|string|max:300',
            ]);
            $data['kind'] = 'text';
        }

        $data['enabled'] = true;
        AiProvider::create($data);
        return back()->with('success', 'Provider AI "' . $data['name'] . '" disimpan.');
    }

    /* ===================== Pengaturan Cloudinary ===================== */

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'cloudinary_cloud'  => 'nullable|string|max:120',
            'cloudinary_key'    => 'nullable|string|max:120',
            'cloudinary_secret' => 'nullable|string|max:200',
        ]);

        if ($request->has('cloudinary_cloud')) SiteSetting::set('cloudinary_cloud', trim((string) $data['cloudinary_cloud']));
        if ($request->has('cloudinary_key'))   SiteSetting::set('cloudinary_key', trim((string) $data['cloudinary_key']));
        // secret hanya ditimpa bila diisi (biar tidak terhapus saat edit field lain)
        if (!empty($data['cloudinary_secret'])) {
            SiteSetting::set('cloudinary_secret', Crypt::encryptString(trim($data['cloudinary_secret'])));
        }

        return back()->with('success', 'Pengaturan Cloudinary disimpan.');
    }

    /* ===================== Generate Gambar (Fase A) ===================== */

    public function generateImage(Request $request)
    {
        $data = $request->validate([
            'prompt'      => 'required|string|max:1500',
            'ratio'       => 'nullable|in:9:16,16:9,1:1',
            'provider_id' => 'nullable|integer',
            'song_id'     => 'nullable|integer',
        ]);

        $cloud = new CloudinaryService();
        if (!$cloud->configured()) {
            return response()->json(['error' => 'Cloudinary belum dikonfigurasi. Buka Pengaturan AI → isi kredensial Cloudinary.'], 422);
        }

        // pilih provider gambar (by id, lalu fallback ke provider image aktif pertama)
        $provider = null;
        if (!empty($data['provider_id'])) {
            $provider = AiProvider::where('id', $data['provider_id'])->where('kind', 'image')->first();
        }
        if (!$provider) {
            $provider = AiProvider::where('kind', 'image')->where('enabled', true)->orderBy('id')->first();
        }
        $providerName = $provider ? $provider->name : 'Pollinations (gratis)';

        [$w, $h] = $this->ratioDims($data['ratio'] ?? '9:16');

        try {
            $bytes = $this->callImageProvider($provider, $data['prompt'], $w, $h);
            $up = $cloud->uploadBytes($bytes);

            $img = AiImage::create([
                'user_id'   => auth()->id(),
                'song_id'   => $data['song_id'] ?? null,
                'prompt'    => $data['prompt'],
                'provider'  => $providerName,
                'url'       => $up['url'],
                'public_id' => $up['public_id'],
                'ratio'     => $data['ratio'] ?? '9:16',
            ]);

            return response()->json([
                'success'  => true,
                'id'       => $img->id,
                'url'      => $up['url'],
                'provider' => $providerName,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI image generate error', ['error' => $e->getMessage()]);
            $msg = $e->getMessage();
            if (str_contains($msg, '429') || str_contains($msg, 'RESOURCE_EXHAUSTED') || str_contains($msg, 'quota')) {
                $msg = 'Kuota provider habis / butuh billing aktif. Gemini & Imagen perlu billing untuk gambar. '
                     . 'Pakai provider "Pollinations" (gratis) sebagai gantinya, atau aktifkan billing di Google.';
            }
            return response()->json(['error' => $msg], 500);
        }
    }

    public function destroyImage($id)
    {
        $img = AiImage::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        try {
            if ($img->public_id) (new CloudinaryService())->destroy($img->public_id);
        } catch (\Throwable $e) {
            // gagal hapus di Cloudinary — tetap hapus record lokal
        }
        $img->delete();
        return response()->json(['success' => true]);
    }

    protected function ratioDims(string $ratio): array
    {
        return match ($ratio) {
            '16:9'  => [1280, 720],
            '1:1'   => [1024, 1024],
            default => [768, 1344], // 9:16
        };
    }

    protected function dalleSize(int $w, int $h): string
    {
        if ($w > $h) return '1792x1024';
        if ($h > $w) return '1024x1792';
        return '1024x1024';
    }

    protected function imagenAspect(int $w, int $h): string
    {
        if ($w > $h) return '16:9';
        if ($h > $w) return '9:16';
        return '1:1';
    }

    /** Generate gambar → kembalikan raw bytes. */
    protected function callImageProvider(?AiProvider $provider, string $prompt, int $w, int $h): string
    {
        $format = $provider->format ?? 'pollinations';

        if ($provider && $format === 'dalle') {
            $key = $provider->api_key;
            if (!$key) throw new \Exception('API key untuk "' . $provider->name . '" belum diisi.');
            $resp = Http::timeout(120)->withToken($key)->post(
                rtrim($provider->base_url ?: 'https://api.openai.com/v1', '/') . '/images/generations',
                [
                    'model'           => $provider->model ?: 'dall-e-3',
                    'prompt'          => $prompt,
                    'n'               => 1,
                    'size'            => $this->dalleSize($w, $h),
                    'response_format' => 'b64_json',
                ]
            );
            if (!$resp->successful()) throw new \Exception('Image API error (' . $resp->status() . '): ' . $resp->body());
            $b64 = $resp->json('data.0.b64_json');
            if ($b64) return base64_decode($b64);
            $url = $resp->json('data.0.url');
            if ($url) return Http::timeout(60)->get($url)->body();
            throw new \Exception('Image API tidak mengembalikan gambar.');
        }

        if ($provider && $format === 'imagen') {
            $key = $provider->api_key;
            if (!$key) throw new \Exception('API key untuk "' . $provider->name . '" belum diisi.');

            // Normalisasi base URL — buang bagian /models/... bila terlanjur disertakan
            $base = rtrim($provider->base_url ?: 'https://generativelanguage.googleapis.com/v1beta', '/');
            if (($pos = strpos($base, '/models')) !== false) {
                $base = rtrim(substr($base, 0, $pos), '/');
            }
            $model = $provider->model ?: 'imagen-3.0-generate-002';

            // Model Imagen (imagen-*) pakai :predict; model Gemini native image pakai :generateContent
            if (str_starts_with($model, 'imagen')) {
                $resp = Http::timeout(120)->post(
                    $base . '/models/' . $model . ':predict?key=' . urlencode($key),
                    [
                        'instances'  => [['prompt' => $prompt]],
                        'parameters' => ['sampleCount' => 1, 'aspectRatio' => $this->imagenAspect($w, $h)],
                    ]
                );
                if (!$resp->successful()) throw new \Exception('Imagen error (' . $resp->status() . '): ' . $resp->body());
                $b64 = $resp->json('predictions.0.bytesBase64Encoded');
                if ($b64) return base64_decode($b64);
                throw new \Exception('Imagen tidak mengembalikan gambar (cek akses Imagen/billing di API key).');
            }

            // Gemini native image (mis. gemini-2.5-flash-image) → generateContent + responseModalities
            $hint = $prompt . ' (aspect ratio ' . $this->imagenAspect($w, $h) . ')';
            $resp = Http::timeout(120)->post(
                $base . '/models/' . $model . ':generateContent?key=' . urlencode($key),
                [
                    'contents'         => [['parts' => [['text' => $hint]]]],
                    'generationConfig' => ['responseModalities' => ['TEXT', 'IMAGE']],
                ]
            );
            if (!$resp->successful()) throw new \Exception('Gemini image error (' . $resp->status() . '): ' . $resp->body());
            $parts = $resp->json('candidates.0.content.parts', []);
            foreach ($parts as $p) {
                $b64 = $p['inlineData']['data'] ?? $p['inline_data']['data'] ?? null;
                if ($b64) return base64_decode($b64);
            }
            throw new \Exception('Gemini tidak mengembalikan gambar — pastikan model image (mis. gemini-2.5-flash-image), bukan model teks.');
        }

        // Pollinations.ai — gratis, tanpa API key
        $model = ($provider && $provider->model) ? $provider->model : 'flux';
        $url = 'https://image.pollinations.ai/prompt/' . rawurlencode($prompt)
            . '?width=' . $w . '&height=' . $h . '&nologo=true&model=' . rawurlencode($model);
        $resp = Http::timeout(150)->get($url);
        if (!$resp->successful()) throw new \Exception('Pollinations error (' . $resp->status() . ')');
        $body = $resp->body();
        if (strlen($body) < 1000) throw new \Exception('Gambar gagal dibuat, coba lagi sebentar.');
        return $body;
    }

    public function destroyProvider($id)
    {
        AiProvider::findOrFail($id)->delete();
        return back()->with('success', 'Provider dihapus.');
    }

    /* ===================== AI call ===================== */

    protected function callAi(AiProvider $p, string $prompt): string
    {
        $key = $p->api_key;
        if (!$key) throw new \Exception('API key untuk "' . $p->name . '" belum diisi.');

        if ($p->format === 'anthropic') {
            $resp = Http::timeout(120)->withHeaders([
                'x-api-key'         => $key,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post(rtrim($p->base_url, '/') . '/messages', [
                'model'      => $p->model,
                'max_tokens' => 8000,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);
            if (!$resp->successful()) throw new \Exception('AI error (' . $resp->status() . '): ' . $resp->body());
            return $resp->json('content.0.text', '');
        }

        // OpenAI-compatible (Gemini/Groq/OpenRouter/OpenAI/DeepSeek/dll)
        $resp = Http::timeout(120)->withToken($key)->post(rtrim($p->base_url, '/') . '/chat/completions', [
            'model'       => $p->model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.9,
            'max_tokens'  => 8000,
        ]);
        if (!$resp->successful()) throw new \Exception('AI error (' . $resp->status() . '): ' . $resp->body());
        return $resp->json('choices.0.message.content', '');
    }

    /* ===================== Generate ===================== */

    public function generate(Request $request, $id)
    {
        $song = Song::findOrFail($id);

        $provider = AiProvider::find($request->input('provider_id'));
        if (!$provider) {
            return response()->json(['error' => 'Pilih provider AI dulu (atau tambahkan di Pengaturan AI).'], 422);
        }

        $lyrics = $song->lyrics ? "Lirik:\n" . $song->lyrics
            : "Lirik belum ada — gunakan judul, hook, dan deskripsi sebagai panduan.";
        $hook = $song->story_hook ?? $song->description ?? $song->title;

        $mode = $request->input('mode', 'short');           // short | long | umum | all
        $want = $mode === 'all' ? ['short', 'long', 'umum'] : [$mode];

        // Sumber tambahan: teks bebas atau link (Wikipedia dll) — diambil isinya
        $sourceInput = trim((string) $request->input('source', ''));
        $sourceText = '';
        if ($sourceInput !== '') {
            if (preg_match('~^https?://~i', $sourceInput)) {
                try {
                    $html = Http::timeout(20)->get($sourceInput)->body();
                    $html = preg_replace('~<(script|style)[^>]*>.*?</\1>~is', ' ', $html);
                    $text = html_entity_decode(strip_tags($html), ENT_QUOTES);
                    $sourceText = trim(preg_replace('/\s+/', ' ', $text));
                    $sourceText = mb_substr($sourceText, 0, 4000);
                } catch (\Throwable $e) {
                    $sourceText = '';
                }
            } else {
                $sourceText = mb_substr($sourceInput, 0, 4000);
            }
        }

        // Pengaturan gaya gambar (opsional) — nilai sudah dalam frasa Inggris dari frontend
        $st = (array) $request->input('style', []);
        $orient = $st['orientation'] ?? '9:16';
        $orientText = $orient === '16:9' ? 'horizontal 16:9 landscape'
            : ($orient === '1:1' ? 'square 1:1' : 'vertical 9:16 portrait');
        $styleBits = [];
        foreach (['art', 'people', 'gender', 'age', 'time', 'light'] as $k) {
            if (!empty($st[$k])) $styleBits[] = $st[$k];
        }
        $styleLine = "setiap image prompt WAJIB beraspect-ratio {$orientText}"
            . ($styleBits ? '; ' . implode('; ', $styleBits) : '')
            . '; tetap memakai palet brand (retro blue, warm cream, burnt orange) & nuansa sinematik.';

        $header = <<<EOT
Kamu content strategist musik Indonesia yang menulis seperti manusia asli — hangat, relatable, tidak kaku. Audiens: 25–40 tahun, galau malam hari, scrolling HP sebelum tidur.

DATA LAGU:
- Judul: {$song->title}
- Era: {$song->era}
- Hook: {$hook}
{$lyrics}

IDENTITAS VISUAL BRAND (konsisten di semua image prompt):
- Palet: retro blue, warm cream, burnt orange
- Mood: sinematik, intimate, melankolis tapi tidak lebay
- Karakter: orang Indonesia 25–35 tahun, urban, thoughtful
- Gaya: candid, natural light, depth of field lensa 50mm/85mm

TUGAS:
1. NICHE: tentukan satu sudut konten/niche paling kuat dari lirik (1 kalimat, bahasa Indonesia).
EOT;

        $tasks  = '';
        $schema = ['"niche": "..."'];
        $n = 2;
        if (in_array('short', $want)) {
            $tasks .= "\n{$n}. SHORT VIDEO: pecah niche jadi 3–5 topik kejadian sehari-hari yang SANGAT spesifik & berbeda (label max 5 kata). Tiap topik 5 narasi pendek (punchy, gaya notes HP jam 2 pagi, 1–2 kalimat, Indonesia). Tiap narasi 1 image prompt (BAHASA INGGRIS, max 400 char, ikuti ATURAN GAMBAR, karakter Indonesia).";
            $schema[] = '"topics": [{"id":1,"label":"max 5 kata","narrations":[{"text":"narasi","image_prompt":"english 9:16 prompt"},{"text":"...","image_prompt":"..."},{"text":"...","image_prompt":"..."},{"text":"...","image_prompt":"..."},{"text":"...","image_prompt":"..."}]}]';
            $n++;
        }
        if (in_array('long', $want)) {
            $tasks .= "\n{$n}. VIDEO PANJANG 3–5 MENIT: judul menarik + naskah NARASI mengalir bahasa Indonesia ~500–750 kata (pembuka-isi-penutup, storytelling sesuai niche) + 6–8 image prompt (BAHASA INGGRIS, ikuti ATURAN GAMBAR).";
            $schema[] = '"long_form": {"title":"...","duration_estimate":"± 4 menit","narration":"naskah 500-750 kata","scenes":[{"image_prompt":"english 9:16 prompt"}]}';
            $n++;
        }
        if (in_array('umum', $want)) {
            $umumSrc = $sourceText
                ? "Jadikan SUMBER TAMBAHAN di bawah sebagai bahan cerita UTAMA (boleh 1–3 tema dari sumber itu)."
                : "Pilih cerita rakyat Indonesia, sejarah, kisah cinta legendaris, adegan film, atau momen kehidupan.";
            $tasks .= "\n{$n}. TEMA UMUM (video panjang 3–5 menit): 3 ide tema/cerita UMUM yang COCOK memakai lagu ini sebagai BACKSOUND. {$umumSrc} Tiap tema: judul tema, angle (kenapa lagu cocok jadi backsound-nya), NARASI panjang bahasa Indonesia ~500–700 kata (durasi dibaca 3–5 menit, storytelling utuh pembuka-konflik-penutup), + 5–6 image prompt (BAHASA INGGRIS, ikuti ATURAN GAMBAR). Contoh tema: kisah Roro Jonggrang & Bandung Bondowoso.";
            $schema[] = '"umum": [{"theme":"judul tema","angle":"alasan cocok jadi backsound","narration":"naskah 500-700 kata (3-5 menit)","scenes":[{"image_prompt":"english 9:16 prompt"}]}]';
            $n++;
        }

        $prompt = $header . "\n" . $tasks
            . "\n\nATURAN GAMBAR (berlaku untuk SEMUA image prompt): " . $styleLine
            . ($sourceText ? "\n\nSUMBER TAMBAHAN:\n" . $sourceText : "")
            . "\n\nBalas HANYA JSON valid tanpa markdown tanpa backtick:\n{\n  "
            . implode(",\n  ", $schema) . "\n}";

        try {
            $raw = $this->callAi($provider, $prompt);

            // Ekstrak JSON (buang teks/markdown di luar kurung)
            $clean = trim($raw);
            $clean = preg_replace('/^```(json)?\s*|\s*```$/i', '', $clean);
            $start = strpos($clean, '{');
            $end   = strrpos($clean, '}');
            if ($start !== false && $end !== false) {
                $clean = substr($clean, $start, $end - $start + 1);
            }
            $result = json_decode($clean, true);

            if (!is_array($result)) {
                Log::error('AI v2 parse failed', ['preview' => substr($raw, 0, 800)]);
                return response()->json(['error' => 'Gagal memproses jawaban AI. Coba lagi / ganti provider.'], 422);
            }

            // Simpan hanya section yang digenerate (preserve hasil mode lain)
            try {
                $update = [];
                if (!empty($result['niche']))    $update['shorts_description'] = $result['niche'];
                if (isset($result['topics']))    $update['topics'] = json_encode($result['topics']);
                if (isset($result['long_form'])) $update['scripts'] = json_encode($result['long_form']);
                if (isset($result['umum']))      $update['visual_sequences'] = json_encode($result['umum']);
                if ($update) {
                    AiGeneration::updateOrCreate(
                        ['song_id' => $song->id, 'user_id' => auth()->id()],
                        $update
                    );
                }
            } catch (\Throwable $e) {
                // simpan history opsional — abaikan bila gagal
            }

            return response()->json([
                'success'   => true,
                'song'      => $song->title,
                'song_id'   => $song->id,
                'provider'  => $provider->name,
                'mode'      => $mode,
                'niche'     => $result['niche'] ?? null,
                'topics'    => $result['topics'] ?? null,
                'long_form' => $result['long_form'] ?? null,
                'umum'      => $result['umum'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI v2 generate error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* ===================== Jadwalkan ke Calendar ===================== */

    public function scheduleToCalendar(Request $request)
    {
        $data = $request->validate([
            'song_id'           => 'nullable|integer',
            'start_date'        => 'required|date',
            'platforms'         => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.text'      => 'required|string',
            'items.*.image_prompt' => 'nullable|string',
            'items.*.type'      => 'nullable|string',
        ]);

        $date = \Carbon\Carbon::parse($data['start_date']);
        $count = 0;
        foreach ($data['items'] as $i => $item) {
            $planDate = (clone $date)->addDays($i); // satu konten per hari
            $type = $item['type'] ?? 'short';
            ContentPlan::create([
                'plan_date'    => $planDate->toDateString(),
                'song_id'      => $data['song_id'] ?: null,
                'platforms'    => $data['platforms'] ?? 'TikTok,Instagram',
                'content_type' => $type,
                'title'        => mb_substr($item['text'], 0, 240),
                'status'       => 'rencana',
                'notes'        => $item['image_prompt']
                    ? ($type === 'long' ? "📝 Narasi:\n" : ($type === 'umum' ? "🌐 Tema backsound:\n" : "🎨 Image prompt:\n")) . $item['image_prompt']
                    : null,
            ]);
            $count++;
        }

        return response()->json(['success' => true, 'count' => $count]);
    }
}
