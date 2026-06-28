<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>EMINOR — Ekosistem Musik Indie Indonesia</title>
<meta name="description" content="EMINOR adalah ekosistem musik indie Indonesia — tempat belajar, berkarya, bertemu musisi, dan tumbuh bersama. Gratis untuk semua musisi Indonesia.">
<meta property="og:title" content="EMINOR — Ekosistem Musik Indie Indonesia">
<meta property="og:description" content="Ekosistem musik indie Indonesia. Belajar, berkarya, dan bertemu musisi — semua di satu tempat.">
<meta property="og:image" content="{{ asset('images/Margonoandi.jpeg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;overflow-x:hidden}
body{background:#06080f;color:#e2e8f0;font-family:'Sora',system-ui,sans-serif;line-height:1.6;overflow-x:hidden}
::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#1e3a5f;border-radius:2px}
img{max-width:100%;display:block}
a{text-decoration:none;color:inherit}

/* ── TOKENS ── */
:root{
  --bg:#06080f;
  --bg2:#0a0e1a;
  --card:rgba(14,20,40,.7);
  --border:rgba(56,168,204,.12);
  --border2:rgba(255,255,255,.06);
  --accent:#38A8CC;
  --accent2:#5B6EF5;
  --text:#e2e8f0;
  --text2:#94a3b8;
  --text3:#4a5568;
  --r:18px;
}

/* ── AURORA ── */
.aurora{position:absolute;inset:0;overflow:hidden;pointer-events:none;z-index:0}
.aurora::before,.aurora::after{content:'';position:absolute;border-radius:50%;filter:blur(80px);opacity:.18}
.aurora::before{width:700px;height:700px;background:radial-gradient(circle,#38A8CC,transparent 70%);top:-200px;left:-150px;animation:aur1 18s ease-in-out infinite alternate}
.aurora::after{width:600px;height:600px;background:radial-gradient(circle,#5B6EF5,transparent 70%);bottom:-100px;right:-100px;animation:aur2 22s ease-in-out infinite alternate}
.aurora-blob3{position:absolute;width:500px;height:500px;background:radial-gradient(circle,#8B5CF6,transparent 70%);border-radius:50%;filter:blur(90px);opacity:.1;bottom:20%;left:30%;animation:aur3 26s ease-in-out infinite alternate}
@keyframes aur1{0%{transform:translate(0,0) scale(1)}100%{transform:translate(80px,60px) scale(1.2)}}
@keyframes aur2{0%{transform:translate(0,0) scale(1)}100%{transform:translate(-60px,-40px) scale(1.15)}}
@keyframes aur3{0%{transform:translate(0,0)}100%{transform:translate(40px,-50px)}}

/* ── INTRO OVERLAY ── */
#intro{position:fixed;inset:0;background:#020307;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity .8s ease}
#intro.fade-out{opacity:0;pointer-events:none}
.intro-skip{position:absolute;top:1.5rem;right:1.5rem;font-size:11px;color:rgba(255,255,255,.35);letter-spacing:.1em;cursor:pointer;border:1px solid rgba(255,255,255,.12);padding:5px 13px;border-radius:20px;transition:.2s}
.intro-skip:hover{color:#fff;border-color:rgba(255,255,255,.4)}
.intro-metro{display:flex;gap:10px;align-items:center;height:32px;margin-bottom:2.5rem}
.metro-dot{width:7px;height:7px;background:#38A8CC;border-radius:50%;opacity:0;transform:scale(0)}
@keyframes metro-pop{0%,100%{opacity:0;transform:scale(0)}30%,70%{opacity:1;transform:scale(1)}}
.intro-text{text-align:center;min-height:100px}
.intro-line{font-size:clamp(1rem,3vw,1.35rem);font-weight:300;color:rgba(255,255,255,.9);letter-spacing:.04em;opacity:0;transition:opacity .7s ease}
.intro-line.show{opacity:1}
.intro-logo{font-size:clamp(2rem,6vw,3rem);font-weight:700;letter-spacing:.12em;color:#fff;opacity:0;transition:opacity 1s ease;margin-top:2rem}
.intro-logo span{color:#38A8CC}
.intro-logo.show{opacity:1}
.intro-tagline{font-size:13px;color:rgba(255,255,255,.4);letter-spacing:.08em;opacity:0;transition:opacity 1s .3s ease;margin-top:.5rem;text-align:center}
.intro-tagline.show{opacity:1}

/* ── NAVBAR ── */
nav{position:fixed;top:0;left:0;right:0;z-index:900;padding:0 2rem;height:64px;display:flex;align-items:center;justify-content:space-between;transition:background .4s,border .4s}
nav.scrolled{background:rgba(6,8,15,.88);backdrop-filter:blur(14px);border-bottom:1px solid var(--border)}
.nav-logo{font-size:1.15rem;font-weight:700;letter-spacing:.1em;color:#fff}
.nav-logo span{color:#38A8CC}
.nav-links{display:flex;gap:2rem;list-style:none}
.nav-links a{font-size:13px;color:var(--text2);letter-spacing:.05em;transition:.2s}
.nav-links a:hover{color:#fff}
.nav-cta{background:#38A8CC;color:#fff;font-size:12px;font-weight:600;padding:9px 22px;border-radius:50px;letter-spacing:.06em;transition:.2s;white-space:nowrap}
.nav-cta:hover{background:#2d8fad;transform:translateY(-1px)}
.nav-mobile-toggle{display:none;background:none;border:none;color:#fff;font-size:1.4rem;cursor:pointer;padding:4px}

/* ── SECTION BASE ── */
section{position:relative;overflow:hidden}

/* ── S1: HERO ── */
#s-hero{min-height:100vh;display:flex;flex-direction:column;justify-content:flex-end;padding:0 3rem 5rem}
.hero-bg-slides{position:absolute;inset:0;z-index:0}
.hero-slide{position:absolute;inset:0;opacity:0;transition:opacity 1.5s ease;background-size:cover;background-position:center}
.hero-slide.active{opacity:1}
.hero-slide.s1{background:linear-gradient(160deg,#060a1a 0%,#0d1a2e 40%,#0a0f1e 100%)}
.hero-slide.s2{background:linear-gradient(160deg,#0a0f1e 0%,#1a0a2e 50%,#060a1a 100%)}
.hero-slide.s3{background:linear-gradient(160deg,#0a1a1e 0%,#061a1a 50%,#06080f 100%)}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(6,8,15,.9) 30%,rgba(6,8,15,.4) 70%,rgba(6,8,15,.2) 100%);z-index:1}

.hero-content{position:relative;z-index:2;max-width:760px}
.hero-fade-text{min-height:120px;margin-bottom:2rem}
.hft{font-size:clamp(1.3rem,3.5vw,1.9rem);font-weight:300;color:rgba(255,255,255,.85);letter-spacing:.03em;opacity:0;position:absolute;transition:opacity .8s ease}
.hft.show{opacity:1}
.hft.small{font-size:clamp(1.1rem,2.5vw,1.45rem);color:var(--text2)}
.hft.big{font-size:clamp(1.8rem,5vw,3rem);font-weight:700;color:#fff}
.hero-tagline{font-size:clamp(.9rem,2vw,1.05rem);color:var(--text2);line-height:1.8;margin-bottom:2.5rem;max-width:500px;opacity:0;transition:opacity .8s ease}
.hero-tagline.show{opacity:1}
.hero-actions{display:flex;gap:14px;flex-wrap:wrap;opacity:0;transition:opacity .8s ease}
.hero-actions.show{opacity:1}
.btn-hero{display:inline-flex;align-items:center;gap:8px;padding:14px 32px;border-radius:50px;font-size:14px;font-weight:600;letter-spacing:.05em;cursor:pointer;transition:.2s;border:none}
.btn-hero-primary{background:linear-gradient(135deg,#38A8CC,#2186a8);color:#fff;box-shadow:0 8px 30px rgba(56,168,204,.3)}
.btn-hero-primary:hover{transform:translateY(-2px);box-shadow:0 12px 40px rgba(56,168,204,.45)}
.btn-hero-ghost{background:rgba(255,255,255,.06);color:var(--text2);border:1px solid var(--border2)}
.btn-hero-ghost:hover{background:rgba(255,255,255,.1);color:#fff}
.hero-scroll{position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:8px;z-index:2}
.hero-scroll span{font-size:10px;letter-spacing:.2em;color:var(--text3);text-transform:uppercase}
.scroll-arrow{width:1px;height:40px;background:linear-gradient(to bottom,transparent,#38A8CC);animation:scrollpulse 2s ease-in-out infinite}
@keyframes scrollpulse{0%,100%{opacity:.3;transform:scaleY(.8)}50%{opacity:1;transform:scaleY(1)}}

/* ── S2: CARDS ── */
#s-cards{background:#fff;padding:6rem 2rem}
.s-title{text-align:center;font-size:clamp(1.4rem,3vw,2rem);font-weight:700;color:#06080f;margin-bottom:.75rem}
.s-sub{text-align:center;font-size:14px;color:#64748b;margin-bottom:3rem}
.cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;max-width:1000px;margin:0 auto}
.card-item{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:20px;padding:1.75rem 1.5rem;cursor:pointer;transition:.25s;text-align:left}
.card-item:hover{transform:translateY(-6px);box-shadow:0 20px 50px rgba(56,168,204,.15);border-color:#38A8CC}
.card-icon{font-size:2rem;margin-bottom:.9rem}
.card-title{font-size:15px;font-weight:700;color:#0f172a;margin-bottom:.5rem}
.card-divider{width:32px;height:2px;background:#38A8CC;border-radius:2px;margin:.6rem 0}
.card-tags{display:flex;flex-direction:column;gap:3px}
.card-tag{font-size:12px;color:#64748b}

/* ── S3: TIMELINE ── */
#s-timeline{background:#06080f;padding:7rem 2rem}
#s-timeline .s-title{color:#fff}
.timeline{position:relative;max-width:480px;margin:3rem auto 0;padding-left:2rem}
.timeline::before{content:'';position:absolute;left:0;top:0;width:2px;height:0;background:linear-gradient(to bottom,#38A8CC,#5B6EF5,#8B5CF6);border-radius:2px;transition:height 1.5s ease}
.timeline.animate::before{height:100%}
.tl-item{position:relative;padding:0 0 2.5rem 2rem;opacity:0;transform:translateX(-12px);transition:.5s ease}
.tl-item.show{opacity:1;transform:none}
.tl-dot{position:absolute;left:-2.45rem;top:2px;width:18px;height:18px;border-radius:50%;background:#06080f;border:2px solid #38A8CC;display:flex;align-items:center;justify-content:center;font-size:10px;transition:.3s}
.tl-item.show .tl-dot{background:#38A8CC;border-color:#38A8CC;box-shadow:0 0 12px rgba(56,168,204,.5)}
.tl-icon{font-size:1.2rem;margin-bottom:.3rem}
.tl-label{font-size:15px;font-weight:600;color:#fff}
.tl-sub{font-size:12px;color:var(--text2);margin-top:.2rem}

/* ── S4: MANIFESTO ── */
#s-manifesto{background:#020307;padding:9rem 2rem;text-align:center}
.manifesto-text{max-width:640px;margin:0 auto;position:relative;z-index:1}
.manifesto-eyebrow{font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:#38A8CC;margin-bottom:2rem}
.manifesto-line{font-size:clamp(1.1rem,3vw,1.6rem);font-weight:300;color:rgba(255,255,255,.85);line-height:1.9;margin-bottom:.5rem}
.manifesto-line.accent{font-size:clamp(1.3rem,3.5vw,1.9rem);font-weight:600;color:#fff}
.manifesto-line.highlight{color:#38A8CC}
.manifesto-spacer{height:2rem}

/* ── S5: LIVE ── */
#s-live{background:#06080f;padding:6rem 2rem}
#s-live .s-title{color:#fff}
.live-badge{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:rgba(56,168,204,.1);border:1px solid rgba(56,168,204,.25);color:#38A8CC;padding:4px 14px;border-radius:20px;margin-bottom:1.25rem}
.live-dot{width:6px;height:6px;border-radius:50%;background:#38A8CC;animation:livepulse 1.5s ease-in-out infinite}
@keyframes livepulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
.live-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;max-width:900px;margin:2rem auto 0}
.live-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:1.1rem 1.25rem;display:flex;gap:14px;align-items:flex-start;animation:slideup .4s ease}
@keyframes slideup{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.live-icon{font-size:1.4rem;flex-shrink:0;margin-top:2px}
.live-user{font-size:13px;font-weight:600;color:#fff}
.live-desc{font-size:12px;color:var(--text2);margin-top:2px;line-height:1.5}
.live-time{font-size:10px;color:var(--text3);margin-top:5px}

/* ── S6: FITUR / PHONES ── */
#s-fitur{background:#0a0e1a;padding:7rem 2rem}
#s-fitur .s-title{color:#fff}
.phones-row{display:flex;justify-content:center;align-items:flex-end;gap:2rem;margin:3.5rem 0;flex-wrap:wrap}
.phone-col{display:flex;flex-direction:column;align-items:center;gap:1.25rem;max-width:180px}
.phone{width:130px;height:220px;border:2.5px solid rgba(56,168,204,.4);border-radius:24px;background:rgba(14,20,40,.8);position:relative;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;transition:.3s}
.phone:hover{border-color:#38A8CC;box-shadow:0 0 30px rgba(56,168,204,.2);transform:translateY(-4px)}
.phone::before{content:'';position:absolute;top:10px;width:40px;height:4px;background:rgba(56,168,204,.25);border-radius:2px}
.phone::after{content:'';position:absolute;bottom:10px;width:20px;height:20px;border:2px solid rgba(56,168,204,.2);border-radius:50%}
.phone-emoji{font-size:1.8rem}
.phone-label-main{font-size:13px;font-weight:700;color:#fff;letter-spacing:.05em}
.phone-section-label{font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#38A8CC}
.phone-desc{font-size:12px;color:var(--text2);text-align:center;line-height:1.6}
.phone-arrow{font-size:1.5rem;color:rgba(56,168,204,.4);align-self:center;padding-bottom:3rem}
.fitur-tagline{text-align:center;font-size:15px;color:var(--text2);max-width:420px;margin:0 auto;line-height:1.8}
.fitur-tagline strong{color:#fff}

/* ── S7: DREAM ── */
#s-dream{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:7rem 2rem;background:linear-gradient(160deg,#020307 0%,#06080f 50%,#020307 100%);position:relative}
.dream-content{text-align:center;position:relative;z-index:1;max-width:600px}
.dream-eyebrow{font-size:13px;color:var(--text2);letter-spacing:.1em;margin-bottom:2.5rem;font-style:italic}
.dream-line{font-size:clamp(1.1rem,3vw,1.5rem);color:rgba(255,255,255,.75);line-height:1.9;font-weight:300}
.dream-line.bold{font-weight:600;color:#fff;font-size:clamp(1.2rem,3.5vw,1.7rem)}
.dream-pause{height:2rem}
.btn-dream{display:inline-block;margin-top:3rem;padding:18px 48px;background:linear-gradient(135deg,#38A8CC,#5B6EF5);color:#fff;border-radius:50px;font-size:15px;font-weight:700;letter-spacing:.08em;transition:.25s;box-shadow:0 12px 40px rgba(56,168,204,.3)}
.btn-dream:hover{transform:translateY(-3px);box-shadow:0 18px 50px rgba(56,168,204,.5)}

/* ── S8: ROADMAP ── */
#s-roadmap{background:#06080f;padding:6rem 2rem}
#s-roadmap .s-title{color:#fff}
.roadmap-wrap{max-width:640px;margin:2.5rem auto 0;display:grid;grid-template-columns:1fr 1fr;gap:2rem}
.rm-col-title{font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--text3);font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:8px}
.rm-col-title::after{content:'';flex:1;height:1px;background:var(--border)}
.rm-item{display:flex;align-items:center;gap:10px;font-size:13px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04);color:var(--text2)}
.rm-check{width:18px;height:18px;border-radius:50%;background:rgba(56,168,204,.15);border:1px solid rgba(56,168,204,.3);display:flex;align-items:center;justify-content:center;font-size:9px;color:#38A8CC;flex-shrink:0}
.rm-soon{width:18px;height:18px;border-radius:50%;background:rgba(139,92,246,.12);border:1px dashed rgba(139,92,246,.3);flex-shrink:0}
.rm-item.done{color:var(--text)}
.rm-badge{font-size:9px;background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.3);color:#a78bfa;padding:2px 8px;border-radius:10px;margin-left:auto;white-space:nowrap}

/* ── S9: CTA FINAL ── */
#s-cta{padding:8rem 2rem;text-align:center;position:relative}
#s-cta .s-title{color:#fff;font-size:clamp(1.5rem,4vw,2.4rem)}
.cta-sub{font-size:15px;color:var(--text2);max-width:460px;margin:1rem auto 3rem;line-height:1.8}
.btn-cta-main{display:inline-flex;flex-direction:column;align-items:center;cursor:pointer;background:none;border:none;gap:0}
.btn-cta-border{width:100%;height:2px;background:linear-gradient(90deg,transparent,#38A8CC,transparent);border-radius:2px}
.btn-cta-inner{display:flex;align-items:center;gap:12px;padding:22px 48px;background:rgba(56,168,204,.08);border:1px solid rgba(56,168,204,.3);border-top:none;border-bottom:none;font-size:clamp(14px,2vw,17px);font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#fff;transition:.3s}
.btn-cta-inner:hover,.btn-cta-main:hover .btn-cta-inner{background:rgba(56,168,204,.18);color:#38A8CC}
.btn-cta-main:hover .btn-cta-border{background:linear-gradient(90deg,transparent,#38A8CC 30%,#5B6EF5 70%,transparent)}

/* ── MODAL ── */
#modal-bg{position:fixed;inset:0;background:rgba(2,3,7,.85);backdrop-filter:blur(8px);z-index:9000;display:none;align-items:center;justify-content:center;padding:1rem}
#modal-bg.open{display:flex}
.modal-box{background:#0d1220;border:1px solid var(--border);border-radius:24px;padding:2.5rem 2rem;max-width:440px;width:100%;position:relative;animation:modalin .3s ease}
@keyframes modalin{from{opacity:0;transform:translateY(20px)scale(.97)}to{opacity:1;transform:none}}
.modal-close{position:absolute;top:1rem;right:1.25rem;background:none;border:none;color:var(--text3);font-size:1.3rem;cursor:pointer;transition:.2s;line-height:1}
.modal-close:hover{color:#fff}
.modal-title{font-size:1.3rem;font-weight:700;color:#fff;margin-bottom:.3rem}
.modal-sub{font-size:13px;color:var(--text2);margin-bottom:1.75rem}
.modal-options{display:flex;flex-direction:column;gap:8px;margin-bottom:1.75rem}
.modal-option{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;transition:.2s;font-size:13.5px;color:var(--text2);background:none;text-align:left;font-family:inherit}
.modal-option:hover,.modal-option.selected{border-color:#38A8CC;color:#fff;background:rgba(56,168,204,.07)}
.modal-option input{accent-color:#38A8CC}
.btn-modal-login{width:100%;padding:14px;border-radius:50px;background:linear-gradient(135deg,#38A8CC,#2186a8);color:#fff;font-size:14px;font-weight:700;letter-spacing:.06em;border:none;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:10px}
.btn-modal-login:hover{opacity:.9;transform:translateY(-1px)}
.modal-divider{display:flex;align-items:center;gap:10px;margin:1rem 0;color:var(--text3);font-size:12px}
.modal-divider::before,.modal-divider::after{content:'';flex:1;height:1px;background:var(--border)}

/* ── FOOTER ── */
footer{background:#020307;padding:5rem 2rem 3rem;text-align:center;border-top:1px solid var(--border)}
.footer-logo{font-size:1.5rem;font-weight:700;letter-spacing:.15em;color:#fff;margin-bottom:.5rem}
.footer-logo span{color:#38A8CC}
.footer-tagline{font-size:14px;color:var(--text2);margin-bottom:2.5rem;line-height:1.7}
.footer-divider{width:48px;height:1px;background:linear-gradient(90deg,transparent,#38A8CC,transparent);margin:2rem auto}
.footer-poem{font-size:13px;color:var(--text3);line-height:2.2;max-width:360px;margin:0 auto 2rem}
.footer-poem em{color:var(--text2);font-style:normal}
.footer-links{display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;margin-bottom:2rem}
.footer-links a{font-size:12px;color:var(--text3);letter-spacing:.06em;transition:.2s}
.footer-links a:hover{color:#38A8CC}
.footer-copy{font-size:11px;color:var(--text3)}

/* ── OBSERVER ANIMATIONS ── */
.reveal{opacity:0;transform:translateY(24px);transition:.7s ease}
.reveal.visible{opacity:1;transform:none}

/* ── S-VISI: VISION & MISSION ── */
#s-visi{background:#030510;padding:9rem 2rem;position:relative;overflow:hidden}
#visi-canvas{position:absolute;inset:0;pointer-events:none;z-index:0}
.visi-inner{position:relative;z-index:1;max-width:900px;margin:0 auto;text-align:center}
.visi-eyebrow{font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:#38A8CC;margin-bottom:1rem;opacity:0;transform:translateY(16px);transition:.6s ease}
.visi-eyebrow.in{opacity:1;transform:none}
.visi-headline{font-size:clamp(1.5rem,4vw,2.6rem);font-weight:700;color:#fff;min-height:1.4em;margin-bottom:3.5rem}
.visi-cursor{display:inline-block;width:3px;height:1em;background:#38A8CC;vertical-align:middle;margin-left:4px;animation:blink .7s step-end infinite}
@keyframes blink{50%{opacity:0}}

/* three pillars */
.visi-pillars{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:4rem}
.visi-pillar{background:rgba(14,20,40,.6);border:1px solid rgba(56,168,204,.1);border-radius:24px;padding:2.2rem 1.5rem 1.75rem;opacity:0;transition:.7s cubic-bezier(.22,1,.36,1);position:relative;overflow:hidden}
.visi-pillar::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 50% 0%,rgba(56,168,204,.06),transparent 65%);pointer-events:none}
.visi-pillar.from-left{transform:translateX(-70px)}
.visi-pillar.from-bottom{transform:translateY(50px)}
.visi-pillar.from-right{transform:translateX(70px)}
.visi-pillar.in{opacity:1;transform:none}
.visi-pillar:hover{border-color:rgba(56,168,204,.35);transform:translateY(-5px) !important;box-shadow:0 24px 60px rgba(56,168,204,.1)}
.visi-p-icon{font-size:2.4rem;margin-bottom:1rem}
.visi-p-title{font-size:13px;font-weight:800;letter-spacing:.2em;color:#38A8CC;margin-bottom:.75rem;text-transform:uppercase}
.visi-p-bar{height:2px;background:rgba(56,168,204,.15);border-radius:2px;margin-bottom:1rem;position:relative;overflow:hidden}
.visi-p-bar::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,#38A8CC,#5B6EF5);transform:scaleX(0);transform-origin:left;transition:transform .9s .3s cubic-bezier(.22,1,.36,1)}
.visi-pillar.in .visi-p-bar::after{transform:scaleX(1)}
.visi-p-desc{font-size:13px;color:var(--text2);line-height:1.75}

/* stat counters */
.visi-stats{display:flex;justify-content:center;gap:0;margin-bottom:4rem;background:rgba(14,20,40,.5);border:1px solid rgba(56,168,204,.1);border-radius:20px;overflow:hidden;flex-wrap:wrap}
.visi-stat{flex:1;min-width:120px;padding:1.75rem 1rem;border-right:1px solid rgba(56,168,204,.08);position:relative;opacity:0;transform:translateY(20px);transition:.5s ease}
.visi-stat:last-child{border-right:none}
.visi-stat.in{opacity:1;transform:none}
.visi-num{font-size:clamp(1.8rem,5vw,2.8rem);font-weight:800;color:#fff;line-height:1;font-variant-numeric:tabular-nums}
.visi-num-suffix{font-size:1rem;color:#38A8CC;font-weight:700}
.visi-stat-label{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-top:.4rem}

/* word-split vision statement */
.visi-statement{max-width:680px;margin:0 auto;font-size:clamp(1rem,2.5vw,1.3rem);font-weight:300;color:rgba(255,255,255,.75);line-height:2;text-align:center}
.visi-word{display:inline-block;opacity:0;transform:translateY(22px);transition:.5s ease;margin:0 .18em}
.visi-word.in{opacity:1;transform:none}
.visi-word.accent{color:#38A8CC;font-weight:600}

/* ── RESPONSIVE ── */
@media(max-width:768px){
  #s-hero{padding:0 1.5rem 4rem}
  .nav-links,.nav-cta{display:none}
  .nav-mobile-toggle{display:block}
  #s-cards{padding:4rem 1.5rem}
  .cards-grid{grid-template-columns:1fr 1fr}
  .roadmap-wrap{grid-template-columns:1fr}
  .phones-row{gap:1rem}
  .phone-arrow{display:none}
  .phone{width:110px;height:185px}
  #s-hero{padding-left:1.5rem;padding-right:1.5rem}
  .btn-cta-inner{padding:18px 28px;font-size:13px}
  .manifesto-line{font-size:1rem}
  .manifesto-line.accent{font-size:1.2rem}
  .visi-pillars{grid-template-columns:1fr}
  .visi-stats{border-radius:16px}
  .visi-stat{min-width:50%}
}
@media(max-width:480px){
  .cards-grid{grid-template-columns:1fr}
  .hero-actions{flex-direction:column}
  .btn-hero{justify-content:center}
}
</style>
</head>
<body>

<!-- ─────────────────────────────────────────────
     INTRO OVERLAY
───────────────────────────────────────────── -->
<div id="intro">
  <button class="intro-skip" onclick="skipIntro()">SKIP ↗</button>
  <div class="intro-metro">
    <div class="metro-dot" id="m1"></div>
    <div class="metro-dot" id="m2"></div>
    <div class="metro-dot" id="m3"></div>
  </div>
  <div class="intro-text">
    <div class="intro-line" id="il1">Dulu...</div>
    <div class="intro-line" id="il2" style="color:var(--text2);font-size:1rem">musisi membutuhkan label</div>
    <div class="intro-line" id="il3" style="color:var(--text2);font-size:1rem">untuk didengar.</div>
    <div class="intro-line" id="il4" style="margin-top:1.5rem">Sekarang...</div>
    <div class="intro-line" id="il5" style="color:var(--text2);font-size:1rem">yang dibutuhkan</div>
    <div class="intro-line" id="il6" style="color:var(--text2);font-size:1rem">hanya tempat yang tepat.</div>
  </div>
  <div class="intro-logo" id="intro-logo">E<span>MINOR</span></div>
  <div class="intro-tagline" id="intro-tagline">Ekosistem Musik Indie Indonesia</div>
</div>

<!-- ─────────────────────────────────────────────
     NAVBAR
───────────────────────────────────────────── -->
<nav id="navbar">
  <a href="#" class="nav-logo">E<span>MINOR</span></a>
  <ul class="nav-links">
    <li><a href="#s-manifesto">Tentang</a></li>
    <li><a href="#s-fitur">Fitur</a></li>
    <li><a href="#s-live">Komunitas</a></li>
    <li><a href="#s-roadmap">Roadmap</a></li>
  </ul>
  <a href="{{ route('google.login') }}" class="nav-cta">Mulai Perjalanan</a>
  <button class="nav-mobile-toggle" onclick="toggleMobileNav()">☰</button>
</nav>

<!-- ─────────────────────────────────────────────
     S1 — HERO
───────────────────────────────────────────── -->
<section id="s-hero">
  <div class="aurora"><div class="aurora-blob3"></div></div>
  <div class="hero-bg-slides">
    <div class="hero-slide s1 active"></div>
    <div class="hero-slide s2"></div>
    <div class="hero-slide s3"></div>
  </div>
  <div class="hero-overlay"></div>

  <div class="hero-content">
    <div class="hero-fade-text" style="position:relative;min-height:110px">
      <div class="hft small" id="hf1">Tidak semua musisi lahir di kota besar.</div>
      <div class="hft small" id="hf2">Tidak semua musisi punya studio.</div>
      <div class="hft small" id="hf3">Tidak semua musisi punya koneksi.</div>
      <div class="hft big"   id="hf4">Tetapi semua musisi<br>pantas didengar.</div>
    </div>

    <p class="hero-tagline" id="hero-tagline">
      EMINOR — Ekosistem Musik Indie Indonesia.<br>
      bagi musisi Indonesia<br>
      yang sedang tumbuh sendirian.
    </p>

    <div class="hero-actions" id="hero-actions">
      <a href="{{ route('google.login') }}" class="btn-hero btn-hero-primary">
        🎵 Mulai Perjalanan Musik
      </a>
      <a href="#s-manifesto" class="btn-hero btn-hero-ghost">
        ▶ Lihat Kisah Kami
      </a>
    </div>
  </div>

  <div class="hero-scroll">
    <span>Scroll</span>
    <div class="scroll-arrow"></div>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S2 — APA YANG KAMU CARI?
───────────────────────────────────────────── -->
<section id="s-cards">
  <h2 class="s-title">Apa yang sedang kamu cari?</h2>
  <p class="s-sub">Pilih perjalananmu — dan mulai dari sana.</p>
  <div class="cards-grid">
    <div class="card-item" onclick="openModal()">
      <div class="card-icon">🎸</div>
      <div class="card-title">Belajar Gitar</div>
      <div class="card-divider"></div>
      <div class="card-tags">
        <span class="card-tag">Chord Builder</span>
        <span class="card-tag">Tuner Online</span>
        <span class="card-tag">Latihan BPM</span>
      </div>
    </div>
    <div class="card-item" onclick="openModal()">
      <div class="card-icon">🎤</div>
      <div class="card-title">Menulis Lagu</div>
      <div class="card-divider"></div>
      <div class="card-tags">
        <span class="card-tag">Catatan Lirik</span>
        <span class="card-tag">Inspirasi</span>
        <span class="card-tag">AI Assistant</span>
      </div>
    </div>
    <div class="card-item" onclick="openModal()">
      <div class="card-icon">🥁</div>
      <div class="card-title">Mencari Band</div>
      <div class="card-divider"></div>
      <div class="card-tags">
        <span class="card-tag">Cari Personil</span>
        <span class="card-tag">Audisi</span>
        <span class="card-tag">Kolaborasi</span>
      </div>
    </div>
    <div class="card-item" onclick="openModal()">
      <div class="card-icon">❤️</div>
      <div class="card-title">Membagikan Karya</div>
      <div class="card-divider"></div>
      <div class="card-tags">
        <span class="card-tag">Upload Lagu</span>
        <span class="card-tag">Feedback</span>
        <span class="card-tag">Fans</span>
      </div>
    </div>
    <div class="card-item" onclick="openModal()">
      <div class="card-icon">🌎</div>
      <div class="card-title">Komunitas</div>
      <div class="card-divider"></div>
      <div class="card-tags">
        <span class="card-tag">Teman Baru</span>
        <span class="card-tag">Chat</span>
        <span class="card-tag">Diskusi</span>
      </div>
    </div>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S3 — TIMELINE
───────────────────────────────────────────── -->
<section id="s-timeline">
  <div class="aurora" style="opacity:.4"><div class="aurora-blob3" style="opacity:.07"></div></div>
  <h2 class="s-title reveal">Perjalanan Seorang Musisi</h2>
  <div class="timeline reveal" id="timeline">
    <div class="tl-item"><div class="tl-dot">🎸</div><div class="tl-icon">🎸</div><div class="tl-label">Belajar</div><div class="tl-sub">Chord pertama. Nada pertama.</div></div>
    <div class="tl-item"><div class="tl-dot">✍</div><div class="tl-icon">✍</div><div class="tl-label">Menulis</div><div class="tl-sub">Lagu pertama yang jujur.</div></div>
    <div class="tl-item"><div class="tl-dot">🎤</div><div class="tl-icon">🎤</div><div class="tl-label">Upload Lagu</div><div class="tl-sub">Karya yang akhirnya ada di dunia.</div></div>
    <div class="tl-item"><div class="tl-dot">👥</div><div class="tl-icon">👥</div><div class="tl-label">Bertemu Musisi</div><div class="tl-sub">Kolaborator, teman, circle baru.</div></div>
    <div class="tl-item"><div class="tl-dot">🥁</div><div class="tl-icon">🥁</div><div class="tl-label">Band Terbentuk</div><div class="tl-sub">Suara yang lebih besar dari satu orang.</div></div>
    <div class="tl-item"><div class="tl-dot">🎶</div><div class="tl-icon">🎶</div><div class="tl-label">Manggung</div><div class="tl-sub">Panggung pertama. Gemetar. Bahagia.</div></div>
    <div class="tl-item" style="padding-bottom:0"><div class="tl-dot" style="background:#5B6EF5;border-color:#5B6EF5;box-shadow:0 0 12px rgba(91,110,245,.5)">❤️</div><div class="tl-icon">❤️</div><div class="tl-label">Didengar Banyak Orang</div><div class="tl-sub">Tujuan yang selalu layak diperjuangkan.</div></div>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S4 — MANIFESTO
───────────────────────────────────────────── -->
<section id="s-manifesto">
  <div class="aurora"></div>
  <div class="manifesto-text">
    <div class="manifesto-eyebrow reveal">Kami Percaya</div>
    <p class="manifesto-line reveal">Bakat tidak memilih tempat lahir.</p>
    <p class="manifesto-line reveal">Musik tidak memilih kota.</p>
    <div class="manifesto-spacer"></div>
    <p class="manifesto-line accent reveal">Lagu yang hebat</p>
    <p class="manifesto-line accent reveal">bisa lahir</p>
    <p class="manifesto-line accent highlight reveal">di kamar berukuran 3×3 meter.</p>
    <div class="manifesto-spacer"></div>
    <p class="manifesto-line reveal" style="color:var(--text2)">Yang dibutuhkan hanyalah</p>
    <p class="manifesto-line reveal" style="color:var(--text2)">tempat untuk bertemu.</p>
    <div class="manifesto-spacer"></div>
    <p class="manifesto-line accent reveal">Dan EMINOR ingin menjadi tempat itu.</p>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S4.5 — VISI & MISI
───────────────────────────────────────────── -->
<section id="s-visi">
  <canvas id="visi-canvas"></canvas>
  <div class="visi-inner">

    <div class="visi-eyebrow" id="visi-ey">MENGAPA EMINOR</div>
    <h2 class="visi-headline"><span id="visi-typed"></span><span class="visi-cursor" id="visi-cur"></span></h2>

    <div class="visi-pillars">
      <div class="visi-pillar from-left" id="vp1">
        <div class="visi-p-icon">🎸</div>
        <div class="visi-p-title">Belajar</div>
        <div class="visi-p-bar"></div>
        <div class="visi-p-desc">Dari chord pertama hingga teknik lanjutan — materi, tuner, dan 14 alat musik semuanya gratis, langsung di browser.</div>
      </div>
      <div class="visi-pillar from-bottom" id="vp2">
        <div class="visi-p-icon">🌍</div>
        <div class="visi-p-title">Berkarya</div>
        <div class="visi-p-bar"></div>
        <div class="visi-p-desc">Upload lagu, bagikan perjalanan, dan dapatkan feedback jujur dari musisi lain yang mengerti rasanya berkarya sendirian.</div>
      </div>
      <div class="visi-pillar from-right" id="vp3">
        <div class="visi-p-icon">👥</div>
        <div class="visi-p-title">Bertemu</div>
        <div class="visi-p-bar"></div>
        <div class="visi-p-desc">Temukan kolaborator, personil band, dan peluang gig — matchmaking by peran, genre, dan kota.</div>
      </div>
    </div>

    <div class="visi-stats" id="visi-stats">
      <div class="visi-stat" id="vs1"><div class="visi-num"><span class="visi-counter" data-target="50">0</span><span class="visi-num-suffix">+</span></div><div class="visi-stat-label">Musisi Aktif</div></div>
      <div class="visi-stat" id="vs2"><div class="visi-num"><span class="visi-counter" data-target="14">0</span></div><div class="visi-stat-label">Alat Gratis</div></div>
      <div class="visi-stat" id="vs3"><div class="visi-num"><span class="visi-counter" data-target="31">0</span></div><div class="visi-stat-label">Materi Musik</div></div>
      <div class="visi-stat" id="vs4"><div class="visi-num">24<span class="visi-num-suffix">/7</span></div><div class="visi-stat-label">Komunitas</div></div>
    </div>

    <div class="visi-statement" id="visi-stmt"></div>

  </div>
</section>

<!-- ─────────────────────────────────────────────
     S5 — LIVE COMMUNITY
───────────────────────────────────────────── -->
<section id="s-live">
  <div style="text-align:center">
    <div class="live-badge"><div class="live-dot"></div> Live Terus</div>
    <h2 class="s-title" style="color:#fff">Hari Ini di EMINOR</h2>
    <p class="s-sub" style="color:var(--text2)">Bergabung setiap hari — musisi sepertimu selalu aktif di sini.</p>
  </div>

  <div class="live-grid" id="live-feed">
    @forelse($liveActivity as $act)
    <div class="live-card">
      <div class="live-icon">{{ $act['icon'] }}</div>
      <div>
        <div class="live-user">{{ $act['user'] }}</div>
        <div class="live-desc">{{ $act['text'] }}</div>
        <div class="live-time">{{ $act['time'] }}</div>
      </div>
    </div>
    @empty
    <div class="live-card"><div class="live-icon">🎤</div><div><div class="live-user">Fajar</div><div class="live-desc">Baru upload lagu perdana</div><div class="live-time">2 menit lalu</div></div></div>
    <div class="live-card"><div class="live-icon">🥁</div><div><div class="live-user">Rama</div><div class="live-desc">Sedang mencari drummer di Yogyakarta</div><div class="live-time">5 menit lalu</div></div></div>
    <div class="live-card"><div class="live-icon">🎸</div><div><div class="live-user">Rina</div><div class="live-desc">Baru belajar chord Em untuk pertama kali</div><div class="live-time">7 menit lalu</div></div></div>
    <div class="live-card"><div class="live-icon">❤️</div><div><div class="live-user">Lagu "Rindu"</div><div class="live-desc">Mendapat 56 like dari komunitas</div><div class="live-time">12 menit lalu</div></div></div>
    @endforelse
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S6 — FITUR (PHONE MOCKUPS)
───────────────────────────────────────────── -->
<section id="s-fitur">
  <div class="aurora" style="opacity:.3"></div>
  <h2 class="s-title reveal" style="color:#fff">Satu Aplikasi, Seluruh Perjalanan</h2>

  <div class="phones-row">
    <div class="phone-col reveal">
      <div class="phone-section-label">KAMU</div>
      <div class="phone">
        <div class="phone-emoji">🎸</div>
        <div class="phone-label-main">Belajar</div>
      </div>
      <div class="phone-desc">Chord, Tuner,<br>BPM, Transpose</div>
    </div>

    <div class="phone-arrow reveal">→</div>

    <div class="phone-col reveal">
      <div class="phone-section-label">KITA</div>
      <div class="phone">
        <div class="phone-emoji">🌍</div>
        <div class="phone-label-main">Berbagi</div>
      </div>
      <div class="phone-desc">Timeline, Post,<br>Gig, Komunitas</div>
    </div>

    <div class="phone-arrow reveal">→</div>

    <div class="phone-col reveal">
      <div class="phone-section-label">DIA</div>
      <div class="phone">
        <div class="phone-emoji">💬</div>
        <div class="phone-label-main">Kolaborasi</div>
      </div>
      <div class="phone-desc">Chat, Personil,<br>Session, Studio</div>
    </div>
  </div>

  <p class="fitur-tagline reveal">
    <strong>Semua perjalanan musikmu</strong><br>
    ada dalam satu aplikasi.
  </p>
</section>

<!-- ─────────────────────────────────────────────
     S7 — DREAM
───────────────────────────────────────────── -->
<section id="s-dream">
  <div class="aurora"></div>
  <div class="dream-content">
    <div class="dream-eyebrow reveal">Suatu hari nanti...</div>
    <p class="dream-line reveal">Lagu pertamamu</p>
    <p class="dream-line reveal">akan diputar</p>
    <p class="dream-line bold reveal">di perjalanan seseorang.</p>
    <div class="dream-pause"></div>
    <p class="dream-line reveal">Band pertamamu</p>
    <p class="dream-line reveal">akan tampil</p>
    <p class="dream-line bold reveal">di panggung pertamanya.</p>
    <div class="dream-pause"></div>
    <p class="dream-line reveal" style="color:var(--text2)">Dan semua itu...</p>
    <p class="dream-line bold reveal" style="color:#38A8CC">berawal dari satu tombol.</p>
    <a href="{{ route('google.login') }}" class="btn-dream reveal">Mulai Perjalanan Musik</a>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S8 — ROADMAP
───────────────────────────────────────────── -->
<section id="s-roadmap">
  <h2 class="s-title reveal" style="color:#fff">Roadmap</h2>
  <p class="s-sub reveal" style="color:var(--text2)">Yang sudah ada — dan yang sedang kami bangun.</p>
  <div class="roadmap-wrap reveal">
    <div>
      <div class="rm-col-title">Sudah ada</div>
      @foreach(['Profil Musisi','Chat & Grup','Timeline Komunitas','Follow & Koneksi','Chord Builder','Guitar Tuner','BPM Calculator','Materi Musik (39 artikel)','Papan Gig & Audisi','Kalkulator Royalti'] as $done)
      <div class="rm-item done"><div class="rm-check">✓</div>{{ $done }}</div>
      @endforeach
    </div>
    <div>
      <div class="rm-col-title">Coming Soon</div>
      @foreach(['AI Songwriter','Marketplace Session Player','Online Studio Kolaborasi','Tracking Royalti','Gig Finder Nasional','Festival Indie','Label Indie Platform'] as $soon)
      <div class="rm-item"><div class="rm-soon"></div>{{ $soon }}<span class="rm-badge">Soon</span></div>
      @endforeach
    </div>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     S9 — CTA FINAL
───────────────────────────────────────────── -->
<section id="s-cta">
  <div class="aurora"></div>
  <h2 class="s-title reveal">Masih Berkarya Sendirian?</h2>
  <p class="cta-sub reveal">
    Bergabunglah bersama musisi Indonesia<br>
    yang percaya bahwa karya hebat<br>
    tidak ditentukan oleh tempat lahir.
  </p>
  <div class="reveal">
    <button class="btn-cta-main" onclick="openModal()">
      <div class="btn-cta-border"></div>
      <div class="btn-cta-inner">🎵 &nbsp; MULAI PERJALANAN MUSIKMU</div>
      <div class="btn-cta-border"></div>
    </button>
  </div>
</section>

<!-- ─────────────────────────────────────────────
     FOOTER
───────────────────────────────────────────── -->
<footer>
  <div class="footer-logo">E<span>MINOR</span></div>
  <p class="footer-tagline">
    Rumah pertama<br>bagi musisi Indonesia<br>yang sedang tumbuh sendirian.
  </p>
  <div class="footer-divider"></div>
  <p class="footer-poem">
    Karena setiap lagu...<br>
    <em>selalu dimulai</em><br>
    <em>oleh seseorang</em><br>
    <em>yang berani memainkan chord pertama.</em>
  </p>
  <div class="footer-links">
    <a href="{{ route('tools.index') }}">Alat Musisi</a>
    <a href="{{ route('gig.board') }}">Papan Gig</a>
    <a href="{{ route('library.materi') }}">Materi</a>
    <a href="{{ route('library') }}">Diskografi</a>
    <a href="{{ route('google.login') }}">Masuk</a>
  </div>
  <p class="footer-copy">© {{ date('Y') }} EMINOR — Ekosistem Musik Indie Indonesia · margonoandi.my.id</p>
</footer>

<!-- ─────────────────────────────────────────────
     MODAL ONBOARDING
───────────────────────────────────────────── -->
<div id="modal-bg" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div class="modal-title">Selamat datang. 👋</div>
    <p class="modal-sub">Apa yang sedang kamu cari?</p>
    <div class="modal-options">
      @foreach(['Belajar gitar','Mencari band','Mencari personil','Menulis lagu','Membagikan karya','Bertemu musisi'] as $opt)
      <label class="modal-option">
        <input type="radio" name="intent" value="{{ $loop->index }}">
        {{ $opt }}
      </label>
      @endforeach
    </div>
    <a href="{{ route('google.login') }}" class="btn-modal-login">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
      Lanjutkan dengan Google
    </a>
  </div>
</div>

<script>
// ── INTRO ──
(function(){
  var seen = sessionStorage.getItem('eminor_intro');
  if (seen) { document.getElementById('intro').style.display = 'none'; initHero(); return; }

  var AudioCtx = window.AudioContext || window.webkitAudioContext;
  function tick() {
    if (!AudioCtx) return;
    var ctx = new AudioCtx();
    var osc = ctx.createOscillator();
    var gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.value = 880; osc.type = 'sine';
    gain.gain.setValueAtTime(.15, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .1);
    osc.start(); osc.stop(ctx.currentTime + .12);
    setTimeout(function(){ ctx.close(); }, 200);
  }

  var dots = [document.getElementById('m1'), document.getElementById('m2'), document.getElementById('m3')];
  var di = 0;
  function animDot() {
    var d = dots[di % 3]; di++;
    d.style.animation = 'none';
    d.offsetHeight;
    d.style.animation = 'metro-pop .4s ease forwards';
  }

  var seq = [
    [0,    function(){ tick(); animDot(); }],
    [500,  function(){ tick(); animDot(); }],
    [1000, function(){ tick(); animDot(); }],
    [1200, function(){ show('il1'); }],
    [1700, function(){ show('il2'); show('il3'); }],
    [2800, function(){ hide('il1'); hide('il2'); hide('il3'); show('il4'); }],
    [3400, function(){ show('il5'); show('il6'); }],
    [4600, function(){ hideAll(); showEl('intro-logo'); showEl('intro-tagline'); }],
    [5800, function(){ fadeOutIntro(); }],
  ];

  function show(id){ document.getElementById(id).classList.add('show'); }
  function hide(id){ document.getElementById(id).classList.remove('show'); }
  function showEl(id){ document.getElementById(id).classList.add('show'); }
  function hideAll(){
    ['il1','il2','il3','il4','il5','il6'].forEach(function(id){ hide(id); });
  }
  function fadeOutIntro(){
    var el = document.getElementById('intro');
    el.classList.add('fade-out');
    setTimeout(function(){ el.style.display='none'; initHero(); }, 900);
    sessionStorage.setItem('eminor_intro','1');
  }

  seq.forEach(function(s){ setTimeout(s[1], s[0]); });
})();

function skipIntro(){
  sessionStorage.setItem('eminor_intro','1');
  var el = document.getElementById('intro');
  el.classList.add('fade-out');
  setTimeout(function(){ el.style.display='none'; initHero(); }, 500);
}

// ── HERO TEXT SEQUENCE ──
function initHero(){
  var steps = [
    [200,  function(){ heroShow('hf1'); }],
    [1800, function(){ heroHide('hf1'); heroShow('hf2'); }],
    [3400, function(){ heroHide('hf2'); heroShow('hf3'); }],
    [5000, function(){ heroHide('hf3'); heroShow('hf4'); }],
    [6400, function(){ document.getElementById('hero-tagline').classList.add('show'); }],
    [6900, function(){ document.getElementById('hero-actions').classList.add('show'); }],
  ];
  steps.forEach(function(s){ setTimeout(s[1], s[0]); });

  // Background slide rotation
  var slides = document.querySelectorAll('.hero-slide');
  var si = 0;
  setInterval(function(){
    slides[si].classList.remove('active');
    si = (si + 1) % slides.length;
    slides[si].classList.add('active');
  }, 4000);
}

function heroShow(id){ document.getElementById(id).classList.add('show'); }
function heroHide(id){ document.getElementById(id).classList.remove('show'); }

// ── NAVBAR SCROLL ──
window.addEventListener('scroll', function(){
  document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
});

// ── SCROLL REVEAL ──
var revealObs = new IntersectionObserver(function(entries){
  entries.forEach(function(e){
    if(e.isIntersecting) e.target.classList.add('visible');
  });
}, {threshold:.15});
document.querySelectorAll('.reveal').forEach(function(el){ revealObs.observe(el); });

// ── TIMELINE ANIMATION ──
var tlObs = new IntersectionObserver(function(entries){
  if(entries[0].isIntersecting){
    var tl = document.getElementById('timeline');
    tl.classList.add('animate');
    document.querySelectorAll('.tl-item').forEach(function(item, i){
      setTimeout(function(){ item.classList.add('show'); }, i * 200);
    });
    tlObs.disconnect();
  }
}, {threshold:.2});
var tlEl = document.getElementById('timeline');
if(tlEl) tlObs.observe(tlEl);

// ── MODAL ──
function openModal(){
  document.getElementById('modal-bg').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeModal(){
  document.getElementById('modal-bg').classList.remove('open');
  document.body.style.overflow='';
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });

// ── MOBILE NAV ──
function toggleMobileNav(){
  var links = document.querySelector('.nav-links');
  var cta   = document.querySelector('.nav-cta');
  if(!links) return;
  var shown = links.style.display === 'flex';
  links.style.cssText = shown ? '' : 'display:flex;flex-direction:column;position:fixed;top:64px;left:0;right:0;background:rgba(6,8,15,.96);backdrop-filter:blur(14px);padding:1.5rem 2rem;gap:1.25rem;border-bottom:1px solid var(--border)';
  if(cta) cta.style.display = shown ? '' : 'block';
}

// ─────────────────────────────────────────────────────
//  VISI & MISI ANIMATIONS
// ─────────────────────────────────────────────────────

// ── 1. CANVAS PARTICLE (music notes floating up) ──
(function(){
  var canvas = document.getElementById('visi-canvas');
  if(!canvas) return;
  var ctx = canvas.getContext('2d');
  var notes = ['♩','♪','♫','♬','♭','♮'];
  var particles = [];
  var W, H;

  function resize(){
    W = canvas.width  = canvas.offsetWidth;
    H = canvas.height = canvas.offsetHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  for(var i = 0; i < 28; i++){
    particles.push({
      x: Math.random() * (W||1000),
      y: Math.random() * (H||600),
      note: notes[Math.floor(Math.random()*notes.length)],
      speed: .25 + Math.random() * .5,
      size: 10 + Math.random() * 14,
      alpha: .04 + Math.random() * .1,
      drift: (Math.random() - .5) * .4,
      phase: Math.random() * Math.PI * 2,
    });
  }

  function draw(){
    ctx.clearRect(0, 0, W, H);
    var t = Date.now() * .001;
    particles.forEach(function(p){
      p.y -= p.speed;
      p.x += Math.sin(t * .4 + p.phase) * p.drift;
      if(p.y < -20){ p.y = H + 20; p.x = Math.random() * W; }
      ctx.globalAlpha = p.alpha;
      ctx.fillStyle = '#38A8CC';
      ctx.font = p.size + 'px serif';
      ctx.fillText(p.note, p.x, p.y);
    });
    ctx.globalAlpha = 1;
    requestAnimationFrame(draw);
  }
  draw();
})();

// ── 2. TYPEWRITER ──
(function(){
  var el = document.getElementById('visi-typed');
  var cur = document.getElementById('visi-cur');
  if(!el) return;
  var text = 'Kami hadir untuk satu tujuan.';
  var i = 0; var started = false;

  function type(){
    if(i < text.length){
      el.textContent += text[i++];
      setTimeout(type, 45 + Math.random()*30);
    } else {
      if(cur) setTimeout(function(){ cur.style.display='none'; }, 1200);
    }
  }

  // start when section enters viewport
  var visiObs = new IntersectionObserver(function(entries){
    if(entries[0].isIntersecting && !started){
      started = true;
      // eyebrow
      var ey = document.getElementById('visi-ey');
      if(ey){ setTimeout(function(){ ey.classList.add('in'); }, 100); }
      setTimeout(type, 500);
      visiObs.disconnect();
    }
  }, {threshold:.25});
  var sec = document.getElementById('s-visi');
  if(sec) visiObs.observe(sec);
})();

// ── 3. PILLAR ENTRANCE ──
(function(){
  var pillars = [
    {id:'vp1', delay:0},
    {id:'vp2', delay:180},
    {id:'vp3', delay:360},
  ];
  var done = false;
  var obs = new IntersectionObserver(function(entries){
    if(entries[0].isIntersecting && !done){
      done = true;
      pillars.forEach(function(p){
        setTimeout(function(){
          var el = document.getElementById(p.id);
          if(el) el.classList.add('in');
        }, p.delay);
      });
      obs.disconnect();
    }
  }, {threshold:.2});
  var wrap = document.querySelector('.visi-pillars');
  if(wrap) obs.observe(wrap);
})();

// ── 4. STAT COUNTERS ──
(function(){
  var stats = [
    {id:'vs1', delay:0},
    {id:'vs2', delay:120},
    {id:'vs3', delay:240},
    {id:'vs4', delay:360},
  ];
  var done = false;

  function countUp(el, target, duration){
    var start = 0; var startTime = null;
    function step(ts){
      if(!startTime) startTime = ts;
      var progress = Math.min((ts - startTime) / duration, 1);
      var ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(ease * target);
      if(progress < 1) requestAnimationFrame(step);
      else el.textContent = target;
    }
    requestAnimationFrame(step);
  }

  var obs = new IntersectionObserver(function(entries){
    if(entries[0].isIntersecting && !done){
      done = true;
      stats.forEach(function(s){
        setTimeout(function(){
          var el = document.getElementById(s.id);
          if(!el) return;
          el.classList.add('in');
          var counter = el.querySelector('.visi-counter');
          if(counter){
            var target = parseInt(counter.dataset.target);
            countUp(counter, target, 1400);
          }
        }, s.delay);
      });
      obs.disconnect();
    }
  }, {threshold:.3});
  var statsEl = document.getElementById('visi-stats');
  if(statsEl) obs.observe(statsEl);
})();

// ── 5. WORD-SPLIT VISION STATEMENT ──
(function(){
  var container = document.getElementById('visi-stmt');
  if(!container) return;

  var text = 'EMINOR hadir untuk menjadi ekosistem musik indie Indonesia yang paling inklusif — di mana setiap musisi, dari kota manapun, bisa belajar, berkarya, dan berkembang bersama.';
  var accents = ['ekosistem','inklusif','belajar,','berkarya,','berkembang'];

  text.split(' ').forEach(function(word, i){
    var span = document.createElement('span');
    span.className = 'visi-word' + (accents.some(function(a){ return word.toLowerCase().startsWith(a); }) ? ' accent' : '');
    span.textContent = word;
    span.style.transitionDelay = (i * 40) + 'ms';
    container.appendChild(span);
    container.appendChild(document.createTextNode(' '));
  });

  var done = false;
  var obs = new IntersectionObserver(function(entries){
    if(entries[0].isIntersecting && !done){
      done = true;
      container.querySelectorAll('.visi-word').forEach(function(w){ w.classList.add('in'); });
      obs.disconnect();
    }
  }, {threshold:.3});
  obs.observe(container);
})();
</script>
</body>
</html>
