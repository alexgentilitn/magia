<?php
/**
 * ANALISI TABELLA cliente_lezione
 * Verifica struttura e dati esistenti
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 ANALISI TABELLA cliente_lezione\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Verifica esistenza tabella
    $exists = DB::select("SHOW TABLES LIKE 'cliente_lezione'");

    if (count($exists) === 0) {
        echo "❌ Tabella cliente_lezione NON ESISTE!\n";
        echo "⚠️  Deve essere creata prima di implementare le prenotazioni.\n";
        exit;
    }

    echo "✅ Tabella cliente_lezione ESISTE\n\n";

    // Struttura tabella
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 STRUTTURA TABELLA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $columns = DB::select("DESCRIBE cliente_lezione");

    echo "Colonne presenti:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field}";
        echo " ({$col->Type})";
        if ($col->Null === 'NO') echo " NOT NULL";
        if ($col->Key === 'PRI') echo " PRIMARY KEY";
        if ($col->Key === 'MUL') echo " FOREIGN KEY";
        if ($col->Default !== null) echo " DEFAULT {$col->Default}";
        echo "\n";
    }

    echo "\n";

    // Conta record esistenti
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📈 DATI ESISTENTI\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $count = DB::select("SELECT COUNT(*) as total FROM cliente_lezione")[0]->total;
    echo "Prenotazioni totali: {$count}\n\n";

    if ($count > 0) {
        // Mostra alcune prenotazioni esempio
        $samples = DB::select("
            SELECT cl.*,
                   c.nome as cliente_nome,
                   c.cognome as cliente_cognome,
                   l.titolo as lezione_titolo,
                   l.data as lezione_data
            FROM cliente_lezione cl
            LEFT JOIN clienti c ON cl.cliente_id = c.id
            LEFT JOIN lezioni l ON cl.lezione_id = l.id
            LIMIT 5
        ");

        echo "Prime 5 prenotazioni:\n";
        foreach ($samples as $s) {
            $cliente = $s->cliente_nome . ' ' . $s->cliente_cognome;
            $lezione = $s->lezione_titolo . ' (' . $s->lezione_data . ')';
            echo "  - Cliente: {$cliente}\n";
            echo "    Lezione: {$lezione}\n";
            if (isset($s->created_at)) {
                echo "    Prenotato il: {$s->created_at}\n";
            }
            echo "\n";
        }
    } else {
        echo "⚠️  Nessuna prenotazione presente\n";
        echo "✅ Questo è OK se il sistema è nuovo\n\n";
    }

    // Verifica chiavi esterne
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔗 CHIAVI ESTERNE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $foreignKeys = DB::select("
        SELECT
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'cliente_lezione'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    if (count($foreignKeys) > 0) {
        echo "Relazioni configurate:\n";
        foreach ($foreignKeys as $fk) {
            echo "  - {$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
    } else {
        echo "⚠️  Nessuna foreign key configurata\n";
        echo "   (Le relazioni potrebbero essere gestite solo a livello applicativo)\n";
    }

    echo "\n";

    // Analisi per implementazione
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "💡 ANALISI PER IMPLEMENTAZIONE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $hasClienteId = false;
    $hasLezioneId = false;
    $hasStatoPartecipazione = false;
    $hasTimestamps = false;

    foreach ($columns as $col) {
        if ($col->Field === 'cliente_id') $hasClienteId = true;
        if ($col->Field === 'lezione_id') $hasLezioneId = true;
        if ($col->Field === 'stato_partecipazione') $hasStatoPartecipazione = true;
        if ($col->Field === 'created_at' || $col->Field === 'updated_at') $hasTimestamps = true;
    }

    echo "Campi necessari per prenotazioni:\n";
    echo "  - cliente_id: " . ($hasClienteId ? "✅ Presente" : "❌ Mancante") . "\n";
    echo "  - lezione_id: " . ($hasLezioneId ? "✅ Presente" : "❌ Mancante") . "\n";
    echo "  - timestamps: " . ($hasTimestamps ? "✅ Presente" : "⚠️  Opzionale") . "\n";
    echo "  - stato_partecipazione: " . ($hasStatoPartecipazione ? "✅ Presente (per check-in futuro)" : "⚠️  Mancante (aggiungeremo in Fase 4)") . "\n";

    echo "\n";

    if ($hasClienteId && $hasLezioneId) {
        echo "✅ TABELLA PRONTA PER IMPLEMENTAZIONE PRENOTAZIONI!\n";
        echo "\nProssimi step:\n";
        echo "  1. Implementare metodo prenota() in CalendarioController\n";
        echo "  2. Implementare metodo annullaPrenotazione()\n";
        echo "  3. Aggiungere route POST /calendario/{id}/prenota\n";
        echo "  4. Aggiungere route DELETE /calendario/{id}/prenotazioni/{cliente_id}\n";
        echo "  5. Modificare modal-dettagli per mostrare lista partecipanti\n";
    } else {
        echo "❌ TABELLA NON PRONTA\n";
        echo "⚠️  Mancano campi essenziali. Creare migration prima di procedere.\n";
    }

} catch (\Exception $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Analisi completata\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
