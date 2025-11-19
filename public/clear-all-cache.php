<?php
/**
 * PULIZIA TOTALE CACHE - Include OPcache
 * Esegui questo per forzare reload di tutto il codice
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧹 PULIZIA TOTALE CACHE</h1>";
echo "<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #4ec9b0; }
h2 { color: #569cd6; margin-top: 20px; }
.success { color: #4ec9b0; font-weight: bold; }
.error { color: #f48771; font-weight: bold; }
.warning { color: #dcdcaa; font-weight: bold; }
.box { background: #2d2d2d; padding: 15px; margin: 10px 0; border-left: 3px solid #569cd6; }
</style>";

$results = [];

// 1. OPcache Reset (CRITICO!)
echo "<div class='box'>";
echo "<h2>1️⃣ OPcache Reset</h2>";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<span class='success'>✅ OPcache resettato con successo!</span><br>";
        $results[] = "✅ OPcache";
    } else {
        echo "<span class='error'>❌ Errore reset OPcache</span><br>";
        $results[] = "❌ OPcache";
    }

    // Status OPcache
    $status = opcache_get_status();
    echo "Memoria usata: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB<br>";
    echo "File cached: " . $status['opcache_statistics']['num_cached_scripts'] . "<br>";
} else {
    echo "<span class='warning'>⚠️ OPcache non disponibile</span><br>";
    $results[] = "⚠️ OPcache N/A";
}
echo "</div>";

// 2. Realpath Cache
echo "<div class='box'>";
echo "<h2>2️⃣ Realpath Cache</h2>";
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "<span class='success'>✅ Realpath cache pulito</span><br>";
    $results[] = "✅ Realpath";
} else {
    echo "<span class='warning'>⚠️ clearstatcache non disponibile</span><br>";
    $results[] = "⚠️ Realpath N/A";
}
echo "</div>";

// 3. Laravel Cache
echo "<div class='box'>";
echo "<h2>3️⃣ Laravel Cache</h2>";

// Carica Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    // Cache clear
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "<span class='success'>✅ Laravel cache cleared</span><br>";
    $results[] = "✅ Laravel cache";
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span><br>";
    $results[] = "❌ Laravel cache";
}

try {
    // Config clear
    Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<span class='success'>✅ Config cache cleared</span><br>";
    $results[] = "✅ Config";
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span><br>";
    $results[] = "❌ Config";
}

try {
    // Route clear
    Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "<span class='success'>✅ Route cache cleared</span><br>";
    $results[] = "✅ Route";
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span><br>";
    $results[] = "❌ Route";
}

try {
    // View clear
    Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "<span class='success'>✅ View cache cleared</span><br>";
    $results[] = "✅ View";
} catch (\Exception $e) {
    echo "<span class='error'>❌ Errore: " . $e->getMessage() . "</span><br>";
    $results[] = "❌ View";
}

echo "</div>";

// 4. Verifica codice aggiornato
echo "<div class='box'>";
echo "<h2>4️⃣ Verifica Codice Aggiornato</h2>";

$controllerPath = __DIR__ . '/../app/Http/Controllers/Admin/PesateController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);

    // Cerca fix nel codice
    $hasTipoCliente = strpos($content, "'tipo_cliente' => 'effettiva'") !== false;
    $hasStatoCliente = strpos($content, "'stato_cliente' => 'attivo'") !== false;

    if ($hasTipoCliente && $hasStatoCliente) {
        echo "<span class='success'>✅✅✅ CODICE AGGIORNATO CON FIX!</span><br>";
        echo "✅ tipo_cliente fix presente<br>";
        echo "✅ stato_cliente fix presente<br>";
        $results[] = "✅ Codice aggiornato";
    } else {
        echo "<span class='error'>❌❌❌ CODICE VECCHIO!</span><br>";
        if (!$hasTipoCliente) echo "❌ tipo_cliente fix MANCANTE<br>";
        if (!$hasStatoCliente) echo "❌ stato_cliente fix MANCANTE<br>";
        $results[] = "❌ Codice vecchio";
    }

    echo "<br><strong>Ultima modifica file:</strong> " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "<br>";
} else {
    echo "<span class='error'>❌ File controller non trovato!</span><br>";
    $results[] = "❌ File non trovato";
}

echo "</div>";

// 5. Elimina cache files manualmente
echo "<div class='box'>";
echo "<h2>5️⃣ Eliminazione File Cache</h2>";

$cacheDirs = [
    __DIR__ . '/../bootstrap/cache',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        echo "📁 " . basename($dir) . ": <span class='success'>$deleted file eliminati</span><br>";
    }
}

$results[] = "✅ File cache eliminati";
echo "</div>";

// Riepilogo
echo "<div class='box' style='border-left-color: #4ec9b0;'>";
echo "<h2>✅ RIEPILOGO</h2>";
echo "<ul>";
foreach ($results as $result) {
    echo "<li>$result</li>";
}
echo "</ul>";
echo "<br>";
echo "<p><strong>🎯 Prossimi passi:</strong></p>";
echo "<ol>";
echo "<li>✅ Cache completamente pulita</li>";
echo "<li>🔄 Ricarica questa pagina per verificare OPcache</li>";
echo "<li>🧪 Prova import pesate: Admin → Pesate → Import Excel</li>";
echo "<li>📊 Verifica pesate salvate nel database</li>";
echo "</ol>";
echo "</div>";

echo "<br><br>";
echo "<a href='test-insert-pesata.php' style='color: #4ec9b0;'>🔍 Esegui Test Inserimento</a> | ";
echo "<a href='clear-all-cache.php' style='color: #569cd6;'>🔄 Ricarica Pulizia Cache</a>";
?>
