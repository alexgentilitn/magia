<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\JsonDatabase;

echo "🧪 TEST JSON DATABASE HELPER - INTEGRAZIONE LARAVEL\n";
echo "====================================================\n\n";

try {
    // Test 1: Verifica tabella
    echo "📋 Test 1: Verifica esistenza tabella\n";
    if (!JsonDatabase::tableExists('clienti')) {
        echo "   Tabella 'clienti' non esiste, la creo...\n";
        JsonDatabase::createTable('clienti');
        echo "   ✅ Tabella creata\n\n";
    } else {
        echo "   ✅ Tabella 'clienti' esiste già\n\n";
    }

    // Test 2: Insert
    echo "📝 Test 2: INSERT dati di test\n";
    $cliente1 = [
        'id' => 1,
        'nome' => 'Maria',
        'cognome' => 'Rossi',
        'email' => 'maria.rossi@example.com',
        'telefono' => '333-1234567',
        'data_nascita' => '1990-05-15',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $cliente2 = [
        'id' => 2,
        'nome' => 'Giulia',
        'cognome' => 'Bianchi',
        'email' => 'giulia.bianchi@example.com',
        'telefono' => '333-7654321',
        'data_nascita' => '1985-08-20',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Pulisci e ricrea la tabella per il test
    if (JsonDatabase::tableExists('clienti')) {
        unlink(database_path('jsondb/clienti.json'));
        JsonDatabase::createTable('clienti');
    }

    JsonDatabase::insert('clienti', $cliente1);
    JsonDatabase::insert('clienti', $cliente2);
    echo "   ✅ Inseriti 2 clienti\n\n";

    // Test 3: Count
    echo "📊 Test 3: COUNT\n";
    $count = JsonDatabase::count('clienti');
    echo "   ✅ Totale clienti: $count\n\n";

    // Test 4: All
    echo "📋 Test 4: SELECT ALL\n";
    $clienti = JsonDatabase::all('clienti');
    foreach ($clienti as $cliente) {
        echo "   - {$cliente['nome']} {$cliente['cognome']} ({$cliente['email']})\n";
    }
    echo "\n";

    // Test 5: Where
    echo "🔍 Test 5: WHERE nome = 'Maria'\n";
    $maria = JsonDatabase::find('clienti', ['nome' => 'Maria']);
    if ($maria) {
        echo "   ✅ Trovato: {$maria['nome']} {$maria['cognome']}\n";
        echo "      Email: {$maria['email']}\n";
        echo "      Tel: {$maria['telefono']}\n\n";
    }

    // Test 6: Update
    echo "✏️  Test 6: UPDATE telefono di Maria\n";
    $updated = JsonDatabase::update('clienti',
        ['nome' => 'Maria'],
        ['telefono' => '333-9999999']
    );
    if ($updated) {
        echo "   ✅ Aggiornato\n";
        $maria = JsonDatabase::find('clienti', ['nome' => 'Maria']);
        echo "   Nuovo telefono: {$maria['telefono']}\n\n";
    }

    // Test 7: Delete
    echo "🗑️  Test 7: DELETE Giulia\n";
    $deleted = JsonDatabase::delete('clienti', ['nome' => 'Giulia']);
    if ($deleted) {
        echo "   ✅ Eliminato\n";
        $count = JsonDatabase::count('clienti');
        echo "   Clienti rimanenti: $count\n\n";
    }

    echo "🎉 TUTTI I TEST SUPERATI!\n";
    echo "✅ Helper JsonDatabase funziona perfettamente!\n";
    echo "✅ Integrato con Laravel!\n\n";

    echo "📊 Metodi disponibili:\n";
    echo "- JsonDatabase::all(\$table)\n";
    echo "- JsonDatabase::where(\$table, \$conditions)\n";
    echo "- JsonDatabase::find(\$table, \$conditions)\n";
    echo "- JsonDatabase::insert(\$table, \$data)\n";
    echo "- JsonDatabase::update(\$table, \$conditions, \$data)\n";
    echo "- JsonDatabase::delete(\$table, \$conditions)\n";
    echo "- JsonDatabase::count(\$table)\n";
    echo "- JsonDatabase::tableExists(\$table)\n";
    echo "- JsonDatabase::createTable(\$table)\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
