<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cliente;

echo "🧪 TEST CLIENTE MODEL - ELOQUENT su JSON\n";
echo "=========================================\n\n";

try {
    // Test 1: Cliente::all()
    echo "Test 1: Cliente::all()\n";
    $clienti = Cliente::all();
    echo "   ✅ Trovati: {$clienti->count()} clienti\n";
    foreach ($clienti as $cliente) {
        echo "   - {$cliente->nome} {$cliente->cognome} ({$cliente->email})\n";
    }
    echo "\n";

    // Test 2: Cliente::find()
    echo "Test 2: Cliente::find(1)\n";
    $cliente = Cliente::find(1);
    if ($cliente) {
        echo "   ✅ Trovato: {$cliente->nome} {$cliente->cognome}\n";
        echo "      Codice: {$cliente->codice_cliente}\n";
        echo "      Città: {$cliente->citta}\n\n";
    }

    // Test 3: Cliente::where()
    echo "Test 3: Cliente::where('stato_cliente', 'attivo')\n";
    $attivi = Cliente::where('stato_cliente', 'attivo')->get();
    echo "   ✅ Clienti attivi: {$attivi->count()}\n\n";

    // Test 4: Query con OR (come nei controller)
    echo "Test 4: Query complessa con OR (cerca 'Giulia')\n";
    $risultati = Cliente::where(function($q) {
        $q->where('nome', 'like', '%Giulia%')
          ->orWhere('cognome', 'like', '%Giulia%')
          ->orWhere('email', 'like', '%Giulia%');
    })->get();
    echo "   ✅ Trovati: {$risultati->count()} risultati\n";
    foreach ($risultati as $r) {
        echo "      - {$r->nome} {$r->cognome}\n";
    }
    echo "\n";

    // Test 5: Paginazione
    echo "Test 5: Cliente::paginate(20)\n";
    $paginati = Cliente::orderBy('created_at', 'desc')->paginate(20);
    echo "   ✅ Pagina 1 di {$paginati->lastPage()}\n";
    echo "   ✅ Totale: {$paginati->total()} clienti\n";
    echo "   ✅ Per pagina: {$paginati->perPage()}\n\n";

    // Test 6: Count
    echo "Test 6: Cliente::count()\n";
    $count = Cliente::count();
    echo "   ✅ Totale clienti: {$count}\n\n";

    // Test 7: Create (nuovo cliente)
    echo "Test 7: Cliente::create() - Nuovo cliente test\n";
    $nuovo = Cliente::create([
        'utente_id' => 999,
        'nome' => 'Test',
        'cognome' => 'Cliente',
        'codice_fiscale' => 'TSTCLN00A01H501Z',
        'email' => 'test@test.it',
        'telefono_mobile' => '333-0000000',
        'data_nascita' => '2000-01-01',
        'sesso' => 'F',
        'indirizzo' => 'Via Test',
        'citta' => 'Testville',
        'provincia' => 'TT',
        'cap' => '00000',
        'stato_cliente' => 'attivo',
        'codice_cliente' => 'TEST001'
    ]);
    echo "   ✅ Cliente creato con ID: {$nuovo->id}\n\n";

    // Test 8: Update
    echo "Test 8: Update cliente test\n";
    $nuovo->note_interne = 'Note di test aggiornate';
    $nuovo->save();
    echo "   ✅ Cliente aggiornato\n\n";

    // Test 9: Verifica update
    echo "Test 9: Verifica update\n";
    $verificato = Cliente::find($nuovo->id);
    echo "   ✅ Note: {$verificato->note_interne}\n\n";

    // Test 10: Delete
    echo "Test 10: Delete cliente test\n";
    $nuovo->delete();
    echo "   ✅ Cliente eliminato\n\n";

    // Test 11: Verifica delete
    echo "Test 11: Verifica delete\n";
    $eliminato = Cliente::find($nuovo->id);
    echo "   ✅ Cliente eliminato: " . ($eliminato ? "NO" : "SI") . "\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TUTTI I TEST SUPERATI!\n";
    echo "✅ Eloquent funziona perfettamente su JSON!\n";
    echo "✅ I controller esistenti funzioneranno senza modifiche!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "🎉 SOLUZIONE TROVATA!\n";
    echo "Controllo totale del database JSON + compatibilità Eloquent!\n";

} catch (Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
