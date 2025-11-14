<?php
/**
 * Script: Force Recreate Professionisti Table
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "<pre>";
echo "========================================\n";
echo "FORCE RECREATE PROFESSIONISTI TABLE\n";
echo "========================================\n\n";

try {
    echo "1. Disabilitando foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    echo "   ✓ Foreign keys disabilitate\n\n";

    echo "2. Eliminando tabella 'professionisti' esistente...\n";
    Schema::dropIfExists('professionisti');
    echo "   ✓ Tabella 'professionisti' eliminata\n\n";

    echo "3. Riabilitando foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "   ✓ Foreign keys riabilitate\n\n";

    echo "4. Rimuovendo record migration dalla tabella 'migrations'...\n";
    DB::table('migrations')->where('migration', 'like', '%create_professionisti_table%')->delete();
    echo "   ✓ Record migration rimosso\n\n";

    echo "5. Eseguendo migration per tabella 'professionisti'...\n";
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_11_13_100000_create_professionisti_table.php',
        '--force' => true
    ]);
    echo Artisan::output();
    echo "   ✓ Migration completata\n\n";

    echo "6. Verifica finale...\n";
    if (Schema::hasTable('professionisti')) {
        $columns = Schema::getColumnListing('professionisti');
        echo "   ✓ Tabella 'professionisti' creata con successo!\n";
        echo "   ✓ Colonne presenti: " . count($columns) . "\n";
        echo "   Colonne: " . implode(', ', $columns) . "\n\n";
    } else {
        echo "   ✗ ERRORE: Tabella 'professionisti' non trovata!\n\n";
    }

    echo "========================================\n";
    echo "✅ PROCESSO COMPLETATO CON SUCCESSO!\n";
    echo "========================================\n\n";
    echo "Puoi ora accedere a Gestione Professionisti.\n";
    echo "URL: <a href='/progetti_ai/public/admin/professionisti'>Vai a Gestione Professionisti</a>\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERRORE:\n";
    echo $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";

    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    } catch (\Exception $e2) {}
}

echo "</pre>";
