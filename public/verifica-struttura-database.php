<?php
/**
 * VERIFICA STRUTTURA DATABASE
 *
 * Controlla la struttura del database e identifica:
 * - Tabelle esistenti vs migrations disponibili
 * - Colonne presenti in ogni tabella
 * - Potenziali problemi o tabelle mancanti
 */

define('SECRET', '$Magia2025!');

// Verifica autenticazione
if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized - Secret key richiesta');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 VERIFICA STRUTTURA DATABASE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // ============================================
    // FASE 1: INFO DATABASE
    // ============================================
    echo "━━━ FASE 1: INFORMAZIONI DATABASE ━━━\n\n";

    $dbName = DB::connection()->getDatabaseName();
    $dbHost = DB::connection()->getConfig('host');
    $dbDriver = DB::connection()->getDriverName();

    echo "✅ Connessione OK\n";
    echo "📦 Database: {$dbName}\n";
    echo "🖥️  Host: {$dbHost}\n";
    echo "⚙️  Driver: {$dbDriver}\n\n";

    // ============================================
    // FASE 2: TABELLE ESISTENTI
    // ============================================
    echo "━━━ FASE 2: TABELLE ESISTENTI ━━━\n\n";

    $tables = DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . $dbName;

    $tableList = [];
    foreach ($tables as $table) {
        $tableList[] = $table->$tableColumn;
    }

    sort($tableList);

    echo "Totale tabelle: " . count($tableList) . "\n\n";

    foreach ($tableList as $i => $tableName) {
        try {
            $count = DB::table($tableName)->count();
            echo sprintf("[%3d] %-40s → %6d record\n", $i + 1, $tableName, $count);
        } catch (Exception $e) {
            echo sprintf("[%3d] %-40s → Errore conteggio\n", $i + 1, $tableName);
        }
    }

    echo "\n";

    // ============================================
    // FASE 3: TABELLE ATTESE DAI CONTROLLER
    // ============================================
    echo "━━━ FASE 3: TABELLE ATTESE DAI CONTROLLER ━━━\n\n";

    $expectedTables = [
        // Core
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',

        // Utenti e Permessi
        'users',
        'utenti',
        'ruoli',
        'permessi',
        'ruolo_permesso',
        'utente_permesso',

        // Clienti
        'clienti',
        'parametri_corporei',
        'documenti_caricati_cliente',

        // Lezioni e Programmi
        'lezioni',
        'programmi',
        'sedi',
        'sedes', // Duplicate?
        'professionisti',

        // Relazioni
        'cliente_lezione',
        'cliente_programma',
        'professionista_sede',
        'prenotazioni',

        // Pagamenti
        'pagamenti',

        // Schede Allenamento
        'schede_allenamento',
        'scheda_esercizi',

        // Privacy GDPR
        'consensi_privacy',

        // Referral
        'referrals',

        // Email Templates
        'email_templates',

        // Sistema
        'impostazioni',
        'impostazioni_sistema',
        'log_attivita',
    ];

    echo "Tabelle attese: " . count($expectedTables) . "\n\n";

    $missingTables = [];
    $existingTables = [];

    foreach ($expectedTables as $i => $expectedTable) {
        $exists = in_array($expectedTable, $tableList);

        if ($exists) {
            echo sprintf("[%3d] ✅ %-40s (ESISTE)\n", $i + 1, $expectedTable);
            $existingTables[] = $expectedTable;
        } else {
            echo sprintf("[%3d] ❌ %-40s (MANCANTE)\n", $i + 1, $expectedTable);
            $missingTables[] = $expectedTable;
        }
    }

    echo "\n";
    echo "Riepilogo:\n";
    echo "  ✅ Tabelle esistenti: " . count($existingTables) . "\n";
    echo "  ❌ Tabelle mancanti: " . count($missingTables) . "\n\n";

    if (count($missingTables) > 0) {
        echo "⚠️  TABELLE MANCANTI DA CREARE:\n\n";
        foreach ($missingTables as $i => $table) {
            echo sprintf("  [%2d] %s\n", $i + 1, $table);
        }
        echo "\n";
    }

    // ============================================
    // FASE 4: TABELLE NON ATTESE (Extra)
    // ============================================
    echo "━━━ FASE 4: TABELLE NON ATTESE (Extra) ━━━\n\n";

    $extraTables = array_diff($tableList, $expectedTables);

    if (count($extraTables) > 0) {
        echo "Tabelle presenti ma non attese: " . count($extraTables) . "\n\n";

        foreach ($extraTables as $i => $table) {
            try {
                $count = DB::table($table)->count();
                echo sprintf("[%3d] ⚠️  %-40s → %6d record\n", $i + 1, $table, $count);
            } catch (Exception $e) {
                echo sprintf("[%3d] ⚠️  %-40s → Errore\n", $i + 1, $table);
            }
        }
        echo "\n";
        echo "💡 Queste tabelle potrebbero essere:\n";
        echo "   - Tabelle di test da eliminare\n";
        echo "   - Tabelle legacy non più usate\n";
        echo "   - Tabelle mancanti nella lista attesa\n\n";
    } else {
        echo "✅ Nessuna tabella extra trovata\n\n";
    }

    // ============================================
    // FASE 5: STRUTTURA TABELLE CRITICHE
    // ============================================
    echo "━━━ FASE 5: STRUTTURA TABELLE CRITICHE ━━━\n\n";

    $criticalTables = [
        'clienti',
        'lezioni',
        'programmi',
        'pagamenti',
        'sedi',
        'professionisti',
        'prenotazioni',
        'schede_allenamento',
        'consensi_privacy',
        'referrals',
        'email_templates',
    ];

    foreach ($criticalTables as $tableName) {
        if (!in_array($tableName, $tableList)) {
            echo "❌ TABELLA '{$tableName}' NON ESISTE - SKIPPING\n\n";
            continue;
        }

        echo "📋 Tabella: {$tableName}\n";
        echo "   " . str_repeat("─", 60) . "\n";

        try {
            $columns = DB::select("DESCRIBE {$tableName}");

            foreach ($columns as $column) {
                $nullable = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column->Default ? "DEFAULT: {$column->Default}" : '';
                $extra = $column->Extra ? "({$column->Extra})" : '';

                echo sprintf("   %-30s %-20s %-10s %s %s\n",
                    $column->Field,
                    $column->Type,
                    $nullable,
                    $default,
                    $extra
                );
            }

            echo "\n";

        } catch (Exception $e) {
            echo "   ❌ Errore nel leggere struttura: " . $e->getMessage() . "\n\n";
        }
    }

    // ============================================
    // FASE 6: MIGRATIONS STATUS
    // ============================================
    echo "━━━ FASE 6: STATO MIGRATIONS ━━━\n\n";

    if (Schema::hasTable('migrations')) {
        $migrations = DB::table('migrations')
            ->orderBy('batch', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        echo "Migrations eseguite: " . $migrations->count() . "\n\n";

        $batches = $migrations->groupBy('batch');
        foreach ($batches as $batch => $batchMigrations) {
            echo "Batch {$batch}: ({$batchMigrations->count()} migrations)\n";
            foreach ($batchMigrations as $migration) {
                echo "  - {$migration->migration}\n";
            }
            echo "\n";
        }

        $lastBatch = $migrations->max('batch');
        echo "Ultimo batch eseguito: {$lastBatch}\n\n";

    } else {
        echo "⚠️  Tabella 'migrations' non esiste!\n";
        echo "   Probabilmente nessuna migration è stata eseguita.\n\n";
    }

    // ============================================
    // FASE 7: RIEPILOGO E RACCOMANDAZIONI
    // ============================================
    echo "━━━ FASE 7: RIEPILOGO E RACCOMANDAZIONI ━━━\n\n";

    echo "📊 STATISTICHE:\n\n";
    echo "  • Tabelle totali nel database: " . count($tableList) . "\n";
    echo "  • Tabelle attese: " . count($expectedTables) . "\n";
    echo "  • Tabelle esistenti: " . count($existingTables) . "\n";
    echo "  • Tabelle mancanti: " . count($missingTables) . "\n";
    echo "  • Tabelle extra: " . count($extraTables) . "\n\n";

    if (count($missingTables) > 0) {
        echo "🔧 AZIONI NECESSARIE:\n\n";
        echo "  ❌ CI SONO TABELLE MANCANTI!\n\n";
        echo "  Per creare le tabelle mancanti, esegui:\n";
        echo "  https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=\$Magia2025!\n\n";
        echo "  Poi conferma con:\n";
        echo "  https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=\$Magia2025!&confirm=YES\n\n";
    } else {
        echo "✅ STATO:\n\n";
        echo "  Tutte le tabelle attese sono presenti!\n";
        echo "  Il database sembra essere aggiornato.\n\n";
    }

    if (count($extraTables) > 0) {
        echo "🧹 PULIZIA:\n\n";
        echo "  Ci sono " . count($extraTables) . " tabelle extra non attese.\n";
        echo "  Valuta se eliminarle se sono tabelle di test.\n\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ VERIFICA COMPLETATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n\n";
}
