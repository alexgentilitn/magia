<?php
/**
 * DIAGNOSI COMPLETA - Route Segnalazioni e Errore 404
 *
 * Questo script diagnostica il problema 404 sulla route segnalazioni
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 DIAGNOSI ROUTE SEGNALAZIONI - Errore 404\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

echo "📅 Data/Ora: " . date('Y-m-d H:i:s') . "\n\n";

// ============================================
// 1. ROUTE REGISTRATE PER SEGNALAZIONI
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1️⃣  ROUTE REGISTRATE (filtro: segnalazioni)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$routes = Route::getRoutes();
$segnalazioniRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'segnalazioni') !== false || strpos($uri, 'segnalazione') !== false) {
        $segnalazioniRoutes[] = $route;
    }
}

if (empty($segnalazioniRoutes)) {
    echo "⚠️  NESSUNA ROUTE TROVATA per 'segnalazioni'\n\n";
} else {
    echo "Totale route trovate: " . count($segnalazioniRoutes) . "\n\n";

    foreach ($segnalazioniRoutes as $i => $route) {
        echo "Route #" . ($i + 1) . ":\n";
        echo "  URI:     " . $route->uri() . "\n";
        echo "  Name:    " . ($route->getName() ?: 'N/A') . "\n";
        echo "  Methods: " . implode(', ', $route->methods()) . "\n";
        echo "  Action:  " . $route->getActionName() . "\n";

        // Mostra i parametri del route
        $parameterNames = $route->parameterNames();
        if (!empty($parameterNames)) {
            echo "  Params:  " . implode(', ', $parameterNames) . "\n";
        }

        // Mostra i bindings dei parametri
        if (method_exists($route, 'bindingFields')) {
            $bindings = $route->bindingFields();
            if (!empty($bindings)) {
                echo "  Bindings: " . json_encode($bindings) . "\n";
            }
        }

        echo "\n";
    }
}

// ============================================
// 2. TEST ROUTE SPECIFICA
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "2️⃣  TEST ROUTE SPECIFICA: admin/impostazioni/segnalazioni/1\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    $testUrl = 'admin/impostazioni/segnalazioni/1';
    $matchedRoute = null;

    foreach ($routes as $route) {
        if ($route->matches(
            \Illuminate\Http\Request::create('/' . $testUrl, 'GET')
        )) {
            $matchedRoute = $route;
            break;
        }
    }

    if ($matchedRoute) {
        echo "✅ ROUTE TROVATA!\n\n";
        echo "  URI Pattern: " . $matchedRoute->uri() . "\n";
        echo "  Name:        " . ($matchedRoute->getName() ?: 'N/A') . "\n";
        echo "  Controller:  " . $matchedRoute->getActionName() . "\n";
        echo "  Parameters:  " . implode(', ', $matchedRoute->parameterNames()) . "\n\n";
    } else {
        echo "❌ NESSUNA ROUTE MATCHED per: {$testUrl}\n";
        echo "   Questo è il problema!\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Errore durante il test: " . $e->getMessage() . "\n\n";
}

// ============================================
// 3. CONTENUTO FILE routes/web.php
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "3️⃣  CONTENUTO FILE routes/web.php (righe segnalazioni)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$webRoutesPath = base_path('routes/web.php');
if (file_exists($webRoutesPath)) {
    $content = file_get_contents($webRoutesPath);
    $lines = explode("\n", $content);

    echo "File: {$webRoutesPath}\n";
    echo "Righe totali: " . count($lines) . "\n\n";

    // Cerca righe contenenti 'segnalazioni'
    $found = false;
    foreach ($lines as $lineNum => $line) {
        if (stripos($line, 'segnalazioni') !== false) {
            $actualLineNum = $lineNum + 1;

            if (!$found) {
                echo "Righe contenenti 'segnalazioni':\n";
                echo "────────────────────────────────────────────────────────────\n";
                $found = true;
            }

            // Mostra contesto: 2 righe prima e 2 dopo
            $start = max(0, $lineNum - 2);
            $end = min(count($lines) - 1, $lineNum + 2);

            for ($i = $start; $i <= $end; $i++) {
                $marker = ($i === $lineNum) ? '>>>' : '   ';
                $num = str_pad($i + 1, 4, ' ', STR_PAD_LEFT);
                echo "{$marker} {$num}: {$lines[$i]}\n";
            }
            echo "\n";
        }
    }

    if (!$found) {
        echo "⚠️  Nessuna riga trovata con 'segnalazioni'\n\n";
    }
} else {
    echo "❌ File non trovato: {$webRoutesPath}\n\n";
}

// ============================================
// 4. CACHE ROUTES
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "4️⃣  STATO CACHE ROUTES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$cachedRoutesPath = base_path('bootstrap/cache/routes-v7.php');
if (file_exists($cachedRoutesPath)) {
    $size = filesize($cachedRoutesPath);
    $modified = date('Y-m-d H:i:s', filemtime($cachedRoutesPath));

    echo "⚠️  CACHE ROUTES ATTIVA!\n";
    echo "  File:     {$cachedRoutesPath}\n";
    echo "  Size:     " . number_format($size) . " bytes\n";
    echo "  Modified: {$modified}\n\n";
    echo "⚠️  La cache potrebbe contenere route vecchie!\n";
    echo "   Raccomandazione: Eseguire route:clear\n\n";
} else {
    echo "✅ Nessuna cache routes trovata (OK)\n\n";
}

// ============================================
// 5. VERIFICA MODEL SEGNALAZIONE
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "5️⃣  VERIFICA MODEL SEGNALAZIONE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    if (class_exists('App\Models\Segnalazione')) {
        echo "✅ Model Segnalazione trovato\n";

        $model = new \App\Models\Segnalazione();
        echo "  Table:       " . $model->getTable() . "\n";
        echo "  Primary Key: " . $model->getKeyName() . "\n";

        // Conta record
        $count = \App\Models\Segnalazione::count();
        echo "  Record DB:   {$count}\n";

        // Testa se esiste record con ID 1
        $exists = \App\Models\Segnalazione::find(1);
        if ($exists) {
            echo "  Record ID=1: ✅ Esiste\n";
            echo "    Titolo: " . ($exists->titolo ?? 'N/A') . "\n";
        } else {
            echo "  Record ID=1: ❌ Non trovato\n";
        }

        echo "\n";
    } else {
        echo "❌ Model Segnalazione non trovato\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n\n";
}

// ============================================
// 6. RACCOMANDAZIONI
// ============================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "6️⃣  RACCOMANDAZIONI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Per risolvere il problema:\n\n";

if (file_exists($cachedRoutesPath)) {
    echo "1. ⚠️  PRIORITÀ ALTA: Pulire cache routes\n";
    echo "   Esegui: php artisan route:clear\n";
    echo "   Oppure: https://www.agstudio.digital/magia/public/clear-cache.php\n\n";
}

echo "2. Verificare che routes/web.php contenga:\n";
echo "   Route::resource('segnalazioni', SegnalazioniController::class)\n";
echo "       ->parameters(['segnalazioni' => 'segnalazione'])  // ← IMPORTANTE!\n";
echo "       ->except(['edit']);\n\n";

echo "3. Se il fix non è presente, aggiungere manualmente via FTP\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Diagnosi completata!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
