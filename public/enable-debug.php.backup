<?php

/**
 * ATTENZIONE: Questo script abilita il debug mode.
 * Visitalo SOLO per diagnostica, poi disabilitalo subito!
 */

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("File .env non trovato!");
}

$envContent = file_get_contents($envPath);

// Abilita debug
$envContent = preg_replace('/APP_DEBUG=false/', 'APP_DEBUG=true', $envContent);

file_put_contents($envPath, $envContent);

// Clear cache
if (function_exists('opcache_reset')) {
    opcache_reset();
}

echo "✅ DEBUG ABILITATO!\n\n";
echo "Ora visita la pagina che dava errore 500:\n";
echo "https://www.agstudio.digital/magia/public/admin/login\n\n";
echo "⚠️  IMPORTANTE: Quando hai visto l'errore, DISABILITA il debug visitando:\n";
echo "https://www.agstudio.digital/magia/public/disable-debug.php\n";
