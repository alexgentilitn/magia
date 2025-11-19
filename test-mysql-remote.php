<?php

echo "🔌 TEST CONNESSIONE MYSQL REMOTA\n";
echo "=================================\n\n";

$hosts = [
    '212.35.217.66',
    'mysql.agstudio.digital',
    'db.agstudio.digital',
    'ftp.agstudio.digital',
];

$database = 'agstudiodiital_magia';
$username = 'agstudiodiital_agstudiomagia';
$password = '$Magia2015!';

foreach ($hosts as $host) {
    echo "🔍 Provo host: {$host}\n";

    try {
        // Test 1: PDO MySQL
        echo "   Test PDO MySQL...\n";
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);

        echo "   ✅ CONNESSIONE RIUSCITA!\n";

        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM utenti");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ✅ Query funziona! Utenti: {$result['count']}\n";

        // Test lettura dati
        $stmt = $pdo->query("SELECT id, nome, cognome, email FROM utenti LIMIT 3");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "   ✅ Dati letti:\n";
        foreach ($users as $user) {
            echo "      - {$user['nome']} {$user['cognome']} ({$user['email']})\n";
        }

        echo "\n   🎉 HOST FUNZIONANTE: {$host}\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ SOLUZIONE TROVATA!\n";
        echo "✅ MySQL remoto accessibile!\n";
        echo "✅ Posso usare il database direttamente!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "📋 Configurazione per .env:\n";
        echo "DB_CONNECTION=mysql\n";
        echo "DB_HOST={$host}\n";
        echo "DB_PORT=3306\n";
        echo "DB_DATABASE={$database}\n";
        echo "DB_USERNAME={$username}\n";
        echo "DB_PASSWORD={$password}\n";

        exit(0);

    } catch (PDOException $e) {
        echo "   ❌ Errore: " . $e->getMessage() . "\n\n";
    } catch (Exception $e) {
        echo "   ❌ Errore generico: " . $e->getMessage() . "\n\n";
    }
}

echo "❌ Nessun host raggiungibile.\n";
echo "Possibili cause:\n";
echo "- Firewall blocca connessioni da questo IP\n";
echo "- Host MySQL diverso da quelli provati\n";
echo "- Porta diversa da 3306\n";
