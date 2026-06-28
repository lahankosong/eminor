<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>EMINOR — Ekosistem Musik Indie Indonesia</title>
<meta name="description" content="EMINOR adalah ekosistem musik indie Indonesia — tempat belajar, berkarya, bertemu musisi, dan tumbuh bersama. Gratis untuk semua musisi.">
<meta property="og:title" content="EMINOR — Ekosistem Musik Indie Indonesia">
<meta property="og:image" content="{{ asset('images/Margonoandi.jpeg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;overflow-x:hidden}
body{background:#06080f;color:#e2e8f0;font-family:'Sora',system-ui,sans-serif;line-height:1.6;overflow-x:hidden}
::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#1e3a5f;border-radius:2px}
a{text-decoration:none;color:inherit}

:root{
  --accent:#38A8CC;--accent2:#5B6EF5;--accent3:#8B5CF6;
  --text:#e2e8f0;--t2:#94a3b8;--t3:#4a5568;
  --card:rgba(14,20,40,.65);--border:rgba(56,168,204,.14);--border2:rgba(255,255,255,.06);
}

/* ── AURORA ── */
.aurora{position:absolute;inset:0;overflow:hidden;pointer-events:none;z-index:0}
.aurora::before,.aurora::after{content:'';position:absolute;border-radius:50%;filter:blur(90px)}
.aurora::before{width:600px;height:600px;background:radial-gradient(circle,#38A8CC33,transparent 70%);top:-150px;left:-100px;animation:a1 20s ease-in-out infinite alternate}
.aurora::after{width:500px;height:500px;background:radial-gradient(circle,#5B6EF533,transparent 70%);bottom:-80px;right:-80px;animation:a2 25s ease-in-out infinite alternate}
@keyframes a1{to{transform:translate(60px,50px) scale(1.15)}}
@keyframes a2{to{transform:translate(-50px,-40px) scale(1.12)}}

/* ── INTRO ── */
#intro{position:fixed;inset:0;background:#020307;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity .8s}
#intro.out{opacity:0;pointer-events:none}

/* intro background */
.ibg{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.iaur{position:absolute;left:50%;top:50%;width:600px;height:600px;margin:-300px 0 0 -300px;
  background:radial-gradient(circle,rgba(56,168,204,.07) 0%,rgba(91,110,245,.05) 40%,transparent 70%);
  border-radius:50%;filter:blur(70px);
  animation:iaur 7s ease-in-out infinite alternate}
@keyframes iaur{0%{transform:scale(.85) translate(-25px,-15px)}100%{transform:scale(1.18) translate(20px,25px)}}
.iaur2{position:absolute;left:30%;top:60%;width:350px;height:350px;margin:-175px 0 0 -175px;
  background:radial-gradient(circle,rgba(139,92,246,.05),transparent 70%);
  border-radius:50%;filter:blur(60px);
  animation:iaur 9s ease-in-out infinite alternate-reverse}
.iring{position:absolute;left:50%;top:50%;border-radius:50%;
  border:1px solid rgba(56,168,204,.22);
  animation:iring 3.6s ease-out infinite}
.iring:nth-child(3){animation-delay:1.2s}
.iring:nth-child(4){animation-delay:2.4s}
@keyframes iring{
  0%  {width:40px;height:40px;margin:-20px 0 0 -20px;opacity:.7;border-color:rgba(56,168,204,.45)}
  100%{width:600px;height:600px;margin:-300px 0 0 -300px;opacity:0;border-color:rgba(91,110,245,.05)}
}
.iskip{position:absolute;top:1.25rem;right:1.25rem;font-size:11px;color:rgba(255,255,255,.3);letter-spacing:.1em;cursor:pointer;border:1px solid rgba(255,255,255,.1);padding:4px 12px;border-radius:20px;transition:.2s}
.iskip:hover{color:#fff;border-color:rgba(255,255,255,.4)}
.imetro{display:flex;gap:9px;margin-bottom:2.5rem;align-items:center;height:28px}
.idot{width:6px;height:6px;background:#38A8CC;border-radius:50%;opacity:0;transform:scale(0)}
@keyframes dpop{0%,100%{opacity:0;transform:scale(0)}30%,70%{opacity:1;transform:scale(1)}}
.itext{text-align:center;min-height:90px}
.iline{font-size:clamp(.9rem,2.5vw,1.2rem);font-weight:300;color:rgba(255,255,255,.88);letter-spacing:.03em;opacity:0;transition:opacity .7s}
.iline.s{opacity:1}
.ilogo{font-size:clamp(3rem,12vw,6.5rem);font-weight:800;letter-spacing:.18em;color:#fff;
  opacity:0;transform:scale(.88);
  transition:opacity .9s ease, transform .9s cubic-bezier(.22,1,.36,1);
  margin-top:2rem;line-height:1}
.ilogo span{color:#38A8CC}
.ilogo.s{opacity:1;transform:scale(1)}
.ilogo.s span{animation:accentflash 1.8s ease-out .6s forwards}
@keyframes accentflash{
  0%  {color:#38A8CC}
  20% {color:#fff;text-shadow:0 0 40px #38A8CC,0 0 80px rgba(56,168,204,.4)}
  100%{color:#38A8CC;text-shadow:0 0 12px rgba(56,168,204,.2)}
}
.itag{font-size:12px;color:rgba(255,255,255,.35);letter-spacing:.1em;opacity:0;
  transition:opacity 1s .4s;margin-top:.6rem;text-align:center}.itag.s{opacity:1}

/* ── NAV ── */
nav{position:fixed;inset:0 0 auto;z-index:900;height:60px;padding:0 2rem;display:flex;align-items:center;justify-content:space-between;transition:background .4s,border .4s}
nav.on{background:rgba(6,8,15,.9);backdrop-filter:blur(16px);border-bottom:1px solid var(--border)}
.nlogo{font-size:1.1rem;font-weight:800;letter-spacing:.1em;color:#fff}.nlogo span{color:#38A8CC}
.nlinks{display:flex;gap:1.75rem;list-style:none}
.nlinks a{font-size:12.5px;color:var(--t2);letter-spacing:.04em;transition:.2s}.nlinks a:hover{color:#fff}
.ncta{background:#38A8CC;color:#fff;font-size:12px;font-weight:700;padding:8px 20px;border-radius:50px;letter-spacing:.05em;transition:.2s;white-space:nowrap}
.ncta:hover{background:#2d8fad;transform:translateY(-1px)}
.nmob{display:none;background:none;border:none;color:#fff;font-size:1.3rem;cursor:pointer}
section{position:relative;overflow:hidden}

/* ── HERO ── */
#hero{min-height:100vh;display:flex;flex-direction:column;justify-content:flex-end;padding:0 3rem 4.5rem}
.hbg{position:absolute;inset:0}
.hslide{position:absolute;inset:0;opacity:0;transition:opacity 1.8s ease}
.hslide.on{opacity:1}
.hs1{background:linear-gradient(160deg,#050919,#0c1828,#080f1c)}
.hs2{background:linear-gradient(160deg,#08051a,#140826,#060512)}
.hs3{background:linear-gradient(160deg,#05131a,#041515,#06080f)}
.hov{position:absolute;inset:0;background:linear-gradient(to top,rgba(6,8,15,.95) 25%,rgba(6,8,15,.45) 65%,rgba(6,8,15,.15) 100%);z-index:1}
.hcont{position:relative;z-index:2;max-width:740px}
.hftw{position:relative;min-height:160px;margin-bottom:1.75rem}
.hf{font-size:clamp(1.2rem,3.2vw,1.8rem);font-weight:300;letter-spacing:.02em;opacity:0;position:absolute;transition:opacity .8s ease}
.hf.s{opacity:1}
.hf.dim{color:var(--t3);font-size:clamp(.9rem,2vw,1.15rem);letter-spacing:.12em;text-transform:uppercase}
.hf.sm{font-size:clamp(1rem,2.2vw,1.35rem);color:var(--t2)}
.hf.big{font-size:clamp(1.5rem,3.8vw,2.5rem);font-weight:700;color:#fff;line-height:1.3}
.hf.big span{color:#38A8CC}
.htag{font-size:clamp(.88rem,1.8vw,1rem);color:var(--t2);line-height:1.85;max-width:460px;margin-bottom:2.25rem;opacity:0;transition:opacity .8s}
.htag.s{opacity:1}
.hact{display:flex;gap:12px;flex-wrap:wrap;opacity:0;transition:opacity .8s}
.hact.s{opacity:1}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 30px;border-radius:50px;font-size:13.5px;font-weight:600;letter-spacing:.05em;cursor:pointer;transition:.2s;border:none;font-family:inherit}
.btn-p{background:linear-gradient(135deg,#38A8CC,#2186a8);color:#fff;box-shadow:0 6px 28px rgba(56,168,204,.28)}
.btn-p:hover{transform:translateY(-2px);box-shadow:0 10px 36px rgba(56,168,204,.42)}
.btn-g{background:rgba(255,255,255,.06);color:var(--t2);border:1px solid var(--border2)}
.btn-g:hover{background:rgba(255,255,255,.1);color:#fff}
.hscroll{position:absolute;bottom:1.75rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:7px;z-index:2}
.hscroll span{font-size:9px;letter-spacing:.2em;color:var(--t3);text-transform:uppercase}
.harr{width:1px;height:36px;background:linear-gradient(to bottom,transparent,#38A8CC);animation:sp 2s ease-in-out infinite}
@keyframes sp{0%,100%{opacity:.3;transform:scaleY(.8)}50%{opacity:1;transform:scaleY(1)}}

/* ── S2 — EXPLORE (cards + stats) ── */
#s-exp{padding:4.5rem 2rem;background:#06080f}
.exp-top{text-align:center;margin-bottom:2.5rem}
.exp-ey{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#38A8CC;margin-bottom:.6rem}
.exp-h{font-size:clamp(1.3rem,3vw,1.9rem);font-weight:700;color:#fff;margin-bottom:.4rem}
.exp-sub{font-size:13.5px;color:var(--t2)}
.cgrid{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;max-width:900px;margin:0 auto 2.5rem}
.ci{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.4rem 1.1rem;cursor:pointer;transition:.22s;position:relative;overflow:hidden}
.ci::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% -20%,rgba(56,168,204,.07),transparent 60%);opacity:0;transition:.3s}
.ci:hover{border-color:rgba(56,168,204,.45);transform:translateY(-5px);box-shadow:0 16px 40px rgba(56,168,204,.1)}
.ci:hover::before{opacity:1}
.ci-ic{font-size:1.7rem;margin-bottom:.7rem}
.ci-t{font-size:13px;font-weight:700;color:#fff;margin-bottom:.45rem}
.ci-div{width:24px;height:1.5px;background:#38A8CC;border-radius:2px;margin:.5rem 0}
.ci-tag{font-size:11px;color:var(--t2);line-height:1.8}
/* stats strip */
.stats-strip{display:flex;border:1px solid var(--border);border-radius:14px;overflow:hidden;max-width:600px;margin:0 auto}
.ss-item{flex:1;padding:1rem .75rem;text-align:center;border-right:1px solid var(--border)}
.ss-item:last-child{border-right:none}
.ss-num{font-size:1.5rem;font-weight:800;color:#fff;line-height:1}
.ss-suf{font-size:.9rem;color:#38A8CC;font-weight:700}
.ss-lab{font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-top:.25rem}

/* ── S3 — MANIFESTO + LIVE ── */
#s-mid{padding:5rem 2rem;background:#020307}
.mid-wrap{max-width:1000px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start}

/* left: manifesto */
.mf-ey{font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:#38A8CC;margin-bottom:1.5rem;padding-left:1.6rem}
.mf-line{font-size:clamp(1rem,2.4vw,1.4rem);font-weight:300;color:rgba(255,255,255,.8);line-height:1.9;margin-bottom:.3rem}
.mf-line.ac{font-size:clamp(1.1rem,2.7vw,1.55rem);font-weight:600;color:#fff}
.mf-line.hl{color:#38A8CC}
.mf-gap{height:1.4rem}

/* manifesto bar + stagger reveal */
.mf-bar-wrap{position:relative;padding-left:1.6rem}
.mf-bar{position:absolute;left:0;top:0;width:2px;height:0;border-radius:2px;
  background:linear-gradient(to bottom,#38A8CC,#5B6EF5 60%,#8B5CF6);
  transition:height 2s cubic-bezier(.22,1,.36,1);box-shadow:0 0 8px rgba(56,168,204,.3)}
.mf-bar-wrap.go .mf-bar{height:100%}

/* individual line reveal: slide from left */
.mr{opacity:0;transform:translateX(-20px);transition:opacity .55s ease, transform .55s cubic-bezier(.22,1,.36,1)}
.mr.on{opacity:1;transform:none}

/* highlight line glows after appearing */
.mf-line.hl.on{animation:mfglow 3s ease-out .3s forwards}
@keyframes mfglow{
  0%  {text-shadow:none}
  30% {text-shadow:0 0 28px rgba(56,168,204,.8),0 0 60px rgba(56,168,204,.3)}
  100%{text-shadow:0 0 10px rgba(56,168,204,.2)}
}

/* EMINOR brand mark inline */
.brand-em{font-weight:800;letter-spacing:.06em;color:#fff}
.brand-em span{color:#38A8CC}

/* manifesto card — spinning border glow */
.mf-card{position:relative;border-radius:20px;padding:1.75rem 1.75rem 1.75rem 0;overflow:hidden;background:rgba(8,14,30,.92);isolation:isolate}
.mf-card-glow{position:absolute;inset:0;pointer-events:none;z-index:0}
.mf-card-glow::before{content:'';position:absolute;inset:-160%;
  background:conic-gradient(from 0deg at 50% 50%,
    transparent 328deg,
    rgba(56,168,204,.75) 342deg,
    rgba(91,110,245,.5)  350deg,
    transparent 360deg);
  animation:mfCardSpin 5s linear infinite}
.mf-card-glow::after{content:'';position:absolute;inset:1px;background:rgba(8,14,30,.94);border-radius:19px}
@keyframes mfCardSpin{to{transform:rotate(360deg)}}
.mf-card>*:not(.mf-card-glow){position:relative;z-index:1}

/* right: live */
.live-ey{display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#38A8CC;background:rgba(56,168,204,.08);border:1px solid rgba(56,168,204,.22);padding:4px 12px;border-radius:20px;margin-bottom:.85rem}
.ldot{width:5px;height:5px;border-radius:50%;background:#38A8CC;animation:lp 1.4s ease-in-out infinite}
@keyframes lp{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.35;transform:scale(.65)}}
.live-h{font-size:.9rem;font-weight:700;color:#fff;margin-bottom:.6rem}
.lcard{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:.5rem .75rem;display:flex;gap:9px;align-items:center;margin-bottom:5px;transition:.2s}
.lcard:hover{border-color:rgba(56,168,204,.3)}
.lic{font-size:1.05rem;flex-shrink:0}
.lu{font-size:12px;font-weight:600;color:#fff}
.ld{font-size:10.5px;color:var(--t2);margin-top:1px;line-height:1.4}
.lt{font-size:9.5px;color:var(--t3);margin-top:1px}
.live-more{display:block;text-align:center;font-size:12px;color:#38A8CC;margin-top:.6rem;letter-spacing:.04em;transition:.2s}
.live-more:hover{opacity:.7}

/* ── S4 — FITUR GRID ── */
#s-feat{padding:5rem 2rem;background:#06080f}
.feat-top{text-align:center;margin-bottom:3rem}
.feat-top h2{font-size:clamp(1.3rem,3vw,1.9rem);font-weight:700;color:#fff;margin-bottom:.4rem}
.feat-top p{font-size:13.5px;color:var(--t2)}
.fgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;max-width:820px;margin:0 auto 3.5rem}
.fcard{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.5rem 1.3rem;transition:.22s;position:relative;overflow:hidden}
.fcard::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#38A8CC,#5B6EF5);transform:scaleX(0);transform-origin:left;transition:.4s}
.fcard:hover{border-color:rgba(56,168,204,.3);transform:translateY(-3px)}
.fcard:hover::after{transform:scaleX(1)}
.fc-ic{font-size:1.6rem;margin-bottom:.8rem}
.fc-t{font-size:14px;font-weight:700;color:#fff;margin-bottom:.3rem}
.fc-d{font-size:12px;color:var(--t2);line-height:1.65}
.fc-link{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;color:#38A8CC;margin-top:.7rem;font-weight:600;transition:.2s}
.fc-link:hover{gap:8px}
/* roadmap (di dalam s-feat) */
.rm-row{max-width:820px;margin:0 auto}
.rm-head{font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--t3);font-weight:700;margin-bottom:.9rem;display:flex;align-items:center;gap:8px}
.rm-head::after{content:'';flex:1;height:1px;background:var(--border)}
.rm-pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:2rem}
.rp{display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:6px 13px;border-radius:20px}
.rp.done{background:rgba(56,168,204,.1);border:1px solid rgba(56,168,204,.25);color:#38A8CC}
.rp.soon{background:rgba(139,92,246,.08);border:1px dashed rgba(139,92,246,.28);color:#a78bfa}
.rp-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.rp.done .rp-dot{background:#38A8CC}
.rp.soon .rp-dot{background:#a78bfa}

/* ── CTA FINAL ── */
.cta-wrap{text-align:center;padding:4rem 1rem 0;border-top:1px solid var(--border);margin-top:3rem}
.cta-h{font-size:clamp(1.3rem,3.5vw,2rem);font-weight:700;color:#fff;margin-bottom:.6rem}
.cta-sub{font-size:14px;color:var(--t2);max-width:380px;margin:0 auto 2rem;line-height:1.8}
.cta-btn{display:inline-flex;flex-direction:column;align-items:center;gap:0;cursor:pointer;background:none;border:none;font-family:inherit}
.cta-line{width:100%;height:2px;background:linear-gradient(90deg,transparent,#38A8CC,transparent);border-radius:2px;transition:.3s}
.cta-inner{display:flex;align-items:center;gap:10px;padding:20px 44px;background:rgba(56,168,204,.07);border:1px solid rgba(56,168,204,.28);border-top:none;border-bottom:none;font-size:clamp(13px,2vw,16px);font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#fff;transition:.3s}
.cta-btn:hover .cta-inner{background:rgba(56,168,204,.16);color:#38A8CC}
.cta-btn:hover .cta-line{background:linear-gradient(90deg,transparent,#38A8CC 30%,#5B6EF5 70%,transparent)}

/* ── MODAL ── */
#mbg{position:fixed;inset:0;background:rgba(2,3,7,.88);backdrop-filter:blur(8px);z-index:9100;display:none;align-items:center;justify-content:center;padding:1rem}
#mbg.on{display:flex}
.mbox{background:#0c1120;border:1px solid var(--border);border-radius:22px;padding:2.25rem 1.75rem;max-width:420px;width:100%;position:relative;animation:mi .3s ease}
@keyframes mi{from{opacity:0;transform:translateY(18px) scale(.97)}to{opacity:1;transform:none}}
.mclose{position:absolute;top:.9rem;right:1.1rem;background:none;border:none;color:var(--t3);font-size:1.2rem;cursor:pointer;transition:.2s;line-height:1}.mclose:hover{color:#fff}
.mh{font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:.25rem}
.ms{font-size:13px;color:var(--t2);margin-bottom:1.5rem}
.mopts{display:flex;flex-direction:column;gap:7px;margin-bottom:1.5rem}
.mopt{display:flex;align-items:center;gap:11px;padding:10px 13px;border-radius:11px;border:1.5px solid var(--border);cursor:pointer;transition:.18s;font-size:13px;color:var(--t2);background:none;text-align:left;font-family:inherit}
.mopt:hover,.mopt.sel{border-color:#38A8CC;color:#fff;background:rgba(56,168,204,.07)}
.mopt input{accent-color:#38A8CC}
.mlogin{width:100%;padding:13px;border-radius:50px;background:linear-gradient(135deg,#38A8CC,#2186a8);color:#fff;font-size:13.5px;font-weight:700;letter-spacing:.05em;border:none;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:9px;font-family:inherit}
.mlogin:hover{opacity:.9;transform:translateY(-1px)}

/* ── FOOTER ── */
footer{background:#020307;padding:3.5rem 2rem 2.5rem;text-align:center;border-top:1px solid var(--border)}
.flogo{font-size:1.3rem;font-weight:800;letter-spacing:.15em;color:#fff;margin-bottom:.3rem}.flogo span{color:#38A8CC}
.ftag{font-size:13px;color:var(--t2);margin-bottom:2rem;line-height:1.7}
.fdiv{width:40px;height:1px;background:linear-gradient(90deg,transparent,#38A8CC,transparent);margin:1.5rem auto}
.fpoem{font-size:12.5px;color:var(--t3);line-height:2.2;max-width:320px;margin:0 auto 1.75rem}.fpoem em{color:var(--t2);font-style:normal}
.flinks{display:flex;gap:1.75rem;justify-content:center;flex-wrap:wrap;margin-bottom:1.5rem}
.flinks a{font-size:11.5px;color:var(--t3);letter-spacing:.05em;transition:.2s}.flinks a:hover{color:#38A8CC}
.fcopy{font-size:10.5px;color:var(--t3)}

/* ── FILM GRAIN ── */
#grain{position:fixed;inset:0;z-index:9998;pointer-events:none;opacity:.032;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-repeat:repeat;background-size:200px;animation:gr .5s steps(2) infinite}
@keyframes gr{0%,100%{background-position:0 0}25%{background-position:-5% -10%}50%{background-position:-15% 5%}75%{background-position:7% -25%}}

/* ── CURSOR GLOW ── */
#cglow{position:fixed;width:380px;height:380px;border-radius:50%;
  background:radial-gradient(circle,rgba(56,168,204,.065),transparent 70%);
  pointer-events:none;z-index:1;transform:translate(-50%,-50%);
  mix-blend-mode:screen;will-change:left,top}

/* ── VINYL (intro) ── */
.vinyl{width:90px;height:90px;border-radius:50%;position:relative;
  background:conic-gradient(#0f0f0f 0deg,#1c1c1c 22deg,#090909 44deg,#151515 66deg,
    #0f0f0f 88deg,#090909 110deg,#1c1c1c 132deg,#0f0f0f 154deg,#111 176deg,
    #090909 198deg,#151515 220deg,#1c1c1c 242deg,#0f0f0f 264deg,#111 286deg,
    #090909 308deg,#1c1c1c 330deg,#0f0f0f 352deg,#111 360deg);
  animation:vspin 4s linear infinite;margin-bottom:1.5rem;
  box-shadow:0 0 30px rgba(56,168,204,.22),0 0 70px rgba(56,168,204,.08),0 0 120px rgba(56,168,204,.04)}
.vinyl::before{content:'';position:absolute;inset:37%;border-radius:50%;
  background:radial-gradient(circle,#38A8CC 30%,#2186a8);
  box-shadow:0 0 14px rgba(56,168,204,.7)}
@keyframes vspin{to{transform:rotate(360deg)}}

/* ── FLOATING NOTES (hero) ── */
.hnotes{position:absolute;inset:0;pointer-events:none;z-index:1;overflow:hidden}
.hn{position:absolute;color:rgba(56,168,204,.13);animation:hnf 8s ease-in-out infinite}
@keyframes hnf{
  0%,100%{transform:translateY(0) rotate(-15deg);opacity:.06}
  40%{transform:translateY(-55px) rotate(12deg);opacity:.22}
  70%{transform:translateY(-30px) rotate(-5deg);opacity:.13}
}

/* ── EQ BARS (hero) ── */
.heq{display:flex;align-items:flex-end;gap:3px;height:20px;margin-bottom:1.5rem;opacity:.65}
.heqb{width:3px;border-radius:2px 2px 0 0;
  background:linear-gradient(to top,#38A8CC,#5B6EF5);
  animation:heqbar 1s ease-in-out infinite alternate}
.heqb:nth-child(1){animation-duration:.78s}
.heqb:nth-child(2){animation-duration:1.12s;animation-delay:.1s}
.heqb:nth-child(3){animation-duration:.68s;animation-delay:.22s}
.heqb:nth-child(4){animation-duration:1.25s;animation-delay:.08s}
.heqb:nth-child(5){animation-duration:.9s;animation-delay:.35s}
.heqb:nth-child(6){animation-duration:1.05s;animation-delay:.15s}
.heqb:nth-child(7){animation-duration:.72s;animation-delay:.45s}
.heqb:nth-child(8){animation-duration:1.18s;animation-delay:.05s}
@keyframes heqbar{from{height:3px}to{height:18px}}

/* ── S-HOW — CARA KERJA ── */
#s-how{padding:4.5rem 2rem;background:#020307}
.how-wrap{max-width:860px;margin:0 auto;text-align:center}
.how-ey{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#38A8CC;margin-bottom:.6rem;
  display:flex;align-items:center;justify-content:center;gap:14px}
.how-ey::before{content:'';flex:0 0 50px;height:1px;background:linear-gradient(90deg,transparent,#38A8CC)}
.how-ey::after{content:'';flex:0 0 50px;height:1px;background:linear-gradient(90deg,#38A8CC,transparent)}
.how-h{font-size:clamp(1.2rem,2.8vw,1.7rem);font-weight:700;color:#fff;margin-bottom:2.5rem}
.how-grid{display:flex;align-items:stretch;gap:.85rem;justify-content:center}
.how-conn{display:flex;align-items:center;padding-top:1.25rem;color:var(--t3);flex-shrink:0;font-size:.8rem}
.how-step{flex:1;max-width:235px;background:var(--card);border:1px solid var(--border);
  border-radius:16px;padding:1.4rem 1.1rem;position:relative;text-align:left;
  transition:.22s}
.how-step:hover{border-color:rgba(56,168,204,.35);transform:translateY(-4px);box-shadow:0 14px 36px rgba(56,168,204,.09)}
.how-num{font-size:9px;font-weight:800;letter-spacing:.1em;color:#38A8CC;margin-bottom:.65rem}
.how-ic{font-size:1.8rem;margin-bottom:.55rem;display:block}
.how-t{font-size:13.5px;font-weight:700;color:#fff;margin-bottom:.35rem}
.how-d{font-size:11.5px;color:var(--t2);line-height:1.75}

/* ── MARQUEE STRIP ── */
.mq-wrap{overflow:hidden;border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  padding:10px 0;background:rgba(56,168,204,.025)}
.mq-track{display:flex;gap:0;width:max-content;animation:mq 40s linear infinite}
.mq-track:hover{animation-play-state:paused}
.mq-item{display:inline-flex;align-items:center;gap:1.5rem;padding:0 1.5rem;white-space:nowrap}
.mq-item>span{font-size:10.5px;color:var(--t2);letter-spacing:.07em;text-transform:uppercase}
.mq-item>.sep{color:var(--t3);font-size:7px}
@keyframes mq{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ── WAVEFORM FOOTER ── */
.fwave{overflow:hidden;height:48px;margin-bottom:1.75rem;opacity:.7}
.fwave svg{width:100%;height:48px;display:block}

/* ── REVEAL ── */
.rv{opacity:0;transform:translateY(20px);transition:.65s ease}.rv.on{opacity:1;transform:none}

/* ── RESPONSIVE ── */
@media(max-width:860px){
  .mid-wrap{grid-template-columns:1fr;gap:2.5rem}
  .fgrid{grid-template-columns:1fr 1fr}
  .cgrid{grid-template-columns:repeat(3,1fr)}
  .how-grid{gap:.6rem}.how-step{max-width:none}
}
@media(max-width:640px){
  #hero{padding:0 1.5rem 4rem}
  .nlinks,.ncta{display:none}.nmob{display:block}
  .cgrid{grid-template-columns:1fr 1fr}
  .fgrid{grid-template-columns:1fr}
  .stats-strip{flex-wrap:wrap}.ss-item{min-width:50%;border-right:none;border-bottom:1px solid var(--border)}
  .ss-item:last-child{border-bottom:none}
  .cta-inner{padding:16px 24px;font-size:13px}
  .mid-wrap{gap:2rem}
  .how-grid{flex-direction:column;align-items:center}
  .how-conn{display:none}
  .how-step{max-width:100%;width:100%}
  #cglow{display:none}
}
@media(max-width:420px){
  .cgrid{grid-template-columns:1fr}
  .hact{flex-direction:column}
  .btn{justify-content:center}
}
</style>
</head>
<body>
<div id="grain"></div>
<div id="cglow"></div>

{{-- ════════ INTRO OVERLAY ════════ --}}
<div id="intro">
  <div class="ibg">
    <div class="iaur"></div>
    <div class="iaur2"></div>
    <div class="iring"></div>
    <div class="iring"></div>
    <div class="iring"></div>
  </div>
  <button class="iskip" onclick="skipIntro()">SKIP ↗</button>
  <div class="imetro">
    <div class="idot" id="d1"></div>
    <div class="idot" id="d2"></div>
    <div class="idot" id="d3"></div>
  </div>
  <div class="vinyl"></div>
  <div class="itext">
    <div class="iline" id="l1">Dulu...</div>
    <div class="iline" id="l2" style="color:var(--t2);font-size:.95rem">musisi membutuhkan label untuk didengar.</div>
    <div class="iline" id="l3" style="margin-top:1.25rem">Sekarang...</div>
    <div class="iline" id="l4" style="color:var(--t2);font-size:.95rem">yang dibutuhkan hanya tempat yang tepat.</div>
  </div>
  <div class="ilogo" id="ilogo">E<span>MINOR</span></div>
  <div class="itag" id="itag">Ekosistem Musik Indie Indonesia</div>
</div>

{{-- ════════ NAVBAR ════════ --}}
<nav id="nav">
  <a href="#" class="nlogo">E<span>MINOR</span></a>
  <ul class="nlinks">
    <li><a href="#s-mid">Visi</a></li>
    <li><a href="#s-feat">Fitur</a></li>
    <li><a href="#s-mid">Komunitas</a></li>
    <li><a href="#s-feat">Roadmap</a></li>
  </ul>
  <a href="{{ route('google.login') }}" class="ncta">Mulai Perjalanan</a>
  <button class="nmob" onclick="toggleNav()">☰</button>
</nav>

{{-- ════════ HERO ════════ --}}
<section id="hero">
  <div class="aurora"></div>
  <div class="hbg">
    <div class="hslide hs1 on"></div>
    <div class="hslide hs2"></div>
    <div class="hslide hs3"></div>
  </div>
  <div class="hnotes">
    <span class="hn" style="left:58%;top:22%;font-size:1.5rem">♫</span>
    <span class="hn" style="left:72%;top:58%;font-size:1.1rem;animation-delay:1.2s">♪</span>
    <span class="hn" style="left:83%;top:38%;font-size:.9rem;animation-delay:2.5s">♩</span>
    <span class="hn" style="left:66%;top:78%;font-size:1.3rem;animation-delay:.7s">♬</span>
    <span class="hn" style="left:91%;top:52%;font-size:1.2rem;animation-delay:3.2s">♫</span>
    <span class="hn" style="left:76%;top:18%;font-size:.85rem;animation-delay:1.8s">♪</span>
    <span class="hn" style="left:62%;top:68%;font-size:1.4rem;animation-delay:4s">♩</span>
    <span class="hn" style="left:86%;top:73%;font-size:1rem;animation-delay:.35s">♬</span>
  </div>
  <div class="hov"></div>
  <div class="hcont">
    <div class="hftw">
      <div class="hf dim"  id="hf0">Dulu...</div>
      <div class="hf sm"   id="hf1">musisi membutuhkan label untuk didengar.</div>
      <div class="hf dim"  id="hf2">Sekarang...</div>
      <div class="hf sm"   id="hf3">yang dibutuhkan hanya tempat yang tepat.</div>
      <div class="hf big"  id="hf4">E<span>MINOR</span></div>
      <div class="hf sm"   id="hf5">Tidak semua musisi lahir di kota besar.</div>
      <div class="hf sm"   id="hf6">Tidak semua musisi punya studio.</div>
      <div class="hf sm"   id="hf7">Tidak semua musisi punya koneksi.</div>
      <div class="hf big"  id="hf8">Tetapi semua musisi<br>pantas didengar.</div>
      <div class="hf big"  id="hf9">E<span>MINOR</span> adalah Ekosistem Musisi Indie Indonesia<br>
      yang sedang tumbuh sendirian.</div>
    </div>

    <div class="heq" aria-hidden="true">
      <div class="heqb"></div><div class="heqb"></div><div class="heqb"></div>
      <div class="heqb"></div><div class="heqb"></div><div class="heqb"></div>
      <div class="heqb"></div><div class="heqb"></div>
    </div>
    <div class="hact" id="hact">
      <a href="{{ route('google.login') }}" class="btn btn-p">🎵 Mulai Perjalanan Musik</a>
      <a href="#s-mid" class="btn btn-g">▶ Kisah Kami</a>
    </div>
  </div>
  <div class="hscroll">
    <span>Scroll</span>
    <div class="harr"></div>
  </div>
</section>

{{-- ════════ S2 — EXPLORE ════════ --}}
<section id="s-exp">
  <div class="exp-top rv">
    <div class="exp-ey">Mulai dari sini</div>
    <h2 class="exp-h">Apa yang sedang kamu cari?</h2>
    <p class="exp-sub">Pilih jalurmu — dan EMINOR akan memandumu.</p>
  </div>
  <div class="cgrid rv">
    <div class="ci" onclick="openModal()">
      <div class="ci-ic">🎸</div>
      <div class="ci-t">Belajar Gitar</div>
      <div class="ci-div"></div>
      <div class="ci-tag">Chord · Tuner · BPM</div>
    </div>
    <div class="ci" onclick="openModal()">
      <div class="ci-ic">🎤</div>
      <div class="ci-t">Menulis Lagu</div>
      <div class="ci-div"></div>
      <div class="ci-tag">Notes · Inspirasi · AI</div>
    </div>
    <div class="ci" onclick="openModal()">
      <div class="ci-ic">🥁</div>
      <div class="ci-t">Mencari Band</div>
      <div class="ci-div"></div>
      <div class="ci-tag">Personil · Audisi · Kota</div>
    </div>
    <div class="ci" onclick="openModal()">
      <div class="ci-ic">❤️</div>
      <div class="ci-t">Bagikan Karya</div>
      <div class="ci-div"></div>
      <div class="ci-tag">Upload · Feedback · Fans</div>
    </div>
    <div class="ci" onclick="openModal()">
      <div class="ci-ic">🌎</div>
      <div class="ci-t">Komunitas</div>
      <div class="ci-div"></div>
      <div class="ci-tag">Chat · Gig · Diskusi</div>
    </div>
  </div>

  {{-- Stats strip --}}
  <div class="stats-strip rv">
    <div class="ss-item">
      <div class="ss-num"><span class="sc" data-t="100">100</span><span class="ss-suf">pertama</span></div>
      <div class="ss-lab">Musisi Aktif</div>
    </div>
    <div class="ss-item">
      <div class="ss-num"><span class="sc" data-t="14">14</span></div>
      <div class="ss-lab">Alat Gratis</div>
    </div>
    <div class="ss-item">
      <div class="ss-num"><span class="sc" data-t="31">31</span></div>
      <div class="ss-lab">Materi Musik</div>
    </div>
    <div class="ss-item">
      <div class="ss-num">24<span class="ss-suf">/7</span></div>
      <div class="ss-lab">Komunitas</div>
    </div>
  </div>
</section>

{{-- ════════ S-HOW — CARA KERJA ════════ --}}
<section id="s-how">
  <div class="how-wrap rv">
    <div class="how-ey">Cara Kerja</div>
    <h2 class="how-h">Tiga langkah. Tanpa hambatan.</h2>
    <div class="how-grid">
      <div class="how-step">
        <div class="how-num">01 / MULAI</div>
        <span class="how-ic">👤</span>
        <div class="how-t">Buat Profil</div>
        <div class="how-d">Login dengan Google dalam 10 detik. Isi profil musisimu — genre, instrumen, kota, dan karya.</div>
      </div>
      <div class="how-conn">→</div>
      <div class="how-step">
        <div class="how-num">02 / JELAJAHI</div>
        <span class="how-ic">🔍</span>
        <div class="how-t">Temukan</div>
        <div class="how-d">Cari personil band, gig, materi belajar, atau sesama musisi di kotamu dan seluruh Indonesia.</div>
      </div>
      <div class="how-conn">→</div>
      <div class="how-step">
        <div class="how-num">03 / TUMBUH</div>
        <span class="how-ic">🎵</span>
        <div class="how-t">Berkarya</div>
        <div class="how-d">Upload lagu, bagikan ke komunitas, dan dapatkan feedback nyata dari sesama musisi indie.</div>
      </div>
    </div>
  </div>
</section>

{{-- ════════ MARQUEE STRIP ════════ --}}
@php
$mqItems = ['Jakarta','Gitar','Indie Pop','Bandung','Piano','Folk','Yogyakarta','Ukulele','Jazz','Surabaya','Bass','Metal','Bali','Drum','R&B','Medan','Biola','Reggae','Semarang','Flute','Electronic','Makassar','Saksofon','Blues','Vokal','Alternative','Ambient','Post-Rock'];
@endphp
<div class="mq-wrap" aria-hidden="true">
  <div class="mq-track">
    @foreach(array_merge($mqItems,$mqItems) as $mq)
    <div class="mq-item"><span>{{ $mq }}</span><span class="sep">◆</span></div>
    @endforeach
  </div>
</div>

{{-- ════════ S3 — MANIFESTO + LIVE ════════ --}}
<section id="s-mid">
  <div class="aurora"></div>
  <div class="mid-wrap">

    {{-- LEFT: manifesto --}}
    <div class="mf-card">
      <div class="mf-card-glow"></div>
      <div class="mf-ey mr" id="mf-ey">Kami Percaya</div>
      <div class="mf-bar-wrap" id="mf-bar-wrap">
        <div class="mf-bar"></div>
        <p class="mf-line mr">Bakat tidak memilih tempat lahir.</p>
        <p class="mf-line mr">Musik tidak memilih kota.</p>
        <div class="mf-gap"></div>
        <p class="mf-line ac mr">Lagu yang hebat bisa lahir</p>
        <p class="mf-line ac hl mr">di kamar berukuran 3×3 meter.</p>
        <div class="mf-gap"></div>
        <p class="mf-line mr" style="color:var(--t2)">Yang dibutuhkan hanyalah</p>
        <p class="mf-line mr" style="color:var(--t2)">tempat untuk bertemu.</p>
        <div class="mf-gap"></div>
        <p class="mf-line ac mr" style="color:#38A8CC">Dan <span class="brand-em">E<span>MINOR</span></span> ingin menjadi tempat itu.</p>
      </div>
    </div>

    {{-- RIGHT: live community --}}
    <div class="rv">
      <div class="live-ey"><div class="ldot"></div> Live Terus</div>
      <div class="live-h">Hari ini di EMINOR</div>
      @forelse($liveActivity as $act)
      <div class="lcard">
        <div class="lic">{{ $act['icon'] }}</div>
        <div>
          <div class="lu">{{ $act['user'] }}</div>
          <div class="ld">{{ $act['text'] }}</div>
          <div class="lt">{{ $act['time'] }}</div>
        </div>
      </div>
      @empty
      <div class="lcard"><div class="lic">🎤</div><div><div class="lu">Fajar</div><div class="ld">Baru upload lagu perdana</div><div class="lt">2 menit lalu</div></div></div>
      <div class="lcard"><div class="lic">🥁</div><div><div class="lu">Rama</div><div class="ld">Sedang mencari drummer di Yogyakarta</div><div class="lt">5 menit lalu</div></div></div>
      <div class="lcard"><div class="lic">🎸</div><div><div class="lu">Rina</div><div class="ld">Baru belajar chord Em</div><div class="lt">7 menit lalu</div></div></div>
      <div class="lcard"><div class="lic">❤️</div><div><div class="lu">Lagu "Rindu"</div><div class="ld">Mendapat 56 like dari komunitas</div><div class="lt">12 menit lalu</div></div></div>
      @endforelse
      <a href="{{ route('gig.board') }}" class="live-more">Lihat semua aktivitas →</a>
    </div>

  </div>
</section>

{{-- ════════ S4 — FITUR + ROADMAP + CTA ════════ --}}
<section id="s-feat">
  <div class="feat-top rv">
    <h2>Semua yang kamu butuhkan</h2>
    <p>Tools, komunitas, dan peluang — dalam satu platform. Gratis.</p>
  </div>

  <div class="fgrid rv">
    <a href="{{ route('tools.chord-builder') }}" class="fcard">
      <div class="fc-ic">🎸</div>
      <div class="fc-t">Chord Builder</div>
      <div class="fc-d">Visualisasi chord gitar, ukulele, piano & bass. Transposi otomatis.</div>
      <div class="fc-link">Coba sekarang →</div>
    </a>
    <a href="{{ route('tools.bpm-kalkulator') }}" class="fcard">
      <div class="fc-ic">🥁</div>
      <div class="fc-t">BPM Calculator</div>
      <div class="fc-d">Tap tempo untuk menemukan BPM lagu yang sedang kamu dengar.</div>
      <div class="fc-link">Coba sekarang →</div>
    </a>
    <a href="{{ route('gig.board') }}" class="fcard">
      <div class="fc-ic">🎪</div>
      <div class="fc-t">Papan Gig</div>
      <div class="fc-d">Audisi, session player, open mic & rekaman — peluang nasional.</div>
      <div class="fc-link">Lihat gig →</div>
    </a>
    <a href="{{ route('tools.epk') }}" class="fcard">
      <div class="fc-ic">📄</div>
      <div class="fc-t">EPK Generator</div>
      <div class="fc-d">Buat Electronic Press Kit profesional untuk dikirim ke label & promotor.</div>
      <div class="fc-link">Buat EPK →</div>
    </a>
    <a href="{{ route('library.materi') }}" class="fcard">
      <div class="fc-ic">📚</div>
      <div class="fc-t">Materi Musik</div>
      <div class="fc-d">31 artikel lengkap: teori, karir, produksi, dan bisnis musik indie.</div>
      <div class="fc-link">Baca materi →</div>
    </a>
    <a href="{{ route('google.login') }}" class="fcard">
      <div class="fc-ic">👥</div>
      <div class="fc-t">Direktori Musisi</div>
      <div class="fc-d">Temukan kolaborator, session player, dan personil band by kota & genre.</div>
      <div class="fc-link">Cari musisi →</div>
    </a>
  </div>

  {{-- Roadmap --}}
  <div class="rm-row rv">
    <div class="rm-head">Yang sudah ada</div>
    <div class="rm-pills">
      @foreach(['Profil Musisi','Chat & Grup','Timeline','Follow','Chord Builder','Guitar Tuner','BPM','Kalkulator Royalti','Papan Gig','EPK Generator','Release Planner','31 Materi'] as $d)
      <span class="rp done"><span class="rp-dot"></span>{{ $d }}</span>
      @endforeach
    </div>
    <div class="rm-head">Coming Soon</div>
    <div class="rm-pills">
      @foreach(['Marketplace Session','Studio Finder'] as $s)
      <span class="rp soon"><span class="rp-dot"></span>{{ $s }}</span>
      @endforeach
    </div>
  </div>

  {{-- CTA --}}
  <div class="cta-wrap rv">
    <div class="aurora" style="opacity:.5"></div>
    <h2 class="cta-h" style="position:relative;z-index:1">Masih Berkarya Sendirian?</h2>
    <p class="cta-sub" style="position:relative;z-index:1">
      Bergabunglah bersama musisi Indonesia yang percaya<br>
      karya hebat tidak ditentukan oleh tempat lahir.
    </p>
    <div style="position:relative;z-index:1">
      <button class="cta-btn" onclick="openModal()">
        <div class="cta-line"></div>
        <div class="cta-inner">🎵 &nbsp; MULAI PERJALANAN MUSIKMU</div>
        <div class="cta-line"></div>
      </button>
    </div>
  </div>
</section>

{{-- ════════ FOOTER ════════ --}}
<footer>
  <div class="fwave" aria-hidden="true">
    <svg viewBox="0 0 1440 48" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,24 C240,6 480,42 720,24 C960,6 1200,42 1440,24" stroke="rgba(56,168,204,.18)" stroke-width="1.5"/>
      <path d="M0,28 C180,48 360,8 540,28 C720,48 900,8 1080,28 C1260,48 1380,12 1440,28" stroke="rgba(91,110,245,.1)" stroke-width="1"/>
      <path d="M0,20 C120,4 240,38 360,20 C480,4 600,38 720,20 C840,4 960,38 1080,20 C1200,4 1320,38 1440,20" stroke="rgba(139,92,246,.07)" stroke-width="1"/>
    </svg>
  </div>
  <div class="flogo">E<span>MINOR</span></div>
  <p class="ftag">Ekosistem Musik Indie Indonesia<br>Rumah pertama bagi musisi yang sedang tumbuh sendirian.</p>
  <div class="fdiv"></div>
  <p class="fpoem">
    Karena setiap lagu...<br>
    <em>selalu dimulai</em><br>
    <em>oleh seseorang yang berani</em><br>
    <em>memainkan chord pertama.</em>
  </p>
  <div class="flinks">
    <a href="{{ route('tools.index') }}">Alat Musisi</a>
    <a href="{{ route('gig.board') }}">Papan Gig</a>
    <a href="{{ route('library.materi') }}">Materi</a>
    <a href="{{ route('library') }}">Diskografi</a>
    <a href="{{ route('google.login') }}">Masuk</a>
  </div>
  <p class="fcopy">© {{ date('Y') }} EMINOR — Ekosistem Musik Indie Indonesia · margonoandi.my.id</p>
</footer>

{{-- ════════ MODAL ════════ --}}
<div id="mbg" onclick="if(event.target===this)closeModal()">
  <div class="mbox">
    <button class="mclose" onclick="closeModal()">✕</button>
    <div class="mh">Selamat datang. 👋</div>
    <p class="ms">Apa yang sedang kamu cari?</p>
    <div class="mopts">
      @foreach(['Belajar gitar','Mencari band','Mencari personil','Menulis lagu','Membagikan karya','Bertemu musisi'] as $o)
      <label class="mopt"><input type="radio" name="intent" value="{{ $loop->index }}">{{ $o }}</label>
      @endforeach
    </div>
    <a href="{{ route('google.login') }}" class="mlogin">
      <svg width="17" height="17" viewBox="0 0 18 18" fill="none"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
      Lanjutkan dengan Google
    </a>
  </div>
</div>

<script>
// ── INTRO ──
(function(){
  if(sessionStorage.getItem('eminor_i')){ document.getElementById('intro').style.display='none'; startHero(); return; }

  var AC = window.AudioContext||window.webkitAudioContext;
  function tick(){
    if(!AC) return;
    var c=new AC(),o=c.createOscillator(),g=c.createGain();
    o.connect(g);g.connect(c.destination);
    o.frequency.value=900;o.type='sine';
    g.gain.setValueAtTime(.12,c.currentTime);
    g.gain.exponentialRampToValueAtTime(.001,c.currentTime+.09);
    o.start();o.stop(c.currentTime+.11);
    setTimeout(function(){c.close();},200);
  }
  var di=0,ds=[document.getElementById('d1'),document.getElementById('d2'),document.getElementById('d3')];
  function pd(){var d=ds[di%3];di++;d.style.animation='none';d.offsetHeight;d.style.animation='dpop .4s ease forwards';}

  var seq=[
    [0,   function(){tick();pd();}],
    [480, function(){tick();pd();}],
    [960, function(){tick();pd();}],
    [1200,function(){sh('l1');}],
    [1700,function(){sh('l2');}],
    [2700,function(){hh('l1');hh('l2');sh('l3');}],
    [3200,function(){sh('l4');}],
    [4300,function(){hh('l3');hh('l4');document.getElementById('ilogo').classList.add('s');document.getElementById('itag').classList.add('s');}],
    [5500,function(){done();}],
  ];
  function sh(id){document.getElementById(id).classList.add('s');}
  function hh(id){document.getElementById(id).classList.remove('s');}
  function done(){
    var el=document.getElementById('intro');
    el.classList.add('out');
    setTimeout(function(){el.style.display='none';startHero();},850);
    sessionStorage.setItem('eminor_i','1');
  }
  seq.forEach(function(s){setTimeout(s[1],s[0]);});
})();

function skipIntro(){
  sessionStorage.setItem('eminor_i','1');
  var el=document.getElementById('intro');
  el.classList.add('out');
  setTimeout(function(){el.style.display='none';startHero();},500);
}

// ── HERO TEXT LOOP (infinite) ──
function startHero(){
  // hold = ms the text stays visible AFTER fade-in
  var FADE = 800;   // matches CSS transition duration
  var GAP  = 280;   // silent gap between items
  var items = [
    {id:'hf0', hold: 700},   // Dulu...
    {id:'hf1', hold:1600},   // musisi membutuhkan label...
    {id:'hf2', hold: 700},   // Sekarang...
    {id:'hf3', hold:1600},   // yang dibutuhkan...
    {id:'hf4', hold:1600},   // Em
    {id:'hf5', hold:1600},   // Tidak semua ... kota besar
    {id:'hf6', hold:1600},   // Tidak semua ... studio
    {id:'hf7', hold:2400},   // Tidak semua ... koneksi
    {id:'hf8', hold:3200},   // Tetapi semua musisi pantas didengar
    {id:'hf9', hold:3200},   // EMINOR adalah rumah pertama...
  ];

  var idx = 0;
  var ctaShown = false;

  function showItem(){
    var item = items[idx];
    var el   = document.getElementById(item.id);
    if(!el) return next();

    el.classList.add('s');                         // fade in

    setTimeout(function(){
      el.classList.remove('s');                    // fade out
      setTimeout(function(){
        // show CTA once after first full cycle (hf8 = last item)
        if(!ctaShown && idx === items.length - 1){
          ctaShown = true;
          var ha = document.getElementById('hact');
          if(ha) ha.classList.add('s');
        }
        next();
      }, FADE + GAP);                              // wait fade-out + gap
    }, FADE + item.hold);                          // wait fade-in + hold
  }

  function next(){
    idx = (idx + 1) % items.length;
    showItem();
  }

  setTimeout(showItem, 200);

  // background slides
  var sl=document.querySelectorAll('.hslide'), si=0;
  setInterval(function(){
    sl[si].classList.remove('on');
    si = (si+1) % sl.length;
    sl[si].classList.add('on');
  }, 4500);
}

// ── NAVBAR ──
window.addEventListener('scroll',function(){document.getElementById('nav').classList.toggle('on',scrollY>60);});

// ── SCROLL REVEAL ──
new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting)e.target.classList.add('on');});},{threshold:.12})
  .observe === undefined || (function(){
    var obs=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting)e.target.classList.add('on');});},{threshold:.12});
    document.querySelectorAll('.rv').forEach(function(el){obs.observe(el);});
  })();

// ── STAT COUNTERS ──
(function(){
  var done=false;
  var obs=new IntersectionObserver(function(es){
    if(es[0].isIntersecting&&!done){
      done=true;
      document.querySelectorAll('.sc').forEach(function(el){
        var t=parseInt(el.dataset.t),s=0,st=null;
        (function run(ts){
          if(!st)st=ts;
          var p=Math.min((ts-st)/1200,1),e=1-Math.pow(1-p,3);
          el.textContent=Math.floor(e*t);
          if(p<1)requestAnimationFrame(run);else el.textContent=t;
        })(performance.now());
      });
      obs.disconnect();
    }
  },{threshold:.4});
  var strip=document.querySelector('.stats-strip');
  if(strip)obs.observe(strip);
})();

// ── MANIFESTO STAGGER ──
(function(){
  var done = false;
  var obs  = new IntersectionObserver(function(es){
    if(es[0].isIntersecting && !done){
      done = true;
      // eyebrow
      var ey = document.getElementById('mf-ey');
      if(ey) ey.classList.add('on');
      // animated bar draws itself
      var wrap = document.getElementById('mf-bar-wrap');
      if(wrap) setTimeout(function(){ wrap.classList.add('go'); }, 120);
      // stagger each line
      var lines = document.querySelectorAll('#mf-bar-wrap .mr');
      lines.forEach(function(el, i){
        setTimeout(function(){ el.classList.add('on'); }, 180 + i * 110);
      });
      obs.disconnect();
    }
  }, {threshold:.18});
  var sec = document.getElementById('s-mid');
  if(sec) obs.observe(sec);
})();

// ── MODAL ──
function openModal(){document.getElementById('mbg').classList.add('on');document.body.style.overflow='hidden';}
function closeModal(){document.getElementById('mbg').classList.remove('on');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeModal();});

// ── CURSOR GLOW ──
(function(){
  var g=document.getElementById('cglow'),rx=0,ry=0,cx=window.innerWidth/2,cy=window.innerHeight/2;
  document.addEventListener('mousemove',function(e){cx=e.clientX;cy=e.clientY;});
  function loop(){
    rx+=(cx-rx)*.08; ry+=(cy-ry)*.08;
    if(g){g.style.left=rx+'px';g.style.top=ry+'px';}
    requestAnimationFrame(loop);
  }
  loop();
})();

// ── 3D CARD TILT (feature grid) ──
document.querySelectorAll('.fcard').forEach(function(c){
  c.addEventListener('mousemove',function(e){
    var r=c.getBoundingClientRect();
    var x=(e.clientX-r.left)/r.width-.5;
    var y=(e.clientY-r.top)/r.height-.5;
    c.style.transform='perspective(700px) rotateY('+(x*10)+'deg) rotateX('+(-y*10)+'deg) translateY(-4px)';
    c.style.transition='transform .05s';
  });
  c.addEventListener('mouseleave',function(){
    c.style.transform='';
    c.style.transition='transform .35s ease';
  });
});

// ── MOBILE NAV ──
function toggleNav(){
  var l=document.querySelector('.nlinks'),c=document.querySelector('.ncta');
  if(!l)return;
  var open=l.style.display==='flex';
  l.style.cssText=open?'':'display:flex;flex-direction:column;position:fixed;top:60px;left:0;right:0;background:rgba(6,8,15,.96);backdrop-filter:blur(16px);padding:1.25rem 2rem;gap:1rem;border-bottom:1px solid var(--border);z-index:800';
  if(c)c.style.cssText=open?'':'display:block;margin:0 2rem 1.25rem;text-align:center';
}
</script>
</body>
</html>

