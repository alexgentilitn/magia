<?php
/**
 * RISOLUZIONE MIGRATION CON COLONNE DUPLICATE
 *
 * Questo script risolve il problema delle colonne già esistenti
 * registrando la migration come eseguita se le colonne esistono già.
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
    echo "🔧 RISOLUZIONE MIGRATION DUPLICATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // ============================================
    // FASE 1: VERIFICA COLONNE
    // ============================================
    echo "━━━ FASE 1: VERIFICA COLONNE ━━━\n\n";

    $hasPasswordTempExpires = Schema::hasColumn('utenti', 'password_temp_expires_at');
    $hasDeveCambiarePassword = Schema::hasColumn('utenti', 'deve_cambiare_password');

    echo "Colonna 'password_temp_expires_at': " . ($hasPasswordTempExpires ? '✅ ESISTE' : '❌ NON ESISTE') . "\n";
    echo "Colonna 'deve_cambiare_password': " . ($hasDeveCambiarePassword ? '✅ ESISTE' : '❌ NON ESISTE') . "\n\n";

    if (!$hasPasswordTempExpires || !$hasDeveCambiarePassword) {
        echo "❌ ERRORE: Le colonne non esistono tutte. Non posso procedere.\n";
        echo "   Usa lo script normale delle migrations.\n\n";
        exit(1);
    }

    // ============================================
    // FASE 2: VERIFICA MIGRATION
    // ============================================
    echo "━━━ FASE 2: VERIFICA STATO MIGRATION ━━━\n\n";

    $migrationName = '2025_11_13_120000_add_password_temp_expires_to_utenti';
    $migrationExists = DB::table('migrations')
        ->where('migration', $migrationName)
        ->exists();

    echo "Migration '{$migrationName}':\n";
    echo ($migrationExists ? '✅ GIÀ REGISTRATA' : '❌ NON REGISTRATA') . "\n\n";

    if ($migrationExists) {
        echo "ℹ️  La migration è già registrata. Non c'è nulla da fare.\n\n";
        exit(0);
    }

    // ============================================
    // FASE 3: REGISTRAZIONE MIGRATION
    // ============================================
    echo "━━━ FASE 3: REGISTRAZIONE MIGRATION ━━━\n\n";

    // Ottieni il batch successivo
    $nextBatch = DB::table('migrations')->max('batch') + 1;

    echo "📝 Registrazione migration nel batch {$nextBatch}...\n";

    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => $nextBatch
    ]);

    echo "✅ Migration registrata con successo!\n\n";

    // ============================================
    // FASE 4: VERIFICA FINALE
    // ============================================
    echo "━━━ FASE 4: VERIFICA FINALE ━━━\n\n";

    $verifica = DB::table('migrations')
        ->where('migration', $migrationName)
        ->first();

    if ($verifica) {
        echo "✅ Verifica OK:\n";
        echo "   Migration: {$verifica->migration}\n";
        echo "   Batch: {$verifica->batch}\n\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ COMPLETATO CON SUCCESSO\n\n";
    echo "Ora puoi eseguire nuovamente le migrations rimanenti.\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

} catch (Exception $e) {
    echo "\n❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}
