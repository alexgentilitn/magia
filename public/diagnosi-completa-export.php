<?php
/**
 * DIAGNOSI COMPLETA EXPORT PDF/EXCEL - MA.GIA DONNA
 *
 * URL: https://www.agstudio.digital/magia/public/diagnosi-completa-export.php?secret=magia2024
 *
 * Questo script esegue tutti i test necessari per diagnosticare problemi export:
 * 1. Verifica librerie (PhpSpreadsheet, Dompdf, etc.)
 * 2. Analizza schema database cliente_lezione
 * 3. Testa query presenze e lezioni
 * 4. Genera log dettagliati per correzioni
 *
 * Autore: Claude Code Testing Suite
 * Data: 16 Novembre 2025
 */

// ============================================================================
// SICUREZZA
// ============================================================================
$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    http_response_code(403);
    die('
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Accesso Negato</title></head>
    <body style="font-family: Arial; text-align: center; margin-top: 100px;">
        <h1>🔒 Accesso Negato</h1>
        <p>Usa: <code>?secret=magia2024</code></p>
    </body></html>
    ');
}

// ============================================================================
// BOOTSTRAP LARAVEL
// ============================================================================
require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
} catch (\Exception $e) {
    die('ERRORE BOOTSTRAP LARAVEL: ' . $e->getMessage());
}

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Lezione;
use Carbon\Carbon;

// ============================================================================
// CONFIGURAZIONE
// ============================================================================
$logFile = __DIR__ . '/diagnosi-export-log.txt';
$risultati = [];
$errori = [];
$warnings = [];
$successi = [];

// ============================================================================
// FUNZIONI HELPER
// ============================================================================

function scriviLog($messaggio) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $log = "[{$timestamp}] {$messaggio}\n";
    file_put_contents($logFile, $log, FILE_APPEND);
}

function aggiungiRisultato($categoria, $nome, $status, $dettagli = '') {
    global $risultati, $errori, $warnings, $successi;

    $risultati[] = [
        'categoria' => $categoria,
        'nome' => $nome,
        'status' => $status, // 'success', 'error', 'warning'
        'dettagli' => $dettagli
    ];

    $messaggio = "[{$categoria}] {$nome}: {$status}";
    if ($dettagli) $messaggio .= " - {$dettagli}";

    scriviLog($messaggio);

    if ($status === 'success') {
        $successi[] = $nome;
    } elseif ($status === 'error') {
        $errori[] = $nome . ': ' . $dettagli;
    } elseif ($status === 'warning') {
        $warnings[] = $nome . ': ' . $dettagli;
    }
}

// Inizializza log
file_put_contents($logFile, "");
scriviLog("=== INIZIO DIAGNOSI EXPORT PDF/EXCEL ===");
scriviLog("Server: " . $_SERVER['HTTP_HOST']);
scriviLog("Database: " . config('database.connections.mysql.database'));

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosi Completa Export - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #7B2869 0%, #E91E8C 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header .info {
            opacity: 0.9;
            font-size: 0.9em;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 40px;
            border-left: 4px solid #7B2869;
            padding-left: 20px;
        }
        .section h2 {
            color: #7B2869;
            font-size: 1.8em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .section h2 .icon {
            font-size: 1.2em;
            margin-right: 10px;
        }
        .test-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #dee2e6;
        }
        .test-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .test-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .test-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .test-item h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .test-item .status {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .status.success { color: #28a745; }
        .status.error { color: #dc3545; }
        .status.warning { color: #856404; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #7B2869;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f1f3f5;
        }
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .summary-card h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-box.error {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        .stat-box.warning {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }
        .stat-box .number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-box .label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.85em;
            line-height: 1.4;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #7B2869;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
            margin: 5px;
        }
        .btn:hover {
            background: #E91E8C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9em;
        }
        .log-download {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
            margin-left: 10px;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-error { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔬 Diagnosi Completa Export PDF/Excel</h1>
            <div class="info">
                <strong>MA.GIA DONNA</strong> |
                Data: <?= date('d/m/Y H:i:s') ?> |
                Server: <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>
            </div>
        </div>

        <div class="content">
            <?php
            // ================================================================
            // SEZIONE 1: VERIFICA LIBRERIE EXPORT
            // ================================================================
            ?>
            <div class="section">
                <h2><span class="icon">📚</span> 1. Verifica Librerie Export</h2>

                <?php
                // Test PhpSpreadsheet
                $testNome = 'PhpSpreadsheet (Excel)';
                $testItem = '<div class="test-item ';

                if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                    try {
                        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        $sheet->setCellValue('A1', 'Test MA.GIA');

                        $testItem .= 'success">';
                        $testItem .= '<h3>✅ ' . $testNome . '</h3>';
                        $testItem .= '<div class="status success">INSTALLATA E FUNZIONANTE</div>';
                        $testItem .= '<p>✓ Classe trovata: <code>\PhpOffice\PhpSpreadsheet\Spreadsheet</code></p>';
                        $testItem .= '<p>✓ Test creazione spreadsheet: OK</p>';

                        aggiungiRisultato('Librerie', $testNome, 'success', 'Installata e funzionante');
                    } catch (\Exception $e) {
                        $testItem .= 'error">';
                        $testItem .= '<h3>❌ ' . $testNome . '</h3>';
                        $testItem .= '<div class="status error">INSTALLATA MA CON ERRORI</div>';
                        $testItem .= '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';

                        aggiungiRisultato('Librerie', $testNome, 'error', $e->getMessage());
                    }
                } else {
                    $testItem .= 'error">';
                    $testItem .= '<h3>❌ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status error">NON INSTALLATA</div>';
                    $testItem .= '<p>Comando: <code>composer require phpoffice/phpspreadsheet</code></p>';

                    aggiungiRisultato('Librerie', $testNome, 'error', 'Libreria non installata');
                }
                $testItem .= '</div>';
                echo $testItem;

                // Test Dompdf
                $testNome = 'Dompdf (PDF)';
                $testItem = '<div class="test-item ';

                if (class_exists(\Dompdf\Dompdf::class)) {
                    try {
                        $dompdf = new \Dompdf\Dompdf();
                        $dompdf->loadHtml('<h1>Test MA.GIA</h1>');

                        $testItem .= 'success">';
                        $testItem .= '<h3>✅ ' . $testNome . '</h3>';
                        $testItem .= '<div class="status success">INSTALLATA E FUNZIONANTE</div>';
                        $testItem .= '<p>✓ Classe trovata: <code>\Dompdf\Dompdf</code></p>';
                        $testItem .= '<p>✓ Test creazione PDF: OK</p>';

                        aggiungiRisultato('Librerie', $testNome, 'success', 'Installata e funzionante');
                    } catch (\Exception $e) {
                        $testItem .= 'error">';
                        $testItem .= '<h3>❌ ' . $testNome . '</h3>';
                        $testItem .= '<div class="status error">INSTALLATA MA CON ERRORI</div>';
                        $testItem .= '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';

                        aggiungiRisultato('Librerie', $testNome, 'error', $e->getMessage());
                    }
                } else {
                    $testItem .= 'error">';
                    $testItem .= '<h3>❌ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status error">NON INSTALLATA</div>';
                    $testItem .= '<p>Comando: <code>composer require dompdf/dompdf</code></p>';

                    aggiungiRisultato('Librerie', $testNome, 'error', 'Libreria non installata');
                }
                $testItem .= '</div>';
                echo $testItem;

                // Test Barryvdh DomPDF
                $testNome = 'Barryvdh DomPDF (Laravel)';
                $testItem = '<div class="test-item ';

                if (class_exists(\Barryvdh\DomPDF\ServiceProvider::class)) {
                    $testItem .= 'success">';
                    $testItem .= '<h3>✅ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status success">INSTALLATA</div>';
                    $testItem .= '<p>✓ Facade <code>Pdf::loadView()</code> disponibile</p>';

                    aggiungiRisultato('Librerie', $testNome, 'success', 'Installata');
                } else {
                    $testItem .= 'error">';
                    $testItem .= '<h3>❌ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status error">NON INSTALLATA</div>';
                    $testItem .= '<p>Comando: <code>composer require barryvdh/laravel-dompdf</code></p>';

                    aggiungiRisultato('Librerie', $testNome, 'error', 'Libreria non installata');
                }
                $testItem .= '</div>';
                echo $testItem;

                // Test Maatwebsite Excel
                $testNome = 'Maatwebsite Excel (Laravel)';
                $testItem = '<div class="test-item ';

                if (class_exists(\Maatwebsite\Excel\ExcelServiceProvider::class)) {
                    $testItem .= 'success">';
                    $testItem .= '<h3>✅ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status success">INSTALLATA</div>';
                    $testItem .= '<p>✓ Facade <code>Excel::download()</code> disponibile</p>';

                    aggiungiRisultato('Librerie', $testNome, 'success', 'Installata');
                } else {
                    $testItem .= 'warning">';
                    $testItem .= '<h3>⚠️ ' . $testNome . '</h3>';
                    $testItem .= '<div class="status warning">NON INSTALLATA (Opzionale)</div>';
                    $testItem .= '<p>Puoi usare PhpSpreadsheet direttamente</p>';

                    aggiungiRisultato('Librerie', $testNome, 'warning', 'Non installata (opzionale)');
                }
                $testItem .= '</div>';
                echo $testItem;
                ?>
            </div>

            <?php
            // ================================================================
            // SEZIONE 2: ANALISI SCHEMA DATABASE
            // ================================================================
            ?>
            <div class="section">
                <h2><span class="icon">🗄️</span> 2. Analisi Schema Database</h2>

                <?php
                try {
                    // Test connessione
                    DB::connection()->getPdo();
                    aggiungiRisultato('Database', 'Connessione MySQL', 'success', 'Connesso');

                    echo '<div class="test-item success">';
                    echo '<h3>✅ Connessione Database</h3>';
                    echo '<div class="status success">CONNESSO</div>';
                    echo '<p>Database: <code>' . config('database.connections.mysql.database') . '</code></p>';
                    echo '<p>Host: <code>' . config('database.connections.mysql.host') . '</code></p>';
                    echo '</div>';

                    // Verifica tabella cliente_lezione
                    if (Schema::hasTable('cliente_lezione')) {
                        aggiungiRisultato('Database', 'Tabella cliente_lezione', 'success', 'Esiste');

                        echo '<div class="test-item success">';
                        echo '<h3>✅ Tabella cliente_lezione</h3>';
                        echo '<div class="status success">ESISTE</div>';

                        // Schema completo
                        $columns = DB::select('DESCRIBE cliente_lezione');

                        echo '<h4 style="margin-top: 20px;">Schema Completo:</h4>';
                        echo '<table>';
                        echo '<thead><tr>';
                        echo '<th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>';
                        echo '</tr></thead><tbody>';

                        $campiTrovati = [];
                        foreach ($columns as $column) {
                            $campiTrovati[] = $column->Field;

                            echo '<tr>';
                            echo '<td><strong>' . htmlspecialchars($column->Field) . '</strong></td>';
                            echo '<td><code>' . htmlspecialchars($column->Type) . '</code></td>';
                            echo '<td>' . htmlspecialchars($column->Null) . '</td>';
                            echo '<td>' . htmlspecialchars($column->Key) . '</td>';
                            echo '<td>' . htmlspecialchars($column->Default ?? 'NULL') . '</td>';
                            echo '<td>' . htmlspecialchars($column->Extra) . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>';

                        // Analisi campo 'stato'
                        $statoCampo = null;
                        foreach ($columns as $column) {
                            if ($column->Field === 'stato') {
                                $statoCampo = $column;
                                break;
                            }
                        }

                        if ($statoCampo) {
                            echo '<h4 style="margin-top: 20px;">Analisi Campo "stato":</h4>';

                            if (strpos($statoCampo->Type, 'enum') === 0) {
                                preg_match("/enum\((.*)\)/", $statoCampo->Type, $matches);
                                if (isset($matches[1])) {
                                    $valori = str_replace("'", "", $matches[1]);
                                    $valoriArray = explode(',', $valori);

                                    echo '<p>Tipo: <code>ENUM</code> ✓</p>';
                                    echo '<p><strong>Valori possibili:</strong></p>';
                                    echo '<ul>';
                                    foreach ($valoriArray as $valore) {
                                        echo '<li><code>' . htmlspecialchars($valore) . '</code>';
                                        if ($valore === 'presente') {
                                            echo ' <span class="badge badge-success">← USATO PER EXPORT</span>';
                                        }
                                        echo '</li>';
                                    }
                                    echo '</ul>';

                                    if (in_array('presente', $valoriArray)) {
                                        aggiungiRisultato('Database', 'Campo stato ENUM', 'success', 'Valore "presente" disponibile');
                                        echo '<p class="status success">✅ Valore "presente" disponibile per export</p>';
                                    } else {
                                        aggiungiRisultato('Database', 'Campo stato ENUM', 'error', 'Valore "presente" NON trovato');
                                        echo '<p class="status error">❌ Valore "presente" NON trovato nell\'enum</p>';
                                    }
                                }
                            } else {
                                aggiungiRisultato('Database', 'Campo stato', 'warning', 'Non è un ENUM: ' . $statoCampo->Type);
                            }
                        } else {
                            aggiungiRisultato('Database', 'Campo stato', 'error', 'Campo "stato" non trovato');
                            echo '<p class="status error">❌ Campo "stato" NON trovato nella tabella</p>';
                        }

                        // Statistiche dati reali
                        $count = DB::table('cliente_lezione')->count();
                        echo '<h4 style="margin-top: 20px;">Dati Reali:</h4>';
                        echo '<p><strong>Record totali:</strong> ' . $count . '</p>';

                        if ($count > 0 && in_array('stato', $campiTrovati)) {
                            $stati = DB::table('cliente_lezione')
                                ->select('stato', DB::raw('COUNT(*) as totale'))
                                ->groupBy('stato')
                                ->get();

                            echo '<table style="max-width: 400px;">';
                            echo '<tr><th>Stato</th><th>Totale</th></tr>';
                            foreach ($stati as $stato) {
                                echo '<tr>';
                                echo '<td><code>' . htmlspecialchars($stato->stato) . '</code></td>';
                                echo '<td>' . $stato->totale . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';

                            aggiungiRisultato('Database', 'Dati cliente_lezione', 'success', "{$count} record trovati");
                        } elseif ($count == 0) {
                            echo '<p class="status warning">⚠️ Nessun record presente (tabella vuota)</p>';
                            aggiungiRisultato('Database', 'Dati cliente_lezione', 'warning', 'Tabella vuota');
                        }

                        echo '</div>';

                    } else {
                        aggiungiRisultato('Database', 'Tabella cliente_lezione', 'error', 'Tabella non esiste');

                        echo '<div class="test-item error">';
                        echo '<h3>❌ Tabella cliente_lezione</h3>';
                        echo '<div class="status error">NON ESISTE</div>';
                        echo '<p>Esegui le migration per creare la tabella.</p>';
                        echo '</div>';
                    }

                } catch (\Exception $e) {
                    aggiungiRisultato('Database', 'Connessione/Verifica', 'error', $e->getMessage());

                    echo '<div class="test-item error">';
                    echo '<h3>❌ Errore Database</h3>';
                    echo '<div class="status error">ERRORE CONNESSIONE</div>';
                    echo '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';
                }
                ?>
            </div>

            <?php
            // ================================================================
            // SEZIONE 3: TEST QUERY EXPORT
            // ================================================================
            ?>
            <div class="section">
                <h2><span class="icon">🔍</span> 3. Test Query Export</h2>

                <?php
                // Test Query Presenze
                $testNome = 'Query Presenze (Export Excel)';
                try {
                    $dataInizio = now()->subMonth()->format('Y-m-d');
                    $dataFine = now()->format('Y-m-d');

                    $lezioni = Lezione::with(['professionista', 'sede', 'clienti' => function($query) {
                            $query->wherePivot('stato', 'presente');
                        }])
                        ->whereBetween('data', [$dataInizio, $dataFine])
                        ->whereIn('stato', ['completata', 'in_corso'])
                        ->orderBy('data')
                        ->orderBy('ora_inizio')
                        ->get();

                    echo '<div class="test-item success">';
                    echo '<h3>✅ ' . $testNome . '</h3>';
                    echo '<div class="status success">QUERY ESEGUITA CON SUCCESSO</div>';
                    echo '<p>Periodo: ' . $dataInizio . ' → ' . $dataFine . '</p>';
                    echo '<p>Lezioni trovate: <strong>' . $lezioni->count() . '</strong></p>';

                    if ($lezioni->count() > 0) {
                        $totalepresenti = 0;
                        foreach ($lezioni as $l) {
                            $totalePresenti += $l->clienti->count();
                        }
                        echo '<p>Totale presenze: <strong>' . $totalePresenti . '</strong></p>';

                        aggiungiRisultato('Query', $testNome, 'success', "{$lezioni->count()} lezioni, {$totalePresenti} presenze");
                    } else {
                        echo '<p class="status warning">⚠️ Nessuna lezione nel periodo (normale se non ci sono dati)</p>';
                        aggiungiRisultato('Query', $testNome, 'warning', 'Nessuna lezione trovata');
                    }

                    echo '</div>';

                } catch (\Exception $e) {
                    echo '<div class="test-item error">';
                    echo '<h3>❌ ' . $testNome . '</h3>';
                    echo '<div class="status error">ERRORE QUERY</div>';
                    echo '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';

                    aggiungiRisultato('Query', $testNome, 'error', $e->getMessage());
                }

                // Test Query Calendario
                $testNome = 'Query Calendario (Export PDF)';
                try {
                    $dataInizio = now()->startOfMonth()->format('Y-m-d');
                    $dataFine = now()->endOfMonth()->format('Y-m-d');

                    $lezioni = Lezione::with(['professionista', 'sede', 'programma'])
                        ->whereBetween('data', [$dataInizio, $dataFine])
                        ->orderBy('data')
                        ->orderBy('ora_inizio')
                        ->get();

                    echo '<div class="test-item success">';
                    echo '<h3>✅ ' . $testNome . '</h3>';
                    echo '<div class="status success">QUERY ESEGUITA CON SUCCESSO</div>';
                    echo '<p>Periodo: ' . $dataInizio . ' → ' . $dataFine . '</p>';
                    echo '<p>Lezioni trovate: <strong>' . $lezioni->count() . '</strong></p>';

                    if ($lezioni->count() > 0) {
                        $perStato = $lezioni->countBy('stato');
                        echo '<p><strong>Distribuzione per stato:</strong></p>';
                        echo '<ul>';
                        foreach ($perStato as $stato => $count) {
                            echo '<li>' . htmlspecialchars($stato) . ': ' . $count . '</li>';
                        }
                        echo '</ul>';

                        aggiungiRisultato('Query', $testNome, 'success', "{$lezioni->count()} lezioni trovate");
                    } else {
                        echo '<p class="status warning">⚠️ Nessuna lezione nel mese corrente</p>';
                        aggiungiRisultato('Query', $testNome, 'warning', 'Nessuna lezione trovata');
                    }

                    echo '</div>';

                } catch (\Exception $e) {
                    echo '<div class="test-item error">';
                    echo '<h3>❌ ' . $testNome . '</h3>';
                    echo '<div class="status error">ERRORE QUERY</div>';
                    echo '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';

                    aggiungiRisultato('Query', $testNome, 'error', $e->getMessage());
                }
                ?>
            </div>

            <?php
            // ================================================================
            // SEZIONE 4: RIEPILOGO FINALE
            // ================================================================

            $totalTest = count($risultati);
            $successCount = count($successi);
            $errorCount = count($errori);
            $warningCount = count($warnings);

            $percentualeSuccesso = $totalTest > 0 ? round(($successCount / $totalTest) * 100) : 0;

            scriviLog("=== RIEPILOGO ===");
            scriviLog("Test totali: {$totalTest}");
            scriviLog("Successi: {$successCount}");
            scriviLog("Errori: {$errorCount}");
            scriviLog("Warning: {$warningCount}");
            scriviLog("Percentuale successo: {$percentualeSuccesso}%");
            ?>

            <div class="section">
                <h2><span class="icon">📊</span> 4. Riepilogo Finale</h2>

                <div class="summary-grid">
                    <div class="stat-box">
                        <div class="number"><?= $totalTest ?></div>
                        <div class="label">Test Totali</div>
                    </div>
                    <div class="stat-box success">
                        <div class="number"><?= $successCount ?></div>
                        <div class="label">Successi ✓</div>
                    </div>
                    <div class="stat-box error">
                        <div class="number"><?= $errorCount ?></div>
                        <div class="label">Errori ✗</div>
                    </div>
                    <div class="stat-box warning">
                        <div class="number"><?= $warningCount ?></div>
                        <div class="label">Warning ⚠</div>
                    </div>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $percentualeSuccesso ?>%">
                        <?= $percentualeSuccesso ?>% Completato
                    </div>
                </div>

                <?php if ($errorCount === 0 && $warningCount === 0): ?>
                    <div class="summary-card" style="background: #d4edda; border-left: 4px solid #28a745;">
                        <h3 style="color: #155724;">🎉 Tutto Perfetto!</h3>
                        <p style="color: #155724;">
                            <strong>Tutti i test sono passati con successo!</strong><br>
                            Le funzioni di export PDF/Excel dovrebbero funzionare correttamente.
                        </p>
                        <p style="margin-top: 15px;">
                            <a href="/admin/report" class="btn">Vai ai Report →</a>
                        </p>
                    </div>
                <?php elseif ($errorCount > 0): ?>
                    <div class="summary-card" style="background: #f8d7da; border-left: 4px solid #dc3545;">
                        <h3 style="color: #721c24;">❌ Problemi Rilevati</h3>
                        <p style="color: #721c24;">
                            <strong>Sono stati rilevati <?= $errorCount ?> errori che richiedono attenzione.</strong>
                        </p>
                        <h4 style="margin-top: 15px;">Errori da Correggere:</h4>
                        <ul style="color: #721c24;">
                            <?php foreach ($errori as $errore): ?>
                                <li><?= htmlspecialchars($errore) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="summary-card" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                        <h3 style="color: #856404;">⚠️ Warning Rilevati</h3>
                        <p style="color: #856404;">
                            <strong>Ci sono <?= $warningCount ?> warning ma nessun errore critico.</strong>
                        </p>
                        <ul style="color: #856404;">
                            <?php foreach ($warnings as $warning): ?>
                                <li><?= htmlspecialchars($warning) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="summary-card">
                    <h3>📝 Dettaglio Risultati per Categoria</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Test</th>
                                <th>Risultato</th>
                                <th>Dettagli</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($risultati as $risultato): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($risultato['categoria']) ?></strong></td>
                                    <td><?= htmlspecialchars($risultato['nome']) ?></td>
                                    <td>
                                        <?php if ($risultato['status'] === 'success'): ?>
                                            <span class="badge badge-success">✓ SUCCESS</span>
                                        <?php elseif ($risultato['status'] === 'error'): ?>
                                            <span class="badge badge-error">✗ ERROR</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">⚠ WARNING</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($risultato['dettagli']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            // ================================================================
            // SEZIONE 5: LOG E DOWNLOAD
            // ================================================================

            scriviLog("=== FINE DIAGNOSI ===");
            scriviLog("");

            $logContent = file_get_contents($logFile);
            ?>

            <div class="section">
                <h2><span class="icon">📄</span> 5. Log Completo</h2>

                <div class="log-download">
                    <h3>📥 Download Log</h3>
                    <p>Il log completo è stato salvato e può essere scaricato per analisi dettagliate o per invio allo sviluppatore.</p>
                    <p>
                        <a href="diagnosi-export-log.txt" class="btn" download>⬇️ Scarica Log Completo</a>
                    </p>
                    <p style="margin-top: 10px; font-size: 0.9em;">
                        <strong>File:</strong> <code><?= basename($logFile) ?></code><br>
                        <strong>Dimensione:</strong> <?= number_format(strlen($logContent)) ?> bytes<br>
                        <strong>Righe:</strong> <?= substr_count($logContent, "\n") ?>
                    </p>
                </div>

                <div class="summary-card">
                    <h3>Anteprima Log (ultime 50 righe):</h3>
                    <pre><?php
                        $lines = explode("\n", $logContent);
                        $lastLines = array_slice($lines, -50);
                        echo htmlspecialchars(implode("\n", $lastLines));
                    ?></pre>
                </div>
            </div>

            <?php
            // ================================================================
            // SEZIONE 6: AZIONI CONSIGLIATE
            // ================================================================
            ?>

            <div class="section">
                <h2><span class="icon">🎯</span> 6. Prossimi Passi</h2>

                <div class="summary-card">
                    <h3>Cosa Fare Ora:</h3>

                    <?php if ($errorCount === 0): ?>
                        <ol>
                            <li><strong>✅ Test completati con successo!</strong></li>
                            <li>Vai su <a href="/admin/report" target="_blank">/admin/report</a></li>
                            <li>Prova gli export:
                                <ul>
                                    <li>Export Excel Presenze</li>
                                    <li>Export PDF Calendario</li>
                                    <li>Export PDF Professionisti</li>
                                </ul>
                            </li>
                            <li>Se tutto funziona, puoi rimuovere questo file di diagnosi</li>
                        </ol>
                    <?php else: ?>
                        <ol>
                            <li><strong>Scarica il log completo</strong> (pulsante sopra)</li>
                            <li><strong>Leggi gli errori rilevati</strong> nella sezione Riepilogo</li>
                            <li><strong>Correggi gli errori critici:</strong>
                                <ul>
                                    <?php foreach ($errori as $errore): ?>
                                        <li><?= htmlspecialchars($errore) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li><strong>Ri-esegui questa diagnosi</strong> dopo le correzioni</li>
                            <li>Se serve aiuto, invia il log allo sviluppatore</li>
                        </ol>
                    <?php endif; ?>
                </div>

                <div class="summary-card" style="background: #e7f3ff; border-left: 4px solid #2196F3;">
                    <h3>ℹ️ Informazioni Tecniche</h3>
                    <p><strong>Fix Applicato in questo Deploy:</strong></p>
                    <ul>
                        <li>✓ Corretto Model <code>Cliente.php</code></li>
                        <li>✓ Allineato <code>withPivot()</code> con schema database reale</li>
                        <li>✓ Campo <code>'stato'</code> invece di <code>'stato_prenotazione'</code></li>
                        <li>✓ Aggiunti campi: <code>'valutazione'</code>, <code>'feedback'</code>, <code>'data_prenotazione'</code></li>
                    </ul>
                    <p style="margin-top: 10px;"><strong>File modificati:</strong></p>
                    <ul>
                        <li><code>app/Models/Cliente.php</code></li>
                        <li><code>database/factories/ClienteFactory.php</code></li>
                        <li><code>tests/Feature/ExportPdfExcelTest.php</code></li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="footer">
            <p><strong>Diagnosi Completa Export PDF/Excel - MA.GIA DONNA</strong></p>
            <p>File: <code>/public/diagnosi-completa-export.php</code></p>
            <p>Log salvato in: <code>/public/diagnosi-export-log.txt</code></p>
            <p>Generato da: <strong>Claude Code Testing Suite</strong> | Data: <?= date('d/m/Y H:i:s') ?></p>
            <p style="margin-top: 10px;">
                <a href="?secret=magia2024" class="btn btn-secondary">🔄 Ri-esegui Diagnosi</a>
                <a href="/admin/report" class="btn">📊 Vai ai Report</a>
            </p>
        </div>
    </div>
</body>
</html>
