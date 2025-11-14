<?php
/**
 * TROVA TABELLE DI TEST
 * Identifica tutte le tabelle che potrebbero essere di test
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 RICERCA TABELLE DI TEST\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Ottieni tutte le tabelle
$allTables = DB::select('SHOW TABLES');
$dbName = DB::select('SELECT DATABASE() as db')[0]->db;
$columnName = "Tables_in_{$dbName}";

echo "📊 Database: {$dbName}\n";
echo "📋 Totale tabelle: " . count($allTables) . "\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📝 TUTTE LE TABELLE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$testTables = [];
$prodTables = [];

foreach ($allTables as $table) {
    $tableName = $table->$columnName;

    // Identifica tabelle di test (contengono "test" o "temp" o "tmp" o "demo")
    if (
        stripos($tableName, 'test') !== false ||
        stripos($tableName, 'temp') !== false ||
        stripos($tableName, 'tmp') !== false ||
        stripos($tableName, 'demo') !== false ||
        stripos($tableName, 'backup') !== false
    ) {
        $testTables[] = $tableName;
        echo "🧪 TEST:  {$tableName}\n";
    } else {
        $prodTables[] = $tableName;
        echo "✅ PROD:  {$tableName}\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 RIEPILOGO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Tabelle produzione: " . count($prodTables) . "\n";
foreach ($prodTables as $t) {
    echo "   - {$t}\n";
}

echo "\n🧪 Tabelle test/temporanee: " . count($testTables) . "\n";
if (count($testTables) > 0) {
    foreach ($testTables as $t) {
        echo "   - {$t}\n";
    }
} else {
    echo "   Nessuna tabella di test trovata!\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (count($testTables) > 0) {
    echo "⚠️  AZIONE CONSIGLIATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Trovate " . count($testTables) . " tabelle di test.\n";
    echo "Vuoi eliminarle? Esegui il file di pulizia.\n";
} else {
    echo "✅ NESSUNA AZIONE NECESSARIA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Il database contiene solo tabelle di produzione.\n";
    echo "Non ci sono tabelle di test da eliminare.\n";
}
