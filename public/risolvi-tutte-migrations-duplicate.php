<?php
/**
 * RISOLUZIONE COMPLETA MIGRATIONS CON TABELLE GIÀ ESISTENTI
 *
 * Questo script identifica e registra automaticamente tutte le migrations
 * per tabelle che esistono già nel database ma non sono registrate.
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized - Secret key richiesta');
}

header('Content-Type: text/plain; charset=utf-8');

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔧 RISOLUZIONE COMPLETA MIGRATIONS DUPLICATE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // ============================================
    // MAPPATURA MIGRATIONS -> TABELLE
    // ============================================

    $migrationsToCheck = [
        '2025_11_13_130000_create_impostazioni_table' => ['impostazioni'],
        '2025_11_13_164841_create_impostazioni_sistema_table' => ['impostazioni_sistema'],
        '2025_11_15_221308_create_schede_allenamento_table' => ['schede_allenamento'],
        '2025_11_15_221330_create_scheda_esercizi_table' => ['scheda_esercizi'],
        '2025_11_15_232814_create_documenti_caricati_cliente_table' => ['documenti_caricati_cliente', 'documenti_cliente'],
        '2025_11_15_232814_create_parametri_corporei_table' => ['parametri_corporei'],
        '2025_11_15_232815_add_prenotazioni_fields_to_prenotazioni_table' => ['prenotazioni'],
        '2025_11_15_235928_create_consensi_privacy_table' => ['consensi_privacy'],
        '2025_11_16_000824_create_email_templates_table' => ['email_templates'],
        '2025_11_16_000907_create_referral_system_table' => ['referrals'],
    ];

    echo "━━━ FASE 1: VERIFICA TABELLE E MIGRATIONS ━━━\n\n";

    $toRegister = [];
    $nextBatch = DB::table('migrations')->max('batch') + 1;

    foreach ($migrationsToCheck as $migrationName => $tables) {
        // Verifica se la migration è già registrata
        $isRegistered = DB::table('migrations')
            ->where('migration', $migrationName)
            ->exists();

        if ($isRegistered) {
            echo "✅ '{$migrationName}' - GIÀ REGISTRATA\n";
            continue;
        }

        // Verifica se almeno una delle tabelle esiste
        $tableExists = false;
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $tableExists = true;
                break;
            }
        }

        if ($tableExists) {
            echo "⚠️  '{$migrationName}' - TABELLA ESISTE MA NON REGISTRATA\n";
            $toRegister[] = $migrationName;
        } else {
            echo "ℹ️  '{$migrationName}' - Da eseguire normalmente\n";
        }
    }

    echo "\n";

    // ============================================
    // FASE 2: REGISTRAZIONE MIGRATIONS
    // ============================================

    if (empty($toRegister)) {
        echo "━━━ FASE 2: NESSUNA AZIONE NECESSARIA ━━━\n\n";
        echo "✅ Tutte le migrations sono già registrate correttamente.\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        exit(0);
    }

    echo "━━━ FASE 2: REGISTRAZIONE MIGRATIONS ━━━\n\n";
    echo "Totale migrations da registrare: " . count($toRegister) . "\n";
    echo "Batch utilizzato: {$nextBatch}\n\n";

    $registered = 0;
    $errors = [];

    foreach ($toRegister as $migrationName) {
        try {
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $nextBatch
            ]);
            echo "✅ Registrata: {$migrationName}\n";
            $registered++;
        } catch (Exception $e) {
            echo "❌ Errore: {$migrationName} - " . $e->getMessage() . "\n";
            $errors[] = [
                'migration' => $migrationName,
                'error' => $e->getMessage()
            ];
        }
    }

    echo "\n";

    // ============================================
    // FASE 3: VERIFICA FINALE
    // ============================================

    echo "━━━ FASE 3: VERIFICA FINALE ━━━\n\n";

    $totalMigrations = DB::table('migrations')->count();
    echo "📊 Totale migrations registrate nel database: {$totalMigrations}\n\n";

    if (!empty($errors)) {
        echo "⚠️  ATTENZIONE: Alcuni errori rilevati:\n\n";
        foreach ($errors as $error) {
            echo "  • {$error['migration']}\n";
            echo "    {$error['error']}\n\n";
        }
    }

    // ============================================
    // RIEPILOGO
    // ============================================

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    if ($registered > 0 && empty($errors)) {
        echo "✅ COMPLETATO CON SUCCESSO\n\n";
        echo "{$registered} migrations registrate con successo.\n";
        echo "Ora puoi eseguire nuovamente le migrations rimanenti.\n";
    } elseif ($registered > 0 && !empty($errors)) {
        echo "⚠️  COMPLETATO CON ALCUNI ERRORI\n\n";
        echo "{$registered} migrations registrate con successo.\n";
        echo count($errors) . " migrations con errori.\n";
    } else {
        echo "❌ COMPLETATO CON ERRORI\n\n";
        echo "Nessuna migration registrata.\n";
    }
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

} catch (Exception $e) {
    echo "\n❌ ERRORE CRITICO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}
