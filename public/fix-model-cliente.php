<?php
/**
 * Script: Aggiunge la relazione pesate() al Model Cliente
 */

echo "<h1>🔧 Fix Model Cliente</h1><hr>";

$modelPath = __DIR__ . '/../app/Models/Cliente.php';

echo "<h2>1️⃣ Verifica File</h2>";

if (!file_exists($modelPath)) {
    echo "❌ File non trovato: {$modelPath}<br>";
    exit;
}

echo "✅ File trovato: {$modelPath}<br>";

// Leggi il contenuto
$content = file_get_contents($modelPath);

// Verifica se la relazione esiste già
if (strpos($content, 'function pesate()') !== false) {
    echo "<div style='background: #dfd; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h2 style='color: green;'>✅ La relazione pesate() esiste già!</h2>";
    echo "<p>Il Model Cliente è già aggiornato. Non serve fare nulla.</p>";
    echo "</div>";

    echo "<hr>";
    echo "<p><a href='verifica-aggiornamenti.php'>← Torna alla verifica aggiornamenti</a></p>";
    exit;
}

echo "<h2>2️⃣ Backup File Originale</h2>";

$backupPath = $modelPath . '.backup.' . date('Y-m-d_H-i-s');
if (copy($modelPath, $backupPath)) {
    echo "✅ Backup creato: {$backupPath}<br>";
} else {
    echo "❌ Impossibile creare backup<br>";
    exit;
}

echo "<h2>3️⃣ Aggiunta Relazione pesate()</h2>";

// Trova il punto dove inserire la nuova relazione
// Cerco dopo la funzione parametriCorporei()
$searchPattern = '/public function parametriCorporei\(\)[\s\S]*?\{[\s\S]*?return[^}]+\}[\s\n]*/';

if (preg_match($searchPattern, $content, $matches)) {
    $insertAfter = $matches[0];

    $newRelation = $insertAfter . "\n" . '    /**
     * Relazione: Storico pesate
     */
    public function pesate()
    {
        return $this->hasMany(Pesata::class, \'cliente_id\')->orderBy(\'data_rilevazione\', \'desc\');
    }

';

    // Sostituisci
    $newContent = str_replace($insertAfter, $newRelation, $content);

    // Salva il file
    if (file_put_contents($modelPath, $newContent)) {
        echo "✅ Relazione pesate() aggiunta con successo!<br>";

        echo "<div style='background: #dfd; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h2 style='color: green;'>✅ FIX APPLICATO CON SUCCESSO!</h2>";
        echo "<p>La relazione pesate() è stata aggiunta al Model Cliente.</p>";
        echo "<p><strong>Prossimi passi:</strong></p>";
        echo "<ol>";
        echo "<li><a href='pulisci-cache.php'>Pulisci la cache Laravel</a></li>";
        echo "<li><a href='verifica-aggiornamenti.php'>Verifica aggiornamenti</a> (dovrebbe mostrare 4/4)</li>";
        echo "<li>Riprova l'import delle pesate</li>";
        echo "</ol>";
        echo "</div>";

        echo "<h2>4️⃣ Codice Aggiunto</h2>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
        echo htmlspecialchars('    /**
     * Relazione: Storico pesate
     */
    public function pesate()
    {
        return $this->hasMany(Pesata::class, \'cliente_id\')->orderBy(\'data_rilevazione\', \'desc\');
    }');
        echo "</pre>";

    } else {
        echo "❌ Impossibile salvare il file<br>";
        echo "<p>Ripristino backup...</p>";
        copy($backupPath, $modelPath);
        echo "✅ Backup ripristinato<br>";
    }

} else {
    echo "❌ Impossibile trovare il punto di inserimento nel file<br>";
    echo "<p>Il file potrebbe avere una struttura diversa dal previsto.</p>";
    echo "<p><strong>Soluzione manuale:</strong></p>";
    echo "<ol>";
    echo "<li>Apri il file: <code>app/Models/Cliente.php</code></li>";
    echo "<li>Cerca la funzione <code>parametriCorporei()</code></li>";
    echo "<li>Subito dopo, aggiungi questo codice:</li>";
    echo "</ol>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo htmlspecialchars('    /**
     * Relazione: Storico pesate
     */
    public function pesate()
    {
        return $this->hasMany(Pesata::class, \'cliente_id\')->orderBy(\'data_rilevazione\', \'desc\');
    }');
    echo "</pre>";
}

echo "<hr>";
echo "<h2>📂 File di Backup</h2>";
echo "<p>In caso di problemi, puoi ripristinare il file originale da:</p>";
echo "<code>{$backupPath}</code>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo l'uso!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
