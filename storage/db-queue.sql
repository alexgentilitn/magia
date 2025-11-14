-- DB QUEUE: Scrivi qui le query SQL da eseguire (una per riga)
-- Il cron le eseguirà automaticamente ogni minuto
-- Risultati in: storage/logs/db-results.json

-- TEST INIZIALE: Creazione e eliminazione tabella test_claude
CREATE TABLE test_claude (id INT PRIMARY KEY AUTO_INCREMENT, nome VARCHAR(100), descrizione TEXT, data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
INSERT INTO test_claude (nome, descrizione) VALUES ('Test 1', 'Primo record creato automaticamente'), ('Test 2', 'Secondo record'), ('Test 3', 'Terzo record')
SELECT * FROM test_claude
SELECT COUNT(*) as totale FROM test_claude
DROP TABLE test_claude
SHOW TABLES LIKE 'test_claude'
