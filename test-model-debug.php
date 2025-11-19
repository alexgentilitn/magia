<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

use App\Models\Cliente;
use App\Models\Professionista;

echo "🔍 TEST DIAGNOSTICO MODEL\n";
echo "========================\n\n";

echo "Test 1: Professionista (funziona)\n";
echo "───────────────────────────────────\n";
try {
    $p = new Professionista();
    echo "   Classe: " . get_class($p) . "\n";
    echo "   Extends: " . get_parent_class($p) . "\n";
    echo "   Connection: " . var_export($p->getConnectionName(), true) . "\n";
    echo "   Table: {$p->getTable()}\n";
    echo "   JsonTable: " . (Professionista::$jsonTable ?? 'N/A') . "\n";
    $all = Professionista::all();
    echo "   ✅ all() funziona: {$all->count()} record\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
}

echo "Test 2: Cliente (non funziona)\n";
echo "───────────────────────────────────\n";
try {
    $c = new Cliente();
    echo "   Classe: " . get_class($c) . "\n";
    echo "   Extends: " . get_parent_class($c) . "\n";
    echo "   Connection: " . var_export($c->getConnectionName(), true) . "\n";
    echo "   Table: {$c->getTable()}\n";
    echo "   JsonTable: " . (Cliente::$jsonTable ?? 'N/A') . "\n";

    echo "   Tentativo Cliente::all()...\n";
    $all = Cliente::all();
    echo "   ✅ all() funziona: {$all->count()} record\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n";
    foreach ($e->getTrace() as $i => $trace) {
        if ($i > 10) break;
        $file = $trace['file'] ?? 'unknown';
        $line = $trace['line'] ?? 0;
        $function = $trace['function'] ?? 'unknown';
        echo "      #{$i} {$file}:{$line} - {$function}()\n";
    }
    echo "\n";
}
