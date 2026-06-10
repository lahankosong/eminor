@extends('layouts.app')

@push('styles')
<style>
    .agent-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .agent-header h2 { font-size: 1rem; font-weight: 500; color: var(--text); }
    .agent-header p  { font-size: 12px; color: var(--text-3); margin-top: 2px; }

    .agent-layout {
        display: grid; grid-template-columns: 300px 1fr;
        gap: 2rem; align-items: start;
    }

    /* SONG SELECTOR */
    .song-selector {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 12px; overflow: hidden; position: sticky; top: 80px;
    }
    .selector-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
        font-size: 11px; color: var(--text-3); letter-spacing: 0.15em; text-transform: uppercase;
    }
    .song-list { max-height: 70vh; overflow-y: auto; }
    .song-option {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 1.25rem; cursor: pointer; transition: 0.15s;
        border-bottom: 1px solid var(--border-2);
    }
    .song-option:hover { background: var(--card-bg); }
    .song-option.selected { background: var(--card-bg); border-left: 2px solid var(--accent); }
    .song-option-thumb {
        width: 44px; height: 28px; object-fit: cover;
        border-radius: 4px; background: var(--bg-3); flex-shrink: 0;
    }
    .song-option-info { min-width: 0; }
    .song-option-title { font-size: 12px; color: var(--text-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .song-option-era   { font-size: 10px; color: var(--text-3); margin-top: 1px; }

    /* MAIN AREA */
    .agent-main { min-width: 0; }

    /* SELECTED SONG INFO */
    .selected-song-card {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 12px; padding: 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .selected-thumb {
        width: 80px; height: 50px; object-fit: cover;
        border-radius: 6px; background: var(--bg-3); flex-shrink: 0;
    }
    .selected-title { font-size: 15px; font-weight: 500; color: var(--text); }
    .selected-meta  { font-size: 12px; color: var(--text-3); margin-top: 3px; }

    /* GENERATE BUTTON */
    .generate-section {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 2rem;
    }
    .btn-generate {
        padding: 11px 28px; border-radius: 50px; font-size: 13px;
        font-weight: 500; background: var(--text); color: var(--bg);
        border: none; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-generate:hover { filter: brightness(0.88); }
    .btn-generate:disabled { background: var(--bg-3); color: var(--text-3); cursor: not-allowed; }
    .generate-hint { font-size: 12px; color: var(--text-3); }

    /* LOADING */
    .loading-state {
        display: none; text-align: center; padding: 3rem;
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 12px;
    }
    .loading-state.visible { display: block; }
    .loading-dots { display: flex; justify-content: center; gap: 6px; margin-bottom: 1rem; }
    .loading-dot {
        width: 8px; height: 8px; border-radius: 50%; background: var(--bg-4);
        animation: dotPulse 1.4s ease-in-out infinite;
    }
    .loading-dot:nth-child(2) { animation-delay: 0.2s; }
    .loading-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes dotPulse {
        0%, 80%, 100% { background: var(--bg-4); transform: scale(0.8); }
        40% { background: var(--accent); transform: scale(1.2); }
    }
    .loading-text    { font-size: 13px; color: var(--text-3); }
    .loading-subtext { font-size: 11px; color: var(--text-3); margin-top: 4px; opacity: 0.6; }

    /* RESULTS */
    .results-area { display: none; }
    .results-area.visible { display: block; }

    .result-section {
        background: var(--bg-2); border: 1px solid var(--border);
        border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
    }
    .result-section-title {
        font-size: 10px; letter-spacing: 0.2em; color: var(--text-3);
        text-transform: uppercase; margin-bottom: 1rem;
        padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-2);
        display: flex; align-items: center; justify-content: space-between;
    }
    .result-section-title span { font-size: 14px; }

    /* TOPIC TABS */
    .topic-tabs {
        display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1rem;
    }
    .topic-tab {
        padding: 6px 14px; border-radius: 20px; font-size: 11px;
        background: var(--bg-3); border: 1px solid var(--border);
        color: var(--text-2); cursor: pointer; transition: 0.15s;
    }
    .topic-tab:hover { border-color: var(--text-3); color: var(--text); }
    .topic-tab.active { background: var(--text); color: var(--bg); border-color: var(--text); }

    /* VARIATION BUTTONS */
    .variation-buttons { display: flex; gap: 8px; margin-bottom: 1rem; }
    .variation-btn {
        padding: 5px 12px; border-radius: 16px; font-size: 10px;
        background: var(--bg-3); border: 1px solid var(--border); color: var(--text-3);
        cursor: pointer; transition: 0.15s;
    }
    .variation-btn.active { background: var(--bg-4); border-color: var(--accent); color: var(--text); }

    /* CAPTION LINES */
    .caption-lines { background: var(--bg-3); border-radius: 8px; padding: 16px; }
    .caption-line  { font-size: 13px; color: var(--text-2); margin-bottom: 8px; }
    .caption-punchline {
        padding-left: 20px; border-left: 2px solid var(--accent);
        font-weight: 500; margin-top: 8px;
    }

    /* SCENE LIST */
    .scene-list { display: flex; flex-direction: column; gap: 10px; }
    .scene-card {
        background: var(--bg-3); border-left: 2px solid var(--accent);
        padding: 10px 12px; border-radius: 6px;
    }
    .scene-duration { font-size: 10px; color: var(--accent); font-family: monospace; margin-bottom: 6px; }
    .scene-desc { font-size: 11px; color: var(--text-2); margin-top: 4px; line-height: 1.4; }
    .scene-desc strong { color: var(--text-3); }

    /* DREAMINA PROMPT */
    .dreamina-prompt-box {
        background: var(--bg-3); border: 1px solid var(--accent-dim);
        border-radius: 8px; padding: 12px; margin-top: 1rem;
    }
    .dreamina-prompt-text {
        font-size: 11px; color: var(--text-2); font-family: monospace;
        word-break: break-word; white-space: pre-wrap;
    }

    /* DESCRIPTION */
    .desc-box {
        background: var(--bg-3); border: 1px solid var(--border);
        border-radius: 8px; padding: 12px;
        font-size: 13px; color: var(--text-2); line-height: 1.6;
    }
    .hashtags {
        background: var(--bg-3); border: 1px solid var(--border);
        border-radius: 8px; padding: 12px;
        font-size: 12px; color: var(--accent); margin-top: 10px;
    }

    /* ERROR & EMPTY */
    .error-box {
        background: #2e0d0d; color: #f87171; border: 1px solid #991b1b;
        padding: 12px 16px; border-radius: 8px; font-size: 13px;
        display: none; margin-bottom: 1rem;
    }
    .error-box.visible { display: block; }
    .empty-state {
        text-align: center; padding: 4rem 2rem;
        background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px;
    }
    .empty-state p { font-size: 14px; color: var(--text-3); }

    /* TOAST */
    .toast {
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: var(--bg-4); color: var(--text); padding: 8px 16px; border-radius: 40px;
        font-size: 12px; z-index: 1000; opacity: 0; transition: 0.2s;
        pointer-events: none; border: 1px solid var(--border);
    }
    .toast.show { opacity: 1; }

    /* COPY BUTTON */
    .hook-copy {
        padding: 5px 12px; border-radius: 6px; font-size: 11px;
        border: 1px solid var(--accent-dim); color: var(--accent); background: transparent;
        cursor: pointer; transition: 0.15s;
    }
    .hook-copy:hover { background: var(--accent-dim); }

    @media (max-width: 768px) {
        .agent-layout { grid-template-columns: 1fr; }
        .song-selector { position: static; }
    }
</style>
@endpush

@section('content')
<div class="agent-header">
    <div>
        <h2>AI Content Agent — Multi-Scene Video Generator</h2>
        <p>Generate 5 topik × 3 variasi × 4 adegan = 60 konten per lagu</p>
    </div>
    <a href="{{ route('admin.index') }}" style="font-size:12px;color:var(--text-2);text-decoration:none;border:1px solid var(--border);padding:6px 14px;border-radius:8px;">← Panel Admin</a>
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
                        <div style="font-size:10px;color:var(--text-3);margin-bottom:6px;">🎨 COPY-PASTE KE DREAMINA:</div>
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
            linesHtml += `<div class="caption-line${isPunchline ? ' caption-punchline' : ''}">${isPunchline ? '✨ ' : ''}${escapeHtml(line)}</div>`;
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
