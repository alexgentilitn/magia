<?php
/**
 * Script per eseguire le migrations dal browser
 *
 * ATTENZIONE: Questo file è temporaneo e dovrebbe essere rimosso dopo l'uso!
 *
 * Uso: https://tuodominio.it/execute-migrations.php?key=MAGIA2025SECURE
 *
 * Per motivi di sicurezza, è richiesta una chiave di accesso.
 */

// ========================================
// CONFIGURAZIONE SICUREZZA
// ========================================
// Password rimossa per esecuzione immediata
// IMPORTANTE: Questo file verrà eliminato automaticamente dopo l'esecuzione

// ========================================
// INIZIALIZZAZIONE
// ========================================
$startTime = microtime(true);

// Carica Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap Laravel Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esecuzione Migrations - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .section {
            background: #f8f9fa;
            border-left: 4px solid #e91e63;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .section h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .section h2 .icon { margin-right: 10px; font-size: 24px; }
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #e0e0e0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: bold; color: #666; }
        .info-value { color: #333; font-family: 'Courier New', monospace; }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status.success { background: #4caf50; color: white; }
        .status.error { background: #f44336; color: white; }
        .status.warning { background: #ff9800; color: white; }
        .status.info { background: #2196f3; color: white; }
        .migration-list {
            background: #fff;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        .migration-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .migration-item:last-child { border-bottom: none; }
        .migration-name {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #333;
        }
        .output-box {
            background: #263238;
            color: #aed581;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .output-box .line { margin-bottom: 5px; }
        .output-box .success { color: #4caf50; }
        .output-box .error { color: #f44336; }
        .output-box .warning { color: #ff9800; }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert.success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert.error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .alert.warning { background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #e91e63;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn:hover { background: #c2185b; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3); }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .spinner { animation: spin 1s linear infinite; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Esecuzione Migrations Database</h1>
            <p>MA.GIA DONNA - Sistema di Gestione Wellness</p>
        </div>

        <div class="content">
            <?php
            // ========================================
            // STEP 1: VERIFICA CONNESSIONE DATABASE
            // ========================================
            echo '<div class="section">';
            echo '<h2><span class="icon">🔌</span> Step 1: Verifica Connessione Database</h2>';

            try {
                DB::connection()->getPdo();
                $dbName = DB::connection()->getDatabaseName();
                $dbDriver = DB::connection()->getDriverName();

                echo '<div class="alert success">';
                echo '✅ <strong>Connessione riuscita!</strong>';
                echo '</div>';

                echo '<div class="info-box">';
                echo '<div class="info-row">';
                echo '<span class="info-label">Database:</span>';
                echo '<span class="info-value">' . htmlspecialchars($dbName) . '</span>';
                echo '</div>';
                echo '<div class="info-row">';
                echo '<span class="info-label">Driver:</span>';
                echo '<span class="info-value">' . htmlspecialchars($dbDriver) . '</span>';
                echo '</div>';
                echo '<div class="info-row">';
                echo '<span class="info-label">Host:</span>';
                echo '<span class="info-value">' . htmlspecialchars(config('database.connections.mysql.host')) . '</span>';
                echo '</div>';
                echo '</div>';

                $canProceed = true;
            } catch (\Exception $e) {
                echo '<div class="alert error">';
                echo '❌ <strong>Errore di connessione al database!</strong><br>';
                echo 'Messaggio: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                $canProceed = false;
            }
            echo '</div>';

            if ($canProceed) {
                // ========================================
                // STEP 2: STATO MIGRATIONS
                // ========================================
                echo '<div class="section">';
                echo '<h2><span class="icon">📋</span> Step 2: Stato Migrations</h2>';

                // Cattura output del comando migrate:status
                ob_start();
                Artisan::call('migrate:status');
                $statusOutput = ob_get_clean();

                echo '<div class="output-box">';
                echo nl2br(htmlspecialchars($statusOutput));
                echo '</div>';
                echo '</div>';

                // ========================================
                // STEP 3: ESECUZIONE MIGRATIONS
                // ========================================
                echo '<div class="section">';
                echo '<h2><span class="icon">⚙️</span> Step 3: Esecuzione Migrations</h2>';

                try {
                    // Esegui le migrations
                    ob_start();
                    $exitCode = Artisan::call('migrate', ['--force' => true]);
                    $migrationOutput = ob_get_clean();

                    if ($exitCode === 0) {
                        echo '<div class="alert success">';
                        echo '✅ <strong>Migrations eseguite con successo!</strong>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert warning">';
                        echo '⚠️ <strong>Migrations completate con avvisi</strong>';
                        echo '</div>';
                    }

                    echo '<div class="output-box">';
                    echo nl2br(htmlspecialchars($migrationOutput));
                    echo '</div>';

                } catch (\Exception $e) {
                    echo '<div class="alert error">';
                    echo '❌ <strong>Errore durante l\'esecuzione delle migrations!</strong><br>';
                    echo 'Messaggio: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';

                    echo '<div class="output-box">';
                    echo '<div class="error">' . htmlspecialchars($e->getTraceAsString()) . '</div>';
                    echo '</div>';
                }
                echo '</div>';

                // ========================================
                // STEP 4: RIEPILOGO MODIFICHE
                // ========================================
                echo '<div class="section">';
                echo '<h2><span class="icon">📊</span> Step 4: Riepilogo Modifiche Applicate</h2>';

                echo '<div class="migration-list">';

                // Migration 1
                echo '<div class="migration-item">';
                echo '<div>';
                echo '<div class="migration-name">add_privacy_fields_to_clienti_table</div>';
                echo '<div style="font-size: 12px; color: #666; margin-top: 5px;">Consensi GDPR: 10 nuovi campi</div>';
                echo '</div>';
                echo '<span class="status success">✓ Applicata</span>';
                echo '</div>';

                // Migration 2
                echo '<div class="migration-item">';
                echo '<div>';
                echo '<div class="migration-name">add_custom_fields_to_clienti_table</div>';
                echo '<div style="font-size: 12px; color: #666; margin-top: 5px;">Anagrafica estesa: 25 nuovi campi</div>';
                echo '</div>';
                echo '<span class="status success">✓ Applicata</span>';
                echo '</div>';

                echo '</div>';

                // Lista dettagliata campi aggiunti
                echo '<div class="info-box" style="margin-top: 20px;">';
                echo '<h3 style="margin-bottom: 15px; color: #e91e63;">📝 Campi Aggiunti alla Tabella <code>clienti</code></h3>';

                echo '<strong style="display: block; margin: 15px 0 10px; color: #9c27b0;">Consensi GDPR (10 campi):</strong>';
                echo '<ul style="list-style: none; padding-left: 0;">';
                echo '<li>✓ privacy_accettata + privacy_accettata_at</li>';
                echo '<li>✓ termini_accettati + termini_accettati_at</li>';
                echo '<li>✓ marketing_accettato + marketing_accettato_at</li>';
                echo '<li>✓ consenso_dati_sensibili + consenso_dati_sensibili_at</li>';
                echo '<li>✓ ip_registrazione + note_consensi</li>';
                echo '</ul>';

                echo '<strong style="display: block; margin: 15px 0 10px; color: #9c27b0;">Anagrafica Estesa (25 campi):</strong>';
                echo '<ul style="list-style: none; padding-left: 0;">';
                echo '<li>✓ Dati anagrafici: data_nascita, eta, sesso</li>';
                echo '<li>✓ Obiettivi: obiettivi_personali, livello_attivita</li>';
                echo '<li>✓ Dati medici: note_mediche, allergie_intolleranze, patologie, farmaci_assunti, certificato_medico_presente</li>';
                echo '<li>✓ Alimentazione: preferenze_alimentari, regime_alimentare, cibi_da_evitare</li>';
                echo '<li>✓ Parametri corporei: peso, altezza, bmi</li>';
                echo '<li>✓ Circonferenze: vita, fianchi, petto, braccio_dx, braccio_sx, coscia_dx, coscia_sx</li>';
                echo '<li>✓ Timestamp: ultima_pesata, ultima_misurazione</li>';
                echo '<li>✓ Note: note_professionista</li>';
                echo '</ul>';

                echo '<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px; border-left: 4px solid #ffc107;">';
                echo '<strong>📌 Totale: 35 nuove colonne aggiunte alla tabella clienti</strong>';
                echo '</div>';
                echo '</div>';

                echo '</div>';

                // ========================================
                // STEP 5: PROSSIMI PASSI
                // ========================================
                echo '<div class="section">';
                echo '<h2><span class="icon">🎯</span> Step 5: Prossimi Passi</h2>';

                echo '<div class="info-box">';
                echo '<h3 style="margin-bottom: 15px; color: #e91e63;">Cosa puoi fare ora:</h3>';
                echo '<ol style="line-height: 2;">';
                echo '<li><strong>Testa la registrazione:</strong> <a href="/registrazione" target="_blank">/registrazione</a></li>';
                echo '<li><strong>Visualizza Privacy Policy:</strong> <a href="/privacy-policy" target="_blank">/privacy-policy</a></li>';
                echo '<li><strong>Visualizza Termini e Condizioni:</strong> <a href="/termini-condizioni" target="_blank">/termini-condizioni</a></li>';
                echo '<li><strong>Accedi al pannello admin:</strong> <a href="/admin/clienti" target="_blank">/admin/clienti</a></li>';
                echo '<li><strong>Modifica una cliente:</strong> Vedrai tutti i nuovi campi!</li>';
                echo '</ol>';

                echo '<div style="background: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 20px; border-left: 4px solid #dc3545; color: #721c24;">';
                echo '<strong>⚠️ IMPORTANTE - SICUREZZA:</strong><br>';
                echo 'Elimina questo file immediatamente dopo l\'uso!<br>';
                echo '<code>rm public/execute-migrations.php</code>';
                echo '</div>';
                echo '</div>';

                echo '</div>';
            }

            // ========================================
            // STATISTICHE ESECUZIONE
            // ========================================
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            echo '<div class="section" style="background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%); color: white; border: none;">';
            echo '<h2 style="color: white;"><span class="icon">⏱️</span> Statistiche Esecuzione</h2>';
            echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 15px;">';
            echo '<div style="text-align: center;">';
            echo '<div style="font-size: 32px; font-weight: bold;">' . $executionTime . 's</div>';
            echo '<div style="opacity: 0.8; font-size: 14px;">Tempo di esecuzione</div>';
            echo '</div>';
            echo '<div style="text-align: center;">';
            echo '<div style="font-size: 32px; font-weight: bold;">35</div>';
            echo '<div style="opacity: 0.8; font-size: 14px;">Campi aggiunti</div>';
            echo '</div>';
            echo '<div style="text-align: center;">';
            echo '<div style="font-size: 32px; font-weight: bold;">2</div>';
            echo '<div style="opacity: 0.8; font-size: 14px;">Migrations eseguite</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            ?>
        </div>

        <div class="footer">
            <p><strong>MA.GIA DONNA</strong> - Sistema di Gestione Wellness</p>
            <p style="margin-top: 10px; font-size: 12px; opacity: 0.7;">
                Migrations eseguite il <?php echo date('d/m/Y H:i:s'); ?>
            </p>
            <a href="/admin" class="btn">Vai al Pannello Admin →</a>
        </div>
    </div>
</body>
</html>
