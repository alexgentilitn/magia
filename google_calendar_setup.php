<?php
$pageTitle = 'Google Calendar - Configurazione';
require_once 'config.php';
requireLogin();

$currentUser = getCurrentUser();

// Gestione azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_credentials']) && $currentUser['ruolo'] === 'admin') {
        // Solo admin può salvare credenziali
        $client_id = trim($_POST['client_id']);
        $client_secret = trim($_POST['client_secret']);
        $redirect_uri = trim($_POST['redirect_uri']);

        // Verifica se esistono già credenziali
        $result = $db->query("SELECT COUNT(*) as count FROM google_credentials");
        $row = $result->fetchArray(SQLITE3_ASSOC);

        if ($row['count'] > 0) {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE google_credentials SET
                    client_id = :client_id,
                    client_secret = :client_secret,
                    redirect_uri = :redirect_uri
                WHERE id = 1
            ");
        } else {
            // INSERT
            $stmt = $db->prepare("
                INSERT INTO google_credentials (client_id, client_secret, redirect_uri)
                VALUES (:client_id, :client_secret, :redirect_uri)
            ");
        }

        $stmt->bindValue(':client_id', $client_id, SQLITE3_TEXT);
        $stmt->bindValue(':client_secret', $client_secret, SQLITE3_TEXT);
        $stmt->bindValue(':redirect_uri', $redirect_uri, SQLITE3_TEXT);
        $stmt->execute();

        $message = 'Credenziali Google salvate con successo!';
        logActivity('update_google_credentials', 'google_credentials', null, 'Credenziali Google aggiornate');
    } elseif (isset($_POST['disconnect'])) {
        // Disconnetti account Google
        $stmt = $db->prepare("UPDATE users SET google_refresh_token = NULL WHERE id = :id");
        $stmt->bindValue(':id', $currentUser['id'], SQLITE3_INTEGER);
        $stmt->execute();

        $message = 'Account Google disconnesso.';
        logActivity('disconnect_google', 'users', $currentUser['id'], 'Disconnesso Google Calendar');

        // Ricarica dati utente
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $currentUser['id'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        $currentUser = $result->fetchArray(SQLITE3_ASSOC);
    }
}

// Carica credenziali Google
$credentials = null;
$result = $db->query("SELECT * FROM google_credentials LIMIT 1");
$credentials = $result->fetchArray(SQLITE3_ASSOC);

// Verifica connessione utente
$is_connected = !empty($currentUser['google_refresh_token']);

include 'includes_header.php';
?>

<?php if (isset($message)): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo h($message); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        <strong>✗</strong> <?php echo h($error); ?>
    </div>
<?php endif; ?>

<!-- Stato Connessione -->
<div class="card">
    <div class="card-header">
        <h3><i class="fab fa-google"></i> Stato Connessione</h3>
    </div>

    <?php if ($is_connected): ?>
        <div style="padding: 20px; background: #e8f5e9; border-radius: 8px; border-left: 3px solid #388e3c; margin-bottom: 20px;">
            <h4 style="color: #388e3c; margin-bottom: 10px;">
                <i class="fas fa-check-circle"></i> Account Connesso
            </h4>
            <p style="color: var(--gray-700); margin-bottom: 0;">
                Il tuo account Google Calendar è collegato correttamente. Le scadenze verranno sincronizzate automaticamente.
            </p>
        </div>

        <form method="POST" onsubmit="return confirm('Sei sicuro di voler disconnettere Google Calendar?');">
            <button type="submit" name="disconnect" class="btn btn-danger">
                <i class="fas fa-unlink"></i> Disconnetti Google Calendar
            </button>
        </form>
    <?php else: ?>
        <div style="padding: 20px; background: #fff3e0; border-radius: 8px; border-left: 3px solid #f57c00; margin-bottom: 20px;">
            <h4 style="color: #f57c00; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Account Non Connesso
            </h4>
            <p style="color: var(--gray-700); margin-bottom: 0;">
                Collega il tuo account Google per sincronizzare le scadenze con Google Calendar.
            </p>
        </div>

        <?php if ($credentials): ?>
            <a href="google_oauth.php" class="btn btn-success">
                <i class="fab fa-google"></i> Connetti con Google
            </a>
        <?php else: ?>
            <div style="padding: 15px; background: #ffebee; border-radius: 6px; color: #c62828;">
                <i class="fas fa-info-circle"></i> Prima di connettere il tuo account, un amministratore deve configurare le credenziali API di Google qui sotto.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Configurazione Credenziali (solo admin) -->
<?php if ($currentUser['ruolo'] === 'admin'): ?>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-key"></i> Credenziali API Google</h3>
        </div>

        <div style="padding: 15px; background: #e3f2fd; border-radius: 6px; margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px; color: var(--gray-900);">
                <i class="fas fa-info-circle"></i> Come ottenere le credenziali Google
            </h4>
            <ol style="margin: 10px 0; padding-left: 20px; color: var(--gray-700);">
                <li>Vai su <a href="https://console.cloud.google.com/" target="_blank" style="color: var(--accent);">Google Cloud Console</a></li>
                <li>Crea un nuovo progetto o seleziona uno esistente</li>
                <li>Abilita la <strong>Google Calendar API</strong></li>
                <li>Vai su "Credenziali" → "Crea credenziali" → "ID client OAuth 2.0"</li>
                <li>Tipo applicazione: <strong>Applicazione web</strong></li>
                <li>Aggiungi URI di reindirizzamento autorizzati: <code><?php echo 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/google_oauth_callback.php'; ?></code></li>
                <li>Copia Client ID e Client Secret qui sotto</li>
            </ol>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Client ID *</label>
                <input type="text" name="client_id" value="<?php echo h($credentials['client_id'] ?? ''); ?>" required
                       placeholder="xxxxx.apps.googleusercontent.com"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px; font-family: monospace;">
            </div>

            <div class="form-group">
                <label>Client Secret *</label>
                <input type="text" name="client_secret" value="<?php echo h($credentials['client_secret'] ?? ''); ?>" required
                       placeholder="GOCSPX-xxxxx"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px; font-family: monospace;">
            </div>

            <div class="form-group">
                <label>Redirect URI *</label>
                <input type="text" name="redirect_uri"
                       value="<?php echo h($credentials['redirect_uri'] ?? 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/google_oauth_callback.php'); ?>"
                       required readonly
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px; background: var(--gray-100); font-family: monospace;">
                <small style="color: var(--gray-600);">Questo URI deve essere aggiunto nelle impostazioni OAuth di Google Cloud Console</small>
            </div>

            <button type="submit" name="save_credentials" class="btn btn-success">
                <i class="fas fa-save"></i> Salva Credenziali
            </button>
        </form>
    </div>

    <!-- Documentazione -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-book"></i> Documentazione Tecnica</h3>
        </div>

        <h4 style="margin-bottom: 10px;">Funzionalità Sincronizzazione</h4>
        <ul style="margin-bottom: 20px; color: var(--gray-700);">
            <li><strong>Export Scadenze → Google Calendar</strong>: Le scadenze del CRM vengono create come eventi in Google Calendar</li>
            <li><strong>Sincronizzazione Modifiche</strong>: Se modifichi la data di una scadenza nel CRM, viene aggiornata anche su Google</li>
            <li><strong>Import Eventi Google</strong>: Gli eventi del tuo Google Calendar vengono visualizzati nel calendario CRM (solo lettura)</li>
            <li><strong>Sincronizzazione Manuale</strong>: Pulsante "Sincronizza Ora" per forzare la sincronizzazione</li>
        </ul>

        <h4 style="margin-bottom: 10px;">Limitazioni</h4>
        <ul style="margin-bottom: 20px; color: var(--gray-700);">
            <li>Gli eventi creati su Google Calendar non vengono importati come scadenze CRM (solo visualizzazione)</li>
            <li>Le modifiche fatte su Google Calendar agli eventi esportati dal CRM non vengono sincronizzate indietro</li>
            <li>Ogni utente deve connettere il proprio account Google individualmente</li>
        </ul>

        <h4 style="margin-bottom: 10px;">Permessi Richiesti</h4>
        <p style="color: var(--gray-700); margin-bottom: 10px;">
            L'applicazione richiederà i seguenti permessi OAuth:
        </p>
        <ul style="color: var(--gray-700);">
            <li><code>https://www.googleapis.com/auth/calendar.events</code> - Gestione eventi calendario</li>
            <li><code>https://www.googleapis.com/auth/calendar.readonly</code> - Lettura calendario</li>
        </ul>
    </div>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="calendar.php" class="btn">
        <i class="fas fa-calendar"></i> Vai al Calendario
    </a>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
</div>

<style>
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--gray-700);
}

code {
    background: var(--gray-100);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    color: var(--gray-900);
}
</style>

<?php include 'includes_footer.php'; ?>
