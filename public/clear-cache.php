<?php
/**
 * Script per pulire cache Laravel
 * Usa questo quando Laravel non riconosce modifiche al .env
 */

define('CACHE_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== CACHE_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . CACHE_TOKEN . '</p>');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pulizia Cache Laravel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .result {
            margin: 15px 0;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #2196F3;
            background: #f8f9fa;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            overflow-x: auto;
        }
        .emoji { font-size: 1.2em; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><span class="emoji">🧹</span>Pulizia Cache Laravel</h1>

        <?php
        $basePath = dirname(__DIR__);

        echo "<div class='result'>";
        echo "<p><strong>Base Path:</strong> <code>$basePath</code></p>";
        echo "</div>";

        // Array per tracciare le operazioni
        $operations = [];

        // 1. Cancella cache configurazione
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>⚙️</span>Step 1: Cache Configurazione</h3>";
        $configCache = $basePath . '/bootstrap/cache/config.php';
        if (file_exists($configCache)) {
            if (unlink($configCache)) {
                echo "<p class='success'><span class='emoji'>✅</span> File config.php cancellato</p>";
                $operations[] = ['name' => 'Config Cache', 'status' => 'success'];
            } else {
                echo "<p class='error'><span class='emoji'>❌</span> Impossibile cancellare config.php</p>";
                $operations[] = ['name' => 'Config Cache', 'status' => 'error'];
            }
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> File config.php non esiste (già pulito)</p>";
            $operations[] = ['name' => 'Config Cache', 'status' => 'already_clean'];
        }
        echo "</div>";

        // 2. Cancella cache route
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>🛣️</span>Step 2: Cache Route</h3>";
        $routeCache = $basePath . '/bootstrap/cache/routes-v7.php';
        if (file_exists($routeCache)) {
            if (unlink($routeCache)) {
                echo "<p class='success'><span class='emoji'>✅</span> File routes-v7.php cancellato</p>";
                $operations[] = ['name' => 'Route Cache', 'status' => 'success'];
            } else {
                echo "<p class='error'><span class='emoji'>❌</span> Impossibile cancellare routes-v7.php</p>";
                $operations[] = ['name' => 'Route Cache', 'status' => 'error'];
            }
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> File routes-v7.php non esiste (già pulito)</p>";
            $operations[] = ['name' => 'Route Cache', 'status' => 'already_clean'];
        }
        echo "</div>";

        // 3. Cancella cache eventi
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>📅</span>Step 3: Cache Eventi</h3>";
        $eventsCache = $basePath . '/bootstrap/cache/events.php';
        if (file_exists($eventsCache)) {
            if (unlink($eventsCache)) {
                echo "<p class='success'><span class='emoji'>✅</span> File events.php cancellato</p>";
                $operations[] = ['name' => 'Events Cache', 'status' => 'success'];
            } else {
                echo "<p class='error'><span class='emoji'>❌</span> Impossibile cancellare events.php</p>";
                $operations[] = ['name' => 'Events Cache', 'status' => 'error'];
            }
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> File events.php non esiste (già pulito)</p>";
            $operations[] = ['name' => 'Events Cache', 'status' => 'already_clean'];
        }
        echo "</div>";

        // 4. Cancella cache servizi
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>🔧</span>Step 4: Cache Servizi</h3>";
        $servicesCache = $basePath . '/bootstrap/cache/services.php';
        if (file_exists($servicesCache)) {
            if (unlink($servicesCache)) {
                echo "<p class='success'><span class='emoji'>✅</span> File services.php cancellato</p>";
                $operations[] = ['name' => 'Services Cache', 'status' => 'success'];
            } else {
                echo "<p class='error'><span class='emoji'>❌</span> Impossibile cancellare services.php</p>";
                $operations[] = ['name' => 'Services Cache', 'status' => 'error'];
            }
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> File services.php non esiste (già pulito)</p>";
            $operations[] = ['name' => 'Services Cache', 'status' => 'already_clean'];
        }
        echo "</div>";

        // 5. Pulisci directory cache views
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>👁️</span>Step 5: Cache Views</h3>";
        $viewsCache = $basePath . '/storage/framework/views';
        if (is_dir($viewsCache)) {
            $files = glob($viewsCache . '/*');
            $deleted = 0;
            foreach ($files as $file) {
                if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    if (unlink($file)) {
                        $deleted++;
                    }
                }
            }
            echo "<p class='success'><span class='emoji'>✅</span> Cancellati $deleted file di cache views</p>";
            $operations[] = ['name' => 'Views Cache', 'status' => 'success', 'count' => $deleted];
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> Directory views cache non esiste</p>";
            $operations[] = ['name' => 'Views Cache', 'status' => 'not_found'];
        }
        echo "</div>";

        // 6. Pulisci directory cache generale
        echo "<div class='result'>";
        echo "<h3><span class='emoji'>📦</span>Step 6: Cache Generale</h3>";
        $cacheData = $basePath . '/storage/framework/cache/data';
        if (is_dir($cacheData)) {
            $deleted = 0;
            $dirs = glob($cacheData . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        if (unlink($file)) {
                            $deleted++;
                        }
                    }
                }
            }
            echo "<p class='success'><span class='emoji'>✅</span> Cancellati $deleted file di cache generale</p>";
            $operations[] = ['name' => 'General Cache', 'status' => 'success', 'count' => $deleted];
        } else {
            echo "<p class='warning'><span class='emoji'>ℹ️</span> Directory cache data non esiste</p>";
            $operations[] = ['name' => 'General Cache', 'status' => 'not_found'];
        }
        echo "</div>";

        // Riepilogo
        echo "<div class='result success'>";
        echo "<h2><span class='emoji'>✅</span>Riepilogo Operazioni</h2>";
        echo "<ul>";
        foreach ($operations as $op) {
            $icon = $op['status'] === 'success' ? '✅' : ($op['status'] === 'error' ? '❌' : 'ℹ️');
            $count = isset($op['count']) ? " ({$op['count']} files)" : '';
            echo "<li><strong>{$op['name']}:</strong> {$icon} " . ucfirst($op['status']) . $count . "</li>";
        }
        echo "</ul>";
        echo "</div>";

        echo "<div class='result warning'>";
        echo "<h3><span class='emoji'>🎯</span>Prossimi Passi</h3>";
        echo "<ol>";
        echo "<li>Torna alla pagina del professionista</li>";
        echo "<li>Prova a caricare la foto profilo</li>";
        echo "<li>Se funziona, elimina questo file per sicurezza</li>";
        echo "</ol>";
        echo "<div class='code'>rm " . __FILE__ . "</div>";
        echo "</div>";

        ?>
    </div>
</body>
</html>
