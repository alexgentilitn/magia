<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Configurazione Email SMTP';

try {
    require_once 'config.php';
} catch (Throwable $e) {
    die("ERRORE config.php: " . $e->getMessage());
}

try {
    requireLogin();
} catch (Throwable $e) {
    die("ERRORE requireLogin: " . $e->getMessage());
}

try {
    $currentUser = getCurrentUser();
} catch (Throwable $e) {
    die("ERRORE getCurrentUser: " . $e->getMessage());
}

// Solo admin può accedere
if (!$currentUser || $currentUser['ruolo'] !== 'admin') {
    $_SESSION['error_message'] = 'Accesso negato. Solo gli amministratori possono configurare le email.';
    header('Location: dashboard.php');
    exit;
}

$message = '';
$error = '';

// Test email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $test_email = trim($_POST['test_email_address']);

    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Indirizzo email non valido';
    } else {
        // Carica configurazione SMTP
        $result = $db->query("SELECT * FROM smtp_config WHERE enabled = 1 ORDER BY id DESC LIMIT 1");
        $smtp = $result->fetchArray(SQLITE3_ASSOC);

        if (!$smtp) {
            $error = 'Nessuna configurazione SMTP trovata. Salva prima le impostazioni.';
        } else {
            // Invia email di test
            require_once 'PHPMailer/PHPMailer.php';
            require_once 'PHPMailer/SMTP.php';
            require_once 'PHPMailer/Exception.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Configurazione SMTP
                $mail->isSMTP();
                $mail->Host = $smtp['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtp['smtp_username'];
                $mail->Password = $smtp['smtp_password'];
                $mail->SMTPSecure = $smtp['smtp_encryption'];
                $mail->Port = $smtp['smtp_port'];
                $mail->CharSet = 'UTF-8';

                // Mittente e destinatario
                $mail->setFrom($smtp['from_email'], $smtp['from_name']);
                $mail->addAddress($test_email);

                // Contenuto
                $mail->isHTML(true);
                $mail->Subject = 'Test Configurazione SMTP - AGstudio CRM';
                $mail->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
                            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                            .header { background: #27ae60; color: white; padding: 30px 20px; text-align: center; }
                            .content { padding: 30px; }
                            .success-icon { font-size: 48px; margin-bottom: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <div class='success-icon'>✓</div>
                                <h1>Test Email Riuscito!</h1>
                            </div>
                            <div class='content'>
                                <p><strong>Congratulazioni!</strong></p>
                                <p>La configurazione SMTP di AGstudio CRM è stata testata con successo.</p>
                                <p>Questa email conferma che il server può inviare email correttamente usando le impostazioni configurate.</p>
                                <hr style='margin: 20px 0; border: none; border-top: 1px solid #e9ecef;'>
                                <p style='font-size: 12px; color: #6c757d;'>
                                    <strong>Dettagli Configurazione:</strong><br>
                                    Server SMTP: {$smtp['smtp_host']}<br>
                                    Porta: {$smtp['smtp_port']}<br>
                                    Crittografia: {$smtp['smtp_encryption']}<br>
                                    Da: {$smtp['from_name']} &lt;{$smtp['from_email']}&gt;
                                </p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                $mail->send();
                $message = "Email di test inviata con successo a: $test_email. Controlla la casella di posta (anche spam).";

                logActivity('test_email_sent', 'smtp_config', null, "Email di test inviata a: $test_email");

            } catch (Exception $e) {
                $error = "Errore invio email: {$mail->ErrorInfo}";
                logActivity('test_email_failed', 'smtp_config', null, "Errore test email: {$mail->ErrorInfo}");
            }
        }
    }
}

// Salva configurazione SMTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_smtp'])) {
    $smtp_host = trim($_POST['smtp_host']);
    $smtp_port = (int)$_POST['smtp_port'];
    $smtp_username = trim($_POST['smtp_username']);
    $smtp_password = trim($_POST['smtp_password']);
    $smtp_encryption = $_POST['smtp_encryption'];
    $from_email = trim($_POST['from_email']);
    $from_name = trim($_POST['from_name']);
    $enabled = isset($_POST['enabled']) ? 1 : 0;

    // Validazione
    if (empty($smtp_host) || empty($smtp_username) || empty($smtp_password) || empty($from_email) || empty($from_name)) {
        $error = 'Tutti i campi sono obbligatori';
    } elseif (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Indirizzo email mittente non valido';
    } else {
        // Verifica se esiste già configurazione
        $result = $db->query("SELECT COUNT(*) as count FROM smtp_config");
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row['count'] > 0) {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE smtp_config SET
                    smtp_host = :smtp_host,
                    smtp_port = :smtp_port,
                    smtp_username = :smtp_username,
                    smtp_password = :smtp_password,
                    smtp_encryption = :smtp_encryption,
                    from_email = :from_email,
                    from_name = :from_name,
                    enabled = :enabled,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = (SELECT id FROM smtp_config ORDER BY id DESC LIMIT 1)
            ");
        } else {
            // INSERT
            $stmt = $db->prepare("
                INSERT INTO smtp_config (smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, from_email, from_name, enabled)
                VALUES (:smtp_host, :smtp_port, :smtp_username, :smtp_password, :smtp_encryption, :from_email, :from_name, :enabled)
            ");
        }

        $stmt->bindValue(':smtp_host', $smtp_host, SQLITE3_TEXT);
        $stmt->bindValue(':smtp_port', $smtp_port, SQLITE3_INTEGER);
        $stmt->bindValue(':smtp_username', $smtp_username, SQLITE3_TEXT);
        $stmt->bindValue(':smtp_password', $smtp_password, SQLITE3_TEXT);
        $stmt->bindValue(':smtp_encryption', $smtp_encryption, SQLITE3_TEXT);
        $stmt->bindValue(':from_email', $from_email, SQLITE3_TEXT);
        $stmt->bindValue(':from_name', $from_name, SQLITE3_TEXT);
        $stmt->bindValue(':enabled', $enabled, SQLITE3_INTEGER);
        $stmt->execute();

        $message = 'Configurazione SMTP salvata con successo!';
        logActivity('update_smtp_config', 'smtp_config', null, 'Configurazione SMTP aggiornata');
    }
}

// Carica configurazione corrente
$smtp_config = null;
$result = $db->query("SELECT * FROM smtp_config ORDER BY id DESC LIMIT 1");
$smtp_config = $result->fetchArray(SQLITE3_ASSOC);

include 'includes_header.php';
?>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--gray-700);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--gray-300);
    border-radius: 8px;
    font-size: 15px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--accent);
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--gray-600);
    font-size: 13px;
}

.test-email-section {
    background: var(--gray-50);
    padding: 25px;
    border-radius: 8px;
    margin-top: 20px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo h($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        <strong>✗</strong> <?php echo h($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-envelope"></i> Configurazione SMTP</h3>
    </div>

    <div class="info-box">
        <h4 style="margin-bottom: 10px; color: #1976d2;">
            <i class="fas fa-info-circle"></i> Informazioni SMTP
        </h4>
        <p style="margin: 0; color: #1565c0;">
            Configura i parametri del server SMTP per l'invio di email automatiche (alert scadenze, notifiche, etc.).
            Puoi utilizzare Gmail, Outlook, o qualsiasi provider SMTP.
        </p>
    </div>

    <form method="POST">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Server SMTP *</label>
                <input type="text" name="smtp_host" value="<?php echo h($smtp_config['smtp_host'] ?? ''); ?>" required
                       placeholder="smtp.gmail.com">
                <small>Es: smtp.gmail.com, smtp.office365.com</small>
            </div>

            <div class="form-group">
                <label>Porta *</label>
                <input type="number" name="smtp_port" value="<?php echo h($smtp_config['smtp_port'] ?? '587'); ?>" required>
                <small>587 (TLS) o 465 (SSL)</small>
            </div>
        </div>

        <div class="form-group">
            <label>Username/Email SMTP *</label>
            <input type="text" name="smtp_username" value="<?php echo h($smtp_config['smtp_username'] ?? ''); ?>" required
                   placeholder="tuo-email@example.com">
        </div>

        <div class="form-group">
            <label>Password SMTP *</label>
            <input type="password" name="smtp_password" value="<?php echo h($smtp_config['smtp_password'] ?? ''); ?>" required
                   placeholder="La tua password SMTP">
            <small>Per Gmail, potrebbe essere necessaria una "App Password" invece della password normale</small>
        </div>

        <div class="form-group">
            <label>Crittografia *</label>
            <select name="smtp_encryption">
                <option value="tls" <?php echo ($smtp_config['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS (Consigliato)</option>
                <option value="ssl" <?php echo ($smtp_config['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
            </select>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 2px solid var(--gray-200);">

        <h4 style="margin-bottom: 20px; color: var(--gray-900);">
            <i class="fas fa-user"></i> Informazioni Mittente
        </h4>

        <div class="form-group">
            <label>Email Mittente *</label>
            <input type="email" name="from_email" value="<?php echo h($smtp_config['from_email'] ?? ''); ?>" required
                   placeholder="noreply@example.com">
            <small>L'indirizzo email che apparirà come mittente</small>
        </div>

        <div class="form-group">
            <label>Nome Mittente *</label>
            <input type="text" name="from_name" value="<?php echo h($smtp_config['from_name'] ?? 'AGstudio CRM'); ?>" required
                   placeholder="AGstudio CRM">
            <small>Il nome che apparirà come mittente</small>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="enabled" id="enabled" value="1" <?php echo ($smtp_config['enabled'] ?? 1) ? 'checked' : ''; ?>>
                <label for="enabled" style="margin: 0;">Abilita invio email (disabilita per test)</label>
            </div>
        </div>

        <button type="submit" name="save_smtp" class="btn btn-success">
            <i class="fas fa-save"></i> Salva Configurazione
        </button>
    </form>
</div>

<?php if ($smtp_config): ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-vial"></i> Test Invio Email</h3>
        </div>

        <div class="test-email-section">
            <p style="margin-bottom: 20px; color: var(--gray-700);">
                Invia un'email di test per verificare che la configurazione SMTP funzioni correttamente.
            </p>

            <form method="POST">
                <div style="display: flex; gap: 15px; align-items: end;">
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label>Indirizzo Email Destinatario</label>
                        <input type="email" name="test_email_address" required
                               placeholder="test@example.com"
                               value="<?php echo h($currentUser['email'] ?? ''); ?>">
                    </div>

                    <button type="submit" name="test_email" class="btn">
                        <i class="fas fa-paper-plane"></i> Invia Email di Test
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Guida Provider SMTP -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-book"></i> Guida Configurazione Provider</h3>
    </div>

    <div style="padding: 20px;">
        <h4 style="margin-bottom: 15px;">Gmail</h4>
        <ul style="color: var(--gray-700); margin-bottom: 20px;">
            <li>Server: <code>smtp.gmail.com</code></li>
            <li>Porta: <code>587</code> (TLS)</li>
            <li>Abilita "App Password" nelle impostazioni Google Account</li>
            <li>Usa la App Password generata invece della password normale</li>
        </ul>

        <h4 style="margin-bottom: 15px;">Outlook / Office 365</h4>
        <ul style="color: var(--gray-700); margin-bottom: 20px;">
            <li>Server: <code>smtp.office365.com</code></li>
            <li>Porta: <code>587</code> (TLS)</li>
            <li>Usa le credenziali del tuo account Microsoft</li>
        </ul>

        <h4 style="margin-bottom: 15px;">Provider Personalizzato</h4>
        <ul style="color: var(--gray-700);">
            <li>Consulta la documentazione del tuo provider email</li>
            <li>Cerca "SMTP settings" o "Impostazioni SMTP"</li>
            <li>Verifica che il tuo piano supporti l'invio SMTP</li>
        </ul>
    </div>
</div>

<div style="margin-top: 20px;">
    <a href="email_templates.php" class="btn">
        <i class="fas fa-envelope-open-text"></i> Gestisci Template Email
    </a>
    <a href="alert_config.php" class="btn btn-secondary">
        <i class="fas fa-bell"></i> Configurazione Alert
    </a>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
