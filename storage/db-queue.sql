-- DB QUEUE: Scrivi qui le query SQL da eseguire (una per riga)
-- Il cron le eseguirà automaticamente ogni minuto
-- Risultati in: storage/logs/db-results.json

-- TEST AUTOMATICO CRON - Verifica che il cron esegua automaticamente
SHOW TABLES
SELECT COUNT(*) as total_users FROM utenti
SELECT DATABASE() as current_database
