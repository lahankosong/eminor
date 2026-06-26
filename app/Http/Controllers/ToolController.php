<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ToolController extends Controller
{
    /** Bangun $seo lengkap: title/desc/url + OG image per-tool + schema @graph (node utama + BreadcrumbList). */
    private function toolSeo(string $title, string $desc, string $slug, string $bcName, array $mainNode): array
    {
        $url = $slug === '' ? url('/tools') : url('/tools/' . $slug);
        $og  = $slug === '' ? 'studio' : $slug;

        $crumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda',     'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Alat Gratis', 'item' => route('tools.index')],
        ];
        if ($bcName !== '') {
            $crumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $bcName, 'item' => $url];
        }

        return [
            'title'       => $title,
            'description' => $desc,
            'url'         => $url,
            'image'       => asset('images/og/' . $og . '.png'),
            'schema'      => ['@context' => 'https://schema.org', '@graph' => [
                $mainNode,
                ['@type' => 'BreadcrumbList', 'itemListElement' => $crumbs],
            ]],
        ];
    }

    private function appNode(string $name, string $url, string $desc, string $category): array
    {
        return [
            '@type' => 'WebApplication', 'name' => $name, 'url' => $url, 'description' => $desc,
            'applicationCategory' => $category, 'operatingSystem' => 'Any',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
        ];
    }

    public function audioCutter()
    {
        $url = url('/tools/potong-lagu');
        $seo = $this->toolSeo(
            'Pemotong Lagu Online Gratis — Potong MP3, WAV, OGG di Browser',
            'Potong bagian lagu favoritmu secara online, gratis, tanpa upload ke server. Mendukung MP3, WAV, OGG, FLAC. Hasil langsung diunduh ke perangkatmu.',
            'potong-lagu', 'Pemotong Lagu',
            $this->appNode('Pemotong Lagu Online', $url, 'Potong bagian lagu favoritmu, gratis di browser tanpa upload.', 'MultimediaApplication')
        );
        $origin = 'Dulu saya mau potong bagian intro lagu untuk dikirim ke produser, tapi semua tool online minta upload file dulu — privacy risk. Jadi saya buat sendiri: potong langsung di browser, file tidak pernah keluar dari perangkatmu.';
        return view('tools.audio-cutter', compact('seo', 'origin'));
    }

    public function vocalRemover()
    {
        $url = url('/tools/hapus-vokal');
        $seo = $this->toolSeo(
            'Penghapus Vokal Online Gratis — Bikin Karaoke / Minus One di Browser',
            'Hapus vokal dari lagu untuk bikin karaoke / minus one, langsung di browser tanpa upload. Pisahkan instrumen & vokal, unduh MP3/WAV. Gratis, tanpa install.',
            'hapus-vokal', 'Hapus Vokal (Karaoke)',
            $this->appNode('Penghapus Vokal Online (Karaoke Maker)', $url, 'Hapus vokal lagu untuk karaoke/minus one, gratis di browser tanpa upload.', 'MultimediaApplication')
        );
        $origin = 'Saya butuh minus one lagu sendiri untuk latihan live, tapi tidak mau bayar $15/bulan hanya untuk satu fitur. Hasilnya: penghapus vokal berbasis AI langsung di browser, gratis, tanpa upload — dan ternyata banyak musisi lain yang butuh hal yang sama.';
        return view('tools.vocal-remover', compact('seo', 'origin'));
    }

    public function coverMaker()
    {
        $url = url('/tools/cover-art');
        $seo = $this->toolSeo(
            'Buat Cover Lagu / Album Online Gratis — Cover Art Maker 1:1 (3000px)',
            'Bikin cover art lagu/album persegi 1:1 untuk Spotify, Apple Music, YouTube — resolusi 1600/2000/3000 px. Tambah judul & nama artis, atur foto, unduh PNG/JPG. Gratis, tanpa upload.',
            'cover-art', 'Buat Cover',
            $this->appNode('Cover Art Maker (Buat Cover Lagu)', $url, 'Buat cover art lagu/album 1:1 (3000px) untuk platform streaming, gratis tanpa upload.', 'DesignApplication')
        );
        $origin = 'Lagu pertama yang saya upload ke Spotify ditolak karena cover art kurang dari 3000×3000px. Canva butuh langganan untuk export resolusi tinggi. Jadi saya buat tool ini: cover art 3000px, gratis, langsung di browser.';
        return view('tools.cover-maker', compact('seo', 'origin'));
    }

    public function releaseCard()
    {
        $url = url('/tools/kartu-rilis');
        $seo = $this->toolSeo(
            'Kartu Promo Rilis Lagu Online Gratis — Pra-Rilis, Rilis & Countdown',
            'Buat kartu promo rilis lagu untuk Instagram/WhatsApp: pra-rilis (countdown hari rilis), rilis (out now + link/QR platform), pasca-rilis. Feed 1:1 & Story 9:16. Gratis, tanpa upload.',
            'kartu-rilis', 'Kartu Promo Rilis',
            $this->appNode('Kartu Promo Rilis Lagu', $url, 'Buat kartu promo rilis & countdown lagu untuk media sosial, gratis tanpa upload.', 'DesignApplication')
        );
        $origin = 'Waktu rilis single pertama saya, desainer grafis minta Rp 300rb hanya untuk bikin kartu "Out Now" di Instagram. Saya pikir: ini bisa dibuat sendiri. Tool ini lahir dari momen itu — sekarang kamu bisa buat kartu promo profesional dalam 2 menit, gratis.';
        return view('tools.release-card', compact('seo', 'origin'));
    }

    public function countdown(Request $request)
    {
        $url = url('/tools/countdown');
        $j = Str::limit(trim((string) $request->query('j', '')), 60, '');
        $a = Str::limit(trim((string) $request->query('a', '')), 40, '');
        $d = trim((string) $request->query('d', ''));
        $hasParams = $d !== '';

        if ($hasParams) {
            // Mode display (link dibagikan): OG dinamis untuk preview cantik di WA, noindex (cegah duplikat tak terhingga)
            $title = ($j !== '' ? $j : 'Rilis Baru') . ($a !== '' ? ' — ' . $a : '') . ' · Hitung Mundur Rilis';
            $desc  = 'Hitung mundur rilis ' . ($j !== '' ? '“' . $j . '”' : 'lagu') . ($a !== '' ? ' oleh ' . $a : '')
                   . '. Buka untuk lihat countdown langsung & dengarkan saat rilis.';
            $seo = [
                'title' => $title, 'description' => $desc, 'url' => $url, 'type' => 'website',
                'image' => asset('images/og/countdown.png'), 'robots' => 'noindex, follow',
            ];
        } else {
            $seo = $this->toolSeo(
                'Buat Countdown Rilis Lagu — Link Hitung Mundur untuk Bio & Story',
                'Bikin link hitung mundur rilis lagu yang berdetak real-time untuk bio Instagram / link-in-bio / WhatsApp. Saat rilis otomatis jadi "Out Now". Gratis, tanpa daftar.',
                'countdown', 'Countdown Rilis',
                $this->appNode('Countdown Rilis Lagu (Generator Link Hitung Mundur)', $url, 'Buat link hitung mundur rilis lagu real-time untuk media sosial, gratis tanpa daftar.', 'UtilitiesApplication')
            );
        }

        $origin = 'Saya buat countdown untuk rilis single Bersamamu — link satu ini saya tempel di bio Instagram dan stories, jadi fans bisa hitung mundur bareng. Sekarang kamu bisa buat link serupa untuk rilismu sendiri, gratis, dalam hitungan detik.';
        return view('tools.countdown', compact('seo', 'hasParams', 'origin'));
    }

    public function editMetadata()
    {
        $url = url('/tools/edit-metadata');
        $seo = $this->toolSeo(
            'Edit Metadata Lagu (Tag MP3) & Konversi ke WAV — Gratis untuk Agregator',
            'Edit metadata MP3 (judul, artis, album, tahun, genre, track) + tanam cover art, atau konversi lagu ke WAV lossless untuk kirim ke agregator (DistroKid, dll). Gratis, di browser, tanpa upload.',
            'edit-metadata', 'Edit Metadata & WAV',
            $this->appNode('Edit Metadata MP3 & Konversi WAV', $url, 'Edit tag ID3 MP3 + tanam cover, atau konversi ke WAV lossless untuk agregator, gratis tanpa upload.', 'MultimediaApplication')
        );
        $origin = 'DistroKid menolak file saya karena metadata MP3-nya kosong. Saya tidak mau download software besar hanya untuk mengisi tag judul dan artis. Tool ini lahir dari frustrasi itu: edit metadata dan konversi ke WAV, langsung di browser, gratis.';
        return view('tools.edit-metadata', compact('seo', 'origin'));
    }

    public function chordBuilder()
    {
        $url = url('/tools/chord-builder');
        $seo = $this->toolSeo(
            'Chord Builder & Progression Generator — Buat Progresi Chord Gratis',
            'Pilih kunci dan suasana, dapatkan 5 progresi chord siap pakai untuk lagu kamu. Cocok untuk musisi yang mau eksplor harmoni baru. Gratis, tanpa daftar.',
            'chord-builder', 'Chord Builder',
            $this->appNode('Chord Progression Generator', $url, 'Generate 5 progresi chord berdasarkan key dan mood, gratis tanpa daftar.', 'EducationalApplication')
        );
        $origin = 'Saya sering "stuck" saat nulis lagu — pakai I-IV-V terus terasa membosankan. Saya buat tool ini untuk eksplorasi progresi chord di luar zona nyaman. Sekarang saya minta progresi "key C, suasana melankolis" dan langsung dapat 5 pilihan siap dicoba.';
        return view('tools.chord-builder', compact('seo', 'origin'));
    }

    public function bpmCalculator()
    {
        $url = url('/tools/bpm-kalkulator');
        $seo = $this->toolSeo(
            'Kalkulator BPM & Tap Tempo Online Gratis — Cari BPM Lagu',
            'Ketuk tombol mengikuti ritme untuk tahu BPM lagu. Dilengkapi metronome visual dan saran genre berdasarkan BPM. Gratis, tanpa install.',
            'bpm-kalkulator', 'BPM Calculator',
            $this->appNode('Kalkulator BPM & Tap Tempo', $url, 'Hitung BPM lagu dengan tap tempo, metronome visual, dan saran genre gratis.', 'MultimediaApplication')
        );
        $origin = 'Saat kolaborasi dengan drummer, kami sering debat soal tempo. "Ini 90 atau 95 BPM?" — 15 menit diskusi, tidak produktif. Saya buat tap tempo ini agar semua orang bisa langsung ketuk dan tahu angka pastinya dalam 5 detik.';
        return view('tools.bpm-kalkulator', compact('seo', 'origin'));
    }

    public function royaltyCalculator()
    {
        $url = url('/tools/kalkulator-royalti');
        $seo = $this->toolSeo(
            'Kalkulator Royalti Streaming Musik Gratis — Estimasi Pendapatan Spotify dll',
            'Estimasi pendapatan bulanan dari Spotify, Apple Music, YouTube Music, dan TikTok. Hitung royalti bersih setelah potongan distributor. Gratis.',
            'kalkulator-royalti', 'Kalkulator Royalti',
            $this->appNode('Kalkulator Royalti Streaming Musik', $url, 'Estimasi pendapatan streaming dari berbagai platform musik, gratis tanpa daftar.', 'FinanceApplication')
        );
        $origin = 'Saya penasaran: kalau lagu saya tembus 100K streams di Spotify, dapat berapa? Angka per-stream platform streaming memang kecil, tapi dengan tool ini kamu bisa hitung sendiri berapa target streams yang realistis untuk penghasilan tertentu.';
        return view('tools.kalkulator-royalti', compact('seo', 'origin'));
    }

    public function rateCard()
    {
        $url = url('/tools/rate-card');
        $seo = $this->toolSeo(
            'Rate Card Generator Musisi — Buat Daftar Harga Jasa Musik Gratis',
            'Buat rate card profesional untuk jasa musisi: wedding gig, studio session, teaching, mixing. Download sebagai gambar siap share ke klien. Gratis, tanpa daftar.',
            'rate-card', 'Rate Card Generator',
            $this->appNode('Rate Card Generator Musisi', $url, 'Buat daftar harga jasa musik profesional siap share, gratis tanpa daftar.', 'UtilitiesApplication')
        );
        $origin = 'Saya pernah ditanya EO "berapa harga main di wedding?" dan saya bingung jawabnya. Tidak ada patokan, tidak ada dokumen resmi. Dari situ saya buat tool ini — supaya musisi punya rate card profesional yang bisa langsung dikirim ke klien, tanpa malu dan tanpa under-pricing diri sendiri.';
        return view('tools.rate-card', compact('seo', 'origin'));
    }

    public function transposeKey()
    {
        $url = url('/tools/transpose-kunci');
        $seo = $this->toolSeo(
            'Transpose Kunci Gitar Online Gratis — Pindah Kunci Chord Seketika',
            'Pindah kunci chord gitar secara otomatis — tempel progresi chord, pilih kunci asal dan tujuan, langsung transposes. Gratis, di browser, tanpa daftar.',
            'transpose-kunci', 'Transpose Kunci',
            $this->appNode('Transpose Kunci Gitar Online', $url, 'Pindah kunci chord gitar otomatis, gratis di browser tanpa daftar.', 'EducationalApplication')
        );
        $origin = 'Saya sering dapat request lagu tapi kunci aslinya terlalu tinggi atau rendah untuk penyanyi. Daripada transpose manual satu per satu, saya buat tool ini — paste chord, pilih kunci tujuan, selesai dalam 3 detik.';
        return view('tools.transpose-kunci', compact('seo', 'origin'));
    }

    public function epkGenerator()
    {
        $url = url('/tools/epk');
        $seo = $this->toolSeo(
            'EPK Generator Musisi — Buat Electronic Press Kit Gratis',
            'Buat Electronic Press Kit (EPK) musisi yang profesional: bio, foto, link streaming, kontak. Download PDF atau share link langsung ke booker dan media. Gratis.',
            'epk', 'EPK Generator',
            $this->appNode('EPK Generator Musisi', $url, 'Buat press kit musisi profesional (bio, foto, streaming links, kontak) siap kirim ke booker & media, gratis.', 'UtilitiesApplication')
        );
        $origin = 'Pertama kali booker minta "kirim EPK kamu" — saya bingung apa itu. Setelah tahu, saya bingung lagi bagaimana bikinnya. Tool ini lahir dari momen itu: EPK musisi yang serius, selesai dalam 10 menit, tanpa bayar desainer.';
        return view('tools.epk-generator', compact('seo', 'origin'));
    }

    public function setlistBuilder()
    {
        $url = url('/tools/setlist');
        $seo = $this->toolSeo(
            'Setlist Builder Musisi — Susun & Print Setlist Manggung Gratis',
            'Buat setlist manggung: urutkan lagu, catat BPM & kunci, beri catatan per lagu, cetak atau simpan PDF. Gratis, tanpa daftar, langsung di browser.',
            'setlist', 'Setlist Builder',
            $this->appNode('Setlist Builder Musisi', $url, 'Susun setlist manggung: urutan lagu, BPM, kunci, catatan. Print / PDF langsung di browser, gratis.', 'UtilitiesApplication')
        );
        $origin = 'Sebelum gig pertama saya di event kampus, saya nulis setlist di kertas HVS — terus ketumpahan air minum di backstage. Dari situ saya tahu: setlist harus digital, bisa print, bisa diakses dari HP kapanpun.';
        return view('tools.setlist-builder', compact('seo', 'origin'));
    }

    public function releasePlanner()
    {
        $url = url('/tools/release-planner');
        $seo = $this->toolSeo(
            'Music Release Planner — Jadwal Promo Rilis Lagu Otomatis T-42 sampai T+28',
            'Masukkan tanggal rilis, jadwal promo 9 fase dari T-42 hari sampai T+28 hari langsung siap. Checklist interaktif tersimpan otomatis. Gratis, client-side, tanpa daftar.',
            'release-planner', 'Release Planner',
            $this->appNode('Music Release Planner', $url, 'Jadwal promo rilis lagu otomatis dari T-42 hari sampai T+28 hari — checklist tersimpan lokal, gratis.', 'UtilitiesApplication')
        );
        $origin = 'Rilis lagu pertama saya berantakan — tidak ada pre-save, tidak pitch ke playlist, tidak ada konten BTS. Semua reaktif, bukan proaktif. Tool ini adalah yang saya butuhkan waktu itu: jadwal promo terstruktur supaya rilis berikutnya tidak terasa seperti memadamkan kebakaran.';
        return view('tools.release-planner', compact('seo', 'origin'));
    }

    public function hub()
    {
        $tools = [
            ['icon' => '✂️', 'name' => 'Pemotong Lagu Online',       'desc' => 'Potong bagian lagu (MP3/WAV/OGG) langsung di browser.',         'route' => 'tools.potong-lagu'],
            ['icon' => '🎤', 'name' => 'Penghapus Vokal (Karaoke)',   'desc' => 'Pisah instrumen & vokal untuk karaoke / minus one.',            'route' => 'tools.hapus-vokal'],
            ['icon' => '🎨', 'name' => 'Buat Cover Lagu / Album',      'desc' => 'Cover art 1:1 hingga 3000px untuk Spotify, Apple, YouTube.',     'route' => 'tools.cover-art'],
            ['icon' => '🚀', 'name' => 'Kartu Promo Rilis',           'desc' => '3 fase (pra/rilis/pasca) + QR/platform, feed 1:1 & story 9:16.', 'route' => 'tools.kartu-rilis'],
            ['icon' => '⏳', 'name' => 'Countdown Rilis',             'desc' => 'Link hitung mundur real-time untuk bio Instagram / story.',      'route' => 'tools.countdown'],
            ['icon' => '🏷️', 'name' => 'Edit Metadata & WAV',        'desc' => 'Tag MP3 (judul/artis/cover) atau konversi WAV untuk agregator.', 'route' => 'tools.edit-metadata'],
            ['icon' => '🎸', 'name' => 'Chord Progression Generator', 'desc' => 'Pilih key & mood, generate 5 progresi chord siap pakai.',        'route' => 'tools.chord-builder'],
            ['icon' => '🥁', 'name' => 'Kalkulator BPM & Tap Tempo', 'desc' => 'Ketuk ikuti ritme untuk tahu BPM + metronome visual.',           'route' => 'tools.bpm-kalkulator'],
            ['icon' => '💰', 'name' => 'Kalkulator Royalti Streaming','desc' => 'Estimasi pendapatan Spotify, Apple Music, YouTube, TikTok.',     'route' => 'tools.kalkulator-royalti'],
            ['icon' => '💼', 'name' => 'Rate Card Generator',         'desc' => 'Buat daftar harga jasa musik profesional siap share ke klien.',  'route' => 'tools.rate-card'],
            ['icon' => '🔀', 'name' => 'Transpose Kunci Gitar',       'desc' => 'Pindah kunci chord otomatis — paste, pilih kunci, selesai.',        'route' => 'tools.transpose-kunci'],
            ['icon' => '📄', 'name' => 'EPK Generator',               'desc' => 'Buat press kit musisi profesional siap kirim ke booker & media.',    'route' => 'tools.epk'],
            ['icon' => '🎵', 'name' => 'Setlist Builder',             'desc' => 'Susun setlist manggung: urutan lagu, BPM, kunci, catatan — print/PDF.', 'route' => 'tools.setlist'],
            ['icon' => '📅', 'name' => 'Music Release Planner',       'desc' => 'Jadwal promo rilis 9 fase dari T-42 sampai T+28 — checklist otomatis.',   'route' => 'tools.release-planner'],
        ];
        $items = [];
        foreach ($tools as $i => $t) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $t['name'], 'url' => route($t['route'])];
        }
        $seo = $this->toolSeo(
            'Alat Gratis untuk Musisi — Potong Lagu, Karaoke, Cover & Promo Rilis',
            'Kumpulan alat gratis untuk musisi: pemotong lagu, penghapus vokal (karaoke), pembuat cover art 1:1, kartu promo rilis & countdown rilis. Semua di browser, tanpa upload, tanpa daftar.',
            '', '',
            ['@type' => 'ItemList', 'name' => 'Alat Gratis Musisi — Margonoandi', 'itemListElement' => $items]
        );
        $featuredArticles = collect();
        try {
            $featuredArticles = \App\Models\Article::orderBy('batch')->orderBy('id')->take(4)->get(['slug','title','category','reading_time']);
        } catch (\Throwable $e) {}

        $latestGig = null;
        try {
            $latestGig = \App\Models\GigPost::with('user')->where('status','open')->latest()->first();
        } catch (\Throwable $e) {}

        return view('tools.index', compact('seo', 'tools', 'featuredArticles', 'latestGig'));
    }
}
