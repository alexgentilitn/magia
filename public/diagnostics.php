<?php
/**
 * DIAGNOSTICA COMPLETA MA.GIA DONNA
 * Test connessione database e configurazione Laravel
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Diagnostica MA.GIA DONNA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: #22c55e; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .info { color: #3b82f6; }
        h1 { color: #7B2869; }
        h2 { color: #E91E8C; border-bottom: 2px solid #E91E8C; padding-bottom: 10px; }
        pre {
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border-left: 4px solid #E91E8C;
        }
        .metric {
            display: inline-block;
            margin: 10px 15px 10px 0;
            padding: 10px 15px;
            background: #f9fafb;
            border-radius: 4px;
        }
    </style>
</head>
<body>";

echo "<h1>🔍 Diagnostica Completa MA.GIA DONNA</h1>";
echo "<p class='info'>Eseguito il: " . date('Y-m-d H:i:s') . "</p>";

// ============================================
// 1. INFORMAZIONI PHP
// ============================================
echo "<div class='test-section'>";
echo "<h2>1. Informazioni PHP</h2>";
echo "<div class='metric'>Versione PHP: <strong>" . phpversion() . "</strong></div>";
echo "<div class='metric'>Sistema Operativo: <strong>" . PHP_OS . "</strong></div>";
echo "<div class='metric'>SAPI: <strong>" . php_sapi_name() . "</strong></div>";
echo "<div class='metric'>Memoria Limite: <strong>" . ini_get('memory_limit') . "</strong></div>";
echo "<div class='metric'>Max Execution: <strong>" . ini_get('max_execution_time') . "s</strong></div>";

// Estensioni critiche
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
echo "<h3>Estensioni PHP</h3>";
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<div class='metric'>" . $ext . ": ";
    echo $loaded ? "<span class='success'>✓ Installata</span>" : "<span class='error'>✗ Mancante</span>";
    echo "</div>";
}
echo "</div>";

// ============================================
// 2. CONNESSIONE DATABASE
// ============================================
echo "<div class='test-section'>";
echo "<h2>2. Test Connessione Database MySQL</h2>";

$db_config = [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'agstudiodiital_magia',
    'username' => 'agstudiodiital_agstudiomagia',
    'password' => '$Magia2015!'
];

echo "<pre>";
echo "Host:     " . $db_config['host'] . "\n";
echo "Port:     " . $db_config['port'] . "\n";
echo "Database: " . $db_config['database'] . "\n";
echo "Username: " . $db_config['username'] . "\n";
echo "Password: " . str_repeat('*', strlen($db_config['password'])) . "\n";
echo "</pre>";

try {
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    echo "<p class='success'>✓ Connessione MySQL riuscita!</p>";

    // Info server
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<div class='metric'>MySQL Version: <strong>$version</strong></div>";

    // Test tabelle
    echo "<h3>Tabelle nel Database</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>" . implode("\n", $tables) . "</pre>";

    // Conta record nelle tabelle principali
    $main_tables = ['utenti', 'lezioni', 'programmi', 'pagamenti', 'sedi'];
    echo "<h3>Conteggio Record</h3>";
    foreach ($main_tables as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<div class='metric'>$table: <strong class='success'>$count record</strong></div>";
        } catch (Exception $e) {
            echo "<div class='metric'>$table: <strong class='error'>Errore: {$e->getMessage()}</strong></div>";
        }
    }

    // Test query complessa (come quella della dashboard)
    echo "<h3>Test Query Dashboard</h3>";
    try {
        $totale_clienti = $pdo->query("SELECT COUNT(*) FROM utenti WHERE tipo_utente = 'cliente'")->fetchColumn();
        echo "<p class='success'>✓ Totale Clienti: $totale_clienti</p>";

        $totale_lezioni = $pdo->query("SELECT COUNT(*) FROM lezioni")->fetchColumn();
        echo "<p class='success'>✓ Totale Lezioni: $totale_lezioni</p>";

        $incasso_totale = $pdo->query("SELECT SUM(importo_pagato) FROM pagamenti WHERE stato = 'completato'")->fetchColumn();
        echo "<p class='success'>✓ Incasso Totale: €" . number_format($incasso_totale ?? 0, 2) . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Errore query dashboard: {$e->getMessage()}</p>";
    }

} catch (PDOException $e) {
    echo "<p class='error'>✗ ERRORE CONNESSIONE DATABASE</p>";
    echo "<pre class='error'>Codice: {$e->getCode()}\nMessaggio: {$e->getMessage()}</pre>";

    // Suggerimenti
    echo "<h3>Possibili Soluzioni:</h3>";
    echo "<ul>";
    echo "<li>Verifica che MySQL sia in esecuzione</li>";
    echo "<li>Controlla username e password nel .env</li>";
    echo "<li>Verifica permessi utente database</li>";
    echo "<li>Controlla che il database esista</li>";
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<li class='error'>MySQL non è raggiungibile - verifica host e porta</li>";
    }
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<li class='error'>Credenziali errate - verifica username/password</li>";
    }
    echo "</ul>";
}
echo "</div>";

// ============================================
// 3. FILE SYSTEM E PERMESSI
// ============================================
echo "<div class='test-section'>";
echo "<h2>3. File System e Permessi</h2>";

$base_path = dirname(__DIR__);
$critical_paths = [
    '.env' => $base_path . '/.env',
    'storage/logs' => $base_path . '/storage/logs',
    'storage/framework/cache' => $base_path . '/storage/framework/cache',
    'storage/framework/sessions' => $base_path . '/storage/framework/sessions',
    'storage/framework/views' => $base_path . '/storage/framework/views',
    'bootstrap/cache' => $base_path . '/bootstrap/cache'
];

foreach ($critical_paths as $name => $path) {
    $exists = file_exists($path);
    $writable = is_writable($path);
    $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';

    echo "<div class='metric'>$name: ";
    if ($exists) {
        echo "<span class='success'>✓ Esiste</span> | ";
        echo "Permessi: <strong>$perms</strong> | ";
        echo $writable ? "<span class='success'>✓ Scrivibile</span>" : "<span class='error'>✗ Non scrivibile</span>";
    } else {
        echo "<span class='error'>✗ Non esiste</span>";
    }
    echo "</div>";
}
echo "</div>";

// ============================================
// 4. CONFIGURAZIONE LARAVEL
// ============================================
echo "<div class='test-section'>";
echo "<h2>4. Configurazione Laravel (.env)</h2>";

$env_file = $base_path . '/.env';
if (file_exists($env_file)) {
    echo "<p class='success'>✓ File .env trovato</p>";

    $env_content = file_get_contents($env_file);
    $env_lines = explode("\n", $env_content);

    // Mostra solo variabili DB (nascondi password)
    echo "<h3>Configurazione Database</h3>";
    echo "<pre>";
    foreach ($env_lines as $line) {
        if (strpos($line, 'DB_') === 0) {
            if (strpos($line, 'DB_PASSWORD') !== false) {
                $parts = explode('=', $line);
                echo $parts[0] . '=' . str_repeat('*', 10) . "\n";
            } else {
                echo $line . "\n";
            }
        }
    }
    echo "</pre>";

    // Verifica APP_KEY
    echo "<h3>Altre Configurazioni</h3>";
    preg_match('/APP_KEY=(.+)/', $env_content, $matches);
    if (!empty($matches[1]) && $matches[1] !== '') {
        echo "<p class='success'>✓ APP_KEY configurata</p>";
    } else {
        echo "<p class='error'>✗ APP_KEY mancante - esegui: php artisan key:generate</p>";
    }

    preg_match('/APP_ENV=(.+)/', $env_content, $matches);
    $app_env = !empty($matches[1]) ? trim($matches[1]) : 'unknown';
    echo "<div class='metric'>APP_ENV: <strong>$app_env</strong></div>";

    preg_match('/APP_DEBUG=(.+)/', $env_content, $matches);
    $app_debug = !empty($matches[1]) ? trim($matches[1]) : 'unknown';
    echo "<div class='metric'>APP_DEBUG: <strong>$app_debug</strong></div>";

} else {
    echo "<p class='error'>✗ File .env NON trovato!</p>";
    echo "<p>Crea il file .env copiando .env.example e configura le variabili.</p>";
}
echo "</div>";

// ============================================
// 5. LOG LARAVEL
// ============================================
echo "<div class='test-section'>";
echo "<h2>5. Ultimi Log Laravel</h2>";

$log_file = $base_path . '/storage/logs/laravel.log';
if (file_exists($log_file)) {
    echo "<p class='success'>✓ File log trovato: " . date("Y-m-d H:i:s", filemtime($log_file)) . "</p>";
    echo "<p class='info'>Dimensione: " . number_format(filesize($log_file) / 1024, 2) . " KB</p>";

    // Leggi ultime 50 righe
    $log_lines = file($log_file);
    $last_lines = array_slice($log_lines, -50);

    echo "<h3>Ultimi 50 righe del log</h3>";
    echo "<pre style='max-height: 400px; overflow-y: auto;'>";
    foreach ($last_lines as $line) {
        // Evidenzia errori
        if (stripos($line, 'ERROR') !== false || stripos($line, 'CRITICAL') !== false) {
            echo "<span class='error'>$line</span>";
        } elseif (stripos($line, 'WARNING') !== false) {
            echo "<span class='warning'>$line</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p class='warning'>⚠ File log non trovato (potrebbe non esserci ancora nessun log)</p>";
}
echo "</div>";

// ============================================
// 6. CACHE LARAVEL
// ============================================
echo "<div class='test-section'>";
echo "<h2>6. Cache Laravel</h2>";

$cache_files = [
    'Config Cache' => $base_path . '/bootstrap/cache/config.php',
    'Routes Cache' => $base_path . '/bootstrap/cache/routes-v7.php',
    'Services Cache' => $base_path . '/bootstrap/cache/services.php'
];

foreach ($cache_files as $name => $path) {
    if (file_exists($path)) {
        $modified = date("Y-m-d H:i:s", filemtime($path));
        echo "<div class='metric'>$name: <span class='warning'>✓ In cache (modificato: $modified)</span></div>";
    } else {
        echo "<div class='metric'>$name: <span class='success'>✓ Non in cache</span></div>";
    }
}

echo "<p class='info'><strong>Nota:</strong> Se la configurazione è in cache, esegui <code>php artisan config:clear</code> per ricaricarla.</p>";
echo "</div>";

// ============================================
// CONCLUSIONI
// ============================================
echo "<div class='test-section'>";
echo "<h2>✅ Conclusioni e Prossimi Passi</h2>";
echo "<ol>";
echo "<li>Se la connessione MySQL è OK ma vedi ancora errore 500, controlla i log Laravel sopra</li>";
echo "<li>Se le cache sono vecchie, puliscile con: <code>php artisan config:clear && php artisan cache:clear && php artisan view:clear</code></li>";
echo "<li>Se i permessi non sono corretti, sistemali con: <code>chmod -R 755 storage bootstrap/cache</code></li>";
echo "<li>Verifica che APP_KEY sia configurata nel .env</li>";
echo "<li>Se tutto sembra OK qui ma la dashboard dà errore, attiva temporaneamente <code>APP_DEBUG=true</code> nel .env per vedere l'errore completo</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>
