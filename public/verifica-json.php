<?php

echo "VERIFICA FILE JSON\n";
echo "==================\n\n";

$jsonPath = __DIR__ . '/../database/jsondb';

echo "Path: {$jsonPath}\n\n";

// Verifica se la directory esiste
if (!is_dir($jsonPath)) {
    echo "❌ Directory database/jsondb NON ESISTE!\n";
    echo "Questo è il problema!\n\n";

    // Prova a crearla
    if (mkdir($jsonPath, 0755, true)) {
        echo "✅ Directory creata!\n";
    } else {
        echo "❌ Impossibile creare la directory\n";
    }
    exit;
}

echo "✅ Directory esiste\n\n";

// Verifica permessi directory
$perms = substr(sprintf('%o', fileperms($jsonPath)), -4);
echo "Permessi directory: {$perms}\n";
if ($perms < '0755') {
    echo "⚠️  Permessi insufficienti! Dovrebbe essere 0755\n\n";
} else {
    echo "✅ Permessi OK\n\n";
}

// Lista file JSON
$files = [
    'utenti.json',
    'clienti.json',
    'ruoli.json',
    'sedi.json',
    'professionisti.json',
    'programmi.json',
    'lezioni.json',
    'pagamenti.json'
];

echo "File JSON:\n";
foreach ($files as $file) {
    $filepath = $jsonPath . '/' . $file;

    if (file_exists($filepath)) {
        $size = filesize($filepath);
        $perms = substr(sprintf('%o', fileperms($filepath)), -4);
        $readable = is_readable($filepath) ? '✅' : '❌';
        $writable = is_writable($filepath) ? '✅' : '❌';

        echo "  {$readable} {$file} ({$size} bytes, perms: {$perms}, read: {$readable}, write: {$writable})\n";
    } else {
        echo "  ❌ {$file} NON ESISTE!\n";
    }
}

echo "\n";

// Prova a leggere un file
echo "Test lettura utenti.json:\n";
$utenteFile = $jsonPath . '/utenti.json';
if (file_exists($utenteFile)) {
    $content = file_get_contents($utenteFile);
    $data = json_decode($content, true);

    if ($data !== null) {
        echo "  ✅ File leggibile, " . count($data) . " record trovati\n";
    } else {
        echo "  ❌ Errore JSON: " . json_last_error_msg() . "\n";
    }
} else {
    echo "  ❌ File non esiste\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Conta problemi
$problemi = [];
foreach ($files as $file) {
    if (!file_exists($jsonPath . '/' . $file)) {
        $problemi[] = "{$file} mancante";
    }
}

if (empty($problemi)) {
    echo "✅ Tutti i file JSON esistono e sono leggibili!\n";
    echo "Il problema dell'errore 500 è altrove.\n";
} else {
    echo "❌ PROBLEMI TROVATI:\n";
    foreach ($problemi as $p) {
        echo "  - {$p}\n";
    }
    echo "\nQuesto è probabilmente il problema!\n";
}
