@extends('layouts.app')
@section('title', 'Materi Musik Gratis — Panduan dari Teori sampai Rilis')

@push('styles')
<style>
    /* ===== LAYOUT ===== */
    /* Override main hanya untuk halaman 3-kolom ini */
    main:has(.mat-outer) { max-width: 1240px !important; }

    .mat-outer { max-width: 100%; padding: 1.5rem 0 5rem; }

    .mat-layout {
        display: grid;
        grid-template-columns: 185px minmax(0, 1fr) 210px;
        gap: 28px;
        align-items: start;
    }
    @media(max-width: 1080px) { .mat-layout { grid-template-columns: 185px minmax(0, 1fr); } .mat-sidebar-right { display: none; } }
    @media(max-width: 720px)  { .mat-layout { grid-template-columns: 1fr; } .mat-sidebar-left { display: none; } }

    .mat-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .mat-back:hover { color:var(--text); }

    /* ===== COMMUNITY SNAPSHOT ===== */
    .cs-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 1.75rem;
    }
    @media(max-width: 680px) { .cs-strip { grid-template-columns: 1fr; } }

    .cs-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.1rem 1.1rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: border-color .2s;
    }
    .cs-card:hover { border-color: var(--accent); }
    .cs-card-accent { border-color: rgba(56,168,204,.3); background: rgba(56,168,204,.04); }

    .cs-label { font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-3); }
    .cs-avatars { display: flex; gap: -4px; margin: 2px 0; }
    .cs-av {
        width: 30px; height: 30px; border-radius: 50%; object-fit: cover;
        border: 2px solid var(--bg); margin-right: -6px; flex-shrink: 0;
        background: var(--bg-3);
    }
    .cs-av-more {
        width: 30px; height: 30px; border-radius: 50%; background: var(--accent-dim);
        border: 2px solid var(--bg); display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: var(--accent); margin-right: 0;
    }
    .cs-desc { font-size: 12px; color: var(--text-3); line-height: 1.55; flex: 1; }

    .cs-gig-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; background: var(--accent-dim); color: var(--accent); margin-bottom: 2px; }
    .cs-gig-title { font-size: 13.5px; font-weight: 600; color: var(--text); line-height: 1.35; }
    .cs-gig-meta { display: flex; gap: 10px; font-size: 11px; color: var(--text-3); flex-wrap: wrap; }

    .cs-post-author { font-size: 11px; font-weight: 600; color: var(--accent); }
    .cs-post-text { font-size: 12.5px; color: var(--text-3); line-height: 1.55; font-style: italic; flex: 1; }

    .cs-cta { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; margin-top: auto; transition: .15s; }
    .cs-cta:hover { opacity: .8; }
    .cs-cta.locked { color: var(--text-3); }
    .cs-cta.locked:hover { color: var(--accent); }

    /* Mobile filter chips */
    .mat-filter-mobile { display:none;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem; }
    @media(max-width:960px) { .mat-filter-mobile { display:flex; } }
    .mat-chip { padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:none;color:var(--text-3);font-size:12px;font-weight:500;cursor:pointer;transition:.15s;font-family:inherit; }
    .mat-chip:hover { border-color:var(--accent);color:var(--accent); }
    .mat-chip.active { background:var(--accent);color:#fff;border-color:var(--accent); }

    /* ===== LEFT SIDEBAR ===== */
    .mat-sidebar-left { position: sticky; top: 70px; }
    .mat-sidebar-title { font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--text-3);margin-bottom:.75rem; }

    .mat-nav-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 12px; border-radius: 10px; margin-bottom: 3px;
        text-decoration: none; cursor: pointer; background: none; border: none;
        font-family: inherit; width: 100%; text-align: left;
        font-size: 13px; color: var(--text-3); transition: .15s;
    }
    .mat-nav-item:hover { background: var(--card-bg); color: var(--text); }
    .mat-nav-item.active { background: var(--accent-dim); color: var(--accent); font-weight: 600; }
    .mat-nav-count { font-size: 11px; background: var(--bg-3); border-radius: 20px; padding: 1px 7px; color: var(--text-3); font-weight: 500; }
    .mat-nav-item.active .mat-nav-count { background: rgba(56,168,204,.2); color: var(--accent); }

    .mat-nav-divider { height: 1px; background: var(--border); margin: .75rem 0; }

    /* ===== MAIN ===== */
    .mat-section-label { font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);font-weight:700;margin:1.5rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border); }
    .mat-section-label:first-child { margin-top: 0; }

    .mat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-bottom:1.5rem; }
    .mat-card { background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:1.1rem;transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:8px; }
    .mat-card:hover { transform:translateY(-2px);box-shadow:var(--shadow);border-color:var(--accent); }
    .mat-card-top { display:flex;align-items:center;justify-content:space-between;gap:8px; }
    .mat-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff;letter-spacing:.04em;flex-shrink:0; }
    .mat-time { font-size:10px;color:var(--text-3);display:flex;align-items:center;gap:3px; }
    .mat-card-title { font-size:13.5px;font-weight:600;color:var(--text);line-height:1.4; }
    .mat-card-excerpt { font-size:12px;color:var(--text-3);line-height:1.6;flex:1; }
    .mat-card-footer { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:4px; }
    .mat-btn-read { font-size:11px;color:var(--accent);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px; }
    .mat-btn-dl { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--accent-dim);border:1px solid rgba(56,168,204,.25);color:var(--accent);font-weight:600;text-decoration:none;transition:.15s; }
    .mat-btn-dl:hover { background:var(--accent);color:#fff; }
    .mat-btn-dl-lock { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--card-bg);border:1px solid var(--border);color:var(--text-3);display:inline-flex;align-items:center;gap:4px; }

    /* ===== RIGHT SIDEBAR ===== */
    .mat-sidebar-right { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 14px; }
    .mat-widget { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
    .mat-widget-title { font-size: 10px; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: var(--text-3); margin-bottom: .75rem; }
    .mat-tool-link { display:flex;align-items:center;gap:8px;padding:7px 0;text-decoration:none;border-bottom:1px solid var(--border);font-size:12.5px;color:var(--text-3);transition:.15s; }
    .mat-tool-link:last-child { border-bottom:none;padding-bottom:0; }
    .mat-tool-link:hover { color:var(--accent); }
    .mat-tool-link span:first-child { font-size:15px;flex-shrink:0; }
    .mat-see-all { display:block;text-align:center;font-size:11px;color:var(--accent);text-decoration:none;margin-top:.5rem;font-weight:600; }
    .mat-cta-widget { background:linear-gradient(135deg,var(--accent-dim),rgba(56,168,204,.02));border:1px solid rgba(56,168,204,.25);border-radius:16px;padding:1rem;text-align:center; }
    .mat-cta-widget h4 { font-size:13px;font-weight:700;color:var(--text);margin-bottom:.35rem; }
    .mat-cta-widget p { font-size:11.5px;color:var(--text-3);margin-bottom:.85rem;line-height:1.55; }
    .mat-cta-btn { display:inline-block;padding:8px 18px;border-radius:20px;background:var(--accent);color:#fff;font-size:12px;font-weight:600;text-decoration:none; }
</style>
@endpush

@section('content')
<div class="mat-outer">

    <a href="{{ route('library') }}" class="mat-back">← Library</a>

    {{-- ===== COMMUNITY SNAPSHOT (mengganti hero) ===== --}}
    <div class="cs-strip">

        {{-- Kartu 1: Musisi Aktif --}}
        <div class="cs-card">
            <div class="cs-label">👥 Musisi Aktif</div>
            @if($musicians->count())
            <div class="cs-avatars">
                @foreach($musicians->take(4) as $m)
                <img src="{{ $m['avatar'] }}" class="cs-av" alt="{{ $m['name'] }}" title="{{ $m['name'] }}">
                @endforeach
                @if($musicians->count() > 4)
                <div class="cs-av cs-av-more">+{{ $musicians->count() - 4 }}</div>
                @endif
            </div>
            @else
            <div style="font-size:22px;margin:4px 0;">👤 👤 👤</div>
            @endif
            <p class="cs-desc">Temukan personil, kolaborator, dan partner rekaman di kotamu.</p>
            @auth
            <a href="{{ route('musisi.index') }}" class="cs-cta">Cari personil →</a>
            @else
            <a href="{{ route('google.login') }}" class="cs-cta locked">🔒 Cari personil →</a>
            @endauth
        </div>

        {{-- Kartu 2: Gig Terbaru --}}
        <div class="cs-card cs-card-accent">
            <div class="cs-label">🎪 Gig Terbaru</div>
            @if($latestGig)
            <div class="cs-gig-badge">{{ \App\Models\GigPost::typeLabel($latestGig->type) }}</div>
            <div class="cs-gig-title">{{ $latestGig->title }}</div>
            <div class="cs-gig-meta">
                @if($latestGig->location)<span>📍 {{ $latestGig->location }}</span>@endif
                @if($latestGig->date_event)<span>🗓 {{ $latestGig->date_event->format('d M Y') }}</span>@endif
            </div>
            @else
            <div class="cs-gig-title" style="color:var(--text-3);">Belum ada gig terbuka — jadilah yang pertama!</div>
            @endif
            @auth
            <a href="{{ route('gig.board') }}" class="cs-cta">Lihat semua gig →</a>
            @else
            <a href="{{ route('google.login') }}" class="cs-cta locked">🔒 Lamar sekarang →</a>
            @endauth
        </div>

        {{-- Kartu 3: Diskusi Terbaru --}}
        <div class="cs-card">
            <div class="cs-label">💬 Diskusi Komunitas</div>
            @if($latestPost)
            <div class="cs-post-author">{{ $latestPost->user?->name ?? 'Musisi' }}</div>
            <div class="cs-post-text">"{{ \Illuminate\Support\Str::limit(strip_tags($latestPost->body ?? ''), 90) }}"</div>
            @else
            <div class="cs-post-text">Jadilah yang pertama memulai diskusi tentang musik...</div>
            @endif
            @auth
            <a href="{{ route('community.index') }}" class="cs-cta">Ikut diskusi →</a>
            @else
            <a href="{{ route('google.login') }}" class="cs-cta locked">🔒 Gabung komunitas →</a>
            @endauth
        </div>

    </div>{{-- .cs-strip --}}

    {{-- Mobile filter chips --}}
    <div class="mat-filter-mobile">
        <button class="mat-chip active" onclick="matFilter('all',this)" data-cat="all">Semua</button>
        <button class="mat-chip" onclick="matFilter('teori',this)" data-cat="teori">🎵 Teori</button>
        <button class="mat-chip" onclick="matFilter('produksi',this)" data-cat="produksi">🎛️ Produksi</button>
        <button class="mat-chip" onclick="matFilter('kolaborasi',this)" data-cat="kolaborasi">🤝 Kolaborasi</button>
        <button class="mat-chip" onclick="matFilter('rilis',this)" data-cat="rilis">🚀 Rilis</button>
        <button class="mat-chip" onclick="matFilter('karir',this)" data-cat="karir">💼 Karir</button>
    </div>

    @php
    $catLabels = ['teori'=>'Teori Musik','produksi'=>'Produksi & Recording','kolaborasi'=>'Kolaborasi','rilis'=>'Rilis & Branding','karir'=>'Karir & Bisnis Musik'];
    $catColors = ['teori'=>'#38A8CC','produksi'=>'#a855f7','kolaborasi'=>'#f59e0b','rilis'=>'#22c55e','karir'=>'#f97316'];
    $catIcons  = ['teori'=>'🎵','produksi'=>'🎛️','kolaborasi'=>'🤝','rilis'=>'🚀','karir'=>'💼'];
    @endphp

    <div class="mat-layout">

        {{-- LEFT SIDEBAR --}}
        <aside class="mat-sidebar-left">
            <div class="mat-sidebar-title">Kategori</div>
            <button class="mat-nav-item active" onclick="matFilter('all',this)" data-cat="all">
                <span>📖 Semua</span>
                <span class="mat-nav-count">{{ $articles->count() }}</span>
            </button>
            @foreach($catLabels as $cat => $label)
            @if($grouped->has($cat))
            <button class="mat-nav-item" onclick="matFilter('{{ $cat }}',this)" data-cat="{{ $cat }}">
                <span>{{ $catIcons[$cat] }} {{ $label }}</span>
                <span class="mat-nav-count">{{ $grouped[$cat]->count() }}</span>
            </button>
            @endif
            @endforeach

            <div class="mat-nav-divider"></div>
            <div class="mat-sidebar-title">Jelajah</div>
            <a href="{{ route('library') }}" class="mat-nav-item" style="text-decoration:none;">🎵 Diskografi</a>
            <a href="{{ route('tools.index') }}" class="mat-nav-item" style="text-decoration:none;">🎛️ Alat Musisi</a>
            <a href="{{ route('gig.board') }}" class="mat-nav-item" style="text-decoration:none;">🎪 Papan Gig</a>
        </aside>

        {{-- MAIN CONTENT --}}
        <main>
            @foreach($catLabels as $cat => $label)
            @if($grouped->has($cat))
            <div class="mat-section" data-cat="{{ $cat }}">
                <div class="mat-section-label">{{ $catIcons[$cat] }} {{ $label }}</div>
                <div class="mat-grid">
                    @foreach($grouped[$cat] as $a)
                    <a href="{{ route('library.materi.show', $a->slug) }}" class="mat-card">
                        <div class="mat-card-top">
                            <span class="mat-cat-pill" style="background:{{ $catColors[$cat] }}">{{ $label }}</span>
                            <span class="mat-time">🕐 {{ $a->reading_time }} mnt</span>
                        </div>
                        <div class="mat-card-title">{{ $a->title }}</div>
                        <div class="mat-card-excerpt">{{ $a->excerpt }}</div>
                        <div class="mat-card-footer" onclick="event.stopPropagation()">
                            <span class="mat-btn-read">Baca artikel →</span>
                            @auth
                            <a href="{{ route('library.materi.download', $a->slug) }}" class="mat-btn-dl" onclick="event.stopPropagation()">⬇ Unduh</a>
                            @else
                            <span class="mat-btn-dl-lock">🔒 Unduh</span>
                            @endauth
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach

            @guest
            <div style="background:linear-gradient(135deg,var(--accent-dim),rgba(56,168,204,.02));border:1px solid rgba(56,168,204,.2);border-radius:20px;padding:1.5rem;text-align:center;margin-top:2rem;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.4rem;">🔒 Download Semua Artikel</h3>
                <p style="font-size:13px;color:var(--text-3);margin-bottom:1rem;">Login untuk download semua artikel dalam format Markdown.</p>
                <a href="{{ route('google.login') }}" style="display:inline-block;padding:10px 24px;border-radius:30px;background:var(--accent);color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Login dengan Google</a>
            </div>
            @endguest
        </main>

        {{-- RIGHT SIDEBAR --}}
        <aside class="mat-sidebar-right">
            <div class="mat-widget">
                <div class="mat-widget-title">🎛 Alat untuk Musisi</div>
                <a href="{{ route('tools.chord-builder') }}" class="mat-tool-link"><span>🎸</span><span>Chord Builder</span></a>
                <a href="{{ route('tools.transpose-kunci') }}" class="mat-tool-link"><span>🔀</span><span>Transpose Kunci</span></a>
                <a href="{{ route('tools.bpm-kalkulator') }}" class="mat-tool-link"><span>🥁</span><span>BPM Calculator</span></a>
                <a href="{{ route('tools.kalkulator-royalti') }}" class="mat-tool-link"><span>💰</span><span>Kalkulator Royalti</span></a>
                <a href="{{ route('tools.epk') }}" class="mat-tool-link"><span>📄</span><span>EPK Generator</span></a>
                <a href="{{ route('tools.index') }}" class="mat-see-all">Lihat semua alat →</a>
            </div>

            @guest
            <div class="mat-cta-widget">
                <h4>🎵 Gabung Komunitas</h4>
                <p>Download semua artikel, ikut diskusi musisi, dan temukan kolaborator.</p>
                <a href="{{ route('google.login') }}" class="mat-cta-btn">Masuk Gratis</a>
            </div>
            @else
            <div class="mat-cta-widget">
                <h4>🎪 Papan Gig</h4>
                <p>Cari audisi band, open mic, dan peluang manggung di kotamu.</p>
                <a href="{{ route('gig.board') }}" class="mat-cta-btn">Lihat Gig →</a>
            </div>
            @endguest
        </aside>

    </div>{{-- .mat-layout --}}
</div>
@endsection

@push('scripts')
<script>
function matFilter(cat, btn) {
    document.querySelectorAll('.mat-nav-item[data-cat], .mat-chip[data-cat]').forEach(function(b){
        b.classList.toggle('active', b.dataset.cat === cat);
    });
    document.querySelectorAll('.mat-section').forEach(function(s){
        s.style.display = (cat === 'all' || s.dataset.cat === cat) ? '' : 'none';
    });
}
</script>
@endpush
