<?php
/**
 * Test lettura BMI da file Excel con Upload
 */

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Lettura BMI da Excel</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
        .upload-form { background: #e8f5e9; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .upload-form input[type="file"] { padding: 10px; margin: 10px 0; }
        .upload-form button { background: #4CAF50; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .upload-form button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
        .alert { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .danger { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; }
        .highlight { background: #ffeb3b; padding: 2px 5px; }
        pre { background: #263238; color: #aed581; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 Test Lettura BMI da Excel</h1>

    <div class="upload-form">
        <h3>📤 Carica il tuo file Excel</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="excel_file" accept=".xlsx,.xls" required>
            <br>
            <button type="submit">🔍 Analizza File</button>
        </form>
    </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="danger">❌ Errore upload file: ' . $file['error'] . '</div>';
        exit;
    }

    $tmpPath = $file['tmp_name'];

    try {
        echo '<div class="info">✅ File caricato: <strong>' . htmlspecialchars($file['name']) . '</strong> (' . round($file['size']/1024, 2) . ' KB)</div>';

        $spreadsheet = IOFactory::load($tmpPath);
        $worksheet = $spreadsheet->getActiveSheet();

        echo '<h2>📊 Informazioni Foglio</h2>';
        echo '<p><strong>Nome:</strong> ' . htmlspecialchars($worksheet->getTitle()) . '</p>';
        echo '<p><strong>Righe totali:</strong> ' . $worksheet->getHighestRow() . '</p>';
        echo '<p><strong>Colonne:</strong> A-' . $worksheet->getHighestColumn() . '</p>';

        // Leggi intestazioni
        echo '<h2>📋 Intestazioni (Riga 1-2)</h2>';
        echo '<table>';
        echo '<tr><th>Colonna</th><th>Riga 1</th><th>Riga 2</th><th>Tipo Formato</th></tr>';

        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $colIndex = 'A';
        $bmiColumn = null;

        while ($colIndex <= $highestColumn) {
            $header1 = $worksheet->getCell($colIndex . '1')->getValue();
            $header2 = $worksheet->getCell($colIndex . '2')->getValue();

            // Trova colonna BMI
            $headerLower = strtolower(trim($header1 . ' ' . $header2));
            if (stripos($headerLower, 'bmi') !== false) {
                $bmiColumn = $colIndex;
            }

            // Tipo formato della cella
            $cell = $worksheet->getCell($colIndex . '3');
            $formatCode = $cell->getStyle()->getNumberFormat()->getFormatCode();

            echo '<tr>';
            echo '<td><strong>' . $colIndex . '</strong></td>';
            echo '<td>' . htmlspecialchars($header1 ?: '(vuoto)') . '</td>';
            echo '<td>' . htmlspecialchars($header2 ?: '(vuoto)') . '</td>';
            echo '<td><code>' . htmlspecialchars($formatCode) . '</code></td>';
            echo '</tr>';

            if (!empty($header1) || !empty($header2)) {
                $headers[$colIndex] = trim($header1 ?: $header2);
            }

            $colIndex++;
        }
        echo '</table>';

        // Trova colonna BMI
        if ($bmiColumn) {
            echo '<div class="info">✅ <strong>Colonna BMI rilevata automaticamente: ' . $bmiColumn . '</strong></div>';
        } else {
            echo '<div class="alert">⚠️ Colonna BMI non rilevata automaticamente. Specifica manualmente:</div>';
            echo '<form method="GET">';
            echo '<input type="text" name="bmi_col" placeholder="Es: D" style="padding: 8px; margin: 10px 0;">';
            echo '<button type="submit" style="padding: 8px 20px;">Usa questa colonna</button>';
            echo '</form>';

            if (isset($_GET['bmi_col'])) {
                $bmiColumn = strtoupper($_GET['bmi_col']);
                echo '<div class="info">👉 Usando colonna: <strong>' . htmlspecialchars($bmiColumn) . '</strong></div>';
            }
        }

        if ($bmiColumn) {
            // Test lettura valori BMI
            echo '<h2>🔍 Analisi Dettagliata Colonna BMI (' . $bmiColumn . ')</h2>';

            // Helper functions
            function pulisciNumero($valore) {
                if (is_null($valore) || $valore === '') {
                    return null;
                }
                $valore = trim(strval($valore));
                $valore = str_replace('%', '', $valore);
                $numero = str_replace([',', ' '], ['.', ''], $valore);
                return is_numeric($numero) ? (float) $numero : null;
            }

            function validaBMI($valore) {
                if (is_null($valore)) {
                    return null;
                }
                if ($valore < 10 || $valore > 100) {
                    return null;
                }
                return $valore;
            }

            // Controlla formato colonna
            $testCell = $worksheet->getCell($bmiColumn . '3');
            $formatCode = $testCell->getStyle()->getNumberFormat()->getFormatCode();

            echo '<div class="info">';
            echo '<strong>Formato Excel della colonna:</strong> <code>' . htmlspecialchars($formatCode) . '</code><br>';

            $isDateFormat = Date::isDateTimeFormat($testCell->getStyle()->getNumberFormat());
            if ($isDateFormat) {
                echo '<span class="error">❌ PROBLEMA: Excel ha impostato questa colonna come FORMATO DATA!</span><br>';
                echo 'Questo causa la perdita dei valori originali. Devi correggere il formato in Excel.';
            } else {
                echo '<span class="success">✅ OK: Colonna formattata come numero</span>';
            }
            echo '</div>';

            echo '<table>';
            echo '<tr>';
            echo '<th>Riga</th>';
            echo '<th>getValue()</th>';
            echo '<th>getFormattedValue()</th>';
            echo '<th>Tipo PHP</th>';
            echo '<th>Formato Excel</th>';
            echo '<th>È Data?</th>';
            echo '<th>Dopo pulizia</th>';
            echo '<th>Dopo validazione</th>';
            echo '<th>Status</th>';
            echo '</tr>';

            $startRow = 3;
            $maxRow = min($startRow + 19, $worksheet->getHighestRow());

            for ($row = $startRow; $row <= $maxRow; $row++) {
                $cell = $worksheet->getCell($bmiColumn . $row);
                $rawValue = $cell->getValue();
                $formattedValue = $cell->getFormattedValue();
                $tipo = gettype($rawValue);
                $formatCode = $cell->getStyle()->getNumberFormat()->getFormatCode();
                $isDate = Date::isDateTimeFormat($cell->getStyle()->getNumberFormat());

                $pulito = pulisciNumero($rawValue);
                $validato = validaBMI($pulito);

                // Prova anche con formatted value
                $pulitoFormatted = pulisciNumero($formattedValue);
                $validatoFormatted = validaBMI($pulitoFormatted);

                echo '<tr>';
                echo '<td><strong>' . $row . '</strong></td>';
                echo '<td>' . var_export($rawValue, true) . '</td>';
                echo '<td>' . var_export($formattedValue, true) . '</td>';
                echo '<td>' . $tipo . '</td>';
                echo '<td><code>' . htmlspecialchars($formatCode) . '</code></td>';
                echo '<td>' . ($isDate ? '<span class="error">SÌ ❌</span>' : '<span class="success">NO ✅</span>') . '</td>';
                echo '<td>' . var_export($pulito, true) . '</td>';
                echo '<td>' . var_export($validato, true) . '</td>';

                if (!is_null($validato)) {
                    echo '<td class="success">✅ OK</td>';
                } else if ($rawValue > 1000 && !is_null($validatoFormatted)) {
                    echo '<td class="warning">⚠️ FORMATO DATA (userebbe: ' . $validatoFormatted . ')</td>';
                } else {
                    echo '<td class="error">❌ NULL</td>';
                }
                echo '</tr>';
            }
            echo '</table>';

            // Test specifico valore 19.09
            echo '<h2>🎯 Ricerca Valore Specifico "19.09"</h2>';
            $found = false;

            for ($row = 3; $row <= $worksheet->getHighestRow(); $row++) {
                $cell = $worksheet->getCell($bmiColumn . $row);
                $rawValue = $cell->getValue();
                $formattedValue = $cell->getFormattedValue();
                $pulito = pulisciNumero($rawValue);
                $pulitoFormatted = pulisciNumero($formattedValue);

                if ($pulito == 19.09 || $pulitoFormatted == 19.09 || abs($pulito - 19.09) < 0.01) {
                    $found = true;
                    echo '<div class="info">';
                    echo '<strong>✅ Trovato alla riga ' . $row . '</strong><br>';
                    echo '<strong>getValue():</strong> ' . var_export($rawValue, true) . '<br>';
                    echo '<strong>getFormattedValue():</strong> ' . var_export($formattedValue, true) . '<br>';
                    echo '<strong>Tipo:</strong> ' . gettype($rawValue) . '<br>';
                    echo '<strong>Formato Excel:</strong> <code>' . htmlspecialchars($cell->getStyle()->getNumberFormat()->getFormatCode()) . '</code><br>';
                    echo '<strong>È formato Data?:</strong> ' . (Date::isDateTimeFormat($cell->getStyle()->getNumberFormat()) ? 'SÌ ❌' : 'NO ✅') . '<br>';
                    echo '<strong>Dopo pulisciNumero(getValue()):</strong> ' . var_export($pulito, true) . '<br>';
                    echo '<strong>Dopo pulisciNumero(getFormattedValue()):</strong> ' . var_export($pulitoFormatted, true) . '<br>';
                    echo '<strong>Dopo validaBMI():</strong> ' . var_export(validaBMI($pulito), true) . '<br>';

                    if ($rawValue > 1000) {
                        echo '<br><span class="error">❌ PROBLEMA: Il valore interno è ' . $rawValue . ' (numero seriale di data Excel)</span><br>';
                        echo '<span class="warning">⚠️ Excel ha formattato questa cella come DATA invece di NUMERO</span><br>';
                        echo '<span class="info">💡 SOLUZIONE: Devi correggere il formato della colonna in Excel prima di importare</span>';
                    } else {
                        echo '<br><span class="success">✅ Valore corretto!</span>';
                    }
                    echo '</div>';
                }
            }

            if (!$found) {
                echo '<div class="alert">⚠️ Valore 19.09 non trovato nel file</div>';
            }

            // Conclusione e raccomandazioni
            echo '<h2>📝 Conclusione e Raccomandazioni</h2>';

            $hasDateFormat = Date::isDateTimeFormat($testCell->getStyle()->getNumberFormat());

            if ($hasDateFormat) {
                echo '<div class="danger">';
                echo '<h3>❌ PROBLEMA IDENTIFICATO: Formato Data su Colonna Numerica</h3>';
                echo '<p><strong>La colonna BMI è formattata come DATA in Excel.</strong></p>';
                echo '<p>Questo causa:</p>';
                echo '<ul>';
                echo '<li>❌ Perdita del valore originale (19.09 diventa 45974.797...)</li>';
                echo '<li>❌ Impossibilità di recuperare il valore preciso</li>';
                echo '<li>❌ Arrotondamenti errati (19.09 → 19.11)</li>';
                echo '</ul>';
                echo '<p><strong>SOLUZIONE OBBLIGATORIA:</strong></p>';
                echo '<ol>';
                echo '<li>Apri il file Excel</li>';
                echo '<li>Seleziona la colonna BMI (colonna ' . $bmiColumn . ')</li>';
                echo '<li>Tasto destro → "Formato celle" (o Ctrl+1)</li>';
                echo '<li>Categoria: <strong>"Numero"</strong> (NON "Data")</li>';
                echo '<li>Posizioni decimali: <strong>2</strong></li>';
                echo '<li>Clicca OK</li>';
                echo '<li><strong>IMPORTANTE:</strong> Se vedi valori strani (45974...), significa che i dati sono già corrotti. Devi ripristinare i valori originali o ridigirarli.</li>';
                echo '<li>Salva il file</li>';
                echo '<li>Ricaricalo qui per verificare</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="info">';
                echo '<p><strong>✅ Il formato della colonna BMI è corretto (Numero).</strong></p>';
                echo '<p>Puoi procedere con l\'import normalmente.</p>';
                echo '</div>';
            }
        }

    } catch (\Exception $e) {
        echo '<div class="danger">';
        echo '<h3>❌ Errore durante l\'analisi</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
}
?>

</div>
</body>
</html>
