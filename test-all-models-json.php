<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

use App\Models\Cliente;
use App\Models\Utente;
use App\Models\Programma;
use App\Models\Lezione;
use App\Models\Pagamento;
use App\Models\Professionista;
use App\Models\Sede;
use App\Models\Ruolo;

echo "🧪 TEST COMPLETO TUTTI I MODEL - Database JSON\n";
echo "==============================================\n\n";

$models = [
    'Cliente' => Cliente::class,
    'Utente' => Utente::class,
    'Programma' => Programma::class,
    'Lezione' => Lezione::class,
    'Pagamento' => Pagamento::class,
    'Professionista' => Professionista::class,
    'Sede' => Sede::class,
    'Ruolo' => Ruolo::class,
];

$results = [];

foreach ($models as $modelName => $modelClass) {
    echo "📊 Test {$modelName}\n";
    echo str_repeat('-', 50) . "\n";

    try {
        // Test 1: all()
        $all = $modelClass::all();
        echo "   ✅ all(): {$all->count()} record\n";

        // Test 2: count()
        $count = $modelClass::count();
        echo "   ✅ count(): {$count}\n";

        // Test 3: find() se ci sono record
        if ($count > 0) {
            $first = $modelClass::find(1);
            if ($first) {
                echo "   ✅ find(1): OK\n";
            } else {
                echo "   ⚠️  find(1): nessun record con ID 1\n";
            }
        }

        // Test 4: where()
        $results = $modelClass::where('id', '>', 0)->get();
        echo "   ✅ where(): {$results->count()} risultati\n";

        echo "   ✅ {$modelName}: FUNZIONA\n\n";
        $results[$modelName] = true;

    } catch (\Exception $e) {
        echo "   ❌ ERRORE: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
        $results[$modelName] = false;
    }
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 RIEPILOGO TEST\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$passed = 0;
$failed = 0;

foreach ($results as $model => $success) {
    if ($success) {
        echo "✅ {$model}\n";
        $passed++;
    } else {
        echo "❌ {$model}\n";
        $failed++;
    }
}

echo "\n";
echo "Totale: {$passed} passati, {$failed} falliti\n\n";

if ($failed === 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 TUTTI I TEST SUPERATI!\n";
    echo "✅ Database JSON completamente funzionante!\n";
    echo "✅ Tutti i model compatibili con Eloquent!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    exit(0);
} else {
    echo "❌ Alcuni test sono falliti. Verifica gli errori sopra.\n";
    exit(1);
}
