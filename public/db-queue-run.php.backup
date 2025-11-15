<?php
/**
 * DB QUEUE RUNNER - Esecuzione automatica code SQL
 * Da chiamare via cron ogni minuto
 *
 * Cron: * * * * * wget -q -O- https://www.agstudio.digital/magia/public/db-queue-run.php?secret=SECRETKEY
 */

// Configurazione
define('CRON_SECRET', '$Magia2025!');

// Autenticazione
$secret = $_GET['secret'] ?? '';
if ($secret !== CRON_SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

// Imposta timeout lungo per operazioni complesse
set_time_limit(300); // 5 minuti max

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$queueFile = __DIR__ . '/../storage/db-queue.sql';
$resultFile = __DIR__ . '/../storage/logs/db-results.json';
$processedFile = __DIR__ . '/../storage/db-queue-processed.sql';

// Assicura che le directory esistano
if (!is_dir(dirname($resultFile))) {
    mkdir(dirname($resultFile), 0755, true);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 DB QUEUE RUNNER\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n\n";

// Verifica se esiste il file di coda
if (!file_exists($queueFile)) {
    echo "⚠️  File di coda non trovato\n";
    echo "📝 Creazione file: {$queueFile}\n";
    file_put_contents($queueFile, '-- DB Queue: scrivi qui le query SQL da eseguire' . "\n");
    exit(0);
}

// Leggi comandi
$content = file_get_contents($queueFile);
$commands = array_filter(
    explode("\n", $content),
    function($line) {
        $line = trim($line);
        return !empty($line) &&
               strpos($line, '--') !== 0 &&
               strpos($line, '#') !== 0;
    }
);

if (empty($commands)) {
    echo "ℹ️  Nessun comando in coda\n";
    echo "✅ Queue vuota - OK\n";
    exit(0);
}

echo "📋 Comandi in coda: " . count($commands) . "\n\n";

$results = [
    'executed_at' => date('Y-m-d H:i:s'),
    'total_commands' => count($commands),
    'success_count' => 0,
    'error_count' => 0,
    'results' => []
];

$index = 0;
foreach ($commands as $sql) {
    $sql = trim($sql);
    $index++;

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 [{$index}/" . count($commands) . "] Esecuzione query\n";
    echo "SQL: " . substr($sql, 0, 100) . (strlen($sql) > 100 ? '...' : '') . "\n\n";

    try {
        $queryType = strtoupper(substr(ltrim($sql), 0, 6));

        $result = [
            'index' => $index,
            'sql' => $sql,
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (in_array($queryType, ['SELECT', 'SHOW T', 'SHOW C', 'DESCRI'])) {
            // Query SELECT
            $data = DB::select($sql);
            $result['type'] = 'select';
            $result['rows'] = count($data);
            $result['data'] = $data;

            echo "✅ Query eseguita con successo!\n";
            echo "📊 Righe restituite: " . count($data) . "\n";

            if (count($data) > 0 && count($data) <= 10) {
                echo "📋 Dati:\n";
                foreach ($data as $row) {
                    echo "   " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
                }
            }

        } else {
            // Query INSERT/UPDATE/DELETE/CREATE/ALTER/DROP
            DB::statement($sql);
            $result['type'] = 'statement';
            $result['message'] = 'Statement eseguito con successo';

            echo "✅ Statement eseguito con successo!\n";

            // Per CREATE/DROP mostra conferma speciale
            if (stripos($sql, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?/i', $sql, $matches);
                if (isset($matches[1])) {
                    echo "🆕 Tabella creata: {$matches[1]}\n";
                }
            } elseif (stripos($sql, 'DROP TABLE') !== false) {
                preg_match('/DROP TABLE\s+(?:IF EXISTS\s+)?`?(\w+)`?/i', $sql, $matches);
                if (isset($matches[1])) {
                    echo "🗑️  Tabella eliminata: {$matches[1]}\n";
                }
            } elseif (stripos($sql, 'INSERT') !== false) {
                echo "➕ Record inseriti\n";
            } elseif (stripos($sql, 'UPDATE') !== false) {
                echo "✏️  Record aggiornati\n";
            } elseif (stripos($sql, 'DELETE') !== false) {
                echo "🗑️  Record eliminati\n";
            }
        }

        $results['results'][] = $result;
        $results['success_count']++;

    } catch (\Exception $e) {
        echo "❌ ERRORE: " . $e->getMessage() . "\n";

        $results['results'][] = [
            'index' => $index,
            'sql' => $sql,
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $results['error_count']++;
    }

    echo "\n";
}

// Salva risultati
file_put_contents($resultFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "💾 Risultati salvati in: storage/logs/db-results.json\n\n";

// Archivia comandi processati
$archive = "\n-- =====================================\n";
$archive .= "-- Processed at: " . date('Y-m-d H:i:s') . "\n";
$archive .= "-- Commands: " . count($commands) . "\n";
$archive .= "-- Success: {$results['success_count']}\n";
$archive .= "-- Errors: {$results['error_count']}\n";
$archive .= "-- =====================================\n";
$archive .= $content . "\n";
file_put_contents($processedFile, $archive, FILE_APPEND);

// Svuota file di coda
file_put_contents($queueFile, "-- DB Queue: scrivi qui le query SQL da eseguire\n");
echo "🧹 File di coda svuotato\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ ELABORAZIONE COMPLETATA!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Comandi eseguiti: " . count($results['results']) . "\n";
echo "✅ Successi: {$results['success_count']}\n";
echo "❌ Errori: {$results['error_count']}\n";
echo "⏰ Completato: " . date('Y-m-d H:i:s') . "\n";
