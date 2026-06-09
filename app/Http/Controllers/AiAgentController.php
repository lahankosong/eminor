<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\AiGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAgentController extends Controller
{
    public function index()
    {
        $songs = Song::where('is_active', true)->orderBy('track_number')->get();
        return view('admin.ai-agent', compact('songs'));
    }

    protected function callClaude(string $prompt): string
    {
        $response = Http::timeout(120)->withHeaders([
            'x-api-key'         => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-haiku-4-5-20251001',
            'max_tokens' => 8000,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->body());
        }

        return $response->json('content.0.text', '');
    }

    public function generate(Request $request, $id)
    {
        $song = Song::findOrFail($id);

        $lyricsSection = $song->lyrics
            ? "Lirik lagu:\n" . $song->lyrics
            : "Lirik belum tersedia. Gunakan judul dan deskripsi sebagai panduan.";

        $hookSource = $song->story_hook ?? $song->description ?? $song->title;

        $prompt = <<<EOT
Kamu adalah AI content strategist musik Indonesia yang menulis seperti manusia asli — hangat, relatable, tidak kaku. Kamu paham psikologi audiens 25-40 tahun yang galau di malam hari dan scrolling HP sebelum tidur.

DATA LAGU:
- Judul: {$song->title}
- Era: {$song->era}
- Hook utama: {$hookSource}
- Key: {$song->key_signature}
{$lyricsSection}

IDENTITAS VISUAL BRAND (wajib konsisten di semua output):
- Palet: retro blue, warm cream, burnt orange
- Mood: sinematik, intimate, melankolis tapi tidak lebay
- Karakter: orang Indonesia 25-35 tahun, urban, thoughtful
- Gaya: candid, natural light, depth of field lensa 50mm atau 85mm

===

TAHAP 1 — 3 TOPIK
Pecah hook menjadi 3 sudut pandang kejadian sehari-hari yang sangat spesifik dan berbeda. Bukan yang generik. Contoh bagus: "ngetik panjang terus dihapus semua", bukan "kesepian".

TAHAP 2 — 15 NASKAH CAPTION
Per topik: 3 variasi × 5 baris sekuensial. Baris ke-5 adalah punchline yang nyambung dengan hook lagu. Gaya: seperti notes HP jam 2 pagi. Boleh gue/lo atau aku/kamu.

TAHAP 3 — VISUAL SEQUENCE (4 adegan per topik = 20 detik)
Per topik: 4 adegan yang saling nyambung. Masing-masing adegan 5 detik. Wajib ada:
- visual: apa yang terlihat (spesifik: warna baju, ekspresi, lokasi detail)
- camera: angle dan gerakan kamera
- action: apa yang bergerak/terjadi
- lighting: sumber, arah, warna cahaya
- transition_to_next: cara pindah ke adegan berikutnya

TAHAP 4 — PROMPT DREAMINA (per topik, 1 prompt per topik)
Dari 4 adegan, buat 1 prompt bahasa Inggris max 500 karakter untuk Dreamina text-to-video. Harus mencakup keseluruhan 20 detik sequence. Sertakan: palet brand (retro blue, cream, burnt orange), gaya sinematik, karakter Indonesia.

TAHAP 5 — DESKRIPSI & HASHTAG
Deskripsi YouTube Shorts max 120 karakter, personal, bahasa Indonesia. Plus hashtag relevan.

===

Response dalam format JSON valid tanpa markdown tanpa backtick:
{
  "topics": [
    {"id": 1, "label": "max 5 kata"},
    {"id": 2, "label": "max 5 kata"},
    {"id": 3, "label": "max 5 kata"},
    {"id": 4, "label": "max 5 kata"},
    {"id": 5, "label": "max 5 kata"}
  ],
  "scripts": [
    {
      "topic_id": 1,
      "variations": [
        {"v": 1, "lines": ["baris1","baris2","baris3","baris4","punchline"]},
        {"v": 2, "lines": ["baris1","baris2","baris3","baris4","punchline"]},
        {"v": 3, "lines": ["baris1","baris2","baris3","baris4","punchline"]}
      ]
    },
    {"topic_id": 2, "variations": [{"v":1,"lines":["","","","",""]},{"v":2,"lines":["","","","",""]},{"v":3,"lines":["","","","",""]}]},
    {"topic_id": 3, "variations": [{"v":1,"lines":["","","","",""]},{"v":2,"lines":["","","","",""]},{"v":3,"lines":["","","","",""]}]},
    {"topic_id": 4, "variations": [{"v":1,"lines":["","","","",""]},{"v":2,"lines":["","","","",""]},{"v":3,"lines":["","","","",""]}]},
    {"topic_id": 5, "variations": [{"v":1,"lines":["","","","",""]},{"v":2,"lines":["","","","",""]},{"v":3,"lines":["","","","",""]}]}
  ],
  "visual_sequences": [
    {
      "topic_id": 1,
      "total_duration": 20,
      "scenes": [
        {"order":1,"duration":5,"visual":"...","camera":"...","action":"...","lighting":"...","transition_to_next":"..."},
        {"order":2,"duration":5,"visual":"...","camera":"...","action":"...","lighting":"...","transition_to_next":"..."},
        {"order":3,"duration":5,"visual":"...","camera":"...","action":"...","lighting":"...","transition_to_next":"..."},
        {"order":4,"duration":5,"visual":"...","camera":"...","action":"...","lighting":"...","transition_to_next":"..."}
      ]
    },
    {"topic_id":2,"total_duration":20,"scenes":[{"order":1,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":2,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":3,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":4,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""}]},
    {"topic_id":3,"total_duration":20,"scenes":[{"order":1,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":2,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":3,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":4,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""}]},
    {"topic_id":4,"total_duration":20,"scenes":[{"order":1,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":2,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":3,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":4,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""}]},
    {"topic_id":5,"total_duration":20,"scenes":[{"order":1,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":2,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":3,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""},{"order":4,"duration":5,"visual":"","camera":"","action":"","lighting":"","transition_to_next":""}]}
  ],
  "dreamina_prompts": [
    {"topic_id": 1, "prompt": "english prompt max 500 chars for 20s video..."},
    {"topic_id": 2, "prompt": "..."},
    {"topic_id": 3, "prompt": "..."},
    {"topic_id": 4, "prompt": "..."},
    {"topic_id": 5, "prompt": "..."}
  ],
  "shorts_description": "max 120 karakter personal",
  "hashtags": "#margonoandi #laguindie #musikindonesia #lagugalau"
}
EOT;

        try {
            // Di dalam method generate(), ganti bagian ini:

            $raw = $this->callClaude($prompt);

            // === PERBAIKAN PARSING ===
            // 1. Cari JSON pertama yang dimulai dengan { dan diakhiri dengan }
            preg_match('/\{[^{}]*+(?:(?R)[^{}]*)*+\}/s', $raw, $matches);

            if (isset($matches[0])) {
                $clean = $matches[0];
            } else {
                // 2. Fallback: hapus markdown
                $clean = preg_replace('/^```json\\s*|\\s*```$/i', '', trim($raw));
                $clean = preg_replace('/^```\\s*|\\s*```$/i', '', $clean);
            }

            // 3. Coba decode
            $result = json_decode($clean, true);

            // 4. Jika masih gagal, coba ekstrak dari dalam teks (misal: "Here is your JSON: {...}")
            if (!$result && preg_match('/\{[^\}]*\}/', $raw, $simpleMatch)) {
                $result = json_decode($simpleMatch[0], true);
            }

            // 5. Log error jika tetap gagal
            if (!$result) {
                Log::error('JSON parse failed', [
                    'raw_preview' => substr($raw, 0, 1000),
                    'clean_preview' => substr($clean, 0, 500)
                ]);
                return response()->json(['error' => 'Gagal parse response AI. Coba lagi.'], 422);
            }

            // Simpan ke database
            AiGeneration::updateOrCreate(
                ['song_id' => $song->id, 'user_id' => auth()->id()],
                [
                    'topics'             => json_encode($result['topics'] ?? []),
                    'scripts'            => json_encode($result['scripts'] ?? []),
                    'visual_sequences'   => json_encode($result['visual_sequences'] ?? []),
                    'dreamina_prompts'   => json_encode($result['dreamina_prompts'] ?? []),
                    'shorts_description' => $result['shorts_description'] ?? '',
                    'hashtags'           => $result['hashtags'] ?? '',
                ]
            );

            return response()->json(['success' => true, 'song' => $song->title, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('Generate error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveSelected(Request $request)
    {
        $validated = $request->validate([
            'song_id' => 'required|exists:songs,id',
            'topic_id' => 'nullable|integer',
            'variation_id' => 'nullable|integer',
            'selected_hook' => 'nullable|string',
            'selected_caption' => 'nullable|string',
            'selected_prompt' => 'nullable|string',
        ]);
        
        $generation = AiGeneration::where('song_id', $validated['song_id'])
            ->where('user_id', auth()->id())
            ->latest()
            ->first();
            
        if ($generation) {
            $generation->update([
                'selected_topic_id' => $validated['topic_id'] ?? $generation->selected_topic_id,
                'selected_variation_id' => $validated['variation_id'] ?? $generation->selected_variation_id,
                'selected_hook' => $validated['selected_hook'] ?? $generation->selected_hook,
                'selected_caption' => $validated['selected_caption'] ?? $generation->selected_caption,
                'selected_prompt' => $validated['selected_prompt'] ?? $generation->selected_prompt,
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    public function getHistory($songId)
    {
        $generations = AiGeneration::where('song_id', $songId)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json(['data' => $generations]);
    }
}