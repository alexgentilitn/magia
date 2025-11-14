<?php
/**
 * Script: Force Recreate Sedi Table
 * Ricrea forzatamente la tabella sedi con la struttura corretta
 */

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "<pre>";
echo "========================================\n";
echo "FORCE RECREATE SEDI TABLE\n";
echo "========================================\n\n";

try {
    // Step 1: Disabilita controlli foreign key
    echo "1. Disabilitando foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    echo "   ✓ Foreign keys disabilitate\n\n";

    // Step 2: Droppa la tabella se esiste
    echo "2. Eliminando tabella 'sedi' esistente...\n";
    Schema::dropIfExists('sedi');
    echo "   ✓ Tabella 'sedi' eliminata\n\n";

    // Step 3: Riabilita controlli foreign key
    echo "3. Riabilitando foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "   ✓ Foreign keys riabilitate\n\n";

    // Step 4: Rimuovi record dalla tabella migrations
    echo "4. Rimuovendo record migration dalla tabella 'migrations'...\n";
    DB::table('migrations')->where('migration', 'like', '%create_sedi_table%')->delete();
    echo "   ✓ Record migration rimosso\n\n";

    // Step 5: Esegui la migration specifica
    echo "5. Eseguendo migration per tabella 'sedi'...\n";
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_11_13_085812_create_sedi_table.php',
        '--force' => true
    ]);
    echo Artisan::output();
    echo "   ✓ Migration completata\n\n";

    // Step 6: Verifica tabella creata
    echo "6. Verifica finale...\n";
    if (Schema::hasTable('sedi')) {
        $columns = Schema::getColumnListing('sedi');
        echo "   ✓ Tabella 'sedi' creata con successo!\n";
        echo "   ✓ Colonne presenti: " . count($columns) . "\n";
        echo "   Colonne: " . implode(', ', $columns) . "\n\n";
    } else {
        echo "   ✗ ERRORE: Tabella 'sedi' non trovata!\n\n";
    }

    echo "========================================\n";
    echo "✅ PROCESSO COMPLETATO CON SUCCESSO!\n";
    echo "========================================\n\n";
    echo "Puoi ora tornare alla pagina delle sedi e creare una nuova sede.\n";
    echo "URL: <a href='/progetti_ai/public/admin/sedi'>Vai a Gestione Sedi</a>\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE:\n";
    echo $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";

    // Riabilita foreign keys in caso di errore
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    } catch (\Exception $e2) {
        // Ignora errori nel ripristino
    }
}

echo "</pre>";
