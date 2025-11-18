<?php
/**
 * Script Fix Database Pagamenti - MAGIA DONNA
 *
 * Corregge l'errore nel nome tabella: pagamenti_clienti → pagamenti
 * Aggiunge le colonne commissione_paypal e importo_netto
 *
 * Eseguilo visitando: https://tuosito.com/fix-database-pagamenti.php
 *
 * IMPORTANTE: Elimina questo file dopo l'esecuzione!
 */

// Carica configurazione Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Connessione database
$db = DB::connection()->getPdo();

// Array per tracciare risultati
$successi = [];
$errori = [];

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database Pagamenti - MAGIA DONNA</title>
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
        .step.info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
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
        .icon.info { background: #17a2b8; color: white; }
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
        .info-box {
            background: #d1ecf1;
            border: 2px solid #17a2b8;
            color: #0c5460;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            margin-bottom: 10px;
            color: #0c5460;
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
            <h1>🔧 Fix Database Pagamenti - MAGIA DONNA</h1>
            <p>Correzione tabella pagamenti e aggiunta colonne PayPal</p>
        </div>

        <div class="content">
            <div class="info-box">
                <h3>ℹ️ Cosa fa questo script</h3>
                <p>Questo script corregge l'errore nel file setup precedente:</p>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <li>Cerca la tabella <code>pagamenti</code> (nome corretto)</li>
                    <li>Aggiunge colonna <code>commissione_paypal</code> DECIMAL(10,2)</li>
                    <li>Aggiunge colonna <code>importo_netto</code> DECIMAL(10,2)</li>
                </ul>
            </div>

            <div class="warning-box">
                <h3>⚠️ Attenzione</h3>
                <p>Questo script modificherà il database. Assicurati di aver fatto un backup prima di procedere.</p>
                <p style="margin-top: 10px;"><strong>IMPORTANTE:</strong> Elimina questo file dopo l'esecuzione!</p>
            </div>

            <?php

            // ========================================
            // 1. VERIFICA ESISTENZA TABELLA PAGAMENTI
            // ========================================

            echo "<div class='step info'>
                    <h3><span class='icon info'>i</span> Step 1: Verifica tabella pagamenti</h3>
                    <p>Controllo esistenza tabella nel database...</p>
                  </div>";

            try {
                $table_exists = $db->query("SELECT TABLE_NAME FROM information_schema.TABLES
                                           WHERE TABLE_SCHEMA = DATABASE()
                                           AND TABLE_NAME = 'pagamenti'")->fetch();

                if ($table_exists) {
                    $successi[] = "✅ Tabella <code>pagamenti</code> trovata";
                    echo "<div class='step success'>
                            <h3><span class='icon success'>✓</span> Tabella pagamenti</h3>
                            <p>Tabella esistente e pronta per le modifiche</p>
                          </div>";
                } else {
                    $errori[] = "❌ Tabella <code>pagamenti</code> non trovata nel database";
                    echo "<div class='step error'>
                            <h3><span class='icon error'>✗</span> Errore: Tabella non trovata</h3>
                            <p>La tabella <code>pagamenti</code> non esiste. Esegui prima le migrazioni Laravel:<br>
                            <code>php artisan migrate</code></p>
                          </div>";
                }
            } catch (Exception $e) {
                $errori[] = "❌ Errore verifica tabella: " . $e->getMessage();
                echo "<div class='step error'>
                        <h3><span class='icon error'>✗</span> Errore verifica tabella</h3>
                        <p>{$e->getMessage()}</p>
                      </div>";
            }

            // ========================================
            // 2. AGGIUNGI COLONNE SE TABELLA ESISTE
            // ========================================

            if ($table_exists) {

                echo "<div class='step info'>
                        <h3><span class='icon info'>i</span> Step 2: Aggiunta colonne PayPal</h3>
                        <p>Aggiungo le colonne commissione_paypal e importo_netto...</p>
                      </div>";

                $colonne_pagamenti = [
                    'commissione_paypal' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Commissione PayPal 4%' AFTER `importo`",
                    'importo_netto' => "DECIMAL(10,2) DEFAULT NULL COMMENT 'Importo al netto della commissione' AFTER `commissione_paypal`"
                ];

                foreach ($colonne_pagamenti as $nome_colonna => $definizione) {
                    try {
                        // Verifica se la colonna esiste già
                        $exists = $db->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS
                                             WHERE TABLE_SCHEMA = DATABASE()
                                             AND TABLE_NAME = 'pagamenti'
                                             AND COLUMN_NAME = '$nome_colonna'")->fetch();

                        if (!$exists) {
                            $db->exec("ALTER TABLE `pagamenti` ADD COLUMN `$nome_colonna` $definizione");
                            $successi[] = "✅ Colonna <code>pagamenti.$nome_colonna</code> aggiunta con successo";
                            echo "<div class='step success'>
                                    <h3><span class='icon success'>✓</span> Colonna pagamenti.$nome_colonna</h3>
                                    <p>Aggiunta con successo alla tabella</p>
                                  </div>";
                        } else {
                            echo "<div class='step warning'>
                                    <h3><span class='icon warning'>!</span> Colonna pagamenti.$nome_colonna</h3>
                                    <p>Già esistente - nessuna modifica necessaria</p>
                                  </div>";
                        }
                    } catch (Exception $e) {
                        $errori[] = "❌ Errore colonna pagamenti.$nome_colonna: " . $e->getMessage();
                        echo "<div class='step error'>
                                <h3><span class='icon error'>✗</span> Errore pagamenti.$nome_colonna</h3>
                                <p>{$e->getMessage()}</p>
                              </div>";
                    }
                }

                // ========================================
                // 3. VERIFICA STRUTTURA FINALE
                // ========================================

                echo "<div class='step info'>
                        <h3><span class='icon info'>i</span> Step 3: Verifica struttura finale</h3>
                        <p>Controllo finale della struttura della tabella...</p>
                      </div>";

                try {
                    $columns = $db->query("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_COMMENT
                                          FROM information_schema.COLUMNS
                                          WHERE TABLE_SCHEMA = DATABASE()
                                          AND TABLE_NAME = 'pagamenti'
                                          AND COLUMN_NAME IN ('importo', 'commissione_paypal', 'importo_netto')
                                          ORDER BY ORDINAL_POSITION")->fetchAll(PDO::FETCH_ASSOC);

                    if (count($columns) >= 2) {
                        $successi[] = "✅ Struttura tabella verificata con successo";
                        echo "<div class='step success'>
                                <h3><span class='icon success'>✓</span> Struttura tabella verificata</h3>
                                <p>Colonne trovate nella tabella pagamenti:</p>
                                <ul style='margin-top: 10px; margin-left: 20px; font-size: 13px;'>";

                        foreach ($columns as $col) {
                            $comment = $col['COLUMN_COMMENT'] ? " - {$col['COLUMN_COMMENT']}" : "";
                            echo "<li><code>{$col['COLUMN_NAME']}</code> ({$col['DATA_TYPE']}){$comment}</li>";
                        }

                        echo "</ul>
                              </div>";
                    }
                } catch (Exception $e) {
                    $errori[] = "⚠️ Errore verifica struttura: " . $e->getMessage();
                    echo "<div class='step warning'>
                            <h3><span class='icon warning'>!</span> Avviso verifica struttura</h3>
                            <p>{$e->getMessage()}</p>
                          </div>";
                }
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
                    <p style="margin-top: 20px; font-size: 18px;">✅ Fix completato con successo!</p>
                    <p style="margin-top: 10px; opacity: 0.9;">
                        La tabella <code>pagamenti</code> è stata aggiornata correttamente con le colonne PayPal.
                    </p>
                    <p style="margin-top: 15px; opacity: 0.9;">
                        <strong>IMPORTANTE:</strong> Elimina questo file per motivi di sicurezza:<br>
                        <code style="background: rgba(255,255,255,0.2); color: white; padding: 5px 10px; margin-top: 10px; display: inline-block;">rm public/fix-database-pagamenti.php</code>
                    </p>
                <?php else: ?>
                    <p style="margin-top: 20px; font-size: 18px;">⚠️ Fix completato con alcuni errori</p>
                    <p style="margin-top: 10px; opacity: 0.9;">Verifica gli errori sopra. Potrebbe essere necessario eseguire prima le migrazioni Laravel.</p>
                    <p style="margin-top: 10px; opacity: 0.9;">
                        Comando: <code style="background: rgba(255,255,255,0.2); color: white; padding: 5px 10px;">php artisan migrate</code>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (count($errori) > 0): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: #dc3545;">❌ Errori Riscontrati:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($errori as $errore): ?>
                            <li style="margin-bottom: 10px; color: #721c24; padding: 8px; background: #f8d7da; border-radius: 4px;">
                                <?= htmlspecialchars($errore) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (count($successi) > 0): ?>
                <div style="margin-top: 30px; padding: 20px; background: #d4edda; border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: #155724;">✅ Operazioni Completate:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($successi as $successo): ?>
                            <li style="margin-bottom: 8px; color: #155724; padding: 8px; background: white; border-radius: 4px;">
                                <?= $successo ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
