<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Impostazioni';

$currentUser = getCurrentUser();
$errors = [];
$success = false;

// Aggiorna profilo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($nome)) {
        $errors[] = 'Il nome è obbligatorio';
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email non valida';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE users SET
                    nome = :nome,
                    cognome = :cognome,
                    email = :email
                WHERE id = :id
            ");

            $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
            $stmt->bindValue(':cognome', $cognome, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);

            $stmt->execute();

            $_SESSION['nome'] = $nome;
            logActivity('modifica_profilo', 'user', $_SESSION['user_id']);

            $success = 'Profilo aggiornato con successo';
            $currentUser = getCurrentUser(); // Ricarica dati

        } catch (Exception $e) {
            $errors[] = 'Errore nell\'aggiornamento: ' . $e->getMessage();
        }
    }
}

// Cambia password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password)) {
        $errors[] = 'Inserisci la password attuale';
    }

    if (empty($new_password)) {
        $errors[] = 'Inserisci la nuova password';
    }

    if (strlen($new_password) < 6) {
        $errors[] = 'La nuova password deve essere di almeno 6 caratteri';
    }

    if ($new_password !== $confirm_password) {
        $errors[] = 'Le password non corrispondono';
    }

    if (empty($errors)) {
        // Verifica password attuale
        if (!password_verify($current_password, $currentUser['password'])) {
            $errors[] = 'Password attuale non corretta';
        } else {
            try {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->bindValue(':password', $password_hash, SQLITE3_TEXT);
                $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
                $stmt->execute();

                logActivity('cambio_password', 'user', $_SESSION['user_id']);

                $success = 'Password cambiata con successo';

            } catch (Exception $e) {
                $errors[] = 'Errore nel cambio password: ' . $e->getMessage();
            }
        }
    }
}

// Statistiche database
$db_stats = [];
$result = $db->query("SELECT COUNT(*) as count FROM clienti");
$db_stats['clienti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM progetti");
$db_stats['progetti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM scadenze");
$db_stats['scadenze'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM documenti");
$db_stats['documenti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM activity_log");
$db_stats['activity_log'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

// Dimensione database
$db_path = __DIR__ . '/agstudio.db';
$db_size = file_exists($db_path) ? filesize($db_path) : 0;

include 'includes_header.php';
?>

<style>
.settings-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.settings-section h3 {
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.form-full {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #2c3e50;
}

.form-group input {
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.alert-success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 15px 20px;
    border-radius: 8px;
    color: #155724;
    margin-bottom: 20px;
}

.alert-danger {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    padding: 15px 20px;
    border-radius: 8px;
    color: #721c24;
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #3498db;
}

.info-item label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 5px;
}

.info-item value {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($success): ?>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> <?php echo h($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert-danger">
        <strong>Errori:</strong>
        <ul style="margin: 10px 0 0 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Profilo Utente -->
<div class="settings-section">
    <h3><i class="fas fa-user"></i> Profilo Utente</h3>

    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo h($currentUser['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label>Cognome</label>
                <input type="text" name="cognome" value="<?php echo h($currentUser['cognome']); ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo h($currentUser['email']); ?>">
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo h($currentUser['username']); ?>" disabled>
                <small style="color: #7f8c8d; margin-top: 5px;">Il username non può essere modificato</small>
            </div>

            <div class="form-group">
                <label>Ruolo</label>
                <input type="text" value="<?php echo ucfirst(h($currentUser['ruolo'])); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Ultimo Accesso</label>
                <input type="text" value="<?php echo formatDateTime($currentUser['ultimo_accesso']); ?>" disabled>
            </div>
        </div>

        <button type="submit" name="update_profile" class="btn">
            <i class="fas fa-save"></i> Salva Modifiche
        </button>
    </form>
</div>

<!-- Sicurezza -->
<div class="settings-section">
    <h3><i class="fas fa-lock"></i> Sicurezza - Cambia Password</h3>

    <form method="POST">
        <div class="form-grid">
            <div class="form-group form-full">
                <label>Password Attuale</label>
                <input type="password" name="current_password" autocomplete="current-password">
            </div>

            <div class="form-group">
                <label>Nuova Password</label>
                <input type="password" name="new_password" autocomplete="new-password">
                <small style="color: #7f8c8d; margin-top: 5px;">Minimo 6 caratteri</small>
            </div>

            <div class="form-group">
                <label>Conferma Nuova Password</label>
                <input type="password" name="confirm_password" autocomplete="new-password">
            </div>
        </div>

        <button type="submit" name="change_password" class="btn" style="background: #e74c3c;">
            <i class="fas fa-key"></i> Cambia Password
        </button>
    </form>
</div>

<!-- Informazioni Database -->
<div class="settings-section">
    <h3><i class="fas fa-database"></i> Informazioni Database</h3>

    <div class="info-grid">
        <div class="info-item">
            <label>Clienti</label>
            <value><?php echo $db_stats['clienti']; ?></value>
        </div>

        <div class="info-item">
            <label>Progetti</label>
            <value><?php echo $db_stats['progetti']; ?></value>
        </div>

        <div class="info-item">
            <label>Scadenze</label>
            <value><?php echo $db_stats['scadenze']; ?></value>
        </div>

        <div class="info-item">
            <label>Documenti</label>
            <value><?php echo $db_stats['documenti']; ?></value>
        </div>

        <div class="info-item">
            <label>Activity Log</label>
            <value><?php echo $db_stats['activity_log']; ?></value>
        </div>

        <div class="info-item">
            <label>Dimensione DB</label>
            <value><?php echo formatFileSize($db_size); ?></value>
        </div>

        <div class="info-item" style="border-left-color: #27ae60;">
            <label>Versione</label>
            <value><?php echo APP_VERSION; ?></value>
        </div>

        <div class="info-item" style="border-left-color: #9b59b6;">
            <label>PHP</label>
            <value><?php echo PHP_VERSION; ?></value>
        </div>
    </div>
</div>

<!-- Informazioni Sistema -->
<div class="settings-section">
    <h3><i class="fas fa-info-circle"></i> Informazioni Sistema</h3>

    <table class="table-simple">
        <tr>
            <td><strong>Nome Applicazione</strong></td>
            <td><?php echo APP_NAME; ?></td>
        </tr>
        <tr>
            <td><strong>Versione</strong></td>
            <td><?php echo APP_VERSION; ?></td>
        </tr>
        <tr>
            <td><strong>Database</strong></td>
            <td>SQLite <?php echo SQLite3::version()['versionString']; ?></td>
        </tr>
        <tr>
            <td><strong>PHP</strong></td>
            <td><?php echo PHP_VERSION; ?></td>
        </tr>
        <tr>
            <td><strong>Server</strong></td>
            <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td>
        </tr>
        <tr>
            <td><strong>Percorso Database</strong></td>
            <td><code><?php echo h($db_path); ?></code></td>
        </tr>
    </table>
</div>

<?php include 'includes_footer.php'; ?>
