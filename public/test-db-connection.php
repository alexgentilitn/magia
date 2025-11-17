<?php
/**
 * Script di Test Connessione Database
 *
 * Testa diverse configurazioni per trovare quella corretta
 */

define('TEST_TOKEN', 'magia2025');

if (!isset($_GET['token']) || $_GET['token'] !== TEST_TOKEN) {
    die('<h1>Accesso Negato</h1><p>Usa: ?token=' . TEST_TOKEN . '</p>');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connessione Database - Magia</title>
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
        .test {
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
        h2 { margin-bottom: 15px; }
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
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .highlight { background: #fff3cd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Test Connessione Database</h1>
            <p>Diagnostica completa delle credenziali database</p>
        </div>

        <div class="content">
            <?php

            // Carica Laravel ENV
            $envPath = __DIR__ . '/../.env';
            $envVars = [];

            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0) continue;
                    if (strpos($line, '=') === false) continue;

                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $value = trim($value, '"\'');
                    $envVars[$key] = $value;
                }
            }

            echo '<div class="test">';
            echo '<h2>📄 Step 1: Lettura File .env</h2>';

            if (!empty($envVars)) {
                echo '<p><span class="badge success">✓ TROVATO</span> File .env letto correttamente</p>';
                echo '<table>';
                echo '<tr><th>Parametro</th><th>Valore</th></tr>';
                echo '<tr><td>DB_CONNECTION</td><td>' . htmlspecialchars($envVars['DB_CONNECTION'] ?? 'NON IMPOSTATO') . '</td></tr>';
                echo '<tr><td>DB_HOST</td><td>' . htmlspecialchars($envVars['DB_HOST'] ?? 'NON IMPOSTATO') . '</td></tr>';
                echo '<tr><td>DB_PORT</td><td>' . htmlspecialchars($envVars['DB_PORT'] ?? 'NON IMPOSTATO') . '</td></tr>';
                echo '<tr><td>DB_DATABASE</td><td>' . htmlspecialchars($envVars['DB_DATABASE'] ?? 'NON IMPOSTATO') . '</td></tr>';
                echo '<tr><td>DB_USERNAME</td><td>' . htmlspecialchars($envVars['DB_USERNAME'] ?? 'NON IMPOSTATO') . '</td></tr>';
                echo '<tr><td>DB_PASSWORD</td><td>' . (isset($envVars['DB_PASSWORD']) && !empty($envVars['DB_PASSWORD']) ? '****** (impostata)' : 'NON IMPOSTATA') . '</td></tr>';
                echo '</table>';
            } else {
                echo '<p><span class="badge error">✗ ERRORE</span> Impossibile leggere il file .env</p>';
            }
            echo '</div>';

            // Test connessioni con diverse configurazioni
            $hosts = ['localhost', '127.0.0.1', 'mysql', $envVars['DB_HOST'] ?? 'localhost'];
            $hosts = array_unique($hosts);

            $dbConfig = [
                'database' => $envVars['DB_DATABASE'] ?? '',
                'username' => $envVars['DB_USERNAME'] ?? '',
                'password' => $envVars['DB_PASSWORD'] ?? '',
                'port' => $envVars['DB_PORT'] ?? '3306',
            ];

            echo '<div class="test">';
            echo '<h2>🔌 Step 2: Test Connessioni con Diversi Host</h2>';

            $connectionSuccess = false;
            $workingHost = null;

            foreach ($hosts as $host) {
                echo '<p><strong>Test con host: ' . htmlspecialchars($host) . '</strong></p>';

                try {
                    $dsn = "mysql:host={$host};port={$dbConfig['port']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 5,
                    ]);

                    echo '<p style="margin-left: 20px;"><span class="badge success">✓ CONNESSO</span> Connessione MySQL riuscita!</p>';

                    // Test se il database esiste
                    $stmt = $pdo->query("SHOW DATABASES LIKE '" . $dbConfig['database'] . "'");
                    $dbExists = $stmt->fetch();

                    if ($dbExists) {
                        echo '<p style="margin-left: 20px;"><span class="badge success">✓ TROVATO</span> Database "' . htmlspecialchars($dbConfig['database']) . '" esistente</p>';
                        $connectionSuccess = true;
                        $workingHost = $host;

                        // Connessione al database specifico
                        $dsn = "mysql:host={$host};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
                        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);

                        // Mostra tabelle esistenti
                        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        if (count($tables) > 0) {
                            echo '<p style="margin-left: 20px;"><span class="badge info">ℹ INFO</span> Tabelle esistenti: ' . count($tables) . '</p>';
                            echo '<div class="code" style="margin-left: 20px;">';
                            foreach ($tables as $table) {
                                echo htmlspecialchars($table);
                                if (stripos($table, 'profession') !== false) {
                                    echo ' <span style="color: #4ec9b0;">← TABELLA PROFESSIONISTI</span>';
                                }
                                echo '<br>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p style="margin-left: 20px;"><span class="badge info">ℹ INFO</span> Database vuoto (nessuna tabella)</p>';
                        }

                        break; // Trovato host funzionante

                    } else {
                        echo '<p style="margin-left: 20px;"><span class="badge error">✗ ERRORE</span> Database "' . htmlspecialchars($dbConfig['database']) . '" NON ESISTE</p>';
                        echo '<p style="margin-left: 20px;">Database disponibili:</p>';

                        $stmt = $pdo->query("SHOW DATABASES");
                        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        echo '<div class="code" style="margin-left: 20px;">';
                        foreach ($databases as $db) {
                            if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                                echo htmlspecialchars($db) . '<br>';
                            }
                        }
                        echo '</div>';
                    }

                } catch (PDOException $e) {
                    echo '<p style="margin-left: 20px;"><span class="badge error">✗ FALLITO</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
                }

                echo '<hr style="margin: 15px 0;">';
            }

            echo '</div>';

            // Riepilogo e suggerimenti
            if ($connectionSuccess) {
                echo '<div class="test success">';
                echo '<h2>✅ Connessione Riuscita!</h2>';
                echo '<p><strong>Host funzionante:</strong> ' . htmlspecialchars($workingHost) . '</p>';

                if ($workingHost !== ($envVars['DB_HOST'] ?? '')) {
                    echo '<div class="warning" style="margin-top: 15px; padding: 15px; border-radius: 6px;">';
                    echo '<p><strong>⚠️ ATTENZIONE:</strong> L\'host funzionante è diverso da quello in .env!</p>';
                    echo '<p style="margin-top: 10px;">Modifica il file .env:</p>';
                    echo '<div class="code">DB_HOST=' . htmlspecialchars($workingHost) . '</div>';
                    echo '</div>';
                }

                echo '<p style="margin-top: 20px;"><strong>Prossimo passo:</strong></p>';
                echo '<p>Ora puoi eseguire le migrations:</p>';
                echo '<div class="code">
                    <a href="run-migrations.php?token=magia2025" style="color: #4ec9b0; text-decoration: none;">
                        → Vai a run-migrations.php?token=magia2025
                    </a>
                </div>';
                echo '</div>';

            } else {
                echo '<div class="test error">';
                echo '<h2>❌ Connessione Fallita</h2>';
                echo '<p><strong>Possibili cause:</strong></p>';
                echo '<ul style="margin-left: 30px; margin-top: 10px;">';
                echo '<li>Database "' . htmlspecialchars($dbConfig['database']) . '" non esiste</li>';
                echo '<li>Username o password errati</li>';
                echo '<li>Server MySQL non raggiungibile</li>';
                echo '<li>Permessi insufficienti per l\'utente database</li>';
                echo '</ul>';

                echo '<p style="margin-top: 20px;"><strong>Soluzioni:</strong></p>';
                echo '<ol style="margin-left: 30px; margin-top: 10px;">';
                echo '<li><strong>Crea il database</strong> (tramite phpMyAdmin o cPanel):<br>';
                echo '<div class="code" style="margin-top: 5px;">CREATE DATABASE ' . htmlspecialchars($dbConfig['database']) . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</div>';
                echo '</li>';
                echo '<li style="margin-top: 10px;"><strong>Verifica le credenziali</strong> nel file .env</li>';
                echo '<li style="margin-top: 10px;"><strong>Verifica i permessi</strong> dell\'utente database</li>';
                echo '</ol>';
                echo '</div>';
            }

            // Info server
            echo '<div class="test">';
            echo '<h2>📊 Informazioni Server</h2>';
            echo '<table>';
            echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
            echo '<tr><td>PDO Driver MySQL</td><td>' . (extension_loaded('pdo_mysql') ? '✓ Installato' : '✗ NON Installato') . '</td></tr>';
            echo '<tr><td>Server Software</td><td>' . htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . '</td></tr>';
            echo '<tr><td>Document Root</td><td>' . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</td></tr>';
            echo '<tr><td>Script Path</td><td>' . htmlspecialchars(__FILE__) . '</td></tr>';
            echo '</table>';
            echo '</div>';

            ?>
        </div>
    </div>
</body>
</html>
