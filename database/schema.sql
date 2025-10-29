-- CoreSCM Database Schema
-- Extracted from CoreGre backup.sql
-- Tables for SCM (Supply Chain Management) module

-- ========================================
-- TABLE: scm_laboratories
-- Laboratori esterni per lavorazioni SCM
-- ========================================
CREATE TABLE `scm_laboratories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del laboratorio',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email per accesso e comunicazioni',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username per accesso frontend',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password hash per accesso frontend',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Laboratorio attivo/disattivo',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Ultimo accesso al frontend',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_active` (`is_active`),
  KEY `idx_last_login` (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Laboratori esterni per lavorazioni SCM';

-- ========================================
-- TABLE: scm_launches
-- Lanci di lavorazione per laboratori esterni
-- ========================================
CREATE TABLE `scm_launches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `launch_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numero identificativo del lancio (es: 7001)',
  `laboratory_id` int(11) NOT NULL COMMENT 'ID del laboratorio assegnato',
  `launch_date` date NOT NULL COMMENT 'Data del lancio',
  `phases_cycle` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ciclo fasi separate da ; (es: TAGLIO;PREPARAZIONE;ORLATURA;SPEDITO)',
  `status` enum('IN_PREPARAZIONE','IN_LAVORAZIONE','BLOCCATO','COMPLETATO') COLLATE utf8mb4_unicode_ci DEFAULT 'IN_PREPARAZIONE' COMMENT 'Stato generale del lancio',
  `blocked_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Motivo del blocco se status = BLOCCATO',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note generali del lancio',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `launch_number` (`launch_number`),
  KEY `idx_laboratory` (`laboratory_id`),
  KEY `idx_status` (`status`),
  KEY `idx_launch_date` (`launch_date`),
  KEY `idx_launch_number` (`launch_number`),
  CONSTRAINT `fk_launches_laboratory` FOREIGN KEY (`laboratory_id`) REFERENCES `scm_laboratories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lanci di lavorazione per laboratori esterni';

-- ========================================
-- TABLE: scm_launch_articles
-- Articoli inclusi in ogni lancio SCM
-- ========================================
CREATE TABLE `scm_launch_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `launch_id` int(11) NOT NULL COMMENT 'ID del lancio di appartenenza',
  `article_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome articolo (es: MODELLO X ROSSO)',
  `total_pairs` int(11) NOT NULL COMMENT 'Numero totale di paia per questo articolo',
  `article_order` int(11) NOT NULL DEFAULT '1' COMMENT 'Ordine di visualizzazione nell articolo',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche per questo articolo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_launch_article_order` (`launch_id`,`article_order`),
  KEY `idx_launch` (`launch_id`),
  KEY `idx_article_order` (`launch_id`,`article_order`),
  CONSTRAINT `fk_articles_launch` FOREIGN KEY (`launch_id`) REFERENCES `scm_launches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Articoli inclusi in ogni lancio SCM';

-- ========================================
-- TABLE: scm_launch_phases
-- Fasi del ciclo di lavorazione per ogni lancio
-- ========================================
CREATE TABLE `scm_launch_phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `launch_id` int(11) NOT NULL COMMENT 'ID del lancio di appartenenza',
  `phase_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome della fase (es: TAGLIO, PREPARAZIONE, etc)',
  `phase_order` int(11) NOT NULL COMMENT 'Ordine della fase nel ciclo (1, 2, 3, etc)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_launch_phase_order` (`launch_id`,`phase_order`),
  KEY `idx_launch` (`launch_id`),
  KEY `idx_phase_order` (`launch_id`,`phase_order`),
  CONSTRAINT `fk_phases_launch` FOREIGN KEY (`launch_id`) REFERENCES `scm_launches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fasi del ciclo di lavorazione per ogni lancio';

-- ========================================
-- TABLE: scm_progress_tracking
-- Tracciamento avanzamento per ogni articolo/fase
-- ========================================
CREATE TABLE `scm_progress_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `launch_id` int(11) NOT NULL COMMENT 'ID del lancio',
  `article_id` int(11) NOT NULL COMMENT 'ID dell articolo del lancio',
  `phase_id` int(11) NOT NULL COMMENT 'ID della fase del lancio',
  `status` enum('NON_INIZIATA','IN_CORSO','COMPLETATA','BLOCCATA') COLLATE utf8mb4_unicode_ci DEFAULT 'NON_INIZIATA' COMMENT 'Stato della fase per questo articolo',
  `completed_pairs` int(11) DEFAULT '0' COMMENT 'Numero di paia completate per questa fase/articolo',
  `is_blocked` tinyint(1) DEFAULT '0' COMMENT 'Fase bloccata per problemi',
  `blocked_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Motivo del blocco se is_blocked = 1',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche per questa fase/articolo',
  `started_at` timestamp NULL DEFAULT NULL COMMENT 'Quando è stata iniziata la fase',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Quando è stata completata la fase',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_article_phase` (`article_id`,`phase_id`),
  KEY `idx_launch` (`launch_id`),
  KEY `idx_article` (`article_id`),
  KEY `idx_phase` (`phase_id`),
  KEY `idx_status` (`status`),
  KEY `idx_blocked` (`is_blocked`),
  CONSTRAINT `fk_progress_launch` FOREIGN KEY (`launch_id`) REFERENCES `scm_launches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progress_article` FOREIGN KEY (`article_id`) REFERENCES `scm_launch_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_progress_phase` FOREIGN KEY (`phase_id`) REFERENCES `scm_launch_phases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracciamento avanzamento per ogni articolo/fase';

-- ========================================
-- TABLE: scm_settings
-- Impostazioni del modulo SCM
-- ========================================
CREATE TABLE `scm_settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `system_name` varchar(255) NOT NULL DEFAULT 'SCM Emmegiemme',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `timezone` varchar(64) NOT NULL DEFAULT 'Europe/Rome',
  `language` char(2) NOT NULL DEFAULT 'it',
  `launch_number_prefix` varchar(10) NOT NULL DEFAULT 'LAN',
  `auto_start_phases` tinyint(1) NOT NULL DEFAULT '1',
  `require_phase_notes` tinyint(1) NOT NULL DEFAULT '0',
  `max_articles_per_launch` int(10) UNSIGNED NOT NULL DEFAULT '50',
  `notify_launch_completed` tinyint(1) NOT NULL DEFAULT '1',
  `notify_phase_blocked` tinyint(1) NOT NULL DEFAULT '1',
  `notify_laboratory_login` tinyint(1) NOT NULL DEFAULT '0',
  `notification_email` varchar(255) DEFAULT NULL,
  `session_timeout` int(10) UNSIGNED NOT NULL DEFAULT '120',
  `max_login_attempts` int(10) UNSIGNED NOT NULL DEFAULT '5',
  `password_min_length` int(10) UNSIGNED NOT NULL DEFAULT '8',
  `require_password_symbols` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ========================================
-- TABLE: scm_standard_phases
-- Fasi standard predefinite per i cicli di lavorazione
-- ========================================
CREATE TABLE `scm_standard_phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phase_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome della fase standard',
  `phase_order` int(11) NOT NULL COMMENT 'Ordine suggerito per questa fase',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Fase attiva/disattiva',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phase_name` (`phase_name`),
  KEY `idx_active_order` (`is_active`,`phase_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fasi standard predefinite per i cicli di lavorazione';
