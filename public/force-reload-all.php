<?php
/**
 * Force Complete Reload
 * Pulisce TUTTO: cache Laravel + OPcache + Realpath cache
 */

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Force Complete Reload</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #f5576c;
            margin-top: 0;
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
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            background: #f5576c;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .button:hover {
            background: #f093fb;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔥 Force Complete Reload</h1>
        <p>Pulizia completa di TUTTE le cache: Laravel + PHP...</p>
";

$basePath = dirname(__DIR__);

// 1. Pulisci OPcache (cache del codice PHP)
echo "<h3>1️⃣ Pulizia OPcache (PHP Code Cache)</h3>";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<div class='result success'>✅ OPcache pulita con successo!</div>";
    } else {
        echo "<div class='result warning'>⚠️ OPcache reset chiamato ma potrebbe non aver funzionato</div>";
    }
} else {
    echo "<div class='result warning'>ℹ️ OPcache non disponibile o non abilitata</div>";
}

// 2. Pulisci Realpath Cache
echo "<h3>2️⃣ Pulizia Realpath Cache</h3>";
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "<div class='result success'>✅ Realpath cache pulita</div>";
}

// 3. Pulisci Bootstrap Cache
echo "<h3>3️⃣ Pulizia Bootstrap Cache (Laravel)</h3>";
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
foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            $deleted++;
            echo "<div class='result success'>✅ Eliminato: " . basename($file) . "</div>";
        } else {
            echo "<div class='result error'>❌ Impossibile eliminare: " . basename($file) . "</div>";
        }
    }
}

if ($deleted > 0) {
    echo "<div class='result success'><strong>✅ Totale eliminati: $deleted file</strong></div>";
} else {
    echo "<div class='result warning'>ℹ️ Nessun file cache da eliminare</div>";
}

// 4. Verifica import Controller in routes/web.php
echo "<h3>4️⃣ Verifica Import SuperAdminController</h3>";
$routesFile = $basePath . '/routes/web.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    if (strpos($routesContent, 'use App\\Http\\Controllers\\Admin\\SuperAdminController;') !== false) {
        echo "<div class='result success'>✅ Import SuperAdminController PRESENTE in routes/web.php</div>";
    } else {
        echo "<div class='result error'>❌ Import SuperAdminController MANCANTE in routes/web.php!</div>";
        echo "<div class='result warning'>⚠️ Devi aggiungere questa riga all'inizio del file routes/web.php:<br><code>use App\\Http\\Controllers\\Admin\\SuperAdminController;</code></div>";
    }
}

// 5. Verifica esistenza Controller
echo "<h3>5️⃣ Verifica File Controller</h3>";
$controllerFile = $basePath . '/app/Http/Controllers/Admin/SuperAdminController.php';
if (file_exists($controllerFile)) {
    echo "<div class='result success'>✅ File SuperAdminController.php esiste</div>";
    echo "<div class='result'>Dimensione: " . number_format(filesize($controllerFile)) . " bytes</div>";
    echo "<div class='result'>Ultima modifica: " . date('Y-m-d H:i:s', filemtime($controllerFile)) . "</div>";
} else {
    echo "<div class='result error'>❌ File SuperAdminController.php NON trovato!</div>";
}

// 6. Verifica Middleware
echo "<h3>6️⃣ Verifica File Middleware</h3>";
$middlewareFile = $basePath . '/app/Http/Middleware/SuperAdminAuth.php';
if (file_exists($middlewareFile)) {
    echo "<div class='result success'>✅ File SuperAdminAuth.php esiste</div>";
} else {
    echo "<div class='result error'>❌ File SuperAdminAuth.php NON trovato!</div>";
}

// Istruzioni finali
echo "
        <hr style='margin: 30px 0;'>
        <h3>📋 Prossimi Passi</h3>
        <div class='result success'>
            <strong>IMPORTANTE:</strong> Dopo questa pulizia, segui questi passi:
            <ol style='margin: 10px 0 0 20px;'>
                <li><strong>Aspetta 30 secondi</strong> (per permettere al server di aggiornare le cache)</li>
                <li>Apri una <strong>nuova finestra in incognito</strong> del browser</li>
                <li>Vai su: <code>/admin/super-admin</code></li>
                <li>Inserisci la password: <code>\$201174Ag</code></li>
            </ol>
        </div>

        <div style='text-align: center; margin-top: 30px;'>
            <a href='/admin/super-admin' class='button'>🔐 Vai al Super Admin</a>
            <a href='/check-routes-artisan.php' class='button'>🔍 Verifica Rotte</a>
        </div>

        <div class='result warning' style='margin-top: 20px;'>
            <strong>⚠️ Se continua a dare 404:</strong><br>
            Potrebbe essere necessario riavviare PHP-FPM o Apache dal pannello di controllo dell'hosting.
        </div>
    </div>
</body>
</html>
";
