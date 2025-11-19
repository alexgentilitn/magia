<?php
/**
 * ELIMINA TABELLE DI TEST
 * Elimina solo le tabelle identificate come test/temporanee
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🗑️  ELIMINAZIONE TABELLE DI TEST\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Lista tabelle di test da eliminare
$testTablesToDelete = [
    'test_deployment'
];

echo "📋 Tabelle da eliminare: " . count($testTablesToDelete) . "\n\n";

$deleted = [];
$errors = [];

foreach ($testTablesToDelete as $tableName) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🗑️  Eliminazione: {$tableName}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    try {
        // Verifica se la tabella esiste
        $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");

        if (count($exists) === 0) {
            echo "⚠️  Tabella non trovata - già eliminata o non esiste\n\n";
            continue;
        }

        // Verifica struttura tabella
        $structure = DB::select("DESCRIBE {$tableName}");
        echo "📊 Colonne: " . count($structure) . "\n";

        // Conta record
        $count = DB::select("SELECT COUNT(*) as total FROM {$tableName}")[0]->total;
        echo "📝 Record: {$count}\n\n";

        // Elimina la tabella
        DB::statement("DROP TABLE {$tableName}");
        echo "✅ Tabella eliminata con successo!\n\n";

        $deleted[] = $tableName;

    } catch (\Exception $e) {
        echo "❌ ERRORE: " . $e->getMessage() . "\n\n";
        $errors[] = [
            'table' => $tableName,
            'error' => $e->getMessage()
        ];
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 RIEPILOGO FINALE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Tabelle eliminate: " . count($deleted) . "\n";
if (count($deleted) > 0) {
    foreach ($deleted as $t) {
        echo "   - {$t}\n";
    }
}

echo "\n";

if (count($errors) > 0) {
    echo "❌ Errori: " . count($errors) . "\n";
    foreach ($errors as $err) {
        echo "   - {$err['table']}: {$err['error']}\n";
    }
} else {
    echo "✅ Nessun errore\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (count($deleted) > 0) {
    echo "✅ PULIZIA COMPLETATA CON SUCCESSO!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Il database è ora pulito.\n";
    echo "Tutte le tabelle di test sono state eliminate.\n";
    echo "Le tabelle di produzione sono intatte.\n";
} else {
    echo "ℹ️  NESSUNA MODIFICA EFFETTUATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Non c'erano tabelle di test da eliminare.\n";
}

echo "\n";
echo "🔍 Per verificare: apri trova-tabelle-test.php\n";
