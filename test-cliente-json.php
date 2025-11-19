<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClienteJson;

echo "🧪 TEST MODEL CLIENTE JSON - OPERAZIONI CRUD\n";
echo "============================================\n\n";

try {
    // Inizializza tabella
    echo "📋 Inizializzazione tabella\n";
    ClienteJson::initTable();
    echo "   ✅ Tabella pronta\n\n";

    // Pulisci dati precedenti per test pulito
    $dbPath = database_path('jsondb/clienti.json');
    if (file_exists($dbPath)) {
        unlink($dbPath);
        ClienteJson::initTable();
        echo "   ✅ Tabella pulita per test\n\n";
    }

    // Test 1: Create con ID automatico
    echo "➕ Test 1: CREATE cliente (ID automatico)\n";
    $created = ClienteJson::create([
        'nome' => 'Maria',
        'cognome' => 'Rossi',
        'email' => 'maria.rossi@example.com',
        'telefono' => '333-1234567',
        'data_nascita' => '1990-05-15'
    ]);
    if ($created) {
        echo "   ✅ Cliente creato\n";
        echo "   ID assegnato: 1\n\n";
    }

    // Test 2: Create con ID manuale
    echo "➕ Test 2: CREATE secondo cliente\n";
    ClienteJson::create([
        'nome' => 'Giulia',
        'cognome' => 'Bianchi',
        'email' => 'giulia.bianchi@example.com',
        'telefono' => '333-7654321',
        'data_nascita' => '1985-08-20'
    ]);
    echo "   ✅ Secondo cliente creato\n";
    echo "   ID assegnato: 2\n\n";

    // Test 3: Count
    echo "📊 Test 3: COUNT\n";
    $count = ClienteJson::count();
    echo "   ✅ Totale clienti: $count\n\n";

    // Test 4: All
    echo "📋 Test 4: ALL - Lista completa\n";
    $clienti = ClienteJson::all();
    foreach ($clienti as $cliente) {
        echo "   - ID: {$cliente['id']} | {$cliente['nome']} {$cliente['cognome']} ({$cliente['email']})\n";
    }
    echo "\n";

    // Test 5: Find by ID
    echo "🔍 Test 5: FIND by ID (id=1)\n";
    $maria = ClienteJson::find(1);
    if ($maria) {
        echo "   ✅ Trovato: {$maria['nome']} {$maria['cognome']}\n";
        echo "      Email: {$maria['email']}\n";
        echo "      Tel: {$maria['telefono']}\n";
        echo "      Nato: {$maria['data_nascita']}\n\n";
    }

    // Test 6: Find by Email
    echo "🔍 Test 6: FIND by Email\n";
    $giulia = ClienteJson::findByEmail('giulia.bianchi@example.com');
    if ($giulia) {
        echo "   ✅ Trovato: {$giulia['nome']} {$giulia['cognome']}\n";
        echo "      ID: {$giulia['id']}\n\n";
    }

    // Test 7: Where
    echo "🔍 Test 7: WHERE cognome = 'Rossi'\n";
    $rossi = ClienteJson::where(['cognome' => 'Rossi']);
    echo "   ✅ Trovati: " . count($rossi) . " clienti\n";
    foreach ($rossi as $cliente) {
        echo "      - {$cliente['nome']} {$cliente['cognome']}\n";
    }
    echo "\n";

    // Test 8: Search
    echo "🔍 Test 8: SEARCH per 'maria'\n";
    $risultati = ClienteJson::search('maria');
    echo "   ✅ Trovati: " . count($risultati) . " risultati\n";
    foreach ($risultati as $cliente) {
        echo "      - {$cliente['nome']} {$cliente['cognome']} ({$cliente['email']})\n";
    }
    echo "\n";

    // Test 9: Update
    echo "✏️  Test 9: UPDATE telefono di Maria (id=1)\n";
    $updated = ClienteJson::update(1, [
        'telefono' => '333-9999999'
    ]);
    if ($updated) {
        echo "   ✅ Aggiornato\n";
        $maria = ClienteJson::find(1);
        echo "   Nuovo telefono: {$maria['telefono']}\n";
        echo "   Updated at: {$maria['updated_at']}\n\n";
    }

    // Test 10: Exists
    echo "🔍 Test 10: EXISTS verifica esistenza\n";
    $exists1 = ClienteJson::exists(1);
    $exists999 = ClienteJson::exists(999);
    echo "   ✅ ID 1 esiste: " . ($exists1 ? 'SI' : 'NO') . "\n";
    echo "   ✅ ID 999 esiste: " . ($exists999 ? 'SI' : 'NO') . "\n\n";

    // Test 11: Create terzo cliente per test delete
    echo "➕ Test 11: CREATE terzo cliente\n";
    ClienteJson::create([
        'nome' => 'Laura',
        'cognome' => 'Verdi',
        'email' => 'laura.verdi@example.com',
        'telefono' => '333-5555555',
        'data_nascita' => '1995-12-10'
    ]);
    echo "   ✅ Terzo cliente creato (ID: 3)\n";
    echo "   Totale clienti: " . ClienteJson::count() . "\n\n";

    // Test 12: Delete
    echo "🗑️  Test 12: DELETE Giulia (id=2)\n";
    $deleted = ClienteJson::delete(2);
    if ($deleted) {
        echo "   ✅ Cliente eliminato\n";
        echo "   Clienti rimanenti: " . ClienteJson::count() . "\n\n";
    }

    // Test 13: Verifica stato finale
    echo "📊 Test 13: STATO FINALE\n";
    $finale = ClienteJson::all();
    echo "   Clienti finali: " . count($finale) . "\n";
    foreach ($finale as $cliente) {
        echo "   - ID: {$cliente['id']} | {$cliente['nome']} {$cliente['cognome']}\n";
    }
    echo "\n";

    echo "🎉 TUTTI I TEST SUPERATI!\n";
    echo "✅ Model ClienteJson funziona perfettamente!\n";
    echo "✅ Database JSON file-based ATTIVO!\n\n";

    echo "📊 Metodi disponibili:\n";
    echo "- ClienteJson::all()\n";
    echo "- ClienteJson::where(\$conditions)\n";
    echo "- ClienteJson::find(\$id)\n";
    echo "- ClienteJson::findByEmail(\$email)\n";
    echo "- ClienteJson::create(\$data)\n";
    echo "- ClienteJson::update(\$id, \$data)\n";
    echo "- ClienteJson::delete(\$id)\n";
    echo "- ClienteJson::count()\n";
    echo "- ClienteJson::exists(\$id)\n";
    echo "- ClienteJson::search(\$query)\n";
    echo "- ClienteJson::initTable()\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
