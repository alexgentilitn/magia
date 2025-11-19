-- ============================================
-- Script SQL: Impostazioni PayPal in Database
-- Database: agstudiodiital_magia
-- Data: 2025-11-17
-- Descrizione: Aggiunge configurazioni PayPal alla tabella impostazioni
-- ============================================

-- STEP 1: Inserisci impostazioni PayPal
INSERT INTO `impostazioni` (`chiave`, `valore`, `gruppo`, `tipo`, `descrizione`, `criptata`, `created_at`, `updated_at`) VALUES
('paypal_mode', 'sandbox', 'paypal', 'select', 'Modalità PayPal (sandbox per test, live per produzione)', 0, NOW(), NOW()),
('paypal_client_id', '', 'paypal', 'text', 'PayPal Client ID (da PayPal Developer Dashboard)', 1, NOW(), NOW()),
('paypal_client_secret', '', 'paypal', 'password', 'PayPal Client Secret (da PayPal Developer Dashboard)', 1, NOW(), NOW()),
('paypal_importo_registrazione', '50.00', 'paypal', 'number', 'Importo quota registrazione in euro', 0, NOW(), NOW()),
('paypal_attivo', '1', 'paypal', 'boolean', 'Abilita pagamenti con PayPal', 0, NOW(), NOW()),
('bonifico_attivo', '1', 'pagamento', 'boolean', 'Abilita pagamenti con Bonifico', 0, NOW(), NOW()),
('bonifico_iban', 'IT00X0000000000000000000000', 'pagamento', 'text', 'IBAN per bonifici bancari', 0, NOW(), NOW()),
('bonifico_intestatario', 'MA.GIA DONNA S.R.L.', 'pagamento', 'text', 'Intestatario conto bancario', 0, NOW(), NOW()),
('bonifico_banca', 'Banca Esempio', 'pagamento', 'text', 'Nome banca', 0, NOW(), NOW());

-- STEP 2: Verifica inserimento
SELECT * FROM `impostazioni` WHERE `gruppo` IN ('paypal', 'pagamento') ORDER BY `gruppo`, `chiave`;

-- ============================================
-- COMPLETATO
-- ============================================
-- Dopo aver eseguito questo script:
-- 1. Le impostazioni PayPal saranno configurabili dall'interfaccia admin
-- 2. I valori sensibili (Client ID e Secret) saranno criptati automaticamente
-- 3. L'admin potrà testare la connessione PayPal dal pannello
-- ============================================
