<?php
/**
 * VERIFICA COLONNE IMPEDENZIOMETRIA
 *
 * Controlla quali colonne della migration add_campi_impedenziometria_to_clienti_table
 * esistono già nella tabella clienti
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized - Secret key richiesta');
}

header('Content-Type: text/plain; charset=utf-8');

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔍 VERIFICA COLONNE IMPEDENZIOMETRIA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n\n";

    // Lista di tutte le colonne che la migration dovrebbe aggiungere
    $colonneImpedenziometria = [
        'massa_grassa_percentuale',
        'massa_muscolare_kg',
        'massa_ossea_kg',
        'grasso_viscerale',
        'proteine_percentuale',
        'acqua_totale_percentuale',
        'acqua_extra_percentuale',
        'acqua_intra_percentuale',
    ];

    $colonneObiettivi = [
        'obiettivi_primari',
        'obiettivi_secondari',
        'peso_obiettivo',
        'data_obiettivo',
    ];

    $colonneStileVita = [
        'ore_sonno',
        'qualita_sonno',
        'fumatore',
        'consumo_acqua_litri',
    ];

    $colonneMediche = [
        'patologie_croniche',
        'interventi_chirurgici',
        'gravidanza_in_corso',
        'ultima_gravidanza',
    ];

    $colonneMestruazioni = [
        'ciclo_regolare',
        'durata_ciclo_giorni',
        'ultima_mestruazione',
    ];

    $colonneTracking = [
        'ultima_impedenziometria',
        'storico_peso',
        'storico_misure',
    ];

    $gruppi = [
        'Impedenziometria' => $colonneImpedenziometria,
        'Obiettivi' => $colonneObiettivi,
        'Stile di Vita' => $colonneStileVita,
        'Dati Medici' => $colonneMediche,
        'Mestruazioni' => $colonneMestruazioni,
        'Tracking' => $colonneTracking,
    ];

    $esistenti = 0;
    $mancanti = 0;
    $tutteEsistono = true;

    foreach ($gruppi as $nomeGruppo => $colonne) {
        echo "━━━ {$nomeGruppo} ━━━\n\n";

        foreach ($colonne as $colonna) {
            $esiste = Schema::hasColumn('clienti', $colonna);

            if ($esiste) {
                echo "✅ '{$colonna}' - ESISTE\n";
                $esistenti++;
            } else {
                echo "❌ '{$colonna}' - MANCANTE\n";
                $mancanti++;
                $tutteEsistono = false;
            }
        }

        echo "\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 RIEPILOGO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "✅ Colonne esistenti: {$esistenti}\n";
    echo "❌ Colonne mancanti: {$mancanti}\n";
    echo "📝 Totale colonne: " . ($esistenti + $mancanti) . "\n\n";

    // Verifica se la migration è registrata
    $migrationRegistrata = DB::table('migrations')
        ->where('migration', '2025_11_16_000650_add_campi_impedenziometria_to_clienti_table')
        ->exists();

    echo "Migration registrata: " . ($migrationRegistrata ? "✅ SI" : "❌ NO") . "\n\n";

    if ($tutteEsistono && !$migrationRegistrata) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "💡 RACCOMANDAZIONE\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "Tutte le colonne esistono già ma la migration non è registrata.\n";
        echo "Soluzione: registrare manualmente la migration nel database.\n\n";
    } elseif ($esistenti > 0 && $mancanti > 0) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "⚠️  ATTENZIONE\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "Alcune colonne esistono già, altre sono mancanti.\n";
        echo "La migration dovrebbe eseguirsi correttamente grazie ai controlli hasColumn().\n";
        echo "Se fallisce, c'è un problema diverso da investigare.\n\n";
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n❌ ERRORE CRITICO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    exit(1);
}
