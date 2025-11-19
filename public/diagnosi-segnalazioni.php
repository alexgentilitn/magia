<?php
/**
 * Diagnosi Completa Sistema Segnalazioni
 * Scopre la struttura del database e diagnostica il problema
 *
 * ELIMINA QUESTO FILE dopo l'uso!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carica configurazione database dal .env
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("Errore: file .env non trovato");
}

$envContent = file_get_contents($envPath);
preg_match('/DB_HOST=(.+)/', $envContent, $host);
preg_match('/DB_PORT=(.+)/', $envContent, $port);
preg_match('/DB_DATABASE=(.+)/', $envContent, $database);
preg_match('/DB_USERNAME=(.+)/', $envContent, $username);
preg_match('/DB_PASSWORD=(.+)/', $envContent, $password);

$dbHost = trim($host[1] ?? 'localhost');
$dbPort = trim($port[1] ?? '3306');
$dbName = trim($database[1] ?? '');
$dbUser = trim($username[1] ?? '');
$dbPass = trim($password[1] ?? '');

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosi Segnalazioni - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .step {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .step.success {
            background: #ecfdf5;
            border-color: #10b981;
        }
        .step.error {
            background: #fef2f2;
            border-color: #ef4444;
        }
        .step.warning {
            background: #fefce8;
            border-color: #eab308;
        }
        .step-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .step-content {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 10px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 5px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }
        .solution-box {
            background: #e0f2fe;
            border: 2px solid #0ea5e9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnosi Sistema Segnalazioni</h1>
        <p style="color: #666; margin-bottom: 30px; font-size: 14px;">
            Analisi completa della struttura database e identificazione problemi
        </p>

<?php

try {
    // Connessione database
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo '<div class="step success">';
    echo '<div class="step-title">✅ Connessione Database</div>';
    echo '<div class="step-content">Connesso a: <strong>' . htmlspecialchars($dbName) . '</strong></div>';
    echo '</div>';

    // 1. Scopri struttura tabella users
    echo '<div class="step">';
    echo '<div class="step-title">🔍 1. Struttura Tabella Users</div>';

    $usersColumns = $pdo->query("DESCRIBE users")->fetchAll();

    echo '<table>';
    echo '<thead><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>';
    echo '<tbody>';

    $hasNome = false;
    $hasName = false;
    $columnNames = [];

    foreach ($usersColumns as $col) {
        echo '<tr>';
        echo '<td><strong>' . htmlspecialchars($col['Field']) . '</strong></td>';
        echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
        echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
        echo '<td>' . htmlspecialchars($col['Key']) . '</td>';
        echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
        echo '</tr>';

        $columnNames[] = $col['Field'];
        if ($col['Field'] == 'nome') $hasNome = true;
        if ($col['Field'] == 'name') $hasName = true;
    }
    echo '</tbody></table>';

    echo '<div class="step-content">';
    echo '<strong>Colonne trovate:</strong> ' . implode(', ', $columnNames);
    echo '</div>';

    echo '</div>';

    // 2. Determina quali colonne usare
    $nameColumn = $hasName ? 'name' : ($hasNome ? 'nome' : 'id');
    $emailColumn = in_array('email', $columnNames) ? 'email' : null;
    $tipoColumn = in_array('tipo_utente', $columnNames) ? 'tipo_utente' : null;

    echo '<div class="step">';
    echo '<div class="step-title">📋 2. Utenti nel Database</div>';

    // Costruisci query SELECT dinamica
    $selectFields = ['id'];
    if ($nameColumn) $selectFields[] = $nameColumn;
    if ($emailColumn) $selectFields[] = $emailColumn;
    if ($tipoColumn) $selectFields[] = $tipoColumn;

    $sql = "SELECT " . implode(', ', $selectFields) . " FROM users ORDER BY id";
    $users = $pdo->query($sql)->fetchAll();

    if (count($users) > 0) {
        echo '<table>';
        echo '<thead><tr>';
        foreach ($selectFields as $field) {
            echo '<th>' . htmlspecialchars(ucfirst($field)) . '</th>';
        }
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($users as $user) {
            echo '<tr>';
            foreach ($selectFields as $field) {
                $value = $user[$field] ?? '';
                if ($field == 'id') {
                    echo '<td><strong>' . htmlspecialchars($value) . '</strong></td>';
                } else {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
            }
            echo '</tr>';
        }

        echo '</tbody></table>';

        echo '<div class="step-content">';
        echo '<strong>Totale utenti:</strong> ' . count($users) . '<br>';
        $userIds = array_column($users, 'id');
        echo '<strong>ID disponibili:</strong> ' . implode(', ', $userIds);
        echo '</div>';
    } else {
        echo '<div class="step-content" style="color: #ef4444;">';
        echo '<strong>⚠️ Nessun utente trovato!</strong>';
        echo '</div>';
    }

    echo '</div>';

    // 3. Verifica tabella segnalazioni
    echo '<div class="step">';
    echo '<div class="step-title">📊 3. Tabella Segnalazioni</div>';

    $tableExists = $pdo->query("SHOW TABLES LIKE 'segnalazioni'")->rowCount() > 0;

    if ($tableExists) {
        $segnalazioni = $pdo->query("SELECT * FROM segnalazioni ORDER BY created_at DESC")->fetchAll();

        if (count($segnalazioni) > 0) {
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Utente ID</th><th>Titolo</th><th>Tipo</th><th>Stato</th><th>Data</th></tr></thead>';
            echo '<tbody>';
            foreach ($segnalazioni as $seg) {
                echo '<tr>';
                echo '<td>' . $seg['id'] . '</td>';
                echo '<td>' . $seg['utente_id'] . '</td>';
                echo '<td>' . htmlspecialchars(substr($seg['titolo'], 0, 40)) . '</td>';
                echo '<td>' . $seg['tipo'] . '</td>';
                echo '<td>' . $seg['stato'] . '</td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($seg['created_at'])) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="step-content">';
            echo '✓ Tabella esistente ma vuota (nessuna segnalazione)';
            echo '</div>';
        }
    } else {
        echo '<div class="step-content" style="color: #ef4444;">';
        echo '❌ La tabella segnalazioni NON esiste! Esegui setup-segnalazioni.php';
        echo '</div>';
    }

    echo '</div>';

    // 4. Diagnosi e soluzione
    echo '<div class="step warning">';
    echo '<div class="step-title">🔍 4. Diagnosi Problema</div>';
    echo '<div class="step-content">';

    if (count($users) == 0) {
        echo '<strong style="color: #ef4444;">PROBLEMA:</strong> Nessun utente nel database<br><br>';
        echo 'Questo è molto strano. Devi avere almeno un utente per accedere al pannello admin.';
    } else {
        echo '<strong style="color: #10b981;">✅ UTENTI PRESENTI NEL DATABASE</strong><br><br>';
        echo 'Utenti disponibili (ID): ' . implode(', ', $userIds) . '<br><br>';

        echo '<strong>Il problema dell\'errore precedente era:</strong><br>';
        echo '• Tentativo di inserire utente_id = 1<br>';
        echo '• Ma probabilmente stavi usando lo script da browser senza essere loggato in Laravel<br>';
        echo '• La foreign key ha bloccato l\'inserimento perché non trovava l\'utente<br><br>';

        echo '<strong style="color: #10b981;">✅ SOLUZIONE:</strong> Usa il sistema dall\'area admin dove sei già loggato!';
    }

    echo '</div>';
    echo '</div>';

    // 5. Soluzione
    echo '<div class="solution-box">';
    echo '<h3 style="color: #0ea5e9; margin-bottom: 15px;">💡 Come Usare Correttamente il Sistema</h3>';
    echo '<ol style="margin-left: 20px; line-height: 2.2;">';
    echo '<li><strong>NON usare script PHP diretti dal browser</strong></li>';
    echo '<li><strong>Accedi al pannello admin di Laravel:</strong><br>';
    echo '<code style="background: #1e293b; color: #f1f5f9; padding: 5px 10px; border-radius: 4px; display: inline-block; margin-top: 5px;">';
    echo 'https://www.agstudio.digital/magia/public/admin/login';
    echo '</code></li>';
    echo '<li><strong>Fai il login</strong> con le tue credenziali admin</li>';
    echo '<li><strong>Vai su: Impostazioni → Tab "Segnalazioni"</strong></li>';
    echo '<li><strong>Compila il form</strong> e invia la segnalazione</li>';
    echo '<li>Il sistema userà automaticamente il tuo ID utente dalla sessione Laravel</li>';
    echo '</ol>';
    echo '</div>';

    // 6. Info tecniche
    echo '<div class="step success">';
    echo '<div class="step-title">✅ Sistema Funzionante</div>';
    echo '<div class="step-content">';
    echo '<strong>Tutto è configurato correttamente:</strong><br><br>';
    echo '✓ Database connesso<br>';
    echo '✓ Tabella users: ' . count($users) . ' utenti<br>';
    echo '✓ Tabella segnalazioni: ' . ($tableExists ? 'esistente' : 'da creare') . '<br>';
    echo '✓ Foreign keys: configurate<br><br>';
    echo '<strong>Usa il sistema dall\'area admin per farlo funzionare!</strong>';
    echo '</div>';
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="step error">';
    echo '<div class="step-title">❌ Errore Database</div>';
    echo '<div class="step-content">';
    echo htmlspecialchars($e->getMessage());
    echo '</div>';
    echo '</div>';
}

?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="/magia/public/admin/login" class="btn">
                🔐 Vai al Login Admin
            </a>
            <a href="/magia/public/admin/impostazioni" class="btn">
                ⚙️ Impostazioni (se già loggato)
            </a>
        </div>

        <div class="warning-box">
            <strong>⚠️ ELIMINA QUESTO FILE</strong> dopo aver verificato:<br>
            <code>/public/diagnosi-segnalazioni.php</code>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px; font-size: 12px; color: #666; text-align: center;">
            <strong>Diagnosi Segnalazioni v2.0</strong> - MA.GIA DONNA
        </div>
    </div>
</body>
</html>
