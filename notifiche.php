<?php
$pageTitle = 'Notifiche';
require_once 'config.php';
requireLogin();

$currentUser = getCurrentUser();
$user_id = $currentUser['id'];

// Gestione azioni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE notifications SET letta = 1 WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['mark_all_read'])) {
        $stmt = $db->prepare("UPDATE notifications SET letta = 1 WHERE user_id = :user_id AND letta = 0");
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
        $message = 'Tutte le notifiche sono state contrassegnate come lette.';
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM notifications WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['delete_all_read'])) {
        $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = :user_id AND letta = 1");
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();
        $message = 'Notifiche lette eliminate.';
    }
}

// Filtri
$filtro_letta = $_GET['letta'] ?? 'tutte'; // tutte, lette, non_lette
$filtro_tipo = $_GET['tipo'] ?? 'tutte'; // tutte, info, warning, danger, success

// Query base
$where = ["user_id = :user_id"];
$params = [':user_id' => $user_id];

if ($filtro_letta === 'lette') {
    $where[] = "letta = 1";
} elseif ($filtro_letta === 'non_lette') {
    $where[] = "letta = 0";
}

if ($filtro_tipo !== 'tutte') {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $filtro_tipo;
}

$where_clause = implode(' AND ', $where);

$stmt = $db->prepare("
    SELECT *
    FROM notifications
    WHERE $where_clause
    ORDER BY created_at DESC
    LIMIT 100
");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}

$result = $stmt->execute();
$notifiche = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $notifiche[] = $row;
}

// Conta notifiche non lette
$stmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND letta = 0");
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$non_lette = $result->fetchArray(SQLITE3_ASSOC)['count'];

include 'includes_header.php';
?>

<?php if (isset($message)): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo h($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3><i class="fas fa-bell"></i> Notifiche</h3>
            <?php if ($non_lette > 0): ?>
                <span class="badge badge-alta" style="margin-left: 10px;">
                    <?php echo $non_lette; ?> non lette
                </span>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 10px;">
            <?php if ($non_lette > 0): ?>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="mark_all_read" class="btn btn-secondary">
                        <i class="fas fa-check-double"></i> Segna tutte come lette
                    </button>
                </form>
            <?php endif; ?>
            <form method="POST" style="display: inline;" onsubmit="return confirm('Eliminare tutte le notifiche lette?');">
                <button type="submit" name="delete_all_read" class="btn btn-secondary">
                    <i class="fas fa-trash"></i> Elimina lette
                </button>
            </form>
            <?php if ($currentUser['ruolo'] === 'admin'): ?>
                <a href="alert_config.php" class="btn">
                    <i class="fas fa-cog"></i> Configura Alert
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtri -->
    <div style="display: flex; gap: 15px; margin-bottom: 20px; padding: 15px; background: var(--gray-50); border-radius: 6px;">
        <div>
            <label style="font-weight: 500; margin-right: 10px;">Stato:</label>
            <select onchange="window.location.href='notifiche.php?letta=' + this.value + '&tipo=<?php echo $filtro_tipo; ?>'"
                    style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px;">
                <option value="tutte" <?php echo $filtro_letta === 'tutte' ? 'selected' : ''; ?>>Tutte</option>
                <option value="non_lette" <?php echo $filtro_letta === 'non_lette' ? 'selected' : ''; ?>>Non Lette</option>
                <option value="lette" <?php echo $filtro_letta === 'lette' ? 'selected' : ''; ?>>Lette</option>
            </select>
        </div>

        <div>
            <label style="font-weight: 500; margin-right: 10px;">Tipo:</label>
            <select onchange="window.location.href='notifiche.php?letta=<?php echo $filtro_letta; ?>&tipo=' + this.value"
                    style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 6px;">
                <option value="tutte" <?php echo $filtro_tipo === 'tutte' ? 'selected' : ''; ?>>Tutte</option>
                <option value="info" <?php echo $filtro_tipo === 'info' ? 'selected' : ''; ?>>Info</option>
                <option value="warning" <?php echo $filtro_tipo === 'warning' ? 'selected' : ''; ?>>Avviso</option>
                <option value="danger" <?php echo $filtro_tipo === 'danger' ? 'selected' : ''; ?>>Urgente</option>
                <option value="success" <?php echo $filtro_tipo === 'success' ? 'selected' : ''; ?>>Successo</option>
            </select>
        </div>
    </div>

    <?php if (empty($notifiche)): ?>
        <div style="text-align: center; padding: 40px; color: var(--gray-500);">
            <i class="fas fa-bell-slash" style="font-size: 3em; margin-bottom: 15px; opacity: 0.3;"></i>
            <p>Nessuna notifica trovata.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($notifiche as $notifica): ?>
                <div style="
                    display: flex;
                    gap: 15px;
                    padding: 18px;
                    border: 1px solid var(--gray-200);
                    border-radius: 8px;
                    background: <?php echo $notifica['letta'] ? 'white' : 'var(--gray-50)'; ?>;
                    border-left: 3px solid <?php
                        switch($notifica['tipo']) {
                            case 'danger': echo 'var(--danger)'; break;
                            case 'warning': echo 'var(--warning)'; break;
                            case 'success': echo 'var(--success)'; break;
                            default: echo 'var(--info)';
                        }
                    ?>;
                ">
                    <!-- Icona -->
                    <div style="flex-shrink: 0;">
                        <div style="
                            width: 45px;
                            height: 45px;
                            border-radius: 50%;
                            background: <?php
                                switch($notifica['tipo']) {
                                    case 'danger': echo '#ffebee'; break;
                                    case 'warning': echo '#fff3e0'; break;
                                    case 'success': echo '#e8f5e9'; break;
                                    default: echo '#e3f2fd';
                                }
                            ?>;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.2em;
                        ">
                            <?php
                            switch($notifica['tipo']) {
                                case 'danger': echo '<i class="fas fa-exclamation-circle" style="color: var(--danger);"></i>'; break;
                                case 'warning': echo '<i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i>'; break;
                                case 'success': echo '<i class="fas fa-check-circle" style="color: var(--success);"></i>'; break;
                                default: echo '<i class="fas fa-info-circle" style="color: var(--info);"></i>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Contenuto -->
                    <div style="flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                            <h4 style="margin: 0; color: var(--gray-900); font-size: 1em;">
                                <?php if (!$notifica['letta']): ?>
                                    <span style="
                                        display: inline-block;
                                        width: 8px;
                                        height: 8px;
                                        background: var(--accent);
                                        border-radius: 50%;
                                        margin-right: 8px;
                                    "></span>
                                <?php endif; ?>
                                <?php echo h($notifica['titolo']); ?>
                            </h4>
                            <span style="font-size: 0.85em; color: var(--gray-500); white-space: nowrap; margin-left: 15px;">
                                <?php echo formatDateTime($notifica['created_at'], 'd/m/Y H:i'); ?>
                            </span>
                        </div>
                        <p style="margin: 5px 0; color: var(--gray-700); font-size: 0.95em;">
                            <?php echo h($notifica['messaggio']); ?>
                        </p>

                        <div style="display: flex; gap: 10px; margin-top: 12px;">
                            <?php if ($notifica['url']): ?>
                                <a href="<?php echo h($notifica['url']); ?>" class="btn" style="padding: 6px 12px; font-size: 0.85em;">
                                    <i class="fas fa-eye"></i> Visualizza
                                </a>
                            <?php endif; ?>

                            <?php if (!$notifica['letta']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $notifica['id']; ?>">
                                    <button type="submit" name="mark_read" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85em;">
                                        <i class="fas fa-check"></i> Segna come letta
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" style="display: inline;" onsubmit="return confirm('Eliminare questa notifica?');">
                                <input type="hidden" name="id" value="<?php echo $notifica['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85em;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Torna alla Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
