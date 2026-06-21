<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

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

    /** Kirim rangkaian pesan sambutan ke user baru lewat chat (Dia). */
    public static function sendWelcome(User $newUser): void
    {
        $bot = self::botUser();
        if ($bot->id === $newUser->id) return;

        $minId = min($bot->id, $newUser->id);
        $maxId = max($bot->id, $newUser->id);

        $conv = Conversation::firstOrCreate([
            'user_one_id' => $minId,
            'user_two_id' => $maxId,
        ]);

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
    }
}
