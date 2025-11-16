<?php
/**
 * ESECUZIONE MIGRATIONS DATABASE
 *
 * Questo script esegue tutte le migrations mancanti del database.
 * Da usare con ATTENZIONE - modifica la struttura del database!
 */

define('SECRET', '$Magia2025!');

// Verifica autenticazione
if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized - Secret key richiesta');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔧 ESECUZIONE MIGRATIONS DATABASE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

try {
    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // ============================================
    // FASE 1: VERIFICA CONNESSIONE DATABASE
    // ============================================
    echo "━━━ FASE 1: VERIFICA CONNESSIONE ━━━\n\n";

    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    echo "✅ Connessione database OK\n";
    echo "📦 Database: {$dbName}\n\n";

    // ============================================
    // FASE 2: VERIFICA TABELLA MIGRATIONS
    // ============================================
    echo "━━━ FASE 2: VERIFICA TABELLA MIGRATIONS ━━━\n\n";

    if (!Schema::hasTable('migrations')) {
        echo "⚠️  Tabella 'migrations' non esiste! Creazione...\n";
        Schema::create('migrations', function ($table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
        echo "✅ Tabella 'migrations' creata\n\n";
    } else {
        echo "✅ Tabella 'migrations' esistente\n\n";
    }

    // ============================================
    // FASE 3: LISTA MIGRATIONS ESEGUITE
    // ============================================
    echo "━━━ FASE 3: MIGRATIONS GIÀ ESEGUITE ━━━\n\n";

    $executedMigrations = DB::table('migrations')
        ->orderBy('batch')
        ->orderBy('id')
        ->get();

    echo "Totale migrations eseguite: " . $executedMigrations->count() . "\n\n";

    if ($executedMigrations->count() > 0) {
        foreach ($executedMigrations as $i => $migration) {
            echo sprintf("[%3d] Batch %d: %s\n",
                $i + 1,
                $migration->batch,
                $migration->migration
            );
        }
    } else {
        echo "⚠️  Nessuna migration eseguita finora.\n";
    }

    echo "\n";

    // ============================================
    // FASE 4: LISTA TUTTE LE MIGRATIONS DISPONIBILI
    // ============================================
    echo "━━━ FASE 4: MIGRATIONS DISPONIBILI ━━━\n\n";

    $migrationsPath = __DIR__.'/../database/migrations';
    $allMigrationFiles = glob($migrationsPath . '/*.php');

    echo "Totale file migrations: " . count($allMigrationFiles) . "\n\n";

    $allMigrations = [];
    foreach ($allMigrationFiles as $file) {
        $migrationName = pathinfo($file, PATHINFO_FILENAME);
        $allMigrations[] = $migrationName;
    }

    sort($allMigrations);

    foreach ($allMigrations as $i => $migration) {
        echo sprintf("[%3d] %s\n", $i + 1, $migration);
    }

    echo "\n";

    // ============================================
    // FASE 5: IDENTIFICA MIGRATIONS MANCANTI
    // ============================================
    echo "━━━ FASE 5: MIGRATIONS MANCANTI ━━━\n\n";

    $executedMigrationNames = $executedMigrations->pluck('migration')->toArray();
    $pendingMigrations = array_diff($allMigrations, $executedMigrationNames);

    echo "Totale migrations da eseguire: " . count($pendingMigrations) . "\n\n";

    if (count($pendingMigrations) === 0) {
        echo "✅ NESSUNA MIGRATION DA ESEGUIRE!\n";
        echo "   Database completamente aggiornato.\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ COMPLETATO - Nessuna modifica necessaria\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        exit(0);
    }

    foreach ($pendingMigrations as $i => $migration) {
        echo sprintf("  [%2d] ⏳ %s\n", $i + 1, $migration);
    }

    echo "\n";

    // ============================================
    // FASE 6: RICHIESTA CONFERMA
    // ============================================
    echo "━━━ FASE 6: RICHIESTA CONFERMA ━━━\n\n";

    $confirm = $_GET['confirm'] ?? 'NO';

    if ($confirm !== 'YES') {
        echo "⚠️  ATTENZIONE! Stai per eseguire " . count($pendingMigrations) . " migrations!\n\n";
        echo "⚠️  Questo modificherà la struttura del database:\n";
        echo "   - Potrebbe creare nuove tabelle\n";
        echo "   - Potrebbe modificare tabelle esistenti\n";
        echo "   - Potrebbe aggiungere/rimuovere colonne\n\n";
        echo "❌ CONFERMA NON RICEVUTA\n\n";
        echo "Per procedere, aggiungi: &confirm=YES all'URL\n\n";
        echo "Esempio:\n";
        echo "https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=\$Magia2025!&confirm=YES\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "⏸️  OPERAZIONE ANNULLATA - Conferma richiesta\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        exit(0);
    }

    echo "✅ CONFERMA RICEVUTA - Procedo con le migrations...\n\n";

    // ============================================
    // FASE 7: ESECUZIONE MIGRATIONS
    // ============================================
    echo "━━━ FASE 7: ESECUZIONE MIGRATIONS ━━━\n\n";

    $nextBatch = DB::table('migrations')->max('batch') + 1;
    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    foreach ($pendingMigrations as $i => $migrationName) {
        $num = $i + 1;
        $total = count($pendingMigrations);

        echo sprintf("[%2d/%2d] 🔄 Esecuzione: %s\n", $num, $total, $migrationName);

        try {
            // Includi il file migration
            $migrationFile = $migrationsPath . '/' . $migrationName . '.php';

            if (!file_exists($migrationFile)) {
                throw new Exception("File migration non trovato: {$migrationFile}");
            }

            // Crea istanza migration
            $migration = include $migrationFile;

            if (!is_object($migration)) {
                throw new Exception("Migration non valida: non restituisce un oggetto");
            }

            // Esegui UP
            DB::beginTransaction();

            $migration->up();

            // Registra migration eseguita
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $nextBatch
            ]);

            DB::commit();

            echo sprintf("        ✅ SUCCESSO\n");
            $successCount++;

        } catch (Exception $e) {
            DB::rollBack();

            echo sprintf("        ❌ ERRORE: %s\n", $e->getMessage());
            $errorCount++;
            $errors[] = [
                'migration' => $migrationName,
                'error' => $e->getMessage()
            ];

            // Se c'è un errore, fermiamo tutto per sicurezza
            echo "\n⚠️  ERRORE CRITICO - Interruzione esecuzione migrations\n\n";
            break;
        }
    }

    echo "\n";

    // ============================================
    // FASE 8: RIEPILOGO FINALE
    // ============================================
    echo "━━━ FASE 8: RIEPILOGO FINALE ━━━\n\n";

    echo "✅ Migrations eseguite con successo: {$successCount}\n";
    echo "❌ Migrations fallite: {$errorCount}\n";
    echo "📦 Batch utilizzato: {$nextBatch}\n\n";

    if ($errorCount > 0) {
        echo "━━━ ERRORI RILEVATI ━━━\n\n";
        foreach ($errors as $error) {
            echo "Migration: {$error['migration']}\n";
            echo "Errore: {$error['error']}\n\n";
        }
    }

    // ============================================
    // FASE 9: VERIFICA TABELLE CREATE
    // ============================================
    echo "━━━ FASE 9: TABELLE DATABASE FINALI ━━━\n\n";

    $tables = DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . $dbName;

    echo "Totale tabelle nel database: " . count($tables) . "\n\n";

    foreach ($tables as $i => $table) {
        $tableName = $table->$tableColumn;

        // Conta record nella tabella
        try {
            $count = DB::table($tableName)->count();
            echo sprintf("[%3d] %-40s (%d record)\n", $i + 1, $tableName, $count);
        } catch (Exception $e) {
            echo sprintf("[%3d] %-40s (errore conteggio)\n", $i + 1, $tableName);
        }
    }

    echo "\n";

    // ============================================
    // CONCLUSIONE
    // ============================================
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    if ($errorCount === 0) {
        echo "✅ COMPLETATO CON SUCCESSO!\n";
        echo "\n";
        echo "Tutte le migrations sono state eseguite correttamente.\n";
        echo "Il database è ora completamente aggiornato.\n";
    } else {
        echo "⚠️  COMPLETATO CON ERRORI\n";
        echo "\n";
        echo "{$successCount} migrations eseguite con successo.\n";
        echo "{$errorCount} migrations fallite.\n";
        echo "\n";
        echo "Controlla gli errori sopra per i dettagli.\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE FATALE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n\n";
    echo "Stacktrace:\n";
    echo $e->getTraceAsString() . "\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
