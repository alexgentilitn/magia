<?php
/**
 * Migration: Creazione Tabella pagamenti_professionisti
 *
 * Funzione: Traccia i pagamenti effettivi ai professionisti
 * Da eseguire dal browser: http://tuodominio.com/database/setup/001_create_pagamenti_professionisti_table.php
 *
 * IMPORTANTE: Cancellare o spostare questo file dopo l'esecuzione per sicurezza
 */

// Carica configurazione Laravel
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Header HTML per output leggibile
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup Database - Pagamenti Professionisti</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #7b2869; border-bottom: 3px solid #7b2869; padding-bottom: 10px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border-left: 4px solid #7b2869; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .step-title { font-weight: bold; color: #7b2869; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🗄️ Setup Database - Pagamenti Professionisti</h1>
";

try {
    echo "<div class='step'>";
    echo "<div class='step-title'>📋 Step 1: Verifica Connessione Database</div>";

    // Test connessione
    DB::connection()->getPdo();
    echo "<div class='success'>✅ Connessione al database riuscita!</div>";
    echo "<div class='info'>Database: <strong>" . DB::connection()->getDatabaseName() . "</strong></div>";
    echo "</div>";

    echo "<div class='step'>";
    echo "<div class='step-title'>🔍 Step 2: Verifica Tabella Esistente</div>";

    if (Schema::hasTable('pagamenti_professionisti')) {
        echo "<div class='warning'>⚠️ La tabella 'pagamenti_professionisti' esiste già!</div>";
        echo "<div class='info'>Nessuna azione necessaria. La tabella è già presente nel database.</div>";

        // Mostra struttura tabella esistente
        echo "<h3>Struttura Tabella Esistente:</h3>";
        $columns = DB::select('DESCRIBE pagamenti_professionisti');
        echo "<pre>";
        foreach ($columns as $column) {
            echo sprintf("%-30s %-20s %s\n", $column->Field, $column->Type, $column->Null === 'YES' ? 'NULL' : 'NOT NULL');
        }
        echo "</pre>";

    } else {
        echo "<div class='info'>✨ La tabella non esiste. Procedo con la creazione...</div>";
        echo "</div>";

        echo "<div class='step'>";
        echo "<div class='step-title'>🛠️ Step 3: Creazione Tabella</div>";

        Schema::create('pagamenti_professionisti', function (Blueprint $table) {
            $table->id();

            // Relazioni
            $table->unsignedBigInteger('professionista_id')->comment('FK a professionisti.id');
            $table->unsignedBigInteger('utente_id')->comment('FK a utenti.id (professionista)');
            $table->unsignedBigInteger('pagato_da')->nullable()->comment('FK a utenti.id (admin che ha registrato)');

            // Periodo di riferimento
            $table->date('periodo_da')->comment('Inizio periodo compenso');
            $table->date('periodo_a')->comment('Fine periodo compenso');

            // Importi
            $table->decimal('importo_maturato', 10, 2)->default(0)->comment('Compenso lordo maturato');
            $table->decimal('importo_pagato', 10, 2)->default(0)->comment('Importo effettivamente pagato');
            $table->decimal('ritenuta_fiscale', 10, 2)->default(0)->comment('Ritenuta d\'acconto 20%');
            $table->decimal('importo_netto', 10, 2)->default(0)->comment('Netto da pagare (dopo ritenuta)');

            // Dettagli pagamento
            $table->enum('metodo_pagamento', ['bonifico', 'contante', 'assegno'])->default('bonifico');
            $table->dateTime('data_pagamento')->nullable();
            $table->string('numero_bonifico', 100)->nullable();
            $table->string('iban', 34)->nullable();

            // Stato e documenti
            $table->enum('stato', ['completato', 'in_attesa', 'annullato'])->default('completato');
            $table->string('ricevuta_path')->nullable()->comment('Path ricevuta pagamento');
            $table->text('note')->nullable();

            $table->timestamps();

            // Indici per performance
            $table->index('utente_id');
            $table->index('professionista_id');
            $table->index('data_pagamento');
            $table->index('stato');
            $table->index(['periodo_da', 'periodo_a']);
        });

        echo "<div class='success'>✅ Tabella 'pagamenti_professionisti' creata con successo!</div>";

        // Mostra struttura creata
        echo "<h3>Struttura Tabella Creata:</h3>";
        $columns = DB::select('DESCRIBE pagamenti_professionisti');
        echo "<pre>";
        foreach ($columns as $column) {
            echo sprintf("%-30s %-20s %s\n", $column->Field, $column->Type, $column->Null === 'YES' ? 'NULL' : 'NOT NULL');
        }
        echo "</pre>";
        echo "</div>";

        echo "<div class='step'>";
        echo "<div class='step-title'>🔗 Step 4: Creazione Foreign Keys (Opzionale)</div>";
        echo "<div class='info'>ℹ️ Le foreign key possono essere aggiunte successivamente se necessario.</div>";
        echo "<div class='warning'>⚠️ Per ora lasciamo solo gli indici per mantenere flessibilità.</div>";
        echo "</div>";
    }

    echo "<div class='step'>";
    echo "<div class='step-title'>📊 Riepilogo Finale</div>";

    // Conta record esistenti
    $count = DB::table('pagamenti_professionisti')->count();
    echo "<div class='info'>";
    echo "📈 <strong>Record presenti:</strong> $count<br>";
    echo "🗄️ <strong>Tabella:</strong> pagamenti_professionisti<br>";
    echo "✅ <strong>Stato:</strong> Operativa e pronta all'uso<br>";
    echo "</div>";
    echo "</div>";

    echo "<div class='success'>";
    echo "<h2>✅ SETUP COMPLETATO CON SUCCESSO!</h2>";
    echo "<p>La tabella <strong>pagamenti_professionisti</strong> è pronta per essere utilizzata.</p>";
    echo "<p><strong>IMPORTANTE:</strong> Per motivi di sicurezza, cancella o sposta questo file dopo l'esecuzione.</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ ERRORE DURANTE IL SETUP</h2>";
    echo "<p><strong>Messaggio:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Linea:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "
    <div class='info' style='margin-top: 30px;'>
        <h3>📝 Prossimi Passi:</h3>
        <ol>
            <li>Verifica che la tabella sia stata creata correttamente</li>
            <li>Esegui lo script successivo: <code>002_add_payment_fields_to_utenti_table.php</code></li>
            <li>Testa il sistema di pagamenti professionisti dall'area admin</li>
            <li><strong>CANCELLA questo file per sicurezza</strong></li>
        </ol>
    </div>
</div>
</body>
</html>";
