<?php
$secret = 'margono2026';
$key    = $_GET['key'] ?? '';

if ($key !== $secret) {
    http_response_code(403);
    die('<h3>403 - Tambahkan ?key=margono2026 di URL.</h3>');
}

$base = '/home/parr4187/public_html/margonoandi-fanbase';

function runCmd($cmd) {
    $out = '';
    if (function_exists('exec')) {
        exec($cmd . ' 2>&1', $lines);
        $out = implode("\n", $lines);
    } elseif (function_exists('shell_exec')) {
        $out = shell_exec($cmd . ' 2>&1');
    } elseif (function_exists('system')) {
        ob_start(); system($cmd . ' 2>&1'); $out = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start(); passthru($cmd . ' 2>&1'); $out = ob_get_clean();
    } elseif (function_exists('popen')) {
        $h = popen($cmd . ' 2>&1', 'r');
        while (!feof($h)) $out .= fgets($h);
        pclose($h);
    } else {
        $out = 'DISABLED: semua fungsi exec dinonaktifkan di server ini.';
    }
    return trim($out) ?: '(tidak ada output)';
}

// Cek fungsi mana yang tersedia
$available = [];
foreach (['exec','shell_exec','system','passthru','popen','proc_open'] as $fn) {
    if (function_exists($fn)) $available[] = $fn;
}

echo '<!DOCTYPE html><html><head>
<meta charset="UTF-8"><title>Git Patch</title>
<style>
body{font-family:monospace;background:#0b1520;color:#e8f4fa;padding:2rem}
h1{color:#38A8CC}h3{color:#F07040;margin:1.5rem 0 .5rem}
pre{background:#0f1e2e;border:1px solid rgba(56,168,204,.2);padding:1rem;border-radius:8px;white-space:pre-wrap;word-break:break-all}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#38A8CC}
.warn{color:#facc15;margin-top:2rem;border:1px solid #F07040;padding:1rem;border-radius:8px}
</style></head><body>
<h1>Git Patch — Margonoandi</h1>';

// Tampilkan fungsi yang tersedia
echo '<h3>Fungsi Tersedia</h3>';
echo '<pre class="info">' . (empty($available) ? 'TIDAK ADA — semua exec diblokir' : implode(', ', $available)) . '</pre>';

if (empty($available)) {
    echo '<h3>⚠️ Solusi Manual</h3>
    <pre class="err">Server memblokir semua exec function.
Ikuti langkah manual di bawah ini.</pre>';

    // Tampilkan isi .git/HEAD untuk info
    $headFile = "$base/.git/HEAD";
    if (file_exists($headFile)) {
        echo '<h3>Isi .git/HEAD saat ini</h3>';
        echo '<pre class="info">' . htmlspecialchars(file_get_contents($headFile)) . '</pre>';
    }

    // Coba tulis langsung ke .git/HEAD
    if (isset($_GET['fix']) && $_GET['fix'] === '1') {
        $headFile  = "$base/.git/HEAD";
        $refFile   = "$base/.git/refs/heads/main";
        $packedRef = "$base/.git/packed-refs";

        $written = false;

        // Baca hash commit main dari packed-refs
        $hash = '';
        if (file_exists($packedRef)) {
            $lines = file($packedRef);
            foreach ($lines as $line) {
                if (str_contains($line, 'refs/heads/main') || str_contains($line, 'refs/remotes/origin/main')) {
                    $parts = explode(' ', trim($line));
                    $hash  = $parts[0];
                    break;
                }
            }
        }
        if (!$hash && file_exists("$base/.git/refs/remotes/origin/main")) {
            $hash = trim(file_get_contents("$base/.git/refs/remotes/origin/main"));
        }

        echo '<h3>Hash commit main</h3>';
        echo '<pre class="info">' . ($hash ?: 'tidak ditemukan') . '</pre>';

        if ($hash) {
            // Tulis refs/heads/main
            if (!is_dir("$base/.git/refs/heads")) mkdir("$base/.git/refs/heads", 0755, true);
            file_put_contents($refFile, $hash . "\n");
            // Update HEAD
            $r1 = file_put_contents($headFile, "ref: refs/heads/main\n");
            echo '<h3>Hasil Fix</h3>';
            echo '<pre class="' . ($r1 !== false ? 'ok' : 'err') . '">';
            echo $r1 !== false ? "✅ HEAD berhasil diupdate ke refs/heads/main\nHash: $hash" : '❌ Gagal menulis HEAD';
            echo '</pre>';
        } else {
            echo '<pre class="err">❌ Hash commit main tidak ditemukan. Coba Update from Remote dulu di cPanel Git Version Control.</pre>';
        }
    } else {
        echo '<h3>Coba Fix Otomatis</h3>
        <pre class="info">Klik link berikut untuk mencoba fix via file manipulation:</pre>
        <a href="?key=margono2026&fix=1" style="color:#38A8CC;font-size:1.1rem">
        → Jalankan Fix (klik di sini)</a>';
    }

} else {
    // Ada exec function — jalankan git commands
    $cmds = [
        'Git version'   => 'git --version',
        'Fetch origin'  => "cd $base && git fetch origin",
        'Checkout main' => "cd $base && git checkout main",
        'Status'        => "cd $base && git status",
    ];
    foreach ($cmds as $label => $cmd) {
        echo "<h3>$label</h3>";
        $result = runCmd($cmd);
        $class  = (str_contains($result, 'error') || str_contains($result, 'fatal') || str_contains($result, 'DISABLED')) ? 'err' : 'ok';
        echo '<pre class="' . $class . '">' . htmlspecialchars($result) . '</pre>';
    }
}

echo '<div class="warn">⚠️ Hapus file ini setelah selesai: <strong>public/gitpatch.php</strong></div>';
echo '</body></html>';
