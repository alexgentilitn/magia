<?php
/**
 * Script Diagnostica Errore 500 - MA.GIA DONNA
 *
 * Questo script aiuta a identificare problemi di configurazione Laravel
 * Accesso: https://www.agstudio.digital/magia/public/diagnose.php
 */

// ==================== SECURITY GUARD ====================
$securityGuard = require_once __DIR__.'/security-guard.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnostica Laravel - MA.GIA DONNA</h1>";
echo "<style>body{font-family:monospace;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;}</style>";

$basePath = dirname(__DIR__);
$issues = [];

// 1. Verifica File .env
echo "<h2>1. File .env</h2>";
$envPath = $basePath . '/.env';
if (file_exists($envPath)) {
    echo "<p class='ok'>✅ File .env ESISTE</p>";

    // Verifica APP_KEY
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_KEY=base64:') !== false) {
        echo "<p class='ok'>✅ APP_KEY configurata</p>";
    } else {
        echo "<p class='error'>❌ APP_KEY NON configurata o vuota</p>";
        $issues[] = "APP_KEY mancante nel file .env";
    }

    // Verifica DB_DATABASE
    if (strpos($envContent, 'DB_DATABASE=agstudiodiital_magia') !== false) {
        echo "<p class='ok'>✅ Database configurato</p>";
    } else {
        echo "<p class='warning'>⚠️ Database potrebbe non essere configurato correttamente</p>";
    }
} else {
    echo "<p class='error'>❌ File .env NON ESISTE</p>";
    echo "<p>Crea il file .env nella root del progetto: <code>$basePath/.env</code></p>";
    $issues[] = "File .env mancante - CRITICO";
}

// 2. Verifica Permessi Cartelle
echo "<h2>2. Permessi Cartelle</h2>";
$folders = [
    'storage' => $basePath . '/storage',
    'storage/framework' => $basePath . '/storage/framework',
    'storage/framework/cache' => $basePath . '/storage/framework/cache',
    'storage/framework/sessions' => $basePath . '/storage/framework/sessions',
    'storage/framework/views' => $basePath . '/storage/framework/views',
    'storage/logs' => $basePath . '/storage/logs',
    'bootstrap/cache' => $basePath . '/bootstrap/cache',
];

foreach ($folders as $name => $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path);

        if ($writable) {
            echo "<p class='ok'>✅ $name: $perms (scrivibile)</p>";
        } else {
            echo "<p class='error'>❌ $name: $perms (NON scrivibile)</p>";
            $issues[] = "Cartella $name non scrivibile - cambia permessi a 755 o 777";
        }
    } else {
        echo "<p class='error'>❌ $name: NON ESISTE</p>";
        $issues[] = "Cartella $name non esiste";
    }
}

// 3. Verifica Vendor
echo "<h2>3. Dipendenze Composer (vendor/)</h2>";
$vendorPath = $basePath . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo "<p class='ok'>✅ vendor/autoload.php ESISTE</p>";
} else {
    echo "<p class='error'>❌ vendor/autoload.php NON ESISTE</p>";
    $issues[] = "vendor/ non presente - esegui composer install";
}

// 4. Verifica File Cache
echo "<h2>4. File Cache Laravel</h2>";
$cacheFiles = [
    'config.php' => $basePath . '/bootstrap/cache/config.php',
    'routes.php' => $basePath . '/bootstrap/cache/routes-v7.php',
    'services.php' => $basePath . '/bootstrap/cache/services.php',
];

$hasCacheFiles = false;
foreach ($cacheFiles as $name => $path) {
    if (file_exists($path)) {
        echo "<p class='warning'>⚠️ Cache $name presente (potrebbe essere corrotta)</p>";
        $hasCacheFiles = true;
    }
}

if ($hasCacheFiles) {
    echo "<p class='warning'>⚠️ Prova a eliminare i file cache se il problema persiste</p>";
}

// 5. Verifica PHP
echo "<h2>5. Informazioni PHP</h2>";
echo "<p>Versione PHP: <strong>" . phpversion() . "</strong></p>";
echo "<p>Server: <strong>" . $_SERVER['SERVER_SOFTWARE'] ?? 'Sconosciuto' . "</strong></p>";

// Estensioni richieste da Laravel
$requiredExtensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'];
echo "<p>Estensioni PHP:</p><ul>";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li class='ok'>✅ $ext</li>";
    } else {
        echo "<li class='error'>❌ $ext (MANCANTE)</li>";
        $issues[] = "Estensione PHP $ext mancante";
    }
}
echo "</ul>";

// 6. Test Autoload
echo "<h2>6. Test Autoload Laravel</h2>";
try {
    require $basePath . '/vendor/autoload.php';
    echo "<p class='ok'>✅ Autoload funziona</p>";

    // Prova a caricare Laravel
    try {
        $app = require_once $basePath . '/bootstrap/app.php';
        echo "<p class='ok'>✅ Laravel bootstrap OK</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore Laravel bootstrap: " . htmlspecialchars($e->getMessage()) . "</p>";
        $issues[] = "Errore bootstrap Laravel: " . $e->getMessage();
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Errore autoload: " . htmlspecialchars($e->getMessage()) . "</p>";
    $issues[] = "Errore autoload: " . $e->getMessage();
}

// 7. Verifica Log
echo "<h2>7. Log Laravel</h2>";
$logPath = $basePath . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    echo "<p class='ok'>✅ File log esiste</p>";

    // Mostra ultime 20 righe di log
    $logLines = file($logPath);
    $lastLines = array_slice($logLines, -20);

    echo "<p>Ultime 20 righe di log:</p>";
    echo "<pre style='max-height:300px;overflow:auto;'>";
    echo htmlspecialchars(implode('', $lastLines));
    echo "</pre>";
} else {
    echo "<p class='warning'>⚠️ Nessun file log presente</p>";
}

// RIEPILOGO
echo "<h2>📋 Riepilogo Problemi</h2>";
if (empty($issues)) {
    echo "<p class='ok'><strong>✅ Nessun problema rilevato!</strong></p>";
    echo "<p>Se vedi ancora errore 500, controlla i log Laravel sopra per dettagli.</p>";
} else {
    echo "<p class='error'><strong>❌ Trovati " . count($issues) . " problemi:</strong></p>";
    echo "<ol>";
    foreach ($issues as $issue) {
        echo "<li>" . htmlspecialchars($issue) . "</li>";
    }
    echo "</ol>";
}

// AZIONI CONSIGLIATE
echo "<h2>🔧 Azioni Consigliate</h2>";
echo "<ol>";
echo "<li>Se manca <strong>.env</strong>: Crea il file .env nella root</li>";
echo "<li>Se <strong>permessi</strong> errati: Cambia permessi storage/ e bootstrap/cache/ a 755 o 777</li>";
echo "<li>Se <strong>cache</strong> corrotta: Usa lo script clear-cache.php per pulirla</li>";
echo "<li>Se problemi <strong>database</strong>: Verifica credenziali nel .env</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='clear-cache.php'>🧹 Pulisci Cache Laravel</a></p>";
echo "<p><a href='../'>← Torna al sito</a></p>";
