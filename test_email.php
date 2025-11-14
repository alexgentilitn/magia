<?php
/**
 * Test rapido invio email
 * Usa questo script per verificare che la configurazione SMTP funzioni correttamente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Test Invio Email';

try {
    require_once 'config.php';
    requireLogin();
    $currentUser = getCurrentUser();

    // Solo admin può testare email
    if ($currentUser['ruolo'] !== 'admin') {
        die("Accesso negato. Solo gli amministratori possono testare l'invio email.");
    }
} catch (Throwable $e) {
    die("ERRORE: " . $e->getMessage());
}

$message = '';
$error = '';
$test_result = null;

// Processa test email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test'])) {
    $test_email = trim($_POST['test_email']);
    $test_subject = trim($_POST['test_subject'] ?? 'Test Email da AGstudio CRM');
    $test_message = trim($_POST['test_message'] ?? 'Questa è una email di test. Se la ricevi, la configurazione SMTP funziona correttamente!');

    // Validazione
    if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email non valida';
    } else {
        require_once 'send_email_helper.php';

        $corpo_html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px;'>✅ Test Email</h1>
                <p style='margin: 10px 0 0; opacity: 0.9;'>AGstudio CRM</p>
            </div>

            <div style='background: #f8f9fa; padding: 30px;'>
                <div style='background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                    <h2 style='color: #333; margin-top: 0;'>🎉 Configurazione SMTP Funzionante!</h2>
                    <p style='color: #666; line-height: 1.6;'>" . nl2br(htmlspecialchars($test_message)) . "</p>

                    <div style='background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                        <strong style='color: #1976d2;'>ℹ️ Informazioni Test:</strong><br>
                        <span style='color: #555;'>
                            Data: " . date('d/m/Y H:i:s') . "<br>
                            Inviato da: " . htmlspecialchars($currentUser['nome']) . " (" . htmlspecialchars($currentUser['email']) . ")
                        </span>
                    </div>

                    <p style='color: #666; font-size: 14px; margin-bottom: 0;'>
                        Se hai ricevuto questa email correttamente, il tuo sistema di invio email è configurato correttamente e pronto all'uso!
                    </p>
                </div>
            </div>

            <div style='background: #333; color: #fff; padding: 20px; text-align: center; font-size: 12px;'>
                <p style='margin: 0; opacity: 0.8;'>Powered by AGstudio CRM</p>
            </div>
        </div>
        ";

        $start_time = microtime(true);
        $success = sendSimpleEmail($test_email, $test_subject, $corpo_html);
        $end_time = microtime(true);
        $elapsed_time = round(($end_time - $start_time) * 1000, 2);

        if ($success) {
            $message = "✅ Email di test inviata con successo a <strong>$test_email</strong>!";
            $test_result = [
                'success' => true,
                'time' => $elapsed_time,
                'recipient' => $test_email,
                'subject' => $test_subject
            ];
        } else {
            $error = "❌ Errore durante l'invio. Controlla la configurazione SMTP.";
            $test_result = [
                'success' => false,
                'time' => $elapsed_time
            ];
        }
    }
}

// Carica configurazione SMTP attuale
$smtp_config = null;
$result = $db->query("SELECT * FROM smtp_config WHERE enabled = 1 ORDER BY id DESC LIMIT 1");
if ($result) {
    $smtp_config = $result->fetchArray(SQLITE3_ASSOC);
}

include 'includes_header.php';
?>

<style>
.test-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

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
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--gray-300);
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent);
}

.smtp-status {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.smtp-status-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.smtp-status-item:last-child {
    border-bottom: none;
}

.status-label {
    font-weight: 600;
    color: #666;
}

.status-value {
    color: #333;
}

.test-result {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 2px solid #28a745;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
}

.test-result.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-color: #dc3545;
}

.test-result-icon {
    font-size: 48px;
    text-align: center;
    margin-bottom: 15px;
}

.test-result-title {
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 15px;
}

.test-result-details {
    background: rgba(255,255,255,0.5);
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.test-result-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        <strong>✗</strong> <?php echo h($error); ?>
    </div>
<?php endif; ?>

<?php if ($test_result): ?>
    <div class="test-result <?php echo !$test_result['success'] ? 'error' : ''; ?>">
        <div class="test-result-icon">
            <?php echo $test_result['success'] ? '✅' : '❌'; ?>
        </div>
        <div class="test-result-title">
            <?php echo $test_result['success'] ? 'Test Completato con Successo!' : 'Test Fallito'; ?>
        </div>

        <?php if ($test_result['success']): ?>
            <div style="text-align: center; color: #155724; margin-bottom: 15px;">
                La configurazione SMTP funziona correttamente!<br>
                Controlla la casella <strong><?php echo h($test_result['recipient']); ?></strong>
            </div>

            <div class="test-result-details">
                <div class="test-result-item">
                    <span><strong>📧 Destinatario:</strong></span>
                    <span><?php echo h($test_result['recipient']); ?></span>
                </div>
                <div class="test-result-item">
                    <span><strong>📝 Oggetto:</strong></span>
                    <span><?php echo h($test_result['subject']); ?></span>
                </div>
                <div class="test-result-item">
                    <span><strong>⏱️ Tempo invio:</strong></span>
                    <span><?php echo $test_result['time']; ?> ms</span>
                </div>
                <div class="test-result-item">
                    <span><strong>🕐 Orario:</strong></span>
                    <span><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: #721c24;">
                <p>Verifica la configurazione SMTP e riprova.</p>
                <p style="font-size: 14px; margin-top: 10px;">
                    <strong>Tempo tentativo:</strong> <?php echo $test_result['time']; ?> ms
                </p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="test-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0;"><i class="fas fa-envelope-open-text"></i> Test Invio Email</h2>
        <a href="smtp_settings.php" class="btn btn-secondary">
            <i class="fas fa-cog"></i> Configurazione SMTP
        </a>
    </div>

    <?php if ($smtp_config): ?>
        <div class="smtp-status">
            <h3 style="margin-top: 0; margin-bottom: 15px; color: #333;">
                <i class="fas fa-server"></i> Configurazione SMTP Attiva
            </h3>
            <div class="smtp-status-item">
                <span class="status-label">Host SMTP:</span>
                <span class="status-value"><?php echo h($smtp_config['smtp_host']); ?>:<?php echo h($smtp_config['smtp_port']); ?></span>
            </div>
            <div class="smtp-status-item">
                <span class="status-label">Username:</span>
                <span class="status-value"><?php echo h($smtp_config['smtp_username']); ?></span>
            </div>
            <div class="smtp-status-item">
                <span class="status-label">Encryption:</span>
                <span class="status-value"><?php echo strtoupper(h($smtp_config['smtp_encryption'])); ?></span>
            </div>
            <div class="smtp-status-item">
                <span class="status-label">Mittente:</span>
                <span class="status-value"><?php echo h($smtp_config['from_name']); ?> &lt;<?php echo h($smtp_config['from_email']); ?>&gt;</span>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="background: #fff3cd; border-color: #ffc107; color: #856404; margin-bottom: 20px;">
            <strong>⚠️ Attenzione:</strong> Nessuna configurazione SMTP attiva.
            <a href="smtp_settings.php" style="margin-left: 10px;">Configura ora</a>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email Destinatario Test *</label>
            <input type="email" name="test_email" value="<?php echo h($_POST['test_email'] ?? $currentUser['email']); ?>" required
                   placeholder="destinatario@example.com">
            <small style="display: block; margin-top: 5px; color: #666;">
                Inserisci l'email dove vuoi ricevere il messaggio di test
            </small>
        </div>

        <div class="form-group">
            <label>Oggetto Email</label>
            <input type="text" name="test_subject" value="<?php echo h($_POST['test_subject'] ?? 'Test Email da AGstudio CRM'); ?>"
                   placeholder="Oggetto dell'email di test">
        </div>

        <div class="form-group">
            <label>Messaggio</label>
            <textarea name="test_message" rows="4"
                      placeholder="Messaggio personalizzato da includere nell'email di test"><?php echo h($_POST['test_message'] ?? 'Questa è una email di test. Se la ricevi, la configurazione SMTP funziona correttamente!'); ?></textarea>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="submit" name="send_test" class="btn btn-success" <?php echo !$smtp_config ? 'disabled' : ''; ?>>
                <i class="fas fa-paper-plane"></i> Invia Email di Test
            </button>

            <?php if (!$smtp_config): ?>
                <span style="color: #dc3545; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> Configura prima SMTP
                </span>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin-top: 0;"><i class="fas fa-question-circle"></i> Troubleshooting</h3>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
        <h4 style="margin-top: 0; color: #dc3545;">❌ Email non arriva</h4>
        <ul style="margin: 0; padding-left: 20px; color: #666;">
            <li>Verifica che la configurazione SMTP sia corretta</li>
            <li>Controlla la cartella SPAM</li>
            <li>Verifica che username e password siano corretti</li>
            <li>Assicurati che la porta sia aperta sul firewall</li>
        </ul>
    </div>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
        <h4 style="margin-top: 0; color: #ffc107;">⚠️ Email in SPAM</h4>
        <ul style="margin: 0; padding-left: 20px; color: #666;">
            <li>Configura record SPF nel DNS del dominio</li>
            <li>Configura record DKIM per firma digitale</li>
            <li>Configura record DMARC per policy</li>
            <li>Usa un indirizzo mittente del dominio verificato</li>
        </ul>
    </div>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
        <h4 style="margin-top: 0; color: #28a745;">✅ Provider SMTP Consigliati</h4>
        <ul style="margin: 0; padding-left: 20px; color: #666;">
            <li><strong>Gmail:</strong> smtp.gmail.com:587 (TLS) - Richiede App Password se 2FA attivo</li>
            <li><strong>Outlook:</strong> smtp-mail.outlook.com:587 (TLS)</li>
            <li><strong>SendGrid:</strong> smtp.sendgrid.net:587 (TLS) - Professionale, alta deliverability</li>
            <li><strong>Mailgun:</strong> smtp.mailgun.org:587 (TLS) - Per volumi alti</li>
        </ul>
    </div>
</div>

<?php include 'includes_footer.php'; ?>
