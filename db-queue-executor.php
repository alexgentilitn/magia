#!/usr/bin/env php
<?php
/**
 * DB QUEUE EXECUTOR
 * Esegue query SQL da un file di coda e salva i risultati
 *
 * Uso:
 * 1. Scrivi query in: storage/db-queue.sql (una per riga)
 * 2. Esegui: php db-queue-executor.php
 * 3. Leggi risultati in: storage/logs/db-results.json
 */

if (php_sapi_name() !== 'cli') {
    die("Questo script può essere eseguito solo da command line.\n");
}

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$queueFile = __DIR__ . '/storage/db-queue.sql';
$resultFile = __DIR__ . '/storage/logs/db-results.json';
$processedFile = __DIR__ . '/storage/db-queue-processed.sql';

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 DB QUEUE EXECUTOR\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Verifica se esiste il file di coda
if (!file_exists($queueFile)) {
    echo "⚠️  File di coda non trovato: {$queueFile}\n";
    echo "   Creazione file vuoto...\n";
    file_put_contents($queueFile, '');
    exit(0);
}

// Leggi comandi
$commands = file($queueFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($commands)) {
    echo "ℹ️  Nessun comando in coda.\n";
    exit(0);
}

echo "📋 Comandi in coda: " . count($commands) . "\n\n";

$results = [
    'executed_at' => date('Y-m-d H:i:s'),
    'total_commands' => count($commands),
    'results' => []
];

foreach ($commands as $index => $sql) {
    $sql = trim($sql);

    // Salta commenti e righe vuote
    if (empty($sql) || strpos($sql, '--') === 0 || strpos($sql, '#') === 0) {
        continue;
    }

    echo "📝 [" . ($index + 1) . "/" . count($commands) . "] Esecuzione query...\n";
    echo "   SQL: " . substr($sql, 0, 80) . (strlen($sql) > 80 ? '...' : '') . "\n";

    try {
        $queryType = strtoupper(substr(ltrim($sql), 0, 6));

        $result = [
            'index' => $index + 1,
            'sql' => $sql,
            'success' => true,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (in_array($queryType, ['SELECT', 'SHOW T', 'SHOW C', 'DESCRI'])) {
            $data = DB::select($sql);
            $result['type'] = 'select';
            $result['rows'] = count($data);
            $result['data'] = $data;
            echo "   ✅ Righe restituite: " . count($data) . "\n";
        } else {
            DB::statement($sql);
            $result['type'] = 'statement';
            $result['message'] = 'Statement eseguito con successo';
            echo "   ✅ Statement eseguito\n";
        }

        $results['results'][] = $result;

    } catch (\Exception $e) {
        echo "   ❌ Errore: " . $e->getMessage() . "\n";

        $results['results'][] = [
            'index' => $index + 1,
            'sql' => $sql,
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    echo "\n";
}

// Salva risultati
file_put_contents($resultFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "💾 Risultati salvati in: {$resultFile}\n";

// Archivia comandi processati
$archive = "\n-- Processed at " . date('Y-m-d H:i:s') . "\n";
$archive .= implode("\n", $commands) . "\n";
file_put_contents($processedFile, $archive, FILE_APPEND);

// Svuota file di coda
file_put_contents($queueFile, '');
echo "🧹 File di coda svuotato\n";

echo "\n✅ Elaborazione completata!\n";
echo "   Comandi eseguiti: " . count($results['results']) . "\n";
echo "   Successi: " . count(array_filter($results['results'], fn($r) => $r['success'])) . "\n";
echo "   Errori: " . count(array_filter($results['results'], fn($r) => !$r['success'])) . "\n";
