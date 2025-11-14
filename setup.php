<?php
/**
 * Script di Setup Iniziale - AGstudio CRM
 *
 * Esegui questo file visitando: https://www.agstudio.digital/magia/setup.php
 * Questo creerà il database e l'utente admin
 */

$db_path = __DIR__ . '/agstudio.db';
$db = new SQLite3($db_path);
$db->enableExceptions(true);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Setup AGstudio CRM</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .step {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            background: #e8f8f0;
        }
        .success {
            color: #27ae60;
            font-weight: 600;
        }
        .error {
            color: #e74c3c;
            font-weight: 600;
            border-left-color: #e74c3c;
            background: #fee;
        }
        .info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class='container'>
<h1>🚀 Setup AGstudio CRM</h1>";

try {
    // Crea tabella users
    echo "<div class='step'><strong>Creazione tabella users...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT,
            nome TEXT,
            cognome TEXT,
            ruolo TEXT DEFAULT 'utente',
            attivo INTEGER DEFAULT 1,
            ultimo_accesso DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella clienti
    echo "<div class='step'><strong>Creazione tabella clienti...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS clienti (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ragione_sociale TEXT NOT NULL,
            nome_referente TEXT,
            cognome_referente TEXT,
            email TEXT,
            telefono TEXT,
            cellulare TEXT,
            pec TEXT,
            partita_iva TEXT,
            codice_fiscale TEXT,
            indirizzo TEXT,
            citta TEXT,
            cap TEXT,
            provincia TEXT,
            sito_web TEXT,
            note TEXT,
            stato TEXT DEFAULT 'attivo',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella progetti
    echo "<div class='step'><strong>Creazione tabella progetti...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS progetti (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            tipo_progetto TEXT,
            stato TEXT DEFAULT 'pianificazione',
            priorita TEXT DEFAULT 'media',
            importo REAL,
            data_inizio DATE,
            data_consegna DATE,
            data_completamento DATE,
            percentuale_completamento INTEGER DEFAULT 0,
            ore_stimate REAL,
            ore_effettive REAL,
            note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clienti(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella scadenze
    echo "<div class='step'><strong>Creazione tabella scadenze...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS scadenze (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            progetto_id INTEGER NOT NULL,
            descrizione TEXT NOT NULL,
            tipo TEXT DEFAULT 'pagamento',
            data_scadenza DATE NOT NULL,
            importo REAL,
            stato TEXT DEFAULT 'in_attesa',
            data_pagamento DATE,
            note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (progetto_id) REFERENCES progetti(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella documenti
    echo "<div class='step'><strong>Creazione tabella documenti...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS documenti (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            progetto_id INTEGER,
            cliente_id INTEGER,
            titolo TEXT NOT NULL,
            tipo TEXT,
            file_path TEXT NOT NULL,
            file_size INTEGER,
            mime_type TEXT,
            note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (progetto_id) REFERENCES progetti(id),
            FOREIGN KEY (cliente_id) REFERENCES clienti(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella campi_personalizzati
    echo "<div class='step'><strong>Creazione tabella campi_personalizzati...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS campi_personalizzati (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            entita_tipo TEXT NOT NULL,
            nome_campo TEXT NOT NULL,
            tipo_campo TEXT DEFAULT 'text',
            obbligatorio INTEGER DEFAULT 0,
            ordinamento INTEGER DEFAULT 0,
            attivo INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella clienti_campi_custom
    echo "<div class='step'><strong>Creazione tabella clienti_campi_custom...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS clienti_campi_custom (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            campo_id INTEGER NOT NULL,
            valore TEXT,
            FOREIGN KEY (cliente_id) REFERENCES clienti(id),
            FOREIGN KEY (campo_id) REFERENCES campi_personalizzati(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella alert
    echo "<div class='step'><strong>Creazione tabella alert...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS alert (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo TEXT NOT NULL,
            titolo TEXT NOT NULL,
            messaggio TEXT,
            entita_tipo TEXT,
            entita_id INTEGER,
            letto INTEGER DEFAULT 0,
            data_alert DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella activity_log
    echo "<div class='step'><strong>Creazione tabella activity_log...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS activity_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            azione TEXT NOT NULL,
            entita_tipo TEXT,
            entita_id INTEGER,
            dettagli TEXT,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Verifica se esiste già l'utente admin
    $result = $db->query("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] == 0) {
        // Crea utente admin
        echo "<div class='step'><strong>Creazione utente admin...</strong> ";
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("
            INSERT INTO users (username, password, email, nome, cognome, ruolo, attivo)
            VALUES (:username, :password, :email, :nome, :cognome, :ruolo, 1)
        ");
        $stmt->bindValue(':username', 'admin', SQLITE3_TEXT);
        $stmt->bindValue(':password', $password_hash, SQLITE3_TEXT);
        $stmt->bindValue(':email', 'admin@agstudio.digital', SQLITE3_TEXT);
        $stmt->bindValue(':nome', 'Admin', SQLITE3_TEXT);
        $stmt->bindValue(':cognome', 'AGstudio', SQLITE3_TEXT);
        $stmt->bindValue(':ruolo', 'admin', SQLITE3_TEXT);
        $stmt->execute();
        echo "<span class='success'>✓ OK</span></div>";
    } else {
        echo "<div class='step'><strong>Utente admin già esistente</strong> <span class='success'>✓ OK</span></div>";
    }

    // Inserisci dati demo (solo se non esistono)
    $result = $db->query("SELECT COUNT(*) as count FROM clienti");
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] == 0) {
        echo "<div class='step'><strong>Inserimento dati demo...</strong> ";

        // Cliente 1
        $db->exec("
            INSERT INTO clienti (ragione_sociale, nome_referente, cognome_referente, email, telefono, citta, stato)
            VALUES ('Rossi SRL', 'Mario', 'Rossi', 'info@rossisrl.it', '+39 02 1234567', 'Milano', 'attivo')
        ");

        // Cliente 2
        $db->exec("
            INSERT INTO clienti (ragione_sociale, nome_referente, cognome_referente, email, telefono, citta, stato)
            VALUES ('Verdi SpA', 'Giuseppe', 'Verdi', 'contatti@verdispa.it', '+39 06 7654321', 'Roma', 'attivo')
        ");

        // Progetto 1
        $db->exec("
            INSERT INTO progetti (cliente_id, titolo, descrizione, tipo_progetto, stato, priorita, importo, data_inizio, data_consegna, percentuale_completamento)
            VALUES (1, 'Nuovo Sito Web Aziendale', 'Sviluppo sito web responsive con CMS', 'sito_web', 'in_corso', 'alta', 5000.00, '2025-10-01', '2025-11-30', 45)
        ");

        // Progetto 2
        $db->exec("
            INSERT INTO progetti (cliente_id, titolo, descrizione, tipo_progetto, stato, priorita, importo, data_inizio, data_consegna, percentuale_completamento)
            VALUES (2, 'E-commerce Prodotti', 'Piattaforma e-commerce completa', 'ecommerce', 'pianificazione', 'media', 12000.00, '2025-11-15', '2026-02-28', 10)
        ");

        // Scadenza 1
        $db->exec("
            INSERT INTO scadenze (progetto_id, descrizione, tipo, data_scadenza, importo, stato)
            VALUES (1, 'Acconto 30% - Rossi SRL', 'pagamento', '2025-10-15', 1500.00, 'pagato')
        ");

        // Scadenza 2
        $db->exec("
            INSERT INTO scadenze (progetto_id, descrizione, tipo, data_scadenza, importo, stato)
            VALUES (1, 'Saldo finale - Rossi SRL', 'pagamento', '2025-12-05', 3500.00, 'in_attesa')
        ");

        echo "<span class='success'>✓ OK</span></div>";
    }

    echo "
    <div class='info'>
        <h2>✅ Setup Completato con Successo!</h2>
        <p><strong>Database:</strong> agstudio.db creato correttamente</p>
        <p><strong>Utente Admin creato:</strong></p>
        <ul>
            <li><strong>Username:</strong> admin</li>
            <li><strong>Password:</strong> admin123</li>
            <li><strong>Email:</strong> admin@agstudio.digital</li>
        </ul>
        <p>Dati demo inseriti: 2 clienti, 2 progetti, 2 scadenze</p>
        <a href='login.php' class='btn'>🔐 Vai al Login</a>
    </div>
    ";

} catch (Exception $e) {
    echo "<div class='step error'><strong>ERRORE:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";

$db->close();
?>
