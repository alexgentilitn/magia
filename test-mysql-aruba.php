<?php

echo "🔌 TEST CONNESSIONE MYSQL ARUBA\n";
echo "================================\n\n";

$hostname = '31.11.39.98';
$database = 'Sql1670764_4';
$username = 'Sql1670764';
$password = '$PippoFormaggio!00';

echo "Host: {$hostname}\n";
echo "Database: {$database}\n";
echo "Username: {$username}\n\n";

// Test 1: MySQLi
echo "Test 1: MySQLi\n";
echo "──────────────\n";
try {
    ini_set('default_socket_timeout', 10);
    ini_set('mysql.connect_timeout', 10);

    $connection = @new mysqli($hostname, $username, $password, $database);

    if ($connection->connect_error) {
        echo "❌ ERRORE: " . $connection->connect_error . "\n";
        echo "   Codice: " . $connection->connect_errno . "\n\n";
    } else {
        echo "✅ CONNESSIONE RIUSCITA!\n";
        echo "   Server: " . $connection->server_info . "\n";

        // Test query
        $result = $connection->query("SHOW TABLES");
        if ($result) {
            echo "   ✅ Query OK! Tabelle trovate: " . $result->num_rows . "\n";
            while ($row = $result->fetch_array()) {
                echo "      - {$row[0]}\n";
            }
        }

        $connection->close();

        echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ DATABASE MYSQL RAGGIUNGIBILE!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        exit(0);
    }
} catch (Exception $e) {
    echo "❌ Eccezione: " . $e->getMessage() . "\n\n";
}

// Test 2: PDO
echo "Test 2: PDO\n";
echo "───────────\n";
try {
    $dsn = "mysql:host={$hostname};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10
    ]);

    echo "✅ CONNESSIONE RIUSCITA!\n";

    // Test query
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   ✅ Tabelle trovate: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "      - {$table}\n";
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ DATABASE MYSQL RAGGIUNGIBILE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    exit(0);

} catch (PDOException $e) {
    echo "❌ ERRORE: " . $e->getMessage() . "\n\n";
}

echo "❌ Nessun metodo ha avuto successo.\n";
