@extends('layouts.fanbase')
@section('title', $article->title)

@push('styles')
<style>
    .fb-main { --card-bg: var(--card); --accent: var(--sky); --accent-dim: var(--sky-lt); --bg: var(--cream); --bg-3: var(--surface); --border-2: var(--border-lt); --text: var(--text-1); }

    /* ===== LAYOUT ===== */
    .art-outer { padding: 0 0 2rem; }

    .art-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
    }

    .art-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .art-back:hover { color:var(--text-1); }

    /* ===== ARTICLE CONTENT ===== */
    .art-meta { display:flex;align-items:center;gap:8px;margin-bottom:1rem;flex-wrap:wrap; }
    .art-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff; }
    .art-time { font-size:11px;color:var(--text-3); }

    .art-title { font-size:clamp(1.3rem,5vw,1.8rem);font-weight:700;color:var(--text);line-height:1.3;margin-bottom:.5rem; }
    .art-excerpt { font-size:13.5px;color:var(--text-3);line-height:1.7;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border); }

    .art-body { font-size:14px;color:var(--text-2);line-height:1.85; }
    .art-body h1,.art-body h2 { color:var(--text);margin:1.75rem 0 .75rem; }
    .art-body h1 { font-size:1.35rem; }
    .art-body h2 { font-size:1.1rem;border-bottom:1px solid var(--border);padding-bottom:.35rem; }
    .art-body h3 { font-size:.95rem;font-weight:700;color:var(--text);margin:1.25rem 0 .4rem; }
    .art-body p { margin-bottom:1rem; }
    .art-body ul,.art-body ol { padding-left:1.4rem;margin-bottom:1rem; }
    .art-body li { margin-bottom:.4rem; }
    .art-body strong { color:var(--text); }
    .art-body code { background:var(--card-bg);border:1px solid var(--border);border-radius:5px;padding:1px 6px;font-size:12.5px;font-family:'Courier New',monospace;color:var(--accent); }
    .art-body pre { background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:1rem;margin-bottom:1rem;overflow-x:auto; }
    .art-body pre code { background:none;border:none;padding:0;color:var(--text-2); }
    .art-body blockquote { border-left:3px solid var(--accent);padding:.5rem 1rem;margin:1rem 0;background:var(--accent-dim);border-radius:0 8px 8px 0;color:var(--text-2); }
    .art-body table { width:100%;border-collapse:collapse;margin-bottom:1rem;font-size:13px; }
    .art-body th,.art-body td { padding:.5rem .75rem;border:1px solid var(--border);text-align:left; }
    .art-body th { background:var(--card-bg);font-weight:700;color:var(--text); }
    .art-body hr { border:none;border-top:1px solid var(--border);margin:1.5rem 0; }
    .art-body a { color:var(--accent); }

    .art-actions { display:flex;align-items:center;gap:10px;margin-top:2rem;padding-top:1rem;border-top:1px solid var(--border); }
    .art-btn-dl { display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:20px;background:linear-gradient(135deg,var(--accent),#2186A8);color:#fff;font-size:13px;font-weight:600;text-decoration:none; }
    .art-btn-dl-lock { display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:20px;background:var(--card-bg);border:1px solid var(--border);color:var(--text-3);font-size:13px;font-weight:600;text-decoration:none; }
    .art-btn-back { display:inline-flex;align-items:center;gap:5px;padding:9px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:13px;text-decoration:none; }
    .art-btn-back:hover { border-color:var(--accent);color:var(--accent); }

    .art-nav { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:1.5rem; }
    .art-nav-card { background:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:.9rem;text-decoration:none;transition:.2s; }
    .art-nav-card:hover { border-color:var(--accent); }
    .art-nav-label { font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);margin-bottom:.3rem; }
    .art-nav-title { font-size:12px;font-weight:600;color:var(--text);line-height:1.4; }
    .art-nav-card.right { text-align:right; }

    /* Related articles (below article, shown as row) */
    .art-related-row { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-top:1.5rem; }
    .art-related-card { background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.85rem;text-decoration:none;transition:.2s; }
    .art-related-card:hover { border-color:var(--sky); }
    .art-related-title { font-size:12.5px;font-weight:500;color:var(--text-1);line-height:1.35; }
    .art-related-meta { font-size:10px;color:var(--text-3);margin-top:3px; }
</style>
@endpush

@section('content')
<div class="art-outer">
    <a href="{{ route('library.materi') }}" class="art-back">← Semua Materi</a>

    @php
    $catColors = ['teori'=>'#38A8CC','produksi'=>'#a855f7','kolaborasi'=>'#f59e0b','rilis'=>'#22c55e','karir'=>'#f97316'];
    $catTools  = [
        'teori'      => [['icon'=>'🎸','name'=>'Chord Builder','route'=>'tools.chord-builder'],['icon'=>'🔀','name'=>'Transpose Kunci','route'=>'tools.transpose-kunci'],['icon'=>'🥁','name'=>'BPM Calculator','route'=>'tools.bpm-kalkulator']],
        'produksi'   => [['icon'=>'✂️','name'=>'Potong Lagu','route'=>'tools.potong-lagu'],['icon'=>'🎤','name'=>'Hapus Vokal','route'=>'tools.hapus-vokal'],['icon'=>'🏷️','name'=>'Edit Metadata','route'=>'tools.edit-metadata']],
        'kolaborasi' => [['icon'=>'📄','name'=>'EPK Generator','route'=>'tools.epk'],['icon'=>'💼','name'=>'Rate Card','route'=>'tools.rate-card'],['icon'=>'🎵','name'=>'Setlist Builder','route'=>'tools.setlist']],
        'rilis'      => [['icon'=>'🎨','name'=>'Buat Cover Art','route'=>'tools.cover-art'],['icon'=>'🚀','name'=>'Kartu Promo Rilis','route'=>'tools.kartu-rilis'],['icon'=>'⏳','name'=>'Countdown Rilis','route'=>'tools.countdown']],
        'karir'      => [['icon'=>'💰','name'=>'Kalkulator Royalti','route'=>'tools.kalkulator-royalti'],['icon'=>'💼','name'=>'Rate Card','route'=>'tools.rate-card'],['icon'=>'📄','name'=>'EPK Generator','route'=>'tools.epk']],
    ];
    $relevantTools = $catTools[$article->category] ?? $catTools['teori'];
    @endphp

    <div class="art-layout">

        {{-- ===== ARTICLE CONTENT ===== --}}
        <div>
            <div class="art-meta">
                <span class="art-cat-pill" style="background:{{ $catColors[$article->category] ?? '#38A8CC' }}">{{ $article->category_label }}</span>
                <span class="art-time">🕐 {{ $article->reading_time }} menit baca</span>
            </div>

            <h1 class="art-title">{{ $article->title }}</h1>
            <p class="art-excerpt">{{ $article->excerpt }}</p>

            <div class="art-body" id="artBody"></div>

            <div class="art-actions">
                @auth
                <a href="{{ route('library.materi.download', $article->slug) }}" class="art-btn-dl">⬇ Download Artikel</a>
                @else
                <a href="{{ route('google.login') }}" class="art-btn-dl-lock">🔒 Login untuk Download</a>
                @endauth
                <a href="{{ route('library.materi') }}" class="art-btn-back">← Semua Artikel</a>
            </div>

            @if($prev || $next)
            <div class="art-nav">
                @if($prev)
                <a href="{{ route('library.materi.show', $prev->slug) }}" class="art-nav-card">
                    <div class="art-nav-label">← Sebelumnya</div>
                    <div class="art-nav-title">{{ $prev->title }}</div>
                </a>
                @else<div></div>@endif
                @if($next)
                <a href="{{ route('library.materi.show', $next->slug) }}" class="art-nav-card right">
                    <div class="art-nav-label">Berikutnya →</div>
                    <div class="art-nav-title">{{ $next->title }}</div>
                </a>
                @endif
            </div>
            @endif
        </div>

    </div>

    {{-- Artikel terkait (below) --}}
    @if($related->count())
    <p style="font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--text-3);margin-top:2rem;margin-bottom:.6rem;">📖 Artikel Terkait</p>
    <div class="art-related-row">
        @foreach($related as $r)
        <a href="{{ route('library.materi.show', $r->slug) }}" class="art-related-card">
            <div class="art-related-title">{{ $r->title }}</div>
            <div class="art-related-meta">🕐 {{ $r->reading_time }} mnt</div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function() {
    var raw = @json($article->content_markdown);
    function mdToHtml(md) {
        return md
            .replace(/```[\w]*\n?([\s\S]*?)```/g, function(_, code) {
                return '<pre><code>' + esc(code.trim()) + '</code></pre>';
            })
            .replace(/^### (.+)$/gm, '<h3>$1</h3>')
            .replace(/^## (.+)$/gm, '<h2>$1</h2>')
            .replace(/^# (.+)$/gm, '<h1>$1</h1>')
            .replace(/^---$/gm, '<hr>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>')
            .replace(/\|(.+)\|\n\|[-| :]+\|\n((?:\|.+\|\n?)+)/g, function(_, header, rows) {
                var ths = header.split('|').filter(function(c){return c.trim();}).map(function(c){ return '<th>' + c.trim() + '</th>'; }).join('');
                var trs = rows.trim().split('\n').map(function(row) {
                    var tds = row.split('|').filter(function(c){return c.trim();}).map(function(c){ return '<td>' + c.trim() + '</td>'; }).join('');
                    return '<tr>' + tds + '</tr>';
                }).join('');
                return '<table><thead><tr>' + ths + '</tr></thead><tbody>' + trs + '</tbody></table>';
            })
            .replace(/((?:^- .+\n?)+)/gm, function(block) {
                var items = block.trim().split('\n').map(function(l){ return '<li>' + l.replace(/^- /, '') + '</li>'; }).join('');
                return '<ul>' + items + '</ul>';
            })
            .replace(/((?:^\d+\. .+\n?)+)/gm, function(block) {
                var items = block.trim().split('\n').map(function(l){ return '<li>' + l.replace(/^\d+\. /, '') + '</li>'; }).join('');
                return '<ol>' + items + '</ol>';
            })
            .replace(/\n\n([^<\n].+?)(?=\n\n|$)/gs, function(_, p) { return '\n\n<p>' + p + '</p>'; })
            .replace(/([^>])\n([^<])/g, '$1 $2');
    }
    function esc(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    document.getElementById('artBody').innerHTML = mdToHtml(raw);
})();
</script>
@endpush
