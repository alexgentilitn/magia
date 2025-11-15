#!/usr/bin/env php
<?php

/**
 * Script semplice per eseguire le migrations
 *
 * Uso:
 *   php migrate.php
 *
 * Esegue le migrations senza chiedere conferma
 */

echo "\n🚀 Esecuzione migrations...\n\n";

if (!file_exists(__DIR__ . '/artisan')) {
    echo "❌ ERRORE: Esegui questo script dalla root del progetto Laravel.\n\n";
    exit(1);
}

// Esegui le migrations
$returnCode = null;
passthru('php artisan migrate --force 2>&1', $returnCode);

echo "\n";

if ($returnCode === 0) {
    echo "✅ Migrations completate con successo!\n\n";
} else {
    echo "❌ Errore durante l'esecuzione (codice: {$returnCode})\n\n";
    exit(1);
}
