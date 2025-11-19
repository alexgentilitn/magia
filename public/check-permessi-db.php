<?php
// Script rapido per verificare e creare tabella utente_permesso
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain; charset=utf-8');

echo "=== VERIFICA TABELLA UTENTE_PERMESSO ===\n\n";

// Verifica esistenza tabella
$exists = Schema::hasTable('utente_permesso');

if ($exists) {
    echo "✅ Tabella 'utente_permesso' ESISTE\n";
    echo "Colonne: " . implode(', ', Schema::getColumnListing('utente_permesso')) . "\n";
    $count = DB::table('utente_permesso')->count();
    echo "Record: $count\n";
} else {
    echo "❌ Tabella 'utente_permesso' NON ESISTE\n\n";
    echo "CREAZIONE TABELLA...\n";

    try {
        Schema::create('utente_permesso', function ($table) {
            $table->id();
            $table->foreignId('utente_id')->constrained('utenti')->onDelete('cascade');
            $table->foreignId('permesso_id')->constrained('permessi')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['utente_id', 'permesso_id']);
        });

        DB::table('migrations')->insert([
            'migration' => '2025_11_17_create_utente_permesso_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);

        echo "✅ TABELLA CREATA CON SUCCESSO!\n";
        echo "\nOra puoi accedere a:\n";
        echo "https://www.agstudio.digital/magia/public/admin/professionisti/4/permessi\n";
    } catch (Exception $e) {
        echo "❌ ERRORE: " . $e->getMessage() . "\n";
    }
}

echo "\n=== FINE ===\n";
