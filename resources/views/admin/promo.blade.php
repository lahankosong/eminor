@extends('layouts.admin')

@push('styles')
<style>
    .promo-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
        margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
    }
    .promo-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .promo-header p  { font-size: 12px; color: var(--text-3); margin-top: 2px; }
    .btn-back {
        font-size: 12px; color: var(--text-2); text-decoration: none;
        border: 1px solid var(--border); padding: 6px 14px; border-radius: 8px;
    }
    .btn-back:hover { color: var(--text); border-color: var(--text-3); }

    .info-banner {
        background: #0d2218; color: #4ade80; border: 1px solid #166534;
        border-radius: 10px; padding: 12px 16px; margin-bottom: 1.5rem;
        font-size: 13px; line-height: 1.6;
    }

    .promo-layout { display: grid; grid-template-columns: 280px 1fr; gap: 1.5rem; align-items: start; }

    /* Song selector */
    .song-selector { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; position: sticky; top: 1rem; }
    .selector-header { padding: 0.9rem 1.1rem; border-bottom: 1px solid var(--border); font-size: 11px; color: var(--text-3); letter-spacing: 0.12em; text-transform: uppercase; }
    .song-list { max-height: 72vh; overflow-y: auto; }
    .song-option { display: flex; align-items: center; gap: 10px; padding: 9px 1.1rem; cursor: pointer; transition: 0.15s; border-bottom: 1px solid var(--border-2); }
    .song-option:hover { background: var(--card-bg); }
    .song-option.selected { background: var(--card-bg); border-left: 2px solid var(--accent); }
    .song-option-thumb { width: 42px; height: 26px; object-fit: cover; border-radius: 4px; background: var(--bg-3); flex-shrink: 0; }
    .song-option-title { font-size: 12px; color: var(--text-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .song-option-era { font-size: 10px; color: var(--text-3); margin-top: 1px; }

    .promo-main { min-width: 0; }
    .empty-state { text-align: center; color: var(--text-3); padding: 4rem 1rem; font-size: 13px; line-height: 1.6; }

    /* Template card */
    .tpl-card { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1.25rem; overflow: hidden; }
    .tpl-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 0.9rem 1.1rem; border-bottom: 1px solid var(--border); }
    .tpl-title { font-size: 13px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .tpl-title small { font-weight: 400; color: var(--text-3); font-size: 11px; }
    .btn-copy {
        padding: 6px 14px; border-radius: 7px; font-size: 12px; font-weight: 500;
        background: var(--accent-dim); color: var(--accent); border: 1px solid transparent;
        cursor: pointer; transition: 0.15s; white-space: nowrap;
    }
    .btn-copy:hover { filter: brightness(1.1); }
    .btn-copy.done { background: #0d2e1a; color: #4ade80; }
    .tpl-body { padding: 1.1rem; }
    .tpl-textarea {
        width: 100%; background: var(--bg-3); border: 1px solid var(--border); border-radius: 8px;
        color: var(--text-2); font-size: 13px; padding: 10px 12px; outline: none;
        font-family: inherit; line-height: 1.7; resize: vertical; min-height: 120px;
    }
    .tpl-textarea:focus { border-color: var(--text-3); }
    .tpl-variants { display: flex; gap: 6px; margin-bottom: 10px; flex-wrap: wrap; }
    .variant-btn {
        padding: 5px 12px; border-radius: 6px; font-size: 12px;
        background: var(--bg-3); border: 1px solid var(--border); color: var(--text-3);
        cursor: pointer; transition: 0.15s;
    }
    .variant-btn.active { background: var(--text); color: var(--bg); border-color: var(--text); }

    .toast {
        position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px);
        background: var(--text); color: var(--bg); padding: 10px 20px; border-radius: 8px;
        font-size: 13px; opacity: 0; pointer-events: none; transition: 0.25s; z-index: 999;
    }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    @media (max-width: 800px) {
        .promo-layout { grid-template-columns: 1fr; }
        .song-selector { position: static; }
        .song-list { max-height: 40vh; }
    }
</style>
@endpush

@section('content')

<div class="promo-header">
    <div>
        <h2>Promo Templates</h2>
        <p>Generator caption siap-posting â€” tanpa biaya API</p>
    </div>
    <a href="{{ route('admin.index') }}" class="btn-back">â† Panel Admin</a>
</div>

<div class="info-banner">
    ðŸ’¡ <b>Tanpa biaya.</b> Pilih lagu â†’ template caption otomatis terisi dari data lagu â†’
    edit bila perlu â†’ <b>Copy</b> â†’ tempel ke TikTok/IG/YouTube/Spotify/Discord.
</div>

<div class="promo-layout">
    {{-- SELECTOR --}}
    <div class="song-selector">
        <div class="selector-header">Pilih lagu</div>
        <div class="song-list">
            @forelse($songs as $song)
            <div class="song-option" data-id="{{ $song->id }}" onclick="selectSong({{ $song->id }}, this)">
                <img src="https://img.youtube.com/vi/{{ $song->youtube_id }}/mqdefault.jpg" class="song-option-thumb" alt="">
                <div style="min-width:0;">
                    <div class="song-option-title">{{ $song->title }}</div>
                    <div class="song-option-era">{{ $song->era ?? 'â€”' }}</div>
                </div>
            </div>
            @empty
            <div style="padding:1rem;font-size:12px;color:var(--text-3);">Belum ada lagu.</div>
            @endforelse
        </div>
    </div>

    {{-- MAIN --}}
    <div class="promo-main">
        <div class="empty-state" id="emptyState">
            <p style="font-size:24px;margin-bottom:0.75rem;">ðŸ“‹</p>
            <p>Pilih lagu di kiri untuk membuat template promosi.</p>
        </div>

        <div id="templates" style="display:none;">
            {{-- TikTok / Reels Hook --}}
            <div class="tpl-card">
                <div class="tpl-head">
                    <div class="tpl-title">ðŸŽ¬ TikTok / Reels Hook <small>15â€“30 detik</small></div>
                    <button class="btn-copy" onclick="copyTpl('tplTiktok', this)">Copy</button>
                </div>
                <div class="tpl-body">
                    <textarea class="tpl-textarea" id="tplTiktok"></textarea>
                </div>
            </div>

            {{-- Instagram Caption (3 variasi) --}}
            <div class="tpl-card">
                <div class="tpl-head">
                    <div class="tpl-title">ðŸ“¸ Instagram Caption</div>
                    <button class="btn-copy" onclick="copyTpl('tplIg', this)">Copy</button>
                </div>
                <div class="tpl-body">
                    <div class="tpl-variants" id="igVariants">
                        <button class="variant-btn active" onclick="setIgVariant(0, this)">Variasi 1</button>
                        <button class="variant-btn" onclick="setIgVariant(1, this)">Variasi 2</button>
                        <button class="variant-btn" onclick="setIgVariant(2, this)">Variasi 3</button>
                    </div>
                    <textarea class="tpl-textarea" id="tplIg"></textarea>
                </div>
            </div>

            {{-- YouTube Description --}}
            <div class="tpl-card">
                <div class="tpl-head">
                    <div class="tpl-title">â–¶ï¸ YouTube Description</div>
                    <button class="btn-copy" onclick="copyTpl('tplYt', this)">Copy</button>
                </div>
                <div class="tpl-body">
                    <textarea class="tpl-textarea" id="tplYt" style="min-height:160px;"></textarea>
                </div>
            </div>

            {{-- Spotify Pitch --}}
            <div class="tpl-card">
                <div class="tpl-head">
                    <div class="tpl-title">ðŸŽ§ Spotify Playlist Pitch <small>untuk kurator</small></div>
                    <button class="btn-copy" onclick="copyTpl('tplSpotify', this)">Copy</button>
                </div>
                <div class="tpl-body">
                    <textarea class="tpl-textarea" id="tplSpotify" style="min-height:150px;"></textarea>
                </div>
            </div>

            {{-- Discord Announcement --}}
            <div class="tpl-card">
                <div class="tpl-head">
                    <div class="tpl-title">ðŸ’¬ Discord Announcement</div>
                    <button class="btn-copy" onclick="copyTpl('tplDiscord', this)">Copy</button>
                </div>
                <div class="tpl-body">
                    <textarea class="tpl-textarea" id="tplDiscord"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast">Tersalin!</div>

@php
    $songsData = $songs->map(function($s) {
        return [
            'id'         => $s->id,
            'title'      => $s->title,
            'era'        => $s->era,
            'hook'       => $s->story_hook,
            'desc'       => $s->description,
            'spotify'    => $s->spotify_url,
            'apple'      => $s->apple_music_url,
            'youtube_id' => $s->youtube_id,
            'key'        => $s->key_signature,
            'tempo'      => $s->tempo,
        ];
    })->values();
@endphp
<script>
var SONGS = {!! json_encode($songsData, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!};

var SITE = 'margonoandi.my.id';
var igVariants = ['', '', ''];

function songById(id) { return SONGS.find(function(s){ return s.id === id; }); }

function selectSong(id, el) {
    document.querySelectorAll('.song-option').forEach(function(o){ o.classList.remove('selected'); });
    if (el) el.classList.add('selected');
    var s = songById(id);
    if (!s) return;

    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('templates').style.display = 'block';

    var hook  = (s.hook && s.hook.trim()) ? s.hook.trim() : (s.desc || '').trim();
    var desc  = (s.desc || '').trim();
    var yt    = 'https://youtu.be/' + s.youtube_id;
    var listen = s.spotify ? s.spotify : yt;
    var eraTag = s.era ? '#' + s.era.replace(/[^a-zA-Z0-9]/g, '') : '';
    var tags  = ['#margonoandi', '#musisiindie', '#laguindonesia', '#singersongwriter'];
    if (eraTag) tags.push(eraTag);
    var tagLine = tags.join(' ');

    // TikTok / Reels
    document.getElementById('tplTiktok').value =
        'ðŸŽµ "' + s.title + '"\n\n' +
        (hook ? '"' + hook + '"\n\n' : '') +
        'Lagu yang mungkin kamu butuhin hari ini. ðŸŽ¸\n' +
        'Dengerin full-nya, link di bio ðŸ‘†\n\n' +
        tagLine;

    // Instagram (3 variasi)
    igVariants[0] =
        'ðŸŽµ "' + s.title + '"' + (s.era ? ' â€” era ' + s.era : '') + '\n\n' +
        (hook ? hook + '\n\n' : '') +
        'Sekarang bisa kamu dengerin di semua platform.\n' +
        'Link lengkap di bio. Ceritakan, lagu ini ngingetin kamu sama siapa? ðŸ’¬\n\n' + tagLine;
    igVariants[1] =
        '"' + s.title + '" ðŸŽ¶\n\n' +
        (desc ? desc + '\n\n' : (hook ? hook + '\n\n' : '')) +
        'Dengerin â†’ ' + listen + '\n\n' + tagLine;
    igVariants[2] =
        'Behind the song: "' + s.title + '" ðŸŽ™ï¸\n\n' +
        (s.era ? 'Lahir di era ' + s.era + '. ' : '') +
        (hook ? hook + '\n\n' : '\n') +
        'Full version di bio. Save & share kalau relate ya. ðŸ¤\n\n' + tagLine;
    document.getElementById('tplIg').value = igVariants[0];
    document.querySelectorAll('#igVariants .variant-btn').forEach(function(b,i){ b.classList.toggle('active', i===0); });

    // YouTube Description
    document.getElementById('tplYt').value =
        s.title + ' â€” Margonoandi (Official Audio)\n\n' +
        (hook ? hook + '\n\n' : '') +
        (desc ? desc + '\n\n' : '') +
        'ðŸŽ§ Dengarkan di semua platform:\n' +
        (s.spotify ? 'Spotify: ' + s.spotify + '\n' : '') +
        (s.apple ? 'Apple Music: ' + s.apple + '\n' : '') +
        'Website & lirik: ' + SITE + '\n\n' +
        'Gabung komunitas: ' + SITE + '\n\n' +
        tagLine;

    // Spotify Pitch (untuk kurator)
    document.getElementById('tplSpotify').value =
        'Halo,\n\n' +
        'Saya Margonoandi, songwriter independen dari Indonesia. ' +
        'Saya ingin submit lagu "' + s.title + '"' +
        (s.key || s.tempo ? ' (' + [s.key ? 'key ' + s.key : '', s.tempo ? s.tempo + ' BPM' : ''].filter(Boolean).join(', ') + ')' : '') + '.\n\n' +
        (hook ? hook + '\n\n' : '') +
        'Cocok untuk playlist bertema akustik/indie/galau Indonesia. ' +
        'Link: ' + listen + '\n\n' +
        'Terima kasih atas waktunya.\nâ€” Margonoandi (' + SITE + ')';

    // Discord
    document.getElementById('tplDiscord').value =
        '@everyone ðŸŽµ Lagu baru: **' + s.title + '**\n\n' +
        (hook ? '> ' + hook + '\n\n' : '') +
        'Dengerin sekarang ðŸ‘‰ ' + listen + '\n' +
        'Ngobrolin lagunya di sini ya! Apa bagian favoritmu? ðŸ’¬';
}

function setIgVariant(i, btn) {
    document.getElementById('tplIg').value = igVariants[i];
    document.querySelectorAll('#igVariants .variant-btn').forEach(function(b){ b.classList.remove('active'); });
    if (btn) btn.classList.add('active');
}

function copyTpl(id, btn) {
    var ta = document.getElementById(id);
    navigator.clipboard.writeText(ta.value).then(function(){
        var old = btn.textContent;
        btn.textContent = 'âœ“ Tersalin'; btn.classList.add('done');
        showToast();
        setTimeout(function(){ btn.textContent = old; btn.classList.remove('done'); }, 1600);
    });
}

function showToast() {
    var t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 1600);
}
</script>

@endsection

