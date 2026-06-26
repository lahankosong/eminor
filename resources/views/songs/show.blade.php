@extends('layouts.app')

@push('preload')
@if($song->youtube_id)
<link rel="preload" as="image" href="https://img.youtube.com/vi/{{ $song->youtube_id }}/maxresdefault.jpg">
@endif
@if($prevSong)
<link rel="prev" href="{{ route('song.show', $prevSong->slug) }}">
@endif
@if($nextSong)
<link rel="next" href="{{ route('song.show', $nextSong->slug) }}">
@endif
@endpush

@push('styles')
<style>
    /* HERO */
    .song-hero {
        position: relative; min-height: 55vh;
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 0 0 3rem; overflow: hidden;
    }
    .song-hero-bg { position: absolute; inset: 0; z-index: 0; background: var(--bg); }
    .song-hero-thumb {
        position: absolute; inset: 0; z-index: 0;
        width: 100%; height: 100%; object-fit: cover;
        opacity: 0.15; filter: blur(8px); transform: scale(1.05);
    }
    .song-hero-overlay {
        position: absolute; inset: 0; z-index: 1;
        background: linear-gradient(to top, var(--bg) 30%, transparent 100%);
    }
    .song-hero-content { position: relative; z-index: 2; }
    .song-breadcrumb {
        font-size: 11px; color: var(--text-4); margin-bottom: 1rem;
        display: flex; align-items: center; gap: 8px;
    }
    .song-breadcrumb a { color: var(--text-4); text-decoration: none; transition: 0.15s; }
    .song-breadcrumb a:hover { color: var(--text-2); }
    .song-era-badge {
        display: inline-block; font-size: 10px; letter-spacing: 0.2em;
        color: var(--text-3); text-transform: uppercase; border: 1px solid var(--border);
        padding: 3px 10px; border-radius: 20px; margin-bottom: 1rem;
    }
    .song-hero-title {
        font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 300;
        letter-spacing: 0.1em; margin-bottom: 0.75rem; color: var(--text);
    }
    .song-hero-hook {
        font-size: 14px; color: var(--text-2); font-style: italic;
        max-width: 560px; line-height: 1.7; margin-bottom: 2rem;
    }
    .song-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .hero-btn {
        padding: 9px 20px; border-radius: 50px; font-size: 12px;
        font-weight: 500; text-decoration: none; border: 1px solid var(--border);
        transition: 0.15s; display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer; background: transparent; color: var(--text-2);
    }
    .hero-btn.primary { background: var(--text); color: var(--bg); border-color: var(--text); }
    .hero-btn.primary:hover { opacity: 0.85; }
    .hero-btn:hover { border-color: var(--border); color: var(--text); }
    .hero-btn-spotify { color: #1DB954; }
    .hero-btn-youtube { color: #FF0000; }
    .hero-btn-apple   { color: #fc3c44; }

    /* LAYOUT */
    .song-layout {
        display: grid; grid-template-columns: 1fr 300px;
        gap: 3rem; align-items: start; padding: 3rem 0;
    }
    .song-main { min-width: 0; }
    .song-sidebar { position: sticky; top: 80px; }

    /* SECTION */
    .song-section { margin-bottom: 3rem; }
    .song-section-title {
        font-size: 10px; letter-spacing: 0.25em; color: var(--text-4);
        text-transform: uppercase; margin-bottom: 1.5rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-2);
    }

    /* PLAYER */
    .player-wrap {
        background: var(--bg-2); border-radius: 12px; overflow: hidden;
        margin-bottom: 3rem;
    }
    .player-wrap iframe { width: 100%; height: 360px; display: block; border: none; }

    /* STORY */
    .story-body { font-size: 15px; color: var(--text-2); line-height: 2; }
    .story-body p { margin-bottom: 1.25rem; }
    .story-body strong { color: var(--text); font-weight: 500; }
    .story-body em { color: var(--text-2); font-style: italic; }

    /* CHORD */
    .chord-transpose {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 1rem; padding: 10px 14px;
        background: var(--bg-2); border-radius: 8px; border: 1px solid var(--border);
    }
    .chord-transpose span { font-size: 12px; color: var(--text-3); }
    .transpose-btn {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--bg-3); border: 1px solid var(--border);
        color: var(--text-2); font-size: 14px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: 0.15s; padding: 0;
    }
    .transpose-btn:hover { background: var(--bg-4); color: var(--text); }
    .current-key { font-size: 14px; font-weight: 500; color: var(--accent); min-width: 30px; text-align: center; }
    .chord-body-inline {
        font-family: 'Courier New', monospace; font-size: 13px;
        line-height: 2.4; color: var(--text-2); white-space: pre-wrap;
    }
    .chord-mark   { color: var(--accent); font-weight: 700; }
    .section-mark { color: var(--text-3); font-size: 11px; }

    /* LYRICS */
    .lyrics-body {
        font-size: 14px; color: var(--text-2); line-height: 2.2;
        white-space: pre-wrap; font-style: italic;
    }
    .lyrics-body strong { color: var(--text); font-style: normal; font-weight: 500; }

    /* SHARE */
    .share-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
    .share-btn {
        padding: 8px 18px; border-radius: 50px; font-size: 12px;
        font-weight: 500; text-decoration: none; border: 1px solid var(--border);
        transition: 0.15s; display: inline-flex; align-items: center; gap: 8px;
        cursor: pointer; background: transparent;
    }
    .share-btn:hover { border-color: var(--border); opacity: 0.85; }
    .share-wa   { color: #25D366; }
    .share-x    { color: var(--text-2); }
    .share-copy { color: var(--text-2); }
    .share-copy.copied { color: #4ade80; border-color: #166534; }

    /* COMMENTS */
    .comment-item {
        display: flex; gap: 12px; padding: 1rem 0;
        border-bottom: 1px solid var(--border-2);
    }
    .comment-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        object-fit: cover; background: var(--bg-2); flex-shrink: 0;
    }
    .comment-name { font-size: 13px; font-weight: 500; color: var(--text); }
    .comment-date { font-size: 11px; color: var(--text-4); margin-left: 8px; }
    .comment-body { font-size: 13px; color: var(--text-2); line-height: 1.7; margin-top: 4px; }
    .no-comments { font-size: 13px; color: var(--text-4); padding: 2rem 0; text-align: center; font-style: italic; }
    .comment-form textarea {
        width: 100%; background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text); font-size: 13px;
        padding: 12px 14px; outline: none; resize: vertical;
        min-height: 100px; line-height: 1.7; font-family: inherit; transition: 0.15s;
    }
    .comment-form textarea:focus { border-color: var(--accent); }
    .comment-form textarea::placeholder { color: var(--text-4); }
    .comment-submit {
        margin-top: 10px; padding: 9px 24px; border-radius: 8px;
        font-size: 13px; font-weight: 500; background: var(--text); color: var(--bg);
        border: none; cursor: pointer; transition: 0.2s;
    }
    .comment-submit:hover { opacity: 0.85; }
    .login-to-comment {
        background: var(--bg); border: 1px solid var(--border-2);
        border-radius: 10px; padding: 1.5rem; text-align: center; margin-top: 1.5rem;
    }
    .login-to-comment p { font-size: 13px; color: var(--text-3); margin-bottom: 1rem; }
    .btn-login-comment {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 20px; border-radius: 50px;
        background: var(--text); color: var(--bg); font-size: 13px;
        font-weight: 500; text-decoration: none;
    }

    /* SIDEBAR */
    .sidebar-info {
        background: var(--bg); border: 1px solid var(--border-2);
        border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
    }
    .sidebar-thumb {
        width: 100%; aspect-ratio: 16/9; object-fit: cover;
        border-radius: 8px; background: var(--bg-2); display: block; margin-bottom: 1rem;
        opacity: 0.8;
    }
    .sidebar-title { font-size: 14px; font-weight: 500; color: var(--text); margin-bottom: 4px; }
    .sidebar-era   { font-size: 11px; color: var(--text-4); margin-bottom: 1rem; }
    .sidebar-links { display: flex; flex-direction: column; gap: 6px; }
    .sidebar-link {
        font-size: 12px; padding: 8px 14px; border-radius: 8px;
        text-decoration: none; border: 1px solid var(--border);
        transition: 0.15s; display: flex; align-items: center; gap: 8px;
    }
    .sidebar-link:hover { border-color: var(--border); opacity: 0.85; }
    .sidebar-link-spotify { color: #1DB954; }
    .sidebar-link-youtube { color: #FF0000; }
    .sidebar-link-apple   { color: #fc3c44; }

    /* SONG NAV */
    .song-nav { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 1rem; }
    .song-nav-btn {
        background: var(--bg); border: 1px solid var(--border-2);
        border-radius: 10px; padding: 0.875rem 1rem;
        text-decoration: none; transition: 0.15s; display: block;
    }
    .song-nav-btn:hover { border-color: var(--border); }
    .song-nav-label { font-size: 10px; color: var(--text-4); text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 4px; }
    .song-nav-title { font-size: 12px; color: var(--text-2); font-weight: 500; }
    .song-nav-btn.next { text-align: right; }

    .alert-success {
        background: rgba(74,222,128,0.08); color: #4ade80; border: 1px solid #166534;
        padding: 10px 16px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 13px;
    }

    @media (max-width: 768px) {
        .song-layout { grid-template-columns: 1fr; }
        .song-sidebar { position: static; }
        .player-wrap iframe { height: 220px; }
    }
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="song-hero">
    <div class="song-hero-bg"></div>
    <img src="https://img.youtube.com/vi/{{ $song->youtube_id }}/maxresdefault.jpg"
         class="song-hero-thumb" alt="{{ $song->title }}">
    <div class="song-hero-overlay"></div>
    <div class="song-hero-content">
        <div class="song-breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>›</span>
            <span>{{ $song->era ?? 'Lagu' }}</span>
            <span>›</span>
            <span style="color:var(--text-3);">{{ $song->title }}</span>
        </div>
        @if($song->era)
        <span class="song-era-badge">{{ $song->era }}</span>
        @endif
        <h1 class="song-hero-title">{{ $song->title }}</h1>
        @if($song->story_hook)
        <p class="song-hero-hook">"{{ $song->story_hook }}"</p>
        @endif
        <div class="song-hero-actions">
            <button class="hero-btn primary"
                onclick="document.getElementById('playerSection').scrollIntoView({behavior:'smooth'}); gtag && gtag('event', 'play_song', {event_category:'music', song_title:'{{ addslashes($song->title) }}', song_slug:'{{ $song->slug }}'})">
                &#9654; Putar lagu
            </button>
            @if($song->spotify_url)
            <a href="{{ $song->spotify_url }}" target="_blank" class="hero-btn hero-btn-spotify">&#9834; Spotify</a>
            @endif
            <a href="https://youtu.be/{{ $song->youtube_id }}" target="_blank" class="hero-btn hero-btn-youtube">&#9658; YouTube</a>
            @if($song->apple_music_url)
            <a href="{{ $song->apple_music_url }}" target="_blank" class="hero-btn hero-btn-apple">&#9835; Apple Music</a>
            @endif
        </div>
    </div>
</div>

<div class="song-layout">

    {{-- MAIN --}}
    <div class="song-main">

        @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
        @endif

        {{-- PLAYER --}}
        <div id="playerSection" class="player-wrap">
            <iframe
                src="https://www.youtube.com/embed/{{ $song->youtube_id }}?rel=0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        {{-- CERITA --}}
        @if($song->description)
        <div class="song-section">
            <p class="song-section-title">Cerita di balik lagu</p>
            <div class="story-body">
                {!! nl2br(e($song->description)) !!}
            </div>
        </div>
        @endif

        {{-- CHORD --}}
        @if($song->chords)
        <div class="song-section">
            <p class="song-section-title">Chord</p>
            @if($song->key_signature)
            <div class="chord-transpose">
                <span>Transpose</span>
                <button class="transpose-btn" onclick="transposeChord(-1)">&#8722;</button>
                <span class="current-key" id="currentKey">{{ $song->key_signature }}</span>
                <button class="transpose-btn" onclick="transposeChord(1)">&#43;</button>
                @if($song->tempo)
                <span style="font-size:11px;color:var(--text-4);margin-left:auto;">&#9834; {{ $song->tempo }} bpm</span>
                @endif
            </div>
            @endif
            <div class="chord-body-inline" id="chordDisplay">{{ $song->chords }}</div>
        </div>
        @endif

        {{-- LIRIK --}}
        @if($song->lyrics)
        <div class="song-section">
            <p class="song-section-title">Lirik lagu</p>
            <div class="lyrics-body">{!! nl2br(e($song->lyrics)) !!}</div>
        </div>
        @endif

        {{-- CTA: CHORD BUILDER --}}
        @if($song->chords || $song->key_signature)
        <div class="song-section">
            <div style="background:var(--bg-2);border:1px solid var(--border);border-radius:14px;padding:1.1rem 1.2rem;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <span style="font-size:24px;">🎸</span>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:3px;">Eksplor chord {{ $song->title }} di Chord Builder</div>
                    <div style="font-size:12px;color:var(--text-3);">Generate variasi progresi chord, transpose kunci, dan simpan favoritmu — gratis di fanbase.</div>
                </div>
                <a href="{{ route('tools.chord-builder') }}" style="flex-shrink:0;padding:8px 18px;border-radius:20px;background:var(--accent);color:#fff;font-size:12.5px;font-weight:600;text-decoration:none;white-space:nowrap;">Buka Chord Builder →</a>
            </div>
        </div>
        @endif

        {{-- CTA: DISKUSI KOMUNITAS --}}
        <div class="song-section">
            <div style="background:linear-gradient(135deg,rgba(56,168,204,.08),rgba(56,168,204,.03));border:1px solid rgba(56,168,204,.2);border-radius:14px;padding:1.1rem 1.2rem;">
                <div style="font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:var(--accent);font-weight:700;margin-bottom:.4rem;">💬 Komunitas Musisi</div>
                <p style="font-size:13.5px;color:var(--text-2);margin-bottom:.85rem;line-height:1.6;">Punya cerita tentang lagu ini, pertanyaan chord, atau mau cover? Diskusikan di komunitas musisi Margonoandi — tempat musisi kamar Indonesia saling berbagi.</p>
                @auth
                <a href="{{ route('kita') }}" style="display:inline-block;padding:8px 20px;border-radius:20px;background:var(--accent);color:#fff;font-size:12.5px;font-weight:600;text-decoration:none;">Diskusikan di komunitas →</a>
                @else
                <a href="{{ route('google.login') }}" style="display:inline-block;padding:8px 20px;border-radius:20px;background:var(--accent);color:#fff;font-size:12.5px;font-weight:600;text-decoration:none;" onclick="gtag && gtag('event','cta_click',{event_category:'song_page',button:'diskusi_komunitas',song:'{{ addslashes($song->slug) }}'})">Gabung komunitas untuk diskusi →</a>
                @endauth
            </div>
        </div>

        {{-- SHARE --}}
        <div class="song-section">
            <p class="song-section-title">Bagikan lagu ini</p>
            <div class="share-buttons">
                <a href="https://wa.me/?text={{ urlencode($song->title . ' — Margonoandi | ' . url()->current()) }}"
                   target="_blank" class="share-btn share-wa">&#128172; WhatsApp</a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Dengerin "' . $song->title . '" dari Margonoandi') }}&url={{ urlencode(url()->current()) }}"
                   target="_blank" class="share-btn share-x">&#10005; Twitter/X</a>
                <button class="share-btn share-copy" id="copyBtn" onclick="copyLink()">&#128279; Salin link</button>
            </div>
        </div>

        {{-- KOMENTAR --}}
        <div class="song-section">
            <p class="song-section-title">Komentar ({{ $comments->count() }})</p>

            @if($comments->count() > 0)
            <div style="margin-bottom:1.5rem;">
                @foreach($comments as $comment)
                <div class="comment-item">
                    <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.png') }}"
                         class="comment-avatar" alt="{{ $comment->user->name }}">
                    <div style="flex:1;min-width:0;">
                        <span class="comment-name">{{ $comment->user->name }}</span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                        <p class="comment-body">{{ $comment->body }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="no-comments">Belum ada komentar. Jadilah yang pertama.</p>
            @endif

            @auth
            <div class="comment-form">
                <form method="POST" action="{{ route('song.comment', $song->slug) }}">
                    @csrf
                    <textarea name="body"
                        placeholder="Tulis ceritamu, pendapatmu, atau apapun tentang lagu ini..."
                        required></textarea>
                    <button type="submit" class="comment-submit">Kirim komentar</button>
                </form>
            </div>
            @else
            <div class="login-to-comment">
                <p>Login untuk meninggalkan komentar.</p>
                <a href="{{ route('google.login') }}" class="btn-login-comment">
                    <img src="https://www.google.com/favicon.ico" alt="G" style="width:14px;">
                    Masuk dengan Google
                </a>
            </div>
            @endauth
        </div>

    </div>

    {{-- SIDEBAR --}}
    <div class="song-sidebar">
        <div class="sidebar-info">
            <img src="https://img.youtube.com/vi/{{ $song->youtube_id }}/mqdefault.jpg"
                 class="sidebar-thumb" alt="{{ $song->title }}">
            <div class="sidebar-title">{{ $song->title }}</div>
            <div class="sidebar-era">{{ $song->era ?? 'Margonoandi' }}</div>
            <div class="sidebar-links">
                @if($song->spotify_url)
                <a href="{{ $song->spotify_url }}" target="_blank" class="sidebar-link sidebar-link-spotify">
                    &#9834; Dengarkan di Spotify
                </a>
                @endif
                <a href="https://youtu.be/{{ $song->youtube_id }}" target="_blank" class="sidebar-link sidebar-link-youtube">
                    &#9658; Tonton di YouTube
                </a>
                @if($song->apple_music_url)
                <a href="{{ $song->apple_music_url }}" target="_blank" class="sidebar-link sidebar-link-apple">
                    &#9835; Apple Music
                </a>
                @endif
            </div>
        </div>

        {{-- PREV / NEXT --}}
        <div class="song-nav">
            @if($prevSong)
            <a href="{{ route('song.show', $prevSong->slug) }}" class="song-nav-btn">
                <div class="song-nav-label">&#8249; Sebelumnya</div>
                <div class="song-nav-title">{{ $prevSong->title }}</div>
            </a>
            @else
            <div></div>
            @endif
            @if($nextSong)
            <a href="{{ route('song.show', $nextSong->slug) }}" class="song-nav-btn next">
                <div class="song-nav-label">Berikutnya &#8250;</div>
                <div class="song-nav-title">{{ $nextSong->title }}</div>
            </a>
            @endif
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
var originalChords  = @json($song->chords ?? '');
var noteNames       = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
var transposeOffset = 0;

function renderChords(text) {
    if (!text) return '';
    return text
        .replace(/\[([^\]]+)\]/g, '<span class="section-mark">[$1]</span>')
        .replace(/\b([A-G][#b]?(?:m|maj|min|dim|aug|sus|add)?[0-9]?(?:\/[A-G][#b]?)?)\b/g,
            '<span class="chord-mark">$1</span>');
}

function transposeNote(note, semitones) {
    var flat = {'Db':'C#','Eb':'D#','Gb':'F#','Ab':'G#','Bb':'A#'};
    var n    = flat[note] || note;
    var idx  = noteNames.indexOf(n);
    if (idx === -1) return note;
    return noteNames[((idx + semitones) % 12 + 12) % 12];
}

function transposeText(text, semitones) {
    if (!semitones) return text;
    return text.replace(/\b([A-G][#b]?)(m|maj|min|dim|aug|sus|add)?([0-9]?)(\/[A-G][#b]?)?\b/g,
        function(match, root, quality, num, bass) {
            var newRoot = transposeNote(root, semitones);
            var newBass = bass ? '/' + transposeNote(bass.slice(1), semitones) : '';
            return newRoot + (quality || '') + (num || '') + newBass;
        });
}

function transposeChord(dir) {
    transposeOffset += dir;
    var transposed = transposeText(originalChords, transposeOffset);
    document.getElementById('chordDisplay').innerHTML = renderChords(transposed);
    var baseKey = '{{ $song->key_signature ?? "" }}';
    if (baseKey) {
        document.getElementById('currentKey').textContent =
            transposeText(baseKey, transposeOffset);
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        var btn = document.getElementById('copyBtn');
        btn.textContent = '✓ Tersalin!';
        btn.classList.add('copied');
        setTimeout(function() {
            btn.innerHTML = '🔗 Salin link';
            btn.classList.remove('copied');
        }, 2000);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('chordDisplay');
    if (el && originalChords) el.innerHTML = renderChords(originalChords);
});
</script>
@endpush
