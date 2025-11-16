<?php
/**
 * CLEANUP MIGRATION REFERRAL SYSTEM
 *
 * Rimuove il record errato della migration referral_system dalla tabella migrations
 * per permettere la riesecuzione corretta della migration.
 */

define('SECRET', '$Magia2025!');

// Verifica autenticazione
if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized - Secret key richiesta');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔧 CLEANUP MIGRATION REFERRAL SYSTEM\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // Verifica connessione
    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    echo "✅ Connessione database OK\n";
    echo "📦 Database: {$dbName}\n\n";

    $migrationName = '2025_11_16_000907_create_referral_system_table';

    // Verifica se la migration è registrata
    echo "━━━ STEP 1: VERIFICA RECORD MIGRATION ━━━\n\n";

    $migrationRecord = DB::table('migrations')
        ->where('migration', $migrationName)
        ->first();

    if ($migrationRecord) {
        echo "⚠️  Record trovato nella tabella migrations:\n";
        echo "   Migration: {$migrationRecord->migration}\n";
        echo "   Batch: {$migrationRecord->batch}\n\n";

        // Richiedi conferma
        $confirm = $_GET['confirm'] ?? 'NO';

        if ($confirm !== 'YES') {
            echo "⚠️  Per rimuovere questo record, aggiungi: &confirm=YES all'URL\n\n";
            echo "Esempio:\n";
            echo "https://www.agstudio.digital/magia/public/cleanup-referral-migration.php?secret=\$Magia2025!&confirm=YES\n\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "⏸️  OPERAZIONE ANNULLATA - Conferma richiesta\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            exit(0);
        }

        echo "✅ Conferma ricevuta - Procedo con la rimozione...\n\n";

        // Rimuovi il record
        DB::table('migrations')
            ->where('migration', $migrationName)
            ->delete();

        echo "✅ Record rimosso dalla tabella migrations\n\n";
    } else {
        echo "✅ Nessun record trovato nella tabella migrations\n";
        echo "   La migration non è stata registrata.\n\n";
    }

    // Verifica tabella referrals
    echo "━━━ STEP 2: VERIFICA TABELLA REFERRALS ━━━\n\n";

    if (Schema::hasTable('referrals')) {
        $count = DB::table('referrals')->count();
        echo "✅ Tabella 'referrals' esistente ({$count} record)\n";
        echo "   La tabella verrà droppata e ricreata dalla migration.\n\n";
    } else {
        echo "✅ Tabella 'referrals' NON esistente\n";
        echo "   Verrà creata dalla migration.\n\n";
    }

    // Riepilogo
    echo "━━━ RIEPILOGO ━━━\n\n";

    $totalMigrations = DB::table('migrations')->count();
    echo "✅ Totale migrations registrate: {$totalMigrations}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ CLEANUP COMPLETATO\n\n";
    echo "Ora puoi eseguire lo script di migrations:\n";
    echo "https://www.agstudio.digital/magia/public/esegui-migrations.php?secret=\$Magia2025!&confirm=YES\n";
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
