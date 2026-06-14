<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\AiGeneration;
use App\Models\AiProvider;
use App\Models\ContentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                // hanya hasil format v2 (punya narrations)
                if (!is_array($topics) || empty($topics[0]['narrations'])) continue;
                $saved[$g->song_id] = [
                    'niche'     => $g->shorts_description,
                    'topics'    => $topics,
                    'long_form' => json_decode($g->scripts, true),
                ];
                if ($lastSongId === null) $lastSongId = $g->song_id;
            }
        } catch (\Throwable $e) {
            // tabel belum ada / kolom beda — abaikan
        }

        return view('admin.ai-agent', compact('songs', 'providers', 'saved', 'lastSongId'));
    }

    /* ===================== Provider CRUD ===================== */

    public function storeProvider(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'base_url' => 'required|string|max:255',
            'model'    => 'required|string|max:120',
            'format'   => 'required|in:openai,anthropic',
            'api_key'  => 'nullable|string|max:300',
        ]);
        $data['enabled'] = true;
        AiProvider::create($data);
        return back()->with('success', 'Provider AI "' . $data['name'] . '" disimpan.');
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

        $prompt = <<<EOT
Kamu adalah content strategist musik Indonesia yang menulis seperti manusia asli — hangat, relatable, tidak kaku. Audiens: 25–40 tahun, galau malam hari, scrolling HP sebelum tidur.

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
2. TOPIK: pecah niche jadi 3–5 topik kejadian sehari-hari yang SANGAT spesifik & berbeda (bukan generik). Tiap topik max 5 kata label.
3. NARASI: tiap topik buat 5 narasi pendek (kata-kata pendek & punchy, gaya caption/hook notes HP jam 2 pagi, 1–2 kalimat, Indonesia).
4. IMAGE PROMPT: tiap narasi buat 1 prompt gambar (BAHASA INGGRIS, untuk text-to-image, max 400 karakter). WAJIB format VERTIKAL 9:16 untuk video short — sertakan frasa "vertical 9:16 aspect ratio, portrait". Wajib mencakup palet brand + gaya sinematik + karakter Indonesia.
5. LONG FORM (konten video 3–5 menit): buat 1 video panjang — judul menarik + naskah NARASI mengalir bahasa Indonesia ~500–750 kata (durasi dibaca 3–5 menit, storytelling personal sesuai niche, ada pembuka-isi-penutup) + 6–8 image prompt (BAHASA INGGRIS, vertical 9:16, palet brand) untuk mengilustrasikan narasi.

Balas HANYA JSON valid tanpa markdown tanpa backtick:
{
  "niche": "...",
  "topics": [
    {
      "id": 1,
      "label": "max 5 kata",
      "narrations": [
        {"text": "narasi pendek", "image_prompt": "english image prompt"},
        {"text": "...", "image_prompt": "..."},
        {"text": "...", "image_prompt": "..."},
        {"text": "...", "image_prompt": "..."},
        {"text": "...", "image_prompt": "..."}
      ]
    }
  ],
  "long_form": {
    "title": "judul video 3-5 menit",
    "duration_estimate": "± 4 menit",
    "narration": "naskah narasi bahasa Indonesia mengalir 500-750 kata (3-5 menit)",
    "scenes": [
      {"image_prompt": "english vertical 9:16 image prompt"}
    ]
  }
}
Jumlah topics 3 sampai 5. Tiap topik WAJIB 5 narasi. long_form.scenes berisi 6-8 image prompt.
EOT;

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

            if (!$result || empty($result['topics'])) {
                Log::error('AI v2 parse failed', ['preview' => substr($raw, 0, 800)]);
                return response()->json(['error' => 'Gagal memproses jawaban AI. Coba lagi / ganti provider.'], 422);
            }

            try {
                AiGeneration::updateOrCreate(
                    ['song_id' => $song->id, 'user_id' => auth()->id()],
                    [
                        'topics'             => json_encode($result['topics']),
                        'shorts_description' => $result['niche'] ?? '',
                        'scripts'            => json_encode($result['long_form'] ?? null),
                    ]
                );
            } catch (\Throwable $e) {
                // simpan history opsional — abaikan bila gagal
            }

            return response()->json([
                'success'  => true,
                'song'     => $song->title,
                'song_id'  => $song->id,
                'provider' => $provider->name,
                'niche'    => $result['niche'] ?? '',
                'topics'   => $result['topics'],
                'long_form' => $result['long_form'] ?? null,
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
                    ? ($type === 'long' ? "📝 Narasi:\n" : "🎨 Image prompt:\n") . $item['image_prompt']
                    : null,
            ]);
            $count++;
        }

        return response()->json(['success' => true, 'count' => $count]);
    }
}
