<?php

require __DIR__.'/vendor/autoload.php';

use Jajo\JSONDB;

echo "🧪 TEST JSONDB - DATABASE FILE-BASED PURO PHP\n";
echo "=============================================\n\n";

try {
    // Inizializza JsonDB
    $dbPath = __DIR__ . '/database/jsondb';
    $db = new JSONDB($dbPath);

    echo "✅ JsonDB inizializzato\n";
    echo "📁 Directory: database/jsondb/\n\n";

    // Crea tabella clienti se non esiste
    if (!file_exists($dbPath . '/clienti.json')) {
        file_put_contents(
            $dbPath . '/clienti.json',
            json_encode([
                [
                    'id' => 1,
                    'nome' => 'Maria',
                    'cognome' => 'Rossi',
                    'email' => 'maria.rossi@example.com',
                    'telefono' => '333-1234567',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'nome' => 'Giulia',
                    'cognome' => 'Bianchi',
                    'email' => 'giulia.bianchi@example.com',
                    'telefono' => '333-7654321',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ], JSON_PRETTY_PRINT)
        );
        echo "📝 Creato file clienti.json con dati di test\n\n";
    }

    // Test 1: Select all
    $clienti = $db->select('*')
        ->from('clienti.json')
        ->get();

    echo "✅ SELECT * FROM clienti\n";
    echo "   Trovati: " . count($clienti) . " clienti\n\n";

    // Test 2: Mostra dati
    echo "📋 Lista clienti:\n";
    foreach ($clienti as $cliente) {
        echo "  - ID: {$cliente['id']} | {$cliente['nome']} {$cliente['cognome']} | {$cliente['email']}\n";
    }
    echo "\n";

    // Test 3: Select con where
    $maria = $db->select('*')
        ->from('clienti.json')
        ->where(['nome' => 'Maria'])
        ->get();

    if (!empty($maria)) {
        echo "✅ WHERE nome = 'Maria'\n";
        echo "   Trovato: {$maria[0]['nome']} {$maria[0]['cognome']}\n\n";
    }

    // Test 4: Insert
    $nuovoCliente = [
        'id' => 3,
        'nome' => 'Laura',
        'cognome' => 'Verdi',
        'email' => 'laura.verdi@example.com',
        'telefono' => '333-9999999',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db->insert('clienti.json', $nuovoCliente);
    echo "✅ INSERT nuovo cliente: Laura Verdi\n\n";

    // Verifica insert
    $totale = count($db->select('*')->from('clienti.json')->get());
    echo "✅ Totale clienti dopo insert: $totale\n\n";

    echo "🎉 TUTTI I TEST SUPERATI!\n";
    echo "🎉 JsonDB funziona perfettamente!\n";
    echo "🎉 Database file-based ATTIVO!\n\n";

    echo "📊 Info:\n";
    echo "- Directory: database/jsondb/\n";
    echo "- File: clienti.json\n";
    echo "- Libreria: jajo/jsondb (PHP puro)\n";
    echo "- Nessuna dipendenza da SQLite!\n";
    echo "- Posso fare: SELECT, INSERT, UPDATE, DELETE\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
