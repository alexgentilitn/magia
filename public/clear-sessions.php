<?php

echo "PULIZIA SESSIONI\n";
echo "================\n\n";

// Path sessioni Laravel
$sessionPath = __DIR__ . '/../storage/framework/sessions';

if (!is_dir($sessionPath)) {
    echo "❌ Directory sessioni non trovata: {$sessionPath}\n";
    exit;
}

echo "Directory sessioni: {$sessionPath}\n\n";

// Conta file sessione
$files = glob($sessionPath . '/*');
$count = count($files);

echo "Sessioni trovate: {$count}\n\n";

if ($count === 0) {
    echo "✅ Nessuna sessione da eliminare\n";
    exit;
}

// Elimina tutti i file di sessione
$deleted = 0;
foreach ($files as $file) {
    if (is_file($file)) {
        if (unlink($file)) {
            $deleted++;
        }
    }
}

echo "✅ Eliminate {$deleted} sessioni\n\n";

// Clear opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ FATTO!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Ora:\n";
echo "1. Pulisci i cookie del browser (o usa incognito)\n";
echo "2. Vai su /admin/login\n";
echo "3. Fai login normalmente\n\n";
echo "Il sito dovrebbe funzionare!\n";
