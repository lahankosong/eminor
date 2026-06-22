@extends('layouts.admin')

@section('title', 'Analisis Komunitas')

@section('content')
@php
    $maxFreq = collect($freq)->max() ?: 1;
@endphp

<div style="margin-bottom:1.25rem;">
    <div class="dash-title">🧠 Analisis Perbincangan Komunitas</div>
    <p style="font-size:12px;color:var(--text-3);margin-top:3px;">
        Base data dari <b>postingan & komentar publik</b> (Aku & Kita). Obrolan pribadi (Dia) & catatan (Kamu) tidak disertakan.
    </p>
</div>

@if(session('success'))
<div style="background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.3);color:#15803d;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:1rem;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#b91c1c;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:1rem;">⚠ {{ session('error') }}</div>
@endif

{{-- RINGKASAN SUMBER --}}
<div class="metric-grid" style="margin-bottom:1.25rem;">
    @foreach($counts as $label => $c)
    <div class="metric-card sky">
        <div class="metric-num">{{ number_format($c) }}</div>
        <div class="metric-label">{{ $label }}</div>
        <div class="metric-sub">dianalisa</div>
    </div>
    @endforeach
</div>

{{-- KATA KUNCI (base data mentah, tanpa AI) --}}
<div class="dash-card" style="margin-bottom:1.25rem;">
    <div class="dash-card-head">
        <div class="dash-card-title">🔤 Kata yang Sering Muncul</div>
        <span style="font-size:11px;color:var(--text-4);">{{ $total }} item · ukuran = frekuensi</span>
    </div>
    <div style="padding:1rem 1.25rem;">
        @forelse($freq as $word => $n)
            @php $sz = 12 + round(($n / $maxFreq) * 12); @endphp
            <span title="{{ $n }}×"
                  style="display:inline-block;margin:4px 6px 4px 0;padding:4px 12px;border-radius:20px;background:var(--sky-lt);color:var(--sky-dk);border:1px solid var(--border);font-weight:600;font-size:{{ $sz }}px;line-height:1.4;">
                {{ $word }} <span style="opacity:0.55;font-size:10px;">{{ $n }}</span>
            </span>
        @empty
            <div style="font-size:13px;color:var(--text-3);">Belum ada konten untuk dianalisa.</div>
        @endforelse
    </div>
</div>

{{-- ANALISIS AI --}}
<div class="dash-card">
    <div class="dash-card-head">
        <div class="dash-card-title">🤖 Analisis AI</div>
        @if($ai)
            <span style="font-size:11px;color:var(--text-4);">{{ $ai['at'] }} · {{ $ai['n'] }} item</span>
        @endif
    </div>
    <div style="padding:1rem 1.25rem;">
        @if($ai)
            <div style="font-size:13.5px;color:var(--text-1);line-height:1.75;white-space:pre-wrap;">{!! nl2br(e($ai['text'])) !!}</div>
        @else
            <p style="font-size:13px;color:var(--text-3);margin-bottom:1rem;">Belum ada analisis. Klik tombol untuk minta AI merangkum topik, suasana, & ide konten dari obrolan komunitas.</p>
        @endif

        @if($hasAi)
        <form method="POST" action="{{ route('admin.insights.analyze') }}" style="margin-top:1rem;"
              onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='⏳ Menganalisa (10-30 dtk)...';">
            @csrf
            <button type="submit"
                    style="background:linear-gradient(135deg,var(--sky),var(--sky-dk));color:#fff;border:none;border-radius:10px;padding:11px 22px;font-size:13px;font-weight:600;cursor:pointer;">
                {{ $ai ? '🔄 Analisa Ulang dengan AI' : '✨ Analisa dengan AI' }}
            </button>
            <span style="font-size:11px;color:var(--text-4);margin-left:10px;">hasil tersimpan 7 hari</span>
        </form>
        @else
        <div style="font-size:12px;color:var(--text-3);background:var(--surface);border:1px dashed var(--border);border-radius:10px;padding:12px 14px;margin-top:1rem;">
            Belum ada provider AI teks aktif. Pasang <b>DeepSeek</b> di
            <a href="{{ route('admin.ai-settings') }}" style="color:var(--sky-dk);">Pengaturan AI</a> dulu untuk mengaktifkan analisis AI.
        </div>
        @endif
    </div>
</div>

{{-- SARAN SEO (Fase 3) --}}
<div class="dash-card" style="margin-top:1.25rem;">
    <div class="dash-card-head">
        <div class="dash-card-title">🔎 Saran SEO</div>
        @if($seoTips)
            <span style="font-size:11px;color:var(--text-4);">{{ $seoTips['at'] }}</span>
        @endif
    </div>
    <div style="padding:1rem 1.25rem;">
        <p style="font-size:12px;color:var(--text-3);margin-bottom:0.75rem;">Dari kata kunci komunitas + lagu terpopuler &rarr; AI menyarankan meta description, ide konten, lagu yang layak didorong &amp; frasa kunci. <b>Saran saja</b> (terapkan manual di Pengaturan/konten).</p>
        @if($seoTips)
            <div style="font-size:13.5px;color:var(--text-1);line-height:1.75;white-space:pre-wrap;">{!! nl2br(e($seoTips['text'])) !!}</div>
        @endif
        @if($hasAi)
        <form method="POST" action="{{ route('admin.insights.seo') }}" style="margin-top:1rem;"
              onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='⏳ Membuat saran (10-30 dtk)...';">
            @csrf
            <button type="submit"
                    style="background:linear-gradient(135deg,var(--orange),var(--orange-dk));color:#fff;border:none;border-radius:10px;padding:11px 22px;font-size:13px;font-weight:600;cursor:pointer;">
                {{ $seoTips ? '🔄 Buat Ulang Saran SEO' : '✨ Buat Saran SEO' }}
            </button>
            <span style="font-size:11px;color:var(--text-4);margin-left:10px;">hasil tersimpan 7 hari</span>
        </form>
        @else
        <div style="font-size:12px;color:var(--text-3);background:var(--surface);border:1px dashed var(--border);border-radius:10px;padding:12px 14px;margin-top:1rem;">
            Butuh provider AI (DeepSeek) aktif — pasang di
            <a href="{{ route('admin.ai-settings') }}" style="color:var(--sky-dk);">Pengaturan AI</a>.
        </div>
        @endif
    </div>
</div>

@endsection
