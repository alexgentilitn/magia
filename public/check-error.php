<?php
/**
 * Script di Verifica Errore 500
 * Controlla lo stato dell'applicazione e mostra errori dettagliati
 */

// Abilita tutti gli errori
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Verifica Errore 500 - MA.GIA DONNA</h1>";
echo "<pre>";

echo "1. Verifica autoloader...\n";
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
    echo "✓ Autoloader caricato\n\n";
} else {
    echo "✗ ERRORE: autoloader non trovato\n";
    die();
}

echo "2. Carica applicazione Laravel...\n";
try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✓ Applicazione Laravel caricata\n\n";
} catch (Exception $e) {
    echo "✗ ERRORE durante il caricamento:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    die();
}

echo "3. Avvia kernel...\n";
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "✓ Kernel avviato\n\n";
} catch (Exception $e) {
    echo "✗ ERRORE durante l'avvio del kernel:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    die();
}

echo "4. Verifica connessione database...\n";
try {
    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    echo "✓ Database connesso: $dbName\n\n";
} catch (Exception $e) {
    echo "✗ ERRORE database:\n";
    echo $e->getMessage() . "\n";
}

echo "5. Verifica route...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    echo "✓ Route caricate: " . $routes->count() . "\n\n";
} catch (Exception $e) {
    echo "✗ ERRORE route:\n";
    echo $e->getMessage() . "\n";
}

echo "6. Verifica configurazione...\n";
try {
    $appName = config('app.name');
    $appEnv = config('app.env');
    $appDebug = config('app.debug') ? 'true' : 'false';
    echo "✓ App Name: $appName\n";
    echo "✓ Environment: $appEnv\n";
    echo "✓ Debug Mode: $appDebug\n\n";
} catch (Exception $e) {
    echo "✗ ERRORE configurazione:\n";
    echo $e->getMessage() . "\n";
}

echo "7. Verifica ultimi log...\n";
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logs = shell_exec("tail -n 50 " . escapeshellarg($logFile));
    if (!empty($logs)) {
        echo "Ultimi 50 righe del log:\n";
        echo "=====================================\n";
        echo $logs;
        echo "\n=====================================\n";
    } else {
        echo "✓ Log vuoto o nessun errore recente\n";
    }
} else {
    echo "⚠ File di log non trovato\n";
}

echo "\n8. Test route dashboard...\n";
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.dashboard');
    if ($route) {
        echo "✓ Route 'admin.dashboard' trovata\n";
        echo "  URI: " . $route->uri() . "\n";
        echo "  Action: " . $route->getActionName() . "\n";
    } else {
        echo "✗ Route 'admin.dashboard' NON trovata\n";
    }
} catch (Exception $e) {
    echo "✗ ERRORE verifica route:\n";
    echo $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "VERIFICA COMPLETATA\n";
echo "========================================\n";

echo "</pre>";

echo "<style>
body { font-family: monospace; background: #f5f5f5; padding: 20px; }
h1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; }
pre { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
</style>";
