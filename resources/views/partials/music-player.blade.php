@php
    $playerSongs = \App\Models\Song::whereNotNull('audio_file')
        ->where('audio_file', '!=', '')
        ->where('is_active', true)
        ->orderBy('track_number')
        ->get(['id', 'title', 'era', 'audio_file', 'youtube_id', 'slug']);
@endphp

@if($playerSongs->count() > 0)

{{-- ===== DESKTOP SIDEBAR PLAYER ===== --}}
<div class="Ekosistem-player-sidebar" id="fpsidebarPlayer">
    <div class="fps-header">
        <span class="fps-title">&#9834; EMINOR</span>
        <button class="fps-toggle" onclick="toggleSidebarPlayer()" title="Sembunyikan">&#8722;</button>
    </div>

    {{-- Now playing --}}
    <div class="fps-now-playing">
        <div class="fps-thumb" id="fpThumb">
            <img id="fpThumbImg" src="" alt="">
            <div class="fps-thumb-overlay">&#9654;</div>
        </div>
        <div class="fps-now-info">
            <div class="fps-now-title" id="fpNowTitle">Pilih lagu</div>
            <div class="fps-now-era"   id="fpNowEra">EMINOR</div>
        </div>
    </div>

    {{-- Progress --}}
    <div class="fps-progress-wrap" onclick="fpSeek(event)" id="fpProgressWrap">
        <div class="fps-progress-bar">
            <div class="fps-progress-fill" id="fpProgressFill"></div>
        </div>
    </div>
    <div class="fps-time-row">
        <span id="fpCurTime">0:00</span>
        <span id="fpDurTime">0:00</span>
    </div>

    {{-- Controls --}}
    <div class="fps-controls">
        <button class="fps-ctrl-btn" onclick="fpPrev()" title="Sebelumnya">&#9664;&#9664;</button>
        <button class="fps-ctrl-btn fps-play-btn" onclick="fpTogglePlay()" id="fpPlayBtn">
            <span id="fpPlayIcon">&#9654;</span>
        </button>
        <button class="fps-ctrl-btn" onclick="fpNext()" title="Berikutnya">&#9654;&#9654;</button>
        <button class="fps-ctrl-btn fps-vol-btn" onclick="fpToggleMute()" id="fpVolBtn" title="Mute">&#128266;</button>
    </div>

    {{-- Playlist --}}
    <div class="fps-playlist" id="fpPlaylist">
        @foreach($playerSongs as $i => $s)
        <div class="fps-track {{ $i === 0 ? 'active' : '' }}"
             id="fpTrack{{ $i }}"
             onclick="fpPlayTrack({{ $i }})">
            <div class="fps-track-num" id="fpTrackNum{{ $i }}">{{ $i + 1 }}</div>
            <div class="fps-track-info">
                <div class="fps-track-title">{{ $s->title }}</div>
                <div class="fps-track-era">{{ $s->era ?? 'EMINOR' }}</div>
            </div>
            <span class="fps-track-icon" id="fpTrackIcon{{ $i }}">&#9654;</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Collapsed tab --}}
<div class="Ekosistem-player-tab" id="fpTab" onclick="toggleSidebarPlayer()" style="display:none;">
    <span>&#9834;</span>
</div>

{{-- ===== MOBILE STICKY PLAYER ===== --}}
<div class="Ekosistem-player-mobile" id="fpMobilePlayer">
    <div class="fpm-inner" onclick="fpExpandMobile()">
        <img class="fpm-thumb" id="fpmThumb" src="" alt="">
        <div class="fpm-info">
            <div class="fpm-title" id="fpmTitle">Pilih lagu</div>
            <div class="fpm-progress">
                <div class="fpm-progress-fill" id="fpmFill"></div>
            </div>
        </div>
        <div class="fpm-controls" onclick="event.stopPropagation()">
            <button class="fpm-btn" onclick="fpPrev()">&#9664;</button>
            <button class="fpm-btn fpm-play" onclick="fpTogglePlay()" id="fpmPlayBtn">&#9654;</button>
            <button class="fpm-btn" onclick="fpNext()">&#9654;&#9654;</button>
        </div>
    </div>
</div>

{{-- ===== MOBILE EXPANDED PLAYER ===== --}}
<div class="Ekosistem-player-expanded" id="fpExpanded">
    <div class="fpe-handle" onclick="fpCollapseMobile()">
        <div class="fpe-handle-bar"></div>
    </div>
    <img class="fpe-cover" id="fpeThumb" src="" alt="">
    <div class="fpe-title"  id="fpeTitle">—</div>
    <div class="fpe-era"    id="fpeEra">EMINOR</div>

    <div class="fpe-progress-wrap" onclick="fpSeekMobile(event)" id="fpeProgressWrap">
        <div class="fpe-progress-bar">
            <div class="fpe-progress-fill" id="fpeFill"></div>
        </div>
    </div>
    <div class="fpe-time-row">
        <span id="fpeCur">0:00</span>
        <span id="fpeDur">0:00</span>
    </div>

    <div class="fpe-controls">
        <button class="fpe-btn" onclick="fpPrev()">&#9664;&#9664;</button>
        <button class="fpe-btn fpe-play" onclick="fpTogglePlay()" id="fpePlayBtn">&#9654;</button>
        <button class="fpe-btn" onclick="fpNext()">&#9654;&#9654;</button>
    </div>

    <div class="fpe-playlist">
        @foreach($playerSongs as $i => $s)
        <div class="fpe-track {{ $i === 0 ? 'active' : '' }}"
             id="fpeTrack{{ $i }}"
             onclick="fpPlayTrack({{ $i }})">
            <div class="fpe-track-num">{{ $i + 1 }}</div>
            <div class="fpe-track-info">
                <div class="fpe-track-title">{{ $s->title }}</div>
                <div class="fpe-track-era">{{ $s->era ?? '' }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<div class="fpe-overlay" id="fpeOverlay" onclick="fpCollapseMobile()"></div>

{{-- Hidden audio element --}}
<audio id="fpAudio" preload="none"></audio>

<style>
/* ===== DESKTOP SIDEBAR PLAYER ===== */
.Ekosistem-player-sidebar {
    position: fixed; right: 16px; bottom: 16px;
    width: 240px; background: #0a0a0a;
    border: 1px solid #1a1a1a; border-radius: 14px;
    z-index: 400; overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.6);
    transition: transform 0.3s ease;
}
.fps-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 12px 8px;
    border-bottom: 1px solid #111;
}
.fps-title { font-size: 11px; color: #444; letter-spacing: 0.15em; text-transform: uppercase; }
.fps-toggle {
    background: transparent; border: none; color: #333;
    font-size: 16px; cursor: pointer; padding: 0 2px; transition: 0.15s; line-height: 1;
}
.fps-toggle:hover { color: #888; }

.fps-now-playing {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px;
}
.fps-thumb {
    width: 44px; height: 44px; border-radius: 8px;
    background: #111; flex-shrink: 0; position: relative; overflow: hidden;
}
.fps-thumb img { width: 100%; height: 100%; object-fit: cover; }
.fps-thumb-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: #fff; opacity: 0; transition: 0.2s;
}
.fps-thumb:hover .fps-thumb-overlay { opacity: 1; }
.fps-now-title { font-size: 12px; font-weight: 500; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fps-now-era   { font-size: 10px; color: #444; margin-top: 2px; }
.fps-now-info  { min-width: 0; flex: 1; }

.fps-progress-wrap {
    padding: 0 12px; cursor: pointer;
}
.fps-progress-bar {
    height: 3px; background: #1a1a1a; border-radius: 2px; overflow: hidden;
}
.fps-progress-fill {
    height: 100%; width: 0%; background: #fff; border-radius: 2px; transition: none;
}
.fps-time-row {
    display: flex; justify-content: space-between;
    padding: 4px 12px 8px; font-size: 10px; color: #333;
}

.fps-controls {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; padding: 6px 12px 10px;
}
.fps-ctrl-btn {
    background: transparent; border: none; color: #555;
    font-size: 13px; cursor: pointer; padding: 4px; transition: 0.15s;
}
.fps-ctrl-btn:hover { color: #fff; }
.fps-play-btn {
    width: 34px; height: 34px; border-radius: 50% !important;
    background: #fff !important; color: #000 !important;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; border: none; cursor: pointer; transition: 0.2s;
}
.fps-play-btn:hover { background: #ddd !important; }
.fps-vol-btn { margin-left: 4px; }

.fps-playlist {
    max-height: 180px; overflow-y: auto; border-top: 1px solid #111;
    scrollbar-width: thin; scrollbar-color: #1a1a1a transparent;
}
.fps-track {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px; cursor: pointer; transition: 0.12s;
}
.fps-track:hover { background: #111; }
.fps-track.active { background: #0d0d0d; }
.fps-track-num {
    width: 20px; height: 20px; border-radius: 4px;
    background: #111; display: flex; align-items: center; justify-content: center;
    font-size: 9px; color: #444; flex-shrink: 0; font-weight: 600;
}
.fps-track.active .fps-track-num { background: #1a2a1a; color: #4ade80; }
.fps-track-info { flex: 1; min-width: 0; }
.fps-track-title { font-size: 11px; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fps-track.active .fps-track-title { color: #fff; }
.fps-track-era  { font-size: 10px; color: #333; }
.fps-track-icon { font-size: 8px; color: #2a2a2a; flex-shrink: 0; }
.fps-track.active .fps-track-icon { color: #4ade80; }

.Ekosistem-player-tab {
    position: fixed; right: 16px; bottom: 80px;
    width: 40px; height: 40px; border-radius: 50%;
    background: #0a0a0a; border: 1px solid #1a1a1a;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #555; cursor: pointer; z-index: 400;
    box-shadow: 0 4px 16px rgba(0,0,0,0.5); transition: 0.2s;
}
.Ekosistem-player-tab:hover { color: #fff; border-color: #333; }

/* ===== MOBILE STICKY PLAYER ===== */
.Ekosistem-player-mobile {
    display: none;
    position: fixed; bottom: 60px; left: 0; right: 0;
    background: rgba(8,8,8,0.97); backdrop-filter: blur(12px);
    border-top: 1px solid #1a1a1a; z-index: 350;
}
.fpm-inner {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 12px; cursor: pointer;
}
.fpm-thumb {
    width: 36px; height: 36px; border-radius: 6px;
    object-fit: cover; background: #111; flex-shrink: 0;
}
.fpm-info { flex: 1; min-width: 0; }
.fpm-title { font-size: 12px; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fpm-progress {
    height: 2px; background: #1a1a1a; border-radius: 2px; margin-top: 5px; overflow: hidden;
}
.fpm-progress-fill { height: 100%; width: 0%; background: #fff; border-radius: 2px; }
.fpm-controls { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.fpm-btn {
    background: transparent; border: none; color: #555;
    font-size: 13px; cursor: pointer; padding: 4px; transition: 0.15s;
}
.fpm-btn:hover { color: #fff; }
.fpm-play {
    width: 30px; height: 30px; border-radius: 50% !important;
    background: #fff !important; color: #000 !important;
    display: flex; align-items: center; justify-content: center; font-size: 11px;
    border: none !important; cursor: pointer;
}

/* ===== MOBILE EXPANDED PLAYER ===== */
.Ekosistem-player-expanded {
    display: none; position: fixed; bottom: 0; left: 0; right: 0;
    background: #0a0a0a; border-top: 1px solid #1a1a1a;
    border-radius: 20px 20px 0 0; z-index: 600;
    padding: 0 1.25rem 1.5rem;
    max-height: 90vh; overflow-y: auto;
    transform: translateY(100%); transition: transform 0.35s ease;
}
.Ekosistem-player-expanded.open {
    display: block; transform: translateY(0);
}
.fpe-overlay {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6);
    z-index: 590;
}
.fpe-overlay.open { display: block; }

.fpe-handle { display: flex; justify-content: center; padding: 12px 0 8px; cursor: pointer; }
.fpe-handle-bar {
    width: 36px; height: 4px; border-radius: 2px; background: #2a2a2a;
}
.fpe-cover {
    width: 160px; height: 160px; object-fit: cover; border-radius: 12px;
    display: block; margin: 0 auto 1rem; background: #111;
}
.fpe-title { font-size: 1.1rem; font-weight: 500; text-align: center; margin-bottom: 4px; }
.fpe-era   { font-size: 12px; color: #555; text-align: center; margin-bottom: 1.25rem; }

.fpe-progress-wrap { cursor: pointer; margin-bottom: 4px; }
.fpe-progress-bar  { height: 4px; background: #1a1a1a; border-radius: 2px; overflow: hidden; }
.fpe-progress-fill { height: 100%; width: 0%; background: #fff; border-radius: 2px; }
.fpe-time-row      { display: flex; justify-content: space-between; font-size: 10px; color: #333; margin-bottom: 1.25rem; }

.fpe-controls {
    display: flex; align-items: center; justify-content: center;
    gap: 20px; margin-bottom: 1.5rem;
}
.fpe-btn {
    background: transparent; border: none; color: #555;
    font-size: 18px; cursor: pointer; padding: 6px; transition: 0.15s;
}
.fpe-btn:hover { color: #fff; }
.fpe-play {
    width: 52px; height: 52px; border-radius: 50% !important;
    background: #fff !important; color: #000 !important;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; border: none !important; cursor: pointer;
}

.fpe-playlist {
    border-top: 1px solid #111; padding-top: 1rem;
}
.fpe-track {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 4px; cursor: pointer; border-radius: 8px; transition: 0.12s;
}
.fpe-track:hover { background: #111; padding-left: 8px; }
.fpe-track.active { background: #0d0d0d; }
.fpe-track-num  { font-size: 12px; color: #333; min-width: 24px; text-align: center; }
.fpe-track.active .fpe-track-num { color: #4ade80; }
.fpe-track-title { font-size: 13px; color: #888; }
.fpe-track.active .fpe-track-title { color: #fff; }
.fpe-track-era  { font-size: 11px; color: #333; }

/* Show/hide by screen */
@media (min-width: 769px) {
    .Ekosistem-player-mobile { display: none !important; }
    .Ekosistem-player-expanded { display: none !important; }
    .fpe-overlay { display: none !important; }
}
@media (max-width: 768px) {
    .Ekosistem-player-sidebar { display: none !important; }
    .Ekosistem-player-tab     { display: none !important; }
    .Ekosistem-player-mobile  { display: block; }
}
</style>

<script>
@php
$tracksJs = $playerSongs->map(function($s) {
    return [
        'title'     => $s->title,
        'era'       => $s->era ?? 'EMINOR',
        'audio'     => asset($s->audio_file),
        'thumb'     => 'https://img.youtube.com/vi/' . $s->youtube_id . '/mqdefault.jpg',
        'slug'      => $s->slug,
    ];
});
@endphp
var fpTracks  = @json($tracksJs);
var fpTotal   = fpTracks.length;
var fpCurrent = 0;
var fpPlaying = false;
var fpMuted   = false;

var fpAudio   = document.getElementById('fpAudio');

function fpPlayTrack(index) {
    fpCurrent = index;
    var t = fpTracks[index];

    fpAudio.src = t.audio;
    fpAudio.play().then(function() {
        fpPlaying = true;
        fpUpdateUI();
    }).catch(function() {
        fpPlaying = false;
    });

    // Update all UIs
    fpUpdateAllTrackRows(index);
}

function fpUpdateUI() {
    var t     = fpTracks[fpCurrent];
    var thumb = t.thumb;

    // Sidebar
    var img = document.getElementById('fpThumbImg');
    if (img) img.src = thumb;
    var nt = document.getElementById('fpNowTitle');
    if (nt) nt.textContent = t.title;
    var ne = document.getElementById('fpNowEra');
    if (ne) ne.textContent = t.era;

    // Play buttons
    var icon = document.getElementById('fpPlayIcon');
    if (icon) icon.innerHTML = fpPlaying ? '&#9646;&#9646;' : '&#9654;';

    // Mobile sticky
    var mthumb = document.getElementById('fpmThumb');
    if (mthumb) mthumb.src = thumb;
    var mt = document.getElementById('fpmTitle');
    if (mt) mt.textContent = t.title;
    var mplay = document.getElementById('fpmPlayBtn');
    if (mplay) mplay.innerHTML = fpPlaying ? '&#9646;&#9646;' : '&#9654;';

    // Mobile expanded
    var ethumb = document.getElementById('fpeThumb');
    if (ethumb) ethumb.src = thumb;
    var etitle = document.getElementById('fpeTitle');
    if (etitle) etitle.textContent = t.title;
    var eera = document.getElementById('fpeEra');
    if (eera) eera.textContent = t.era;
    var eplay = document.getElementById('fpePlayBtn');
    if (eplay) eplay.innerHTML = fpPlaying ? '&#9646;&#9646;' : '&#9654;';
}

function fpUpdateAllTrackRows(activeIdx) {
    for (var i = 0; i < fpTotal; i++) {
        // Sidebar
        var tr = document.getElementById('fpTrack' + i);
        if (tr) tr.classList.toggle('active', i === activeIdx);
        // Expanded
        var er = document.getElementById('fpeTrack' + i);
        if (er) er.classList.toggle('active', i === activeIdx);
    }
}

function fpTogglePlay() {
    if (!fpAudio.src || fpAudio.src === window.location.href) {
        fpPlayTrack(0); return;
    }
    if (fpPlaying) {
        fpAudio.pause();
        fpPlaying = false;
    } else {
        fpAudio.play();
        fpPlaying = true;
    }
    fpUpdateUI();
}

function fpNext() {
    var next = (fpCurrent + 1) % fpTotal;
    fpPlayTrack(next);
}

function fpPrev() {
    if (fpAudio.currentTime > 3) {
        fpAudio.currentTime = 0; return;
    }
    var prev = (fpCurrent - 1 + fpTotal) % fpTotal;
    fpPlayTrack(prev);
}

function fpToggleMute() {
    fpMuted = !fpMuted;
    fpAudio.muted = fpMuted;
    var btn = document.getElementById('fpVolBtn');
    if (btn) btn.innerHTML = fpMuted ? '&#128263;' : '&#128266;';
}

function fpSeek(e) {
    var rect = document.getElementById('fpProgressWrap').getBoundingClientRect();
    var pct  = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
    if (fpAudio.duration) fpAudio.currentTime = pct * fpAudio.duration;
}

function fpSeekMobile(e) {
    var rect = document.getElementById('fpeProgressWrap').getBoundingClientRect();
    var pct  = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
    if (fpAudio.duration) fpAudio.currentTime = pct * fpAudio.duration;
}

function fpFmt(s) {
    if (!s || isNaN(s)) return '0:00';
    var m = Math.floor(s / 60), sec = Math.floor(s % 60);
    return m + ':' + (sec < 10 ? '0' : '') + sec;
}

// Audio events
fpAudio.addEventListener('timeupdate', function() {
    if (!fpAudio.duration) return;
    var pct = (fpAudio.currentTime / fpAudio.duration * 100).toFixed(2) + '%';

    var fill = document.getElementById('fpProgressFill');
    if (fill) fill.style.width = pct;
    var cur = document.getElementById('fpCurTime');
    if (cur) cur.textContent = fpFmt(fpAudio.currentTime);

    // Mobile
    var mfill = document.getElementById('fpmFill');
    if (mfill) mfill.style.width = pct;
    var efill = document.getElementById('fpeFill');
    if (efill) efill.style.width = pct;
    var ecur = document.getElementById('fpeCur');
    if (ecur) ecur.textContent = fpFmt(fpAudio.currentTime);
});

fpAudio.addEventListener('loadedmetadata', function() {
    var dur = document.getElementById('fpDurTime');
    if (dur) dur.textContent = fpFmt(fpAudio.duration);
    var edur = document.getElementById('fpeDur');
    if (edur) edur.textContent = fpFmt(fpAudio.duration);
});

fpAudio.addEventListener('ended', fpNext);

// Sidebar toggle
function toggleSidebarPlayer() {
    var sidebar = document.getElementById('fpsidebarPlayer');
    var tab     = document.getElementById('fpTab');
    if (sidebar.style.display === 'none') {
        sidebar.style.display = 'block';
        tab.style.display = 'none';
    } else {
        sidebar.style.display = 'none';
        tab.style.display = 'flex';
    }
}

// Mobile expand/collapse
function fpExpandMobile() {
    document.getElementById('fpExpanded').classList.add('open');
    document.getElementById('fpeOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fpCollapseMobile() {
    document.getElementById('fpExpanded').classList.remove('open');
    document.getElementById('fpeOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

// Init first track thumbnail
document.addEventListener('DOMContentLoaded', function() {
    if (fpTracks.length > 0) {
        var t = fpTracks[0];
        var img = document.getElementById('fpThumbImg');
        if (img) img.src = t.thumb;
        var mt = document.getElementById('fpmThumb');
        if (mt) mt.src = t.thumb;
        var et = document.getElementById('fpeThumb');
        if (et) et.src = t.thumb;
        var nt = document.getElementById('fpNowTitle');
        if (nt) nt.textContent = t.title;
        var ne = document.getElementById('fpNowEra');
        if (ne) ne.textContent = t.era;
        var mt2 = document.getElementById('fpmTitle');
        if (mt2) mt2.textContent = t.title;
        var et2 = document.getElementById('fpeTitle');
        if (et2) et2.textContent = t.title;
        var ee  = document.getElementById('fpeEra');
        if (ee)  ee.textContent  = t.era;
    }
});
</script>

@endif