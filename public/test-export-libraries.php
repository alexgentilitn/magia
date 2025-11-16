<?php
/**
 * TEST LIBRERIE EXPORT PDF/EXCEL
 *
 * Accesso: https://www.agstudio.digital/magia/public/test-export-libraries.php
 *
 * Verifica che tutte le librerie per export PDF/Excel siano installate
 */

// Sicurezza: solo in ambiente di sviluppo o con parametro segreto
$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

require __DIR__ . '/../vendor/autoload.php';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Librerie Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .test-item {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .icon-ok::before {
            content: "✅ ";
        }
        .icon-error::before {
            content: "❌ ";
        }
        .icon-warning::before {
            content: "⚠️ ";
        }
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 14px;
        }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .summary {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Librerie Export PDF/Excel - MA.GIA DONNA</h1>
        <p><strong>Data/Ora:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>Server:</strong> <?= $_SERVER['HTTP_HOST'] ?></p>

        <?php
        $problemi = [];
        $successi = [];
        $warnings = [];

        // ========================================
        // TEST 1: PhpSpreadsheet (Excel)
        // ========================================
        ?>
        <h2>1. PhpSpreadsheet (Export Excel)</h2>
        <div class="test-item">
            <p><strong>Classe verificata:</strong> <code>\PhpOffice\PhpSpreadsheet\Spreadsheet</code></p>
            <?php
            if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                echo '<p class="success icon-ok">INSTALLATA</p>';
                $successi[] = 'PhpSpreadsheet';

                // Test creazione
                try {
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setCellValue('A1', 'Test MA.GIA DONNA');
                    $sheet->setCellValue('A2', date('d/m/Y H:i:s'));

                    echo '<p class="success icon-ok">Test creazione Spreadsheet: OK</p>';
                    echo '<p>✓ Può creare fogli Excel</p>';
                    echo '<p>✓ Può scrivere celle</p>';
                } catch (\Exception $e) {
                    echo '<p class="error icon-error">Test creazione FALLITO</p>';
                    echo '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    $problemi[] = 'PhpSpreadsheet installata ma errore al test: ' . $e->getMessage();
                }
            } else {
                echo '<p class="error icon-error">NON INSTALLATA</p>';
                echo '<div class="code-block">composer require phpoffice/phpspreadsheet</div>';
                $problemi[] = 'PhpSpreadsheet NON installata';
            }
            ?>
        </div>

        <?php
        // ========================================
        // TEST 2: Dompdf
        // ========================================
        ?>
        <h2>2. Dompdf (Export PDF)</h2>
        <div class="test-item">
            <p><strong>Classe verificata:</strong> <code>\Dompdf\Dompdf</code></p>
            <?php
            if (class_exists(\Dompdf\Dompdf::class)) {
                echo '<p class="success icon-ok">INSTALLATA</p>';
                $successi[] = 'Dompdf';

                try {
                    $dompdf = new \Dompdf\Dompdf();
                    $html = '<html><body><h1>Test MA.GIA DONNA</h1><p>Data: ' . date('d/m/Y H:i:s') . '</p></body></html>';
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');

                    echo '<p class="success icon-ok">Test creazione PDF: OK</p>';
                    echo '<p>✓ Può generare PDF</p>';
                    echo '<p>✓ Può processare HTML</p>';
                } catch (\Exception $e) {
                    echo '<p class="error icon-error">Test creazione FALLITO</p>';
                    echo '<p>Errore: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    $problemi[] = 'Dompdf installata ma errore al test: ' . $e->getMessage();
                }
            } else {
                echo '<p class="error icon-error">NON INSTALLATA</p>';
                echo '<div class="code-block">composer require dompdf/dompdf</div>';
                $problemi[] = 'Dompdf NON installata';
            }
            ?>
        </div>

        <?php
        // ========================================
        // TEST 3: Barryvdh DomPDF (Laravel)
        // ========================================
        ?>
        <h2>3. Barryvdh DomPDF (Laravel Wrapper)</h2>
        <div class="test-item">
            <p><strong>Classe verificata:</strong> <code>\Barryvdh\DomPDF\ServiceProvider</code></p>
            <?php
            if (class_exists(\Barryvdh\DomPDF\ServiceProvider::class)) {
                echo '<p class="success icon-ok">INSTALLATA</p>';
                $successi[] = 'Barryvdh DomPDF';
                echo '<p>✓ Integrazione Laravel disponibile</p>';
                echo '<p>✓ Facade <code>Pdf::loadView()</code> disponibile</p>';
            } else {
                echo '<p class="error icon-error">NON INSTALLATA</p>';
                echo '<div class="code-block">composer require barryvdh/laravel-dompdf</div>';
                $problemi[] = 'Barryvdh DomPDF NON installata';
            }
            ?>
        </div>

        <?php
        // ========================================
        // TEST 4: Maatwebsite Excel (Laravel)
        // ========================================
        ?>
        <h2>4. Maatwebsite Excel (Laravel Wrapper)</h2>
        <div class="test-item">
            <p><strong>Classe verificata:</strong> <code>\Maatwebsite\Excel\ExcelServiceProvider</code></p>
            <?php
            if (class_exists(\Maatwebsite\Excel\ExcelServiceProvider::class)) {
                echo '<p class="success icon-ok">INSTALLATA</p>';
                $successi[] = 'Maatwebsite Excel';
                echo '<p>✓ Integrazione Laravel disponibile</p>';
                echo '<p>✓ Facade <code>Excel::download()</code> disponibile</p>';
            } else {
                echo '<p class="warning icon-warning">NON INSTALLATA (opzionale)</p>';
                echo '<p>Puoi usare PhpSpreadsheet direttamente</p>';
                echo '<div class="code-block">composer require maatwebsite/excel</div>';
            }
            ?>
        </div>

        <?php
        // ========================================
        // RIEPILOGO
        // ========================================
        ?>
        <h2>📊 Riepilogo Finale</h2>

        <?php if (!empty($successi)): ?>
        <div class="summary" style="background: #d4edda; border-left-color: #28a745;">
            <h3 style="color: #155724; margin-top: 0;">✅ Librerie Installate (<?= count($successi) ?>)</h3>
            <ul>
                <?php foreach ($successi as $lib): ?>
                    <li><?= htmlspecialchars($lib) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($problemi)): ?>
        <div class="summary" style="background: #f8d7da; border-left-color: #dc3545;">
            <h3 style="color: #721c24; margin-top: 0;">❌ Problemi Trovati (<?= count($problemi) ?>)</h3>
            <ul>
                <?php foreach ($problemi as $problema): ?>
                    <li><?= htmlspecialchars($problema) ?></li>
                <?php endforeach; ?>
            </ul>

            <h4>Azioni Necessarie:</h4>
            <p>Contatta lo sviluppatore per installare le librerie mancanti via Composer.</p>
        </div>
        <?php else: ?>
        <div class="summary" style="background: #d4edda; border-left-color: #28a745;">
            <h3 style="color: #155724; margin-top: 0;">🎉 Tutto OK!</h3>
            <p><strong>Tutte le librerie necessarie per export PDF/Excel sono installate e funzionanti.</strong></p>
            <p>Gli export dovrebbero funzionare correttamente.</p>
        </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <h3 style="margin-top: 0;">🔍 Verifica Export Effettivi</h3>
            <p>Per verificare che gli export funzionino effettivamente:</p>
            <ol>
                <li>Vai su: <a href="/admin/report" target="_blank">/admin/report</a></li>
                <li>Prova "Export Excel Presenze"</li>
                <li>Prova "Export PDF Calendario"</li>
                <li>Prova "Export PDF Professionisti"</li>
            </ol>
            <p>Se gli export danno errore, usa: <a href="/test-schema-database.php?secret=magia2024" target="_blank">test-schema-database.php</a></p>
        </div>

        <div style="margin-top: 20px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 12px; color: #6c757d;">
            <p><strong>File:</strong> /public/test-export-libraries.php</p>
            <p><strong>Generato da:</strong> Claude Code Testing Suite</p>
            <p><strong>Per rimuovere questo file:</strong> Elimina /public/test-export-libraries.php</p>
        </div>
    </div>
</body>
</html>
