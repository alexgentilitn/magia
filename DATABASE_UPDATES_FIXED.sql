DELIMITER $$

DROP PROCEDURE IF EXISTS aggiungi_colonne_clienti$$
CREATE PROCEDURE aggiungi_colonne_clienti()
BEGIN
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clienti' AND COLUMN_NAME = 'tipo_cliente') THEN
        ALTER TABLE `clienti` ADD COLUMN `tipo_cliente` ENUM('prova', 'effettiva') DEFAULT 'effettiva' AFTER `stato_cliente`;
    END IF;

    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clienti' AND COLUMN_NAME = 'data_iscrizione') THEN
        ALTER TABLE `clienti` ADD COLUMN `data_iscrizione` DATE NULL AFTER `tipo_cliente`;
    END IF;

    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clienti' AND COLUMN_NAME = 'peso_iniziale') THEN
        ALTER TABLE `clienti` ADD COLUMN `peso_iniziale` DECIMAL(5,2) NULL AFTER `data_iscrizione`;
    END IF;

    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clienti' AND COLUMN_NAME = 'altezza') THEN
        ALTER TABLE `clienti` ADD COLUMN `altezza` DECIMAL(5,2) NULL AFTER `peso_iniziale`;
    END IF;
END$$

CALL aggiungi_colonne_clienti()$$
DROP PROCEDURE aggiungi_colonne_clienti$$

CREATE TABLE IF NOT EXISTS `parametri_corporei` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `data_rilevazione` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altezza` decimal(5,2) DEFAULT NULL,
  `circonferenza_vita` decimal(5,2) DEFAULT NULL,
  `circonferenza_fianchi` decimal(5,2) DEFAULT NULL,
  `massa_grassa` decimal(5,2) DEFAULT NULL,
  `massa_magra` decimal(5,2) DEFAULT NULL,
  `acqua_corporea` decimal(5,2) DEFAULT NULL,
  `metabolismo_basale` int DEFAULT NULL,
  `massa_ossea` decimal(5,2) DEFAULT NULL,
  `grasso_viscerale` decimal(5,2) DEFAULT NULL,
  `proteine` decimal(5,2) DEFAULT NULL,
  `eta_metabolica` int DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `note` text,
  `rilevato_da` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parametri_corporei_cliente_id_foreign` (`cliente_id`),
  KEY `parametri_corporei_rilevato_da_foreign` (`rilevato_da`),
  CONSTRAINT `parametri_corporei_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parametri_corporei_rilevato_da_foreign` FOREIGN KEY (`rilevato_da`) REFERENCES `utenti` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci$$

CREATE TABLE IF NOT EXISTS `log_attivita` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `utente_id` bigint unsigned NOT NULL,
  `azione` varchar(255) NOT NULL,
  `descrizione` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `dati_modificati` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_attivita_utente_id_foreign` (`utente_id`),
  KEY `log_attivita_created_at_index` (`created_at`),
  CONSTRAINT `log_attivita_utente_id_foreign` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci$$

DROP PROCEDURE IF EXISTS aggiungi_colonne_pagamenti$$
CREATE PROCEDURE aggiungi_colonne_pagamenti()
BEGIN
    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pagamenti_clienti' AND COLUMN_NAME = 'commissione_paypal') THEN
        ALTER TABLE `pagamenti_clienti` ADD COLUMN `commissione_paypal` DECIMAL(10,2) DEFAULT 0.00 AFTER `importo`;
    END IF;

    IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pagamenti_clienti' AND COLUMN_NAME = 'importo_netto') THEN
        ALTER TABLE `pagamenti_clienti` ADD COLUMN `importo_netto` DECIMAL(10,2) DEFAULT NULL AFTER `commissione_paypal`;
    END IF;
END$$

CALL aggiungi_colonne_pagamenti()$$
DROP PROCEDURE aggiungi_colonne_pagamenti$$

DELIMITER ;
