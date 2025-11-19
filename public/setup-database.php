<?php
/**
 * Script Setup Database - MAGIA DONNA
 *
 * Questo script aggiorna il database con le nuove tabelle e colonne necessarie.
 * Eseguilo una sola volta visitando: https://tuosito.com/setup-database.php
 *
 * IMPORTANTE: Elimina questo file dopo l'esecuzione per motivi di sicurezza!
 */

// Carica configurazione Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Connessione database
$db = DB::connection()->getPdo();

// Array per tracciare risultati
$risultati = [];
$errori = [];
$successi = [];

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - MAGIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #8B5CF6;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .step.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .step.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .step.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .step h3 {
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        .icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
        }
        .icon.success { background: #28a745; color: white; }
        .icon.error { background: #dc3545; color: white; }
        .icon.warning { background: #ffc107; color: white; }
        .summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            text-align: center;
        }
        .summary h2 { margin-bottom: 15px; }
        .summary .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
        }
        .summary .stat { text-align: center; }
        .summary .stat-number {
            font-size: 32px;
            font-weight: bold;
            display: block;
        }
        .summary .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .warning-box h3 {
            margin-bottom: 10px;
            color: #856404;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Setup Database - MAGIA DONNA</h1>
            <p>Aggiornamento database per nuove funzionalità</p>
        </div>

        <div class="content">
            <div class="warning-box">
                <h3>⚠️ Attenzione</h3>
                <p>Questo script modificherà il database. Assicurati di aver fatto un backup prima di procedere.</p>
                <p style="margin-top: 10px;"><strong>IMPORTANTE:</strong> Elimina questo file dopo l'esecuzione!</p>
            </div>

            <?php

            // ========================================
            // 1. AGGIUNGI COLONNE TABELLA CLIENTI
            // ========================================

            $colonne_clienti = [
                'tipo_cliente' => "ENUM('prova', 'effettiva') DEFAULT 'effettiva' AFTER `stato_cliente`",
                'data_iscrizione' => "DATE NULL AFTER `tipo_cliente`",
                'peso_iniziale' => "DECIMAL(5,2) NULL AFTER `data_iscrizione`",
                'altezza' => "DECIMAL(5,2) NULL AFTER `peso_iniziale`"
            ];

            foreach ($colonne_clienti as $nome_colonna => $definizione) {
                try {
                    // Verifica se la colonna esiste già
                    $exists = $db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS
                                         WHERE TABLE_SCHEMA = DATABASE()
                                         AND TABLE_NAME = 'clienti'
                                         AND COLUMN_NAME = '$nome_colonna'")->fetch();

                    if (!$exists) {
                        $db->exec("ALTER TABLE `clienti` ADD COLUMN `$nome_colonna` $definizione");
                        $successi[] = "✅ Colonna <code>clienti.$nome_colonna</code> aggiunta con successo";
                        echo "<div class='step success'>
                                <h3><span class='icon success'>✓</span> Colonna clienti.$nome_colonna</h3>
                                <p>Aggiunta con successo</p>
                              </div>";
                    } else {
                        echo "<div class='step warning'>
                                <h3><span class='icon warning'>!</span> Colonna clienti.$nome_colonna</h3>
                                <p>Già esistente - saltata</p>
                              </div>";
                    }
                } catch (Exception $e) {
                    $errori[] = "❌ Errore colonna clienti.$nome_colonna: " . $e->getMessage();
                    echo "<div class='step error'>
                            <h3><span class='icon error'>✗</span> Errore clienti.$nome_colonna</h3>
                            <p>{$e->getMessage()}</p>
                          </div>";
                }
            }

            // ========================================
            // 2. CREA TABELLA PARAMETRI_CORPOREI
            // ========================================

            try {
                $sql = "CREATE TABLE IF NOT EXISTS `parametri_corporei` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

                $db->exec($sql);
                $successi[] = "✅ Tabella <code>parametri_corporei</code> creata con successo";
                echo "<div class='step success'>
                        <h3><span class='icon success'>✓</span> Tabella parametri_corporei</h3>
                        <p>Creata con successo (o già esistente)</p>
                      </div>";
            } catch (Exception $e) {
                $errori[] = "❌ Errore tabella parametri_corporei: " . $e->getMessage();
                echo "<div class='step error'>
                        <h3><span class='icon error'>✗</span> Errore parametri_corporei</h3>
                        <p>{$e->getMessage()}</p>
                      </div>";
            }

            // ========================================
            // 3. CREA TABELLA LOG_ATTIVITA
            // ========================================

            try {
                $sql = "CREATE TABLE IF NOT EXISTS `log_attivita` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

                $db->exec($sql);
                $successi[] = "✅ Tabella <code>log_attivita</code> creata con successo";
                echo "<div class='step success'>
                        <h3><span class='icon success'>✓</span> Tabella log_attivita</h3>
                        <p>Creata con successo (o già esistente)</p>
                      </div>";
            } catch (Exception $e) {
                $errori[] = "❌ Errore tabella log_attivita: " . $e->getMessage();
                echo "<div class='step error'>
                        <h3><span class='icon error'>✗</span> Errore log_attivita</h3>
                        <p>{$e->getMessage()}</p>
                      </div>";
            }

            // ========================================
            // 4. AGGIUNGI COLONNE TABELLA PAGAMENTI_CLIENTI
            // ========================================

            $colonne_pagamenti = [
                'commissione_paypal' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `importo`",
                'importo_netto' => "DECIMAL(10,2) DEFAULT NULL AFTER `commissione_paypal`"
            ];

            // Verifica prima se la tabella pagamenti_clienti esiste
            $table_exists = $db->query("SELECT TABLE_NAME FROM information_schema.TABLES
                                       WHERE TABLE_SCHEMA = DATABASE()
                                       AND TABLE_NAME = 'pagamenti_clienti'")->fetch();

            if ($table_exists) {
                foreach ($colonne_pagamenti as $nome_colonna => $definizione) {
                    try {
                        $exists = $db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS
                                             WHERE TABLE_SCHEMA = DATABASE()
                                             AND TABLE_NAME = 'pagamenti_clienti'
                                             AND COLUMN_NAME = '$nome_colonna'")->fetch();

                        if (!$exists) {
                            $db->exec("ALTER TABLE `pagamenti_clienti` ADD COLUMN `$nome_colonna` $definizione");
                            $successi[] = "✅ Colonna <code>pagamenti_clienti.$nome_colonna</code> aggiunta con successo";
                            echo "<div class='step success'>
                                    <h3><span class='icon success'>✓</span> Colonna pagamenti_clienti.$nome_colonna</h3>
                                    <p>Aggiunta con successo</p>
                                  </div>";
                        } else {
                            echo "<div class='step warning'>
                                    <h3><span class='icon warning'>!</span> Colonna pagamenti_clienti.$nome_colonna</h3>
                                    <p>Già esistente - saltata</p>
                                  </div>";
                        }
                    } catch (Exception $e) {
                        $errori[] = "❌ Errore colonna pagamenti_clienti.$nome_colonna: " . $e->getMessage();
                        echo "<div class='step error'>
                                <h3><span class='icon error'>✗</span> Errore pagamenti_clienti.$nome_colonna</h3>
                                <p>{$e->getMessage()}</p>
                              </div>";
                    }
                }
            } else {
                echo "<div class='step warning'>
                        <h3><span class='icon warning'>!</span> Tabella pagamenti_clienti</h3>
                        <p>La tabella non esiste ancora - verrà creata dalla migrazione Laravel</p>
                      </div>";
            }

            ?>

            <div class="summary">
                <h2>📊 Riepilogo Operazioni</h2>
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number"><?= count($successi) ?></span>
                        <span class="stat-label">Operazioni Riuscite</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?= count($errori) ?></span>
                        <span class="stat-label">Errori</span>
                    </div>
                </div>

                <?php if (count($errori) === 0): ?>
                    <p style="margin-top: 20px; font-size: 18px;">✅ Setup completato con successo!</p>
                    <p style="margin-top: 10px; opacity: 0.9;">
                        <strong>IMPORTANTE:</strong> Elimina questo file per motivi di sicurezza:<br>
                        <code style="background: rgba(255,255,255,0.2); color: white; padding: 5px 10px; margin-top: 10px; display: inline-block;">rm public/setup-database.php</code>
                    </p>
                <?php else: ?>
                    <p style="margin-top: 20px; font-size: 18px;">⚠️ Setup completato con alcuni errori</p>
                    <p style="margin-top: 10px; opacity: 0.9;">Verifica gli errori sopra e contatta il supporto se necessario.</p>
                <?php endif; ?>
            </div>

            <?php if (count($errori) > 0): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: #dc3545;">Errori Riscontrati:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($errori as $errore): ?>
                            <li style="margin-bottom: 10px; color: #721c24;"><?= htmlspecialchars($errore) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
