@extends('layouts.app')
@section('title', 'Materi Musik Gratis — Panduan dari Teori sampai Rilis')

@push('styles')
<style>
    .mat-page { max-width: 800px; margin: 0 auto; padding: 1.5rem 1rem 5rem; }
    .mat-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .mat-back:hover { color:var(--text); }

    .mat-hero { text-align:center; margin-bottom:2rem; }
    .mat-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#38A8CC;background:rgba(56,168,204,.12);border:1px solid rgba(56,168,204,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .mat-hero h1 { font-family:'Sora','Space Grotesk',sans-serif;font-size:clamp(1.4rem,5vw,2rem);font-weight:700;color:var(--text-1);line-height:1.2;margin-bottom:.5rem; }
    .mat-hero p { font-size:13.5px;color:var(--text-3);max-width:520px;margin:0 auto;line-height:1.7; }

    .mat-stats { display:flex;gap:1.5rem;justify-content:center;margin-top:1rem;flex-wrap:wrap;font-size:12px;color:var(--text-3); }
    .mat-stats b { color:var(--text-1); }

    .mat-filter { display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem; }
    .mat-chip { padding:6px 14px;border-radius:20px;border:1px solid var(--border);background:var(--surface);color:var(--text-3);font-size:12px;font-weight:500;cursor:pointer;transition:.15s;font-family:inherit; }
    .mat-chip:hover { border-color:#38A8CC;color:#38A8CC; }
    .mat-chip.active { background:#38A8CC;color:#fff;border-color:#38A8CC; }

    .mat-section-label { font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--text-3);font-weight:700;margin:1.5rem 0 .75rem;padding-bottom:.4rem;border-bottom:1px solid var(--border-lt); }

    .mat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px;margin-bottom:1.5rem; }
    .mat-card { background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.1rem;box-shadow:var(--shadow-sm);transition:.2s;text-decoration:none;display:flex;flex-direction:column;gap:8px; }
    .mat-card:hover { transform:translateY(-2px);box-shadow:var(--shadow);border-color:var(--sky-mid); }
    .mat-card-top { display:flex;align-items:center;justify-content:space-between;gap:8px; }
    .mat-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff;letter-spacing:.04em;flex-shrink:0; }
    .mat-time { font-size:10px;color:var(--text-4);display:flex;align-items:center;gap:3px; }
    .mat-card-title { font-family:'Sora',sans-serif;font-size:14px;font-weight:600;color:var(--text-1);line-height:1.4; }
    .mat-card-excerpt { font-size:12px;color:var(--text-3);line-height:1.6;flex:1; }
    .mat-card-footer { display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:4px; }
    .mat-btn-read { font-size:11px;color:#38A8CC;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px; }
    .mat-btn-dl { font-size:11px;padding:4px 12px;border-radius:12px;background:rgba(56,168,204,.1);border:1px solid rgba(56,168,204,.25);color:#38A8CC;font-weight:600;text-decoration:none;transition:.15s; }
    .mat-btn-dl:hover { background:#38A8CC;color:#fff; }
    .mat-btn-dl-lock { font-size:11px;padding:4px 12px;border-radius:12px;background:var(--surface);border:1px solid var(--border);color:var(--text-4);cursor:not-allowed;display:inline-flex;align-items:center;gap:4px; }

    .mat-cta { background:linear-gradient(135deg,rgba(56,168,204,.1),rgba(56,168,204,.05));border:1px solid rgba(56,168,204,.2);border-radius:20px;padding:1.5rem;text-align:center;margin-top:2rem; }
    .mat-cta h3 { font-family:'Sora',sans-serif;font-size:1rem;font-weight:700;color:var(--text-1);margin-bottom:.4rem; }
    .mat-cta p { font-size:13px;color:var(--text-3);margin-bottom:1rem; }
    .mat-cta-btn { display:inline-block;padding:10px 24px;border-radius:30px;background:linear-gradient(135deg,#38A8CC,#2186A8);color:#fff;text-decoration:none;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(56,168,204,.3); }
</style>
@endpush

@section('content')
<div class="mat-page">
    <a href="{{ route('library') }}" class="mat-back">← Library</a>

    <div class="mat-hero">
        <div class="mat-badge">📚 Materi Gratis</div>
        <h1>Panduan Musik dari Teori sampai Rilis</h1>
        <p>31 artikel lengkap dalam Bahasa Indonesia — dari belajar chord pertama sampai strategi rilis, monetisasi, dan manifesto songwriter independen.</p>
        <div class="mat-stats">
            <span><b>{{ $articles->count() }}</b> artikel</span>
            <span><b>{{ $grouped->count() }}</b> kategori</span>
            <span><b>~{{ $articles->sum('reading_time') }}</b> menit baca</span>
            <span>100% gratis</span>
        </div>
    </div>

    {{-- Filter --}}
    <div class="mat-filter">
        <button class="mat-chip active" onclick="matFilter('all', this)">Semua</button>
        <button class="mat-chip" onclick="matFilter('teori', this)">🎵 Teori Musik</button>
        <button class="mat-chip" onclick="matFilter('produksi', this)">🎛️ Produksi</button>
        <button class="mat-chip" onclick="matFilter('kolaborasi', this)">🤝 Kolaborasi</button>
        <button class="mat-chip" onclick="matFilter('rilis', this)">🚀 Rilis & Branding</button>
        <button class="mat-chip" onclick="matFilter('karir', this)">💼 Karir & Bisnis</button>
    </div>

    @php
    $catLabels = ['teori' => 'Teori Musik', 'produksi' => 'Produksi & Recording', 'kolaborasi' => 'Kolaborasi', 'rilis' => 'Rilis & Branding', 'karir' => 'Karir & Bisnis Musik'];
    $catColors = ['teori' => '#38A8CC', 'produksi' => '#a855f7', 'kolaborasi' => '#f59e0b', 'rilis' => '#22c55e', 'karir' => '#f97316'];
    $catIcons  = ['teori' => '🎵', 'produksi' => '🎛️', 'kolaborasi' => '🤝', 'rilis' => '🚀', 'karir' => '💼'];
    @endphp

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
    <div class="mat-cta">
        <h3>🔒 Download Semua Artikel</h3>
        <p>Login untuk download semua artikel dalam format Markdown — baca offline kapanpun.</p>
        <a href="{{ route('google.login') }}" class="mat-cta-btn">Login dengan Google</a>
    </div>
    @endguest
</div>
@endsection

@push('scripts')
<script>
function matFilter(cat, btn) {
    document.querySelectorAll('.mat-chip').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.mat-section').forEach(function(s){
        s.style.display = (cat === 'all' || s.dataset.cat === cat) ? '' : 'none';
    });
}
</script>
@endpush
