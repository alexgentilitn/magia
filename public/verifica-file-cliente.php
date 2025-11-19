<?php
/**
 * Script: Verifica DIRETTA del file Cliente.php (non usa cache)
 */

echo "<h1>🔍 Verifica File Cliente.php</h1><hr>";

$modelPath = __DIR__ . '/../app/Models/Cliente.php';

echo "<h2>1️⃣ Informazioni File</h2>";

if (!file_exists($modelPath)) {
    echo "❌ File non trovato: {$modelPath}<br>";
    exit;
}

echo "✅ File trovato: {$modelPath}<br>";
echo "Ultima modifica: <strong>" . date('Y-m-d H:i:s', filemtime($modelPath)) . "</strong><br>";
echo "Dimensione: <strong>" . filesize($modelPath) . " bytes</strong><br>";

echo "<h2>2️⃣ Verifica Contenuto File</h2>";

// Leggi il contenuto del file
$content = file_get_contents($modelPath);

// Verifica presenza relazione pesate()
echo "<h3>✓ Relazione pesate()</h3>";
if (strpos($content, 'function pesate()') !== false) {
    echo "✅ <strong style='color: green;'>PRESENTE nel file</strong><br>";

    // Mostra il codice della funzione
    if (preg_match('/\/\*\*[\s\S]*?Relazione: Storico pesate[\s\S]*?\*\/[\s\S]*?public function pesate\(\)[\s\S]*?\{[\s\S]*?return[^}]+\}/', $content, $matches)) {
        echo "<h4>Codice trovato:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
        echo htmlspecialchars($matches[0]);
        echo "</pre>";
    }
} else {
    echo "❌ <strong style='color: red;'>MANCANTE nel file</strong><br>";
    echo "<p>Lo script di fix non ha funzionato correttamente.</p>";
}

// Verifica presenza altre relazioni per debug
echo "<h3>✓ Altre Relazioni Presenti</h3>";
$relazioni = [
    'parametriCorporei' => 'Parametri Corporei',
    'utente' => 'Utente',
    'pagamenti' => 'Pagamenti',
    'documenti' => 'Documenti',
];

echo "<ul>";
foreach ($relazioni as $metodo => $nome) {
    if (strpos($content, "function {$metodo}()") !== false) {
        echo "<li>✅ {$nome} - <code>function {$metodo}()</code></li>";
    } else {
        echo "<li>❌ {$nome} - <code>function {$metodo}()</code> mancante</li>";
    }
}
echo "</ul>";

echo "<h2>3️⃣ Test Caricamento Model</h2>";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h3>✓ Verifica con method_exists() [usa cache]</h3>";
if (method_exists(\App\Models\Cliente::class, 'pesate')) {
    echo "✅ <strong style='color: green;'>method_exists() = TRUE</strong> - Il metodo è disponibile<br>";
} else {
    echo "❌ <strong style='color: red;'>method_exists() = FALSE</strong> - Il metodo NON è disponibile (cache vecchia)<br>";
    echo "<p style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<strong>⚠️ PROBLEMA DI CACHE!</strong><br>";
    echo "Il metodo esiste nel file ma Laravel usa ancora la versione in cache.<br>";
    echo "<strong>Soluzione:</strong> <a href='pulisci-cache.php'>Pulisci la cache Laravel</a>";
    echo "</p>";
}

echo "<h3>✓ Test Creazione Istanza</h3>";
try {
    $cliente = new \App\Models\Cliente();
    echo "✅ Istanza creata<br>";

    // Prova a chiamare il metodo
    if (method_exists($cliente, 'pesate')) {
        echo "✅ Metodo pesate() disponibile sull'istanza<br>";

        // Verifica che ritorni una relazione
        $relazione = $cliente->pesate();
        if ($relazione instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
            echo "✅ <strong style='color: green;'>La relazione pesate() funziona correttamente!</strong><br>";
            echo "Tipo: <code>HasMany</code><br>";
            echo "Chiave esterna: <code>cliente_id</code><br>";
        } else {
            echo "❌ Il metodo esiste ma non ritorna una relazione HasMany<br>";
        }
    } else {
        echo "❌ Metodo pesate() NON disponibile sull'istanza<br>";
    }
} catch (\Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "<br>";
}

echo "<hr>";

echo "<h2>📊 Riepilogo</h2>";

$fileHasMethod = strpos($content, 'function pesate()') !== false;
$classHasMethod = method_exists(\App\Models\Cliente::class, 'pesate');

if ($fileHasMethod && $classHasMethod) {
    echo "<div style='background: #dfd; padding: 20px; border-left: 4px solid green; border-radius: 5px;'>";
    echo "<h3 style='color: green; margin-top: 0;'>✅ TUTTO OK!</h3>";
    echo "<p>Il metodo pesate() è presente sia nel file che nella classe caricata.</p>";
    echo "<p><strong>Puoi procedere con l'import delle pesate!</strong></p>";
    echo "</div>";
} elseif ($fileHasMethod && !$classHasMethod) {
    echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid orange; border-radius: 5px;'>";
    echo "<h3 style='color: orange; margin-top: 0;'>⚠️ PROBLEMA DI CACHE</h3>";
    echo "<p>Il metodo pesate() è presente nel file ma Laravel usa ancora la cache vecchia.</p>";
    echo "<p><strong>Soluzione:</strong></p>";
    echo "<ol>";
    echo "<li><a href='pulisci-cache.php' style='font-weight: bold;'>Pulisci la cache Laravel</a></li>";
    echo "<li>Ricarica questa pagina</li>";
    echo "<li>Verifica che entrambi siano ✅</li>";
    echo "</ol>";
    echo "</div>";
} elseif (!$fileHasMethod) {
    echo "<div style='background: #fee; padding: 20px; border-left: 4px solid red; border-radius: 5px;'>";
    echo "<h3 style='color: red; margin-top: 0;'>❌ METODO MANCANTE</h3>";
    echo "<p>Il metodo pesate() NON è presente nel file Cliente.php.</p>";
    echo "<p><strong>Soluzione:</strong></p>";
    echo "<ol>";
    echo "<li><a href='fix-model-cliente.php'>Esegui di nuovo lo script di fix</a></li>";
    echo "<li>Oppure aggiungi manualmente il codice (vedi sotto)</li>";
    echo "</ol>";
    echo "<h4>Codice da aggiungere:</h4>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo htmlspecialchars('    /**
     * Relazione: Storico pesate
     */
    public function pesate()
    {
        return $this->hasMany(Pesata::class, \'cliente_id\')->orderBy(\'data_rilevazione\', \'desc\');
    }');
    echo "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo il debug!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
