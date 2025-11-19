<?php
/**
 * Script: Verifica se il codice sul server è aggiornato
 */

echo "<h1>🔄 Verifica Aggiornamenti Codice</h1><hr>";

// Test 1: Verifica versione codice
echo "<h2>1️⃣ Verifica Versione Codice</h2>";

$fixPresenti = [];
$fixMancanti = [];

// Check 1: Relazione pesate() nel model Cliente
echo "<h3>✓ Relazione pesate() nel Model Cliente</h3>";
if (method_exists(\App\Models\Cliente::class, 'pesate')) {
    echo "✅ <strong style='color: green;'>PRESENTE</strong> - Il model Cliente ha la relazione pesate()<br>";
    $fixPresenti[] = "Relazione pesate()";
} else {
    echo "❌ <strong style='color: red;'>MANCANTE</strong> - Il model Cliente NON ha la relazione pesate()<br>";
    $fixMancanti[] = "Relazione pesate()";
}

// Check 2: Fix generaCodiceCliente() con verifica duplicati
echo "<h3>✓ Fix Codice Cliente Univoco</h3>";
$riflessione = new ReflectionMethod(\App\Models\Cliente::class, 'generaCodiceCliente');
$sourceCode = file_get_contents($riflessione->getFileName());
$methodStart = $riflessione->getStartLine();
$methodEnd = $riflessione->getEndLine();
$lines = file($riflessione->getFileName());
$methodCode = implode('', array_slice($lines, $methodStart - 1, $methodEnd - $methodStart + 1));

if (stripos($methodCode, 'withTrashed') !== false && stripos($methodCode, 'while') !== false) {
    echo "✅ <strong style='color: green;'>PRESENTE</strong> - La funzione generaCodiceCliente() verifica duplicati<br>";
    $fixPresenti[] = "Fix codice_cliente univoco";
} else {
    echo "❌ <strong style='color: red;'>MANCANTE</strong> - La funzione generaCodiceCliente() NON ha il fix per duplicati<br>";
    $fixMancanti[] = "Fix codice_cliente univoco";
}

// Check 3: Protezioni admin nel model Utente
echo "<h3>✓ Protezioni Admin/Professionisti</h3>";
if (method_exists(\App\Models\Utente::class, 'boot')) {
    $riflessione = new ReflectionMethod(\App\Models\Utente::class, 'boot');
    $sourceCode = file_get_contents($riflessione->getFileName());

    if (stripos($sourceCode, 'PROTEZIONE') !== false) {
        echo "✅ <strong style='color: green;'>PRESENTE</strong> - Il model Utente ha le protezioni anti-delete<br>";
        $fixPresenti[] = "Protezioni admin";
    } else {
        echo "❌ <strong style='color: red;'>MANCANTE</strong> - Il model Utente NON ha le protezioni<br>";
        $fixMancanti[] = "Protezioni admin";
    }
} else {
    echo "❌ <strong style='color: red;'>MANCANTE</strong> - Il model Utente NON ha il metodo boot()<br>";
    $fixMancanti[] = "Protezioni admin";
}

// Check 4: Logging dettagliato import
echo "<h3>✓ Logging Dettagliato Import</h3>";
$controllerPath = app_path('Http/Controllers/Admin/PesateController.php');
$controllerCode = file_get_contents($controllerPath);

if (stripos($controllerCode, '=== INIZIO IMPORT PESATE ===') !== false) {
    echo "✅ <strong style='color: green;'>PRESENTE</strong> - Il controller ha logging dettagliato<br>";
    $fixPresenti[] = "Logging dettagliato";
} else {
    echo "❌ <strong style='color: red;'>MANCANTE</strong> - Il controller NON ha logging dettagliato<br>";
    $fixMancanti[] = "Logging dettagliato";
}

echo "<hr>";

// Riepilogo
echo "<h2>📊 Riepilogo</h2>";
echo "<p><strong>Fix presenti:</strong> " . count($fixPresenti) . " / 4</p>";
echo "<p><strong>Fix mancanti:</strong> " . count($fixMancanti) . " / 4</p>";

if (count($fixMancanti) > 0) {
    echo "<div style='background: #fee; padding: 20px; border-left: 4px solid red; border-radius: 5px;'>";
    echo "<h3 style='color: red; margin-top: 0;'>⚠️ CODICE NON AGGIORNATO</h3>";
    echo "<p>Il server ha codice vecchio. Devi aggiornare:</p>";
    echo "<ul>";
    foreach ($fixMancanti as $fix) {
        echo "<li><strong>{$fix}</strong></li>";
    }
    echo "</ul>";

    echo "<h4>🔧 Come aggiornare:</h4>";
    echo "<p><strong>Opzione 1 - Via SSH (CONSIGLIATO):</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>ssh tuoutente@agstudio.digital
cd /percorso/magia
git pull origin claude/magia-2-014g9oftuhszylpebgmbjt-0122wXiaFGHiG8aJQWvpg2yD
php artisan cache:clear
php artisan config:clear
php artisan view:clear</pre>";

    echo "<p><strong>Opzione 2 - Via File Manager:</strong></p>";
    echo "<ol>";
    echo "<li>Scarica tutto il codice dal repository Git</li>";
    echo "<li>Carica i file via FTP sostituendo quelli vecchi</li>";
    echo "<li>Esegui: <code>php artisan cache:clear</code></li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #dfd; padding: 20px; border-left: 4px solid green; border-radius: 5px;'>";
    echo "<h3 style='color: green; margin-top: 0;'>✅ CODICE AGGIORNATO!</h3>";
    echo "<p>Il server ha tutto il codice più recente. Sei pronto per importare le pesate!</p>";
    echo "<p><strong>Ricorda:</strong></p>";
    echo "<ul>";
    echo "<li>Quando mappi le colonne, metti <strong>Codice Fiscale → \"Salta questa colonna\"</strong></li>";
    echo "<li>Verifica che Peso sia mappato alla colonna corretta</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>📂 File Modificati da Aggiornare</h2>";
echo "<ul>";
echo "<li><code>app/Models/Cliente.php</code> - Relazione pesate()</li>";
echo "<li><code>app/Models/Cliente.php</code> - Fix generaCodiceCliente()</li>";
echo "<li><code>app/Models/Utente.php</code> - Protezioni admin</li>";
echo "<li><code>app/Http/Controllers/Admin/PesateController.php</code> - Logging dettagliato</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ ELIMINA QUESTO FILE dopo aver aggiornato!</strong></p>";
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    echo "✅ File eliminato!";
    exit;
}
echo "<a href='?delete=1' style='color: red;'>Elimina questo file</a>";
