@extends('layouts.app')

@push('styles')
<style>
    :root { --ac:#38A8CC; --ac-dk:#0ea5e9; --ac-lt:rgba(56,168,204,.12); }

    .lib-page { max-width:820px; margin:0 auto; padding:1.75rem 1rem 5rem; }
    .lib-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3,#94a3b8);text-decoration:none;margin-bottom:1.25rem; }
    .lib-back:hover { color:var(--text); }

    /* Hero */
    .lib-hero { text-align:center; margin-bottom:2rem; }
    .lib-badge { display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ac-dk);background:var(--ac-lt);border:1px solid rgba(56,168,204,.3);border-radius:20px;padding:4px 12px;margin-bottom:.75rem; }
    .lib-hero h1 { font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(1.5rem,5vw,2.1rem);font-weight:700;color:var(--text);line-height:1.2;margin-bottom:.5rem; }
    .lib-hero p { font-size:13.5px;color:var(--text-3,#94a3b8);max-width:540px;margin:0 auto;line-height:1.7; }

    /* Stats bar */
    .lib-stats { display:flex;justify-content:center;gap:2rem;margin:1.25rem 0 1.75rem;flex-wrap:wrap; }
    .lib-stat { text-align:center; }
    .lib-stat-num { font-size:1.5rem;font-weight:700;color:var(--ac);line-height:1; }
    .lib-stat-lbl { font-size:11px;color:var(--text-3);margin-top:2px;letter-spacing:.04em; }

    /* Mood filter */
    .lib-filters { display:flex;gap:8px;flex-wrap:wrap;justify-content:center;margin-bottom:1.5rem; }
    .lib-filter { padding:6px 16px;border-radius:20px;border:1px solid var(--border,rgba(56,168,204,.14));background:transparent;color:var(--text-3);font-size:12px;font-weight:600;cursor:pointer;transition:.18s;font-family:inherit; }
    .lib-filter:hover { border-color:var(--ac);color:var(--text); }
    .lib-filter.active { background:var(--ac-lt);border-color:var(--ac);color:var(--ac); }

    /* Grid */
    .lib-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px; }

    /* Song card */
    .song-card { background:var(--card-bg,rgba(56,168,204,.05));border:1px solid var(--border,rgba(56,168,204,.14));border-radius:16px;padding:1.1rem 1.2rem;transition:.18s;position:relative;display:flex;flex-direction:column;gap:8px; }
    .song-card:hover { border-color:var(--ac);transform:translateY(-3px);box-shadow:0 16px 34px -20px rgba(56,168,204,.3); }
    .song-card.hidden { display:none; }

    .song-top { display:flex;align-items:flex-start;justify-content:space-between;gap:8px; }
    .song-title { font-weight:700;font-size:14px;color:var(--text);line-height:1.3;flex:1; }
    .song-year { font-size:11px;color:var(--text-3);flex-shrink:0;margin-top:2px; }

    .song-meta { display:flex;align-items:center;gap:6px;flex-wrap:wrap; }
    .song-genre { font-size:11px;color:var(--text-3);background:rgba(255,255,255,.04);border:1px solid var(--border-2,rgba(56,168,204,.07));border-radius:8px;padding:2px 8px; }
    .song-mood { font-size:10px;font-weight:700;border-radius:8px;padding:2px 9px;letter-spacing:.04em;text-transform:uppercase; }
    .mood-energetik    { background:rgba(251,146,60,.12);color:#fb923c;border:1px solid rgba(251,146,60,.25); }
    .mood-emosional    { background:rgba(167,139,250,.12);color:#a78bfa;border:1px solid rgba(167,139,250,.25); }
    .mood-introspektif { background:rgba(56,189,248,.12);color:#38bdf8;border:1px solid rgba(56,189,248,.25); }
    .mood-spiritual    { background:rgba(52,211,153,.12);color:#34d399;border:1px solid rgba(52,211,153,.25); }

    .song-theme { font-size:12px;color:var(--text-3);line-height:1.4;margin-top:2px; }

    .song-actions { display:flex;gap:8px;margin-top:auto;padding-top:6px; }
    .song-btn { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:6px 14px;border-radius:10px;text-decoration:none;transition:.18s;flex:1;justify-content:center; }
    .song-btn-spotify { background:rgba(29,185,84,.1);color:#1db954;border:1px solid rgba(29,185,84,.25); }
    .song-btn-spotify:hover { background:rgba(29,185,84,.2);border-color:#1db954; }
    .song-btn-all { background:var(--ac-lt);color:var(--ac);border:1px solid rgba(56,168,204,.25); }
    .song-btn-all:hover { background:rgba(56,168,204,.2);border-color:var(--ac); }

    /* No result */
    .lib-empty { text-align:center;color:var(--text-3);font-size:13px;padding:2.5rem 0;display:none; }
    .lib-empty.show { display:block; }

    /* CTA */
    .lib-cta { margin-top:2.5rem;background:linear-gradient(140deg,var(--ac-lt),var(--card-bg));border:1px solid var(--ac);border-radius:18px;padding:1.5rem;text-align:center; }
    .lib-cta h2 { font-family:'Space Grotesk','Sora',sans-serif;font-size:1.05rem;font-weight:700;color:var(--text);margin-bottom:.4rem; }
    .lib-cta p { font-size:12.5px;color:var(--text-3);line-height:1.7;max-width:460px;margin:0 auto .9rem; }
    .lib-cta-btns { display:flex;gap:10px;justify-content:center;flex-wrap:wrap; }
    .lib-cta-btn { display:inline-block;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;text-decoration:none;transition:.18s; }
    .lib-cta-btn-primary { background:linear-gradient(135deg,var(--ac),var(--ac-dk));color:#fff; }
    .lib-cta-btn-secondary { background:var(--card-bg);border:1px solid var(--border);color:var(--text); }
    .lib-cta-btn:hover { opacity:.85; }

    @media(max-width:480px) {
        .lib-grid { grid-template-columns:1fr; }
        .lib-stats { gap:1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="lib-page">
    <a href="{{ route('home') }}" class="lib-back">← Beranda</a>

    <div class="lib-hero">
        <div class="lib-badge">🎵 Music Library</div>
        <h1>Semua Lagu Margonoandi</h1>
        <p>Pop rock, indie acoustic, gospel, sampai electronic pop. Filter berdasarkan mood dan temukan lagu yang pas buat momen kamu.</p>
    </div>

    <div class="lib-stats">
        <div class="lib-stat">
            <div class="lib-stat-num">{{ count($songs) }}</div>
            <div class="lib-stat-lbl">Lagu Rilis</div>
        </div>
        <div class="lib-stat">
            <div class="lib-stat-num">{{ $genres }}</div>
            <div class="lib-stat-lbl">Genre</div>
        </div>
        <div class="lib-stat">
            <div class="lib-stat-num">2019–2026</div>
            <div class="lib-stat-lbl">Periode</div>
        </div>
        <div class="lib-stat">
            <div class="lib-stat-num">3</div>
            <div class="lib-stat-lbl">Mood</div>
        </div>
    </div>

    {{-- Filter mood --}}
    <div class="lib-filters" id="moodFilters">
        <button class="lib-filter active" data-mood="all">🎵 Semua</button>
        <button class="lib-filter" data-mood="energetik">🔥 Energetik</button>
        <button class="lib-filter" data-mood="emosional">💜 Emosional</button>
        <button class="lib-filter" data-mood="introspektif">🌊 Introspektif</button>
    </div>

    {{-- Song grid --}}
    <div class="lib-grid" id="songGrid">
        @foreach($songs as $song)
        <div class="song-card" data-mood="{{ $song['mood'] }}">
            <div class="song-top">
                <span class="song-title">{{ $song['title'] }}</span>
                <span class="song-year">{{ $song['year'] }}</span>
            </div>
            <div class="song-meta">
                <span class="song-genre">{{ $song['genre'] }}</span>
                <span class="song-mood mood-{{ $song['mood'] }}">{{ $moodLabels[$song['mood']] }}</span>
            </div>
            <div class="song-theme">{{ $song['theme'] }}</div>
            <div class="song-actions">
                @if($song['spotify'])
                <a href="{{ $song['spotify'] }}" target="_blank" rel="noopener" class="song-btn song-btn-spotify">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                    Dengarkan
                </a>
                @endif
                @if($song['slug'])
                <a href="{{ route('song.show', $song['slug']) }}" class="song-btn song-btn-all">Detail →</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="lib-empty" id="libEmpty">Tidak ada lagu dengan mood ini.</div>

    <div class="lib-cta">
        <h2>🎸 Buat musikmu sendiri</h2>
        <p>Margonoandi menyediakan tools gratis untuk musisi Indonesia — chord builder, BPM calculator, royalty estimator, dan masih banyak lagi. Semuanya di browser, tanpa daftar.</p>
        <div class="lib-cta-btns">
            <a href="{{ route('tools.index') }}" class="lib-cta-btn lib-cta-btn-primary">Explore Tools Gratis →</a>
            <a href="{{ route('google.login') }}" class="lib-cta-btn lib-cta-btn-secondary">Buat profil musisi</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var filters = document.querySelectorAll('.lib-filter');
    var cards   = document.querySelectorAll('.song-card');
    var empty   = document.getElementById('libEmpty');

    filters.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var mood = btn.dataset.mood;
            filters.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var visible = 0;
            cards.forEach(function (c) {
                var show = mood === 'all' || c.dataset.mood === mood;
                c.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            empty.classList.toggle('show', visible === 0);
        });
    });
})();
</script>
@endpush
