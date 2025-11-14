<?php
/**
 * Aggiornamento Database - Nuove Funzionalità Avanzate
 * Esegui visitando: https://www.agstudio.digital/magia/update_database.php
 */

$db_path = __DIR__ . '/agstudio.db';
$db = new SQLite3($db_path);
$db->enableExceptions(true);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Update Database - AGstudio CRM</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f4f6f9;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        h1 {
            color: #1a2332;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .step {
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 3px solid #27ae60;
            background: #e8f5e9;
        }
        .success {
            color: #27ae60;
            font-weight: 600;
        }
        .error {
            color: #e74c3c;
            font-weight: 600;
            border-left-color: #e74c3c;
            background: #ffebee;
        }
        .info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 3px solid #3498db;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
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
<h1>🚀 Update Database - Funzionalità Avanzate</h1>";

try {
    // Crea tabella time_entries per time tracking
    echo "<div class='step'><strong>Creazione tabella time_entries (Time Tracking)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS time_entries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            progetto_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            descrizione TEXT,
            ore REAL NOT NULL,
            data DATE NOT NULL,
            ora_inizio TIME,
            ora_fine TIME,
            fatturabile INTEGER DEFAULT 1,
            note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (progetto_id) REFERENCES progetti(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella alert_config per configurazione alert
    echo "<div class='step'><strong>Creazione tabella alert_config (Sistema Alert)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS alert_config (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo TEXT NOT NULL,
            attivo INTEGER DEFAULT 1,
            giorni_anticipo INTEGER DEFAULT 7,
            email_destinatario TEXT,
            ultima_esecuzione DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Inserisci configurazioni alert di default
    $result = $db->query("SELECT COUNT(*) as count FROM alert_config");
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] == 0) {
        echo "<div class='step'><strong>Inserimento configurazioni alert di default...</strong> ";

        $db->exec("
            INSERT INTO alert_config (tipo, attivo, giorni_anticipo, email_destinatario)
            VALUES
                ('scadenze_in_arrivo', 1, 7, 'admin@agstudio.digital'),
                ('scadenze_scadute', 1, 0, 'admin@agstudio.digital'),
                ('progetti_in_ritardo', 1, 0, 'admin@agstudio.digital')
        ");

        echo "<span class='success'>✓ OK</span></div>";
    } else {
        echo "<div class='step'><strong>Configurazioni alert già esistenti</strong> <span class='success'>✓ OK</span></div>";
    }

    // Aggiungi colonne alla tabella users se non esistono
    echo "<div class='step'><strong>Aggiornamento tabella users (permessi multi-utente)...</strong> ";

    // Controlla se la colonna permessi esiste
    $columns = [];
    $result = $db->query("PRAGMA table_info(users)");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[] = $row['name'];
    }

    if (!in_array('permessi', $columns)) {
        $db->exec("ALTER TABLE users ADD COLUMN permessi TEXT DEFAULT 'all'");
        $db->exec("ALTER TABLE users ADD COLUMN reparto TEXT");
        echo "<span class='success'>✓ OK (colonne aggiunte)</span></div>";
    } else {
        echo "<span class='success'>✓ OK (già aggiornata)</span></div>";
    }

    // Crea tabella notifications per notifiche sistema
    echo "<div class='step'><strong>Creazione tabella notifications...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            titolo TEXT NOT NULL,
            messaggio TEXT,
            tipo TEXT DEFAULT 'info',
            letta INTEGER DEFAULT 0,
            url TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella email_queue per invio email
    echo "<div class='step'><strong>Creazione tabella email_queue (Sistema Email)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS email_queue (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            destinatario TEXT NOT NULL,
            oggetto TEXT NOT NULL,
            messaggio TEXT NOT NULL,
            tipo TEXT,
            inviata INTEGER DEFAULT 0,
            tentativi INTEGER DEFAULT 0,
            data_invio DATETIME,
            errore TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella google_credentials per integrazione Google Calendar
    echo "<div class='step'><strong>Creazione tabella google_credentials (Google Calendar)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS google_credentials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id TEXT NOT NULL,
            client_secret TEXT NOT NULL,
            redirect_uri TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Aggiungi colonna google_refresh_token alla tabella users
    echo "<div class='step'><strong>Aggiornamento tabella users (Google OAuth)...</strong> ";
    $result = $db->query("PRAGMA table_info(users)");
    $user_columns = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $user_columns[] = $row['name'];
    }

    if (!in_array('google_refresh_token', $user_columns)) {
        $db->exec("ALTER TABLE users ADD COLUMN google_refresh_token TEXT");
        echo "<span class='success'>✓ OK (colonna aggiunta)</span></div>";
    } else {
        echo "<span class='success'>✓ OK (già aggiornata)</span></div>";
    }

    // Aggiungi colonna google_event_id alla tabella scadenze
    echo "<div class='step'><strong>Aggiornamento tabella scadenze (sync Google)...</strong> ";
    $result = $db->query("PRAGMA table_info(scadenze)");
    $scadenze_columns = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $scadenze_columns[] = $row['name'];
    }

    if (!in_array('google_event_id', $scadenze_columns)) {
        $db->exec("ALTER TABLE scadenze ADD COLUMN google_event_id TEXT");
        echo "<span class='success'>✓ OK (colonna aggiunta)</span></div>";
    } else {
        echo "<span class='success'>✓ OK (già aggiornata)</span></div>";
    }

    // Crea tabella calendar_events per eventi generici (note, appuntamenti)
    echo "<div class='step'><strong>Creazione tabella calendar_events (Eventi Generici)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS calendar_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            tipo_evento TEXT DEFAULT 'nota',
            data_inizio DATETIME NOT NULL,
            data_fine DATETIME,
            tutto_il_giorno INTEGER DEFAULT 1,
            colore TEXT DEFAULT '#9c27b0',
            priorita TEXT DEFAULT 'normale',
            completato INTEGER DEFAULT 0,
            google_event_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella smtp_config per configurazione SMTP
    echo "<div class='step'><strong>Creazione tabella smtp_config (Configurazione Email)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS smtp_config (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            smtp_host TEXT NOT NULL,
            smtp_port INTEGER NOT NULL DEFAULT 587,
            smtp_username TEXT NOT NULL,
            smtp_password TEXT NOT NULL,
            smtp_encryption TEXT DEFAULT 'tls',
            from_email TEXT NOT NULL,
            from_name TEXT NOT NULL,
            enabled INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella email_templates per template email
    echo "<div class='step'><strong>Creazione tabella email_templates (Template Email)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS email_templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL UNIQUE,
            oggetto TEXT NOT NULL,
            corpo_html TEXT NOT NULL,
            descrizione TEXT,
            variabili_disponibili TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Inserisci template email predefinito
    echo "<div class='step'><strong>Inserimento template email predefinito...</strong> ";
    $default_template = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: #3498db;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>{{titolo}}</h1>
        </div>
        <div class='content'>
            {{corpo}}
        </div>
        <div class='footer'>
            <p>AGstudio CRM - Sistema di gestione progetti</p>
            <p>Questa è un'email automatica, si prega di non rispondere.</p>
        </div>
    </div>
</body>
</html>";

    $stmt = $db->prepare("
        INSERT OR IGNORE INTO email_templates (nome, oggetto, corpo_html, descrizione, variabili_disponibili)
        VALUES ('alert_scadenza', 'Promemoria Scadenza: {{progetto}}', :corpo, 'Template per alert scadenze progetti', '{{titolo}}, {{corpo}}, {{progetto}}, {{cliente}}, {{data_scadenza}}')
    ");
    $stmt->bindValue(':corpo', $default_template, SQLITE3_TEXT);
    $stmt->execute();
    echo "<span class='success'>✓ OK</span></div>";

    // ==================== TASK MANAGEMENT ====================

    // Crea tabella tasks
    echo "<div class='step'><strong>Creazione tabella tasks (Task Management)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            progetto_id INTEGER NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            assegnato_a INTEGER,
            creato_da INTEGER NOT NULL,
            priorita TEXT DEFAULT 'media',
            stato TEXT DEFAULT 'da_fare',
            data_inizio DATE,
            data_scadenza DATE,
            data_completamento DATETIME,
            tempo_stimato INTEGER,
            tempo_effettivo INTEGER,
            ordine INTEGER DEFAULT 0,
            tags TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (progetto_id) REFERENCES progetti(id) ON DELETE CASCADE,
            FOREIGN KEY (assegnato_a) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (creato_da) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella task_checklist
    echo "<div class='step'><strong>Creazione tabella task_checklist (Checklist Task)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS task_checklist (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            testo TEXT NOT NULL,
            completato INTEGER DEFAULT 0,
            ordine INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella task_comments
    echo "<div class='step'><strong>Creazione tabella task_comments (Commenti Task)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS task_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            commento TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella task_attachments
    echo "<div class='step'><strong>Creazione tabella task_attachments (Allegati Task)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS task_attachments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            filename TEXT NOT NULL,
            file_path TEXT NOT NULL,
            file_size INTEGER,
            mime_type TEXT,
            uploaded_by INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella task_dependencies (dipendenze tra task)
    echo "<div class='step'><strong>Creazione tabella task_dependencies (Dipendenze Task)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS task_dependencies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER NOT NULL,
            depends_on_task_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY (depends_on_task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // ==================== LEAD MANAGEMENT / PIPELINE ====================

    // Crea tabella leads
    echo "<div class='step'><strong>Creazione tabella leads (Pipeline Vendite)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            azienda TEXT NOT NULL,
            nome_contatto TEXT,
            email TEXT,
            telefono TEXT,
            website TEXT,
            indirizzo TEXT,
            citta TEXT,
            provincia TEXT,
            cap TEXT,
            nazione TEXT DEFAULT 'Italia',
            fonte TEXT,
            settore TEXT,
            note TEXT,
            stato TEXT DEFAULT 'nuovo',
            probabilita INTEGER DEFAULT 50,
            valore_stimato REAL,
            data_chiusura_prevista DATE,
            assegnato_a INTEGER,
            created_by INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            converted_to_cliente_id INTEGER,
            converted_at DATETIME,
            FOREIGN KEY (assegnato_a) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id),
            FOREIGN KEY (converted_to_cliente_id) REFERENCES clienti(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella lead_activities
    echo "<div class='step'><strong>Creazione tabella lead_activities (Storico Interazioni Lead)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_activities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            tipo TEXT NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            esito TEXT,
            user_id INTEGER NOT NULL,
            data_attivita DATETIME DEFAULT CURRENT_TIMESTAMP,
            prossima_azione TEXT,
            data_prossima_azione DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // Crea tabella lead_files
    echo "<div class='step'><strong>Creazione tabella lead_files (Documenti Lead)...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_files (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            filename TEXT NOT NULL,
            file_path TEXT NOT NULL,
            file_size INTEGER,
            mime_type TEXT,
            uploaded_by INTEGER NOT NULL,
            descrizione TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // ==================== CONTRATTI & RINNOVI ====================

    echo "<div class='step'><strong>Creazione tabella contratti...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS contratti (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            tipo TEXT,
            valore REAL,
            data_inizio DATE NOT NULL,
            data_scadenza DATE NOT NULL,
            data_rinnovo DATE,
            rinnovo_automatico INTEGER DEFAULT 0,
            frequenza_rinnovo TEXT,
            giorni_preavviso INTEGER DEFAULT 30,
            stato TEXT DEFAULT 'attivo',
            file_path TEXT,
            note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE CASCADE
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // ==================== NOTE & ATTIVITÀ CLIENTI ====================

    echo "<div class='step'><strong>Creazione tabella cliente_attivita...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS cliente_attivita (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            tipo TEXT NOT NULL,
            titolo TEXT NOT NULL,
            descrizione TEXT,
            esito TEXT,
            user_id INTEGER NOT NULL,
            data_attivita DATETIME DEFAULT CURRENT_TIMESTAMP,
            prossima_azione TEXT,
            data_prossima_azione DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    echo "<div class='step'><strong>Creazione tabella cliente_note...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS cliente_note (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_id INTEGER NOT NULL,
            nota TEXT NOT NULL,
            importante INTEGER DEFAULT 0,
            user_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    // ==================== SPESE PROGETTO ====================

    echo "<div class='step'><strong>Creazione tabella progetto_spese...</strong> ";
    $db->exec("
        CREATE TABLE IF NOT EXISTS progetto_spese (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            progetto_id INTEGER NOT NULL,
            descrizione TEXT NOT NULL,
            categoria TEXT,
            importo REAL NOT NULL,
            data_spesa DATE NOT NULL,
            rimborsabile INTEGER DEFAULT 0,
            rimborsata INTEGER DEFAULT 0,
            ricevuta_path TEXT,
            fornitore TEXT,
            note TEXT,
            user_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (progetto_id) REFERENCES progetti(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");
    echo "<span class='success'>✓ OK</span></div>";

    echo "
    <div class='info'>
        <h2>✅ Database Aggiornato con Successo!</h2>
        <p><strong>Nuove Tabelle Aggiunte:</strong></p>
        <ul>
            <li>✅ <strong>time_entries</strong> - Tracciamento ore lavorate per progetto</li>
            <li>✅ <strong>alert_config</strong> - Configurazione alert automatici</li>
            <li>✅ <strong>notifications</strong> - Notifiche in-app per utenti</li>
            <li>✅ <strong>email_queue</strong> - Coda email per invio automatico</li>
            <li>✅ <strong>smtp_config</strong> - Configurazione SMTP per invio email</li>
            <li>✅ <strong>email_templates</strong> - Template HTML personalizzabili per email (per alert automatici)</li>
            <li>✅ <strong>google_credentials</strong> - Credenziali API Google Calendar</li>
            <li>✅ <strong>calendar_events</strong> - Eventi generici (note, appuntamenti, promemoria)</li>
            <li>✅ <strong>tasks</strong> - Task management con stati, priorità, assegnazioni</li>
            <li>✅ <strong>task_checklist</strong> - Checklist all'interno dei task</li>
            <li>✅ <strong>task_comments</strong> - Commenti e discussioni sui task</li>
            <li>✅ <strong>task_attachments</strong> - Allegati sui task</li>
            <li>✅ <strong>task_dependencies</strong> - Dipendenze tra task</li>
            <li>✅ <strong>leads</strong> - Pipeline vendite e gestione lead/opportunità</li>
            <li>✅ <strong>lead_activities</strong> - Storico interazioni con lead (chiamate, email, riunioni)</li>
            <li>✅ <strong>lead_files</strong> - Documenti e file allegati ai lead</li>
            <li>✅ <strong>contratti</strong> - Gestione contratti con scadenze e rinnovi automatici</li>
            <li>✅ <strong>cliente_attivita</strong> - Log attività sui clienti (chiamate, meeting, email)</li>
            <li>✅ <strong>cliente_note</strong> - Note testuali sui clienti</li>
            <li>✅ <strong>progetto_spese</strong> - Spese di progetto con categorie e rimborsi</li>
            <li>✅ <strong>users</strong> - Aggiornata con permessi, reparto e google_refresh_token</li>
            <li>✅ <strong>scadenze</strong> - Aggiornata con google_event_id per sincronizzazione</li>
        </ul>

        <p><strong>Nuove Funzionalità Disponibili:</strong></p>
        <ul>
            <li>✅ <strong>Task Management</strong> - Gestione task con Kanban, priorità, assegnazioni</li>
            <li>🎯 <strong>Pipeline Vendite</strong> - Gestione lead, opportunità e conversion tracking</li>
            <li>⏱️ <strong>Time Tracking</strong> - Traccia ore lavorate sui progetti</li>
            <li>🔔 <strong>Alert Email</strong> - Notifiche automatiche per scadenze</li>
            <li>👥 <strong>Multi-Utente</strong> - Ruoli e permessi (admin, manager, user)</li>
            <li>📧 <strong>Email System</strong> - Invio email automatico</li>
            <li>📅 <strong>Notifications</strong> - Notifiche in-app</li>
            <li>📆 <strong>Google Calendar</strong> - Sincronizzazione bidirezionale con Google Calendar</li>
            <li>🗓️ <strong>FullCalendar</strong> - Calendario interattivo stile Google (drag & drop)</li>
            <li>📝 <strong>Eventi Generici</strong> - Note, appuntamenti e promemoria nel calendario</li>
        </ul>

        <a href='dashboard.php' class='btn'>🏠 Vai alla Dashboard</a>
        <a href='calendar.php' class='btn' style='background: #27ae60; margin-left: 10px;'>📆 Vai al Calendario</a>
    </div>
    ";

} catch (Exception $e) {
    echo "<div class='step error'><strong>ERRORE:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";

$db->close();
?>
