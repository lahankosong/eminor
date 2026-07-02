<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AiProvider;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WelcomeBot
{
    /** Akun bot resmi pengirim sambutan (dibuat sekali, dipakai ulang). */
    public static function botUser(): User
    {
        return User::firstOrCreate(
            ['google_id' => 'bot-margonoandi'],
            [
                'name'   => 'EMINOR',
                'email'  => 'bot@eminor.margonoandi.my.id',
                'avatar' => asset('images/Margonoandi.jpeg'),
            ]
        );
    }

    /** Kirim rangkaian pesan sambutan ke user lewat chat (Dia). Idempoten: skip kalau sudah pernah disambut. Return true bila benar-benar dikirim. */
    public static function sendWelcome(User $newUser): bool
    {
        $bot = self::botUser();
        if ($bot->id === $newUser->id) return false;

        $minId = min($bot->id, $newUser->id);
        $maxId = max($bot->id, $newUser->id);

        $conv = Conversation::firstOrCreate([
            'user_one_id' => $minId,
            'user_two_id' => $maxId,
        ]);

        // Sudah pernah disambut? (ada pesan dari bot) -> jangan kirim lagi
        if (Message::where('conversation_id', $conv->id)->where('user_id', $bot->id)->exists()) {
            return false;
        }

        $first = trim(strtok($newUser->name ?? 'kawan', ' ')) ?: 'kawan';

        $msgs = [
            "Halo {$first}! 👋 Selamat datang di <span>E</span>MINOR — ekosistem musik indie Indonesia yang dibangun dari kamar, untuk semua musisi.",
            "EMINOR lahir karena satu kenyataan pahit: musisi asli makin susah didengar di platform besar. Sudah coba FB Ads, TikTok Ads, Google Ads — tapi algoritmanya dikuasai konten kreator dari berbagai profesi, bukan musisi murni. Daripada terus berjuang di platform orang, kita bangun rumah sendiri. 🏠",
            "Di sini kamu bisa bikin profil musisi, cari personil band, pakai tools gratis (chord, tuner, BPM, potong lagu, dll), dan tumbuh bareng komunitas musisi Indonesia. Ajak teman musisimu bergabung — ekosistem ini besar karena kalian. 🔥",
        ];

        $last = '';
        foreach ($msgs as $body) {
            Message::create([
                'conversation_id' => $conv->id,
                'user_id'         => $bot->id,
                'body'            => $body,
            ]);
            $last = $body;
        }

        $conv->update(['last_message' => $last, 'last_message_at' => now()]);

        try {
            NotifHelper::send(
                $newUser->id, $bot->id,
                'message', 'EMINOR menyambutmu 🎶',
                $msgs[0], url('/dia/conversation/' . $conv->id)
            );
        } catch (\Throwable $e) {}

        return true;
    }

    /** Apakah percakapan ini dengan bot? (cek by google_id, hemat query) */
    public static function isBotId($userId): bool
    {
        return User::where('id', $userId)->where('google_id', 'bot-margonoandi')->exists();
    }

    /** Buat balasan bot untuk percakapan (AI DeepSeek -> fallback rule-based). */
    public static function reply(Conversation $conv, User $fromUser): ?Message
    {
        $bot = self::botUser();
        if ($fromUser->id === $bot->id) return null;

        $text = '';
        try { $text = self::cleanReply(self::aiReply($conv, $bot)); } catch (\Throwable $e) { $text = ''; }
        if ($text === '') {
            $lastUser = Message::where('conversation_id', $conv->id)
                ->where('user_id', '!=', $bot->id)->latest('id')->value('body') ?? '';
            $text = self::ruleReply((string) $lastUser, $fromUser);
        }
        $text = self::softCap($text, 700);
        if ($text === '') return null;

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'user_id'         => $bot->id,
            'body'            => $text,
        ]);
        $conv->update(['last_message' => $text, 'last_message_at' => now()]);
        return $msg;
    }

    /** Panggil LLM (DeepSeek / OpenAI-compatible / Anthropic) dengan grounding. Return '' jika gagal. */
    protected static function aiReply(Conversation $conv, User $bot): string
    {
        $p = AiProvider::where('enabled', true)->whereNotNull('api_key')
                ->where('base_url', 'like', '%deepseek%')->orderBy('id')->first()
            ?: AiProvider::where('enabled', true)->whereNotNull('api_key')
                ->where('kind', 'text')->orderBy('id')->first();
        if (!$p) return '';
        $key = $p->api_key;
        if (!$key) return '';

        $hist = Message::where('conversation_id', $conv->id)
            ->orderBy('id', 'desc')->take(12)->get()->reverse()->values();
        $msgs = [['role' => 'system', 'content' => self::systemPrompt()]];
        foreach ($hist as $m) {
            $msgs[] = [
                'role'    => ($m->user_id === $bot->id) ? 'assistant' : 'user',
                'content' => (string) $m->body,
            ];
        }
        if (count($msgs) === 1) return '';

        if ($p->format === 'anthropic') {
            $resp = Http::timeout(18)->withHeaders([
                'x-api-key' => $key, 'anthropic-version' => '2023-06-01', 'content-type' => 'application/json',
            ])->post(rtrim($p->base_url, '/') . '/messages', [
                'model'      => $p->model,
                'max_tokens' => 500,
                'system'     => $msgs[0]['content'],
                'messages'   => array_slice($msgs, 1),
            ]);
            return $resp->successful() ? (string) $resp->json('content.0.text', '') : '';
        }

        $resp = Http::timeout(18)->withToken($key)->post(rtrim($p->base_url, '/') . '/chat/completions', [
            'model'       => $p->model ?: 'deepseek-chat',
            'messages'    => $msgs,
            'temperature' => 0.85,
            'max_tokens'  => 500,
        ]);
        return $resp->successful() ? (string) $resp->json('choices.0.message.content', '') : '';
    }

    /** System prompt: persona EMINOR — fokus ekosistem, bukan promosi lagu. */
    protected static function systemPrompt(): string
    {
        return implode("\n", [
            'Kamu adalah bot ramah EMINOR — ekosistem musik indie Indonesia.',

            'GAYA BAHASA: anak muda Indonesia (santai, slang ringan: "wih", "nih", "yuk", "bareng") tapi SOPAN & hangat. 1-2 emoji secukupnya. Jangan kaku, jangan alay.',
            'FORMAT WAJIB: chat biasa — mengalir seperti WhatsApp. DILARANG pakai markdown (**, *, #, -, •) atau daftar/list. Balasan PENDEK: maksimal 2-3 kalimat. Fokus ke yang ditanya saja.',

            'CERITA AWAL EMINOR (gunakan ini kalau ditanya sejarah/latar belakang):',
            'EMINOR lahir dari frustrasi seorang musisi kamar bernama Margonoandi. Lagunya awalnya di YouTube (@rahmento), kemudian diproduksi ulang dengan Suno dan dirilis ke agregator musik. Sudah promosi habis-habisan — Facebook Ads, TikTok Ads, Google Ads — tapi tidak ada yang menembus algoritma. Bukan karena lagunya jelek, tapi karena platform-platform itu sekarang dikuasai oleh content creator dari berbagai profesi non-musik yang lebih tahu cara main algoritma. Musisi asli makin susah didengar. Dari frustrasi itulah muncul ide: daripada berjuang sendirian di platform orang, kenapa tidak bangun ekosistem sendiri khusus untuk musisi? Itulah EMINOR.',

            'VISI & MISI EMINOR:',
            'EMINOR adalah gerakan "dimulai dari kamarmu" — tempat semua peran musik tumbuh bareng: gitaris, bassist, drummer, vokalis, keyboardis, songwriter, arranger, event organizer, promotor, sampai penikmat musik. Bukan tentang satu artis. Ini tentang seluruh ekosistem musik Indonesia.',

            'FITUR APP (sebut 1-3 yang relevan, jangan didaftar semua): tools gratis (chord builder, tuner, BPM, potong lagu, hapus vokal, rate card, EPK, dll), komunitas Stage & Studio, chat Room, direktori musisi, cari personil band, dan pencatatan pribadi musisi.',

            'TOPIK YANG BOLEH DIBAHAS: visi/misi/cerita EMINOR, fitur aplikasi, tips musik umum, cara tumbuh sebagai musisi indie, ekosistem musik Indonesia. JANGAN promosikan lagu atau artis tertentu — EMINOR bukan tentang satu musisi.',

            'TETAP ON-TOPIC: kalau user nanya di luar topik musik/app, tanggapi singkat lalu arahkan balik dengan halus.',
            'ATURAN: jangan menjanjikan fitur yang belum ada. Selalu Bahasa Indonesia. Jangan kasar/SARA. JANGAN pernah menyebut nama "Margonoandi" atau "Rakhman Andi" dalam balasan kecuali user yang duluan menyebutnya dan kamu cukup bilang "iya, beliau yang mendirikan EMINOR" lalu fokus balik ke ekosistem.',
        ]);
    }

    /** Bersihkan markdown/list dari balasan AI supaya tampil natural di chat. */
    protected static function cleanReply(string $t): string
    {
        $t = trim((string) $t);
        if ($t === '') return '';
        $t = preg_replace('/\*\*(.+?)\*\*/s', '$1', $t);          // **tebal**
        $t = preg_replace('/__(.+?)__/s', '$1', $t);              // __tebal__
        $t = preg_replace('/(?<!\*)\*(?!\*)(.+?)\*(?!\*)/s', '$1', $t); // *miring*
        $t = preg_replace('/`{1,3}([^`]*)`{1,3}/s', '$1', $t);    // `code`
        $t = preg_replace('/^\s{0,3}#{1,6}\s*/m', '', $t);        // # heading
        $t = preg_replace('/^\s*(?:[-*•]|\d+[.)])\s+/m', '', $t); // bullet/angka di awal baris
        $t = preg_replace('/[ \t]{2,}/', ' ', $t);               // rapikan spasi
        $t = preg_replace('/\n{3,}/', "\n\n", $t);
        return trim($t);
    }

    /** Potong di batas kalimat/spasi (bukan mid-kata) bila kepanjangan. */
    protected static function softCap(string $t, int $max): string
    {
        $t = trim($t);
        if (mb_strlen($t) <= $max) return $t;
        $cut = mb_substr($t, 0, $max);
        $p = max((int) mb_strrpos($cut, '. '), (int) mb_strrpos($cut, '! '), (int) mb_strrpos($cut, '? '));
        if ($p < 150) { $sp = mb_strrpos($cut, ' '); $p = ($sp !== false) ? (int) $sp : $max; }
        return rtrim(mb_substr($cut, 0, $p + 1));
    }

    /** Balasan cadangan tanpa AI (keyword), gaya anak muda sopan. */
    protected static function ruleReply(string $text, User $user): string
    {
        $t = mb_strtolower($text);
        $first = trim(strtok($user->name ?? 'kawan', ' ')) ?: 'kawan';
        $has = function ($keys) use ($t) {
            foreach ((array) $keys as $k) { if ($k !== '' && strpos($t, $k) !== false) return true; }
            return false;
        };

        if ($t === '' || $has(['halo', 'hai', 'hello', 'assalam', 'pagi', 'siang', 'sore', 'malam']))
            return "Halo {$first}! 👋 Selamat datang di EMINOR — ekosistem musik indie Indonesia. Mau eksplor tools, cari personil band, atau mau tau cerita kenapa EMINOR lahir? 🎸";
        if ($has(['gratis', 'bayar', 'harga', 'biaya', 'premium', 'langganan']))
            return "Semua fitur GRATIS kok {$first} 🙌 Cukup login Google. Kalau suka, bantu sebarin ke teman musisimu ya 🔥";
        if ($has(['stem', 'tuner', 'setel', 'setem', 'fals']))
            return "Buat stem gitar ada di menu Tools → Tuner {$first}. Tinggal petik senarnya, langsung dipandu 🎸";
        if ($has(['chord', 'kunci', 'gitar', 'piano', 'ukulele', 'bass', 'genjreng']))
            return "Ada Chord Builder di Tools — lengkap gitar, piano, ukulele, sama bass, bisa dibunyikan juga 🎶 Cek aja di menu Tools!";
        if ($has(['dukung', 'support', 'bagi', 'share', 'sebar', 'ajak']))
            return "Makasih banget {$first}! 🙏 Cara paling ampuh dukung EMINOR ya bagiin ke teman-teman musisimu. Gerakan ini tumbuh dari kalian 💪";
        if ($has(['algoritma', 'platform', 'tiktok', 'facebook', 'instagram', 'youtube', 'spotify', 'streaming']))
            return "Nah itu dia masalah kita semua — platform besar sekarang dikuasai content creator berbagai profesi, bukan musisi murni. Makanya EMINOR hadir: ekosistem kita sendiri 🏠";
        if ($has(['kenapa', 'awal', 'sejarah', 'cerita', 'mulai', 'lahir', 'buat', 'bikin', 'didirikan']))
            return "EMINOR lahir dari frustrasi musisi yang sudah promosi di FB, TikTok, Google Ads tapi algoritmanya dikuasai konten kreator berbagai profesi. Daripada terus berjuang di platform orang, mendingan bangun ekosistem sendiri — buat semua musisi Indonesia 🎯";
        if ($has(['personil', 'cari', 'band', 'kolaborasi', 'anggota', 'join', 'drummer', 'gitaris', 'bassist', 'vokalis']))
            return "Ada fitur direktori musisi di Studio {$first} — bisa bikin profil, lihat skill musisi lain, dan posting lowongan personil band. Cus coba! 🎸";
        if ($has(['tools', 'tool', 'fitur', 'apa aja', 'apa saja', 'ada apa']))
            return "Ada banyak tools gratis nih: tuner, chord builder, BPM, potong lagu, hapus vokal, EPK generator, rate card, sampai release planner — semuanya di menu Tools. Mau yang mana dulu? 🛠️";
        if ($has(['beta', 'rumah', 'kapan', 'rilis', 'resmi', 'selesai']))
            return "EMINOR masih BETA dan terus berkembang {$first}. Kalau makin banyak musisi gabung, ekosistem ini makin kuat dan kita bisa bangun sesuatu yang lebih besar lagi 🏠🔥";
        if ($has(['siapa', 'bot', 'asisten', 'kamu apa', 'kamu siapa']))
            return "Aku bot EMINOR 🤖 — pemandu kamu di ekosistem ini. Tanya soal fitur, cerita EMINOR, atau cara tumbuh sebagai musisi indie — aku siap!";
        if ($has(['makasih', 'terima kasih', 'thanks', 'mantap', 'keren', 'bagus', 'oke', 'sip']))
            return "Sama-sama {$first}! 🙌 Jangan lupa ajak temen musisimu gabung — makin rame makin seru 🔥";

        return "Aku bisa bantu soal fitur EMINOR, tools gratis, cari personil, atau cerita kenapa ekosistem ini dibuat. Mau mulai dari mana, {$first}? 🎸";
    }
}