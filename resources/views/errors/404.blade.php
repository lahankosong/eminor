@extends('layouts.app')

@section('content')
@php $seo = ['title' => 'Halaman Tak Ditemukan (404) — Margonoandi', 'robots' => 'noindex, follow']; @endphp
<div style="max-width:520px;margin:0 auto;padding:4rem 1.25rem;text-align:center;">
    <div style="font-size:4rem;line-height:1;margin-bottom:.5rem;">🎸</div>
    <div style="font-family:'Space Grotesk','Sora','Inter',sans-serif;font-size:clamp(2.2rem,9vw,3.5rem);font-weight:800;color:var(--accent,#38bdf8);line-height:1;">404</div>
    <h1 style="font-family:'Space Grotesk','Sora',sans-serif;font-size:1.3rem;font-weight:700;color:var(--text,#f0f0f0);margin:.85rem 0 .5rem;">Wah, halaman ini hilang dari setlist</h1>
    <p style="font-size:13.5px;color:var(--text-3,#94a3b8);line-height:1.7;max-width:420px;margin:0 auto 1.75rem;">Mungkin link-nya keliru atau halaman sudah pindah. Yuk balik ke jalur yang benar:</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('home') }}" style="background:linear-gradient(135deg,var(--accent,#38bdf8),#0ea5e9);color:#fff;padding:11px 22px;border-radius:11px;font-size:14px;font-weight:700;text-decoration:none;">← Beranda</a>
        <a href="{{ route('tools.index') }}" style="background:var(--card-bg,rgba(15,23,42,.6));border:1px solid var(--border,#334155);color:var(--text,#f0f0f0);padding:11px 22px;border-radius:11px;font-size:14px;font-weight:700;text-decoration:none;">🎛️ Alat Gratis</a>
    </div>
</div>
@endsection
