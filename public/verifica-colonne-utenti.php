<?php
/**
 * VERIFICA COLONNE TABELLA UTENTI
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
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔍 VERIFICA STRUTTURA TABELLA UTENTI\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    // Ottieni tutte le colonne della tabella utenti
    $columns = DB::select('DESCRIBE utenti');

    echo "📊 Colonne presenti nella tabella 'utenti':\n\n";

    foreach ($columns as $column) {
        echo sprintf("  • %-30s | %-20s | %s\n",
            $column->Field,
            $column->Type,
            ($column->Null === 'YES' ? 'NULL' : 'NOT NULL')
        );
    }

    echo "\n";

    // Verifica colonne specifiche
    echo "━━━ VERIFICA COLONNE PROBLEMATICHE ━━━\n\n";

    $hasPasswordTempExpires = Schema::hasColumn('utenti', 'password_temp_expires_at');
    $hasDeveCambiarePassword = Schema::hasColumn('utenti', 'deve_cambiare_password');

    echo "password_temp_expires_at: " . ($hasPasswordTempExpires ? '✅ ESISTE' : '❌ NON ESISTE') . "\n";
    echo "deve_cambiare_password: " . ($hasDeveCambiarePassword ? '✅ ESISTE' : '❌ NON ESISTE') . "\n\n";

    // Verifica stato migration
    $migrationName = '2025_11_13_120000_add_password_temp_expires_to_utenti';
    $migrationExists = DB::table('migrations')
        ->where('migration', $migrationName)
        ->exists();

    echo "━━━ STATO MIGRATION ━━━\n\n";
    echo "Migration '{$migrationName}':\n";
    echo ($migrationExists ? '✅ GIÀ ESEGUITA (registrata in tabella migrations)' : '❌ NON ESEGUITA') . "\n\n";

    // Raccomandazione
    echo "━━━ RACCOMANDAZIONE ━━━\n\n";

    if ($hasPasswordTempExpires && $hasDeveCambiarePassword && !$migrationExists) {
        echo "⚠️  Le colonne esistono ma la migration non è registrata.\n";
        echo "    Soluzione: Registrare manualmente la migration come eseguita.\n";
    } elseif (!$hasPasswordTempExpires || !$hasDeveCambiarePassword) {
        echo "ℹ️  Alcune colonne mancano. La migration deve essere eseguita.\n";
    } else {
        echo "✅ Tutto OK - migration già eseguita correttamente.\n";
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    exit(1);
}
