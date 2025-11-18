-- =====================================================
-- QUERY SQL PER SISTEMA PESATE
-- Da eseguire in phpMyAdmin
-- =====================================================

-- 1. CREA TABELLA PESATE
CREATE TABLE IF NOT EXISTS `pesate` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `sede` varchar(100) DEFAULT NULL COMMENT 'Sede dove è stata effettuata la pesata',
  `peso` decimal(5,2) NOT NULL COMMENT 'Peso in kg',
  `bmi` decimal(5,2) DEFAULT NULL COMMENT 'Body Mass Index',
  `peso_corporeo_senza_grassi` decimal(5,2) DEFAULT NULL COMMENT 'Massa magra in kg',
  `muscolo_scheletrico` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale muscolo scheletrico',
  `grasso_corporeo` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale grasso corporeo',
  `grasso_sottocutaneo` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale grasso sottocutaneo',
  `grasso_viscerale` int(11) DEFAULT NULL COMMENT 'Livello grasso viscerale',
  `acqua_corporea` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale acqua corporea',
  `massa_muscolare` decimal(5,2) DEFAULT NULL COMMENT 'Massa muscolare in kg',
  `massa_ossea` decimal(5,2) DEFAULT NULL COMMENT 'Massa ossea in kg',
  `proteine` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale proteine',
  `bmr` int(11) DEFAULT NULL COMMENT 'Metabolismo basale (calorie)',
  `eta_metabolica` int(11) DEFAULT NULL COMMENT 'Età metabolica in anni',
  `data_rilevazione` date NOT NULL COMMENT 'Data della pesata',
  `note` text DEFAULT NULL COMMENT 'Note aggiuntive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_data_rilevazione` (`data_rilevazione`),
  KEY `idx_sede` (`sede`),
  CONSTRAINT `fk_pesate_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clienti` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Storico pesate e parametri corporei clienti';

-- =====================================================
-- ISTRUZIONI PER L'IMPORTAZIONE
-- =====================================================

-- DOPO aver creato la tabella, usa il comando Artisan per importare i dati dai file Excel:
-- php artisan pesate:import

-- =====================================================
-- QUERY UTILI PER VERIFICA
-- =====================================================

-- Conta pesate per cliente
SELECT
    c.nome,
    c.cognome,
    COUNT(p.id) as totale_pesate,
    MAX(p.data_rilevazione) as ultima_pesata
FROM clienti c
LEFT JOIN pesate p ON c.id = p.cliente_id
GROUP BY c.id
ORDER BY totale_pesate DESC;

-- Vedi ultime 10 pesate inserite
SELECT
    p.*,
    c.nome,
    c.cognome
FROM pesate p
JOIN clienti c ON p.cliente_id = c.id
ORDER BY p.created_at DESC
LIMIT 10;

-- Statistiche pesate per sede
SELECT
    sede,
    COUNT(*) as totale_pesate,
    AVG(peso) as peso_medio,
    AVG(bmi) as bmi_medio
FROM pesate
GROUP BY sede
ORDER BY totale_pesate DESC;
