<?php
/**
 * Script: Aggiorna tabella clienti per permettere import con dati minimi
 * Esegui questo file via browser per rendere nullable i campi non essenziali
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

echo "<h1>🔧 Aggiornamento Tabella Clienti</h1>";
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

    // Array di modifiche da eseguire
    $modifiche = [
        'codice_fiscale' => "ALTER TABLE `clienti` MODIFY `codice_fiscale` VARCHAR(16) NULL",
        'email' => "ALTER TABLE `clienti` MODIFY `email` VARCHAR(255) NULL",
        'telefono_mobile' => "ALTER TABLE `clienti` MODIFY `telefono_mobile` VARCHAR(20) NULL",
        'data_nascita' => "ALTER TABLE `clienti` MODIFY `data_nascita` DATE NULL",
        'indirizzo' => "ALTER TABLE `clienti` MODIFY `indirizzo` VARCHAR(200) NULL",
        'citta' => "ALTER TABLE `clienti` MODIFY `citta` VARCHAR(100) NULL",
        'provincia' => "ALTER TABLE `clienti` MODIFY `provincia` VARCHAR(2) NULL",
        'cap' => "ALTER TABLE `clienti` MODIFY `cap` VARCHAR(5) NULL",
    ];

    echo "<h2>📋 Modifiche da eseguire:</h2>";
    echo "<ul>";
    foreach ($modifiche as $campo => $sql) {
        echo "<li>Campo <strong>{$campo}</strong> → NULL</li>";
    }
    echo "</ul>";
    echo "<hr>";

    // Rimuovi UNIQUE constraint da codice_fiscale (impedisce multipli NULL)
    echo "<h2>🔓 Rimozione UNIQUE constraint da codice_fiscale</h2>";
    try {
        // Prima verifica se esiste l'index
        $checkIndex = $pdo->query("SHOW INDEX FROM `clienti` WHERE Column_name = 'codice_fiscale' AND Non_unique = 0")->fetch();

        if ($checkIndex) {
            $indexName = $checkIndex['Key_name'];
            echo "Index trovato: <strong>{$indexName}</strong><br>";

            // Rimuovi l'index UNIQUE
            $pdo->exec("ALTER TABLE `clienti` DROP INDEX `{$indexName}`");
            echo "✅ UNIQUE constraint rimosso da codice_fiscale<br>";
        } else {
            echo "ℹ️ Nessun UNIQUE constraint trovato su codice_fiscale<br>";
        }
    } catch (PDOException $e) {
        echo "⚠️ Errore rimozione constraint: " . $e->getMessage() . "<br>";
    }
    echo "<br>";

    // Rimuovi UNIQUE constraint da email (impedisce multipli NULL)
    echo "<h2>🔓 Rimozione UNIQUE constraint da email</h2>";
    try {
        // Prima verifica se esiste l'index
        $checkIndex = $pdo->query("SHOW INDEX FROM `clienti` WHERE Column_name = 'email' AND Non_unique = 0")->fetch();

        if ($checkIndex) {
            $indexName = $checkIndex['Key_name'];
            echo "Index trovato: <strong>{$indexName}</strong><br>";

            // Rimuovi l'index UNIQUE
            $pdo->exec("ALTER TABLE `clienti` DROP INDEX `{$indexName}`");
            echo "✅ UNIQUE constraint rimosso da email<br>";
        } else {
            echo "ℹ️ Nessun UNIQUE constraint trovato su email<br>";
        }
    } catch (PDOException $e) {
        echo "⚠️ Errore rimozione constraint: " . $e->getMessage() . "<br>";
    }
    echo "<br>";

    // Esegui le modifiche
    echo "<h2>⚙️ Esecuzione modifiche</h2>";
    $modificheEseguite = 0;
    $modificheFallite = 0;

    foreach ($modifiche as $campo => $sql) {
        try {
            echo "<div style='margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 5px;'>";
            echo "<strong>{$campo}</strong><br>";
            echo "<code style='font-size: 11px;'>{$sql}</code><br>";

            $pdo->exec($sql);

            echo "<span style='color: green;'>✅ Modifica eseguita con successo</span>";
            echo "</div>";

            $modificheEseguite++;
        } catch (PDOException $e) {
            echo "<span style='color: red;'>❌ Errore: " . $e->getMessage() . "</span>";
            echo "</div>";

            $modificheFallite++;
        }
    }

    echo "<hr>";
    echo "<h2>📊 Riepilogo</h2>";
    echo "✅ Modifiche riuscite: <strong>{$modificheEseguite}</strong><br>";
    echo "❌ Modifiche fallite: <strong>{$modificheFallite}</strong><br><br>";

    if ($modificheEseguite === count($modifiche)) {
        echo "<h2 style='color: green;'>🎉 TUTTE LE MODIFICHE COMPLETATE CON SUCCESSO!</h2>";
    } else {
        echo "<h2 style='color: orange;'>⚠️ Alcune modifiche potrebbero essere fallite</h2>";
    }

    // Verifica finale
    echo "<hr>";
    echo "<h2>🔍 Verifica Finale</h2>";
    $columns = $pdo->query("DESCRIBE clienti")->fetchAll();

    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>NULL</th><th>Key</th><th>Default</th></tr>";

    foreach ($columns as $column) {
        $highlight = in_array($column['Field'], array_keys($modifiche)) ? "background-color: #ffffcc;" : "";
        echo "<tr style='{$highlight}'>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>" . ($column['Null'] === 'YES' ? '✅ YES' : '❌ NO') . "</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr>";
    echo "<p style='color: green; font-size: 18px;'><strong>✅ Operazione completata!</strong></p>";
    echo "<p>Ora puoi importare le pesate fornendo solo nome, cognome e peso.</p>";

    echo "<hr>";
    echo "<p style='color: red;'><strong>⚠️ IMPORTANTE: Elimina questo file dopo l'uso!</strong></p>";
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
