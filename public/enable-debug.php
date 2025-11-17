<?php
/**
 * Abilita debug mode temporaneamente
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

// Verifica se APP_DEBUG è già true
if (preg_match('/^APP_DEBUG=true$/m', $envContent)) {
    echo '<h1>Debug già attivo!</h1>';
    echo '<p>APP_DEBUG è già impostato a true nel file .env</p>';
    echo '<p><a href="../">← Torna al sito</a></p>';
    exit;
}

// Sostituisci APP_DEBUG=false con APP_DEBUG=true
$envContent = preg_replace('/^APP_DEBUG=false$/m', 'APP_DEBUG=true', $envContent);

// Salva il file
if (file_put_contents($envFile, $envContent)) {
    echo '<h1>✅ Debug Mode Abilitato!</h1>';
    echo '<p>Il file .env è stato aggiornato.</p>';
    echo '<p><strong>APP_DEBUG=true</strong></p>';
    
    // Cancella cache config se esiste
    $configCache = __DIR__ . '/../bootstrap/cache/config.php';
    if (file_exists($configCache)) {
        unlink($configCache);
        echo '<p>✅ Cache config eliminata</p>';
    }
    
    echo '<hr>';
    echo '<p><strong>Ora riprova a caricare la foto.</strong></p>';
    echo '<p>Vedrai l\'errore dettagliato invece del generico "500".</p>';
    echo '<hr>';
    echo '<p style="color: red;"><strong>⚠️ RICORDA:</strong> Quando hai finito, DISABILITA il debug mode per sicurezza!</p>';
    echo '<p><a href="disable-debug.php?token=magia2025">🔒 Disabilita Debug Mode</a></p>';
} else {
    echo '<h1>❌ Errore!</h1>';
    echo '<p>Impossibile scrivere il file .env</p>';
    echo '<p>Verifica i permessi del file.</p>';
}
