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
                'name'   => 'Margonoandi',
                'email'  => 'bot@margonoandi.my.id',
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
            "Halo {$first}! 👋 Selamat datang di keluarga Margonoandi 🎶 Seneng banget kamu berani gabung lebih dulu.",
            "Jujur ya — aplikasi ini masih tahap beta, dan untuk sekarang masih menumpang di web pribadi Margonoandi. Tapi kalau dukungan kalian besar, kita serius bangun rumah baru yang layak buat ekosistem ini. 🏠",
            "Bantu kami dong: bagikan ke teman-teman musisimu — gitaris, basis, drummer, vokalis, siapa pun yang cinta musik. Langkah besar ini dimulai dari kamu yang berani gabung lebih dulu. 🔥",
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
                'message', 'Margonoandi menyambutmu 🎶',
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

    /** System prompt: persona + visi/misi + data lagu nyata dari DB (anti-ngarang). */
    protected static function systemPrompt(): string
    {
        $songLines = '';
        try {
            $songs = Song::where('is_active', true)->orderBy('track_number')
                ->get(['title', 'era', 'story_hook', 'key_signature', 'chords']);
            $songLines = $songs->map(function ($s) {
                $b = '- ' . $s->title;
                if ($s->era)           $b .= ' (' . $s->era . ')';
                if ($s->story_hook)    $b .= ' — ' . Str::limit($s->story_hook, 90);
                if ($s->key_signature) $b .= ' [key ' . $s->key_signature . ']';
                if (!empty($s->chords)) $b .= ' [ada chord]';
                return $b;
            })->implode("\n");
        } catch (\Throwable $e) {}
        if ($songLines === '') $songLines = '(daftar lagu belum tersedia)';

        return implode("\n", [
            'Kamu adalah "Margonoandi", bot ramah di aplikasi fanbase musik Margonoandi.',
            'GAYA BAHASA: anak muda Indonesia banget (santai, slang ringan seperti "wih", "nih", "yuk", "bareng") TAPI tetap SOPAN & hangat. Pakai 1-2 emoji secukupnya. Jangan kaku/formal, jangan alay.',
            'FORMAT WAJIB: ini CHAT biasa — tulis MENGALIR seperti ngobrol di WhatsApp. DILARANG KERAS pakai markdown atau simbol format (tanda **, *, __, #, -, •) dan DILARANG bikin daftar/list/poin. Balasan PENDEK: maksimal 2-3 kalimat. Fokus HANYA ke yang ditanya, jangan menjelaskan semuanya sekaligus.',
            'TUGAS: menyambut & membantu user, menjawab seputar aplikasi & lagu Margonoandi, dan sesekali mengajak mereka mendukung (membagikan ke teman musisinya) tanpa memaksa.',
            'VISI: ini sebuah gerakan ekosistem musik Indonesia "dimulai dari kamarmu" untuk SEMUA peran (gitaris, basis, drummer, vokalis, keyboardis, songwriter, arranger, event/wedding organizer, promotor, penikmat musik). Statusnya MASIH BETA dan untuk sekarang menumpang di web pribadi Margonoandi; kalau dukungan besar, akan dibangun "rumah baru" yang layak.',
            'FITUR APP (sebut SEPERLUNYA saja, jangan didaftar semua): pemutar lagu, belajar chord gitar/piano/ukulele/bass + tuner gitar, komunitas (Aku & Kita), chat (Dia), direktori musisi & cari personil band, catatan pribadi. Kalau ditanya "ada fitur apa", sebut 1-3 yang paling relevan dengan santai lalu tawarkan cerita lebih, JANGAN sebut semua.',
            'LAGU MARGONOANDI (HANYA gunakan data ini; JANGAN mengarang judul/cerita/fakta lain):',
            $songLines,
            'TETAP ON-TOPIC: HANYA bahas seputar musik, lagu Margonoandi, dan fitur aplikasi ini. Kalau user ngobrol di luar topik (nanya kabar, "udah makan belum", curhat pribadi, dll), tanggapi singkat & ramah SECUKUPNYA lalu arahkan balik ke musik/aplikasi dengan halus. Jangan ikut ngelantur.',
            'ATURAN: JANGAN PERNAH menyebut judul lagu, lirik, atau cerita yang TIDAK ADA persis di daftar lagu di atas (dilarang mengarang/menebak). Kalau ditanya lagu yang tak ada di daftar, akui jujur belum tahu/belum ada. Jangan menjanjikan fitur yang belum ada. Selalu Bahasa Indonesia, jangan kasar/SARA.',
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
            return "Halo {$first}! 👋 Seneng kamu mampir. Mau eksplor apa nih — dengerin lagu, belajar chord, atau stem gitar? 🎶";
        if ($has(['gratis', 'bayar', 'harga', 'biaya', 'premium', 'langganan']))
            return "Tenang, semua fitur GRATIS kok 🙌 Cukup login. Kalau suka, bantu bagikan ke teman ya 🔥";
        if ($has(['stem', 'tuner', 'setel', 'setem', 'fals']))
            return "Buat stem gitar, buka menu Kamu → Tuner ya. Tinggal petik senarnya, nanti dipandu 🎸";
        if ($has(['chord', 'kunci', 'gitar', 'piano', 'ukulele', 'bass', 'genjreng']))
            return "Belajar chord ada di menu Kamu → Chord — lengkap gitar, piano, ukulele, sama bass, plus bisa dibunyikan 🎶 Cobain deh!";
        if ($has(['dukung', 'support', 'bagi', 'share', 'sebar', 'promosi', 'ajak']))
            return "Makasih banyak! 🙏🔥 Bantu kami dengan membagikan link aplikasi ini ke teman-teman musisimu ya. Gerakan ini dimulai dari kamu 💪";
        if ($has(['lagu', 'dengar', 'denger', 'musik', 'putar', 'play']))
            return "Cus dengerin lagu-lagunya di halaman utama 🎧 Sambil belajar chord-nya juga bisa. Ada genre favoritmu?";
        if ($has(['beta', 'rumah', 'serius', 'kapan', 'rilis', 'resmi']))
            return "Iya nih, kita masih BETA & numpang di web pribadi Margonoandi dulu. Kalau dukungan kalian gede, kita bangun rumah baru yang layak buat ekosistem ini 🏠🔥";
        if ($has(['siapa', 'bot', 'asisten', 'kamu apa']))
            return "Aku bot kecilnya Margonoandi 🤖 — temen ngobrol sekaligus pemandu kamu di aplikasi ini. Tanya aja apa pun soal app atau lagunya 🎶";
        if ($has(['makasih', 'terima kasih', 'thanks', 'mantap', 'keren', 'bagus']))
            return "Sama-sama! 🙌 Seneng bisa bantu. Jangan lupa ajak temen ya biar makin rame 🔥";

        return "Hmm, aku belum yakin nangkep maksudmu 😅 Tapi aku bisa bantu soal lagu, belajar chord, tuner, atau cara dukung gerakan ini. Mau yang mana, {$first}? 🎶";
    }
}
