<?php
/**
 * CHECK RESULTS - Visualizza risultati esecuzione cron
 * URL: https://www.agstudio.digital/magia/public/check-results.php?secret=$Magia2025!
 */

define('SECRET', '$Magia2025!');

// Autenticazione
if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 RISULTATI CRON DB QUEUE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$resultFile = __DIR__ . '/../storage/logs/db-results.json';
$queueFile = __DIR__ . '/../storage/db-queue.sql';

// Verifica file risultati
if (!file_exists($resultFile)) {
    echo "⚠️  Nessun risultato trovato\n";
    echo "❌ File non esiste: {$resultFile}\n";
    echo "\nIl cron potrebbe non essere ancora stato eseguito.\n";
    echo "Aspetta 1-2 minuti e riprova.\n";
    exit;
}

echo "📅 Ora attuale: " . date('Y-m-d H:i:s') . "\n";
echo "📂 File risultati: {$resultFile}\n";
echo "📏 Dimensione: " . filesize($resultFile) . " bytes\n";
echo "⏰ Ultimo aggiornamento: " . date('Y-m-d H:i:s', filemtime($resultFile)) . "\n";
echo "\n";

// Leggi e mostra risultati
$content = file_get_contents($resultFile);
$results = json_decode($content, true);

if (!$results) {
    echo "❌ Errore nel leggere il file JSON\n";
    echo "Contenuto:\n{$content}\n";
    exit;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 RIEPILOGO ESECUZIONE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "⏰ Eseguito il: {$results['executed_at']}\n";
echo "📝 Comandi totali: {$results['total_commands']}\n";
echo "✅ Successi: {$results['success_count']}\n";
echo "❌ Errori: {$results['error_count']}\n";
echo "\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 DETTAGLIO RISULTATI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

foreach ($results['results'] as $result) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 Query #{$result['index']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "SQL: " . substr($result['sql'], 0, 100) . (strlen($result['sql']) > 100 ? '...' : '') . "\n";
    echo "⏰ Timestamp: {$result['timestamp']}\n";

    if ($result['success']) {
        echo "✅ Stato: SUCCESS\n";
        echo "📋 Tipo: {$result['type']}\n";

        if ($result['type'] === 'select') {
            echo "📊 Righe: {$result['rows']}\n";
            if ($result['rows'] > 0 && isset($result['data'])) {
                echo "📄 Dati:\n";
                foreach (array_slice($result['data'], 0, 5) as $row) {
                    echo "   " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                }
                if ($result['rows'] > 5) {
                    echo "   ... e altre " . ($result['rows'] - 5) . " righe\n";
                }
            }
        } else {
            echo "💬 {$result['message']}\n";
        }
    } else {
        echo "❌ Stato: ERROR\n";
        echo "⚠️  Errore: {$result['error']}\n";
        if (isset($result['file'])) {
            echo "📂 File: {$result['file']}:{$result['line']}\n";
        }
    }
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📂 STATO QUEUE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (file_exists($queueFile)) {
    $queueContent = file_get_contents($queueFile);
    $queueLines = explode("\n", $queueContent);
    $pendingQueries = array_filter($queueLines, function($line) {
        $line = trim($line);
        return !empty($line) && strpos($line, '--') !== 0;
    });

    if (count($pendingQueries) > 0) {
        echo "⚠️  Query in attesa: " . count($pendingQueries) . "\n";
        foreach (array_slice($pendingQueries, 0, 3) as $query) {
            echo "   - " . substr($query, 0, 60) . "...\n";
        }
    } else {
        echo "✅ Nessuna query in attesa\n";
        echo "💚 Queue vuota - tutto processato!\n";
    }
} else {
    echo "❌ File queue non trovato\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Fine report\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
