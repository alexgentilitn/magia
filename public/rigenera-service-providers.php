<?php
/**
 * RIGENERA SERVICE PROVIDERS LARAVEL
 *
 * URL: https://www.agstudio.digital/magia/public/rigenera-service-providers.php?secret=magia2024
 *
 * Elimina tutte le cache di Laravel che impediscono il caricamento
 * dei service provider auto-discovered (come DomPDF)
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'magia2024') {
    die('Accesso negato. Usa: ?secret=magia2024');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rigenera Service Providers - MA.GIA DONNA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
            border-bottom: 3px solid #f5576c;
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
            font-size: 0.9em;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #f5576c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 10px 0;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #f093fb;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #f5576c;
            margin: 20px 0;
        }
        .file-item {
            background: white;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Rigenera Service Providers Laravel</h1>

        <div class="warning">
            <strong>⚠️ IMPORTANTE:</strong> Questo script elimina TUTTE le cache di Laravel per forzare
            la rigenerazione dei service provider auto-discovered.
        </div>

        <?php
        $basePath = realpath(__DIR__ . '/..');
        $cacheFiles = [
            'bootstrap/cache/packages.php' => 'Cache Package Discovery',
            'bootstrap/cache/services.php' => 'Cache Service Providers',
            'bootstrap/cache/config.php' => 'Cache Configurazione',
            'bootstrap/cache/routes-v7.php' => 'Cache Routes',
            'bootstrap/cache/events.php' => 'Cache Events',
        ];

        $operazioni = [];
        $errori = [];

        // Step 1: Elimina file cache
        echo '<div class="step">';
        echo '<h3>Step 1: Elimina File di Cache</h3>';

        foreach ($cacheFiles as $file => $description) {
            $fullPath = $basePath . '/' . $file;

            echo '<div class="file-item">';
            echo '<strong>' . htmlspecialchars($description) . ':</strong><br>';
            echo htmlspecialchars($file);

            if (file_exists($fullPath)) {
                echo '<br>📄 Trovato, eliminazione in corso...';

                if (@unlink($fullPath)) {
                    echo '<br><span style="color: #28a745;">✅ Eliminato con successo!</span>';
                    $operazioni[] = "Eliminato: $description";
                } else {
                    echo '<br><span style="color: #dc3545;">❌ Errore durante eliminazione</span>';
                    $errori[] = "Impossibile eliminare: $description";
                }
            } else {
                echo '<br><span style="color: #6c757d;">ℹ️ Non presente (OK)</span>';
                $operazioni[] = "Non presente: $description";
            }

            echo '</div>';
        }
        echo '</div>';

        // Step 2: Verifica directory storage scrivibili
        echo '<div class="step">';
        echo '<h3>Step 2: Verifica Directory Storage</h3>';

        $storageDirectories = [
            'storage/framework/cache' => 'Cache Framework',
            'storage/framework/views' => 'View Compilate',
            'storage/logs' => 'Log',
            'storage/fonts' => 'Font PDF',
        ];

        foreach ($storageDirectories as $dir => $description) {
            $fullPath = $basePath . '/' . $dir;

            echo '<div class="file-item">';
            echo '<strong>' . htmlspecialchars($description) . ':</strong><br>';
            echo htmlspecialchars($dir);

            if (file_exists($fullPath)) {
                if (is_writable($fullPath)) {
                    echo '<br><span style="color: #28a745;">✅ Scrivibile</span>';
                    $operazioni[] = "Directory OK: $description";
                } else {
                    echo '<br><span style="color: #dc3545;">❌ NON scrivibile!</span>';
                    echo '<br>Permessi: ' . substr(sprintf('%o', fileperms($fullPath)), -4);
                    $errori[] = "Permessi errati: $description";
                }
            } else {
                echo '<br><span style="color: #ffc107;">⚠️ Non esiste, tentativo creazione...</span>';

                if (@mkdir($fullPath, 0755, true)) {
                    echo '<br><span style="color: #28a745;">✅ Creata con successo!</span>';
                    $operazioni[] = "Creata: $description";
                } else {
                    echo '<br><span style="color: #dc3545;">❌ Impossibile creare</span>';
                    $errori[] = "Impossibile creare: $description";
                }
            }

            echo '</div>';
        }
        echo '</div>';

        // Step 3: Forza Bootstrap Laravel e rigenera cache
        echo '<div class="step">';
        echo '<h3>Step 3: Bootstrap Laravel e Rigenera Service Providers</h3>';

        try {
            // Bootstrap Laravel SENZA cache
            require $basePath . '/vendor/autoload.php';
            $app = require_once $basePath . '/bootstrap/app.php';

            echo '<div class="success">✅ Laravel bootstrappato con successo!</div>';
            $operazioni[] = 'Laravel bootstrapped';

            // Forza il kernel a caricare i provider
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            echo '<div class="success">✅ Kernel bootstrappato!</div>';
            $operazioni[] = 'Kernel bootstrapped';

            // Ottieni lista service providers registrati
            $providers = array_map(function($provider) {
                return get_class($provider);
            }, $app->getLoadedProviders());

            echo '<div class="info">';
            echo '<strong>📋 Service Providers Caricati (' . count($providers) . ' totali):</strong><br>';

            // Filtra solo i provider rilevanti
            $relevantProviders = array_filter($providers, function($provider) {
                return stripos($provider, 'dompdf') !== false ||
                       stripos($provider, 'excel') !== false ||
                       stripos($provider, 'maatwebsite') !== false;
            });

            if (!empty($relevantProviders)) {
                echo '<div class="success">';
                echo '<strong>✅ Provider Export trovati:</strong><br>';
                foreach ($relevantProviders as $provider) {
                    echo '• ' . htmlspecialchars($provider) . '<br>';
                }
                echo '</div>';
                $operazioni[] = 'Provider export trovati: ' . count($relevantProviders);
            } else {
                echo '<div class="error">';
                echo '<strong>❌ NESSUN provider export trovato!</strong><br>';
                echo 'I provider DomPDF e Maatwebsite Excel NON sono stati caricati.<br>';
                echo '</div>';
                $errori[] = 'Provider export NON trovati';
            }

            // Mostra tutti i provider (collassato)
            echo '<details style="margin-top: 15px;">';
            echo '<summary style="cursor: pointer; color: #0c5460;"><strong>Mostra tutti i ' . count($providers) . ' provider</strong></summary>';
            echo '<div class="code" style="max-height: 300px; overflow-y: auto; margin-top: 10px;">';
            foreach ($providers as $provider) {
                echo htmlspecialchars($provider) . '<br>';
            }
            echo '</div>';
            echo '</details>';

            echo '</div>';

        } catch (\Exception $e) {
            echo '<div class="error">';
            echo '<strong>❌ Errore durante bootstrap:</strong><br>';
            echo htmlspecialchars($e->getMessage());
            echo '</div>';
            echo '<div class="code">' . htmlspecialchars($e->getTraceAsString()) . '</div>';
            $errori[] = 'Errore bootstrap: ' . $e->getMessage();
        }
        echo '</div>';

        // Step 4: Test binding DomPDF
        echo '<div class="step">';
        echo '<h3>Step 4: Test Binding DomPDF nel Container</h3>';

        try {
            if ($app->bound('dompdf.wrapper')) {
                echo '<div class="success">✅ Binding "dompdf.wrapper" TROVATO!</div>';

                // Prova a risolvere
                $pdf = $app->make('dompdf.wrapper');
                echo '<div class="success">✅ Istanza creata: ' . get_class($pdf) . '</div>';
                $operazioni[] = 'Binding DomPDF OK';

            } else {
                echo '<div class="error">❌ Binding "dompdf.wrapper" NON TROVATO!</div>';

                // Debug: mostra tutti i binding
                echo '<div class="info">';
                echo '<strong>🔍 Binding disponibili nel container:</strong><br>';
                echo '<details><summary style="cursor: pointer;">Mostra tutti i binding</summary>';
                echo '<div class="code" style="max-height: 300px; overflow-y: auto;">';

                // Usa reflection per ottenere i binding (non c'è metodo pubblico)
                $reflection = new ReflectionClass($app);
                $bindingsProperty = $reflection->getProperty('bindings');
                $bindingsProperty->setAccessible(true);
                $bindings = $bindingsProperty->getValue($app);

                foreach (array_keys($bindings) as $binding) {
                    echo htmlspecialchars($binding) . '<br>';
                }
                echo '</div></details>';
                echo '</div>';

                $errori[] = 'Binding DomPDF NON trovato';
            }

        } catch (\Exception $e) {
            echo '<div class="error">❌ Errore test binding: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $errori[] = 'Errore binding: ' . $e->getMessage();
        }
        echo '</div>';

        // Step 5: Verifica file composer.json del package
        echo '<div class="step">';
        echo '<h3>Step 5: Verifica Auto-Discovery nel Package</h3>';

        $packageComposerPath = $basePath . '/vendor/barryvdh/laravel-dompdf/composer.json';

        if (file_exists($packageComposerPath)) {
            $packageComposer = json_decode(file_get_contents($packageComposerPath), true);

            if (isset($packageComposer['extra']['laravel']['providers'])) {
                echo '<div class="success">';
                echo '<strong>✅ Auto-discovery configurato nel package:</strong><br>';
                echo '<div class="code">';
                foreach ($packageComposer['extra']['laravel']['providers'] as $provider) {
                    echo '• Provider: ' . htmlspecialchars($provider) . '<br>';
                }
                if (isset($packageComposer['extra']['laravel']['aliases'])) {
                    echo '<br>Aliases:<br>';
                    foreach ($packageComposer['extra']['laravel']['aliases'] as $alias => $class) {
                        echo '• ' . htmlspecialchars($alias) . ' => ' . htmlspecialchars($class) . '<br>';
                    }
                }
                echo '</div>';
                echo '</div>';
                $operazioni[] = 'Auto-discovery presente nel package';
            } else {
                echo '<div class="error">❌ Auto-discovery NON configurato nel package!</div>';
                $errori[] = 'Auto-discovery mancante';
            }
        } else {
            echo '<div class="error">❌ Package barryvdh/laravel-dompdf NON trovato!</div>';
            $errori[] = 'Package DomPDF mancante';
        }
        echo '</div>';

        // Step 6: Suggerimento registrazione manuale
        if (!empty($errori) && in_array('Binding DomPDF NON trovato', $errori)) {
            echo '<div class="step">';
            echo '<h3>🔧 Soluzione: Registrazione Manuale Service Provider</h3>';

            echo '<div class="warning">';
            echo '<strong>⚠️ L\'auto-discovery non funziona su questo server.</strong><br><br>';
            echo 'Devo registrare manualmente il service provider in <code>config/app.php</code><br>';
            echo '</div>';

            echo '<div class="info">';
            echo '<strong>📝 Cosa farò:</strong><br>';
            echo '1. Aprire <code>config/app.php</code><br>';
            echo '2. Aggiungere nella sezione <code>providers</code>:<br>';
            echo '<div class="code">';
            echo "Barryvdh\\DomPDF\\ServiceProvider::class,<br>";
            echo "Maatwebsite\\Excel\\ExcelServiceProvider::class,";
            echo '</div>';
            echo '3. Committare e pushare<br>';
            echo '4. Testare nuovamente gli export<br>';
            echo '</div>';

            echo '</div>';
        }

        // Riepilogo finale
        echo '<hr style="margin: 30px 0;">';
        echo '<h2>📊 Riepilogo Operazioni</h2>';

        if (empty($errori)) {
            echo '<div class="success">';
            echo '<h3>✅ TUTTO OK! Service Providers rigenerati!</h3>';
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
            echo '<li>Prova export PDF</li>';
            echo '<li>Se funziona: ✅ Problema risolto!</li>';
            echo '</ol>';
            echo '</div>';

        } else {
            echo '<div class="error">';
            echo '<h3>⚠️ Problemi rilevati (' . count($errori) . ')</h3>';
            echo '<ul>';
            foreach ($errori as $err) {
                echo '<li>' . htmlspecialchars($err) . '</li>';
            }
            echo '</ul>';
            echo '</div>';

            if (!empty($operazioni)) {
                echo '<div class="info">';
                echo '<p><strong>Operazioni completate:</strong></p>';
                echo '<ul>';
                foreach ($operazioni as $op) {
                    echo '<li>' . htmlspecialchars($op) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        }
        ?>

        <hr style="margin: 30px 0;">

        <div style="text-align: center;">
            <a href="?secret=magia2024" class="btn">🔄 Ricarica Pagina</a>
            <a href="/admin/report" class="btn">📊 Vai ai Report</a>
            <a href="vedi-errori-laravel.php?secret=magia2024" class="btn">📋 Vedi Errori</a>
        </div>

        <div class="info" style="margin-top: 30px; font-size: 0.9em;">
            <strong>File:</strong> /public/rigenera-service-providers.php<br>
            <strong>Data:</strong> <?= date('d/m/Y H:i:s') ?><br>
            <strong>PHP:</strong> <?= PHP_VERSION ?>
        </div>
    </div>
</body>
</html>
