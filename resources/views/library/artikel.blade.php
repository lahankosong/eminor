@extends('layouts.app')
@section('title', $article->title . ' — Materi Musik Margonoandi')

@push('styles')
<style>
    .art-page { max-width: 680px; margin: 0 auto; padding: 1.5rem 1rem 5rem; }
    .art-back { display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--text-3);text-decoration:none;margin-bottom:1.25rem; }
    .art-back:hover { color:var(--text); }

    .art-meta { display:flex;align-items:center;gap:8px;margin-bottom:1rem;flex-wrap:wrap; }
    .art-cat-pill { font-size:10px;font-weight:700;padding:3px 9px;border-radius:12px;color:#fff; }
    .art-time { font-size:11px;color:var(--text-4); }

    .art-title { font-family:'Sora',sans-serif;font-size:clamp(1.3rem,5vw,1.8rem);font-weight:700;color:var(--text-1);line-height:1.3;margin-bottom:.5rem; }
    .art-excerpt { font-size:13.5px;color:var(--text-3);line-height:1.7;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border); }

    .art-body { font-size:14px;color:var(--text-2);line-height:1.85; }
    .art-body h1,.art-body h2 { font-family:'Sora',sans-serif;color:var(--text-1);margin:1.75rem 0 .75rem; }
    .art-body h1 { font-size:1.35rem; }
    .art-body h2 { font-size:1.1rem;border-bottom:1px solid var(--border-lt);padding-bottom:.35rem; }
    .art-body h3 { font-size:.95rem;font-weight:700;color:var(--text-1);margin:1.25rem 0 .4rem; }
    .art-body p { margin-bottom:1rem; }
    .art-body ul,.art-body ol { padding-left:1.4rem;margin-bottom:1rem; }
    .art-body li { margin-bottom:.4rem; }
    .art-body strong { color:var(--text-1); }
    .art-body em { color:var(--text-2); }
    .art-body code { background:var(--surface);border:1px solid var(--border);border-radius:5px;padding:1px 6px;font-size:12.5px;font-family:'Courier New',monospace;color:#38A8CC; }
    .art-body pre { background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:1rem;margin-bottom:1rem;overflow-x:auto; }
    .art-body pre code { background:none;border:none;padding:0;color:var(--text-2); }
    .art-body blockquote { border-left:3px solid #38A8CC;padding:.5rem 1rem;margin:1rem 0;background:rgba(56,168,204,.06);border-radius:0 8px 8px 0;color:var(--text-2); }
    .art-body table { width:100%;border-collapse:collapse;margin-bottom:1rem;font-size:13px; }
    .art-body th,.art-body td { padding:.5rem .75rem;border:1px solid var(--border);text-align:left; }
    .art-body th { background:var(--surface);font-weight:700;color:var(--text-1); }
    .art-body hr { border:none;border-top:1px solid var(--border);margin:1.5rem 0; }

    .art-actions { display:flex;align-items:center;gap:10px;margin-top:2rem;padding-top:1rem;border-top:1px solid var(--border); }
    .art-btn-dl { display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:20px;background:linear-gradient(135deg,#38A8CC,#2186A8);color:#fff;font-size:13px;font-weight:600;text-decoration:none; }
    .art-btn-dl-lock { display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border-radius:20px;background:var(--surface);border:1px solid var(--border);color:var(--text-3);font-size:13px;font-weight:600;text-decoration:none; }
    .art-btn-back { display:inline-flex;align-items:center;gap:5px;padding:9px 18px;border-radius:20px;border:1px solid var(--border);color:var(--text-3);font-size:13px;text-decoration:none; }

    .art-nav { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:1.5rem; }
    .art-nav-card { background:var(--card);border:1px solid var(--border);border-radius:12px;padding:.9rem;text-decoration:none;transition:.2s; }
    .art-nav-card:hover { border-color:var(--sky-mid);box-shadow:var(--shadow-sm); }
    .art-nav-label { font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--text-4);margin-bottom:.3rem; }
    .art-nav-title { font-size:12px;font-weight:600;color:var(--text-1);line-height:1.4; }
    .art-nav-card.right { text-align:right; }
</style>
@endpush

@section('content')
<div class="art-page">
    <a href="{{ route('library.materi') }}" class="art-back">← Semua Materi</a>

    @php
    $catColors = ['teori' => '#38A8CC', 'produksi' => '#a855f7', 'kolaborasi' => '#f59e0b', 'rilis' => '#22c55e', 'karir' => '#f97316'];
    @endphp

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
        @else
        <div></div>
        @endif
        @if($next)
        <a href="{{ route('library.materi.show', $next->slug) }}" class="art-nav-card right">
            <div class="art-nav-label">Berikutnya →</div>
            <div class="art-nav-title">{{ $next->title }}</div>
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function() {
    var raw = @json($article->content_markdown);
    // Simple markdown-to-HTML renderer (no external lib needed)
    function mdToHtml(md) {
        return md
            // Code blocks first (multi-line)
            .replace(/```[\w]*\n?([\s\S]*?)```/g, function(_, code) {
                return '<pre><code>' + esc(code.trim()) + '</code></pre>';
            })
            // Headings
            .replace(/^### (.+)$/gm, '<h3>$1</h3>')
            .replace(/^## (.+)$/gm, '<h2>$1</h2>')
            .replace(/^# (.+)$/gm, '<h1>$1</h1>')
            // HR
            .replace(/^---$/gm, '<hr>')
            // Bold
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            // Italic
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            // Inline code
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            // Blockquote
            .replace(/^> (.+)$/gm, '<blockquote>$1</blockquote>')
            // Tables
            .replace(/\|(.+)\|\n\|[-| :]+\|\n((?:\|.+\|\n?)+)/g, function(_, header, rows) {
                var ths = header.split('|').filter(function(c){return c.trim();}).map(function(c){ return '<th>' + c.trim() + '</th>'; }).join('');
                var trs = rows.trim().split('\n').map(function(row) {
                    var tds = row.split('|').filter(function(c){return c.trim();}).map(function(c){ return '<td>' + c.trim() + '</td>'; }).join('');
                    return '<tr>' + tds + '</tr>';
                }).join('');
                return '<table><thead><tr>' + ths + '</tr></thead><tbody>' + trs + '</tbody></table>';
            })
            // Lists (unordered)
            .replace(/((?:^- .+\n?)+)/gm, function(block) {
                var items = block.trim().split('\n').map(function(l){ return '<li>' + l.replace(/^- /, '') + '</li>'; }).join('');
                return '<ul>' + items + '</ul>';
            })
            // Lists (ordered)
            .replace(/((?:^\d+\. .+\n?)+)/gm, function(block) {
                var items = block.trim().split('\n').map(function(l){ return '<li>' + l.replace(/^\d+\. /, '') + '</li>'; }).join('');
                return '<ol>' + items + '</ol>';
            })
            // Paragraphs
            .replace(/\n\n([^<\n].+?)(?=\n\n|$)/gs, function(_, p) { return '\n\n<p>' + p + '</p>'; })
            // Line breaks inside paragraphs
            .replace(/([^>])\n([^<])/g, '$1 $2');
    }
    function esc(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    document.getElementById('artBody').innerHTML = mdToHtml(raw);
})();
</script>
@endpush
