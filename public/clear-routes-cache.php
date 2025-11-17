<?php
/**
 * Script per pulire la cache delle route
 *
 * Uso: Visita questo file dal browser
 * URL: https://www.agstudio.digital/magia/public/clear-routes-cache.php
 */

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap dell'applicazione
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head><title>Clear Routes Cache</title>";
echo "<style>body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; } .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; } .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; } .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; } pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }</style>";
echo "</head>";
echo "<body>";
echo "<h1>🔧 Pulizia Cache Laravel</h1>";

try {
    echo "<div class='info'>🔄 Pulizia cache in corso...</div>";

    // Pulisci cache route
    Artisan::call('route:clear');
    echo "<div class='success'>✓ Cache route pulita</div>";

    // Pulisci cache config
    Artisan::call('config:clear');
    echo "<div class='success'>✓ Cache configurazione pulita</div>";

    // Pulisci cache view
    Artisan::call('view:clear');
    echo "<div class='success'>✓ Cache view pulita</div>";

    // Pulisci cache applicazione
    Artisan::call('cache:clear');
    echo "<div class='success'>✓ Cache applicazione pulita</div>";

    echo "<div class='success'>";
    echo "<h2>✅ Tutte le cache sono state pulite con successo!</h2>";
    echo "<p>Ora puoi provare ad accedere alla pagina dei permessi:</p>";
    echo "<a href='/magia/public/admin/professionisti/4/permessi' style='display: inline-block; margin: 10px 0; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Vai ai Permessi Collaboratore</a>";
    echo "</div>";

    // Verifica route registrate
    echo "<div class='info'>";
    echo "<h3>📋 Verifica Route Professionisti</h3>";
    echo "<p>Controllo le route registrate per 'professionisti.permessi':</p>";
    echo "<pre>";

    $routes = app('router')->getRoutes();
    $permessiRoutes = [];
    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name && strpos($name, 'professionisti.permessi') !== false) {
            $permessiRoutes[] = [
                'name' => $name,
                'uri' => $route->uri(),
                'methods' => implode('|', $route->methods()),
                'action' => $route->getActionName(),
            ];
        }
    }

    if (empty($permessiRoutes)) {
        echo "⚠️  Nessuna route trovata per 'professionisti.permessi'\n";
    } else {
        foreach ($permessiRoutes as $r) {
            echo "Route: {$r['name']}\n";
            echo "  URI: {$r['uri']}\n";
            echo "  Methods: {$r['methods']}\n";
            echo "  Action: {$r['action']}\n\n";
        }
    }
    echo "</pre>";
    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Errore durante la pulizia cache</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #999; font-size: 12px;'>Script pulizia cache - MA.GIA DONNA</p>";
echo "</body>";
echo "</html>";
