<?php
/**
 * Script per configurare storage e directory per upload foto
 *
 * IMPORTANTE: Elimina questo file dopo l'uso!
 */

define('SETUP_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== SETUP_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . SETUP_TOKEN . '</p>');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Storage - Magia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content { padding: 30px; }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
        }
        .code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin: 10px 0;
        }
        h2 { margin-bottom: 15px; color: #667eea; }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin-right: 10px;
        }
        .badge.success { background: #28a745; color: white; }
        .badge.error { background: #dc3545; color: white; }
        .badge.info { background: #17a2b8; color: white; }
        .badge.warning { background: #ffc107; color: #333; }
        ul { margin: 15px 0 15px 30px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📂 Setup Storage Professionisti</h1>
            <p>Configura directory e permessi per upload foto</p>
        </div>

        <div class="content">
            <?php
            $baseDir = __DIR__ . '/..';
            $errors = [];
            $warnings = [];
            $success = [];

            // STEP 1: Verifica directory storage principale
            echo '<div class="step">';
            echo '<h2>📁 Step 1: Verifica Directory Storage</h2>';

            $storageDirs = [
                'storage/app/public',
                'storage/app/public/professionisti',
                'storage/app/public/professionisti/galleria',
                'storage/app/public/professionisti/documenti',
            ];

            foreach ($storageDirs as $dir) {
                $fullPath = $baseDir . '/' . $dir;

                if (is_dir($fullPath)) {
                    echo '<p><span class="badge success">✓ ESISTE</span> ' . htmlspecialchars($dir) . '</p>';
                    $success[] = "Directory $dir esiste";

                    // Verifica permessi
                    $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
                    echo '<p style="margin-left: 30px;">Permessi: <code>' . $perms . '</code>';

                    if (is_writable($fullPath)) {
                        echo ' <span class="badge success">✓ Scrivibile</span></p>';
                    } else {
                        echo ' <span class="badge error">✗ NON Scrivibile</span></p>';
                        $errors[] = "Directory $dir non scrivibile";
                    }
                } else {
                    echo '<p><span class="badge warning">⚠ MANCANTE</span> ' . htmlspecialchars($dir) . '</p>';

                    // Prova a creare
                    if (@mkdir($fullPath, 0755, true)) {
                        echo '<p style="margin-left: 30px;"><span class="badge success">✓ CREATA</span> Directory creata con successo!</p>';
                        $success[] = "Directory $dir creata";
                    } else {
                        echo '<p style="margin-left: 30px;"><span class="badge error">✗ ERRORE</span> Impossibile creare la directory</p>';
                        $errors[] = "Impossibile creare directory $dir";
                    }
                }
            }
            echo '</div>';

            // STEP 2: Verifica symlink public/storage
            echo '<div class="step">';
            echo '<h2>🔗 Step 2: Verifica Symlink public/storage</h2>';

            $symlinkPath = $baseDir . '/public/storage';
            $targetPath = $baseDir . '/storage/app/public';

            if (file_exists($symlinkPath)) {
                if (is_link($symlinkPath)) {
                    $linkTarget = readlink($symlinkPath);
                    echo '<p><span class="badge success">✓ ESISTE</span> Symlink public/storage trovato</p>';
                    echo '<p style="margin-left: 30px;">Punta a: <code>' . htmlspecialchars($linkTarget) . '</code></p>';

                    if (realpath($symlinkPath) === realpath($targetPath)) {
                        echo '<p style="margin-left: 30px;"><span class="badge success">✓ CORRETTO</span> Il symlink punta alla directory corretta</p>';
                        $success[] = "Symlink corretto";
                    } else {
                        echo '<p style="margin-left: 30px;"><span class="badge warning">⚠ ERRATO</span> Il symlink non punta alla directory corretta</p>';
                        $warnings[] = "Symlink punta alla directory sbagliata";
                    }
                } else {
                    echo '<p><span class="badge error">✗ ERRORE</span> public/storage esiste ma NON è un symlink</p>';
                    echo '<p style="margin-left: 30px;">È una directory o file normale. Eliminala manualmente e rilancia questo script.</p>';
                    $errors[] = "public/storage esiste ma non è un symlink";
                }
            } else {
                echo '<p><span class="badge warning">⚠ MANCANTE</span> Symlink non trovato</p>';

                // Prova a creare symlink
                echo '<p style="margin-left: 30px;">Tentativo di creazione symlink...</p>';

                if (@symlink($targetPath, $symlinkPath)) {
                    echo '<p style="margin-left: 30px;"><span class="badge success">✓ CREATO</span> Symlink creato con successo!</p>';
                    $success[] = "Symlink creato";
                } else {
                    echo '<p style="margin-left: 30px;"><span class="badge error">✗ FALLITO</span> Impossibile creare symlink automaticamente</p>';
                    echo '<div class="warning" style="margin: 15px 0; padding: 15px; border-radius: 6px;">';
                    echo '<p><strong>Crea il symlink manualmente via SSH:</strong></p>';
                    echo '<div class="code">cd ' . htmlspecialchars($baseDir) . '<br>';
                    echo 'php artisan storage:link</div>';
                    echo '<p style="margin-top: 10px;"><strong>Oppure crea il symlink manualmente:</strong></p>';
                    echo '<div class="code">ln -s ' . htmlspecialchars($targetPath) . ' ' . htmlspecialchars($symlinkPath) . '</div>';
                    echo '</div>';
                    $errors[] = "Impossibile creare symlink automaticamente";
                }
            }
            echo '</div>';

            // STEP 3: Test scrittura
            echo '<div class="step">';
            echo '<h2>✍️ Step 3: Test Scrittura File</h2>';

            $testFile = $baseDir . '/storage/app/public/professionisti/test-write-' . time() . '.txt';
            $testContent = 'Test scrittura: ' . date('Y-m-d H:i:s');

            if (@file_put_contents($testFile, $testContent)) {
                echo '<p><span class="badge success">✓ SUCCESSO</span> File di test scritto correttamente</p>';
                echo '<p style="margin-left: 30px;">File: <code>' . htmlspecialchars($testFile) . '</code></p>';

                // Elimina file di test
                @unlink($testFile);
                echo '<p style="margin-left: 30px;"><span class="badge info">ℹ INFO</span> File di test eliminato</p>';
                $success[] = "Test scrittura riuscito";
            } else {
                echo '<p><span class="badge error">✗ FALLITO</span> Impossibile scrivere file di test</p>';
                echo '<p style="margin-left: 30px;">Verifica i permessi della directory storage/app/public/professionisti</p>';
                $errors[] = "Test scrittura fallito";
            }
            echo '</div>';

            // STEP 4: Verifica configurazione Laravel
            echo '<div class="step">';
            echo '<h2>⚙️ Step 4: Configurazione Laravel Filesystem</h2>';

            $configFile = $baseDir . '/config/filesystems.php';
            if (file_exists($configFile)) {
                echo '<p><span class="badge success">✓ TROVATO</span> File config/filesystems.php esiste</p>';

                // Mostra configurazione disco public
                echo '<p style="margin-top: 15px;">Configurazione disco <strong>public</strong>:</p>';
                echo '<div class="code">';
                echo "'public' => [<br>";
                echo "&nbsp;&nbsp;'driver' => 'local',<br>";
                echo "&nbsp;&nbsp;'root' => storage_path('app/public'),<br>";
                echo "&nbsp;&nbsp;'url' => env('APP_URL').'/storage',<br>";
                echo "&nbsp;&nbsp;'visibility' => 'public',<br>";
                echo "],";
                echo '</div>';
                $success[] = "Config filesystems.php presente";
            } else {
                echo '<p><span class="badge error">✗ MANCANTE</span> File config/filesystems.php non trovato</p>';
                $errors[] = "File config/filesystems.php mancante";
            }
            echo '</div>';

            // STEP 5: Informazioni percorsi
            echo '<div class="step">';
            echo '<h2>📍 Step 5: Informazioni Percorsi</h2>';

            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 10px;"><strong>Base Directory</strong></td><td style="padding: 10px;"><code>' . htmlspecialchars($baseDir) . '</code></td></tr>';
            echo '<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 10px;"><strong>Storage Public</strong></td><td style="padding: 10px;"><code>' . htmlspecialchars($baseDir . '/storage/app/public') . '</code></td></tr>';
            echo '<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 10px;"><strong>Public Storage Symlink</strong></td><td style="padding: 10px;"><code>' . htmlspecialchars($baseDir . '/public/storage') . '</code></td></tr>';
            echo '<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 10px;"><strong>Professionisti Dir</strong></td><td style="padding: 10px;"><code>' . htmlspecialchars($baseDir . '/storage/app/public/professionisti') . '</code></td></tr>';
            echo '<tr style="border-bottom: 1px solid #ddd;"><td style="padding: 10px;"><strong>URL Storage</strong></td><td style="padding: 10px;"><code>' . htmlspecialchars($_SERVER['HTTP_HOST'] ?? '') . '/magia/public/storage/professionisti/</code></td></tr>';
            echo '</table>';
            echo '</div>';

            // Riepilogo finale
            if (count($errors) === 0) {
                echo '<div class="step success">';
                echo '<h2>✅ Setup Completato con Successo!</h2>';
                echo '<p><strong>Tutto pronto per l\'upload delle foto!</strong></p>';
                echo '<ul>';
                foreach ($success as $msg) {
                    echo '<li>✓ ' . htmlspecialchars($msg) . '</li>';
                }
                echo '</ul>';
                echo '<p style="margin-top: 20px;">Ora puoi:</p>';
                echo '<ol>';
                echo '<li>Tornare alla pagina del professionista</li>';
                echo '<li>Caricare la foto profilo</li>';
                echo '<li>Gestire la galleria foto</li>';
                echo '</ol>';
                echo '<p style="margin-top: 20px;"><strong>🔒 IMPORTANTE:</strong> Elimina questo file per sicurezza!</p>';
                echo '<div class="code">rm ' . htmlspecialchars(__FILE__) . '</div>';
                echo '</div>';
            } else {
                echo '<div class="step error">';
                echo '<h2>❌ Setup Completato con Errori</h2>';
                echo '<p><strong>Problemi riscontrati:</strong></p>';
                echo '<ul>';
                foreach ($errors as $err) {
                    echo '<li>✗ ' . htmlspecialchars($err) . '</li>';
                }
                echo '</ul>';

                if (count($warnings) > 0) {
                    echo '<p style="margin-top: 15px;"><strong>Avvisi:</strong></p>';
                    echo '<ul>';
                    foreach ($warnings as $warn) {
                        echo '<li>⚠ ' . htmlspecialchars($warn) . '</li>';
                    }
                    echo '</ul>';
                }

                echo '<p style="margin-top: 20px;"><strong>Soluzioni:</strong></p>';
                echo '<ol>';
                echo '<li>Verifica i permessi delle directory con <code>chmod -R 755 storage/</code></li>';
                echo '<li>Crea il symlink manualmente con <code>php artisan storage:link</code></li>';
                echo '<li>Verifica che l\'utente web server abbia accesso alle directory</li>';
                echo '</ol>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
