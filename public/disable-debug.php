<?php
/**
 * Disabilita debug mode
 */

define('DEBUG_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== DEBUG_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . DEBUG_TOKEN . '</p>');
}

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    die('File .env non trovato!');
}

// Leggi il file .env
$envContent = file_get_contents($envFile);

// Sostituisci APP_DEBUG=true con APP_DEBUG=false
$envContent = preg_replace('/^APP_DEBUG=true$/m', 'APP_DEBUG=false', $envContent);

// Salva il file
if (file_put_contents($envFile, $envContent)) {
    echo '<h1>🔒 Debug Mode Disabilitato!</h1>';
    echo '<p>Il file .env è stato aggiornato.</p>';
    echo '<p><strong>APP_DEBUG=false</strong></p>';
    
    // Cancella cache config se esiste
    $configCache = __DIR__ . '/../bootstrap/cache/config.php';
    if (file_exists($configCache)) {
        unlink($configCache);
        echo '<p>✅ Cache config eliminata</p>';
    }
    
    echo '<hr>';
    echo '<p>✅ Il sito è di nuovo in modalità produzione sicura.</p>';
    echo '<p><a href="../">← Torna al sito</a></p>';
} else {
    echo '<h1>❌ Errore!</h1>';
    echo '<p>Impossibile scrivere il file .env</p>';
}
