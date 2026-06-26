@extends('layouts.app')
@section('title', 'Materi Musik Gratis — Panduan dari Teori sampai Rilis')

@push('styles')
<style>
    /* ===== LAYOUT ===== */
    .mat-outer { max-width: 1160px; margin: 0 auto; padding: 1.5rem 1rem 5rem; }

    .mat-layout {
        display: grid;
        grid-template-columns: 200px 1fr 220px;
        gap: 24px;
        align-items: start;
    }
    @media(max-width: 960px) { .mat-layout { grid-template-columns: 1fr 220px; } .mat-sidebar-left { display: none; } }
    @media(max-width: 680px) { .mat-layout { grid-template-columns: 1fr; } .mat-sidebar-right { display: none; } }

    /* ===== HERO (full-width above grid) ===== */
    .mat-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .mat-back:hover { color:var(--text); }

    .mat-hero { text-align:center; margin-bottom:1.75rem; }
    .mat-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#38A8CC;background:rgba(56,168,204,.12);border:1px solid rgba(56,168,204,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .mat-hero h1 { font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text);line-height:1.2;margin-bottom:.5rem; }
    .mat-hero p { font-size:13.5px;color:var(--text-3);max-width:520px;margin:0 auto;line-height:1.7; }
    .mat-stats { display:flex;gap:1.5rem;justify-content:center;margin-top:1rem;flex-wrap:wrap;font-size:12px;color:var(--text-3); }
    .mat-stats b { color:var(--text); }

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
    .mat-nav-item.active { background: rgba(56,168,204,.12); color: #38A8CC; font-weight: 600; }
    .mat-nav-count { font-size: 11px; background: var(--bg-3); border-radius: 20px; padding: 1px 7px; color: var(--text-3); font-weight: 500; }
    .mat-nav-item.active .mat-nav-count { background: rgba(56,168,204,.2); color: #38A8CC; }

    /* ===== MAIN CONTENT ===== */
    .mat-section-label { font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);font-weight:700;margin:1.5rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border); }

    .mat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-bottom:1.5rem; }
    .mat-card { background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:1.1rem;transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:8px; }
    .mat-card:hover { transform:translateY(-2px);box-shadow:var(--shadow);border-color:#38A8CC; }
    .mat-card-top { display:flex;align-items:center;justify-content:space-between;gap:8px; }
    .mat-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff;letter-spacing:.04em;flex-shrink:0; }
    .mat-time { font-size:10px;color:var(--text-3);display:flex;align-items:center;gap:3px; }
    .mat-card-title { font-size:13.5px;font-weight:600;color:var(--text);line-height:1.4; }
    .mat-card-excerpt { font-size:12px;color:var(--text-3);line-height:1.6;flex:1; }
    .mat-card-footer { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:4px; }
    .mat-btn-read { font-size:11px;color:#38A8CC;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px; }
    .mat-btn-dl { font-size:11px;padding:4px 12px;border-radius:12px;background:rgba(56,168,204,.1);border:1px solid rgba(56,168,204,.25);color:#38A8CC;font-weight:600;text-decoration:none;transition:.15s; }
    .mat-btn-dl:hover { background:#38A8CC;color:#fff; }
    .mat-btn-dl-lock { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--card-bg);border:1px solid var(--border);color:var(--text-3);cursor:not-allowed;display:inline-flex;align-items:center;gap:4px; }

    /* Mobile filter chips (hanya tampil di mobile) */
    .mat-filter-mobile { display:none;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem; }
    @media(max-width:960px) { .mat-filter-mobile { display:flex; } }
    .mat-chip { padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:none;color:var(--text-3);font-size:12px;font-weight:500;cursor:pointer;transition:.15s;font-family:inherit; }
    .mat-chip:hover { border-color:#38A8CC;color:#38A8CC; }
    .mat-chip.active { background:#38A8CC;color:#fff;border-color:#38A8CC; }

    /* ===== RIGHT SIDEBAR ===== */
    .mat-sidebar-right { position: sticky; top: 70px; display: flex; flex-direction: column; gap: 14px; }

    .mat-widget { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
    .mat-widget-title { font-size: 10px; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: var(--text-3); margin-bottom: .75rem; }

    .mat-tool-link { display: flex; align-items: center; gap: 8px; padding: 7px 0; text-decoration: none; border-bottom: 1px solid var(--border); font-size: 12.5px; color: var(--text-3); transition: .15s; }
    .mat-tool-link:last-child { border-bottom: none; padding-bottom: 0; }
    .mat-tool-link:hover { color: #38A8CC; }
    .mat-tool-link span:first-child { font-size: 16px; flex-shrink: 0; }

    .mat-cta-widget { background: linear-gradient(135deg, rgba(56,168,204,.12), rgba(56,168,204,.05)); border: 1px solid rgba(56,168,204,.25); border-radius: 16px; padding: 1rem; text-align: center; }
    .mat-cta-widget h4 { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: .35rem; }
    .mat-cta-widget p { font-size: 11.5px; color: var(--text-3); margin-bottom: .85rem; line-height: 1.55; }
    .mat-cta-btn { display: inline-block; padding: 8px 18px; border-radius: 20px; background: #38A8CC; color: #fff; font-size: 12px; font-weight: 600; text-decoration: none; }

    .mat-progress-item { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text-3); margin-bottom: 6px; }
    .mat-progress-bar-wrap { height: 4px; background: var(--bg-3); border-radius: 4px; margin-bottom: 10px; }
    .mat-progress-bar { height: 4px; border-radius: 4px; background: linear-gradient(90deg, #38A8CC, #2186A8); }
</style>
@endpush

@section('content')
<div class="mat-outer">

    <a href="{{ route('library') }}" class="mat-back">← Library</a>

    {{-- HERO --}}
    <div class="mat-hero">
        <div class="mat-badge">📚 Materi Gratis</div>
        <h1>Panduan Musik dari Teori sampai Rilis</h1>
        <p>Artikel lengkap dalam Bahasa Indonesia — dari belajar chord pertama sampai strategi rilis, monetisasi, dan manifesto songwriter independen.</p>
        <div class="mat-stats">
            <span><b>{{ $articles->count() }}</b> artikel</span>
            <span><b>{{ $grouped->count() }}</b> kategori</span>
            <span><b>~{{ $articles->sum('reading_time') }}</b> menit baca</span>
            <span>100% gratis</span>
        </div>
    </div>

    {{-- Mobile filter chips --}}
    <div class="mat-filter-mobile">
        <button class="mat-chip active" onclick="matFilter('all', this)">Semua</button>
        <button class="mat-chip" onclick="matFilter('teori', this)">🎵 Teori</button>
        <button class="mat-chip" onclick="matFilter('produksi', this)">🎛️ Produksi</button>
        <button class="mat-chip" onclick="matFilter('kolaborasi', this)">🤝 Kolaborasi</button>
        <button class="mat-chip" onclick="matFilter('rilis', this)">🚀 Rilis</button>
        <button class="mat-chip" onclick="matFilter('karir', this)">💼 Karir</button>
    </div>

    @php
    $catLabels = ['teori' => 'Teori Musik', 'produksi' => 'Produksi & Recording', 'kolaborasi' => 'Kolaborasi', 'rilis' => 'Rilis & Branding', 'karir' => 'Karir & Bisnis Musik'];
    $catColors = ['teori' => '#38A8CC', 'produksi' => '#a855f7', 'kolaborasi' => '#f59e0b', 'rilis' => '#22c55e', 'karir' => '#f97316'];
    $catIcons  = ['teori' => '🎵', 'produksi' => '🎛️', 'kolaborasi' => '🤝', 'rilis' => '🚀', 'karir' => '💼'];
    @endphp

    <div class="mat-layout">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="mat-sidebar-left">
            <div class="mat-sidebar-title">Kategori</div>

            <button class="mat-nav-item active" onclick="matFilter('all', this)" data-cat="all">
                <span>📖 Semua Artikel</span>
                <span class="mat-nav-count">{{ $articles->count() }}</span>
            </button>
            @foreach($catLabels as $cat => $label)
            @if($grouped->has($cat))
            <button class="mat-nav-item" onclick="matFilter('{{ $cat }}', this)" data-cat="{{ $cat }}">
                <span>{{ $catIcons[$cat] }} {{ $label }}</span>
                <span class="mat-nav-count">{{ $grouped[$cat]->count() }}</span>
            </button>
            @endif
            @endforeach
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <main>
            @foreach($catLabels as $cat => $label)
            @if($grouped->has($cat))
            <div class="mat-section" data-cat="{{ $cat }}">
                <div class="mat-section-label">{{ $catIcons[$cat] }} {{ $label }}</div>
                <div class="mat-grid">
                    @foreach($grouped[$cat] as $a)
                    <a href="{{ route('library.materi.show', $a->slug) }}" class="mat-card" data-cat="{{ $a->category }}">
                        <div class="mat-card-top">
                            <span class="mat-cat-pill" style="background:{{ $catColors[$cat] ?? '#38A8CC' }}">{{ $label }}</span>
                            <span class="mat-time">🕐 {{ $a->reading_time }} mnt</span>
                        </div>
                        <div class="mat-card-title">{{ $a->title }}</div>
                        <div class="mat-card-excerpt">{{ $a->excerpt }}</div>
                        <div class="mat-card-footer" onclick="event.stopPropagation()">
                            <span class="mat-btn-read">Baca artikel →</span>
                            @auth
                            <a href="{{ route('library.materi.download', $a->slug) }}" class="mat-btn-dl" onclick="event.stopPropagation()">⬇ Unduh</a>
                            @else
                            <span class="mat-btn-dl-lock" title="Login untuk unduh">🔒 Unduh</span>
                            @endauth
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach

            @guest
            <div style="background:linear-gradient(135deg,rgba(56,168,204,.1),rgba(56,168,204,.05));border:1px solid rgba(56,168,204,.2);border-radius:20px;padding:1.5rem;text-align:center;margin-top:2rem;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text);margin-bottom:.4rem;">🔒 Download Semua Artikel</h3>
                <p style="font-size:13px;color:var(--text-3);margin-bottom:1rem;">Login untuk download semua artikel dalam format Markdown — baca offline kapanpun.</p>
                <a href="{{ route('google.login') }}" style="display:inline-block;padding:10px 24px;border-radius:30px;background:linear-gradient(135deg,#38A8CC,#2186A8);color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Login dengan Google</a>
            </div>
            @endguest
        </main>

        {{-- ===== RIGHT SIDEBAR ===== --}}
        <aside class="mat-sidebar-right">

            {{-- Progress baca --}}
            <div class="mat-widget">
                <div class="mat-widget-title">📊 Progres Baca</div>
                @foreach($catLabels as $cat => $label)
                @if($grouped->has($cat))
                @php $pct = $grouped->has($cat) ? min(100, round($grouped[$cat]->count() / max($articles->count(),1) * 100)) : 0; @endphp
                <div class="mat-progress-item">
                    <span>{{ $catIcons[$cat] }} {{ $label }}</span>
                    <span style="font-size:11px;color:var(--text-3);">{{ $grouped[$cat]->count() }} artikel</span>
                </div>
                <div class="mat-progress-bar-wrap">
                    <div class="mat-progress-bar" style="width:{{ $pct }}%;background:{{ $catColors[$cat] ?? '#38A8CC' }};"></div>
                </div>
                @endif
                @endforeach
            </div>

            {{-- Alat relevan --}}
            <div class="mat-widget">
                <div class="mat-widget-title">🎛 Alat untuk Musisi</div>
                <a href="{{ route('tools.chord-builder') }}" class="mat-tool-link">
                    <span>🎸</span><span>Chord Builder</span>
                </a>
                <a href="{{ route('tools.transpose-kunci') }}" class="mat-tool-link">
                    <span>🔀</span><span>Transpose Kunci</span>
                </a>
                <a href="{{ route('tools.bpm-kalkulator') }}" class="mat-tool-link">
                    <span>🥁</span><span>BPM Calculator</span>
                </a>
                <a href="{{ route('tools.kalkulator-royalti') }}" class="mat-tool-link">
                    <span>💰</span><span>Kalkulator Royalti</span>
                </a>
                <a href="{{ route('tools.epk') }}" class="mat-tool-link">
                    <span>📄</span><span>EPK Generator</span>
                </a>
                <a href="{{ route('tools.index') }}" style="display:block;text-align:center;font-size:11px;color:#38A8CC;text-decoration:none;margin-top:.5rem;font-weight:600;">Lihat semua alat →</a>
            </div>

            {{-- CTA bergabung --}}
            @guest
            <div class="mat-cta-widget">
                <h4>🎵 Gabung Komunitas</h4>
                <p>Download semua artikel, ikut diskusi musisi, dan temukan kolaborator.</p>
                <a href="{{ route('google.login') }}" class="mat-cta-btn">Masuk Gratis</a>
            </div>
            @else
            <div class="mat-cta-widget">
                <h4>🎪 Papan Gig</h4>
                <p>Cari audisi band, open mic, dan session player di kotamu.</p>
                <a href="{{ route('gig.board') }}" class="mat-cta-btn">Lihat Gig →</a>
            </div>
            @endguest

        </aside>

    </div>{{-- .mat-layout --}}
</div>{{-- .mat-outer --}}
@endsection

@push('scripts')
<script>
function matFilter(cat, btn) {
    // update nav items (left sidebar + mobile chips)
    document.querySelectorAll('.mat-nav-item, .mat-chip').forEach(function(b) {
        b.classList.toggle('active', b.dataset.cat === cat || (b.getAttribute('onclick') || '').includes("'" + cat + "'"));
    });
    document.querySelectorAll('.mat-section').forEach(function(s) {
        s.style.display = (cat === 'all' || s.dataset.cat === cat) ? '' : 'none';
    });
}
</script>
@endpush
