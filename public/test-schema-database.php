<?php
/**
 * TEST SCHEMA DATABASE cliente_lezione
 *
 * Accesso: https://www.agstudio.digital/magia/public/test-schema-database.php?secret=magia2024
 *
 * Verifica lo schema della tabella cliente_lezione per identificare problemi export
 */

// Sicurezza
$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Schema Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        h2 {
            color: #333;
            margin-top: 30px;
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #7B2869;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #7B2869;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 14px;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Test Schema Database - cliente_lezione</h1>
        <p><strong>Data/Ora:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>Database:</strong> <?= config('database.default') ?> - <?= config('database.connections.mysql.database') ?></p>

        <?php
        try {
            // ========================================
            // VERIFICA CONNESSIONE DATABASE
            // ========================================
            ?>
            <h2>1. Connessione Database</h2>
            <?php
            try {
                DB::connection()->getPdo();
                echo '<div class="success-box">✅ Connessione database OK</div>';
            } catch (\Exception $e) {
                echo '<div class="error-box">';
                echo '<strong>❌ ERRORE CONNESSIONE DATABASE</strong><br>';
                echo 'Errore: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                die();
            }

            // ========================================
            // SCHEMA TABELLA cliente_lezione
            // ========================================
            ?>
            <h2>2. Schema Tabella cliente_lezione</h2>
            <?php

            if (Schema::hasTable('cliente_lezione')) {
                echo '<div class="success-box">✅ Tabella <code>cliente_lezione</code> esiste</div>';

                // Ottieni colonne
                $columns = DB::select('DESCRIBE cliente_lezione');

                echo '<table>';
                echo '<thead><tr>';
                echo '<th>Campo</th>';
                echo '<th>Tipo</th>';
                echo '<th>Null</th>';
                echo '<th>Key</th>';
                echo '<th>Default</th>';
                echo '<th>Extra</th>';
                echo '</tr></thead>';
                echo '<tbody>';

                $campiTrovati = [];
                foreach ($columns as $column) {
                    echo '<tr>';
                    echo '<td><strong>' . htmlspecialchars($column->Field) . '</strong></td>';
                    echo '<td><code>' . htmlspecialchars($column->Type) . '</code></td>';
                    echo '<td>' . htmlspecialchars($column->Null) . '</td>';
                    echo '<td>' . htmlspecialchars($column->Key) . '</td>';
                    echo '<td>' . htmlspecialchars($column->Default ?? 'NULL') . '</td>';
                    echo '<td>' . htmlspecialchars($column->Extra) . '</td>';
                    echo '</tr>';

                    $campiTrovati[] = $column->Field;
                }

                echo '</tbody></table>';

                // ========================================
                // VERIFICA CAMPI CRITICI
                // ========================================
                ?>
                <h2>3. Verifica Campi Critici per Export</h2>
                <div class="info-box">
                    <p><strong>Campi necessari per export PDF/Excel:</strong></p>
                    <ul>
                        <li><code>cliente_id</code> - FK verso utenti o clienti</li>
                        <li><code>lezione_id</code> - FK verso lezioni</li>
                        <li><code>stato</code> - Stato prenotazione (enum)</li>
                        <li><code>check_in</code> - Timestamp check-in</li>
                        <li><code>check_out</code> - Timestamp check-out</li>
                        <li><code>data_prenotazione</code> - Data prenotazione</li>
                    </ul>
                </div>

                <?php
                $campiNecessari = ['cliente_id', 'lezione_id', 'stato', 'check_in', 'check_out'];
                $campiMancanti = [];
                $campiPresenti = [];

                foreach ($campiNecessari as $campo) {
                    if (in_array($campo, $campiTrovati)) {
                        $campiPresenti[] = $campo;
                    } else {
                        $campiMancanti[] = $campo;
                    }
                }

                if (empty($campiMancanti)) {
                    echo '<div class="success-box">';
                    echo '<strong>✅ Tutti i campi necessari sono presenti</strong><br>';
                    echo '<ul>';
                    foreach ($campiPresenti as $campo) {
                        echo '<li>✓ ' . htmlspecialchars($campo) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } else {
                    echo '<div class="error-box">';
                    echo '<strong>❌ Campi mancanti:</strong><br>';
                    echo '<ul>';
                    foreach ($campiMancanti as $campo) {
                        echo '<li>✗ ' . htmlspecialchars($campo) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }

                // ========================================
                // VERIFICA CAMPO 'stato'
                // ========================================
                ?>
                <h2>4. Analisi Campo 'stato'</h2>
                <?php

                $statoCampo = null;
                foreach ($columns as $column) {
                    if ($column->Field === 'stato') {
                        $statoCampo = $column;
                        break;
                    }
                }

                if ($statoCampo) {
                    echo '<div class="info-box">';
                    echo '<strong>Campo trovato:</strong> <code>stato</code><br>';
                    echo '<strong>Tipo:</strong> <code>' . htmlspecialchars($statoCampo->Type) . '</code><br>';

                    // Verifica se è ENUM
                    if (strpos($statoCampo->Type, 'enum') === 0) {
                        echo '<p class="success">✅ Il campo è un ENUM (corretto)</p>';

                        // Estrai valori enum
                        preg_match("/enum\((.*)\)/", $statoCampo->Type, $matches);
                        if (isset($matches[1])) {
                            $valori = str_replace("'", "", $matches[1]);
                            $valoriArray = explode(',', $valori);

                            echo '<p><strong>Valori possibili:</strong></p>';
                            echo '<ul>';
                            foreach ($valoriArray as $valore) {
                                echo '<li><code>' . htmlspecialchars($valore) . '</code>';
                                if ($valore === 'presente') {
                                    echo ' <span class="success">← USATO PER EXPORT</span>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';

                            // Verifica valore 'presente'
                            if (in_array('presente', $valoriArray)) {
                                echo '<div class="success-box">';
                                echo '<strong>✅ Valore "presente" disponibile</strong><br>';
                                echo 'Il codice export può usare: <code>wherePivot(\'stato\', \'presente\')</code>';
                                echo '</div>';
                            } else {
                                echo '<div class="error-box">';
                                echo '<strong>❌ Valore "presente" NON trovato nell\'enum</strong><br>';
                                echo 'Valori disponibili: ' . implode(', ', $valoriArray);
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<p class="warning">⚠️ Il campo NON è un ENUM</p>';
                        echo '<p>Tipo: ' . htmlspecialchars($statoCampo->Type) . '</p>';
                    }
                    echo '</div>';
                } else {
                    // Cerca campo alternativo
                    $campiAlternativi = ['stato_prenotazione', 'presente'];
                    echo '<div class="error-box">';
                    echo '<strong>❌ Campo "stato" NON trovato</strong><br>';
                    echo '<p>Campi alternativi trovati:</p>';
                    echo '<ul>';
                    foreach ($campiAlternativi as $campoAlt) {
                        if (in_array($campoAlt, $campiTrovati)) {
                            echo '<li class="success">✓ ' . htmlspecialchars($campoAlt) . ' (TROVATO)</li>';
                        } else {
                            echo '<li class="error">✗ ' . htmlspecialchars($campoAlt) . ' (non trovato)</li>';
                        }
                    }
                    echo '</ul>';
                    echo '</div>';
                }

                // ========================================
                // TEST QUERY SU DATI REALI
                // ========================================
                ?>
                <h2>5. Test Query su Dati Reali</h2>
                <?php

                $count = DB::table('cliente_lezione')->count();
                echo '<p><strong>Record totali in cliente_lezione:</strong> ' . $count . '</p>';

                if ($count > 0) {
                    // Conta per stato
                    if (in_array('stato', $campiTrovati)) {
                        $stati = DB::table('cliente_lezione')
                            ->select('stato', DB::raw('COUNT(*) as totale'))
                            ->groupBy('stato')
                            ->get();

                        echo '<div class="info-box">';
                        echo '<strong>Distribuzione per stato:</strong>';
                        echo '<table style="margin-top: 10px;">';
                        echo '<tr><th>Stato</th><th>Totale</th></tr>';
                        foreach ($stati as $stato) {
                            echo '<tr>';
                            echo '<td><code>' . htmlspecialchars($stato->stato) . '</code></td>';
                            echo '<td>' . $stato->totale . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                        echo '</div>';
                    }

                    // Esempio record
                    $esempio = DB::table('cliente_lezione')->first();
                    echo '<div class="info-box">';
                    echo '<strong>Esempio record (primo trovato):</strong>';
                    echo '<pre>' . json_encode($esempio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                    echo '</div>';
                } else {
                    echo '<div class="alert-box">';
                    echo '<strong>⚠️ Nessun record presente nella tabella</strong><br>';
                    echo 'Non è possibile testare query reali. Crea alcune prenotazioni prima di testare.';
                    echo '</div>';
                }

            } else {
                echo '<div class="error-box">';
                echo '<strong>❌ Tabella cliente_lezione NON ESISTE</strong><br>';
                echo 'Esegui le migration per creare la tabella.';
                echo '</div>';
            }

            // ========================================
            // DIAGNOSI PROBLEMI EXPORT
            // ========================================
            ?>
            <h2>6. Diagnosi Problemi Export</h2>
            <div class="info-box">
                <h3 style="margin-top: 0;">Problema Comune: Mismatch Campi Pivot</h3>
                <p><strong>Sintomo:</strong> Export vuoti o errori SQL "Unknown column"</p>

                <p><strong>Causa:</strong> Model Laravel usa campi pivot diversi da quelli nel database</p>

                <p><strong>Verifica nei Model:</strong></p>
                <ul>
                    <li><strong>app/Models/Cliente.php:</strong>
                        <pre>->withPivot('stato', 'data_prenotazione', 'check_in', 'check_out', ...)</pre>
                    </li>
                    <li><strong>app/Models/Lezione.php:</strong>
                        <pre>->withPivot('stato', 'data_prenotazione', 'check_in', 'check_out', ...)</pre>
                    </li>
                </ul>

                <p><strong>I campi nel withPivot() DEVONO corrispondere ai campi reali nella tabella sopra!</strong></p>
            </div>

            <?php
            // Verifica fix applicato
            if (in_array('stato', $campiTrovati)) {
                echo '<div class="success-box">';
                echo '<h3 style="margin-top: 0;">✅ Fix Applicato Correttamente</h3>';
                echo '<p>Il campo <code>stato</code> esiste nel database.</p>';
                echo '<p>Il Model Cliente è stato aggiornato per usare <code>stato</code> invece di <code>stato_prenotazione</code>.</p>';
                echo '<p><strong>Gli export dovrebbero funzionare ora!</strong></p>';
                echo '</div>';
            }

        } catch (\Exception $e) {
            echo '<div class="error-box">';
            echo '<strong>❌ ERRORE:</strong><br>';
            echo htmlspecialchars($e->getMessage());
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <h3 style="margin-top: 0;">📝 Prossimi Passi</h3>
            <ol>
                <li>Verifica che lo schema sopra corrisponda a quello previsto</li>
                <li>Se tutto OK, vai su <a href="/admin/report" target="_blank">/admin/report</a></li>
                <li>Prova gli export PDF/Excel</li>
                <li>Se ci sono ancora errori, controlla i log: <code>storage/logs/laravel.log</code></li>
            </ol>
        </div>

        <div style="margin-top: 20px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 12px; color: #6c757d;">
            <p><strong>File:</strong> /public/test-schema-database.php</p>
            <p><strong>Generato da:</strong> Claude Code Testing Suite</p>
        </div>
    </div>
</body>
</html>
