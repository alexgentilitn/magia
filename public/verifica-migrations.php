<?php
/**
 * VERIFICA STATO MIGRATIONS
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $migrations = DB::table('migrations')
        ->orderBy('id')
        ->get();

    echo "Totale record in tabella migrations: " . $migrations->count() . "\n\n";

    foreach ($migrations as $m) {
        echo sprintf("[%3d] Batch %2d: %s\n", $m->id, $m->batch, $m->migration);
    }

    echo "\n\n";

    // Cerca record referral
    $referral = DB::table('migrations')
        ->where('migration', '2025_11_16_000907_create_referral_system_table')
        ->first();

    if ($referral) {
        echo "⚠️  TROVATO RECORD MIGRATION REFERRAL:\n";
        echo "   ID: {$referral->id}\n";
        echo "   Batch: {$referral->batch}\n";
        echo "   Migration: {$referral->migration}\n\n";

        $confirm = $_GET['delete'] ?? 'NO';
        if ($confirm === 'YES') {
            DB::table('migrations')->where('id', $referral->id)->delete();
            echo "✅ Record rimosso!\n";
        } else {
            echo "Per rimuoverlo aggiungi: &delete=YES\n";
        }
    } else {
        echo "✅ Nessun record referral trovato\n";
    }

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
}
