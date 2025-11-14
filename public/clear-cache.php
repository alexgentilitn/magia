<?php
/**
 * Script Pulizia Cache Laravel - MA.GIA DONNA
 *
 * Pulisce tutte le cache di Laravel che potrebbero causare errore 500
 * Accesso: https://www.agstudio.digital/magia/public/clear-cache.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧹 Pulizia Cache Laravel</h1>";
echo "<style>body{font-family:monospace;margin:20px;} .ok{color:green;} .error{color:red;}</style>";

$basePath = dirname(__DIR__);
$cleared = [];
$errors = [];

// 1. Elimina cache config
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    if (unlink($configCache)) {
        $cleared[] = "bootstrap/cache/config.php";
    } else {
        $errors[] = "Impossibile eliminare config.php";
    }
}

// 2. Elimina cache routes
$routesCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routesCache)) {
    if (unlink($routesCache)) {
        $cleared[] = "bootstrap/cache/routes-v7.php";
    } else {
        $errors[] = "Impossibile eliminare routes-v7.php";
    }
}

// 3. Elimina cache services
$servicesCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    if (unlink($servicesCache)) {
        $cleared[] = "bootstrap/cache/services.php";
    } else {
        $errors[] = "Impossibile eliminare services.php";
    }
}

// 4. Elimina cache views
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }

    return true;
}

$viewsCache = $basePath . '/storage/framework/views';
if (is_dir($viewsCache)) {
    deleteDirectory($viewsCache);
    $cleared[] = "storage/framework/views/* (tutte le views compilate)";
}

// 5. Elimina cache framework
$frameworkCache = $basePath . '/storage/framework/cache/data';
if (is_dir($frameworkCache)) {
    deleteDirectory($frameworkCache);
    $cleared[] = "storage/framework/cache/data/* (cache applicazione)";
}

// Risultati
echo "<h2>Risultati</h2>";

if (!empty($cleared)) {
    echo "<p class='ok'><strong>✅ Cache pulite:</strong></p>";
    echo "<ul>";
    foreach ($cleared as $item) {
        echo "<li class='ok'>✅ $item</li>";
    }
    echo "</ul>";
}

if (!empty($errors)) {
    echo "<p class='error'><strong>❌ Errori:</strong></p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li class='error'>❌ $error</li>";
    }
    echo "</ul>";
}

if (empty($cleared) && empty($errors)) {
    echo "<p>ℹ️ Nessuna cache da pulire (già pulito)</p>";
}

echo "<hr>";
echo "<p><strong>Prossimo passo:</strong> Prova a ricaricare il sito</p>";
echo "<p><a href='diagnose.php'>🔍 Torna alla Diagnostica</a> | <a href='../'>← Vai al Sito</a></p>";
