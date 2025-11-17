-- ============================================
-- Script SQL: Chat Interna e Notifiche
-- Database: agstudiodiital_magia
-- Data: 2025-11-17
-- Descrizione: Crea tabelle per sistema messaggistica e notifiche
-- ============================================

-- STEP 1: Crea tabella conversazioni
CREATE TABLE `conversazioni` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) DEFAULT NULL COMMENT 'Titolo conversazione (opzionale)',
  `tipo` enum('privata','gruppo') NOT NULL DEFAULT 'privata' COMMENT 'Tipo conversazione',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ultimo_messaggio_at` timestamp NULL DEFAULT NULL COMMENT 'Data ultimo messaggio',
  PRIMARY KEY (`id`),
  KEY `conversazioni_ultimo_messaggio_at_index` (`ultimo_messaggio_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversazioni chat tra utenti';

-- STEP 2: Crea tabella conversazione_utente (pivot)
CREATE TABLE `conversazione_utente` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversazione_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK a conversazioni.id',
  `utente_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK a utenti.id',
  `ultimo_accesso` timestamp NULL DEFAULT NULL COMMENT 'Ultima volta che ha visto la chat',
  `messaggi_non_letti` int(11) NOT NULL DEFAULT 0 COMMENT 'Contatore messaggi non letti',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversazione_utente_unique` (`conversazione_id`, `utente_id`),
  KEY `conversazione_utente_conversazione_id_index` (`conversazione_id`),
  KEY `conversazione_utente_utente_id_index` (`utente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relazione utenti-conversazioni';

-- STEP 3: Crea tabella messaggi
CREATE TABLE `messaggi` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversazione_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK a conversazioni.id',
  `mittente_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK a utenti.id (chi invia)',
  `corpo` text NOT NULL COMMENT 'Testo del messaggio',
  `tipo` enum('testo','immagine','file') NOT NULL DEFAULT 'testo' COMMENT 'Tipo messaggio',
  `allegato_path` varchar(255) DEFAULT NULL COMMENT 'Path file allegato',
  `allegato_nome` varchar(255) DEFAULT NULL COMMENT 'Nome originale file',
  `letto` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Messaggio letto',
  `letto_at` timestamp NULL DEFAULT NULL COMMENT 'Data lettura',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messaggi_conversazione_id_index` (`conversazione_id`),
  KEY `messaggi_mittente_id_index` (`mittente_id`),
  KEY `messaggi_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Messaggi chat';

-- STEP 4: Crea tabella notifiche
CREATE TABLE `notifiche` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `utente_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK a utenti.id (destinatario)',
  `tipo` varchar(50) NOT NULL COMMENT 'Tipo notifica (lezione, messaggio, pagamento, etc)',
  `titolo` varchar(255) NOT NULL COMMENT 'Titolo notifica',
  `corpo` text DEFAULT NULL COMMENT 'Testo notifica',
  `icona` varchar(50) DEFAULT NULL COMMENT 'FontAwesome icon class',
  `colore` varchar(20) DEFAULT 'blue' COMMENT 'Colore badge (blue, green, red, etc)',
  `link_url` varchar(255) DEFAULT NULL COMMENT 'URL da aprire al click',
  `dati_extra` json DEFAULT NULL COMMENT 'Dati aggiuntivi JSON',
  `letta` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Notifica letta',
  `letta_at` timestamp NULL DEFAULT NULL COMMENT 'Data lettura',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifiche_utente_id_index` (`utente_id`),
  KEY `notifiche_tipo_index` (`tipo`),
  KEY `notifiche_letta_index` (`letta`),
  KEY `notifiche_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifiche utenti';

-- STEP 5: Registra migrations
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_11_17_220000_create_conversazioni_table', 6),
('2025_11_17_220001_create_conversazione_utente_table', 6),
('2025_11_17_220002_create_messaggi_table', 6),
('2025_11_17_220003_create_notifiche_table', 6);

-- STEP 6: Verifica creazione tabelle
SELECT
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'agstudiodiital_magia'
AND TABLE_NAME IN ('conversazioni', 'conversazione_utente', 'messaggi', 'notifiche')
ORDER BY TABLE_NAME;

-- STEP 7: Verifica migrations registrate
SELECT * FROM `migrations` WHERE `migration` LIKE '%conversazioni%' OR `migration` LIKE '%messaggi%' OR `migration` LIKE '%notifiche%';

-- ============================================
-- COMPLETATO
-- ============================================
-- Dopo aver eseguito questo script:
-- 1. Le tabelle chat e notifiche saranno create
-- 2. Il sistema di messaggistica sarà pronto per l'uso
-- 3. Le notifiche potranno essere inviate agli utenti
-- ============================================
