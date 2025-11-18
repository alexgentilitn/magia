#!/usr/bin/env php
<?php
/**
 * DB CLI - Command Line Interface per database MySQL
 * Esegui: php db-cli.php "YOUR SQL QUERY"
 */

if (php_sapi_name() !== 'cli') {
    die("Questo script può essere eseguito solo da command line.\n");
}

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ottieni query da argomento
$sql = $argv[1] ?? null;

if (!$sql) {
    echo "❌ Uso: php db-cli.php \"SQL QUERY\"\n";
    echo "\nEsempi:\n";
    echo "  php db-cli.php \"SHOW TABLES\"\n";
    echo "  php db-cli.php \"SELECT * FROM utenti LIMIT 5\"\n";
    echo "  php db-cli.php \"CREATE TABLE test (id INT, nome VARCHAR(100))\"\n";
    exit(1);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🗄️  DB CLI - MySQL Query Executor\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "📝 Query: {$sql}\n\n";

try {
    $queryType = strtoupper(substr(ltrim($sql), 0, 6));

    if (in_array($queryType, ['SELECT', 'SHOW T', 'SHOW C', 'DESCRI'])) {
        // Query SELECT
        $results = DB::select($sql);

        echo "✅ Query eseguita con successo!\n";
        echo "📊 Righe restituite: " . count($results) . "\n\n";

        if (count($results) > 0) {
            // Stampa intestazioni
            $firstRow = (array) $results[0];
            $columns = array_keys($firstRow);

            echo str_repeat('─', 80) . "\n";
            foreach ($columns as $col) {
                echo str_pad($col, 20) . " | ";
            }
            echo "\n" . str_repeat('─', 80) . "\n";

            // Stampa dati
            foreach ($results as $row) {
                foreach ((array)$row as $value) {
                    $displayValue = $value ?? 'NULL';
                    if (strlen($displayValue) > 18) {
                        $displayValue = substr($displayValue, 0, 15) . '...';
                    }
                    echo str_pad($displayValue, 20) . " | ";
                }
                echo "\n";
            }
            echo str_repeat('─', 80) . "\n";
        }

    } else {
        // Query INSERT/UPDATE/DELETE/CREATE/ALTER/DROP
        DB::statement($sql);
        echo "✅ Statement eseguito con successo!\n";
    }

    echo "\n✨ Operazione completata!\n";
    exit(0);

} catch (\Exception $e) {
    echo "❌ ERRORE:\n";
    echo "   Messaggio: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
