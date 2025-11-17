<?php
/**
 * Script per eseguire migrations dal browser
 *
 * IMPORTANTE: Elimina questo file dopo l'uso per sicurezza!
 *
 * URL: http://tuosito.com/run-migrations.php
 */

// Token di sicurezza semplice - CAMBIALO!
define('MIGRATION_TOKEN', 'magia2025');

// Verifica token
if (!isset($_GET['token']) || $_GET['token'] !== MIGRATION_TOKEN) {
    die('
    <html>
    <head>
        <title>Accesso Negato</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .error { background: #fee; border: 2px solid #c33; padding: 20px; border-radius: 8px; max-width: 600px; margin: 50px auto; }
            h1 { color: #c33; }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>⛔ Accesso Negato</h1>
            <p>Token di sicurezza mancante o non valido.</p>
            <p>Usa: <code>?token=' . MIGRATION_TOKEN . '</code></p>
        </div>
    </body>
    </html>
    ');
}

// Carica Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Bootstrap dell'applicazione
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Imposta l'applicazione Laravel come istanza globale
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esecuzione Migrations - Magia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
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
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .step h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .success h2 { color: #28a745; }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .error h2 { color: #dc3545; }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .warning h2 { color: #ff6b6b; }
        .code {
            background: #1e1e1e;
            color: #dcdcdc;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            line-height: 1.6;
            margin: 10px 0;
        }
        .code .success-line { color: #4ec9b0; }
        .code .error-line { color: #f48771; }
        .code .info-line { color: #9cdcfe; }
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
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn:hover { background: #5568d3; }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        ul { margin: 15px 0 15px 30px; }
        li { margin: 8px 0; }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Esecuzione Migrations Magia</h1>
            <p>Gestione Database Professionisti</p>
        </div>

        <div class="content">
            <?php
            $startTime = microtime(true);
            $hasErrors = false;
            $migrations = [];

            // STEP 1: Verifica connessione database
            echo '<div class="step">';
            echo '<h2>📡 Step 1: Verifica Connessione Database</h2>';

            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
                echo '<p><span class="badge success">✓ CONNESSO</span> Database: <strong>' . htmlspecialchars($dbName) . '</strong></p>';

                // Mostra info database
                $driver = config('database.default');
                $host = config("database.connections.{$driver}.host");
                echo '<p>Driver: <strong>' . htmlspecialchars($driver) . '</strong> | Host: <strong>' . htmlspecialchars($host) . '</strong></p>';

            } catch (\Exception $e) {
                echo '<div class="error">';
                echo '<p><span class="badge error">✗ ERRORE</span> Impossibile connettersi al database</p>';
                echo '<div class="code"><span class="error-line">' . htmlspecialchars($e->getMessage()) . '</span></div>';
                echo '</div>';
                $hasErrors = true;
            }
            echo '</div>';

            if (!$hasErrors) {
                // STEP 2: Lista migrations pending
                echo '<div class="step">';
                echo '<h2>📋 Step 2: Verifica Migrations Pending</h2>';

                try {
                    // Cattura output del comando migrate:status
                    \Illuminate\Support\Facades\Artisan::call('migrate:status');
                    $status = \Illuminate\Support\Facades\Artisan::output();

                    echo '<div class="code">' . nl2br(htmlspecialchars($status)) . '</div>';

                } catch (\Exception $e) {
                    echo '<p><span class="badge info">ℹ INFO</span> Tabella migrations non ancora creata (normale per prima installazione)</p>';
                }
                echo '</div>';

                // STEP 3: Esegui migrations
                echo '<div class="step">';
                echo '<h2>⚙️ Step 3: Esecuzione Migrations</h2>';
                echo '<div class="spinner"></div>';
                echo '<p>Esecuzione in corso...</p>';

                flush();
                ob_flush();

                try {
                    // Esegui migrations con verbose
                    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', [
                        '--force' => true,
                        '--verbose' => true,
                    ]);

                    $output = \Illuminate\Support\Facades\Artisan::output();

                    // Analizza output
                    $lines = explode("\n", $output);
                    $migratedCount = 0;

                    echo '<div class="code">';
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;

                        $class = 'info-line';
                        if (stripos($line, 'Migrated:') !== false || stripos($line, 'Migrating:') !== false) {
                            $class = 'success-line';
                            $migratedCount++;
                        } elseif (stripos($line, 'error') !== false || stripos($line, 'failed') !== false) {
                            $class = 'error-line';
                            $hasErrors = true;
                        }

                        echo '<span class="' . $class . '">' . htmlspecialchars($line) . '</span><br>';
                    }
                    echo '</div>';

                    if ($exitCode === 0 && !$hasErrors) {
                        echo '</div><div class="step success">';
                        echo '<h2>✅ Migrations Completate con Successo!</h2>';
                        echo '<p>Numero di migrations eseguite: <strong>' . $migratedCount . '</strong></p>';
                    } else {
                        echo '</div><div class="step error">';
                        echo '<h2>❌ Errore durante le migrations</h2>';
                        echo '<p>Exit code: <strong>' . $exitCode . '</strong></p>';
                        $hasErrors = true;
                    }

                } catch (\Exception $e) {
                    echo '</div><div class="step error">';
                    echo '<h2>❌ Eccezione durante l\'esecuzione</h2>';
                    echo '<div class="code"><span class="error-line">';
                    echo 'Messaggio: ' . htmlspecialchars($e->getMessage()) . '<br>';
                    echo 'File: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '<br><br>';
                    echo 'Stack Trace:<br>' . nl2br(htmlspecialchars($e->getTraceAsString()));
                    echo '</span></div>';
                    $hasErrors = true;
                }
                echo '</div>';

                // STEP 4: Verifica tabelle create
                echo '<div class="step">';
                echo '<h2>🗄️ Step 4: Verifica Tabelle Database</h2>';

                try {
                    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $tableKey = 'Tables_in_' . \Illuminate\Support\Facades\DB::getDatabaseName();

                    $professionistiTables = [];
                    foreach ($tables as $table) {
                        $tableName = $table->$tableKey;
                        if (stripos($tableName, 'profession') !== false) {
                            $professionistiTables[] = $tableName;
                        }
                    }

                    if (count($professionistiTables) > 0) {
                        echo '<p><span class="badge success">✓ TROVATE</span> Tabelle professionisti create:</p>';
                        echo '<ul>';
                        foreach ($professionistiTables as $tableName) {
                            // Conta record
                            $count = \Illuminate\Support\Facades\DB::table($tableName)->count();
                            echo '<li><strong>' . htmlspecialchars($tableName) . '</strong> - ' . $count . ' record</li>';
                        }
                        echo '</ul>';

                        // Mostra struttura tabella professionisti
                        if (in_array('professionisti', $professionistiTables)) {
                            echo '<p style="margin-top: 20px;"><strong>Struttura tabella professionisti:</strong></p>';
                            $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE professionisti');

                            echo '<div class="code" style="max-height: 400px; overflow-y: auto;">';
                            echo '<table style="width: 100%; color: #dcdcdc;">';
                            echo '<tr style="color: #4ec9b0;"><th style="text-align: left; padding: 5px;">Campo</th><th style="text-align: left; padding: 5px;">Tipo</th><th style="text-align: left; padding: 5px;">Null</th><th style="text-align: left; padding: 5px;">Default</th></tr>';
                            foreach ($columns as $col) {
                                echo '<tr>';
                                echo '<td style="padding: 3px; color: #9cdcfe;">' . htmlspecialchars($col->Field) . '</td>';
                                echo '<td style="padding: 3px;">' . htmlspecialchars($col->Type) . '</td>';
                                echo '<td style="padding: 3px;">' . htmlspecialchars($col->Null) . '</td>';
                                echo '<td style="padding: 3px;">' . htmlspecialchars($col->Default ?? 'NULL') . '</td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p><span class="badge warning">⚠ ATTENZIONE</span> Nessuna tabella professionisti trovata</p>';
                    }

                } catch (\Exception $e) {
                    echo '<p><span class="badge error">✗ ERRORE</span> Impossibile verificare le tabelle</p>';
                    echo '<div class="code"><span class="error-line">' . htmlspecialchars($e->getMessage()) . '</span></div>';
                }
                echo '</div>';

                // STEP 5: Stato finale migrations
                echo '<div class="step">';
                echo '<h2>📊 Step 5: Stato Finale Migrations</h2>';

                try {
                    \Illuminate\Support\Facades\Artisan::call('migrate:status');
                    $finalStatus = \Illuminate\Support\Facades\Artisan::output();

                    echo '<div class="code">' . nl2br(htmlspecialchars($finalStatus)) . '</div>';

                } catch (\Exception $e) {
                    echo '<div class="code"><span class="error-line">' . htmlspecialchars($e->getMessage()) . '</span></div>';
                }
                echo '</div>';
            }

            // Tempo di esecuzione
            $executionTime = round(microtime(true) - $startTime, 2);

            // Riepilogo finale
            if (!$hasErrors) {
                echo '<div class="step success">';
                echo '<h2>🎉 Processo Completato con Successo!</h2>';
                echo '<p>✅ Database configurato correttamente</p>';
                echo '<p>✅ Tutte le migrations eseguite</p>';
                echo '<p>✅ Tabelle professionisti create</p>';
                echo '<p>⏱️ Tempo di esecuzione: <strong>' . $executionTime . ' secondi</strong></p>';
                echo '<br>';
                echo '<a href="' . url('/admin/professionisti') . '" class="btn">Vai alla Gestione Professionisti →</a>';
                echo '</div>';

                echo '<div class="step warning">';
                echo '<h2>🔒 Importante - Sicurezza</h2>';
                echo '<p><strong>ELIMINA SUBITO QUESTO FILE!</strong></p>';
                echo '<p>Per sicurezza, elimina il file <code>public/run-migrations.php</code> dal server:</p>';
                echo '<div class="code">rm ' . __FILE__ . '</div>';
                echo '</div>';

            } else {
                echo '<div class="step error">';
                echo '<h2>❌ Processo Completato con Errori</h2>';
                echo '<p>Controlla i messaggi di errore sopra e risolvi i problemi.</p>';
                echo '<p>⏱️ Tempo di esecuzione: <strong>' . $executionTime . ' secondi</strong></p>';
                echo '<br>';
                echo '<p><strong>Possibili soluzioni:</strong></p>';
                echo '<ul>';
                echo '<li>Verifica le credenziali del database in <code>.env</code></li>';
                echo '<li>Verifica che il database esista</li>';
                echo '<li>Verifica i permessi dell\'utente database</li>';
                echo '<li>Controlla i log di Laravel in <code>storage/logs/</code></li>';
                echo '</ul>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="footer">
            <p><strong>Magia Studio</strong> - Migration Manager v1.0</p>
            <p>⚠️ Questo file è solo per uso di sviluppo/deployment. Eliminalo dopo l'uso!</p>
        </div>
    </div>
</body>
</html>
