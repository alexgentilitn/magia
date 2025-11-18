<?php
/**
 * Emergency Cache Clear Script
 * Pulisce la cache di Laravel per risolvere problemi di routing
 */

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Emergency Cache Clear</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .success {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        .icon {
            font-size: 24px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .button:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1><span class='icon'>🚨</span> Emergency Cache Clear</h1>
        <p>Pulizia cache di emergenza per risolvere problemi di routing...</p>
";

$results = [];
$totalDeleted = 0;
$hasErrors = false;

// Percorso base Laravel
$basePath = dirname(__DIR__);

// 1. Cache Bootstrap
echo "<h3>1️⃣ Pulizia Bootstrap Cache</h3>";
try {
    $bootstrapCache = $basePath . '/bootstrap/cache';
    $files = [
        $bootstrapCache . '/config.php',
        $bootstrapCache . '/routes-v7.php',
        $bootstrapCache . '/routes.php',
        $bootstrapCache . '/services.php',
        $bootstrapCache . '/events.php',
        $bootstrapCache . '/packages.php',
    ];

    $deleted = 0;
    $skipped = 0;

    foreach ($files as $file) {
        if (file_exists($file)) {
            if (is_writable($file)) {
                unlink($file);
                $deleted++;
                echo "<div class='result success'>✅ Eliminato: " . basename($file) . "</div>";
            } else {
                $skipped++;
                echo "<div class='result error'>❌ Non scrivibile: " . basename($file) . "</div>";
                $hasErrors = true;
            }
        }
    }

    if ($deleted > 0) {
        echo "<div class='result success'>✅ Bootstrap cache: <strong>$deleted</strong> file eliminati</div>";
        $totalDeleted += $deleted;
    }
    if ($skipped > 0) {
        echo "<div class='result warning'>⚠️ Saltati: <strong>$skipped</strong> file (permessi insufficienti)</div>";
    }
} catch (Exception $e) {
    echo "<div class='result error'>❌ Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
    $hasErrors = true;
}

// 2. Cache Views
echo "<h3>2️⃣ Pulizia Views Cache</h3>";
try {
    $viewsPath = $basePath . '/storage/framework/views';
    $deleted = 0;
    $skipped = 0;

    if (is_dir($viewsPath)) {
        $files = glob($viewsPath . '/*.php');
        foreach ($files as $file) {
            if (is_writable($file)) {
                unlink($file);
                $deleted++;
            } else {
                $skipped++;
            }
        }

        if ($deleted > 0) {
            echo "<div class='result success'>✅ Views cache: <strong>$deleted</strong> file eliminati</div>";
            $totalDeleted += $deleted;
        } else {
            echo "<div class='result warning'>ℹ️ Nessun file da eliminare</div>";
        }
        if ($skipped > 0) {
            echo "<div class='result warning'>⚠️ Saltati: <strong>$skipped</strong> file</div>";
        }
    } else {
        echo "<div class='result warning'>ℹ️ Directory views cache non esiste</div>";
    }
} catch (Exception $e) {
    echo "<div class='result error'>❌ Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
    $hasErrors = true;
}

// 3. Cache Framework
echo "<h3>3️⃣ Pulizia Framework Cache</h3>";
try {
    $cachePath = $basePath . '/storage/framework/cache/data';
    $deleted = 0;

    if (is_dir($cachePath)) {
        $dirs = glob($cachePath . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && is_writable($file)) {
                    unlink($file);
                    $deleted++;
                }
            }
        }

        if ($deleted > 0) {
            echo "<div class='result success'>✅ Framework cache: <strong>$deleted</strong> file eliminati</div>";
            $totalDeleted += $deleted;
        } else {
            echo "<div class='result warning'>ℹ️ Nessun file da eliminare</div>";
        }
    } else {
        echo "<div class='result warning'>ℹ️ Directory framework cache non esiste</div>";
    }
} catch (Exception $e) {
    echo "<div class='result error'>❌ Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
    $hasErrors = true;
}

// Riepilogo finale
echo "<hr style='margin: 30px 0; border: none; border-top: 2px solid #eee;'>";
echo "<h3>📊 Riepilogo</h3>";

if ($hasErrors) {
    echo "<div class='result error'>
        <strong>⚠️ Completato con alcuni errori</strong><br>
        Totale file eliminati: <strong>$totalDeleted</strong><br>
        Alcuni file potrebbero non essere stati eliminati per problemi di permessi.
    </div>";
} else {
    echo "<div class='result success'>
        <strong>✅ Cache pulita con successo!</strong><br>
        Totale file eliminati: <strong>$totalDeleted</strong>
    </div>";
}

echo "
        <div class='result warning' style='margin-top: 20px;'>
            <strong>🔄 Prossimi passi:</strong><br>
            1. Ricarica la pagina del Super Admin<br>
            2. Effettua nuovamente il login con la password <code>\$201174Ag</code><br>
            3. Le funzionalità dovrebbero ora funzionare correttamente
        </div>

        <a href='/admin/super-admin' class='button'>🔐 Vai al Super Admin Panel</a>

        <div style='margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;'>
            <strong>⚠️ IMPORTANTE:</strong> Dopo aver verificato che tutto funziona, elimina questo file per sicurezza:<br>
            <code>public/clear-cache-emergency.php</code>
        </div>
    </div>
</body>
</html>
";
