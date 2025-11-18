<?php
/**
 * Check Routes via Artisan
 * Usa il comando artisan route:list per vedere le rotte
 */

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Artisan Route Check</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #00ff00;
            text-shadow: 0 0 10px #00ff00;
        }
        pre {
            background: #000;
            padding: 20px;
            border: 2px solid #00ff00;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre;
            font-size: 12px;
        }
        .success {
            color: #00ff00;
        }
        .error {
            color: #ff0000;
        }
        .warning {
            color: #ffff00;
        }
        .highlight {
            background: #ffff00;
            color: #000;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Artisan Route List</h1>
";

$basePath = dirname(__DIR__);
$artisanPath = $basePath . '/artisan';

if (!file_exists($artisanPath)) {
    echo "<pre class='error'>❌ File artisan non trovato in: $artisanPath</pre>";
    exit;
}

echo "<pre class='success'>✅ Esecuzione comando: php artisan route:list --name=super-admin</pre>";

// Cambia nella directory base di Laravel
chdir($basePath);

// Esegui il comando artisan
$command = "php artisan route:list --name=super-admin 2>&1";
$output = shell_exec($command);

if ($output === null) {
    echo "<pre class='error'>❌ Errore: impossibile eseguire il comando artisan</pre>";
    echo "<pre class='warning'>Verifica che PHP CLI sia disponibile e che l'utente web abbia i permessi necessari.</pre>";
} else {
    echo "<pre>";

    // Evidenzia le righe che contengono super-admin
    $lines = explode("\n", $output);
    foreach ($lines as $line) {
        if (stripos($line, 'super-admin') !== false) {
            echo "<span class='highlight'>" . htmlspecialchars($line) . "</span>\n";
        } else {
            echo htmlspecialchars($line) . "\n";
        }
    }

    echo "</pre>";
}

// Conta quante rotte super-admin ci sono
if ($output !== null) {
    $count = substr_count(strtolower($output), 'super-admin');
    echo "<pre class='success'>📊 Trovate $count occorrenze di 'super-admin' nell'output</pre>";
}

// Esegui anche la lista completa (solo prime 50 righe)
echo "<hr style='border-color: #00ff00;'>";
echo "<h2>📋 Tutte le rotte (prime 50)</h2>";
echo "<pre class='success'>✅ Esecuzione comando: php artisan route:list | head -50</pre>";

$command = "php artisan route:list 2>&1 | head -50";
$output = shell_exec($command);

if ($output !== null) {
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
} else {
    echo "<pre class='error'>❌ Errore esecuzione comando</pre>";
}

echo "
    </div>
</body>
</html>
";
