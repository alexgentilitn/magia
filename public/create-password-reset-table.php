<?php
/**
 * Script: Crea tabella password_reset_tokens
 * Esegui questo file via browser per creare la tabella nel database
 */

// Carica configurazione database da .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Carica .env
loadEnv(__DIR__ . '/../.env');

// Connessione al database
$host = getenv('DB_HOST') ?: 'localhost';
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

echo "<h1>🔧 Creazione Tabella password_reset_tokens</h1>";
echo "<hr>";

try {
    // Connetti al database
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<h2>✅ Connessione al database riuscita</h2>";
    echo "Database: <strong>{$database}</strong><br>";
    echo "Host: <strong>{$host}</strong><br><br>";

    // Verifica se la tabella esiste già
    $checkTable = $pdo->query("SHOW TABLES LIKE 'password_reset_tokens'")->fetch();

    if ($checkTable) {
        echo "<h2>ℹ️ La tabella 'password_reset_tokens' esiste già</h2>";

        // Mostra struttura tabella
        $structure = $pdo->query("DESCRIBE password_reset_tokens")->fetchAll();
        echo "<h3>Struttura attuale:</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            echo "<td><strong>{$column['Field']}</strong></td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<hr>";
        echo "<p style='color: green;'><strong>✅ Tabella già presente - nessuna azione necessaria!</strong></p>";

    } else {
        echo "<h2>📋 Creazione tabella in corso...</h2>";

        // Crea la tabella
        $sql = "CREATE TABLE `password_reset_tokens` (
            `email` VARCHAR(255) NOT NULL,
            `token` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);

        echo "<h2 style='color: green;'>✅ Tabella 'password_reset_tokens' creata con successo!</h2>";

        // Mostra struttura tabella creata
        $structure = $pdo->query("DESCRIBE password_reset_tokens")->fetchAll();
        echo "<h3>Struttura creata:</h3>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            echo "<td><strong>{$column['Field']}</strong></td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<hr>";
        echo "<p style='color: green; font-size: 18px;'><strong>🎉 OPERAZIONE COMPLETATA CON SUCCESSO!</strong></p>";
        echo "<p>La tabella è stata creata correttamente. Ora il sistema di reset password è attivo.</p>";
    }

    echo "<hr>";
    echo "<h2>📋 Verifica Finale</h2>";

    // Conta eventuali token esistenti
    $count = $pdo->query("SELECT COUNT(*) as total FROM password_reset_tokens")->fetch();
    echo "Token presenti nel database: <strong>{$count['total']}</strong><br>";

    echo "<hr>";
    echo "<h2>🧪 Prossimi Passi</h2>";
    echo "<ol>";
    echo "<li>Testa il recupero password su: <a href='../admin/password/forgot'>../admin/password/forgot</a></li>";
    echo "<li>Usa lo script di debug per testare l'invio email: <a href='debug-reset-password.php'>debug-reset-password.php</a></li>";
    echo "<li><strong style='color: red;'>Elimina questo file dopo l'uso!</strong></li>";
    echo "</ol>";

    echo "<hr>";
    echo "<p style='color: red; font-size: 16px;'><strong>⚠️ IMPORTANTE: Elimina questo file per motivi di sicurezza!</strong></p>";

    if (isset($_GET['delete'])) {
        if (unlink(__FILE__)) {
            echo "<h2 style='color: green;'>✅ File eliminato con successo!</h2>";
        } else {
            echo "<h2 style='color: red;'>❌ Impossibile eliminare il file. Eliminalo manualmente via FTP.</h2>";
        }
    } else {
        echo "<p><a href='?delete=1' style='color: red; font-weight: bold; font-size: 16px;'>🗑️ CLICCA QUI PER ELIMINARE QUESTO FILE</a></p>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ ERRORE DI CONNESSIONE AL DATABASE</h2>";
    echo "<p><strong>Messaggio:</strong> {$e->getMessage()}</p>";
    echo "<p><strong>Codice:</strong> {$e->getCode()}</p>";

    echo "<hr>";
    echo "<h3>🔍 Possibili Cause:</h3>";
    echo "<ul>";
    echo "<li>Credenziali database errate nel file .env</li>";
    echo "<li>MySQL non raggiungibile</li>";
    echo "<li>Database '{$database}' non esiste</li>";
    echo "<li>Permessi insufficienti per l'utente '{$username}'</li>";
    echo "</ul>";

    echo "<h3>📝 Configurazione attuale (.env):</h3>";
    echo "DB_HOST: <strong>{$host}</strong><br>";
    echo "DB_DATABASE: <strong>{$database}</strong><br>";
    echo "DB_USERNAME: <strong>{$username}</strong><br>";
    echo "DB_PASSWORD: <strong>" . (empty($password) ? '(vuota)' : '(impostata)') . "</strong><br>";
}
