<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\GigPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BotSeeder extends Seeder
{
    private array $bots = [
        [
            'name'     => 'Budi Prasetyo',
            'email'    => 'budi.drummer@margonoandi.bot',
            'bot_type' => 'drummer',
            'avatar'   => 'https://ui-avatars.com/api/?name=Budi+Prasetyo&background=1e3a5f&color=38A8CC&size=128',
            'profile'  => [
                'roles'       => 'Drummer,Perkusi',
                'skill_level' => 'intermediate',
                'location'    => 'Yogyakarta',
                'genres'      => 'Rock,Pop,Indie',
                'bio'         => 'Drummer dengan 8 tahun pengalaman. Pernah manggung di berbagai event kampus dan kafe di Jogja. Bisa rekaman via Zoom/remote. Open kolaborasi!',
                'tip_info'    => null,
            ],
        ],
        [
            'name'     => 'Sari Dewi',
            'email'    => 'sari.vocalist@margonoandi.bot',
            'bot_type' => 'vocalist',
            'avatar'   => 'https://ui-avatars.com/api/?name=Sari+Dewi&background=2d1b4e&color=a855f7&size=128',
            'profile'  => [
                'roles'       => 'Vokalis,Penulis Lagu',
                'skill_level' => 'advanced',
                'location'    => 'Jakarta Selatan',
                'genres'      => 'Pop,Soul,R&B',
                'bio'         => 'Penyanyi dan penulis lagu independen. Sudah rilis 2 EP di Spotify. Aktif mencari kolaborator untuk proyek akustik dan pop indie.',
                'tip_info'    => null,
            ],
        ],
        [
            'name'     => 'Dimas Nugroho',
            'email'    => 'dimas.bassist@margonoandi.bot',
            'bot_type' => 'bassist',
            'avatar'   => 'https://ui-avatars.com/api/?name=Dimas+Nugroho&background=1a3a2a&color=22c55e&size=128',
            'profile'  => [
                'roles'       => 'Bassist,Gitaris',
                'skill_level' => 'intermediate',
                'location'    => 'Bandung',
                'genres'      => 'Funk,Jazz,Indie',
                'bio'         => 'Bassist dan gitaris yang gemar eksperimen genre. Suka jam session dan rekaman bedroom. Sedang cari band untuk gig reguler di Bandung.',
                'tip_info'    => null,
            ],
        ],
        [
            'name'     => 'Reni Kusuma',
            'email'    => 'reni.keys@margonoandi.bot',
            'bot_type' => 'keyboardist',
            'avatar'   => 'https://ui-avatars.com/api/?name=Reni+Kusuma&background=3a1a1a&color=f97316&size=128',
            'profile'  => [
                'roles'       => 'Keyboardist,Pianis',
                'skill_level' => 'advanced',
                'location'    => 'Solo',
                'genres'      => 'Classical,Pop,Jazz',
                'bio'         => 'Pianis klasik yang beralih ke pop dan jazz modern. Lulusan ISI Surakarta. Tersedia untuk session recording, wedding, dan event korporat.',
                'tip_info'    => null,
            ],
        ],
        [
            'name'     => 'Andi Wahyu',
            'email'    => 'andi.producer@margonoandi.bot',
            'bot_type' => 'producer',
            'avatar'   => 'https://ui-avatars.com/api/?name=Andi+Wahyu&background=1a2a3a&color=f59e0b&size=128',
            'profile'  => [
                'roles'       => 'Produser,Sound Engineer',
                'skill_level' => 'advanced',
                'location'    => 'Surabaya',
                'genres'      => 'Electronic,Indie,Hip-Hop',
                'bio'         => 'Music producer dan sound engineer dengan home studio lengkap. Sudah handle produksi untuk 20+ artis indie. Terbuka untuk kolaborasi online maupun offline.',
                'tip_info'    => null,
            ],
        ],
    ];

    private array $posts = [
        'Ada yang tau cara pitch lagu ke playlist Spotify editorial? Sudah 3 rilis tapi belum pernah masuk. Deadline-nya gimana?',
        'Baru selesai rekaman EP pertama setelah setahun nulis lagu. Rasanya campur aduk antara senang dan takut. Ada yang mau dengerin dulu sebelum rilis? 🎵',
        'Tips untuk yang baru mau debut: jangan tunggu sempurna. Rilis dulu, perbaiki di rilis berikutnya. Perfectionism membunuh karya.',
        'Siapa yang pakai DistroKid di sini? Gimana pengalamannya? Lagi banding-bandingan sama TuneCore sebelum rilis bulan depan.',
        'Open mic di Malioboro bulan depan — siapa yang mau gabung? Lagi nyari sesama musisi untuk kolaborasi sesi pendek.',
        'Baru tahu ternyata ada alat transpose kunci gratis di sini. Game changer buat yang sering dapat request lagu tapi kuncinya beda 😅',
        'Lagi nyari bassist untuk band indie pop di Yogyakarta. Kalau ada yang minat atau kenal siapa, tag di sini ya!',
        'Pertanyaan buat yang sudah pernah manggung berbayar: berapa rate kalian untuk wedding? Masih bingung nentuin harga.',
    ];

    private array $gigs = [
        [
            'title'       => 'Cari Drummer untuk Band Indie Pop Yogyakarta',
            'type'        => 'audisi',
            'description' => 'Band indie pop asal Yogyakarta butuh drummer tetap. Sudah punya materi 8 lagu, rencana rekaman EP akhir tahun. Latihan rutin 1x seminggu di Sleman.',
            'location'    => 'Yogyakarta',
        ],
        [
            'title'       => 'Open Mic Kedai Musik — Jakarta Selatan',
            'type'        => 'open_mic',
            'description' => 'Open mic rutin setiap Sabtu malam di Kedai Musik Kemang. Slot 10-15 menit, free entry untuk performer. Cocok untuk solo artist dan duo.',
            'location'    => 'Jakarta Selatan',
        ],
        [
            'title'       => 'Session Bassist untuk Rekaman EP',
            'type'        => 'session',
            'description' => 'Butuh session bassist untuk rekaman EP 5 lagu genre folk-pop. Bisa remote (file exchange) atau langsung di studio Bandung. Budget ada.',
            'location'    => 'Bandung',
        ],
        [
            'title'       => 'Cari Vokalis Wanita untuk Duo Akustik',
            'type'        => 'audisi',
            'description' => 'Gitaris/penulis lagu cari vokalis wanita untuk proyek duo akustik. Genre: indie folk, acoustic pop. Target: manggung di kafe dan event kecil di Jogja-Solo.',
            'location'    => 'Yogyakarta',
        ],
    ];

    public function run(): void
    {
        foreach ($this->bots as $i => $botData) {
            $user = User::updateOrCreate(
                ['email' => $botData['email']],
                [
                    'name'     => $botData['name'],
                    'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                    'avatar'   => $botData['avatar'],
                    'is_bot'   => true,
                    'bot_type' => $botData['bot_type'],
                ]
            );

            try {
                \App\Models\MusicianProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge($botData['profile'], [
                        'user_id'   => $user->id,
                        'is_active' => true,
                    ])
                );
            } catch (\Throwable $e) {}

            // Setiap bot buat 1-2 post
            try {
                $postBody = $this->posts[($i * 2) % count($this->posts)];
                Post::updateOrCreate(
                    ['user_id' => $user->id, 'body' => $postBody],
                    ['user_id' => $user->id, 'body' => $postBody]
                );
                if (isset($this->posts[($i * 2 + 1) % count($this->posts)])) {
                    $postBody2 = $this->posts[($i * 2 + 1) % count($this->posts)];
                    if ($postBody2 !== $postBody) {
                        Post::firstOrCreate(
                            ['user_id' => $user->id, 'body' => $postBody2],
                            ['user_id' => $user->id, 'body' => $postBody2]
                        );
                    }
                }
            } catch (\Throwable $e) {}

            // Dua bot pertama buat gig listing
            if ($i < count($this->gigs)) {
                try {
                    GigPost::updateOrCreate(
                        ['user_id' => $user->id, 'title' => $this->gigs[$i]['title']],
                        array_merge($this->gigs[$i], [
                            'user_id' => $user->id,
                            'status'  => 'open',
                        ])
                    );
                } catch (\Throwable $e) {}
            }
        }
    }
}
