<?php
/**
 * Migration: Aggiungi Campi Pagamento alla Tabella utenti
 *
 * Funzione: Aggiunge campi per gestire pagamenti clienti (PayPal + Bonifico)
 * Da eseguire dal browser: http://tuodominio.com/database/setup/002_add_payment_fields_to_utenti_table.php
 *
 * IMPORTANTE: Cancellare o spostare questo file dopo l'esecuzione per sicurezza
 */

// Carica configurazione Laravel
require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
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
    <title>Setup Database - Campi Pagamento Utenti</title>
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
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #7b2869; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
    </style>
</head>
<body>
<div class='container'>
    <h1>💳 Setup Database - Campi Pagamento Clienti</h1>
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
    echo "<div class='step-title'>🔍 Step 2: Verifica Tabella Utenti</div>";

    if (!Schema::hasTable('utenti')) {
        throw new Exception("La tabella 'utenti' non esiste! Impossibile procedere.");
    }

    echo "<div class='success'>✅ Tabella 'utenti' trovata!</div>";
    echo "</div>";

    echo "<div class='step'>";
    echo "<div class='step-title'>🔍 Step 3: Verifica Colonne Esistenti</div>";

    // Lista campi da aggiungere
    $campiDaAggiungere = [
        'metodo_pagamento' => 'ENUM',
        'stato_pagamento' => 'ENUM',
        'data_pagamento' => 'DATETIME',
        'importo_pagamento' => 'DECIMAL',
        'paypal_order_id' => 'VARCHAR',
        'paypal_payer_id' => 'VARCHAR',
        'ricevuta_bonifico_path' => 'VARCHAR',
        'data_bonifico' => 'DATE',
        'note_verifica_bonifico' => 'TEXT'
    ];

    $campiEsistenti = [];
    $campiMancanti = [];

    foreach ($campiDaAggiungere as $campo => $tipo) {
        if (Schema::hasColumn('utenti', $campo)) {
            $campiEsistenti[] = $campo;
        } else {
            $campiMancanti[] = $campo;
        }
    }

    if (!empty($campiEsistenti)) {
        echo "<div class='warning'>⚠️ Alcuni campi esistono già:</div>";
        echo "<table><tr><th>Campo</th><th>Stato</th></tr>";
        foreach ($campiEsistenti as $campo) {
            echo "<tr><td>$campo</td><td>✅ Già presente</td></tr>";
        }
        echo "</table>";
    }

    if (empty($campiMancanti)) {
        echo "<div class='info'>✨ Tutti i campi sono già presenti. Nessuna modifica necessaria.</div>";
    } else {
        echo "<div class='info'>📝 Campi da aggiungere: " . count($campiMancanti) . "</div>";
        echo "<table><tr><th>Campo</th><th>Tipo</th></tr>";
        foreach ($campiMancanti as $campo) {
            echo "<tr><td>$campo</td><td>{$campiDaAggiungere[$campo]}</td></tr>";
        }
        echo "</table>";
        echo "</div>";

        echo "<div class='step'>";
        echo "<div class='step-title'>🛠️ Step 4: Aggiunta Colonne</div>";

        Schema::table('utenti', function (Blueprint $table) use ($campiMancanti) {
            // Metodo pagamento
            if (in_array('metodo_pagamento', $campiMancanti)) {
                $table->enum('metodo_pagamento', ['paypal', 'bonifico'])
                    ->nullable()
                    ->after('email')
                    ->comment('Metodo pagamento registrazione');
                echo "<div class='success'>✅ Aggiunto: metodo_pagamento</div>";
            }

            // Stato pagamento
            if (in_array('stato_pagamento', $campiMancanti)) {
                $table->enum('stato_pagamento', ['in_attesa', 'completato', 'rifiutato'])
                    ->default('in_attesa')
                    ->after('metodo_pagamento')
                    ->comment('Stato verifica pagamento');
                echo "<div class='success'>✅ Aggiunto: stato_pagamento</div>";
            }

            // Data pagamento
            if (in_array('data_pagamento', $campiMancanti)) {
                $table->dateTime('data_pagamento')
                    ->nullable()
                    ->after('stato_pagamento')
                    ->comment('Data completamento/verifica pagamento');
                echo "<div class='success'>✅ Aggiunto: data_pagamento</div>";
            }

            // Importo pagamento
            if (in_array('importo_pagamento', $campiMancanti)) {
                $table->decimal('importo_pagamento', 10, 2)
                    ->default(0)
                    ->after('data_pagamento')
                    ->comment('Importo pagato per registrazione');
                echo "<div class='success'>✅ Aggiunto: importo_pagamento</div>";
            }

            // PayPal Order ID
            if (in_array('paypal_order_id', $campiMancanti)) {
                $table->string('paypal_order_id', 100)
                    ->nullable()
                    ->after('importo_pagamento')
                    ->comment('ID ordine PayPal');
                echo "<div class='success'>✅ Aggiunto: paypal_order_id</div>";
            }

            // PayPal Payer ID
            if (in_array('paypal_payer_id', $campiMancanti)) {
                $table->string('paypal_payer_id', 100)
                    ->nullable()
                    ->after('paypal_order_id')
                    ->comment('ID pagatore PayPal');
                echo "<div class='success'>✅ Aggiunto: paypal_payer_id</div>";
            }

            // Path ricevuta bonifico
            if (in_array('ricevuta_bonifico_path', $campiMancanti)) {
                $table->string('ricevuta_bonifico_path')
                    ->nullable()
                    ->after('paypal_payer_id')
                    ->comment('Path file ricevuta bonifico caricata');
                echo "<div class='success'>✅ Aggiunto: ricevuta_bonifico_path</div>";
            }

            // Data bonifico
            if (in_array('data_bonifico', $campiMancanti)) {
                $table->date('data_bonifico')
                    ->nullable()
                    ->after('ricevuta_bonifico_path')
                    ->comment('Data bonifico dichiarata dall\'utente');
                echo "<div class='success'>✅ Aggiunto: data_bonifico</div>";
            }

            // Note verifica
            if (in_array('note_verifica_bonifico', $campiMancanti)) {
                $table->text('note_verifica_bonifico')
                    ->nullable()
                    ->after('data_bonifico')
                    ->comment('Note admin su verifica bonifico');
                echo "<div class='success'>✅ Aggiunto: note_verifica_bonifico</div>";
            }
        });

        echo "</div>";

        echo "<div class='step'>";
        echo "<div class='step-title'>📊 Step 5: Aggiunta Indici per Performance</div>";

        // Aggiungi indici se i campi sono stati creati
        try {
            if (in_array('stato_pagamento', $campiMancanti)) {
                DB::statement('CREATE INDEX idx_utenti_stato_pagamento ON utenti(stato_pagamento)');
                echo "<div class='success'>✅ Indice creato: idx_utenti_stato_pagamento</div>";
            }

            if (in_array('data_pagamento', $campiMancanti)) {
                DB::statement('CREATE INDEX idx_utenti_data_pagamento ON utenti(data_pagamento)');
                echo "<div class='success'>✅ Indice creato: idx_utenti_data_pagamento</div>";
            }

            if (in_array('paypal_order_id', $campiMancanti)) {
                DB::statement('CREATE INDEX idx_utenti_paypal_order ON utenti(paypal_order_id)');
                echo "<div class='success'>✅ Indice creato: idx_utenti_paypal_order</div>";
            }
        } catch (Exception $e) {
            echo "<div class='warning'>⚠️ Alcuni indici potrebbero esistere già: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        echo "</div>";
    }

    echo "</div>";

    echo "<div class='step'>";
    echo "<div class='step-title'>📋 Step 6: Verifica Finale - Struttura Completa</div>";

    $columns = DB::select("SELECT
        COLUMN_NAME,
        COLUMN_TYPE,
        IS_NULLABLE,
        COLUMN_DEFAULT,
        COLUMN_COMMENT
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'utenti'
    AND COLUMN_NAME IN (
        'metodo_pagamento',
        'stato_pagamento',
        'data_pagamento',
        'importo_pagamento',
        'paypal_order_id',
        'paypal_payer_id',
        'ricevuta_bonifico_path',
        'data_bonifico',
        'note_verifica_bonifico'
    )");

    echo "<h3>Campi Pagamento nella Tabella Utenti:</h3>";
    echo "<table>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nullable</th><th>Default</th><th>Descrizione</th></tr>";
    foreach ($columns as $col) {
        $default = $col->COLUMN_DEFAULT ?? 'NULL';
        $comment = $col->COLUMN_COMMENT ?? '-';
        echo "<tr>";
        echo "<td><strong>{$col->COLUMN_NAME}</strong></td>";
        echo "<td>{$col->COLUMN_TYPE}</td>";
        echo "<td>{$col->IS_NULLABLE}</td>";
        echo "<td><code>$default</code></td>";
        echo "<td><em>$comment</em></td>";
        echo "</tr>";
    }
    echo "</table>";

    // Conta utenti con pagamenti
    $utentiConPagamento = DB::table('utenti')
        ->whereNotNull('metodo_pagamento')
        ->count();

    echo "<div class='info'>";
    echo "📊 <strong>Statistiche:</strong><br>";
    echo "- Totale utenti: " . DB::table('utenti')->count() . "<br>";
    echo "- Utenti con pagamento: $utentiConPagamento<br>";
    echo "</div>";
    echo "</div>";

    echo "<div class='success'>";
    echo "<h2>✅ SETUP COMPLETATO CON SUCCESSO!</h2>";
    echo "<p>La tabella <strong>utenti</strong> è stata aggiornata con i campi per la gestione pagamenti.</p>";
    echo "<p><strong>Funzionalità abilitate:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Pagamenti PayPal per registrazione clienti</li>";
    echo "<li>✅ Caricamento ricevuta bonifico bancario</li>";
    echo "<li>✅ Verifica admin bonifici in attesa</li>";
    echo "<li>✅ Tracciamento storico pagamenti</li>";
    echo "</ul>";
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
            <li>Verifica che tutti i campi siano stati aggiunti correttamente</li>
            <li>Testa il sistema di registrazione con PayPal (modalità sandbox)</li>
            <li>Testa il caricamento bonifico e la verifica admin</li>
            <li>Configura le credenziali PayPal in .env:
                <pre>PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret</pre>
            </li>
            <li><strong>CANCELLA questo file per sicurezza</strong></li>
        </ol>
    </div>
</div>
</body>
</html>";
