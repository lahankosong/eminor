/* vocal-remover.js — Hapus vokal berbasis STFT (FFT) dengan gate frekuensi bertingkat.
   Berbeda dari L-R polos: hanya membuang komponen TENGAH (vokal) di pita suara vokal,
   sementara BASS/KICK (pita rendah) & instrumen ter-pan dipertahankan → musik tak drop.
   API: window.VocalRemover.process(audioBuffer, onProgress(pct,label), onDone(out), onError(e))
        out = { instL, instR, vocL, vocR, sr }  (vokal mono diduplikasi L/R)
   Murni client-side, tanpa model/unduhan. Jalan di HP. */
(function () {
    'use strict';

    // FFT radix-2 in-place (iteratif). re/im: Float32Array panjang pangkat-2.
    function fft(re, im, inv) {
        var n = re.length, i, j;
        for (i = 1, j = 0; i < n; i++) {
            var bit = n >> 1;
            for (; j & bit; bit >>= 1) j ^= bit;
            j ^= bit;
            if (i < j) { var t = re[i]; re[i] = re[j]; re[j] = t; t = im[i]; im[i] = im[j]; im[j] = t; }
        }
        for (var len = 2; len <= n; len <<= 1) {
            var ang = (inv ? 2 : -2) * Math.PI / len, wr = Math.cos(ang), wi = Math.sin(ang), half = len >> 1;
            for (i = 0; i < n; i += len) {
                var cr = 1, ci = 0;
                for (var k = 0; k < half; k++) {
                    var ar = re[i + k], ai = im[i + k];
                    var br = re[i + k + half] * cr - im[i + k + half] * ci;
                    var bi = re[i + k + half] * ci + im[i + k + half] * cr;
                    re[i + k] = ar + br; im[i + k] = ai + bi;
                    re[i + k + half] = ar - br; im[i + k + half] = ai - bi;
                    var tw = cr * wr - ci * wi; ci = cr * wi + ci * wr; cr = tw;
                }
            }
        }
        if (inv) { for (i = 0; i < n; i++) { re[i] /= n; im[i] /= n; } }
    }

    function process(buf, onProgress, onDone, onError) {
        try {
            var sr = buf.sampleRate;
            var L = buf.getChannelData(0);
            var R = buf.numberOfChannels > 1 ? buf.getChannelData(1) : buf.getChannelData(0);
            var n = L.length;
            var N = 4096, HOP = N >> 2;                 // 75% overlap

            // Jendela Hann
            var win = new Float32Array(N);
            for (var w = 0; w < N; w++) win[w] = 0.5 - 0.5 * Math.cos(2 * Math.PI * w / N);

            // Gate frekuensi (LAYER BERTINGKAT): seberapa kuat vokal dibuang per pita
            // <170Hz: 0 (bass/kick aman) · 170-320: ramp · 320-7k: penuh · 7k-11k: taper · >11k: 0.55
            var gate = new Float32Array(N / 2 + 1);
            for (var k = 0; k <= N / 2; k++) {
                var f = k * sr / N, g;
                if (f < 170) g = 0;
                else if (f < 320) g = (f - 170) / 150;
                else if (f < 7000) g = 1;
                else if (f < 11000) g = 1 - 0.45 * (f - 7000) / 4000;
                else g = 0.55;
                gate[k] = g;
            }

            var instL = new Float32Array(n), instR = new Float32Array(n), wsum = new Float32Array(n);
            var nFrames = Math.max(1, Math.floor((n - N) / HOP) + 1);
            var Lr = new Float32Array(N), Li = new Float32Array(N), Rr = new Float32Array(N), Ri = new Float32Array(N);
            var Ar = new Float32Array(N), Ai = new Float32Array(N), Br = new Float32Array(N), Bi = new Float32Array(N);
            var frame = 0;

            function chunk() {
                var end = Math.min(frame + 30, nFrames);
                for (; frame < end; frame++) {
                    var pos = frame * HOP, t, idx;
                    for (t = 0; t < N; t++) {
                        idx = pos + t;
                        var ww = win[t];
                        Lr[t] = (idx < n ? L[idx] : 0) * ww; Li[t] = 0;
                        Rr[t] = (idx < n ? R[idx] : 0) * ww; Ri[t] = 0;
                    }
                    fft(Lr, Li, false); fft(Rr, Ri, false);
                    for (k = 0; k < N; k++) {
                        var kk = k <= N / 2 ? k : N - k, gg = gate[kk];
                        var lr = Lr[k], li = Li[k], rr = Rr[k], ri = Ri[k];
                        var midr = (lr + rr) * 0.5, midi = (li + ri) * 0.5;
                        // koherensi: ~1 bila L≈R (tengah/vokal), ~0 tak berkorelasi, <0 anti-fase
                        var dot = lr * rr + li * ri;
                        var pw = lr * lr + li * li + rr * rr + ri * ri;
                        var sim = pw > 1e-12 ? (2 * dot / pw) : 0;
                        var cg = sim > 0 ? sim : 0;
                        var rem = cg * gg;                 // jumlah komponen tengah yang dibuang
                        Ar[k] = lr - rem * midr; Ai[k] = li - rem * midi;   // instrumen L
                        Br[k] = rr - rem * midr; Bi[k] = ri - rem * midi;   // instrumen R
                    }
                    fft(Ar, Ai, true); fft(Br, Bi, true);
                    for (t = 0; t < N; t++) {
                        idx = pos + t;
                        if (idx < n) { var ws = win[t]; instL[idx] += Ar[t] * ws; instR[idx] += Br[t] * ws; wsum[idx] += ws * ws; }
                    }
                }
                if (onProgress) onProgress(8 + Math.round(86 * frame / nFrames), 'Memproses ' + Math.round(100 * frame / nFrames) + '%…');
                if (frame < nFrames) setTimeout(chunk, 0);
                else finish();
            }

            function finish() {
                var i;
                for (i = 0; i < n; i++) { var s = wsum[i]; if (s > 1e-6) { instL[i] /= s; instR[i] /= s; } }
                // Vokal (mono) = tengah asli − tengah instrumen
                var voc = new Float32Array(n);
                for (i = 0; i < n; i++) voc[i] = (L[i] + R[i]) * 0.5 - (instL[i] + instR[i]) * 0.5;
                // Normalisasi: instrumen pakai gain SAMA (jaga stereo), vokal terpisah
                var mx = 0, v;
                for (i = 0; i < n; i++) { v = instL[i] < 0 ? -instL[i] : instL[i]; if (v > mx) mx = v; v = instR[i] < 0 ? -instR[i] : instR[i]; if (v > mx) mx = v; }
                if (mx > 1e-4) { var gi = 0.97 / mx; for (i = 0; i < n; i++) { instL[i] *= gi; instR[i] *= gi; } }
                mx = 0; for (i = 0; i < n; i++) { v = voc[i] < 0 ? -voc[i] : voc[i]; if (v > mx) mx = v; }
                if (mx > 1e-4) { var gv = 0.97 / mx; for (i = 0; i < n; i++) voc[i] *= gv; }
                if (onProgress) onProgress(100, 'Selesai!');
                if (onDone) onDone({ instL: instL, instR: instR, vocL: voc, vocR: voc, sr: sr });
            }

            if (onProgress) onProgress(6, 'Menyiapkan…');
            setTimeout(chunk, 0);
        } catch (e) { if (onError) onError(e); }
    }

    window.VocalRemover = { process: process };
})();
