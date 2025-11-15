#!/usr/bin/env php
<?php

/**
 * Script per eseguire le migrations del database
 *
 * Uso:
 *   php run-migrations.php
 *
 * Questo script esegue automaticamente le migrations pendenti
 * sul database configurato in .env
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     MA.GIA DONNA - Esecuzione Migrations Database         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Verifica che siamo nella directory corretta
if (!file_exists(__DIR__ . '/artisan')) {
    echo "❌ ERRORE: File 'artisan' non trovato.\n";
    echo "   Assicurati di eseguire questo script dalla root del progetto Laravel.\n\n";
    exit(1);
}

// Verifica che il file .env esista
if (!file_exists(__DIR__ . '/.env')) {
    echo "⚠️  ATTENZIONE: File .env non trovato.\n";
    echo "   Copia .env.example in .env e configura il database prima di continuare.\n\n";
    exit(1);
}

echo "📋 Elenco migrations che verranno eseguite:\n";
echo "   1. add_privacy_fields_to_clienti_table (Consensi GDPR)\n";
echo "   2. add_custom_fields_to_clienti_table (Anagrafica estesa)\n";
echo "\n";

echo "🔍 Verifico lo stato delle migrations...\n\n";

// Esegui migrate:status per vedere lo stato
passthru('php artisan migrate:status 2>&1', $statusCode);

echo "\n";
echo "──────────────────────────────────────────────────────────────\n";
echo "\n";

// Chiedi conferma
echo "Vuoi procedere con l'esecuzione delle migrations? [s/N]: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 's' && strtolower($line) !== 'si') {
    echo "\n❌ Operazione annullata dall'utente.\n\n";
    exit(0);
}

echo "\n";
echo "🚀 Esecuzione migrations in corso...\n";
echo "──────────────────────────────────────────────────────────────\n";
echo "\n";

// Esegui le migrations
passthru('php artisan migrate --force 2>&1', $returnCode);

echo "\n";
echo "──────────────────────────────────────────────────────────────\n";

if ($returnCode === 0) {
    echo "\n";
    echo "✅ SUCCESSO! Migrations eseguite correttamente.\n";
    echo "\n";
    echo "📊 Modifiche applicate al database:\n";
    echo "   ✓ Aggiunti 10 campi per consensi GDPR\n";
    echo "   ✓ Aggiunti 25 campi per anagrafica estesa clienti\n";
    echo "\n";
    echo "🎯 Prossimi passi:\n";
    echo "   1. Accedi al pannello admin: /admin/clienti\n";
    echo "   2. Modifica una cliente per vedere i nuovi campi\n";
    echo "   3. Testa la registrazione con i nuovi consensi GDPR\n";
    echo "\n";
} else {
    echo "\n";
    echo "❌ ERRORE durante l'esecuzione delle migrations.\n";
    echo "   Codice di errore: {$returnCode}\n";
    echo "\n";
    echo "🔧 Possibili soluzioni:\n";
    echo "   1. Verifica la connessione al database in .env\n";
    echo "   2. Controlla che il database esista\n";
    echo "   3. Verifica i permessi dell'utente database\n";
    echo "   4. Controlla i log in storage/logs/laravel.log\n";
    echo "\n";
    exit(1);
}

echo "\n";
