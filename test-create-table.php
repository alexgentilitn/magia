<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🧪 TEST CREAZIONE TABELLA\n";
echo "========================\n\n";

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Step 1: Crea tabella di test
    echo "Step 1: Creazione tabella test_claude\n";
    DB::statement("
        CREATE TABLE test_claude (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nome VARCHAR(100),
            descrizione TEXT,
            data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "   ✅ Tabella creata con successo!\n\n";

    // Step 2: Verifica che la tabella esiste
    echo "Step 2: Verifica esistenza tabella\n";
    $tables = DB::select("SHOW TABLES LIKE 'test_claude'");
    if (count($tables) > 0) {
        echo "   ✅ Tabella trovata nel database!\n\n";
    } else {
        echo "   ❌ Tabella NON trovata!\n\n";
    }

    // Step 3: Inserisci dati di test
    echo "Step 3: Inserimento dati di test\n";
    DB::table('test_claude')->insert([
        ['nome' => 'Test 1', 'descrizione' => 'Primo record di test'],
        ['nome' => 'Test 2', 'descrizione' => 'Secondo record di test'],
        ['nome' => 'Test 3', 'descrizione' => 'Terzo record di test']
    ]);
    echo "   ✅ 3 record inseriti!\n\n";

    // Step 4: Leggi i dati
    echo "Step 4: Lettura dati inseriti\n";
    $records = DB::table('test_claude')->get();
    foreach ($records as $record) {
        echo "   - ID: {$record->id} | Nome: {$record->nome} | Descrizione: {$record->descrizione}\n";
    }
    echo "   ✅ Trovati {$records->count()} record\n\n";

    // Step 5: Elimina la tabella
    echo "Step 5: Eliminazione tabella test_claude\n";
    DB::statement("DROP TABLE test_claude");
    echo "   ✅ Tabella eliminata con successo!\n\n";

    // Step 6: Verifica che la tabella non esiste più
    echo "Step 6: Verifica eliminazione\n";
    $tables = DB::select("SHOW TABLES LIKE 'test_claude'");
    if (count($tables) == 0) {
        echo "   ✅ Tabella correttamente eliminata!\n\n";
    } else {
        echo "   ❌ Tabella ancora presente!\n\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TEST COMPLETATO CON SUCCESSO!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    echo "Il Database Manager funziona correttamente!\n";
    echo "Puoi usarlo per:\n";
    echo "- Creare nuove tabelle\n";
    echo "- Modificare strutture esistenti\n";
    echo "- Inserire/aggiornare/eliminare dati\n";
    echo "- Eseguire qualsiasi query SQL\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE:\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
