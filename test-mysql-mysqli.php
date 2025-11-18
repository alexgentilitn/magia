<?php

echo "🔌 TEST CONNESSIONE MYSQL con MySQLi\n";
echo "=====================================\n\n";

$hosts = [
    '212.35.217.66',
    'mysql.agstudio.digital',
    'db.agstudio.digital',
    'ftp.agstudio.digital',
];

$database = 'agstudiodiital_magia';
$username = 'agstudiodiital_agstudiomagia';
$password = '$Magia2015!';

foreach ($hosts as $hostname) {
    echo "🔍 Provo host: {$hostname}\n";

    // Imposta timeout più breve
    ini_set('default_socket_timeout', 5);
    ini_set('mysql.connect_timeout', 5);

    try {
        $connection = @new mysqli($hostname, $username, $password, $database);

        if ($connection->connect_error) {
            echo "   ❌ ERRORE: " . $connection->connect_error . "\n";
            echo "   Codice errore: " . $connection->connect_errno . "\n\n";
            continue;
        }
    } catch (Exception $e) {
        echo "   ❌ ERRORE: " . $e->getMessage() . "\n\n";
        continue;
    }

    if ($connection && !$connection->connect_error) {
        echo "   ✅ CONNESSIONE RIUSCITA!\n";
        echo "   Host: " . $connection->server_info . "\n";
        echo "   Database: " . $database . "\n";

        // Test query
        $result = $connection->query("SELECT COUNT(*) as count FROM utenti");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "   ✅ Query OK! Utenti: {$row['count']}\n";
        }

        $connection->close();

        echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ SOLUZIONE TROVATA!\n";
        echo "✅ MySQL remoto accessibile!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "📋 Configurazione per .env:\n";
        echo "DB_CONNECTION=mysql\n";
        echo "DB_HOST={$hostname}\n";
        echo "DB_PORT=3306\n";
        echo "DB_DATABASE={$database}\n";
        echo "DB_USERNAME={$username}\n";
        echo "DB_PASSWORD={$password}\n";

        exit(0);
    }
}

echo "❌ Nessun host raggiungibile.\n";
