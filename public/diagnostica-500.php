<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "DIAGNOSTICA ERRORE 500\n";
echo "======================\n\n";

// Test 1: Autoload
echo "Test 1: Autoload vendor\n";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "   ✅ Autoload OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore autoload: " . $e->getMessage() . "\n\n";
    die();
}

// Test 2: Bootstrap Laravel
echo "Test 2: Bootstrap Laravel\n";
try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "   ✅ Bootstrap OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore bootstrap: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    die();
}

// Test 3: Kernel
echo "Test 3: Kernel\n";
try {
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    echo "   ✅ Kernel OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore kernel: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    die();
}

// Test 4: Bootstrap kernel
echo "Test 4: Bootstrap Kernel\n";
try {
    $kernel->bootstrap();
    echo "   ✅ Kernel Bootstrap OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore kernel bootstrap: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    die();
}

// Test 5: Carica modelli
echo "Test 5: Carica modelli\n";
try {
    $cliente = new \App\Models\Cliente();
    echo "   ✅ Cliente model OK\n";

    $utente = new \App\Models\Utente();
    echo "   ✅ Utente model OK\n";

    $pagamento = new \App\Models\Pagamento();
    echo "   ✅ Pagamento model OK\n\n";
} catch (\Exception $e) {
    echo "   ❌ Errore modelli: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "   Trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    die();
}

echo "✅ TUTTO OK! L'errore 500 non è qui.\n";
echo "Controlla i log del server web.\n";
