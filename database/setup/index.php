<?php
/**
 * Database Setup - Menu Principale
 *
 * Pannello di controllo per eseguire tutti gli script di setup database
 * Accesso: http://tuodominio.com/database/setup/
 *
 * IMPORTANTE: Cancellare o proteggere questa cartella dopo l'uso!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - MA.GIA DONNA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #e91e8c 0%, #7b2869 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: start;
        }
        .warning-icon {
            font-size: 2em;
            margin-right: 15px;
        }
        .warning-content h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        .warning-content ul {
            margin-left: 20px;
            color: #856404;
        }
        .script-list {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }
        .script-item {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .script-item:hover {
            border-color: #7b2869;
            box-shadow: 0 5px 15px rgba(123, 40, 105, 0.2);
            transform: translateY(-2px);
        }
        .script-info {
            flex: 1;
        }
        .script-number {
            background: #7b2869;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            font-weight: bold;
            margin-right: 20px;
        }
        .script-title {
            font-size: 1.3em;
            color: #7b2869;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .script-desc {
            color: #6c757d;
            line-height: 1.5;
        }
        .script-btn {
            background: linear-gradient(135deg, #e91e8c 0%, #7b2869 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(123, 40, 105, 0.3);
        }
        .script-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(123, 40, 105, 0.4);
        }
        .info-box {
            background: #d1ecf1;
            border: 2px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .info-box h3 {
            color: #0c5460;
            margin-bottom: 15px;
        }
        .info-box ol {
            margin-left: 20px;
            color: #0c5460;
            line-height: 1.8;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Setup</h1>
            <p>MA.GIA DONNA - Sistema Gestione Pagamenti</p>
        </div>

        <div class="content">
            <div class="warning-box">
                <div class="warning-icon">⚠️</div>
                <div class="warning-content">
                    <h3>ATTENZIONE - Sicurezza</h3>
                    <ul>
                        <li>Questi script modificano il database in modo permanente</li>
                        <li>Esegui gli script in sequenza numerica (001, 002, ...)</li>
                        <li>Ogni script può essere eseguito solo UNA volta</li>
                        <li><strong>CANCELLA questa cartella dopo l'uso!</strong></li>
                    </ul>
                </div>
            </div>

            <h2 style="color: #7b2869; margin-bottom: 20px;">📋 Script Disponibili</h2>

            <div class="script-list">
                <!-- Script 001 -->
                <div class="script-item">
                    <div class="script-number">1</div>
                    <div class="script-info">
                        <div class="script-title">Tabella Pagamenti Professionisti</div>
                        <div class="script-desc">
                            Crea la tabella <code>pagamenti_professionisti</code> per tracciare i pagamenti effettivi
                            ai collaboratori (compensi, ritenute, storico).
                        </div>
                    </div>
                    <a href="001_create_pagamenti_professionisti_table.php" class="script-btn">
                        Esegui →
                    </a>
                </div>

                <!-- Script 002 -->
                <div class="script-item">
                    <div class="script-number">2</div>
                    <div class="script-info">
                        <div class="script-title">Campi Pagamento Clienti</div>
                        <div class="script-desc">
                            Aggiunge campi alla tabella <code>utenti</code> per gestire pagamenti registrazione
                            (PayPal + Bonifico Bancario).
                        </div>
                    </div>
                    <a href="002_add_payment_fields_to_utenti_table.php" class="script-btn">
                        Esegui →
                    </a>
                </div>
            </div>

            <div class="info-box">
                <h3>📝 Istruzioni Passo-Passo</h3>
                <ol>
                    <li><strong>Backup Database:</strong> Fai un backup completo prima di procedere</li>
                    <li><strong>Esegui Script 001:</strong> Crea tabella pagamenti professionisti</li>
                    <li><strong>Esegui Script 002:</strong> Aggiunge campi pagamento alla tabella utenti</li>
                    <li><strong>Verifica:</strong> Controlla che non ci siano errori nei log</li>
                    <li><strong>Configura .env:</strong> Aggiungi credenziali PayPal:
                        <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px; overflow-x: auto;">
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id_here
PAYPAL_CLIENT_SECRET=your_client_secret_here</pre>
                    </li>
                    <li><strong>Test Funzionalità:</strong> Prova registrazione cliente e pagamenti professionisti</li>
                    <li><strong>🔒 CANCELLA CARTELLA:</strong> Rimuovi <code>/database/setup/</code> per sicurezza!</li>
                </ol>
            </div>
        </div>

        <div class="footer">
            <p><strong>MA.GIA DONNA</strong> - Sistema Gestione Database v1.0</p>
            <p style="margin-top: 10px; font-size: 0.9em;">
                ⚙️ Eseguire in ambiente di sviluppo prima della produzione
            </p>
        </div>
    </div>
</body>
</html>
