<?php
/**
 * STATUS MIGRATIONS - Verifica stato completo
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 STATUS MIGRATIONS DATABASE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // Connessione database
    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    echo "✅ Database: {$dbName}\n\n";

    // ============================================
    // FASE 1: MIGRATIONS ESEGUITE
    // ============================================
    echo "━━━ MIGRATIONS ESEGUITE ━━━\n\n";

    $executedMigrations = DB::table('migrations')
        ->orderBy('batch')
        ->orderBy('id')
        ->get();

    $lastBatch = DB::table('migrations')->max('batch');

    echo "Totale migrations eseguite: " . $executedMigrations->count() . "\n";
    echo "Ultimo batch: {$lastBatch}\n\n";

    // ============================================
    // FASE 2: MIGRATIONS DISPONIBILI
    // ============================================
    echo "━━━ MIGRATIONS DISPONIBILI ━━━\n\n";

    $migrationsPath = __DIR__.'/../database/migrations';
    $allMigrationFiles = glob($migrationsPath . '/*.php');

    echo "Totale file migrations: " . count($allMigrationFiles) . "\n\n";

    $allMigrations = [];
    foreach ($allMigrationFiles as $file) {
        $migrationName = pathinfo($file, PATHINFO_FILENAME);
        $allMigrations[] = $migrationName;
    }

    sort($allMigrations);

    // ============================================
    // FASE 3: CONFRONTO
    // ============================================
    echo "━━━ ANALISI STATO ━━━\n\n";

    $executedMigrationNames = $executedMigrations->pluck('migration')->toArray();
    $pendingMigrations = array_diff($allMigrations, $executedMigrationNames);
    $extraMigrations = array_diff($executedMigrationNames, $allMigrations);

    // Migrations mancanti
    if (count($pendingMigrations) > 0) {
        echo "⚠️  MIGRATIONS DA ESEGUIRE: " . count($pendingMigrations) . "\n\n";
        foreach ($pendingMigrations as $i => $migration) {
            echo sprintf("  [%2d] ⏳ %s\n", $i + 1, $migration);
        }
        echo "\n";
    } else {
        echo "✅ NESSUNA MIGRATION DA ESEGUIRE\n";
        echo "   Tutte le migrations sono state eseguite.\n\n";
    }

    // Migrations obsolete (eseguite ma non più presenti)
    if (count($extraMigrations) > 0) {
        echo "⚠️  MIGRATIONS OBSOLETE (eseguite ma file non più presente): " . count($extraMigrations) . "\n\n";
        foreach ($extraMigrations as $i => $migration) {
            echo sprintf("  [%2d] 🗑️  %s\n", $i + 1, $migration);
        }
        echo "\n";
    }

    // ============================================
    // FASE 4: DETTAGLIO ULTIME 10 MIGRATIONS
    // ============================================
    echo "━━━ ULTIME 10 MIGRATIONS ESEGUITE ━━━\n\n";

    $lastTenMigrations = DB::table('migrations')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();

    foreach ($lastTenMigrations as $i => $m) {
        echo sprintf("[%2d] Batch %2d: %s\n", $i + 1, $m->batch, $m->migration);
    }

    echo "\n";

    // ============================================
    // FASE 5: TABELLE DATABASE
    // ============================================
    echo "━━━ TABELLE DATABASE ━━━\n\n";

    $tables = DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . $dbName;

    echo "Totale tabelle: " . count($tables) . "\n\n";

    $tablesWithRecords = [];
    foreach ($tables as $table) {
        $tableName = $table->$tableColumn;
        try {
            $count = DB::table($tableName)->count();
            if ($count > 0) {
                $tablesWithRecords[] = sprintf("%-40s (%d record)", $tableName, $count);
            }
        } catch (Exception $e) {
            // Ignora errori
        }
    }

    if (count($tablesWithRecords) > 0) {
        echo "Tabelle con record:\n";
        foreach ($tablesWithRecords as $info) {
            echo "  • {$info}\n";
        }
    }

    echo "\n";

    // ============================================
    // CONCLUSIONE
    // ============================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    if (count($pendingMigrations) === 0) {
        echo "✅ DATABASE AGGIORNATO\n\n";
        echo "Tutte le migrations sono state eseguite correttamente.\n";
    } else {
        echo "⚠️  MIGRATIONS DA ESEGUIRE: " . count($pendingMigrations) . "\n\n";
        echo "Per eseguirle, usa:\n";
        echo "https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=\$Magia2025!&confirm=YES\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
