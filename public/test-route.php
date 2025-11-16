<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "TEST ROUTE E MIDDLEWARE\n";
echo "=======================\n\n";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';

    echo "✅ Bootstrap OK\n\n";

    // Simula una richiesta HTTP
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "Test 1: Route /admin/login (GET)\n";
    $request = Illuminate\Http\Request::create('/admin/login', 'GET');

    try {
        $response = $kernel->handle($request);
        echo "   Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() == 200) {
            echo "   ✅ Route /admin/login funziona!\n\n";
        } else if ($response->getStatusCode() == 302) {
            echo "   ⚠️  Redirect a: " . $response->headers->get('Location') . "\n\n";
        } else {
            echo "   ❌ Errore status code\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERRORE: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "   Trace:\n";
        echo substr($e->getTraceAsString(), 0, 1000) . "\n\n";
    }

    echo "Test 2: Route /admin/pagamenti (GET)\n";
    $request = Illuminate\Http\Request::create('/admin/pagamenti', 'GET');

    try {
        $response = $kernel->handle($request);
        echo "   Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() == 200) {
            echo "   ✅ Route /admin/pagamenti funziona!\n\n";
        } else if ($response->getStatusCode() == 302) {
            echo "   ⚠️  Redirect (probabilmente richiede login)\n";
            echo "   Location: " . $response->headers->get('Location') . "\n\n";
        } else {
            echo "   ❌ Errore status code\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERRORE: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "   Trace:\n";
        echo substr($e->getTraceAsString(), 0, 1000) . "\n\n";
    }

    echo "Test 3: Route / (homepage)\n";
    $request = Illuminate\Http\Request::create('/', 'GET');

    try {
        $response = $kernel->handle($request);
        echo "   Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() == 200) {
            echo "   ✅ Homepage funziona!\n\n";
        } else if ($response->getStatusCode() == 302) {
            echo "   ⚠️  Redirect a: " . $response->headers->get('Location') . "\n\n";
        } else {
            echo "   ❌ Errore status code\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERRORE: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "   Trace:\n";
        echo substr($e->getTraceAsString(), 0, 1000) . "\n\n";
    }

    $kernel->terminate($request, $response);

} catch (\Exception $e) {
    echo "\n❌ ERRORE CRITICO:\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
