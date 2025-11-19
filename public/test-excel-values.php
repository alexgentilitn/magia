<?php
/**
 * DIAGNOSTICA LETTURA EXCEL - Mostra differenza tra getValue e getFormattedValue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>📊 Diagnostica Lettura Valori Excel</h1>";
echo "<style>
body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
h1 { color: #4ec9b0; }
h2 { color: #569cd6; margin-top: 20px; }
.success { color: #4ec9b0; font-weight: bold; }
.error { color: #f48771; font-weight: bold; }
.warning { color: #dcdcaa; font-weight: bold; }
.box { background: #2d2d2d; padding: 15px; margin: 10px 0; border-left: 3px solid #569cd6; }
table { border-collapse: collapse; margin: 10px 0; width: 100%; }
td, th { padding: 8px; border: 1px solid #3e3e3e; text-align: left; }
th { background: #264f78; }
.diff { background: #4a3a1a; }
</style>";

// Verifica upload file
if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    ?>
    <div class='box'>
        <h2>📤 Upload File Excel</h2>
        <form method="POST" enctype="multipart/form-data">
            <p>Carica il file Excel per analizzare i valori:</p>
            <input type="file" name="excel_file" accept=".xlsx,.xls" required style="padding: 10px; margin: 10px 0;">
            <br>
            <button type="submit" style="background: #569cd6; color: white; border: none; padding: 10px 20px; cursor: pointer; margin-top: 10px;">
                Analizza File
            </button>
        </form>
    </div>
    <?php
    exit;
}

// Processa file Excel
require_once __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $tmpFile = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($tmpFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();

    echo "<div class='box'>";
    echo "<h2>📋 Informazioni File</h2>";
    echo "<strong>Nome file:</strong> " . $_FILES['excel_file']['name'] . "<br>";
    echo "<strong>Righe:</strong> $highestRow<br>";
    echo "<strong>Colonne:</strong> $highestColumn<br>";
    echo "</div>";

    // Mostra prime 20 righe con confronto getValue vs getFormattedValue
    echo "<div class='box'>";
    echo "<h2>🔍 Confronto getValue() vs getFormattedValue()</h2>";
    echo "<p><span class='warning'>⚠️ Le celle evidenziate mostrano valori diversi</span></p>";

    $startRow = 3; // Salta header
    $maxRows = min($startRow + 19, $highestRow); // Max 20 righe

    echo "<table>";
    echo "<tr>";
    echo "<th>Riga</th>";
    echo "<th>Colonna</th>";
    echo "<th>getValue()<br><small>(valore reale)</small></th>";
    echo "<th>getFormattedValue()<br><small>(valore visuale)</small></th>";
    echo "<th>Tipo</th>";
    echo "<th>Differenza?</th>";
    echo "</tr>";

    $columns = range('A', $highestColumn);
    $differenze = 0;

    for ($row = $startRow; $row <= $maxRows; $row++) {
        foreach ($columns as $col) {
            $cell = $worksheet->getCell($col . $row);
            $value = $cell->getValue();
            $formatted = $cell->getFormattedValue();
            $dataType = $cell->getDataType();

            // Skip celle vuote
            if ($value === null || $value === '') continue;

            // Skip testo uguale
            if (!is_numeric($value)) continue;

            $isDifferent = false;

            // Confronta valori numerici
            if (is_numeric($value) && is_numeric($formatted)) {
                $diff = abs((float)$value - (float)$formatted);
                if ($diff > 0.001) { // Soglia per considerare differenza
                    $isDifferent = true;
                    $differenze++;
                }
            } elseif ((string)$value !== (string)$formatted) {
                $isDifferent = true;
                $differenze++;
            }

            $rowClass = $isDifferent ? " class='diff'" : "";

            echo "<tr$rowClass>";
            echo "<td>$row</td>";
            echo "<td>$col</td>";
            echo "<td>" . var_export($value, true) . "</td>";
            echo "<td>" . var_export($formatted, true) . "</td>";
            echo "<td>$dataType</td>";
            echo "<td>" . ($isDifferent ? "<span class='error'>⚠️ DIVERSO</span>" : "<span class='success'>✅</span>") . "</td>";
            echo "</tr>";
        }
    }

    echo "</table>";

    echo "<p><strong>Totale celle con differenze:</strong> <span class='" . ($differenze > 0 ? "error" : "success") . "'>$differenze</span></p>";

    echo "</div>";

    // Esempio specifico BMI
    echo "<div class='box'>";
    echo "<h2>🎯 Esempio Specifico: Colonna BMI</h2>";
    echo "<p>Inserisci la colonna Excel del BMI (es: E) per vedere i valori reali vs formattati:</p>";

    if (isset($_POST['bmi_column'])) {
        $bmiCol = strtoupper($_POST['bmi_column']);

        echo "<table>";
        echo "<tr><th>Riga</th><th>getValue()</th><th>getFormattedValue()</th><th>Differenza</th></tr>";

        for ($row = $startRow; $row <= min($startRow + 10, $highestRow); $row++) {
            $cell = $worksheet->getCell($bmiCol . $row);
            $value = $cell->getValue();
            $formatted = $cell->getFormattedValue();

            if ($value === null || $value === '') continue;

            $diff = is_numeric($value) && is_numeric($formatted)
                ? abs((float)$value - (float)$formatted)
                : 0;

            echo "<tr" . ($diff > 0.001 ? " class='diff'" : "") . ">";
            echo "<td>$row</td>";
            echo "<td>" . number_format((float)$value, 10) . "</td>";
            echo "<td>$formatted</td>";
            echo "<td>" . number_format($diff, 10) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<form method='POST' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='excel_file' value='1'>";
        echo "<input type='text' name='bmi_column' placeholder='Es: E' style='padding: 8px; margin: 10px 0;' required>";
        echo "<button type='submit' style='background: #569cd6; color: white; border: none; padding: 8px 16px; cursor: pointer;'>Analizza Colonna</button>";
        echo "</form>";
    }

    echo "</div>";

    // Raccomandazioni
    echo "<div class='box' style='border-left-color: " . ($differenze > 0 ? "#f48771" : "#4ec9b0") . ";'>";
    echo "<h2>💡 Raccomandazioni</h2>";

    if ($differenze > 0) {
        echo "<p><span class='error'>⚠️ PROBLEMA RILEVATO</span></p>";
        echo "<ul>";
        echo "<li><strong>getValue()</strong> restituisce il valore REALE della cella (più preciso)</li>";
        echo "<li><strong>getFormattedValue()</strong> restituisce il valore VISUALE (può essere arrotondato)</li>";
        echo "</ul>";
        echo "<p><strong>Soluzione:</strong> Il controller deve usare <code>getValue()</code> invece di <code>getFormattedValue()</code></p>";
    } else {
        echo "<p><span class='success'>✅ Nessuna differenza significativa trovata</span></p>";
        echo "<p>I valori formattati corrispondono ai valori reali.</p>";
    }

    echo "</div>";

} catch (\Exception $e) {
    echo "<div class='box'>";
    echo "<span class='error'>❌ ERRORE:</span> " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<br><br>";
echo "<a href='test-excel-values.php' style='color: #569cd6;'>🔄 Analizza Altro File</a>";
?>
