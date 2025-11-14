<?php
/**
 * Helper per invio email con SMTP e template
 */

// Questa funzione richiede PHPMailer
// Installa con: composer require phpmailer/phpmailer
// Oppure scarica da: https://github.com/PHPMailer/PHPMailer

/**
 * Invia email usando configurazione SMTP e template
 *
 * @param string $destinatario Email destinatario
 * @param string $template_name Nome del template da usare
 * @param array $variabili Array associativo con variabili da sostituire
 * @param string|null $oggetto_custom Oggetto personalizzato (opzionale, altrimenti usa quello del template)
 * @return bool True se inviata, False altrimenti
 */
function sendEmailWithTemplate($destinatario, $template_name, $variabili = [], $oggetto_custom = null) {
    global $db;

    // Carica configurazione SMTP
    $result = $db->query("SELECT * FROM smtp_config WHERE enabled = 1 ORDER BY id DESC LIMIT 1");
    $smtp = $result->fetchArray(SQLITE3_ASSOC);

    if (!$smtp) {
        error_log("SMTP non configurato");
        return false;
    }

    // Carica template
    $stmt = $db->prepare("SELECT * FROM email_templates WHERE nome = :nome");
    $stmt->bindValue(':nome', $template_name, SQLITE3_TEXT);
    $result = $stmt->execute();
    $template = $result->fetchArray(SQLITE3_ASSOC);

    if (!$template) {
        error_log("Template '$template_name' non trovato");
        return false;
    }

    // Verifica PHPMailer
    if (!file_exists(__DIR__ . '/PHPMailer/PHPMailer.php')) {
        error_log("PHPMailer non installato. Installare con: composer require phpmailer/phpmailer");
        // Fallback: usa email_queue per invio manuale
        queueEmail($destinatario, $oggetto_custom ?? $template['oggetto'], $template['corpo_html'], $template_name);
        return false;
    }

    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    require_once __DIR__ . '/PHPMailer/Exception.php';

    try {
        // Sostituisci variabili nel template
        $oggetto = $oggetto_custom ?? $template['oggetto'];
        $corpo_html = $template['corpo_html'];

        foreach ($variabili as $key => $value) {
            $oggetto = str_replace("{{{$key}}}", $value, $oggetto);
            $corpo_html = str_replace("{{{$key}}}", $value, $corpo_html);
        }

        // Configura PHPMailer
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['smtp_username'];
        $mail->Password = $smtp['smtp_password'];
        $mail->SMTPSecure = $smtp['smtp_encryption'];
        $mail->Port = $smtp['smtp_port'];
        $mail->CharSet = 'UTF-8';

        // ANTI-SPAM: Headers migliorati
        $mail->XMailer = ' '; // Nasconde "PHPMailer" per evitare rilevamento spam

        // Mittente e destinatario
        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress($destinatario);
        $mail->addReplyTo($smtp['from_email'], $smtp['from_name']); // Reply-To esplicito

        // Headers aggiuntivi per autenticazione
        $mail->addCustomHeader('X-Priority', '3'); // Normal priority
        $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
        $mail->addCustomHeader('Importance', 'Normal');

        // DKIM: Se hai configurato DKIM, decommenta e configura:
        // $mail->DKIM_domain = 'tuodominio.com';
        // $mail->DKIM_private = '/path/to/private.key';
        // $mail->DKIM_selector = 'default';
        // $mail->DKIM_passphrase = '';
        // $mail->DKIM_identity = $mail->From;

        // Contenuto
        $mail->isHTML(true);
        $mail->Subject = $oggetto;
        $mail->Body = $corpo_html;

        // Testo alternativo per client non-HTML
        $mail->AltBody = strip_tags($corpo_html);

        // Invia
        $mail->send();

        // Log successo
        logActivity('email_sent', 'email', null, "Email inviata a: $destinatario (template: $template_name)");

        return true;

    } catch (Exception $e) {
        error_log("Errore invio email: {$mail->ErrorInfo}");
        logActivity('email_failed', 'email', null, "Errore email a: $destinatario - {$mail->ErrorInfo}");

        // Fallback: aggiungi a coda per retry manuale
        queueEmail($destinatario, $oggetto, $corpo_html, $template_name);

        return false;
    }
}

/**
 * Invia email semplice (senza template)
 */
function sendSimpleEmail($destinatario, $oggetto, $corpo_html) {
    global $db;

    // Carica configurazione SMTP
    $result = $db->query("SELECT * FROM smtp_config WHERE enabled = 1 ORDER BY id DESC LIMIT 1");
    $smtp = $result->fetchArray(SQLITE3_ASSOC);

    if (!$smtp) {
        error_log("SMTP non configurato");
        queueEmail($destinatario, $oggetto, $corpo_html, 'simple');
        return false;
    }

    // Verifica PHPMailer
    if (!file_exists(__DIR__ . '/PHPMailer/PHPMailer.php')) {
        error_log("PHPMailer non installato");
        queueEmail($destinatario, $oggetto, $corpo_html, 'simple');
        return false;
    }

    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    require_once __DIR__ . '/PHPMailer/Exception.php';

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['smtp_username'];
        $mail->Password = $smtp['smtp_password'];
        $mail->SMTPSecure = $smtp['smtp_encryption'];
        $mail->Port = $smtp['smtp_port'];
        $mail->CharSet = 'UTF-8';

        // ANTI-SPAM: Headers migliorati
        $mail->XMailer = ' '; // Nasconde "PHPMailer" per evitare rilevamento spam
        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress($destinatario);
        $mail->addReplyTo($smtp['from_email'], $smtp['from_name']); // Reply-To esplicito

        // Headers aggiuntivi per autenticazione
        $mail->addCustomHeader('X-Priority', '3'); // Normal priority
        $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
        $mail->addCustomHeader('Importance', 'Normal');

        // DKIM: Se hai configurato DKIM, decommenta e configura:
        // $mail->DKIM_domain = 'tuodominio.com';
        // $mail->DKIM_private = '/path/to/private.key';
        // $mail->DKIM_selector = 'default';
        // $mail->DKIM_passphrase = '';
        // $mail->DKIM_identity = $mail->From;

        $mail->isHTML(true);
        $mail->Subject = $oggetto;
        $mail->Body = $corpo_html;

        // Testo alternativo per client non-HTML
        $mail->AltBody = strip_tags($corpo_html);

        $mail->send();

        logActivity('email_sent', 'email', null, "Email semplice inviata a: $destinatario");
        return true;

    } catch (Exception $e) {
        error_log("Errore invio email: {$mail->ErrorInfo}");
        queueEmail($destinatario, $oggetto, $corpo_html, 'simple');
        return false;
    }
}

/**
 * Aggiunge email alla coda per invio successivo
 */
function queueEmail($destinatario, $oggetto, $messaggio, $tipo) {
    global $db;

    $stmt = $db->prepare("
        INSERT INTO email_queue (destinatario, oggetto, messaggio, tipo, inviata, tentativi, created_at)
        VALUES (:destinatario, :oggetto, :messaggio, :tipo, 0, 0, CURRENT_TIMESTAMP)
    ");
    $stmt->bindValue(':destinatario', $destinatario, SQLITE3_TEXT);
    $stmt->bindValue(':oggetto', $oggetto, SQLITE3_TEXT);
    $stmt->bindValue(':messaggio', $messaggio, SQLITE3_TEXT);
    $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
    $stmt->execute();

    return $db->lastInsertRowID();
}

/**
 * Processa email in coda (da eseguire periodicamente)
 */
function processEmailQueue($limit = 10) {
    global $db;

    $stmt = $db->prepare("
        SELECT * FROM email_queue
        WHERE inviata = 0 AND tentativi < 3
        ORDER BY created_at ASC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $sent = 0;
    $failed = 0;

    while ($email = $result->fetchArray(SQLITE3_ASSOC)) {
        if (sendSimpleEmail($email['destinatario'], $email['oggetto'], $email['messaggio'])) {
            // Marca come inviata
            $stmt = $db->prepare("UPDATE email_queue SET inviata = 1, data_invio = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->bindValue(':id', $email['id'], SQLITE3_INTEGER);
            $stmt->execute();
            $sent++;
        } else {
            // Incrementa tentativi
            $stmt = $db->prepare("UPDATE email_queue SET tentativi = tentativi + 1 WHERE id = :id");
            $stmt->bindValue(':id', $email['id'], SQLITE3_INTEGER);
            $stmt->execute();
            $failed++;
        }
    }

    return ['sent' => $sent, 'failed' => $failed];
}
?>
