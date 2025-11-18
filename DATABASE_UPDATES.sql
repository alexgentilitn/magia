-- ============================================
-- DATABASE UPDATES - MAGIA DONNA
-- ============================================
-- Da eseguire in phpMyAdmin per implementare tutte le nuove funzionalità
-- Data: 2025-11-18
-- ============================================

-- ============================================
-- 1. GIORNATA DI PROVA - Aggiungi campo tipo_cliente
-- ============================================
ALTER TABLE `clienti` 
ADD COLUMN `tipo_cliente` ENUM('prova', 'effettiva') DEFAULT 'effettiva' 
AFTER `stato_cliente`;

-- Aggiungi commento alla colonna
ALTER TABLE `clienti` 
MODIFY COLUMN `tipo_cliente` ENUM('prova', 'effettiva') DEFAULT 'effettiva' 
COMMENT 'Tipo di cliente: prova (giornata prova) o effettiva (cliente pagante)';


-- ============================================
-- 2. PARAMETRI CORPOREI - Crea tabella completa
-- ============================================
CREATE TABLE IF NOT EXISTS `parametri_corporei` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cliente_id` BIGINT UNSIGNED NOT NULL,
  `data_rilevazione` DATE NOT NULL,
  `peso` DECIMAL(5,2) NOT NULL COMMENT 'Peso in kg',
  `altezza` DECIMAL(5,2) NOT NULL COMMENT 'Altezza in cm',
  `circonferenza_vita` DECIMAL(5,2) NULL COMMENT 'Circonferenza vita in cm',
  `circonferenza_fianchi` DECIMAL(5,2) NULL COMMENT 'Circonferenza fianchi in cm',
  `circonferenza_braccia` DECIMAL(5,2) NULL COMMENT 'Circonferenza braccia in cm',
  `circonferenza_cosce` DECIMAL(5,2) NULL COMMENT 'Circonferenza cosce in cm',
  `massa_grassa` DECIMAL(5,2) NULL COMMENT 'Massa grassa %',
  `massa_magra` DECIMAL(5,2) NULL COMMENT 'Massa magra %',
  `acqua_corporea` DECIMAL(5,2) NULL COMMENT 'Acqua corporea %',
  `metabolismo_basale` DECIMAL(6,2) NULL COMMENT 'Metabolismo basale kcal/giorno',
  `massa_ossea` DECIMAL(4,2) NULL COMMENT 'Massa ossea kg',
  `grasso_viscerale` DECIMAL(4,2) NULL COMMENT 'Livello grasso viscerale',
  `proteine` DECIMAL(4,2) NULL COMMENT 'Proteine %',
  `eta_metabolica` INT NULL COMMENT 'Età metabolica anni',
  `bmi` DECIMAL(5,2) NULL COMMENT 'BMI calcolato automaticamente',
  `note` TEXT NULL,
  `rilevato_da` VARCHAR(255) NULL COMMENT 'Nome operatore che ha rilevato i dati',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`cliente_id`) REFERENCES `clienti`(`id`) ON DELETE CASCADE,
  INDEX `idx_cliente_data` (`cliente_id`, `data_rilevazione`),
  INDEX `idx_data_rilevazione` (`data_rilevazione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Storico parametri corporei delle clienti';


-- ============================================
-- 3. LOG ATTIVITÀ AMMINISTRATORI
-- ============================================
CREATE TABLE IF NOT EXISTS `log_attivita` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `utente_id` BIGINT UNSIGNED NOT NULL,
  `azione` VARCHAR(100) NOT NULL COMMENT 'Tipo azione: create, update, delete, login, etc',
  `descrizione` TEXT NOT NULL COMMENT 'Descrizione dettagliata azione',
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `dati_modificati` JSON NULL COMMENT 'Dati modificati in formato JSON',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`utente_id`) REFERENCES `utenti`(`id`) ON DELETE CASCADE,
  INDEX `idx_utente` (`utente_id`),
  INDEX `idx_azione` (`azione`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Log di tutte le attività degli amministratori per audit e sicurezza';


-- ============================================
-- 4. DEADLINE PRENOTAZIONE LEZIONI
-- ============================================
ALTER TABLE `lezioni` 
ADD COLUMN `deadline_prenotazione` INT UNSIGNED NULL DEFAULT 2 
AFTER `note` 
COMMENT 'Ore prima della lezione entro cui è possibile prenotare (default: 2 ore)';


-- ============================================
-- 5. GDPR - Campi aggiuntivi consensi
-- ============================================
-- Aggiungi campi per gestione consensi GDPR granulari
ALTER TABLE `clienti` 
ADD COLUMN `consenso_profilazione` TINYINT(1) DEFAULT 0 AFTER `consenso_marketing`,
ADD COLUMN `consenso_profilazione_data` DATETIME NULL AFTER `consenso_profilazione`,
ADD COLUMN `consenso_terze_parti` TINYINT(1) DEFAULT 0 AFTER `consenso_profilazione_data`,
ADD COLUMN `consenso_terze_parti_data` DATETIME NULL AFTER `consenso_terze_parti`;

-- Commenti
ALTER TABLE `clienti` 
MODIFY COLUMN `consenso_profilazione` TINYINT(1) DEFAULT 0 
COMMENT 'Consenso per profilazione e analisi comportamentale',
MODIFY COLUMN `consenso_terze_parti` TINYINT(1) DEFAULT 0 
COMMENT 'Consenso per condivisione dati con terze parti (partner, fornitori)';


-- ============================================
-- 6. TABELLA GESTIONE PREFERENZE EMAIL (GDPR)
-- ============================================
CREATE TABLE IF NOT EXISTS `preferenze_email` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `utente_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `ricevi_newsletter` TINYINT(1) DEFAULT 1,
  `ricevi_promemoria` TINYINT(1) DEFAULT 1,
  `ricevi_auguri` TINYINT(1) DEFAULT 1,
  `ricevi_marketing` TINYINT(1) DEFAULT 1,
  `ricevi_notifiche_sistema` TINYINT(1) DEFAULT 1,
  `disiscritto_tutto` TINYINT(1) DEFAULT 0 COMMENT 'Utente disiscritto da TUTTE le email',
  `token_disiscrizione` VARCHAR(64) UNIQUE NULL COMMENT 'Token univoco per link disiscrizione email',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`utente_id`) REFERENCES `utenti`(`id`) ON DELETE CASCADE,
  INDEX `idx_disiscritto` (`disiscritto_tutto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Preferenze email utente per conformità GDPR';


-- ============================================
-- 7. BENEFIT PROGRAMMI - Sistema differenziato
-- ============================================
CREATE TABLE IF NOT EXISTS `programma_benefit` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `programma_id` BIGINT UNSIGNED NOT NULL,
  `benefit_tipo` ENUM('ricette', 'video', 'consulenza', 'eventi', 'materiale_extra', 'follow_up', 'altro') NOT NULL,
  `benefit_nome` VARCHAR(255) NOT NULL,
  `benefit_descrizione` TEXT NULL,
  `attivo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`programma_id`) REFERENCES `programmi`(`id`) ON DELETE CASCADE,
  INDEX `idx_programma` (`programma_id`),
  INDEX `idx_tipo` (`benefit_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Benefit differenziati per ogni programma';


-- ============================================
-- 8. ANALYTICS AVANZATE - Tabella conversioni
-- ============================================
CREATE TABLE IF NOT EXISTS `analytics_conversioni` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `utente_id` BIGINT UNSIGNED NULL,
  `session_id` VARCHAR(255) NOT NULL,
  `tipo_conversione` ENUM('registrazione', 'pagamento', 'prenotazione', 'completamento_profilo') NOT NULL,
  `step_completato` VARCHAR(100) NOT NULL,
  `tempo_conversione` INT NULL COMMENT 'Secondi impiegati per completare la conversione',
  `valore_conversione` DECIMAL(10,2) NULL COMMENT 'Valore economico della conversione (es. importo pagato)',
  `sorgente` VARCHAR(255) NULL COMMENT 'Da dove proviene (landing page, email, etc)',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`utente_id`) REFERENCES `utenti`(`id`) ON DELETE SET NULL,
  INDEX `idx_tipo` (`tipo_conversione`),
  INDEX `idx_session` (`session_id`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tracciamento conversioni e funnel per analytics avanzate';


-- ============================================
-- 9. REFERRAL - Aggiungi campo condivisioni
-- ============================================
ALTER TABLE `clienti` 
ADD COLUMN `condivisioni_social` INT UNSIGNED DEFAULT 0 AFTER `codice_referral` 
COMMENT 'Numero di volte che il codice referral è stato condiviso sui social';


-- ============================================
-- 10. VERIFICA E OTTIMIZZAZIONE
-- ============================================

-- Verifica integrità referenziale
SET FOREIGN_KEY_CHECKS=1;

-- Ottimizza tutte le tabelle modificate
OPTIMIZE TABLE `clienti`;
OPTIMIZE TABLE `parametri_corporei`;
OPTIMIZE TABLE `log_attivita`;
OPTIMIZE TABLE `lezioni`;
OPTIMIZE TABLE `preferenze_email`;
OPTIMIZE TABLE `programma_benefit`;
OPTIMIZE TABLE `analytics_conversioni`;


-- ============================================
-- QUERY DI VERIFICA (opzionali - per testare)
-- ============================================

-- Verifica clienti di prova
-- SELECT COUNT(*) as clienti_prova FROM clienti WHERE tipo_cliente = 'prova';

-- Verifica parametri corporei
-- SELECT COUNT(*) as parametri_salvati FROM parametri_corporei;

-- Verifica log attività
-- SELECT COUNT(*) as log_registrati FROM log_attivita;

-- Verifica preferenze email
-- SELECT COUNT(*) as preferenze_impostate FROM preferenze_email;


-- ============================================
-- FINE AGGIORNAMENTI DATABASE
-- ============================================
-- IMPORTANTE: Esegui questo script UNA SOLA VOLTA
-- Backup del database consigliato prima dell'esecuzione
-- ============================================
