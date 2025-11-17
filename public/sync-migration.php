<?php
/**
 * Script per sincronizzare migration già eseguita
 *
 * Uso: Visita questo file dal browser
 * URL: https://www.agstudio.digital/magia/public/sync-migration.php
 */

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Migrazione da sincronizzare
$migration = '2025_11_17_create_utente_permesso_table';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head><title>Sync Migration</title>";
echo "<style>body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; } .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; } .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; } .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; } pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }</style>";
echo "</head>";
echo "<body>";
echo "<h1>🔧 Sincronizzazione Migration</h1>";
echo "<p><strong>Migration:</strong> <code>{$migration}</code></p>";

try {
    // 1. Verifica che la tabella esista
    if (!Schema::hasTable('utente_permesso')) {
        echo "<div class='error'>❌ La tabella 'utente_permesso' non esiste nel database!</div>";
        echo "<p>La migration deve essere eseguita prima di sincronizzarla.</p>";
        exit;
    }

    echo "<div class='success'>✓ Tabella 'utente_permesso' trovata nel database</div>";

    // 2. Verifica che la migration non sia già registrata
    $exists = DB::table('migrations')
        ->where('migration', $migration)
        ->exists();

    if ($exists) {
        echo "<div class='info'>ℹ Migration già registrata nella tabella 'migrations'</div>";
        echo "<p>Non è necessaria nessuna azione. Lo stato è già sincronizzato!</p>";
        exit;
    }

    echo "<div class='info'>ℹ Migration non registrata. Procedo con la sincronizzazione...</div>";

    // 3. Ottieni l'ultimo batch
    $lastBatch = DB::table('migrations')->max('batch') ?? 0;
    $newBatch = $lastBatch + 1;

    // 4. Registra la migration
    DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => $newBatch
    ]);

    echo "<div class='success'>";
    echo "<h2>✅ Sincronizzazione completata con successo!</h2>";
    echo "<ul>";
    echo "<li><strong>Migration:</strong> {$migration}</li>";
    echo "<li><strong>Batch:</strong> {$newBatch}</li>";
    echo "<li><strong>Data:</strong> " . date('Y-m-d H:i:s') . "</li>";
    echo "</ul>";
    echo "</div>";

    echo "<p><strong>✓ La migration è ora sincronizzata!</strong></p>";
    echo "<p>Puoi verificare lo stato andando su <a href='/magia/public/admin/super-admin'>Super Admin → Esegui Migrations</a></p>";

    // 5. Verifica la sincronizzazione
    $check = DB::table('migrations')
        ->where('migration', $migration)
        ->first();

    if ($check) {
        echo "<div class='info'>";
        echo "<h3>📋 Verifica</h3>";
        echo "<pre>";
        print_r($check);
        echo "</pre>";
        echo "</div>";
    }

} catch (\Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Errore durante la sincronizzazione</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Tipo:</strong> " . get_class($e) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #999; font-size: 12px;'>Script di sincronizzazione migration - MA.GIA DONNA</p>";
echo "</body>";
echo "</html>";
