<?php

/**
 * Questo script disabilita il debug mode
 */

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("File .env non trovato!");
}

$envContent = file_get_contents($envPath);

// Disabilita debug
$envContent = preg_replace('/APP_DEBUG=true/', 'APP_DEBUG=false', $envContent);

file_put_contents($envPath, $envContent);

// Clear cache
if (function_exists('opcache_reset')) {
    opcache_reset();
}

echo "✅ DEBUG DISABILITATO!\n\n";
echo "Il sito è tornato in modalità produzione.\n";
