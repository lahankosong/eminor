@extends('layouts.app')

@push('styles')
<style>
    .agent-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid #1a1a1a;
    }
    .agent-header h2 { font-size: 1rem; font-weight: 500; }
    .agent-header p  { font-size: 12px; color: #555; margin-top: 2px; }

    .agent-layout {
        display: grid; grid-template-columns: 300px 1fr;
        gap: 2rem; align-items: start;
    }

    /* SONG SELECTOR */
    .song-selector {
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 12px; overflow: hidden; position: sticky; top: 80px;
    }
    .selector-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #141414;
        font-size: 11px; color: #444; letter-spacing: 0.15em; text-transform: uppercase;
    }
    .song-list { max-height: 70vh; overflow-y: auto; }
    .song-option {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 1.25rem; cursor: pointer; transition: 0.15s;
        border-bottom: 1px solid #0d0d0d;
    }
    .song-option:hover { background: #111; }
    .song-option.selected { background: #111; border-left: 2px solid #fff; }
    .song-option-thumb {
        width: 44px; height: 28px; object-fit: cover;
        border-radius: 4px; background: #111; flex-shrink: 0;
    }
    .song-option-info { min-width: 0; }
    .song-option-title { font-size: 12px; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .song-option-era   { font-size: 10px; color: #444; margin-top: 1px; }

    /* MAIN AREA */
    .agent-main { min-width: 0; }

    /* SELECTED SONG INFO */
    .selected-song-card {
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 12px; padding: 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .selected-thumb {
        width: 80px; height: 50px; object-fit: cover;
        border-radius: 6px; background: #111; flex-shrink: 0;
    }
    .selected-title { font-size: 15px; font-weight: 500; color: #fff; }
    .selected-meta  { font-size: 12px; color: #555; margin-top: 3px; }

    /* GENERATE BUTTON */
    .generate-section {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 2rem;
    }
    .btn-generate {
        padding: 11px 28px; border-radius: 50px; font-size: 13px;
        font-weight: 500; background: #fff; color: #000;
        border: none; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-generate:hover { background: #e0e0e0; }
    .btn-generate:disabled { background: #1a1a1a; color: #444; cursor: not-allowed; }
    .generate-hint { font-size: 12px; color: #333; }

    /* LOADING */
    .loading-state {
        display: none; text-align: center; padding: 3rem;
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 12px;
    }
    .loading-state.visible { display: block; }
    .loading-dots { display: flex; justify-content: center; gap: 6px; margin-bottom: 1rem; }
    .loading-dot {
        width: 8px; height: 8px; border-radius: 50%; background: #333;
        animation: dotPulse 1.4s ease-in-out infinite;
    }
    .loading-dot:nth-child(2) { animation-delay: 0.2s; }
    .loading-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes dotPulse {
        0%, 80%, 100% { background: #222; transform: scale(0.8); }
        40% { background: #60a5fa; transform: scale(1.2); }
    }
    .loading-text { font-size: 13px; color: #555; }
    .loading-subtext { font-size: 11px; color: #333; margin-top: 4px; }

    /* RESULTS */
    .results-area { display: none; }
    .results-area.visible { display: block; }

    .result-section {
        background: #0a0a0a; border: 1px solid #141414;
        border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
    }
    .result-section-title {
        font-size: 10px; letter-spacing: 0.2em; color: #444;
        text-transform: uppercase; margin-bottom: 1rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid #111;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .result-section-title span { font-size: 14px; }

    /* TOPIC TABS */
    .topic-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .topic-tab {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        background: #111;
        border: 1px solid #2a2a2a;
        color: #888;
        cursor: pointer;
        transition: 0.15s;
    }
    .topic-tab:hover { border-color: #444; color: #ccc; }
    .topic-tab.active { background: #fff; color: #000; border-color: #fff; }

    /* VARIATION BUTTONS */
    .variation-buttons {
        display: flex;
        gap: 8px;
        margin-bottom: 1rem;
    }
    .variation-btn {
        padding: 5px 12px;
        border-radius: 16px;
        font-size: 10px;
        background: #111;
        border: 1px solid #2a2a2a;
        color: #666;
        cursor: pointer;
    }
    .variation-btn.active { background: #1a1a1a; border-color: #fff; color: #fff; }

    /* CAPTION LINES */
    .caption-lines {
        background: #111;
        border-radius: 8px;
        padding: 16px;
    }
    .caption-line {
        font-size: 13px;
        color: #ccc;
        margin-bottom: 8px;
    }
    .caption-punchline {
        padding-left: 20px;
        border-left: 2px solid #fff;
        font-weight: 500;
        margin-top: 8px;
    }

    /* SCENE LIST */
    .scene-list { display: flex; flex-direction: column; gap: 10px; }
    .scene-card {
        background: #0d0d0d;
        border-left: 2px solid #60a5fa;
        padding: 10px 12px;
        border-radius: 6px;
    }
    .scene-duration { font-size: 10px; color: #60a5fa; font-family: monospace; margin-bottom: 6px; }
    .scene-desc { font-size: 11px; color: #888; margin-top: 4px; line-height: 1.4; }
    .scene-desc strong { color: #555; }

    /* DREAMINA PROMPT */
    .dreamina-prompt-box {
        background: #0a0a1a;
        border: 1px solid #1a1a3a;
        border-radius: 8px;
        padding: 12px;
        margin-top: 1rem;
    }
    .dreamina-prompt-text {
        font-size: 11px;
        color: #4a6fa5;
        font-family: monospace;
        word-break: break-word;
        white-space: pre-wrap;
    }

    /* DESCRIPTION */
    .desc-box {
        background: #111; border: 1px solid #1a1a1a;
        border-radius: 8px; padding: 12px;
        font-size: 13px; color: #aaa; line-height: 1.6;
    }
    .hashtags {
        background: #111; border: 1px solid #1a1a1a;
        border-radius: 8px; padding: 12px;
        font-size: 12px; color: #4a6fa5;
        margin-top: 10px;
    }

    /* SAVE SECTION */
    .save-section {
        background: #0a0a1a; border: 1px solid #1a1a3a;
        border-radius: 12px; padding: 1.25rem; margin-top: 1rem;
    }
    .save-section-title {
        font-size: 11px; color: #4a6fa5; letter-spacing: 0.15em;
        text-transform: uppercase; margin-bottom: 1rem;
    }
    .save-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 1rem; }
    .save-field label { font-size: 11px; color: #444; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
    .save-field input, .save-field textarea, .save-field select {
        width: 100%; background: #0d0d0d; border: 1px solid #2a2a2a;
        border-radius: 6px; color: #ccc; font-size: 12px;
        padding: 8px 10px; outline: none; font-family: inherit;
    }
    .save-field input:focus, .save-field textarea:focus { border-color: #444; }
    .save-field textarea { resize: vertical; min-height: 80px; line-height: 1.6; }
    .save-field.full { grid-column: 1 / -1; }
    .btn-save-content {
        padding: 9px 24px; border-radius: 8px; font-size: 13px;
        font-weight: 500; background: #fff; color: #000;
        border: none; cursor: pointer; transition: 0.2s;
    }
    .btn-save-content:hover { background: #ddd; }

    /* ERROR & EMPTY */
    .error-box {
        background: #2e0d0d; color: #f87171; border: 1px solid #991b1b;
        padding: 12px 16px; border-radius: 8px; font-size: 13px;
        display: none; margin-bottom: 1rem;
    }
    .error-box.visible { display: block; }
    .empty-state {
        text-align: center; padding: 4rem 2rem;
        background: #0a0a0a; border: 1px solid #141414; border-radius: 12px;
    }
    .empty-state p { font-size: 14px; color: #333; }

    /* TOAST */
    .toast {
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: #222; color: #fff; padding: 8px 16px; border-radius: 40px;
        font-size: 12px; z-index: 1000; opacity: 0; transition: 0.2s;
        pointer-events: none;
    }
    .toast.show { opacity: 1; }

    @media (max-width: 768px) {
        .agent-layout { grid-template-columns: 1fr; }
        .song-selector { position: static; }
        .save-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="agent-header">
    <div>
        <h2>AI Content Agent — Multi-Scene Video Generator</h2>
        <p>Generate 5 topik × 3 variasi × 4 adegan = 60 konten per lagu</p>
    </div>
    <a href="{{ route('admin.index') }}" style="font-size:12px;color:#555;text-decoration:none;border:1px solid #1a1a1a;padding:6px 14px;border-radius:8px;">← Panel Admin</a>
</div>

<div class="agent-layout">
    {{-- SONG SELECTOR --}}
    <div class="song-selector">
        <div class="selector-header">Pilih lagu</div>
        <div class="song-list">
            @foreach($songs as $song)
            <div class="song-option" onclick="selectSong({{ $song->id }}, '{{ addslashes($song->title) }}', '{{ $song->youtube_id }}', '{{ addslashes($song->era ?? '') }}', '{{ addslashes($song->key_signature ?? '') }}', {{ $song->lyrics ? 'true' : 'false' }})">
                <img src="https://img.youtube.com/vi/{{ $song->youtube_id }}/mqdefault.jpg" class="song-option-thumb">
                <div class="song-option-info">
                    <div class="song-option-title">{{ $song->title }}</div>
                    <div class="song-option-era">{{ $song->era ?? '—' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- MAIN AREA --}}
    <div class="agent-main">
        <div class="empty-state" id="emptyState">
            <p style="font-size:24px;margin-bottom:1rem;">🎵</p>
            <p>Pilih lagu dari daftar di sebelah kiri<br>untuk mulai generate konten.</p>
        </div>

        <div id="selectedSongCard" style="display:none;">
            <div class="selected-song-card">
                <img id="selThumb" src="" class="selected-thumb">
                <div>
                    <div class="selected-title" id="selTitle">—</div>
                    <div class="selected-meta" id="selMeta">—</div>
                </div>
            </div>

            <div class="generate-section">
                <button class="btn-generate" id="generateBtn" onclick="generateContent()">✨ Generate dengan AI</button>
                <span class="generate-hint">Claude AI · 5 topik × 15 naskah × 4 adegan</span>
            </div>

            <div class="error-box" id="errorBox"></div>

            <div class="loading-state" id="loadingState">
                <div class="loading-dots"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div>
                <div class="loading-text">AI sedang menganalisis lagu...</div>
            </div>

            <div class="results-area" id="resultsArea">
                <div class="result-section">
                    <div class="result-section-title">📌 Pilih Topik</div>
                    <div class="topic-tabs" id="topicTabs"></div>
                </div>

                <div id="selectedTopicInfo" style="display:none;">
                    <div class="result-section">
                        <div class="result-section-title">📝 Caption Overlay</div>
                        <div class="variation-buttons" id="variationButtons"></div>
                        <div id="captionLines"></div>
                    </div>

                    <div class="result-section">
                        <div class="result-section-title">🎬 Visual Sequence (4×5 detik = 20 detik)</div>
                        <div class="scene-list" id="sceneList"></div>
                    </div>

                    <div class="dreamina-prompt-box">
                        <div style="font-size:10px; color:#4a6fa5; margin-bottom:6px;">🎨 COPY-PASTE KE DREAMINA:</div>
                        <div id="dreaminaPrompt" class="dreamina-prompt-text"></div>
                        <button class="hook-copy" onclick="copyDreaminaPrompt()" style="margin-top:8px;">📋 Copy Prompt</button>
                    </div>
                </div>

                <div class="result-section">
                    <div class="result-section-title">📄 Deskripsi & Hashtag</div>
                    <div id="shortsDesc" class="desc-box" contenteditable="true"></div>
                    <div id="hashtags" class="hashtags" contenteditable="true"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast">Copied!</div>
@endsection

@push('scripts')
<script>
let currentSongId = null;
let currentData = null;
let currentTopicId = 1;
let currentVariationId = 1;
let topicsData = [], scriptsData = [], visualSequencesData = [], dreaminaPromptsData = [];

function selectSong(id, title, ytId, era, key, hasLyrics) {
    currentSongId = id;
    document.querySelectorAll('.song-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('selectedSongCard').style.display = 'block';
    document.getElementById('resultsArea').classList.remove('visible');
    document.getElementById('selectedTopicInfo').style.display = 'none';
    document.getElementById('errorBox').classList.remove('visible');
    document.getElementById('selThumb').src = `https://img.youtube.com/vi/${ytId}/mqdefault.jpg`;
    document.getElementById('selTitle').textContent = title;
    document.getElementById('selMeta').textContent = (era || 'Margonoandi') + (key ? ' · Key ' + key : '');
    document.getElementById('generateBtn').disabled = false;
}

function generateContent() {
    if (!currentSongId) return;
    document.getElementById('generateBtn').disabled = true;
    document.getElementById('loadingState').classList.add('visible');
    document.getElementById('resultsArea').classList.remove('visible');
    document.getElementById('errorBox').classList.remove('visible');
    
    fetch('{{ url("/admin/ai-agent/generate") }}/' + currentSongId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(res => {
        document.getElementById('loadingState').classList.remove('visible');
        document.getElementById('generateBtn').disabled = false;
        if (res.error) {
            document.getElementById('errorBox').textContent = 'Error: ' + res.error;
            document.getElementById('errorBox').classList.add('visible');
            return;
        }
        currentData = res.data;
        renderResults(currentData);
    })
    .catch(err => {
        document.getElementById('loadingState').classList.remove('visible');
        document.getElementById('generateBtn').disabled = false;
        document.getElementById('errorBox').textContent = 'Error: ' + err.message;
        document.getElementById('errorBox').classList.add('visible');
    });
}

function renderResults(data) {
    topicsData = data.topics || [];
    scriptsData = data.scripts || [];
    visualSequencesData = data.visual_sequences || [];
    dreaminaPromptsData = data.dreamina_prompts || [];
    
    let topicHtml = '';
    topicsData.forEach(topic => {
        topicHtml += `<div class="topic-tab" onclick="selectTopic(${topic.id})">${escapeHtml(topic.label)}</div>`;
    });
    document.getElementById('topicTabs').innerHTML = topicHtml;
    document.getElementById('shortsDesc').textContent = data.shorts_description || '';
    document.getElementById('hashtags').textContent = data.hashtags || '';
    
    if (topicsData.length) selectTopic(topicsData[0].id);
    document.getElementById('resultsArea').classList.add('visible');
}

function selectTopic(topicId) {
    currentTopicId = topicId;
    document.querySelectorAll('.topic-tab').forEach((tab, i) => {
        if (i + 1 === topicId) tab.classList.add('active');
        else tab.classList.remove('active');
    });
    
    const topicScript = scriptsData.find(s => s.topic_id === topicId);
    if (topicScript) {
        let varHtml = '';
        for (let i = 1; i <= 3; i++) {
            varHtml += `<button class="variation-btn" onclick="selectVariation(${i})">Variasi ${i}</button>`;
        }
        document.getElementById('variationButtons').innerHTML = varHtml;
        window.currentVariations = topicScript.variations;
        selectVariation(1);
    }
    
    const visualSeq = visualSequencesData.find(v => v.topic_id === topicId);
    if (visualSeq && visualSeq.scenes) {
        let sceneHtml = '';
        visualSeq.scenes.forEach(scene => {
            sceneHtml += `<div class="scene-card">
                <div class="scene-duration">🎬 ADEGAN ${scene.order} · ${scene.duration} DETIK</div>
                <div class="scene-desc"><strong>Visual:</strong> ${escapeHtml(scene.visual)}</div>
                <div class="scene-desc"><strong>Camera:</strong> ${escapeHtml(scene.camera)}</div>
                <div class="scene-desc"><strong>Action:</strong> ${escapeHtml(scene.action)}</div>
                <div class="scene-desc"><strong>Lighting:</strong> ${escapeHtml(scene.lighting)}</div>
                <div class="scene-desc"><strong>➡ Transisi:</strong> ${escapeHtml(scene.transition_to_next)}</div>
            </div>`;
        });
        document.getElementById('sceneList').innerHTML = sceneHtml;
    }
    
    const dreaminaPrompt = dreaminaPromptsData.find(p => p.topic_id === topicId);
    if (dreaminaPrompt) document.getElementById('dreaminaPrompt').textContent = dreaminaPrompt.prompt;
    
    document.getElementById('selectedTopicInfo').style.display = 'block';
}

function selectVariation(variationId) {
    currentVariationId = variationId;
    document.querySelectorAll('.variation-btn').forEach((btn, i) => {
        if (i + 1 === variationId) btn.classList.add('active');
        else btn.classList.remove('active');
    });
    
    const variation = window.currentVariations.find(v => v.v === variationId);
    if (variation && variation.lines) {
        let linesHtml = '<div class="caption-lines">';
        variation.lines.forEach((line, idx) => {
            const isPunchline = idx === 4;
            linesHtml += `<div class="caption-line" style="${isPunchline ? 'padding-left:20px; border-left:2px solid #fff; font-weight:500; margin-top:8px;' : ''}">${isPunchline ? '✨ ' : ''}${escapeHtml(line)}</div>`;
        });
        linesHtml += '</div>';
        document.getElementById('captionLines').innerHTML = linesHtml;
    }
}

function copyDreaminaPrompt() {
    const prompt = document.getElementById('dreaminaPrompt').textContent;
    navigator.clipboard.writeText(prompt);
    const toast = document.getElementById('toast');
    toast.textContent = '✅ Prompt Dreamina disalin!';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>
@endpush