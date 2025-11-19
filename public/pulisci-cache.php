<?php
/**
 * Script: Pulisce tutte le cache di Laravel
 */

echo "<h1>🧹 Pulizia Cache Laravel</h1><hr>";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$risultati = [];

// Cache generale
echo "<h2>1️⃣ Cache Generale</h2>";
try {
    Artisan::call('cache:clear');
    echo "✅ Cache generale pulita<br>";
    $risultati['cache'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['cache'] = false;
}

// Config cache
echo "<h2>2️⃣ Config Cache</h2>";
try {
    Artisan::call('config:clear');
    echo "✅ Config cache pulita<br>";
    $risultati['config'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['config'] = false;
}

// Route cache
echo "<h2>3️⃣ Route Cache</h2>";
try {
    Artisan::call('route:clear');
    echo "✅ Route cache pulita<br>";
    $risultati['route'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['route'] = false;
}

// View cache
echo "<h2>4️⃣ View Cache</h2>";
try {
    Artisan::call('view:clear');
    echo "✅ View cache pulita<br>";
    $risultati['view'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['view'] = false;
}

// Event cache
echo "<h2>5️⃣ Event Cache</h2>";
try {
    Artisan::call('event:clear');
    echo "✅ Event cache pulita<br>";
    $risultati['event'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['event'] = false;
}

// Optimized autoloader
echo "<h2>6️⃣ Optimized Autoloader</h2>";
try {
    Artisan::call('clear-compiled');
    echo "✅ Optimized autoloader pulito<br>";
    $risultati['compiled'] = true;
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
    $risultati['compiled'] = false;
}

// OPcache
echo "<h2>7️⃣ OPcache (PHP)</h2>";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache pulita<br>";
    $risultati['opcache'] = true;
} else {
    echo "ℹ️ OPcache non disponibile o non abilitato<br>";
    $risultati['opcache'] = null;
}

echo "<hr>";

// Riepilogo
echo "<h2>📊 Riepilogo</h2>";
$successi = count(array_filter($risultati, fn($v) => $v === true));
$totale = count(array_filter($risultati, fn($v) => $v !== null));

echo "<p><strong>Operazioni riuscite:</strong> {$successi} / {$totale}</p>";

if ($successi === $totale) {
    echo "<div style='background: #dfd; padding: 20px; border-left: 4px solid green; border-radius: 5px;'>";
    echo "<h3 style='color: green; margin-top: 0;'>✅ CACHE PULITA CON SUCCESSO!</h3>";
    echo "<p>Tutte le cache sono state pulite. Il codice aggiornato è ora attivo.</p>";
    echo "<p><strong>Prossimi passi:</strong></p>";
    echo "<ol>";
    echo "<li>Verifica che il codice sia aggiornato: <a href='verifica-aggiornamenti.php' target='_blank'>Verifica Aggiornamenti</a></li>";
    echo "<li>Riprova l'import delle pesate</li>";
    echo "<li>Controlla i risultati: <a href='debug-import-pesate.php' target='_blank'>Debug Import</a></li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid orange; border-radius: 5px;'>";
    echo "<h3 style='color: orange; margin-top: 0;'>⚠️ Alcune cache non sono state pulite</h3>";
    echo "<p>Riprova o contatta il supporto tecnico.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>🔄 Aggiorna il codice</h2>";
echo "<p>Se hai fatto modifiche al codice, assicurati di fare il pull da GitHub:</p>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>cd /percorso/magia
git pull origin claude/magia-2-014g9oftuhszylpebgmbjt-0122wXiaFGHiG8aJQWvpg2yD</pre>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo l'uso!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
