<?php
/**
 * Verifica presenza file sistema permessi
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== VERIFICA FILE SISTEMA PERMESSI ===\n\n";

$files_to_check = [
    'Controller' => __DIR__.'/../app/Http/Controllers/Admin/PermessiCollaboratoreController.php',
    'Routes' => __DIR__.'/../routes/web.php',
    'View' => __DIR__.'/../resources/views/admin/professionisti/permessi.blade.php',
    'Model Permesso' => __DIR__.'/../app/Models/Permesso.php',
    'Model Utente' => __DIR__.'/../app/Models/Utente.php',
];

foreach ($files_to_check as $name => $path) {
    if (file_exists($path)) {
        $size = filesize($path);
        $modified = date('Y-m-d H:i:s', filemtime($path));
        echo "✅ $name: ESISTE\n";
        echo "   Path: $path\n";
        echo "   Size: $size bytes\n";
        echo "   Modified: $modified\n\n";
    } else {
        echo "❌ $name: NON ESISTE\n";
        echo "   Path: $path\n\n";
    }
}

// Verifica contenuto routes/web.php
echo "=== VERIFICA ROTTE IN WEB.PHP ===\n\n";
if (file_exists(__DIR__.'/../routes/web.php')) {
    $content = file_get_contents(__DIR__.'/../routes/web.php');

    if (strpos($content, 'PermessiCollaboratoreController') !== false) {
        echo "✅ PermessiCollaboratoreController trovato in routes/web.php\n\n";

        // Estrai le righe che contengono PermessiCollaboratoreController
        $lines = explode("\n", $content);
        echo "Righe trovate:\n";
        foreach ($lines as $num => $line) {
            if (stripos($line, 'PermessiCollaboratore') !== false) {
                echo "  Riga " . ($num + 1) . ": " . trim($line) . "\n";
            }
        }
    } else {
        echo "❌ PermessiCollaboratoreController NON trovato in routes/web.php\n";
    }
} else {
    echo "❌ routes/web.php non esiste\n";
}

echo "\n=== BOOTSTRAP LARAVEL ===\n\n";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "✅ Laravel bootstrap OK\n\n";

    echo "=== ROTTE REGISTRATE ===\n\n";

    $routes = app('router')->getRoutes();
    $found = false;

    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name && strpos($name, 'professionisti.permessi') !== false) {
            $found = true;
            echo "Route: $name\n";
            echo "  URI: " . $route->uri() . "\n";
            echo "  Methods: " . implode('|', $route->methods()) . "\n";
            echo "  Action: " . $route->getActionName() . "\n\n";
        }
    }

    if (!$found) {
        echo "❌ Nessuna rotta 'professionisti.permessi.*' trovata!\n";
        echo "\nRotte professionisti trovate:\n";
        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && strpos($name, 'professionisti') !== false) {
                echo "  - $name\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FINE ===\n";
