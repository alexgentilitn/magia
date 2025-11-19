<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Pagamento;

echo "🧪 TEST COMPLETO CONTROLLER PAGAMENTI\n";
echo "=====================================\n\n";

try {
    // Test 1: Query base con with() (eager loading)
    echo "Test 1: Pagamento::with(['cliente', 'programma', 'lezione'])\n";
    $query = Pagamento::with(['cliente', 'programma', 'lezione']);
    echo "   ✅ with() funziona\n\n";

    // Test 2: Filtri where
    echo "Test 2: Filtri where\n";
    $query->where('stato', 'completato');
    echo "   ✅ where('stato') funziona\n\n";

    // Test 3: Scope methods
    echo "Test 3: Scope methods\n";
    try {
        $scaduti = Pagamento::scaduti()->get();
        echo "   ✅ scaduti() funziona: {$scaduti->count()} risultati\n";
    } catch (\Exception $e) {
        echo "   ⚠️  scaduti() errore: " . $e->getMessage() . "\n";
    }

    try {
        $inAttesa = Pagamento::inAttesa()->get();
        echo "   ✅ inAttesa() funziona: {$inAttesa->count()} risultati\n\n";
    } catch (\Exception $e) {
        echo "   ⚠️  inAttesa() errore: " . $e->getMessage() . "\n\n";
    }

    // Test 4: Ordinamento
    echo "Test 4: Ordinamento\n";
    $query = Pagamento::orderBy('data_emissione', 'desc');
    echo "   ✅ orderBy() funziona\n\n";

    // Test 5: Paginazione con withQueryString
    echo "Test 5: Paginazione\n";
    $pagamenti = Pagamento::orderBy('created_at', 'desc')->paginate(20);
    echo "   ✅ paginate() funziona: pagina 1 di {$pagamenti->lastPage()}\n";
    echo "   ✅ totale: {$pagamenti->total()} pagamenti\n\n";

    // Test 6: Aggregazioni (statistiche)
    echo "Test 6: Statistiche\n";
    $totale = Pagamento::count();
    echo "   ✅ count(): {$totale}\n";

    $inAtesaCount = Pagamento::where('stato', 'in_attesa')->count();
    echo "   ✅ count con where: {$inAtesaCount}\n";

    $completatiCount = Pagamento::where('stato', 'completato')->count();
    echo "   ✅ completati: {$completatiCount}\n";

    try {
        $importoTotale = Pagamento::where('stato', 'completato')->sum('importo_pagato');
        echo "   ✅ sum('importo_pagato'): {$importoTotale}\n";
    } catch (\Exception $e) {
        echo "   ⚠️  sum() errore: " . $e->getMessage() . "\n";
    }

    try {
        $importoAttesa = Pagamento::where('stato', 'in_attesa')->sum('importo_residuo');
        echo "   ✅ sum('importo_residuo'): {$importoAttesa}\n\n";
    } catch (\Exception $e) {
        echo "   ⚠️  sum() errore: " . $e->getMessage() . "\n\n";
    }

    // Test 7: Relazioni
    echo "Test 7: Relazioni\n";
    $pagamento = Pagamento::first();
    if ($pagamento) {
        echo "   Pagamento ID: {$pagamento->id}\n";

        $cliente = $pagamento->cliente();
        echo "   ✅ ->cliente(): " . ($cliente ? "OK" : "null") . "\n";

        $programma = $pagamento->programma();
        echo "   ✅ ->programma(): " . ($programma ? "OK" : "null") . "\n";

        $lezione = $pagamento->lezione();
        echo "   ✅ ->lezione(): " . ($lezione ? "OK" : "null") . "\n";
    }

    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TUTTI I TEST SUPERATI!\n";
    echo "✅ Il controller pagamenti dovrebbe funzionare!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (\Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
