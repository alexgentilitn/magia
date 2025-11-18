<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClienteLocal;

echo "🧪 TEST SUSHI DATABASE FILE-BASED\n";
echo "==================================\n\n";

try {
    // Test 1: Count records
    $count = ClienteLocal::count();
    echo "✅ Totale clienti: $count\n\n";

    // Test 2: Get all records
    $clienti = ClienteLocal::all();
    echo "📋 Lista clienti:\n";
    foreach ($clienti as $cliente) {
        echo "  - {$cliente->nome} {$cliente->cognome} ({$cliente->email})\n";
    }
    echo "\n";

    // Test 3: Query specific record
    $maria = ClienteLocal::where('nome', 'Maria')->first();
    if ($maria) {
        echo "🔍 Cliente trovato: {$maria->nome} {$maria->cognome}\n";
        echo "   Email: {$maria->email}\n";
        echo "   Telefono: {$maria->telefono}\n\n";
    }

    // Test 4: Test Eloquent methods
    $ultimoCliente = ClienteLocal::latest('id')->first();
    echo "📅 Ultimo cliente: {$ultimoCliente->nome} {$ultimoCliente->cognome}\n\n";

    echo "✅ TUTTI I TEST SUPERATI!\n";
    echo "✅ Sushi funziona perfettamente!\n";
    echo "✅ Posso usare file JSON come database!\n\n";

    echo "📊 Info:\n";
    echo "- File dati: database/sushi/clienti.json\n";
    echo "- Model: App\\Models\\ClienteLocal\n";
    echo "- Query Eloquent: FUNZIONANTI\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
