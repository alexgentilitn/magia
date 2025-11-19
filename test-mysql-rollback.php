<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Cliente;
use App\Models\Utente;
use App\Models\Pagamento;

echo "🔙 TEST ROLLBACK A MYSQL\n";
echo "========================\n\n";

try {
    // Test 1: Connessione database
    echo "Test 1: Connessione MySQL\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Connessione MySQL OK\n";
    echo "   Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n\n";

    // Test 2: Cliente model
    echo "Test 2: Cliente::all()\n";
    $clienti = Cliente::all();
    echo "   ✅ Trovati: {$clienti->count()} clienti\n\n";

    // Test 3: Utente model
    echo "Test 3: Utente::all()\n";
    $utenti = Utente::all();
    echo "   ✅ Trovati: {$utenti->count()} utenti\n\n";

    // Test 4: Pagamento model
    echo "Test 4: Pagamento::all()\n";
    $pagamenti = Pagamento::all();
    echo "   ✅ Trovati: {$pagamenti->count()} pagamenti\n\n";

    // Test 5: Relazioni
    echo "Test 5: Test relazioni Eloquent\n";
    if ($utenti->count() > 0) {
        $utente = $utenti->first();
        echo "   Utente: {$utente->nome} {$utente->cognome}\n";

        $ruolo = $utente->ruolo;
        echo "   ✅ Relazione ruolo: " . ($ruolo ? $ruolo->nome : 'N/A') . "\n";
    }

    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ ROLLBACK COMPLETATO!\n";
    echo "✅ Database MySQL funzionante!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE:\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
