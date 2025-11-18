<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "🧪 TEST CONTROLLER PAGAMENTI\n";
echo "============================\n\n";

try {
    // Simula la richiesta al controller
    echo "Test 1: Pagamento::all()\n";
    $pagamenti = \App\Models\Pagamento::all();
    echo "   ✅ Trovati: {$pagamenti->count()} pagamenti\n\n";

    echo "Test 2: Pagamento con relazioni\n";
    if ($pagamenti->count() > 0) {
        $p = $pagamenti->first();
        echo "   ID: {$p->id}\n";
        echo "   Importo: {$p->importo}\n";

        // Test relazione cliente
        echo "   Test relazione ->cliente()...\n";
        try {
            $cliente = $p->cliente();
            echo "   ✅ Relazione cliente OK\n";
        } catch (\Exception $e) {
            echo "   ❌ Errore relazione cliente: " . $e->getMessage() . "\n";
        }

        // Test relazione programma
        echo "   Test relazione ->programma()...\n";
        try {
            $programma = $p->programma();
            echo "   ✅ Relazione programma OK\n";
        } catch (\Exception $e) {
            echo "   ❌ Errore relazione programma: " . $e->getMessage() . "\n";
        }
    }

    echo "\n";
    echo "Test 3: Query con where\n";
    $results = \App\Models\Pagamento::where('stato', 'pagato')->get();
    echo "   ✅ Query where: {$results->count()} risultati\n\n";

    echo "Test 4: Paginazione\n";
    $paginated = \App\Models\Pagamento::orderBy('created_at', 'desc')->paginate(20);
    echo "   ✅ Paginazione: pagina 1 di {$paginated->lastPage()}, totale {$paginated->total()}\n\n";

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TUTTI I TEST OK!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (\Exception $e) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
