<?php
/**
 * Setup Tabella Segnalazioni
 * Crea la tabella segnalazioni nel database
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
    <title>Setup Segnalazioni - MA.GIA DONNA</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }
        h1 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
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
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
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
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .icon { margin-right: 8px; }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            color: #856404;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Setup Tabella Segnalazioni</h1>
        <p class="subtitle">Creazione tabella per sistema di segnalazione bug/suggerimenti</p>

<?php

try {
    // Connessione database
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo '<div class="step success">';
    echo '<div class="step-title">✅ 1. Connessione Database</div>';
    echo '<div class="step-content">Connesso con successo a: <strong>' . htmlspecialchars($dbName) . '</strong></div>';
    echo '</div>';

    // Verifica se la tabella esiste già
    $stmt = $pdo->query("SHOW TABLES LIKE 'segnalazioni'");
    $tableExists = $stmt->rowCount() > 0;

    // Gestisci azione ricrea
    if (isset($_GET['action']) && $_GET['action'] == 'ricrea' && $tableExists) {
        echo '<div class="step warning">';
        echo '<div class="step-title">🔄 Eliminazione Tabella Esistente</div>';
        echo '<div class="step-content">Eliminazione della tabella esistente in corso...</div>';
        echo '</div>';

        try {
            // Elimina foreign keys prima
            $pdo->exec("ALTER TABLE `segnalazioni` DROP FOREIGN KEY `fk_segnalazioni_utente`");
            $pdo->exec("ALTER TABLE `segnalazioni` DROP FOREIGN KEY `fk_segnalazioni_risolto_da`");
        } catch (PDOException $e) {
            // Ignora errori se le FK non esistono
        }

        $pdo->exec("DROP TABLE IF EXISTS `segnalazioni`");
        $tableExists = false;

        echo '<div class="step success">';
        echo '<div class="step-title">✅ Tabella Eliminata</div>';
        echo '<div class="step-content">La vecchia tabella è stata eliminata. Ora ricreo con le foreign keys corrette...</div>';
        echo '</div>';
    }

    if ($tableExists) {
        echo '<div class="step warning">';
        echo '<div class="step-title">⚠️ 2. Verifica Tabella</div>';
        echo '<div class="step-content">La tabella <strong>segnalazioni</strong> esiste già nel database!</div>';
        echo '</div>';

        // Mostra struttura esistente
        $stmt = $pdo->query("DESCRIBE segnalazioni");
        $columns = $stmt->fetchAll();

        echo '<div class="step">';
        echo '<div class="step-title">📋 Struttura Attuale</div>';
        echo '<table>';
        echo '<thead><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>';
        echo '<tbody>';
        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($col['Field']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Key']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';

        // Conta record
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM segnalazioni");
        $count = $stmt->fetch()['count'];

        echo '<div class="step">';
        echo '<div class="step-title">📊 Dati</div>';
        echo '<div class="step-content">La tabella contiene <strong>' . $count . '</strong> segnalazioni.</div>';
        echo '</div>';

        echo '<div class="step success">';
        echo '<div class="step-title">✅ Risultato</div>';
        echo '<div class="step-content">';
        echo '<strong>La tabella esiste già!</strong><br><br>';
        echo '<strong>⚠️ ATTENZIONE:</strong> Se hai creato questa tabella prima delle correzioni, ';
        echo 'le foreign keys puntano alla tabella sbagliata (<code>users</code> invece di <code>utenti</code>).<br><br>';
        echo '<strong>Soluzione:</strong> ';
        echo '<a href="?action=ricrea" onclick="return confirm(\'Sei sicuro? Questo eliminerà tutte le segnalazioni esistenti!\')" ';
        echo 'style="display: inline-block; padding: 8px 16px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">';
        echo '🔄 Elimina e Ricrea Tabella con Foreign Keys Corrette</a>';
        echo '</div>';
        echo '</div>';

    } else {
        echo '<div class="step">';
        echo '<div class="step-title">ℹ️ 2. Verifica Tabella</div>';
        echo '<div class="step-content">La tabella <strong>segnalazioni</strong> non esiste. Procedo con la creazione...</div>';
        echo '</div>';

        // Crea la tabella con foreign keys corrette su 'utenti'
        $sql = "CREATE TABLE `segnalazioni` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `utente_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID dell\'utente che ha creato la segnalazione',
            `titolo` varchar(255) NOT NULL COMMENT 'Titolo della segnalazione',
            `descrizione` text NOT NULL COMMENT 'Descrizione dettagliata del problema o suggerimento',
            `tipo` enum('bug','suggerimento','altro') NOT NULL DEFAULT 'bug' COMMENT 'Tipo di segnalazione',
            `priorita` enum('bassa','media','alta') NOT NULL DEFAULT 'media' COMMENT 'Priorità della segnalazione',
            `stato` enum('aperta','in_lavorazione','risolta') NOT NULL DEFAULT 'aperta' COMMENT 'Stato attuale',
            `risposta` text DEFAULT NULL COMMENT 'Risposta/soluzione del Super Admin',
            `email_notifica` varchar(255) DEFAULT NULL COMMENT 'Email per ricevere notifiche di cambio stato',
            `risolto_da` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID utente che ha risolto',
            `risolto_il` timestamp NULL DEFAULT NULL COMMENT 'Data e ora risoluzione',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_segnalazioni_utente` (`utente_id`),
            KEY `idx_segnalazioni_stato` (`stato`),
            KEY `idx_segnalazioni_tipo` (`tipo`),
            KEY `idx_segnalazioni_created` (`created_at`),
            CONSTRAINT `fk_segnalazioni_utente` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_segnalazioni_risolto_da` FOREIGN KEY (`risolto_da`) REFERENCES `utenti` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);

        echo '<div class="step success">';
        echo '<div class="step-title">✅ 3. Creazione Tabella</div>';
        echo '<div class="step-content">Tabella <strong>segnalazioni</strong> creata con successo!</div>';
        echo '</div>';

        // Verifica creazione
        $stmt = $pdo->query("DESCRIBE segnalazioni");
        $columns = $stmt->fetchAll();

        echo '<div class="step success">';
        echo '<div class="step-title">📋 4. Verifica Struttura</div>';
        echo '<table>';
        echo '<thead><tr><th>Campo</th><th>Tipo</th><th>Descrizione</th></tr></thead>';
        echo '<tbody>';

        $descriptions = [
            'id' => 'ID univoco',
            'utente_id' => 'Utente che ha creato la segnalazione',
            'titolo' => 'Titolo breve della segnalazione',
            'descrizione' => 'Descrizione dettagliata',
            'tipo' => 'Bug, Suggerimento o Altro',
            'priorita' => 'Bassa, Media o Alta',
            'stato' => 'Aperta, In Lavorazione o Risolta',
            'risposta' => 'Risposta del Super Admin',
            'email_notifica' => 'Email per ricevere notifiche',
            'risolto_da' => 'Utente che ha risolto',
            'risolto_il' => 'Data risoluzione',
            'created_at' => 'Data creazione',
            'updated_at' => 'Data ultimo aggiornamento',
        ];

        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($col['Field']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($descriptions[$col['Field']] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';

        echo '<div class="step success">';
        echo '<div class="step-title">🎉 Completato!</div>';
        echo '<div class="step-content">';
        echo '<strong>La tabella segnalazioni è stata creata con successo!</strong><br><br>';
        echo 'Puoi ora utilizzare il sistema di segnalazioni da:<br>';
        echo '<strong>Admin → Impostazioni → Tab "Segnalazioni"</strong>';
        echo '</div>';
        echo '</div>';
    }

    // Mostra funzionalità disponibili
    echo '<div class="step">';
    echo '<div class="step-title">📌 Funzionalità Disponibili</div>';
    echo '<div class="step-content">';
    echo '<ul style="margin-left: 20px; line-height: 2;">';
    echo '<li>✅ <strong>Creazione segnalazioni</strong> - Gli admin possono segnalare bug e suggerimenti</li>';
    echo '<li>✅ <strong>3 tipi</strong> - Bug, Suggerimento, Altro</li>';
    echo '<li>✅ <strong>3 priorità</strong> - Bassa, Media, Alta</li>';
    echo '<li>✅ <strong>3 stati</strong> - Aperta, In Lavorazione, Risolta</li>';
    echo '<li>✅ <strong>Gestione Super Admin</strong> - Cambia stato e aggiungi risposte</li>';
    echo '<li>✅ <strong>Notifiche email</strong> - Email automatica quando cambia lo stato</li>';
    echo '<li>✅ <strong>Statistiche</strong> - Dashboard con contatori in tempo reale</li>';
    echo '<li>✅ <strong>Timeline</strong> - Tracciamento date creazione/risoluzione</li>';
    echo '</ul>';
    echo '</div>';
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="step error">';
    echo '<div class="step-title">❌ Errore Database</div>';
    echo '<div class="step-content">';
    echo '<strong>Impossibile connettersi al database:</strong><br>';
    echo htmlspecialchars($e->getMessage());
    echo '</div>';
    echo '</div>';

    echo '<div class="step">';
    echo '<div class="step-title">🔧 Configurazione Tentata</div>';
    echo '<div class="step-content">';
    echo '<strong>Host:</strong> ' . htmlspecialchars($dbHost) . '<br>';
    echo '<strong>Port:</strong> ' . htmlspecialchars($dbPort) . '<br>';
    echo '<strong>Database:</strong> ' . htmlspecialchars($dbName) . '<br>';
    echo '<strong>Username:</strong> ' . htmlspecialchars($dbUser) . '<br>';
    echo '</div>';
    echo '</div>';
}

?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="/magia/public/admin/impostazioni" class="btn">
                <span class="icon">📊</span>
                Vai alle Impostazioni
            </a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn">
                <span class="icon">🔄</span>
                Ricarica
            </a>
        </div>

        <div class="warning-box">
            <strong>⚠️ IMPORTANTE - ELIMINA QUESTO FILE</strong><br>
            Dopo aver verificato che tutto funziona correttamente, elimina questo file per sicurezza:<br>
            <code>/public/setup-segnalazioni.php</code>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px; font-size: 12px; color: #666; text-align: center;">
            <strong>Setup Segnalazioni v1.0</strong> - MA.GIA DONNA<br>
            Sistema di segnalazione bug/suggerimenti integrato
        </div>
    </div>
</body>
</html>
