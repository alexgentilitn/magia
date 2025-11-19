<?php
/**
 * Diagnostica precisione BMI
 * Analizza come PhpSpreadsheet legge la cella BMI
 */

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnostica Precisione BMI</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .highlight { background: #fff3cd; font-weight: bold; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { background: #e3f2fd; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; }
        pre { background: #263238; color: #aed581; padding: 15px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔬 Diagnostica Precisione BMI</h1>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo '<p class="error">Errore upload: ' . $file['error'] . '</p>';
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();

        // Trova colonna BMI
        $highestColumn = $worksheet->getHighestColumn();
        $bmiColumn = null;

        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $header = strtolower(trim($worksheet->getCell($col . '1')->getValue() . ' ' . $worksheet->getCell($col . '2')->getValue()));
            if (stripos($header, 'bmi') !== false) {
                $bmiColumn = $col;
                break;
            }
        }

        if (!$bmiColumn) {
            echo '<p class="error">Colonna BMI non trovata</p>';
            exit;
        }

        echo '<div class="info"><strong>Colonna BMI trovata:</strong> ' . $bmiColumn . '</div>';

        // Analizza prime 10 righe
        echo '<h2>Analisi Dettagliata Celle BMI</h2>';
        echo '<table>';
        echo '<tr>';
        echo '<th>Riga</th>';
        echo '<th>getValue()</th>';
        echo '<th>getFormattedValue()</th>';
        echo '<th>Tipo</th>';
        echo '<th>Formato Excel</th>';
        echo '<th>È Data?</th>';
        echo '<th>Valore XML Raw</th>';
        echo '<th>Diff</th>';
        echo '</tr>';

        $startRow = 3;
        $maxRow = min($startRow + 9, $worksheet->getHighestRow());

        for ($row = $startRow; $row <= $maxRow; $row++) {
            $cell = $worksheet->getCell($bmiColumn . $row);

            // Tutti i metodi di lettura disponibili
            $getValue = $cell->getValue();
            $getFormattedValue = $cell->getFormattedValue();
            $getCalculatedValue = $cell->getCalculatedValue();
            $tipo = gettype($getValue);
            $formatCode = $cell->getStyle()->getNumberFormat()->getFormatCode();
            $isDate = Date::isDateTimeFormat($cell->getStyle()->getNumberFormat());

            // Prova a ottenere il valore raw dal data type
            $dataType = $cell->getDataType();

            // Calcola differenza
            $diff = '';
            if (is_numeric($getValue) && is_numeric($getFormattedValue)) {
                $diff = abs($getValue - $getFormattedValue);
                if ($getValue > 1000 && $getFormattedValue < 100) {
                    $diff = 'CONVERSIONE DA DATA';
                }
            }

            $isHighlighted = (is_numeric($getFormattedValue) && abs($getFormattedValue - 19.09) < 0.1);

            echo '<tr' . ($isHighlighted ? ' class="highlight"' : '') . '>';
            echo '<td><strong>' . $row . '</strong></td>';
            echo '<td>' . var_export($getValue, true) . '</td>';
            echo '<td>' . var_export($getFormattedValue, true) . '</td>';
            echo '<td>' . $tipo . ' / ' . $dataType . '</td>';
            echo '<td><code>' . htmlspecialchars($formatCode) . '</code></td>';
            echo '<td>' . ($isDate ? 'SÌ ❌' : 'NO ✅') . '</td>';
            echo '<td>' . var_export($getCalculatedValue, true) . '</td>';
            echo '<td>' . $diff . '</td>';
            echo '</tr>';
        }

        echo '</table>';

        // Cerca valore 19.09 o simile
        echo '<h2>🎯 Analisi Valore ~19.09</h2>';

        for ($row = 3; $row <= $worksheet->getHighestRow(); $row++) {
            $cell = $worksheet->getCell($bmiColumn . $row);
            $formatted = $cell->getFormattedValue();
            $raw = $cell->getValue();

            if (is_numeric($formatted) && abs($formatted - 19.09) < 0.5) {
                echo '<div class="info">';
                echo '<h3>Trovato alla riga ' . $row . '</h3>';

                // Tutti i metodi possibili
                echo '<table>';
                echo '<tr><th>Metodo</th><th>Valore</th><th>Tipo</th></tr>';

                $methods = [
                    'getValue()' => $cell->getValue(),
                    'getFormattedValue()' => $cell->getFormattedValue(),
                    'getCalculatedValue()' => $cell->getCalculatedValue(),
                    'getOldCalculatedValue()' => $cell->getOldCalculatedValue(),
                ];

                foreach ($methods as $method => $value) {
                    echo '<tr>';
                    echo '<td><code>' . $method . '</code></td>';
                    echo '<td><strong>' . var_export($value, true) . '</strong></td>';
                    echo '<td>' . gettype($value) . '</td>';
                    echo '</tr>';
                }

                echo '</table>';

                // Info formato
                $style = $cell->getStyle();
                $numberFormat = $style->getNumberFormat();

                echo '<h4>Informazioni Formato</h4>';
                echo '<table>';
                echo '<tr><td><strong>Codice Formato:</strong></td><td><code>' . htmlspecialchars($numberFormat->getFormatCode()) . '</code></td></tr>';
                echo '<tr><td><strong>Formato Built-in:</strong></td><td>' . $numberFormat->getBuiltInFormatCode() . '</td></tr>';
                echo '<tr><td><strong>È Formato Data?:</strong></td><td>' . (Date::isDateTimeFormat($numberFormat) ? 'SÌ' : 'NO') . '</td></tr>';
                echo '<tr><td><strong>Data Type:</strong></td><td>' . $cell->getDataType() . '</td></tr>';
                echo '</table>';

                // Prova formattazione custom
                echo '<h4>Test Formattazione Custom</h4>';
                echo '<table>';

                $customFormats = [
                    '0.00' => NumberFormat::toFormattedString($raw, '0.00'),
                    '0.000' => NumberFormat::toFormattedString($raw, '0.000'),
                    '0.0000' => NumberFormat::toFormattedString($raw, '0.0000'),
                    'General' => NumberFormat::toFormattedString($raw, NumberFormat::FORMAT_GENERAL),
                    'Number' => NumberFormat::toFormattedString($raw, NumberFormat::FORMAT_NUMBER),
                ];

                foreach ($customFormats as $format => $result) {
                    echo '<tr>';
                    echo '<td><code>' . htmlspecialchars($format) . '</code></td>';
                    echo '<td>' . htmlspecialchars($result) . '</td>';
                    echo '</tr>';
                }

                echo '</table>';

                // Se è formato data, prova a decodificare
                if (Date::isDateTimeFormat($numberFormat) && $raw > 1000) {
                    echo '<h4>⚠️ Problema Rilevato: Formato Data su Campo Numerico</h4>';
                    echo '<p>Il valore interno è un numero seriale Excel: <code>' . $raw . '</code></p>';
                    echo '<p>Excel lo visualizza come: <code>' . $formatted . '</code></p>';

                    // Prova a convertire in data per vedere cosa succede
                    try {
                        $dateValue = Date::excelToDateTimeObject($raw);
                        echo '<p>Come data: <code>' . $dateValue->format('Y-m-d H:i:s') . '</code></p>';
                    } catch (\Exception $e) {
                        echo '<p>Errore conversione data: ' . $e->getMessage() . '</p>';
                    }

                    echo '<div style="background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107;">';
                    echo '<strong>SOLUZIONE:</strong><br>';
                    echo '1. Apri il file Excel<br>';
                    echo '2. Seleziona la colonna ' . $bmiColumn . '<br>';
                    echo '3. Formato celle → Numero → 2 decimali<br>';
                    echo '4. Salva e ricarica';
                    echo '</div>';
                }

                echo '</div>';
                break;
            }
        }

    } catch (\Exception $e) {
        echo '<p class="error">Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }

} else {
    echo '<div class="info">';
    echo '<p>Carica il file Excel per analizzare come viene letto il campo BMI</p>';
    echo '</div>';

    echo '<form method="POST" enctype="multipart/form-data">';
    echo '<input type="file" name="excel_file" accept=".xlsx,.xls" required style="padding: 10px; margin: 20px 0;">';
    echo '<br>';
    echo '<button type="submit" style="padding: 10px 30px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Analizza</button>';
    echo '</form>';
}
?>

</div>
</body>
</html>
