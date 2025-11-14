-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Nov 14, 2025 alle 22:38
-- Versione del server: 5.7.44-log-cll-lve
-- Versione PHP: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agstudiodiital_magia`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente_lezione`
--

CREATE TABLE `cliente_lezione` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `lezione_id` bigint(20) UNSIGNED NOT NULL,
  `stato` enum('prenotato','confermato','presente','assente','cancellato','lista_attesa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prenotato',
  `data_prenotazione` date DEFAULT NULL,
  `check_in` timestamp NULL DEFAULT NULL,
  `check_out` timestamp NULL DEFAULT NULL,
  `valutazione` int(11) DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `cliente_lezione`
--

INSERT INTO `cliente_lezione` (`id`, `cliente_id`, `lezione_id`, `stato`, `data_prenotazione`, `check_in`, `check_out`, `valutazione`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'prenotato', '2025-11-13', NULL, NULL, NULL, NULL, '2025-11-13 12:45:32', '2025-11-13 12:48:54');

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente_programma`
--

CREATE TABLE `cliente_programma` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `programma_id` bigint(20) UNSIGNED NOT NULL,
  `data_iscrizione` date DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `stato` enum('attivo','completato','cancellato','sospeso','in_attesa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo',
  `prezzo_pagato` decimal(10,2) DEFAULT NULL,
  `completamento` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `clienti`
--

CREATE TABLE `clienti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utente_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome cliente',
  `cognome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome cliente',
  `codice_fiscale` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice fiscale italiano',
  `indirizzo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Via e numero civico',
  `citta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Città di residenza',
  `provincia` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sigla provincia (es: TN)',
  `cap` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice Avviamento Postale',
  `nazione` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Italia' COMMENT 'Nazione di residenza',
  `telefono_fisso` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono fisso',
  `indirizzo_via` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_citta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_provincia` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_cap` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_mobile` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cellulare (obbligatorio)',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email principale',
  `pec` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PEC (opzionale)',
  `data_nascita` date NOT NULL COMMENT 'Data di nascita',
  `sesso` enum('F','M','Altro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F' COMMENT 'Sesso biologico',
  `luogo_nascita` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Città di nascita',
  `peso_iniziale` decimal(5,2) DEFAULT NULL COMMENT 'Peso iniziale in kg',
  `altezza` decimal(5,2) DEFAULT NULL COMMENT 'Altezza in cm',
  `circonferenza_vita` decimal(5,2) DEFAULT NULL COMMENT 'Circonferenza vita in cm',
  `circonferenza_fianchi` decimal(5,2) DEFAULT NULL COMMENT 'Circonferenza fianchi in cm',
  `circonferenza_braccia` decimal(5,2) DEFAULT NULL COMMENT 'Circonferenza braccia in cm',
  `circonferenza_cosce` decimal(5,2) DEFAULT NULL COMMENT 'Circonferenza cosce in cm',
  `massa_grassa` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale massa grassa',
  `massa_magra` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale massa magra',
  `acqua_corporea` decimal(5,2) DEFAULT NULL COMMENT 'Percentuale acqua corporea',
  `metabolismo_basale` decimal(6,2) DEFAULT NULL COMMENT 'Kcal metabolismo basale',
  `obiettivi_personali` text COLLATE utf8mb4_unicode_ci COMMENT 'Obiettivi della cliente (dimagrimento, tonificazione, ecc)',
  `note_mediche` text COLLATE utf8mb4_unicode_ci COMMENT 'Patologie, allergie, intolleranze',
  `preferenze_alimentari` text COLLATE utf8mb4_unicode_ci COMMENT 'Preferenze e restrizioni alimentari',
  `farmaci_assunti` text COLLATE utf8mb4_unicode_ci COMMENT 'Farmaci in uso regolare',
  `certificato_medico` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha consegnato certificato medico',
  `certificato_medico_scadenza` date DEFAULT NULL COMMENT 'Scadenza certificato medico',
  `programma_attuale` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Programma in corso (Balla&Snella, MA.GIA, ecc)',
  `data_iscrizione` date DEFAULT NULL COMMENT 'Data prima iscrizione',
  `inizio_programma` date DEFAULT NULL COMMENT 'Data inizio programma attuale',
  `fine_programma` date DEFAULT NULL COMMENT 'Data fine programma (se prevista)',
  `stato_programma` enum('attivo','sospeso','completato','cancellato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo' COMMENT 'Stato programma attuale',
  `dati_personalizzati` json DEFAULT NULL COMMENT 'Campi extra personalizzabili per programmi specifici (JSON)',
  `gruppo_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link gruppo WhatsApp associato',
  `sede_preferita_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Sede preferita dalla cliente',
  `consenso_privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Accettazione privacy policy',
  `consenso_privacy_data` timestamp NULL DEFAULT NULL COMMENT 'Data accettazione privacy',
  `consenso_marketing` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Consenso comunicazioni marketing',
  `consenso_foto` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Consenso uso foto/video',
  `note_interne` text COLLATE utf8mb4_unicode_ci COMMENT 'Note riservate admin (non visibili a cliente)',
  `stato_cliente` enum('attivo','sospeso','inattivo','cancellato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo' COMMENT 'Stato account cliente',
  `codice_cliente` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice univoco cliente (generato automaticamente)',
  `codice_referral` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice personale per invitare amiche',
  `invitato_da_cliente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `clienti`
--

INSERT INTO `clienti` (`id`, `utente_id`, `nome`, `cognome`, `codice_fiscale`, `indirizzo`, `citta`, `provincia`, `cap`, `nazione`, `telefono_fisso`, `indirizzo_via`, `indirizzo_citta`, `indirizzo_provincia`, `indirizzo_cap`, `telefono_mobile`, `email`, `pec`, `data_nascita`, `sesso`, `luogo_nascita`, `peso_iniziale`, `altezza`, `circonferenza_vita`, `circonferenza_fianchi`, `circonferenza_braccia`, `circonferenza_cosce`, `massa_grassa`, `massa_magra`, `acqua_corporea`, `metabolismo_basale`, `obiettivi_personali`, `note_mediche`, `preferenze_alimentari`, `farmaci_assunti`, `certificato_medico`, `certificato_medico_scadenza`, `programma_attuale`, `data_iscrizione`, `inizio_programma`, `fine_programma`, `stato_programma`, `dati_personalizzati`, `gruppo_whatsapp`, `sede_preferita_id`, `consenso_privacy`, `consenso_privacy_data`, `consenso_marketing`, `consenso_foto`, `note_interne`, `stato_cliente`, `codice_cliente`, `codice_referral`, `invitato_da_cliente_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Giulia', 'Gentili', 'GNTLXA74S20L378C', 'Strada di torchio 13', 'Civezzano', 'TN', '38045', 'Italia', '0461123456', NULL, NULL, NULL, NULL, '3478445159', 'giulia.gentili2019@gmail.comE', NULL, '2019-10-12', 'F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2025-11-09', NULL, NULL, 'attivo', NULL, NULL, NULL, 0, NULL, 0, 0, 'Per ora nessuna nota, questo è il primo cliente test..ggg', 'attivo', 'CL00001', '1523A1CD', NULL, '2025-11-09 15:14:29', '2025-11-12 14:01:23', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `impostazioni`
--

CREATE TABLE `impostazioni` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chiave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valore` text COLLATE utf8mb4_unicode_ci,
  `gruppo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generale',
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'testo',
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `criptata` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `impostazioni`
--

INSERT INTO `impostazioni` (`id`, `chiave`, `valore`, `gruppo`, `tipo`, `descrizione`, `criptata`, `created_at`, `updated_at`) VALUES
(1, 'smtp_host', 'mail.agstudio.digital', 'smtp', 'testo', 'Host del server SMTP', 0, '2025-11-13 10:12:22', '2025-11-13 10:15:09'),
(2, 'smtp_porta', '465', 'smtp', 'numero', 'Porta del server SMTP', 0, '2025-11-13 10:12:22', '2025-11-13 10:16:04'),
(3, 'smtp_username', 'support@agstudio.digital', 'smtp', 'email', 'Username/Email per autenticazione SMTP', 0, '2025-11-13 10:12:22', '2025-11-13 10:19:11'),
(4, 'smtp_password', 'eyJpdiI6IkhZcklKd202VkxycERIcUdoZUtCRWc9PSIsInZhbHVlIjoiU3dSUUY2eCthRDZqaUdEVS85NjdsZz09IiwibWFjIjoiZjk0Mzg3MDdkOGVkM2IyZWY3OTU5ZmU3NjNiNzYwNGFkYzZhZDUyMThlZjQ2NjA2MjM3OTJkYWVlMDBhYjFkYyIsInRhZyI6IiJ9', 'smtp', 'password', 'Password per autenticazione SMTP', 1, '2025-11-13 10:12:22', '2025-11-13 10:22:02'),
(5, 'smtp_encryption', 'ssl', 'smtp', 'testo', 'Tipo di encryption (tls, ssl, none)', 0, '2025-11-13 10:12:22', '2025-11-13 10:16:04'),
(6, 'mail_from_address', 'support@agstudio.digital', 'smtp', 'email', 'Indirizzo email mittente', 0, '2025-11-13 10:12:22', '2025-11-13 10:17:54'),
(7, 'mail_from_name', 'MA.GIA DONNA', 'smtp', 'testo', 'Nome mittente', 0, '2025-11-13 10:12:22', '2025-11-13 10:12:22');

-- --------------------------------------------------------

--
-- Struttura della tabella `impostazioni_sistema`
--

CREATE TABLE `impostazioni_sistema` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Categoria: tipologia_lezione, stato_lezione, ecc.',
  `chiave` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chiave univoca (es: individuale, confermata)',
  `etichetta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome visualizzato (es: Lezione Individuale)',
  `descrizione` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione opzionale',
  `colore` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Colore esadecimale per badge (#764ba2)',
  `icona` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe icona FontAwesome (fa-user)',
  `ordinamento` int(10) UNSIGNED DEFAULT '0' COMMENT 'Ordine visualizzazione (0 = primo)',
  `attivo` tinyint(1) DEFAULT '1' COMMENT 'Se disattivato non appare nelle select',
  `di_sistema` tinyint(1) DEFAULT '0' COMMENT 'Se true, non può essere eliminato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni di sistema (tipologie, stati, ecc.)';

--
-- Dump dei dati per la tabella `impostazioni_sistema`
--

INSERT INTO `impostazioni_sistema` (`id`, `categoria`, `chiave`, `etichetta`, `descrizione`, `colore`, `icona`, `ordinamento`, `attivo`, `di_sistema`, `created_at`, `updated_at`) VALUES
(1, 'tipologia_lezione', 'individuale', 'Lezione Individuale', 'Lezione uno-a-uno con il professionista', '#3b82f6', 'fa-user', 1, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(2, 'tipologia_lezione', 'gruppo', 'Lezione di Gruppo', 'Lezione con più partecipanti', '#10b981', 'fa-users', 2, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(3, 'tipologia_lezione', 'online', 'Lezione Online', 'Lezione svolta completamente da remoto', '#8b5cf6', 'fa-video', 3, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(4, 'tipologia_lezione', 'ibrida', 'Lezione Ibrida (Presenza + Online)', 'Alcuni in presenza, altri da remoto', '#f59e0b', 'fa-network-wired', 4, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(5, 'stato_lezione', 'programmata', 'Programmata', 'Lezione pianificata ma non ancora confermata', '#94a3b8', 'fa-calendar-plus', 1, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(6, 'stato_lezione', 'confermata', 'Confermata', 'Lezione confermata e pronta', '#3b82f6', 'fa-calendar-check', 2, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(7, 'stato_lezione', 'in_corso', 'In Corso', 'Lezione attualmente in svolgimento', '#f59e0b', 'fa-circle-play', 3, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(8, 'stato_lezione', 'completata', 'Completata', 'Lezione terminata con successo', '#10b981', 'fa-circle-check', 4, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(9, 'stato_lezione', 'cancellata', 'Cancellata', 'Lezione annullata', '#ef4444', 'fa-circle-xmark', 5, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(10, 'stato_lezione', 'rinviata', 'Rinviata', 'Lezione posticipata a data da definire', '#8b5cf6', 'fa-clock', 6, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(11, 'frequenza_ricorrenza', 'giornaliera', 'Ogni giorno', 'Ripeti la lezione ogni giorno', '#3b82f6', 'fa-calendar-day', 1, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(12, 'frequenza_ricorrenza', 'settimanale', 'Ogni settimana', 'Ripeti la lezione ogni settimana', '#10b981', 'fa-calendar-week', 2, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(13, 'frequenza_ricorrenza', 'bisettimanale', 'Ogni 2 settimane', 'Ripeti la lezione ogni due settimane', '#f59e0b', 'fa-calendar-days', 3, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02'),
(14, 'frequenza_ricorrenza', 'mensile', 'Ogni mese', 'Ripeti la lezione ogni mese', '#8b5cf6', 'fa-calendar', 4, 1, 1, '2025-11-13 17:07:02', '2025-11-13 17:07:02');

-- --------------------------------------------------------

--
-- Struttura della tabella `lezioni`
--

CREATE TABLE `lezioni` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titolo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `tipologia` enum('individuale','gruppo','online','ibrida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gruppo',
  `disciplina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato','tutti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tutti',
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL,
  `durata_minuti` int(11) DEFAULT NULL,
  `posti_totali` int(11) NOT NULL,
  `posti_occupati` int(11) NOT NULL DEFAULT '0',
  `posti_lista_attesa` int(11) NOT NULL DEFAULT '0',
  `programma_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `professionista_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stato` enum('programmata','confermata','in_corso','completata','cancellata','rinviata') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programmata',
  `note_interne` text COLLATE utf8mb4_unicode_ci,
  `note_pubbliche` text COLLATE utf8mb4_unicode_ci,
  `ricorrente` tinyint(1) NOT NULL DEFAULT '0',
  `lezione_padre_id` bigint(20) UNSIGNED DEFAULT NULL,
  `frequenza_ricorrenza` enum('giornaliera','settimanale','bisettimanale','mensile') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fine_ricorrenza` date DEFAULT NULL,
  `prenotazione_obbligatoria` tinyint(1) NOT NULL DEFAULT '1',
  `max_cancellazioni_ore` int(11) DEFAULT NULL,
  `lista_attesa_attiva` tinyint(1) NOT NULL DEFAULT '0',
  `materiale_necessario` json DEFAULT NULL,
  `link_online` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_online` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibile_calendario` tinyint(1) NOT NULL DEFAULT '1',
  `invia_reminder` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `lezioni`
--

INSERT INTO `lezioni` (`id`, `titolo`, `descrizione`, `tipologia`, `disciplina`, `livello`, `data`, `ora_inizio`, `ora_fine`, `durata_minuti`, `posti_totali`, `posti_occupati`, `posti_lista_attesa`, `programma_id`, `sede_id`, `professionista_id`, `stato`, `note_interne`, `note_pubbliche`, `ricorrente`, `lezione_padre_id`, `frequenza_ricorrenza`, `fine_ricorrenza`, `prenotazione_obbligatoria`, `max_cancellazioni_ore`, `lista_attesa_attiva`, `materiale_necessario`, `link_online`, `password_online`, `visibile_calendario`, `invia_reminder`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Lezione di Zumba', 'Questa è la desrizione della lezione di Zumba', 'individuale', NULL, 'tutti', '2025-11-14', '15:00:00', '16:00:00', 60, 10, 0, 0, 1, 1, 1, 'programmata', 'Note 2', NULL, 1, NULL, 'giornaliera', NULL, 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 10:52:44', '2025-11-13 11:00:41', '2025-11-13 11:00:41'),
(2, 'Lezione di Zumba', '11', 'gruppo', NULL, 'tutti', '2025-11-13', '16:00:00', '17:00:00', 60, 10, 0, 0, 1, 1, 11, 'programmata', NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 10:55:48', '2025-11-13 11:00:46', '2025-11-13 11:00:46'),
(3, 'eee', 'ewrewewerw', 'gruppo', NULL, 'tutti', '2025-11-13', '15:30:00', '16:00:00', 15, 10, 1, 0, NULL, 1, 11, 'programmata', NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:08:34', '2025-11-13 15:54:00', NULL),
(4, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-13', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, NULL, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(5, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-14', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(6, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-15', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(7, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-16', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(8, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-17', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(9, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-18', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(10, 'Lezione di pilates', NULL, 'individuale', NULL, 'tutti', '2025-10-19', '18:00:00', '19:00:00', 60, 10, 0, 0, 1, NULL, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 12:36:08', NULL),
(11, 'Lezione di pilates', NULL, 'gruppo', NULL, 'tutti', '2025-10-20', '18:00:00', '19:00:00', 15, 10, 0, 0, 1, 1, 11, 'programmata', 'note 1', 'aaa note 1', 1, 4, 'giornaliera', '2025-10-20', 1, NULL, 0, NULL, NULL, NULL, 1, 1, '2025-11-13 12:36:08', '2025-11-13 15:54:00', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `log_attivita`
--

CREATE TABLE `log_attivita` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utente_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID utente che ha eseguito azione',
  `azione` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo azione (creato, modificato, eliminato, ecc.)',
  `descrizione` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione testuale azione',
  `modello` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del model coinvolto',
  `modello_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID del record coinvolto',
  `dati_precedenti` json DEFAULT NULL COMMENT 'Snapshot dati prima modifica',
  `dati_nuovi` json DEFAULT NULL COMMENT 'Snapshot dati dopo modifica',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Indirizzo IP utente',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Browser/device info',
  `metadata` json DEFAULT NULL COMMENT 'Dati aggiuntivi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `log_attivita`
--

INSERT INTO `log_attivita` (`id`, `utente_id`, `azione`, `descrizione`, `modello`, `modello_id`, `dati_precedenti`, `dati_nuovi`, `ip_address`, `user_agent`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 'modificato', 'Utente admin modificato: Alex Gentili', 'Utente', 1, '{\"id\": 1, \"nome\": \"Amministratore\", \"email\": \"admin@magiadonna.it\", \"attivo\": true, \"cognome\": \"MA.GIA DONNA\", \"ruolo_id\": \"1\", \"telefono\": \"0471123456\", \"ultimo_ip\": \"140.233.176.12\", \"created_at\": \"2025-11-09T16:55:03.000000Z\", \"deleted_at\": null, \"updated_at\": \"2025-11-11T16:58:30.000000Z\", \"tipo_utente\": \"amministratore\", \"ultimo_accesso\": \"2025-11-11T16:58:30.000000Z\", \"email_verificata\": true, \"email_verificata_il\": \"2025-11-09T16:55:03.000000Z\"}', '{\"id\": 1, \"nome\": \"Alex\", \"email\": \"Informatica.gentili@gmail.com\", \"attivo\": true, \"cognome\": \"Gentili\", \"ruolo_id\": \"1\", \"telefono\": \"3491099145\", \"ultimo_ip\": \"140.233.176.12\", \"created_at\": \"2025-11-09T16:55:03.000000Z\", \"deleted_at\": null, \"updated_at\": \"2025-11-11T17:28:05.000000Z\", \"tipo_utente\": \"amministratore\", \"ultimo_accesso\": \"2025-11-11T16:58:30.000000Z\", \"email_verificata\": true, \"email_verificata_il\": \"2025-11-09T16:55:03.000000Z\"}', '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-11 16:28:05', '2025-11-11 16:28:05'),
(2, 1, 'visualizzato', 'Visualizzato profilo utente: Alex Gentili', 'Utente', 1, NULL, NULL, '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-11 16:28:05', '2025-11-11 16:28:05'),
(3, 1, 'visualizzato', 'Visualizzato profilo utente: Alex Gentili', 'Utente', 1, NULL, NULL, '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-11 16:28:52', '2025-11-11 16:28:52'),
(4, 1, 'visualizzato', 'Visualizzato profilo utente: Alex Gentili', 'Utente', 1, NULL, NULL, '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-11 16:30:49', '2025-11-11 16:30:49'),
(5, 1, 'creato', 'Sede creata: Nuova sede test', 'Sede', 1, NULL, '{\"id\": 1, \"nome\": \"Nuova sede test\", \"slug\": \"nuova-sede-test\", \"email\": \"trento@trento.it\", \"attiva\": true, \"ordine\": 1, \"telefono\": \"+390461123456\", \"created_at\": \"2025-11-12T09:54:36.000000Z\", \"latitudine\": null, \"updated_at\": \"2025-11-12T09:54:36.000000Z\", \"descrizione\": \"Descrizione test\", \"longitudine\": null, \"note_accesso\": \"Note di accesso\", \"indirizzo_cap\": \"38100\", \"indirizzo_via\": \"Via roma\", \"indirizzo_citta\": \"Trento\", \"tipologie_corsi\": [\"pilates\", \"yoga\", \"ginnastica\", \"posturale\", \"funzionale\", \"tonificazione\", \"cardio\", \"stretching\"], \"capienza_massima\": 100, \"visibile_pubblico\": true, \"indirizzo_provincia\": \"TN\"}', '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-12 09:54:36', '2025-11-12 09:54:36'),
(6, 1, 'creato', 'Utente admin creato: Cinzia Tonelli (utente1@ut.it)', 'Utente', 8, NULL, '{\"id\": 8, \"nome\": \"Cinzia\", \"email\": \"utente1@ut.it\", \"attivo\": true, \"cognome\": \"Tonelli\", \"ruolo_id\": \"1\", \"telefono\": \"+393478445159\", \"created_at\": \"2025-11-12T14:12:10.000000Z\", \"updated_at\": \"2025-11-12T14:12:10.000000Z\", \"tipo_utente\": \"amministratore\"}', '140.233.176.12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, '2025-11-12 14:12:10', '2025-11-12 14:12:10');

-- --------------------------------------------------------

--
-- Struttura della tabella `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_11_05_100000_create_ruoli_permessi_tables', 1),
(6, '2025_11_05_100001_create_utenti_table', 1),
(7, '2025_11_05_100002_create_clienti_table', 1),
(8, '2025_11_10_170500_add_indirizzo_to_clienti', 2),
(9, '2025_11_11_155927_create_utente_permesso_table', 2),
(10, '2025_11_11_161058_create_log_attivita_table', 2),
(11, '2025_11_12_084253_create_sedes_table', 3),
(12, '2025_11_12_084415_create_professionista_sede_table', 3),
(13, '2025_11_12_090037_create_programmas_table', 3),
(14, '2025_11_12_090122_create_cliente_programma_table', 4),
(15, '2025_11_12_090346_create_pagamentos_table', 4),
(16, '2025_11_12_090525_create_leziones_table', 5),
(17, '2025_11_12_090557_create_cliente_lezione_table', 5),
(18, '2025_11_12_101547_create_test_deployment_table', 6),
(19, '2025_11_12_114458_create_programmi_table', 7),
(20, '2025_11_12_114549_create_cliente_programma_table', 7),
(22, '2025_11_13_085902_create_programmi_table', 9),
(23, '2025_11_13_085936_create_lezioni_table', 9),
(24, '2025_11_13_090010_create_cliente_programma_table', 9),
(25, '2025_11_13_090048_create_cliente_lezione_table', 9),
(26, '2025_11_13_091458_create_pagamentos_table', 10),
(27, '2025_11_13_085812_create_sedi_table', 11),
(30, '2025_11_13_100000_create_professionisti_table', 12);

-- --------------------------------------------------------

--
-- Struttura della tabella `pagamenti`
--

CREATE TABLE `pagamenti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `programma_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lezione_id` bigint(20) UNSIGNED DEFAULT NULL,
  `importo` decimal(10,2) NOT NULL,
  `importo_pagato` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importo_residuo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo` enum('programma','lezione','abbonamento','altro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programma',
  `metodo` enum('contanti','bonifico','carta','pos','paypal','satispay','altro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contanti',
  `stato` enum('in_attesa','parziale','completato','scaduto','rimborsato','cancellato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_attesa',
  `data_emissione` date NOT NULL,
  `data_scadenza` date DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `numero_fattura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riferimento_transazione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `fatturato` tinyint(1) NOT NULL DEFAULT '0',
  `inviato_promemoria` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `pagamenti`
--

INSERT INTO `pagamenti` (`id`, `cliente_id`, `programma_id`, `lezione_id`, `importo`, `importo_pagato`, `importo_residuo`, `tipo`, `metodo`, `stato`, `data_emissione`, `data_scadenza`, `data_pagamento`, `numero_fattura`, `riferimento_transazione`, `note`, `fatturato`, `inviato_promemoria`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, NULL, NULL, 100.00, 100.00, 0.00, 'lezione', 'bonifico', 'completato', '2025-11-13', NULL, '2025-11-13', NULL, NULL, NULL, 0, 0, '2025-11-13 08:26:38', '2025-11-13 08:26:58', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `permessi`
--

CREATE TABLE `permessi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome permesso',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Identificativo univoco',
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Categoria permesso',
  `descrizione` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione del permesso',
  `attivo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Permesso attivo/disattivato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `permessi`
--

INSERT INTO `permessi` (`id`, `nome`, `slug`, `categoria`, `descrizione`, `attivo`, `created_at`, `updated_at`) VALUES
(1, 'Visualizza Dashboard', 'visualizza-dashboard', 'dashboard', 'Accesso alla dashboard amministratore', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(2, 'Visualizza Clienti', 'visualizza-clienti', 'clienti', 'Visualizzare elenco clienti', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(3, 'Crea Clienti', 'crea-clienti', 'clienti', 'Creare nuovi clienti', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(4, 'Modifica Clienti', 'modifica-clienti', 'clienti', 'Modificare dati clienti esistenti', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(5, 'Elimina Clienti', 'elimina-clienti', 'clienti', 'Eliminare clienti dal sistema', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(6, 'Gestione Programmi', 'gestione-programmi', 'programmi', 'Creare e modificare programmi', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(7, 'Gestione Corsi', 'gestione-corsi', 'corsi', 'Creare e modificare corsi/lezioni', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(8, 'Visualizza Pagamenti', 'visualizza-pagamenti', 'pagamenti', 'Visualizzare storico pagamenti', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(9, 'Gestione Pagamenti', 'gestione-pagamenti', 'pagamenti', 'Registrare e modificare pagamenti', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(10, 'Gestione Sedi', 'gestione-sedi', 'sedi', 'Gestire le 5 sedi operative', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(11, 'Gestione Utenti', 'gestione-utenti', 'sistema', 'Creare e modificare utenti amministratori', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(12, 'Gestione Ruoli', 'gestione-ruoli', 'sistema', 'Modificare ruoli e permessi', 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02');

-- --------------------------------------------------------

--
-- Struttura della tabella `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `professionista_sede`
--

CREATE TABLE `professionista_sede` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utente_id` bigint(20) UNSIGNED NOT NULL,
  `sede_id` bigint(20) UNSIGNED NOT NULL,
  `ruolo_sede` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT '1',
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `professionisti`
--

CREATE TABLE `professionisti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utente_id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome professionista',
  `cognome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome professionista',
  `codice_fiscale` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascita` date DEFAULT NULL,
  `sesso` enum('F','M','Altro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titolo_professionale` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Es: Personal Trainer, Nutrizionista',
  `specializzazioni` json DEFAULT NULL COMMENT 'Discipline insegnate (fitness, danza, yoga, ecc)',
  `bio` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione professionale',
  `anni_esperienza` int(11) DEFAULT NULL COMMENT 'Anni di esperienza',
  `partita_iva` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P.IVA se libero professionista',
  `certificazioni` json DEFAULT NULL COMMENT 'Array certificazioni [{nome, ente, data, scadenza}]',
  `qualifiche` json DEFAULT NULL COMMENT 'Titoli di studio e qualifiche professionali',
  `telefono_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citta` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cap` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tariffa_oraria` decimal(8,2) DEFAULT NULL COMMENT 'Tariffa oraria in euro',
  `tariffa_lezione_gruppo` decimal(8,2) DEFAULT NULL COMMENT 'Tariffa lezione di gruppo',
  `tariffa_lezione_privata` decimal(8,2) DEFAULT NULL COMMENT 'Tariffa lezione privata',
  `disponibilita_settimanale` json DEFAULT NULL COMMENT 'Orari disponibili per giorno',
  `disponibile_da` date DEFAULT NULL COMMENT 'Data inizio disponibilità',
  `disponibile_fino` date DEFAULT NULL COMMENT 'Data fine contratto/disponibilità',
  `foto_profilo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path foto profilo',
  `galleria_foto` json DEFAULT NULL COMMENT 'Galleria foto professionista',
  `video_presentazione` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL video presentazione',
  `sito_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tiktok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lingue_parlate` json DEFAULT NULL COMMENT 'Array lingue parlate',
  `tipo_contratto` enum('dipendente','collaboratore','freelance','altro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_assunzione` date DEFAULT NULL,
  `note_interne` text COLLATE utf8mb4_unicode_ci COMMENT 'Note riservate admin',
  `visibile_pubblico` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Visibile sul sito pubblico',
  `stato` enum('attivo','sospeso','inattivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo',
  `codice_professionista` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valutazione_media` decimal(3,2) DEFAULT NULL COMMENT 'Media recensioni (0-5)',
  `numero_recensioni` int(11) NOT NULL DEFAULT '0',
  `lezioni_completate` int(11) NOT NULL DEFAULT '0',
  `clienti_seguiti` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `professionisti`
--

INSERT INTO `professionisti` (`id`, `utente_id`, `nome`, `cognome`, `codice_fiscale`, `data_nascita`, `sesso`, `titolo_professionale`, `specializzazioni`, `bio`, `anni_esperienza`, `partita_iva`, `certificazioni`, `qualifiche`, `telefono_mobile`, `email`, `indirizzo`, `citta`, `provincia`, `cap`, `tariffa_oraria`, `tariffa_lezione_gruppo`, `tariffa_lezione_privata`, `disponibilita_settimanale`, `disponibile_da`, `disponibile_fino`, `foto_profilo`, `galleria_foto`, `video_presentazione`, `sito_web`, `instagram`, `facebook`, `linkedin`, `tiktok`, `lingue_parlate`, `tipo_contratto`, `data_assunzione`, `note_interne`, `visibile_pubblico`, `stato`, `codice_professionista`, `valutazione_media`, `numero_recensioni`, `lezioni_completate`, `clienti_seguiti`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 11, 'Cinzia', 'Tonelli', NULL, NULL, NULL, 'Personal Trainer', NULL, 'aaaaa', 10, NULL, NULL, NULL, '3478445159', NULL, NULL, NULL, NULL, NULL, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'attivo', 'PROF-2025-0001', NULL, 0, 0, 0, '2025-11-13 09:59:18', '2025-11-13 09:59:18', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `programmi`
--

CREATE TABLE `programmi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `descrizione_breve` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipologia` enum('fitness','danza','alimentazione','wellness','personalizzato','altro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fitness',
  `discipline` json DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato','tutti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tutti',
  `durata_giorni` int(11) DEFAULT NULL,
  `durata_mesi` int(11) DEFAULT NULL,
  `lezioni_totali` int(11) DEFAULT NULL,
  `lezioni_settimana` int(11) DEFAULT NULL,
  `durata_singola_lezione` int(11) DEFAULT NULL,
  `prezzo_base` decimal(10,2) NOT NULL,
  `prezzo_promo` decimal(10,2) DEFAULT NULL,
  `promo_valida_da` date DEFAULT NULL,
  `promo_valida_a` date DEFAULT NULL,
  `prezzo_su_richiesta` tinyint(1) NOT NULL DEFAULT '0',
  `posti_disponibili` int(11) DEFAULT NULL,
  `posti_occupati` int(11) NOT NULL DEFAULT '0',
  `min_partecipanti` int(11) DEFAULT NULL,
  `max_partecipanti` int(11) DEFAULT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `professionista_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `data_fine` date DEFAULT NULL,
  `sempre_disponibile` tinyint(1) NOT NULL DEFAULT '0',
  `obiettivi` json DEFAULT NULL,
  `prerequisiti` json DEFAULT NULL,
  `materiali_inclusi` json DEFAULT NULL,
  `immagine_copertina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `galleria_immagini` json DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT '1',
  `visibile_pubblico` tinyint(1) NOT NULL DEFAULT '0',
  `in_evidenza` tinyint(1) NOT NULL DEFAULT '0',
  `ordine` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `programmi`
--

INSERT INTO `programmi` (`id`, `nome`, `slug`, `descrizione`, `descrizione_breve`, `tipologia`, `discipline`, `livello`, `durata_giorni`, `durata_mesi`, `lezioni_totali`, `lezioni_settimana`, `durata_singola_lezione`, `prezzo_base`, `prezzo_promo`, `promo_valida_da`, `promo_valida_a`, `prezzo_su_richiesta`, `posti_disponibili`, `posti_occupati`, `min_partecipanti`, `max_partecipanti`, `sede_id`, `professionista_id`, `data_inizio`, `data_fine`, `sempre_disponibile`, `obiettivi`, `prerequisiti`, `materiali_inclusi`, `immagine_copertina`, `galleria_immagini`, `attivo`, `visibile_pubblico`, `in_evidenza`, `ordine`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Nuovo programma 1 test', 'aa', NULL, NULL, 'fitness', NULL, 'tutti', 30, 3, 24, 2, 30, 99.00, 79.00, '2000-01-01', '2000-01-01', 0, 20, 0, 5, 20, NULL, NULL, '2000-02-20', '2025-02-20', 0, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, '2025-11-13 08:13:52', '2025-11-13 08:14:10', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `ruoli`
--

CREATE TABLE `ruoli` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome ruolo',
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Identificativo univoco',
  `descrizione` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione del ruolo',
  `livello` int(11) NOT NULL DEFAULT '1' COMMENT 'Livello gerarchico',
  `attivo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Ruolo attivo/disattivato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `ruoli`
--

INSERT INTO `ruoli` (`id`, `nome`, `slug`, `descrizione`, `livello`, `attivo`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Accesso completo a tutte le funzionalità del sistema', 1, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(2, 'Moderatore', 'moderatore', 'Gestione clienti, programmi, corsi e contenuti', 2, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(3, 'Collaboratore', 'collaboratore', 'Accesso limitato a visualizzazione e supporto clienti', 3, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02');

-- --------------------------------------------------------

--
-- Struttura della tabella `ruolo_permesso`
--

CREATE TABLE `ruolo_permesso` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruolo_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID ruolo',
  `permesso_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID permesso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `ruolo_permesso`
--

INSERT INTO `ruolo_permesso` (`id`, `ruolo_id`, `permesso_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(2, 1, 2, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(3, 1, 3, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(4, 1, 4, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(5, 1, 5, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(6, 1, 6, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(7, 1, 7, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(8, 1, 8, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(9, 1, 9, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(10, 1, 10, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(11, 1, 11, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(12, 1, 12, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(16, 2, 3, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(17, 2, 7, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(18, 2, 9, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(19, 2, 6, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(20, 2, 10, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(21, 2, 4, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(22, 2, 2, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(23, 2, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(24, 2, 8, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(31, 3, 2, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(32, 3, 1, '2025-11-11 16:50:02', '2025-11-11 16:50:02'),
(33, 3, 8, '2025-11-11 16:50:02', '2025-11-11 16:50:02');

-- --------------------------------------------------------

--
-- Struttura della tabella `sedes`
--

CREATE TABLE `sedes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `indirizzo_via` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indirizzo_citta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indirizzo_provincia` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indirizzo_cap` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitudine` decimal(10,8) DEFAULT NULL,
  `longitudine` decimal(11,8) DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orari_apertura` json DEFAULT NULL,
  `note_accesso` text COLLATE utf8mb4_unicode_ci,
  `tipologie_corsi` json DEFAULT NULL,
  `capienza_massima` int(11) NOT NULL DEFAULT '20',
  `immagine_copertina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `galleria_immagini` json DEFAULT NULL,
  `attiva` tinyint(1) NOT NULL DEFAULT '1',
  `visibile_pubblico` tinyint(1) NOT NULL DEFAULT '1',
  `sede_principale` tinyint(1) NOT NULL DEFAULT '0',
  `ordine` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `sedes`
--

INSERT INTO `sedes` (`id`, `nome`, `slug`, `descrizione`, `indirizzo_via`, `indirizzo_citta`, `indirizzo_provincia`, `indirizzo_cap`, `latitudine`, `longitudine`, `telefono`, `email`, `orari_apertura`, `note_accesso`, `tipologie_corsi`, `capienza_massima`, `immagine_copertina`, `galleria_immagini`, `attiva`, `visibile_pubblico`, `sede_principale`, `ordine`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Nuova sede test', 'nuova-sede-test', 'Descrizione test', 'Via roma', 'Trento', 'TN', '38100', NULL, NULL, '+390461123456', 'trento@trento.it', NULL, 'Note di accesso', '[\"pilates\", \"yoga\", \"ginnastica\", \"posturale\", \"funzionale\", \"tonificazione\", \"cardio\", \"stretching\"]', 100, NULL, NULL, 1, 1, 0, 1, '2025-11-12 09:54:36', '2025-11-12 09:54:36', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `sedi`
--

CREATE TABLE `sedi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `indirizzo_via` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_citta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_provincia` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo_cap` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitudine` decimal(10,8) DEFAULT NULL,
  `longitudine` decimal(11,8) DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orari_apertura` json DEFAULT NULL,
  `note_accesso` text COLLATE utf8mb4_unicode_ci,
  `tipologie_corsi` json DEFAULT NULL,
  `capienza_massima` int(11) DEFAULT NULL,
  `immagine_copertina` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `galleria_immagini` json DEFAULT NULL,
  `attiva` tinyint(1) NOT NULL DEFAULT '1',
  `visibile_pubblico` tinyint(1) NOT NULL DEFAULT '0',
  `sede_principale` tinyint(1) NOT NULL DEFAULT '0',
  `ordine` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `sedi`
--

INSERT INTO `sedi` (`id`, `nome`, `slug`, `descrizione`, `indirizzo_via`, `indirizzo_citta`, `indirizzo_provincia`, `indirizzo_cap`, `latitudine`, `longitudine`, `telefono`, `email`, `orari_apertura`, `note_accesso`, `tipologie_corsi`, `capienza_massima`, `immagine_copertina`, `galleria_immagini`, `attiva`, `visibile_pubblico`, `sede_principale`, `ordine`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Nuova sede test', 'nuova-sede-test', 'Breve descrizione della sede test', 'Via G. B. Trener 16', 'Trento', 'TN', '38121', NULL, NULL, '0461823395', 'arredogiunco@hotmail.com', '{\"lunedi\": {\"aperto\": false}, \"sabato\": {\"aperto\": true, \"apertura\": \"09:00\", \"chiusura\": \"18:00\"}, \"giovedi\": {\"aperto\": true, \"apertura\": \"09:00\", \"chiusura\": \"18:00\"}, \"martedi\": {\"aperto\": false}, \"venerdi\": {\"aperto\": true, \"apertura\": \"09:00\", \"chiusura\": \"18:00\"}, \"domenica\": {\"aperto\": false}, \"mercoledi\": {\"aperto\": false}}', NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, '2025-11-13 08:47:37', '2025-11-13 09:09:16', NULL),
(2, 'Nuova sede test', 'nuova-sede-test-1', 'Breve descrizione della sede test', 'Via G. B. Trener 16', 'Trento', 'TN', '38121', NULL, NULL, '0461823395', 'arredogiunco@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, '2025-11-13 08:47:41', '2025-11-13 09:01:14', '2025-11-13 09:01:14'),
(3, 'Nuova sede test', 'nuova-sede-test-2', 'Breve descrizione della sede test', 'Via G. B. Trener 16', 'Trento', 'TN', '38121', NULL, NULL, '0461823395', 'arredogiunco@hotmail.com', NULL, NULL, NULL, 30, NULL, NULL, 1, 1, 1, 0, '2025-11-13 08:48:05', '2025-11-13 09:01:11', '2025-11-13 09:01:11');

-- --------------------------------------------------------

--
-- Struttura della tabella `test_deployment`
--

CREATE TABLE `test_deployment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `messaggio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GitHub Actions funziona!',
  `test_passato` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_permesso`
--

CREATE TABLE `utente_permesso` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utente_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID utente',
  `permesso_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID permesso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email univoca per login',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password criptata',
  `password_temp_expires_at` timestamp NULL DEFAULT NULL,
  `deve_cambiare_password` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token per "ricordami"',
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome utente',
  `cognome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome utente',
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono contatto',
  `tipo_utente` enum('amministratore','professionista','cliente') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipologia utente nel sistema',
  `ruolo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Account attivo/disattivato',
  `email_verificata` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Email confermata',
  `email_verificata_il` timestamp NULL DEFAULT NULL COMMENT 'Data verifica email',
  `ultimo_accesso` timestamp NULL DEFAULT NULL COMMENT 'Data ultimo login',
  `ultimo_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP ultimo accesso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `email`, `password`, `password_temp_expires_at`, `deve_cambiare_password`, `remember_token`, `nome`, `cognome`, `telefono`, `tipo_utente`, `ruolo_id`, `attivo`, `email_verificata`, `email_verificata_il`, `ultimo_accesso`, `ultimo_ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Informatica.gentili@gmail.com', '$2y$12$Rr.B3bhqAgu7eXDcCBrPg.gwL.OdiLpGhDavoeMgip8NApCIxByHy', NULL, 0, NULL, 'Alex', 'Gentili', '3491099145', 'amministratore', 1, 1, 1, '2025-11-09 15:55:03', '2025-11-14 20:26:04', '95.248.167.162', '2025-11-09 15:55:03', '2025-11-14 20:26:04', NULL),
(2, 'giulia.gentili2019@gmail.comE', '$2y$12$9allf8adoOe8A0gxDovpReCM.jp03GNixmkrZVTLVMMl/jN08q.RW', NULL, 0, NULL, 'Giulia', 'Gentili', '3478445159', 'cliente', NULL, 1, 0, NULL, NULL, NULL, '2025-11-09 15:14:29', '2025-11-12 14:01:23', NULL),
(9, 'support@agstudio.digital', '$2y$12$gQ1vBaNTDWyxHvWQDGgPzeh59KjDGwfBw7s/3Vx./5zRSrrxei8UC', NULL, 0, NULL, 'Maria', 'Rossi', '3491099145', 'professionista', NULL, 1, 0, NULL, NULL, NULL, '2025-11-13 09:27:30', '2025-11-13 09:39:12', NULL),
(11, 'cinziatonelli83@gmail.com', '$2y$12$f2EghMl9mm5E6br1a1LOKe4zbMk5a.jMaZuRppf4JX1xKLhTLmp6W', '2025-11-14 09:59:18', 1, NULL, 'Cinzia', 'Tonelli', '3478445159', 'professionista', NULL, 1, 0, NULL, NULL, NULL, '2025-11-13 09:59:18', '2025-11-13 09:59:18', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `cliente_lezione`
--
ALTER TABLE `cliente_lezione`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_lezione_cliente_id_lezione_id_unique` (`cliente_id`,`lezione_id`),
  ADD KEY `cliente_lezione_lezione_id_foreign` (`lezione_id`),
  ADD KEY `cliente_lezione_stato_index` (`stato`);

--
-- Indici per le tabelle `cliente_programma`
--
ALTER TABLE `cliente_programma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_programma_cliente_id_programma_id_unique` (`cliente_id`,`programma_id`),
  ADD KEY `cliente_programma_programma_id_foreign` (`programma_id`),
  ADD KEY `cliente_programma_stato_index` (`stato`);

--
-- Indici per le tabelle `clienti`
--
ALTER TABLE `clienti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clienti_codice_fiscale_unique` (`codice_fiscale`),
  ADD UNIQUE KEY `clienti_email_unique` (`email`),
  ADD UNIQUE KEY `clienti_codice_cliente_unique` (`codice_cliente`),
  ADD UNIQUE KEY `clienti_codice_referral_unique` (`codice_referral`),
  ADD KEY `clienti_invitato_da_cliente_id_foreign` (`invitato_da_cliente_id`),
  ADD KEY `clienti_utente_id_index` (`utente_id`),
  ADD KEY `clienti_codice_fiscale_index` (`codice_fiscale`),
  ADD KEY `clienti_email_index` (`email`),
  ADD KEY `clienti_telefono_mobile_index` (`telefono_mobile`),
  ADD KEY `clienti_stato_cliente_index` (`stato_cliente`),
  ADD KEY `clienti_programma_attuale_index` (`programma_attuale`),
  ADD KEY `clienti_data_iscrizione_index` (`data_iscrizione`),
  ADD KEY `clienti_codice_cliente_index` (`codice_cliente`),
  ADD KEY `clienti_codice_referral_index` (`codice_referral`),
  ADD KEY `clienti_cognome_nome_index` (`cognome`,`nome`);

--
-- Indici per le tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indici per le tabelle `impostazioni`
--
ALTER TABLE `impostazioni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `impostazioni_chiave_unique` (`chiave`),
  ADD KEY `impostazioni_gruppo_index` (`gruppo`);

--
-- Indici per le tabelle `impostazioni_sistema`
--
ALTER TABLE `impostazioni_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_categoria_chiave` (`categoria`,`chiave`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_attivo` (`attivo`),
  ADD KEY `idx_ordinamento` (`ordinamento`);

--
-- Indici per le tabelle `lezioni`
--
ALTER TABLE `lezioni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lezioni_programma_id_foreign` (`programma_id`),
  ADD KEY `lezioni_sede_id_foreign` (`sede_id`),
  ADD KEY `lezioni_professionista_id_foreign` (`professionista_id`),
  ADD KEY `lezioni_lezione_padre_id_foreign` (`lezione_padre_id`),
  ADD KEY `lezioni_data_index` (`data`),
  ADD KEY `lezioni_stato_index` (`stato`),
  ADD KEY `lezioni_data_ora_inizio_index` (`data`,`ora_inizio`),
  ADD KEY `lezioni_tipologia_index` (`tipologia`);

--
-- Indici per le tabelle `log_attivita`
--
ALTER TABLE `log_attivita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utente_id` (`utente_id`),
  ADD KEY `idx_azione` (`azione`),
  ADD KEY `idx_modello` (`modello`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indici per le tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `pagamenti`
--
ALTER TABLE `pagamenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pagamenti_numero_fattura_unique` (`numero_fattura`),
  ADD KEY `pagamenti_cliente_id_foreign` (`cliente_id`),
  ADD KEY `pagamenti_programma_id_foreign` (`programma_id`),
  ADD KEY `pagamenti_lezione_id_foreign` (`lezione_id`),
  ADD KEY `pagamenti_stato_index` (`stato`),
  ADD KEY `pagamenti_data_emissione_index` (`data_emissione`),
  ADD KEY `pagamenti_data_scadenza_index` (`data_scadenza`),
  ADD KEY `pagamenti_tipo_index` (`tipo`),
  ADD KEY `pagamenti_metodo_index` (`metodo`);

--
-- Indici per le tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indici per le tabelle `permessi`
--
ALTER TABLE `permessi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_categoria` (`categoria`),
  ADD KEY `idx_attivo` (`attivo`);

--
-- Indici per le tabelle `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indici per le tabelle `professionista_sede`
--
ALTER TABLE `professionista_sede`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professionista_sede_utente_id_sede_id_unique` (`utente_id`,`sede_id`),
  ADD KEY `professionista_sede_sede_id_foreign` (`sede_id`),
  ADD KEY `professionista_sede_attivo_index` (`attivo`);

--
-- Indici per le tabelle `professionisti`
--
ALTER TABLE `professionisti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professionisti_codice_fiscale_unique` (`codice_fiscale`),
  ADD UNIQUE KEY `professionisti_codice_professionista_unique` (`codice_professionista`),
  ADD KEY `professionisti_utente_id_index` (`utente_id`),
  ADD KEY `professionisti_stato_index` (`stato`),
  ADD KEY `professionisti_visibile_pubblico_index` (`visibile_pubblico`),
  ADD KEY `professionisti_cognome_nome_index` (`cognome`,`nome`);

--
-- Indici per le tabelle `programmi`
--
ALTER TABLE `programmi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `programmi_slug_unique` (`slug`),
  ADD KEY `programmi_sede_id_foreign` (`sede_id`),
  ADD KEY `programmi_professionista_id_foreign` (`professionista_id`),
  ADD KEY `programmi_slug_index` (`slug`),
  ADD KEY `programmi_tipologia_index` (`tipologia`),
  ADD KEY `programmi_livello_index` (`livello`),
  ADD KEY `programmi_attivo_index` (`attivo`),
  ADD KEY `programmi_visibile_pubblico_index` (`visibile_pubblico`),
  ADD KEY `programmi_in_evidenza_index` (`in_evidenza`);

--
-- Indici per le tabelle `ruoli`
--
ALTER TABLE `ruoli`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_attivo` (`attivo`);

--
-- Indici per le tabelle `ruolo_permesso`
--
ALTER TABLE `ruolo_permesso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ruolo_permesso` (`ruolo_id`,`permesso_id`),
  ADD KEY `idx_ruolo_id` (`ruolo_id`),
  ADD KEY `idx_permesso_id` (`permesso_id`);

--
-- Indici per le tabelle `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sedes_slug_unique` (`slug`),
  ADD KEY `sedes_attiva_index` (`attiva`),
  ADD KEY `sedes_sede_principale_index` (`sede_principale`);

--
-- Indici per le tabelle `sedi`
--
ALTER TABLE `sedi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sedi_slug_unique` (`slug`),
  ADD KEY `sedi_slug_index` (`slug`),
  ADD KEY `sedi_attiva_index` (`attiva`),
  ADD KEY `sedi_visibile_pubblico_index` (`visibile_pubblico`);

--
-- Indici per le tabelle `test_deployment`
--
ALTER TABLE `test_deployment`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indici per le tabelle `utente_permesso`
--
ALTER TABLE `utente_permesso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_utente_permesso` (`utente_id`,`permesso_id`),
  ADD KEY `idx_utente_id` (`utente_id`),
  ADD KEY `idx_permesso_id` (`permesso_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utenti_email_unique` (`email`),
  ADD KEY `utenti_ruolo_id_foreign` (`ruolo_id`),
  ADD KEY `utenti_email_index` (`email`),
  ADD KEY `utenti_tipo_utente_index` (`tipo_utente`),
  ADD KEY `utenti_attivo_index` (`attivo`),
  ADD KEY `utenti_email_attivo_index` (`email`,`attivo`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `cliente_lezione`
--
ALTER TABLE `cliente_lezione`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `cliente_programma`
--
ALTER TABLE `cliente_programma`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `clienti`
--
ALTER TABLE `clienti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `impostazioni`
--
ALTER TABLE `impostazioni`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `impostazioni_sistema`
--
ALTER TABLE `impostazioni_sistema`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la tabella `lezioni`
--
ALTER TABLE `lezioni`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `log_attivita`
--
ALTER TABLE `log_attivita`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT per la tabella `pagamenti`
--
ALTER TABLE `pagamenti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `permessi`
--
ALTER TABLE `permessi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `professionista_sede`
--
ALTER TABLE `professionista_sede`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `professionisti`
--
ALTER TABLE `professionisti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `programmi`
--
ALTER TABLE `programmi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `ruoli`
--
ALTER TABLE `ruoli`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `ruolo_permesso`
--
ALTER TABLE `ruolo_permesso`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT per la tabella `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `sedi`
--
ALTER TABLE `sedi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `test_deployment`
--
ALTER TABLE `test_deployment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente_permesso`
--
ALTER TABLE `utente_permesso`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cliente_lezione`
--
ALTER TABLE `cliente_lezione`
  ADD CONSTRAINT `cliente_lezione_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_lezione_lezione_id_foreign` FOREIGN KEY (`lezione_id`) REFERENCES `lezioni` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `cliente_programma`
--
ALTER TABLE `cliente_programma`
  ADD CONSTRAINT `cliente_programma_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_programma_programma_id_foreign` FOREIGN KEY (`programma_id`) REFERENCES `programmi` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `clienti`
--
ALTER TABLE `clienti`
  ADD CONSTRAINT `clienti_invitato_da_cliente_id_foreign` FOREIGN KEY (`invitato_da_cliente_id`) REFERENCES `clienti` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `clienti_utente_id_foreign` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `lezioni`
--
ALTER TABLE `lezioni`
  ADD CONSTRAINT `lezioni_lezione_padre_id_foreign` FOREIGN KEY (`lezione_padre_id`) REFERENCES `lezioni` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lezioni_professionista_id_foreign` FOREIGN KEY (`professionista_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lezioni_programma_id_foreign` FOREIGN KEY (`programma_id`) REFERENCES `programmi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lezioni_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedi` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `log_attivita`
--
ALTER TABLE `log_attivita`
  ADD CONSTRAINT `log_attivita_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `pagamenti`
--
ALTER TABLE `pagamenti`
  ADD CONSTRAINT `pagamenti_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagamenti_lezione_id_foreign` FOREIGN KEY (`lezione_id`) REFERENCES `lezioni` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pagamenti_programma_id_foreign` FOREIGN KEY (`programma_id`) REFERENCES `programmi` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `professionista_sede`
--
ALTER TABLE `professionista_sede`
  ADD CONSTRAINT `professionista_sede_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professionista_sede_utente_id_foreign` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `professionisti`
--
ALTER TABLE `professionisti`
  ADD CONSTRAINT `professionisti_utente_id_foreign` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `programmi`
--
ALTER TABLE `programmi`
  ADD CONSTRAINT `programmi_professionista_id_foreign` FOREIGN KEY (`professionista_id`) REFERENCES `utenti` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programmi_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedi` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `ruolo_permesso`
--
ALTER TABLE `ruolo_permesso`
  ADD CONSTRAINT `ruolo_permesso_ibfk_1` FOREIGN KEY (`ruolo_id`) REFERENCES `ruoli` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ruolo_permesso_ibfk_2` FOREIGN KEY (`permesso_id`) REFERENCES `permessi` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `utente_permesso`
--
ALTER TABLE `utente_permesso`
  ADD CONSTRAINT `utente_permesso_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `utente_permesso_ibfk_2` FOREIGN KEY (`permesso_id`) REFERENCES `permessi` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `utenti`
--
ALTER TABLE `utenti`
  ADD CONSTRAINT `utenti_ruolo_id_foreign` FOREIGN KEY (`ruolo_id`) REFERENCES `ruoli` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
