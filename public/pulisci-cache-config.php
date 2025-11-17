<?php
/**
 * PULISCI CACHE CONFIGURAZIONE LARAVEL
 *
 * URL: https://www.agstudio.digital/magia/public/pulisci-cache-config.php?secret=magia2024
 *
 * Dopo aver aggiunto config/dompdf.php, Laravel potrebbe aver cachato
 * la vecchia configurazione. Questo script pulisce la cache.
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulisci Cache Configurazione - MA.GIA DONNA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 25px;
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
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
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
        .code {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #764ba2;
        }
        .step {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧹 Pulisci Cache Configurazione Laravel</h1>

        <div class="info">
            <strong>🎯 Scopo:</strong> Questo script pulisce la cache di configurazione Laravel dopo aver aggiunto config/dompdf.php
        </div>

        <?php
        $operazioni = [];
        $errori = [];

        // 1. Verifica che config/dompdf.php esista
        echo '<div class="step">';
        echo '<h3>Step 1: Verifica config/dompdf.php</h3>';

        $configPath = base_path('config/dompdf.php');
        if (file_exists($configPath)) {
            echo '<div class="success">✅ File config/dompdf.php trovato!</div>';
            echo '<div class="code">Path: ' . $configPath . '</div>';
            $operazioni[] = 'Config DomPDF presente';
        } else {
            echo '<div class="error">❌ File config/dompdf.php NON trovato!</div>';
            echo '<div class="warning">Il deploy potrebbe non essere ancora completato. Attendi 2-5 minuti e riprova.</div>';
            $errori[] = 'Config DomPDF mancante';
        }
        echo '</div>';

        // 2. Verifica cartella storage/fonts
        echo '<div class="step">';
        echo '<h3>Step 2: Verifica storage/fonts/</h3>';

        $fontsPath = storage_path('fonts');
        if (file_exists($fontsPath)) {
            $isWritable = is_writable($fontsPath);
            if ($isWritable) {
                echo '<div class="success">✅ Cartella storage/fonts/ trovata e scrivibile!</div>';
                $operazioni[] = 'Storage fonts OK';
            } else {
                echo '<div class="error">❌ Cartella storage/fonts/ esiste ma NON è scrivibile!</div>';
                echo '<div class="warning">Permessi correnti: ' . substr(sprintf('%o', fileperms($fontsPath)), -4) . '</div>';
                echo '<div class="info">Servono permessi 755 o 775</div>';
                $errori[] = 'Permessi storage/fonts non corretti';
            }
        } else {
            echo '<div class="error">❌ Cartella storage/fonts/ NON trovata!</div>';
            echo '<div class="info">Tentativo di creazione...</div>';

            if (@mkdir($fontsPath, 0755, true)) {
                echo '<div class="success">✅ Cartella storage/fonts/ creata con successo!</div>';
                $operazioni[] = 'Storage fonts creato';
            } else {
                echo '<div class="error">❌ Impossibile creare storage/fonts/</div>';
                $errori[] = 'Impossibile creare storage/fonts';
            }
        }
        echo '</div>';

        // 3. Pulisci cache configurazione
        echo '<div class="step">';
        echo '<h3>Step 3: Pulisci Cache Configurazione</h3>';

        try {
            $cachePath = base_path('bootstrap/cache/config.php');

            if (file_exists($cachePath)) {
                echo '<div class="info">📄 File cache trovato: bootstrap/cache/config.php</div>';

                if (@unlink($cachePath)) {
                    echo '<div class="success">✅ Cache configurazione eliminata con successo!</div>';
                    $operazioni[] = 'Cache config eliminata';
                } else {
                    echo '<div class="error">❌ Impossibile eliminare cache configurazione</div>';
                    echo '<div class="warning">Potresti dover eliminare manualmente via FTP: bootstrap/cache/config.php</div>';
                    $errori[] = 'Impossibile eliminare cache';
                }
            } else {
                echo '<div class="success">✅ Nessuna cache configurazione presente (OK)</div>';
                $operazioni[] = 'Nessuna cache da eliminare';
            }

            // Pulisci anche cache view (potrebbe contenere vecchi PDF)
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            echo '<div class="success">✅ Cache view pulita!</div>';
            $operazioni[] = 'Cache view pulita';

        } catch (\Exception $e) {
            echo '<div class="error">❌ Errore durante pulizia cache: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $errori[] = 'Errore pulizia cache: ' . $e->getMessage();
        }
        echo '</div>';

        // 4. Verifica binding DomPDF
        echo '<div class="step">';
        echo '<h3>Step 4: Verifica Binding DomPDF nel Container</h3>';

        try {
            // Prova a risolvere il binding dompdf.wrapper
            $app = app();

            if ($app->bound('dompdf.wrapper')) {
                echo '<div class="success">✅ Binding "dompdf.wrapper" TROVATO nel container!</div>';
                $operazioni[] = 'Binding DomPDF OK';

                // Prova a creare un'istanza
                try {
                    $pdf = app('dompdf.wrapper');
                    echo '<div class="success">✅ Istanza DomPDF creata con successo!</div>';
                    echo '<div class="code">Classe: ' . get_class($pdf) . '</div>';
                    $operazioni[] = 'Istanza PDF creata';
                } catch (\Exception $e) {
                    echo '<div class="error">❌ Errore creazione istanza: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    $errori[] = 'Errore istanza PDF';
                }
            } else {
                echo '<div class="error">❌ Binding "dompdf.wrapper" NON trovato nel container!</div>';
                echo '<div class="warning">Il service provider potrebbe non essere caricato correttamente.</div>';
                $errori[] = 'Binding DomPDF mancante';
            }
        } catch (\Exception $e) {
            echo '<div class="error">❌ Errore verifica binding: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $errori[] = 'Errore verifica binding';
        }
        echo '</div>';

        // 5. Test Facade Pdf
        echo '<div class="step">';
        echo '<h3>Step 5: Test Facade Pdf</h3>';

        try {
            $pdfClass = \Barryvdh\DomPDF\Facade\Pdf::class;
            echo '<div class="info">Facade class: ' . $pdfClass . '</div>';

            // Prova a usare la facade
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1>');
            echo '<div class="success">✅ Facade Pdf funziona correttamente!</div>';
            echo '<div class="code">PDF generato: ' . get_class($pdf) . '</div>';
            $operazioni[] = 'Facade Pdf funzionante';

        } catch (\Exception $e) {
            echo '<div class="error">❌ Errore test facade: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="code">' . htmlspecialchars($e->getTraceAsString()) . '</div>';
            $errori[] = 'Errore test facade: ' . $e->getMessage();
        }
        echo '</div>';

        // Riepilogo finale
        echo '<hr style="margin: 30px 0;">';
        echo '<h2>📊 Riepilogo Operazioni</h2>';

        if (empty($errori)) {
            echo '<div class="success">';
            echo '<h3>✅ TUTTO OK! Export PDF dovrebbero funzionare!</h3>';
            echo '<p><strong>Operazioni completate:</strong></p>';
            echo '<ul>';
            foreach ($operazioni as $op) {
                echo '<li>' . htmlspecialchars($op) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '<div class="info">';
            echo '<h4>🧪 Prossimi passi:</h4>';
            echo '<ol>';
            echo '<li>Vai su <strong>/admin/report</strong></li>';
            echo '<li>Prova a generare un <strong>export PDF</strong></li>';
            echo '<li>Se funziona: ✅ Problema risolto!</li>';
            echo '<li>Se ancora errore 500: torna su vedi-errori-laravel.php per vedere il nuovo errore</li>';
            echo '</ol>';
            echo '</div>';

        } else {
            echo '<div class="error">';
            echo '<h3>⚠️ Alcuni problemi rilevati</h3>';
            echo '<p><strong>Errori:</strong></p>';
            echo '<ul>';
            foreach ($errori as $err) {
                echo '<li>' . htmlspecialchars($err) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '<div class="info">';
            echo '<p><strong>Operazioni completate:</strong></p>';
            echo '<ul>';
            foreach ($operazioni as $op) {
                echo '<li>' . htmlspecialchars($op) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '<div class="warning">';
            echo '<h4>🔧 Cosa fare:</h4>';
            echo '<ol>';
            echo '<li>Se config/dompdf.php manca: attendi che il deploy completi</li>';
            echo '<li>Ricarica questa pagina tra 2-3 minuti</li>';
            echo '<li>Se storage/fonts ha problemi di permessi: contatta supporto hosting</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>

        <hr style="margin: 30px 0;">

        <div style="text-align: center;">
            <a href="?secret=magia2024" class="btn">🔄 Ricarica Pagina</a>
            <a href="/admin/report" class="btn">📊 Vai ai Report</a>
            <a href="vedi-errori-laravel.php?secret=magia2024" class="btn">📋 Vedi Errori Laravel</a>
            <a href="diagnosi-completa-export.php?secret=magia2024" class="btn">🔍 Diagnosi Export</a>
        </div>

        <div class="info" style="margin-top: 30px; font-size: 0.9em;">
            <strong>File:</strong> /public/pulisci-cache-config.php<br>
            <strong>Data:</strong> <?= date('d/m/Y H:i:s') ?><br>
            <strong>Ambiente:</strong> <?= app()->environment() ?>
        </div>
    </div>
</body>
</html>
