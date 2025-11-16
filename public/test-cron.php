<?php
/**
 * TEST CRON - Verifica che il cron funzioni
 * URL: https://www.agstudio.digital/magia/public/test-cron.php?secret=$Magia2025!
 */

define('SECRET', '$Magia2025!');

// Autenticazione
if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

// Header per output testuale
header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 TEST CRON - Diagnostica\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "⏰ Data/Ora: " . date('Y-m-d H:i:s') . "\n";
echo "🌍 Server: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "\n";
echo "📂 Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "📍 Script: " . __FILE__ . "\n\n";

// Test 1: File db-queue-run.php esiste?
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Test 1: File db-queue-run.php\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$runnerFile = __DIR__ . '/db-queue-run.php';
if (file_exists($runnerFile)) {
    echo "✅ File esiste: {$runnerFile}\n";
    echo "📏 Dimensione: " . filesize($runnerFile) . " bytes\n";
    echo "🔒 Permessi: " . substr(sprintf('%o', fileperms($runnerFile)), -4) . "\n";
    echo "📅 Modificato: " . date('Y-m-d H:i:s', filemtime($runnerFile)) . "\n";
} else {
    echo "❌ File NON trovato: {$runnerFile}\n";
    echo "⚠️  Il deploy potrebbe non essere completato!\n";
}
echo "\n";

// Test 2: File db-queue.sql esiste?
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Test 2: File db-queue.sql\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$queueFile = __DIR__ . '/../storage/db-queue.sql';
if (file_exists($queueFile)) {
    echo "✅ File esiste: {$queueFile}\n";
    $content = file_get_contents($queueFile);
    $lines = explode("\n", $content);
    $queries = array_filter($lines, function($line) {
        $line = trim($line);
        return !empty($line) && strpos($line, '--') !== 0;
    });
    echo "📏 Dimensione: " . strlen($content) . " bytes\n";
    echo "📋 Righe totali: " . count($lines) . "\n";
    echo "🔍 Query trovate: " . count($queries) . "\n";
    if (count($queries) > 0) {
        echo "📝 Prime query:\n";
        foreach (array_slice($queries, 0, 3) as $q) {
            echo "   - " . substr($q, 0, 60) . "...\n";
        }
    }
} else {
    echo "❌ File NON trovato: {$queueFile}\n";
}
echo "\n";

// Test 3: Directory storage/logs scrivibile?
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Test 3: Directory storage/logs\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$logsDir = __DIR__ . '/../storage/logs';
if (is_dir($logsDir)) {
    echo "✅ Directory esiste: {$logsDir}\n";
    echo "🔒 Permessi: " . substr(sprintf('%o', fileperms($logsDir)), -4) . "\n";
    if (is_writable($logsDir)) {
        echo "✅ Directory scrivibile\n";

        // Prova a scrivere un file di test
        $testFile = $logsDir . '/cron-test.txt';
        if (file_put_contents($testFile, 'Test cron: ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND)) {
            echo "✅ Test scrittura OK: cron-test.txt\n";
        } else {
            echo "❌ Impossibile scrivere file di test\n";
        }
    } else {
        echo "❌ Directory NON scrivibile\n";
    }
} else {
    echo "❌ Directory NON trovata: {$logsDir}\n";
    echo "🔨 Tentativo creazione...\n";
    if (mkdir($logsDir, 0755, true)) {
        echo "✅ Directory creata\n";
    } else {
        echo "❌ Impossibile creare directory\n";
    }
}
echo "\n";

// Test 4: Laravel funziona?
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Test 4: Bootstrap Laravel\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "✅ Autoload caricato\n";

    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✅ App istanziata\n";

    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    echo "✅ Laravel bootstrap completato\n";

    // Test connessione database
    $pdo = DB::connection()->getPdo();
    echo "✅ Connessione database OK\n";

    // Test query semplice
    $result = DB::select('SELECT DATABASE() as db');
    echo "✅ Database attivo: " . ($result[0]->db ?? 'N/A') . "\n";

} catch (\Exception $e) {
    echo "❌ Errore Laravel: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Comando cron suggerito
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 Test 5: Comando Cron\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Usa questo comando nel cron:\n\n";
echo "* * * * * wget -q -O- \"https://www.agstudio.digital/magia/public/db-queue-run.php?secret=\$Magia2025!\" >> /home/tuouser/cron-db.log 2>&1\n\n";
echo "Oppure con curl:\n\n";
echo "* * * * * curl -s \"https://www.agstudio.digital/magia/public/db-queue-run.php?secret=\$Magia2025!\" >> /home/tuouser/cron-db.log 2>&1\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Diagnostica completata!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
