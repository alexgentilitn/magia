#!/bin/bash

echo "🧪 TEST CREAZIONE TABELLA - Database CLI"
echo "========================================"
echo ""

echo "Step 1: Creazione tabella test_claude"
php db-cli.php "CREATE TABLE test_claude (id INT PRIMARY KEY AUTO_INCREMENT, nome VARCHAR(100), descrizione TEXT, data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
echo ""

echo "Step 2: Verifica esistenza tabella"
php db-cli.php "SHOW TABLES LIKE 'test_claude'"
echo ""

echo "Step 3: Inserimento dati di test"
php db-cli.php "INSERT INTO test_claude (nome, descrizione) VALUES ('Test 1', 'Primo record automatico'), ('Test 2', 'Secondo record automatico'), ('Test 3', 'Terzo record automatico')"
echo ""

echo "Step 4: Lettura dati"
php db-cli.php "SELECT * FROM test_claude"
echo ""

echo "Step 5: Conta record"
php db-cli.php "SELECT COUNT(*) as totale FROM test_claude"
echo ""

echo "Step 6: Eliminazione tabella"
php db-cli.php "DROP TABLE test_claude"
echo ""

echo "Step 7: Verifica eliminazione"
php db-cli.php "SHOW TABLES LIKE 'test_claude'"
echo ""

echo "✅ Test completato!"
