<?php
/**
 * Test Setup Pagamenti Professionisti
 */

// Abilita error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Setup</title></head><body>";
echo "<h1>Test Setup Pagamenti Professionisti</h1>";

try {
    echo "<p>1. Caricamento autoload...</p>";
    require __DIR__.'/../vendor/autoload.php';
    echo "<p style='color: green;'>✓ Autoload OK</p>";

    echo "<p>2. Caricamento app...</p>";
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "<p style='color: green;'>✓ App OK</p>";

    echo "<p>3. Bootstrap kernel...</p>";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "<p style='color: green;'>✓ Kernel OK</p>";

    echo "<p>4. Test connessione database...</p>";
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    echo "<p style='color: green;'>✓ Connesso a: <strong>$dbName</strong></p>";

    echo "<p>5. Verifica tabella pagamenti_professionisti...</p>";
    if (\Illuminate\Support\Facades\Schema::hasTable('pagamenti_professionisti')) {
        $count = \Illuminate\Support\Facades\DB::table('pagamenti_professionisti')->count();
        echo "<p style='color: orange;'>⚠ Tabella già esistente con $count record</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Tabella NON esiste. Procedo alla creazione...</p>";

        use Illuminate\Database\Schema\Blueprint;

        \Illuminate\Support\Facades\Schema::create('pagamenti_professionisti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professionista_id')->comment('FK a professionisti.id');
            $table->unsignedBigInteger('utente_id')->comment('FK a utenti.id (professionista)');
            $table->unsignedBigInteger('pagato_da')->nullable()->comment('FK a utenti.id (admin che ha registrato)');
            $table->date('periodo_da')->comment('Inizio periodo compenso');
            $table->date('periodo_a')->comment('Fine periodo compenso');
            $table->decimal('importo_maturato', 10, 2)->default(0)->comment('Compenso lordo maturato');
            $table->decimal('importo_pagato', 10, 2)->default(0)->comment('Importo effettivamente pagato');
            $table->decimal('ritenuta_fiscale', 10, 2)->default(0)->comment('Ritenuta d\'acconto 20%');
            $table->decimal('importo_netto', 10, 2)->default(0)->comment('Netto da pagare (dopo ritenuta)');
            $table->enum('metodo_pagamento', ['bonifico', 'contante', 'assegno'])->default('bonifico');
            $table->dateTime('data_pagamento')->nullable();
            $table->string('numero_bonifico', 100)->nullable();
            $table->string('iban', 34)->nullable();
            $table->enum('stato', ['completato', 'in_attesa', 'annullato'])->default('completato');
            $table->string('ricevuta_path')->nullable()->comment('Path ricevuta pagamento');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('utente_id');
            $table->index('professionista_id');
            $table->index('data_pagamento');
            $table->index('stato');
            $table->index(['periodo_da', 'periodo_a']);
        });

        echo "<p style='color: green;'>✅ Tabella creata con successo!</p>";
    }

    echo "<h2 style='color: green;'>✅ TUTTO OK!</h2>";
    echo "<p><strong>IMPORTANTE:</strong> Elimina questo file dopo l'uso: <code>public/test-setup.php</code></p>";

} catch (\Exception $e) {
    echo "<h2 style='color: red;'>❌ ERRORE</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
