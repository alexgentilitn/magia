<?php
/**
 * Test Routes Diagnostic Script
 * Verifica lo stato delle rotte Laravel
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Routes Diagnostic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-top: 0;
        }
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .success {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        .highlight {
            background: #ffffcc;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Routes Diagnostic</h1>
";

try {
    // 1. Verifica Controller
    echo "<h3>1️⃣ Verifica Controller SuperAdminController</h3>";
    if (class_exists('App\\Http\\Controllers\\Admin\\SuperAdminController')) {
        echo "<div class='result success'>✅ Controller trovato: <code>App\\Http\\Controllers\\Admin\\SuperAdminController</code></div>";

        $reflection = new ReflectionClass('App\\Http\\Controllers\\Admin\\SuperAdminController');
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        echo "<div class='result'>📋 Metodi pubblici: ";
        foreach ($methods as $method) {
            if ($method->class === 'App\\Http\\Controllers\\Admin\\SuperAdminController') {
                echo "<code>" . $method->name . "</code> ";
            }
        }
        echo "</div>";
    } else {
        echo "<div class='result error'>❌ Controller NON trovato</div>";
    }

    // 2. Verifica Middleware
    echo "<h3>2️⃣ Verifica Middleware SuperAdminAuth</h3>";
    if (class_exists('App\\Http\\Middleware\\SuperAdminAuth')) {
        echo "<div class='result success'>✅ Middleware trovato: <code>App\\Http\\Middleware\\SuperAdminAuth</code></div>";
    } else {
        echo "<div class='result error'>❌ Middleware NON trovato</div>";
    }

    // 3. Lista tutte le rotte
    echo "<h3>3️⃣ Rotte Registrate</h3>";

    $routes = \Route::getRoutes();
    $superAdminRoutes = [];

    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin/super-admin') !== false) {
            $superAdminRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => implode(', ', $route->middleware())
            ];
        }
    }

    if (count($superAdminRoutes) > 0) {
        echo "<div class='result success'>✅ Trovate <strong>" . count($superAdminRoutes) . "</strong> rotte Super Admin</div>";
        echo "<table>";
        echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Middleware</th></tr>";
        foreach ($superAdminRoutes as $r) {
            echo "<tr class='highlight'>";
            echo "<td><code>{$r['method']}</code></td>";
            echo "<td><code>{$r['uri']}</code></td>";
            echo "<td><code>{$r['name']}</code></td>";
            echo "<td><small>{$r['middleware']}</small></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='result error'>❌ NESSUNA rotta Super Admin trovata!</div>";
        echo "<div class='result warning'>⚠️ Questo indica che il file routes/web.php non è stato caricato correttamente o le rotte non sono state registrate.</div>";
    }

    // 4. Verifica file routes/web.php
    echo "<h3>4️⃣ Verifica File Routes</h3>";
    $routesFile = base_path('routes/web.php');
    if (file_exists($routesFile)) {
        echo "<div class='result success'>✅ File trovato: <code>routes/web.php</code></div>";

        $routesContent = file_get_contents($routesFile);
        if (strpos($routesContent, 'super-admin') !== false) {
            echo "<div class='result success'>✅ Il file contiene riferimenti a 'super-admin'</div>";
        } else {
            echo "<div class='result error'>❌ Il file NON contiene riferimenti a 'super-admin'</div>";
        }

        if (strpos($routesContent, 'SuperAdminController') !== false) {
            echo "<div class='result success'>✅ Il file contiene riferimenti a 'SuperAdminController'</div>";
        } else {
            echo "<div class='result error'>❌ Il file NON contiene riferimenti a 'SuperAdminController'</div>";
        }
    } else {
        echo "<div class='result error'>❌ File routes/web.php NON trovato!</div>";
    }

    // 5. Verifica cache
    echo "<h3>5️⃣ Verifica Cache Stato</h3>";
    $cacheFiles = [
        'routes' => base_path('bootstrap/cache/routes-v7.php'),
        'config' => base_path('bootstrap/cache/config.php'),
    ];

    foreach ($cacheFiles as $name => $file) {
        if (file_exists($file)) {
            echo "<div class='result warning'>⚠️ Cache <strong>$name</strong> presente: <code>" . basename($file) . "</code> (modificato: " . date('Y-m-d H:i:s', filemtime($file)) . ")</div>";
        } else {
            echo "<div class='result success'>✅ Cache <strong>$name</strong> assente (buono)</div>";
        }
    }

    // 6. Test URL specifico
    echo "<h3>6️⃣ Test Routing Specifico</h3>";
    try {
        $route = \Route::getRoutes()->match(
            \Illuminate\Http\Request::create('/admin/super-admin/clear-all-cache', 'POST')
        );
        echo "<div class='result success'>✅ La rotta <code>POST /admin/super-admin/clear-all-cache</code> è registrata correttamente</div>";
        echo "<div class='result'>Controller: <code>" . $route->getActionName() . "</code></div>";
    } catch (\Exception $e) {
        echo "<div class='result error'>❌ La rotta <code>POST /admin/super-admin/clear-all-cache</code> NON è stata trovata!</div>";
        echo "<div class='result error'>Errore: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

} catch (Exception $e) {
    echo "<div class='result error'>❌ Errore generale: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "
        <hr style='margin: 30px 0;'>
        <div class='result warning'>
            <strong>📝 Analisi:</strong><br>
            Se vedi rotte registrate qui ma ricevi 404 nel browser, il problema potrebbe essere:<br>
            1. Cache OPcache di PHP (chiedi all'hosting di svuotarla)<br>
            2. File .htaccess mancante o non corretto<br>
            3. Configurazione Apache/Nginx del server
        </div>
    </div>
</body>
</html>
";
