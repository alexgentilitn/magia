#!/usr/bin/env php
<?php
/**
 * Script per verifica automatica alert
 *
 * Questo script deve essere eseguito tramite cron ogni ora:
 * 0 * * * * /usr/bin/php /path/to/check_alerts.php
 */

require_once __DIR__ . '/config.php';

// Log inizio esecuzione
$log_file = __DIR__ . '/logs/alerts.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function logAlert($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

logAlert("===== INIZIO VERIFICA ALERT =====");

// Carica configurazioni attive
$configs = [];
$result = $db->query("SELECT * FROM alert_config WHERE attivo = 1");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $configs[$row['tipo']] = $row;
}

logAlert("Configurazioni attive: " . count($configs));

/**
 * Funzione per aggiungere email alla coda
 */
function queueEmail($destinatario, $oggetto, $messaggio, $tipo) {
    global $db;

    $stmt = $db->prepare("
        INSERT INTO email_queue (destinatario, oggetto, messaggio, tipo, inviata, tentativi)
        VALUES (:destinatario, :oggetto, :messaggio, :tipo, 0, 0)
    ");
    $stmt->bindValue(':destinatario', $destinatario, SQLITE3_TEXT);
    $stmt->bindValue(':oggetto', $oggetto, SQLITE3_TEXT);
    $stmt->bindValue(':messaggio', $messaggio, SQLITE3_TEXT);
    $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
    $stmt->execute();

    return $db->lastInsertRowID();
}

/**
 * Funzione per creare notifica in-app
 */
function createNotification($user_id, $titolo, $messaggio, $tipo, $url = null) {
    global $db;

    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, titolo, messaggio, tipo, letta, url)
        VALUES (:user_id, :titolo, :messaggio, :tipo, 0, :url)
    ");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
    $stmt->bindValue(':messaggio', $messaggio, SQLITE3_TEXT);
    $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
    $stmt->bindValue(':url', $url, SQLITE3_TEXT);
    $stmt->execute();
}

// ============================================
// 1. CHECK SCADENZE IMMINENTI
// ============================================
if (isset($configs['scadenza_imminente'])) {
    $config = $configs['scadenza_imminente'];
    $giorni = $config['giorni_anticipo'];
    $data_limite = date('Y-m-d', strtotime("+$giorni days"));

    logAlert("Verifico scadenze imminenti (prossimi $giorni giorni)...");

    $stmt = $db->prepare("
        SELECT s.*, p.titolo as progetto_titolo, c.ragione_sociale
        FROM scadenze s
        LEFT JOIN progetti p ON s.progetto_id = p.id
        LEFT JOIN clienti c ON p.cliente_id = c.id
        WHERE s.stato = 'in_attesa'
        AND DATE(s.data_scadenza) <= :data_limite
        AND DATE(s.data_scadenza) >= DATE('now')
    ");
    $stmt->bindValue(':data_limite', $data_limite, SQLITE3_TEXT);
    $result = $stmt->execute();

    $count = 0;
    while ($scadenza = $result->fetchArray(SQLITE3_ASSOC)) {
        $count++;

        // Crea email
        $oggetto = "⚠️ Scadenza Imminente: " . $scadenza['titolo'];
        $messaggio = "La scadenza \"{$scadenza['titolo']}\" è prevista per il " .
                     formatDateTime($scadenza['data_scadenza'], 'd/m/Y') . ".\n\n" .
                     "Progetto: {$scadenza['progetto_titolo']}\n" .
                     "Cliente: {$scadenza['ragione_sociale']}\n" .
                     "Importo: " . formatCurrency($scadenza['importo']) . "\n\n" .
                     "Accedi al CRM per gestire la scadenza.";

        queueEmail($config['email_destinatario'], $oggetto, $messaggio, 'scadenza_imminente');

        // Crea notifica per tutti gli admin
        $users = $db->query("SELECT id FROM users WHERE ruolo = 'admin' AND attivo = 1");
        while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
            createNotification(
                $user['id'],
                $oggetto,
                $scadenza['titolo'] . " - " . formatDateTime($scadenza['data_scadenza'], 'd/m/Y'),
                'warning',
                'scadenze.php?id=' . $scadenza['id']
            );
        }
    }

    logAlert("Trovate $count scadenze imminenti");

    // Aggiorna ultima esecuzione
    $db->exec("UPDATE alert_config SET ultima_esecuzione = CURRENT_TIMESTAMP WHERE tipo = 'scadenza_imminente'");
}

// ============================================
// 2. CHECK SCADENZE SCADUTE
// ============================================
if (isset($configs['scadenza_scaduta'])) {
    $config = $configs['scadenza_scaduta'];

    logAlert("Verifico scadenze scadute...");

    $result = $db->query("
        SELECT s.*, p.titolo as progetto_titolo, c.ragione_sociale
        FROM scadenze s
        LEFT JOIN progetti p ON s.progetto_id = p.id
        LEFT JOIN clienti c ON p.cliente_id = c.id
        WHERE s.stato = 'in_attesa'
        AND DATE(s.data_scadenza) < DATE('now')
    ");

    $count = 0;
    while ($scadenza = $result->fetchArray(SQLITE3_ASSOC)) {
        $count++;

        $giorni_ritardo = (strtotime('now') - strtotime($scadenza['data_scadenza'])) / 86400;
        $giorni_ritardo = floor($giorni_ritardo);

        // Crea email
        $oggetto = "🔴 Scadenza Superata: " . $scadenza['titolo'];
        $messaggio = "La scadenza \"{$scadenza['titolo']}\" è scaduta da $giorni_ritardo giorni.\n\n" .
                     "Data scadenza: " . formatDateTime($scadenza['data_scadenza'], 'd/m/Y') . "\n" .
                     "Progetto: {$scadenza['progetto_titolo']}\n" .
                     "Cliente: {$scadenza['ragione_sociale']}\n" .
                     "Importo: " . formatCurrency($scadenza['importo']) . "\n\n" .
                     "AZIONE RICHIESTA: Gestire urgentemente la scadenza.";

        queueEmail($config['email_destinatario'], $oggetto, $messaggio, 'scadenza_scaduta');

        // Crea notifica per tutti gli admin
        $users = $db->query("SELECT id FROM users WHERE ruolo = 'admin' AND attivo = 1");
        while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
            createNotification(
                $user['id'],
                $oggetto,
                "Scaduta da $giorni_ritardo giorni - " . $scadenza['titolo'],
                'danger',
                'scadenze.php?id=' . $scadenza['id']
            );
        }
    }

    logAlert("Trovate $count scadenze scadute");

    // Aggiorna ultima esecuzione
    $db->exec("UPDATE alert_config SET ultima_esecuzione = CURRENT_TIMESTAMP WHERE tipo = 'scadenza_scaduta'");
}

// ============================================
// 3. CHECK PROGETTI IN RITARDO
// ============================================
if (isset($configs['progetto_ritardo'])) {
    $config = $configs['progetto_ritardo'];

    logAlert("Verifico progetti in ritardo...");

    $result = $db->query("
        SELECT p.*, c.ragione_sociale
        FROM progetti p
        LEFT JOIN clienti c ON p.cliente_id = c.id
        WHERE p.stato = 'in_corso'
        AND p.data_consegna IS NOT NULL
        AND DATE(p.data_consegna) < DATE('now')
    ");

    $count = 0;
    while ($progetto = $result->fetchArray(SQLITE3_ASSOC)) {
        $count++;

        $giorni_ritardo = (strtotime('now') - strtotime($progetto['data_consegna'])) / 86400;
        $giorni_ritardo = floor($giorni_ritardo);

        // Crea email
        $oggetto = "⏰ Progetto in Ritardo: " . $progetto['titolo'];
        $messaggio = "Il progetto \"{$progetto['titolo']}\" è in ritardo di $giorni_ritardo giorni.\n\n" .
                     "Data consegna prevista: " . formatDateTime($progetto['data_consegna'], 'd/m/Y') . "\n" .
                     "Cliente: {$progetto['ragione_sociale']}\n" .
                     "Budget: " . formatCurrency($progetto['budget']) . "\n\n" .
                     "Valutare aggiornamento stato o ripianificazione.";

        queueEmail($config['email_destinatario'], $oggetto, $messaggio, 'progetto_ritardo');

        // Crea notifica per tutti i manager e admin
        $users = $db->query("SELECT id FROM users WHERE ruolo IN ('admin', 'manager') AND attivo = 1");
        while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
            createNotification(
                $user['id'],
                $oggetto,
                "Ritardo: $giorni_ritardo giorni - " . $progetto['titolo'],
                'warning',
                'progetti.php?id=' . $progetto['id']
            );
        }
    }

    logAlert("Trovati $count progetti in ritardo");

    // Aggiorna ultima esecuzione
    $db->exec("UPDATE alert_config SET ultima_esecuzione = CURRENT_TIMESTAMP WHERE tipo = 'progetto_ritardo'");
}

// ============================================
// INVIO EMAIL DALLA CODA
// ============================================
logAlert("Invio email dalla coda...");

$result = $db->query("
    SELECT * FROM email_queue
    WHERE inviata = 0
    AND tentativi < 3
    ORDER BY id ASC
    LIMIT 10
");

$email_count = 0;
while ($email = $result->fetchArray(SQLITE3_ASSOC)) {
    $email_count++;

    // Prepara headers
    $headers = "From: AGstudio CRM <noreply@agstudio.digital>\r\n";
    $headers .= "Reply-To: noreply@agstudio.digital\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Tenta invio
    $sent = @mail($email['destinatario'], $email['oggetto'], $email['messaggio'], $headers);

    if ($sent) {
        // Segna come inviata
        $stmt = $db->prepare("
            UPDATE email_queue SET
                inviata = 1,
                data_invio = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $email['id'], SQLITE3_INTEGER);
        $stmt->execute();

        logAlert("Email inviata a {$email['destinatario']}: {$email['oggetto']}");
    } else {
        // Incrementa tentativi
        $stmt = $db->prepare("
            UPDATE email_queue SET
                tentativi = tentativi + 1,
                errore = :errore
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $email['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':errore', 'Errore invio email', SQLITE3_TEXT);
        $stmt->execute();

        logAlert("Errore invio email a {$email['destinatario']}: {$email['oggetto']}");
    }
}

logAlert("Elaborate $email_count email dalla coda");

logAlert("===== FINE VERIFICA ALERT =====\n");

// Chiudi connessione database
$db->close();
?>
