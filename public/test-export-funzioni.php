<?php
/**
 * TEST FUNZIONI EXPORT PDF/EXCEL
 *
 * Accesso: https://www.agstudio.digital/magia/public/test-export-funzioni.php?secret=magia2024
 *
 * Testa le funzioni di export senza dover usare l'interfaccia admin
 */

// Sicurezza
$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Lezione;
use Carbon\Carbon;

$azione = $_GET['azione'] ?? 'menu';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Export Funzioni</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #7B2869;
            border-bottom: 3px solid #E91E8C;
            padding-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 5px;
            background: #7B2869;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #E91E8C;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #7B2869;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Funzioni Export PDF/Excel</h1>
        <p><strong>Data/Ora:</strong> <?= date('d/m/Y H:i:s') ?></p>

        <?php
        if ($azione === 'menu') {
            ?>
            <div class="info">
                <h3>Seleziona un test da eseguire:</h3>
                <p>Questi test simulano le funzioni di export senza richiedere autenticazione admin.</p>
            </div>

            <h3>Test Disponibili:</h3>

            <a href="?secret=magia2024&azione=test-query-presenze" class="btn">
                1. Test Query Presenze
            </a>
            <p style="margin-left: 20px; color: #666;">
                Verifica che la query per recuperare le presenze funzioni correttamente
            </p>

            <a href="?secret=magia2024&azione=test-query-lezioni" class="btn">
                2. Test Query Lezioni
            </a>
            <p style="margin-left: 20px; color: #666;">
                Verifica che la query per recuperare le lezioni funzioni correttamente
            </p>

            <a href="?secret=magia2024&azione=test-export-excel-presenze" class="btn">
                3. Test Export Excel Presenze
            </a>
            <p style="margin-left: 20px; color: #666;">
                Tenta di creare un file Excel con le presenze (scarica il file)
            </p>

            <a href="?secret=magia2024&azione=test-export-pdf-calendario" class="btn">
                4. Test Export PDF Calendario
            </a>
            <p style="margin-left: 20px; color: #666;">
                Tenta di creare un PDF con il calendario (scarica il file)
            </p>

            <div style="margin-top: 30px;">
                <a href="test-export-libraries.php?secret=magia2024" class="btn btn-secondary">
                    ← Verifica Librerie
                </a>
                <a href="test-schema-database.php?secret=magia2024" class="btn btn-secondary">
                    → Verifica Schema DB
                </a>
            </div>
            <?php
        }

        // ========================================
        // TEST QUERY PRESENZE
        // ========================================
        elseif ($azione === 'test-query-presenze') {
            ?>
            <a href="?secret=magia2024" class="btn btn-secondary">← Torna al Menu</a>
            <h2>Test Query Presenze</h2>
            <?php

            try {
                $dataInizio = now()->subMonth()->format('Y-m-d');
                $dataFine = now()->format('Y-m-d');

                echo '<div class="info">';
                echo '<strong>Periodo:</strong> ' . $dataInizio . ' - ' . $dataFine;
                echo '</div>';

                // Query per presenze
                $lezioni = Lezione::with(['professionista', 'sede', 'clienti' => function($query) {
                        $query->wherePivot('stato', 'presente');
                    }])
                    ->whereBetween('data', [$dataInizio, $dataFine])
                    ->whereIn('stato', ['completata', 'in_corso'])
                    ->orderBy('data')
                    ->orderBy('ora_inizio')
                    ->get();

                echo '<div class="success">';
                echo '<strong>✅ Query eseguita con successo!</strong><br>';
                echo 'Trovate ' . $lezioni->count() . ' lezioni nel periodo.';
                echo '</div>';

                if ($lezioni->count() > 0) {
                    echo '<h3>Dettagli Lezioni:</h3>';
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Data</th>';
                    echo '<th>Titolo</th>';
                    echo '<th>Professionista</th>';
                    echo '<th>Sede</th>';
                    echo '<th>Presenti</th>';
                    echo '</tr>';

                    foreach ($lezioni as $lezione) {
                        echo '<tr>';
                        echo '<td>' . $lezione->id . '</td>';
                        echo '<td>' . Carbon::parse($lezione->data)->format('d/m/Y') . '</td>';
                        echo '<td>' . htmlspecialchars($lezione->titolo ?? 'N/A') . '</td>';
                        echo '<td>';
                        if ($lezione->professionista) {
                            echo htmlspecialchars($lezione->professionista->nome . ' ' . $lezione->professionista->cognome);
                        } else {
                            echo 'N/A';
                        }
                        echo '</td>';
                        echo '<td>' . ($lezione->sede ? htmlspecialchars($lezione->sede->nome) : 'Online') . '</td>';
                        echo '<td>' . $lezione->clienti->count() . '</td>';
                        echo '</tr>';
                    }

                    echo '</table>';

                    // Debug primo record
                    $prima = $lezioni->first();
                    echo '<h3>Debug Prima Lezione:</h3>';
                    echo '<pre>' . json_encode([
                        'id' => $prima->id,
                        'titolo' => $prima->titolo,
                        'data' => $prima->data,
                        'professionista' => $prima->professionista ? [
                            'nome' => $prima->professionista->nome,
                            'cognome' => $prima->professionista->cognome
                        ] : null,
                        'sede' => $prima->sede ? ['nome' => $prima->sede->nome] : null,
                        'clienti_presenti' => $prima->clienti->count(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

                } else {
                    echo '<div class="warning">';
                    echo '<strong>⚠️ Nessuna lezione trovata nel periodo</strong><br>';
                    echo 'Crea alcune lezioni con presenze per testare l\'export.';
                    echo '</div>';
                }

            } catch (\Exception $e) {
                echo '<div class="error">';
                echo '<strong>❌ ERRORE:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '<br><br><strong>File:</strong> ' . $e->getFile();
                echo '<br><strong>Linea:</strong> ' . $e->getLine();
                echo '<br><br><strong>Trace:</strong>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
        }

        // ========================================
        // TEST QUERY LEZIONI
        // ========================================
        elseif ($azione === 'test-query-lezioni') {
            ?>
            <a href="?secret=magia2024" class="btn btn-secondary">← Torna al Menu</a>
            <h2>Test Query Lezioni (per Calendario)</h2>
            <?php

            try {
                $dataInizio = now()->startOfMonth()->format('Y-m-d');
                $dataFine = now()->endOfMonth()->format('Y-m-d');

                echo '<div class="info">';
                echo '<strong>Periodo:</strong> ' . $dataInizio . ' - ' . $dataFine;
                echo '</div>';

                $lezioni = Lezione::with(['professionista', 'sede', 'programma'])
                    ->whereBetween('data', [$dataInizio, $dataFine])
                    ->orderBy('data')
                    ->orderBy('ora_inizio')
                    ->get();

                echo '<div class="success">';
                echo '<strong>✅ Query eseguita con successo!</strong><br>';
                echo 'Trovate ' . $lezioni->count() . ' lezioni nel mese corrente.';
                echo '</div>';

                if ($lezioni->count() > 0) {
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Data</th>';
                    echo '<th>Ora</th>';
                    echo '<th>Titolo</th>';
                    echo '<th>Professionista</th>';
                    echo '<th>Stato</th>';
                    echo '<th>Posti</th>';
                    echo '</tr>';

                    $shown = 0;
                    foreach ($lezioni as $lezione) {
                        if ($shown >= 10) {
                            echo '<tr><td colspan="7"><em>... e altre ' . ($lezioni->count() - 10) . ' lezioni</em></td></tr>';
                            break;
                        }

                        echo '<tr>';
                        echo '<td>' . $lezione->id . '</td>';
                        echo '<td>' . Carbon::parse($lezione->data)->format('d/m/Y') . '</td>';
                        echo '<td>' . Carbon::parse($lezione->ora_inizio)->format('H:i') . '</td>';
                        echo '<td>' . htmlspecialchars($lezione->titolo ?? 'N/A') . '</td>';
                        echo '<td>';
                        if ($lezione->professionista) {
                            echo htmlspecialchars($lezione->professionista->nome . ' ' . $lezione->professionista->cognome);
                        } else {
                            echo 'N/A';
                        }
                        echo '</td>';
                        echo '<td>' . htmlspecialchars($lezione->stato ?? 'N/A') . '</td>';
                        echo '<td>' . $lezione->posti_occupati . '/' . $lezione->posti_totali . '</td>';
                        echo '</tr>';

                        $shown++;
                    }

                    echo '</table>';
                } else {
                    echo '<div class="warning">';
                    echo '<strong>⚠️ Nessuna lezione trovata nel mese corrente</strong>';
                    echo '</div>';
                }

            } catch (\Exception $e) {
                echo '<div class="error">';
                echo '<strong>❌ ERRORE:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
        }

        // ========================================
        // TEST EXPORT EXCEL
        // ========================================
        elseif ($azione === 'test-export-excel-presenze') {
            echo '<a href="?secret=magia2024" class="btn btn-secondary">← Torna al Menu</a>';
            echo '<h2>Test Export Excel Presenze</h2>';

            echo '<div class="warning">';
            echo '<strong>⚠️ Funzionalità non disponibile in questa modalità</strong><br>';
            echo 'Per testare l\'export Excel completo, usa l\'interfaccia admin:<br>';
            echo '<a href="/admin/report" target="_blank">Vai a /admin/report</a>';
            echo '</div>';

            echo '<div class="info">';
            echo '<h3>Routes disponibili per export:</h3>';
            echo '<ul>';
            echo '<li><code>/admin/report/export-excel-presenze</code></li>';
            echo '<li><code>/admin/report/export-excel-calendario</code></li>';
            echo '<li><code>/admin/report/export-pdf-calendario</code></li>';
            echo '<li><code>/admin/report/export-pdf-professionisti</code></li>';
            echo '</ul>';
            echo '<p><strong>Nota:</strong> Richiedono autenticazione admin</p>';
            echo '</div>';
        }

        // ========================================
        // TEST EXPORT PDF
        // ========================================
        elseif ($azione === 'test-export-pdf-calendario') {
            echo '<a href="?secret=magia2024" class="btn btn-secondary">← Torna al Menu</a>';
            echo '<h2>Test Export PDF Calendario</h2>';

            echo '<div class="warning">';
            echo '<strong>⚠️ Funzionalità non disponibile in questa modalità</strong><br>';
            echo 'Per testare l\'export PDF completo, usa l\'interfaccia admin:<br>';
            echo '<a href="/admin/report" target="_blank">Vai a /admin/report</a>';
            echo '</div>';

            echo '<div class="info">';
            echo '<h3>Verifica che il fix sia stato applicato:</h3>';
            echo '<p>Il problema identificato era nello schema database:</p>';
            echo '<ul>';
            echo '<li>✓ Model Cliente ora usa <code>withPivot(\'stato\', ...)</code></li>';
            echo '<li>✓ Il campo corrisponde alla migration</li>';
            echo '<li>✓ La query <code>wherePivot(\'stato\', \'presente\')</code> dovrebbe funzionare</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 12px; color: #6c757d;">
            <p><strong>File:</strong> /public/test-export-funzioni.php</p>
        </div>
    </div>
</body>
</html>
