<?php
/**
 * TEST PDF CALENDARIO
 * Script di test progressivo per identificare problemi esportazione PDF
 */

define('SECRET', '$Magia2025!');

if (!isset($_GET['secret']) || $_GET['secret'] !== SECRET) {
    http_response_code(401);
    die('❌ Unauthorized');
}

header('Content-Type: text/plain; charset=utf-8');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 TEST ESPORTAZIONE PDF CALENDARIO\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Lezione;
use Carbon\Carbon;

$testLevel = $_GET['level'] ?? '1';

try {
    echo "📅 Data/Ora: " . date('d/m/Y H:i:s') . "\n";
    echo "🔬 Livello test: {$testLevel}\n\n";

    // ============================================
    // TEST 1: Verifica alias PDF
    // ============================================
    if ($testLevel >= 1) {
        echo "━━━ TEST 1: VERIFICA ALIAS PDF ━━━\n\n";

        if (!class_exists('PDF')) {
            echo "❌ Alias PDF non trovato!\n";
            echo "   Verifica config/app.php\n\n";
            exit(1);
        }

        echo "✅ Alias PDF trovato\n";
        echo "   Classe: " . get_class(app('dompdf.wrapper')) . "\n\n";
    }

    // ============================================
    // TEST 2: PDF Vuoto
    // ============================================
    if ($testLevel >= 2) {
        echo "━━━ TEST 2: GENERAZIONE PDF VUOTO ━━━\n\n";

        try {
            $pdf = \PDF::loadHTML('<h1>Test PDF</h1>');
            echo "✅ PDF vuoto generato correttamente\n\n";
        } catch (Exception $e) {
            echo "❌ ERRORE generazione PDF vuoto:\n";
            echo "   " . $e->getMessage() . "\n\n";
            exit(1);
        }
    }

    // ============================================
    // TEST 3: PDF con HTML complesso
    // ============================================
    if ($testLevel >= 3) {
        echo "━━━ TEST 3: PDF CON HTML COMPLESSO ━━━\n\n";

        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #9c27b0; }
        .box { padding: 10px; background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Test Calendario</h1>
    <div class="box">
        <p>Questo è un test con HTML complesso</p>
        <p>Data: ' . now()->format('d/m/Y H:i') . '</p>
    </div>
</body>
</html>';

        try {
            $pdf = \PDF::loadHTML($html);
            echo "✅ PDF con HTML complesso generato\n\n";
        } catch (Exception $e) {
            echo "❌ ERRORE HTML complesso:\n";
            echo "   " . $e->getMessage() . "\n\n";
            exit(1);
        }
    }

    // ============================================
    // TEST 4: Query Database Lezioni
    // ============================================
    if ($testLevel >= 4) {
        echo "━━━ TEST 4: QUERY DATABASE LEZIONI ━━━\n\n";

        $mese = $_GET['mese'] ?? now()->month;
        $anno = $_GET['anno'] ?? now()->year;

        $dataInizio = Carbon::create($anno, $mese, 1)->startOfMonth();
        $dataFine = Carbon::create($anno, $mese, 1)->endOfMonth();

        echo "Periodo: {$dataInizio->format('d/m/Y')} - {$dataFine->format('d/m/Y')}\n\n";

        try {
            $lezioni = Lezione::with(['professionista', 'sede', 'programma', 'clienti'])
                ->where('visibile_calendario', true)
                ->whereBetween('data', [$dataInizio, $dataFine])
                ->orderBy('data')
                ->orderBy('ora_inizio')
                ->get();

            echo "✅ Query eseguita con successo\n";
            echo "   Lezioni trovate: {$lezioni->count()}\n\n";

            if ($lezioni->count() > 0) {
                echo "Dettagli prima lezione:\n";
                $prima = $lezioni->first();
                echo "  - ID: {$prima->id}\n";
                echo "  - Titolo: {$prima->titolo}\n";
                echo "  - Data: {$prima->data->format('d/m/Y')}\n";
                echo "  - Ora inizio: {$prima->ora_inizio}\n";
                echo "  - Ora fine: {$prima->ora_fine}\n";
                echo "  - Clienti: {$prima->clienti->count()}\n\n";
            }
        } catch (Exception $e) {
            echo "❌ ERRORE query database:\n";
            echo "   " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . "\n";
            echo "   Linea: " . $e->getLine() . "\n\n";
            exit(1);
        }
    }

    // ============================================
    // TEST 5: Generazione vista PDF
    // ============================================
    if ($testLevel >= 5) {
        echo "━━━ TEST 5: GENERAZIONE VISTA PDF ━━━\n\n";

        $mese = $_GET['mese'] ?? now()->month;
        $anno = $_GET['anno'] ?? now()->year;

        $dataInizio = Carbon::create($anno, $mese, 1)->startOfMonth();
        $dataFine = Carbon::create($anno, $mese, 1)->endOfMonth();

        $lezioni = Lezione::with(['professionista', 'sede', 'programma', 'clienti'])
            ->where('visibile_calendario', true)
            ->whereBetween('data', [$dataInizio, $dataFine])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        $lezioniPerGiorno = $lezioni->groupBy(function($lezione) {
            return $lezione->data->format('Y-m-d');
        });

        $statistiche = [
            'totale_lezioni' => $lezioni->count(),
            'totale_partecipanti' => $lezioni->sum(function($lezione) {
                return $lezione->clienti->count();
            }),
            'posti_disponibili' => $lezioni->sum('posti_totali'),
            'posti_occupati' => $lezioni->sum('posti_occupati'),
        ];

        $nomeMese = $dataInizio->locale('it')->isoFormat('MMMM YYYY');

        echo "Preparazione dati per vista:\n";
        echo "  - Nome mese: {$nomeMese}\n";
        echo "  - Totale lezioni: {$statistiche['totale_lezioni']}\n";
        echo "  - Giorni con lezioni: {$lezioniPerGiorno->count()}\n\n";

        try {
            $html = view('admin.calendario.pdf.mensile', [
                'lezioni' => $lezioni,
                'lezioniPerGiorno' => $lezioniPerGiorno,
                'statistiche' => $statistiche,
                'mese' => $mese,
                'anno' => $anno,
                'dataInizio' => $dataInizio,
                'dataFine' => $dataFine,
                'nomeMese' => $nomeMese,
            ])->render();

            echo "✅ Vista renderizzata con successo\n";
            echo "   Dimensione HTML: " . strlen($html) . " bytes\n\n";

        } catch (Exception $e) {
            echo "❌ ERRORE rendering vista:\n";
            echo "   " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . "\n";
            echo "   Linea: " . $e->getLine() . "\n\n";

            if (strpos($e->getMessage(), 'ora_inizio') !== false) {
                echo "⚠️  SUGGERIMENTO: Problema con formattazione orari\n";
                echo "   Verifica che ora_inizio e ora_fine usino Carbon::parse()\n\n";
            }

            exit(1);
        }
    }

    // ============================================
    // TEST 6: Generazione PDF completo
    // ============================================
    if ($testLevel >= 6) {
        echo "━━━ TEST 6: GENERAZIONE PDF COMPLETO ━━━\n\n";

        $mese = $_GET['mese'] ?? now()->month;
        $anno = $_GET['anno'] ?? now()->year;

        $dataInizio = Carbon::create($anno, $mese, 1)->startOfMonth();
        $dataFine = Carbon::create($anno, $mese, 1)->endOfMonth();

        $lezioni = Lezione::with(['professionista', 'sede', 'programma', 'clienti'])
            ->where('visibile_calendario', true)
            ->whereBetween('data', [$dataInizio, $dataFine])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();

        $lezioniPerGiorno = $lezioni->groupBy(function($lezione) {
            return $lezione->data->format('Y-m-d');
        });

        $statistiche = [
            'totale_lezioni' => $lezioni->count(),
            'totale_partecipanti' => $lezioni->sum(function($lezione) {
                return $lezione->clienti->count();
            }),
            'posti_disponibili' => $lezioni->sum('posti_totali'),
            'posti_occupati' => $lezioni->sum('posti_occupati'),
        ];

        $nomeMese = $dataInizio->locale('it')->isoFormat('MMMM YYYY');

        try {
            $pdf = \PDF::loadView('admin.calendario.pdf.mensile', [
                'lezioni' => $lezioni,
                'lezioniPerGiorno' => $lezioniPerGiorno,
                'statistiche' => $statistiche,
                'mese' => $mese,
                'anno' => $anno,
                'dataInizio' => $dataInizio,
                'dataFine' => $dataFine,
                'nomeMese' => $nomeMese,
            ]);

            echo "✅ PDF completo generato con successo!\n\n";

            // Download per test
            if (isset($_GET['download'])) {
                echo "📥 Download PDF...\n";
                $nomeFile = "test-calendario-{$anno}-{$mese}.pdf";
                return $pdf->download($nomeFile);
            } else {
                echo "💡 Aggiungi &download=1 all'URL per scaricare il PDF\n\n";
            }

        } catch (Exception $e) {
            echo "❌ ERRORE generazione PDF:\n";
            echo "   " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . "\n";
            echo "   Linea: " . $e->getLine() . "\n\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n\n";
            exit(1);
        }
    }

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TUTTI I TEST SUPERATI!\n\n";
    echo "Livelli disponibili:\n";
    echo "  1 = Verifica alias PDF\n";
    echo "  2 = PDF vuoto\n";
    echo "  3 = PDF con HTML\n";
    echo "  4 = Query database\n";
    echo "  5 = Rendering vista\n";
    echo "  6 = PDF completo\n\n";
    echo "Parametri opzionali:\n";
    echo "  &level=6     - Livello test (1-6)\n";
    echo "  &mese=11     - Mese (1-12)\n";
    echo "  &anno=2025   - Anno\n";
    echo "  &download=1  - Download PDF\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ ERRORE FATALE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Linea: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
