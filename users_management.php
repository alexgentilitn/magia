<?php
$pageTitle = 'Gestione Utenti';
require_once 'config.php';
requireLogin();

// Solo admin può gestire utenti
$currentUser = getCurrentUser();
if ($currentUser['ruolo'] !== 'admin') {
    die('Accesso negato. Solo gli amministratori possono gestire gli utenti.');
}

// Gestione azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $email = trim($_POST['email']);
        $nome = trim($_POST['nome']);
        $cognome = trim($_POST['cognome']);
        $ruolo = $_POST['ruolo'];
        $reparto = trim($_POST['reparto'] ?? '');
        $permessi = $_POST['permessi'] ?? 'all';
        $attivo = isset($_POST['attivo']) ? 1 : 0;

        // Validazione
        if (empty($username) || empty($password) || empty($email) || empty($nome)) {
            $error = 'Username, password, email e nome sono obbligatori.';
        } else {
            // Verifica username univoco
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = :username");
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $result = $stmt->execute();
            if ($result->fetchArray(SQLITE3_ASSOC)['count'] > 0) {
                $error = 'Username già esistente.';
            } else {
                // Crea utente
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("
                    INSERT INTO users (username, password, email, nome, cognome, ruolo, reparto, permessi, attivo)
                    VALUES (:username, :password, :email, :nome, :cognome, :ruolo, :reparto, :permessi, :attivo)
                ");
                $stmt->bindValue(':username', $username, SQLITE3_TEXT);
                $stmt->bindValue(':password', $password_hash, SQLITE3_TEXT);
                $stmt->bindValue(':email', $email, SQLITE3_TEXT);
                $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
                $stmt->bindValue(':cognome', $cognome, SQLITE3_TEXT);
                $stmt->bindValue(':ruolo', $ruolo, SQLITE3_TEXT);
                $stmt->bindValue(':reparto', $reparto, SQLITE3_TEXT);
                $stmt->bindValue(':permessi', $permessi, SQLITE3_TEXT);
                $stmt->bindValue(':attivo', $attivo, SQLITE3_INTEGER);
                $stmt->execute();

                $message = 'Utente creato con successo!';
                logActivity('create_user', 'users', $db->lastInsertRowID(), "Creato utente: $username");
            }
        }
    } elseif (isset($_POST['update_user'])) {
        $id = (int)$_POST['id'];
        $email = trim($_POST['email']);
        $nome = trim($_POST['nome']);
        $cognome = trim($_POST['cognome']);
        $ruolo = $_POST['ruolo'];
        $reparto = trim($_POST['reparto'] ?? '');
        $permessi = $_POST['permessi'] ?? 'all';
        $attivo = isset($_POST['attivo']) ? 1 : 0;

        $stmt = $db->prepare("
            UPDATE users SET
                email = :email,
                nome = :nome,
                cognome = :cognome,
                ruolo = :ruolo,
                reparto = :reparto,
                permessi = :permessi,
                attivo = :attivo
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
        $stmt->bindValue(':cognome', $cognome, SQLITE3_TEXT);
        $stmt->bindValue(':ruolo', $ruolo, SQLITE3_TEXT);
        $stmt->bindValue(':reparto', $reparto, SQLITE3_TEXT);
        $stmt->bindValue(':permessi', $permessi, SQLITE3_TEXT);
        $stmt->bindValue(':attivo', $attivo, SQLITE3_INTEGER);
        $stmt->execute();

        $message = 'Utente aggiornato con successo!';
        logActivity('update_user', 'users', $id, "Aggiornato utente ID: $id");
    } elseif (isset($_POST['reset_password'])) {
        $id = (int)$_POST['id'];
        $new_password = $_POST['new_password'];

        if (strlen($new_password) < 6) {
            $error = 'La password deve essere di almeno 6 caratteri.';
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':password', $password_hash, SQLITE3_TEXT);
            $stmt->execute();

            $message = 'Password reimpostata con successo!';
            logActivity('reset_password', 'users', $id, "Password reimpostata per utente ID: $id");
        }
    } elseif (isset($_POST['delete_user'])) {
        $id = (int)$_POST['id'];

        // Non può eliminare se stesso
        if ($id == $currentUser['id']) {
            $error = 'Non puoi eliminare il tuo stesso account.';
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            $message = 'Utente eliminato con successo!';
            logActivity('delete_user', 'users', $id, "Eliminato utente ID: $id");
        }
    }
}

// Carica tutti gli utenti
$users = [];
$result = $db->query("SELECT * FROM users ORDER BY ruolo, nome");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

// Statistiche
$totale_utenti = count($users);
$utenti_attivi = count(array_filter($users, fn($u) => $u['attivo'] == 1));
$admin_count = count(array_filter($users, fn($u) => $u['ruolo'] === 'admin'));

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

<!-- Statistiche -->
<div class="stats-grid" style="margin-bottom: 25px;">
    <div class="stat-card">
        <h4>Totale Utenti</h4>
        <div class="stat-value"><?php echo $totale_utenti; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--success);">
        <h4>Utenti Attivi</h4>
        <div class="stat-value"><?php echo $utenti_attivi; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: var(--warning);">
        <h4>Amministratori</h4>
        <div class="stat-value"><?php echo $admin_count; ?></div>
    </div>
</div>

<!-- Form Nuovo Utente -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-user-plus"></i> Nuovo Utente</h3>
    </div>

    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Username *</label>
                <input type="text" name="username" required
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Password *</label>
                <input type="password" name="password" required minlength="6"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email *</label>
                <input type="email" name="email" required
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nome *</label>
                <input type="text" name="nome" required
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Cognome</label>
                <input type="text" name="cognome"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Ruolo *</label>
                <select name="ruolo" required
                        style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                    <option value="user">Utente</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Amministratore</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Reparto</label>
                <input type="text" name="reparto" placeholder="es. Sviluppo, Design, Marketing"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Permessi</label>
                <select name="permessi"
                        style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                    <option value="all">Tutti i permessi</option>
                    <option value="read">Solo lettura</option>
                    <option value="custom">Personalizzati</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="attivo" checked
                       style="width: 18px; height: 18px;">
                <span style="font-weight: 500;">Utente Attivo</span>
            </label>
        </div>

        <button type="submit" name="add_user" class="btn btn-success">
            <i class="fas fa-plus"></i> Crea Utente
        </button>
    </form>
</div>

<!-- Lista Utenti -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> Utenti Registrati</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ruolo</th>
                <th>Reparto</th>
                <th>Stato</th>
                <th>Ultimo Accesso</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><strong><?php echo h($user['username']); ?></strong></td>
                    <td><?php echo h($user['nome'] . ' ' . ($user['cognome'] ?? '')); ?></td>
                    <td><?php echo h($user['email']); ?></td>
                    <td>
                        <?php
                        $ruolo_badges = [
                            'admin' => '<span class="badge badge-alta">Admin</span>',
                            'manager' => '<span class="badge badge-media">Manager</span>',
                            'user' => '<span class="badge badge-bassa">Utente</span>'
                        ];
                        echo $ruolo_badges[$user['ruolo']] ?? h($user['ruolo']);
                        ?>
                    </td>
                    <td><?php echo h($user['reparto'] ?? '-'); ?></td>
                    <td>
                        <?php if ($user['attivo']): ?>
                            <span class="badge badge-pagato">Attivo</span>
                        <?php else: ?>
                            <span class="badge badge-annullato">Disattivato</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if ($user['ultimo_accesso']) {
                            echo formatDateTime($user['ultimo_accesso'], 'd/m/Y H:i');
                        } else {
                            echo '<span style="color: var(--gray-400);">Mai</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                class="btn" style="padding: 6px 12px; font-size: 0.85em;">
                            <i class="fas fa-edit"></i> Modifica
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Modifica Utente -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-bottom: 20px;">
            <i class="fas fa-user-edit"></i> Modifica Utente
        </h3>

        <form method="POST" id="editForm">
            <input type="hidden" name="id" id="edit_id">

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email *</label>
                <input type="email" name="email" id="edit_email" required
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nome *</label>
                <input type="text" name="nome" id="edit_nome" required
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Cognome</label>
                <input type="text" name="cognome" id="edit_cognome"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Ruolo *</label>
                <select name="ruolo" id="edit_ruolo" required
                        style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                    <option value="user">Utente</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Amministratore</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Reparto</label>
                <input type="text" name="reparto" id="edit_reparto"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Permessi</label>
                <select name="permessi" id="edit_permessi"
                        style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                    <option value="all">Tutti i permessi</option>
                    <option value="read">Solo lettura</option>
                    <option value="custom">Personalizzati</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="attivo" id="edit_attivo"
                           style="width: 18px; height: 18px;">
                    <span style="font-weight: 500;">Utente Attivo</span>
                </label>
            </div>

            <hr style="margin: 20px 0;">

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">
                    Reimposta Password (lascia vuoto per non modificare)
                </label>
                <input type="password" name="new_password" id="edit_password" minlength="6"
                       style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: space-between; margin-top: 25px;">
                <div>
                    <button type="submit" name="update_user" class="btn btn-success">
                        <i class="fas fa-save"></i> Salva Modifiche
                    </button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                        Annulla
                    </button>
                </div>
                <button type="button" onclick="deleteUser()" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Elimina
                </button>
            </div>
        </form>

        <form method="POST" id="resetPasswordForm" style="display: none;">
            <input type="hidden" name="id" id="reset_id">
            <input type="hidden" name="new_password" id="reset_password">
        </form>

        <form method="POST" id="deleteForm" style="display: none;">
            <input type="hidden" name="id" id="delete_id">
            <input type="hidden" name="delete_user" value="1">
        </form>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_nome').value = user.nome || '';
    document.getElementById('edit_cognome').value = user.cognome || '';
    document.getElementById('edit_ruolo').value = user.ruolo || 'user';
    document.getElementById('edit_reparto').value = user.reparto || '';
    document.getElementById('edit_permessi').value = user.permessi || 'all';
    document.getElementById('edit_attivo').checked = user.attivo == 1;
    document.getElementById('edit_password').value = '';

    document.getElementById('reset_id').value = user.id;
    document.getElementById('delete_id').value = user.id;

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function deleteUser() {
    if (confirm('Sei sicuro di voler eliminare questo utente? Questa azione è irreversibile.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Submit con gestione password
document.getElementById('editForm').addEventListener('submit', function(e) {
    const password = document.getElementById('edit_password').value;
    if (password.length > 0) {
        e.preventDefault();
        if (confirm('Vuoi anche reimpostare la password per questo utente?')) {
            // Invia reset password separatamente
            document.getElementById('reset_password').value = password;
            document.getElementById('resetPasswordForm').submit();
        } else {
            // Rimuovi password dal form e invia
            document.getElementById('edit_password').value = '';
            this.submit();
        }
    }
});

// Chiudi modal cliccando fuori
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<div style="margin-top: 20px;">
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Torna alla Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
