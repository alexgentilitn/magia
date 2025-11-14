<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($task_id == 0) {
    header('Location: tasks.php');
    exit;
}

$message = '';
$error = '';

// Carica task
$stmt = $db->prepare("
    SELECT t.*,
           p.titolo as progetto_nome,
           p.id as progetto_id,
           u_ass.nome as assegnato_nome,
           u_ass.email as assegnato_email,
           u_cre.nome as creato_nome
    FROM tasks t
    LEFT JOIN progetti p ON t.progetto_id = p.id
    LEFT JOIN users u_ass ON t.assegnato_a = u_ass.id
    LEFT JOIN users u_cre ON t.creato_da = u_cre.id
    WHERE t.id = :id
");
$stmt->bindValue(':id', $task_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$task = $result->fetchArray(SQLITE3_ASSOC);

if (!$task) {
    header('Location: tasks.php');
    exit;
}

$pageTitle = $task['titolo'];

// Aggiungi commento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi_commento'])) {
    $commento = trim($_POST['commento']);

    if (!empty($commento)) {
        $stmt = $db->prepare("
            INSERT INTO task_comments (task_id, user_id, commento)
            VALUES (:task_id, :user_id, :commento)
        ");
        $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':commento', $commento, SQLITE3_TEXT);
        $stmt->execute();

        $message = "Commento aggiunto";
        $_POST = [];
    }
}

// Aggiungi checklist item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi_checklist'])) {
    $testo = trim($_POST['testo_checklist']);

    if (!empty($testo)) {
        $stmt = $db->prepare("
            INSERT INTO task_checklist (task_id, testo, ordine)
            VALUES (:task_id, :testo, (SELECT COALESCE(MAX(ordine), 0) + 1 FROM task_checklist WHERE task_id = :task_id2))
        ");
        $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
        $stmt->bindValue(':task_id2', $task_id, SQLITE3_INTEGER);
        $stmt->bindValue(':testo', $testo, SQLITE3_TEXT);
        $stmt->execute();

        $message = "Item aggiunto alla checklist";
        $_POST = [];
    }
}

// Toggle checklist item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_checklist'])) {
    $checklist_id = (int)$_POST['checklist_id'];

    $stmt = $db->prepare("
        UPDATE task_checklist
        SET completato = CASE WHEN completato = 1 THEN 0 ELSE 1 END
        WHERE id = :id AND task_id = :task_id
    ");
    $stmt->bindValue(':id', $checklist_id, SQLITE3_INTEGER);
    $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
    $stmt->execute();

    header("Location: task_detail.php?id=$task_id");
    exit;
}

// Aggiorna task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiorna_task'])) {
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['descrizione'] ?? '');
    $assegnato_a = !empty($_POST['assegnato_a']) ? (int)$_POST['assegnato_a'] : null;
    $priorita = $_POST['priorita'];
    $stato = $_POST['stato'];
    $data_scadenza = !empty($_POST['data_scadenza']) ? $_POST['data_scadenza'] : null;

    // Se stato è completato, imposta data completamento
    $data_completamento = null;
    if ($stato === 'completato' && $task['stato'] !== 'completato') {
        $data_completamento = date('Y-m-d H:i:s');
    }

    $stmt = $db->prepare("
        UPDATE tasks
        SET titolo = :titolo,
            descrizione = :descrizione,
            assegnato_a = :assegnato_a,
            priorita = :priorita,
            stato = :stato,
            data_scadenza = :data_scadenza,
            data_completamento = COALESCE(:data_completamento, data_completamento),
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");

    $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
    $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
    $stmt->bindValue(':assegnato_a', $assegnato_a, SQLITE3_INTEGER);
    $stmt->bindValue(':priorita', $priorita, SQLITE3_TEXT);
    $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);
    $stmt->bindValue(':data_scadenza', $data_scadenza, SQLITE3_TEXT);
    $stmt->bindValue(':data_completamento', $data_completamento, SQLITE3_TEXT);
    $stmt->bindValue(':id', $task_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        $message = "Task aggiornato con successo";

        // Ricarica task
        $stmt = $db->prepare("
            SELECT t.*,
                   p.titolo as progetto_nome,
                   p.id as progetto_id,
                   u_ass.nome as assegnato_nome,
                   u_cre.nome as creato_nome
            FROM tasks t
            LEFT JOIN progetti p ON t.progetto_id = p.id
            LEFT JOIN users u_ass ON t.assegnato_a = u_ass.id
            LEFT JOIN users u_cre ON t.creato_da = u_cre.id
            WHERE t.id = :id
        ");
        $stmt->bindValue(':id', $task_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $task = $result->fetchArray(SQLITE3_ASSOC);
    }
}

// Elimina task
if (isset($_GET['delete']) && $_GET['delete'] == 1) {
    $db->exec("DELETE FROM tasks WHERE id = $task_id");
    header('Location: tasks.php?message=task_eliminato');
    exit;
}

// Carica checklist
$checklist_result = $db->query("
    SELECT * FROM task_checklist
    WHERE task_id = $task_id
    ORDER BY ordine ASC, created_at ASC
");
$checklist = [];
while ($row = $checklist_result->fetchArray(SQLITE3_ASSOC)) {
    $checklist[] = $row;
}

// Carica commenti
$commenti_result = $db->query("
    SELECT c.*, u.nome as user_nome, u.email as user_email
    FROM task_comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.task_id = $task_id
    ORDER BY c.created_at DESC
");
$commenti = [];
while ($row = $commenti_result->fetchArray(SQLITE3_ASSOC)) {
    $commenti[] = $row;
}

// Carica utenti per assegnazione
$users_result = $db->query("SELECT id, nome, email FROM users ORDER BY nome ASC");
$users = [];
while ($row = $users_result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

include 'includes_header.php';
?>

<style>
.task-detail-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.task-main {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.task-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.task-header {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.task-title-edit {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.task-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
}

.task-breadcrumb a {
    color: var(--accent);
    text-decoration: none;
}

.task-breadcrumb a:hover {
    text-decoration: underline;
}

.task-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.section-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
}

.form-group textarea {
    min-height: 120px;
    font-family: inherit;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    font-size: 14px;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.info-value {
    color: #333;
    font-weight: 500;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-urgente {
    background: #dc3545;
    color: white;
}

.badge-alta {
    background: #fd7e14;
    color: white;
}

.badge-media {
    background: #ffc107;
    color: #000;
}

.badge-bassa {
    background: #6c757d;
    color: white;
}

.badge-stato {
    padding: 6px 12px;
    font-size: 13px;
}

.checklist-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 10px;
    transition: all 0.2s;
}

.checklist-item:hover {
    background: #e9ecef;
}

.checklist-item.completato {
    opacity: 0.6;
}

.checklist-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.checklist-item label {
    flex: 1;
    cursor: pointer;
    margin: 0;
}

.checklist-item.completato label {
    text-decoration: line-through;
    color: #6c757d;
}

.comment-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.comment-author {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.comment-date {
    font-size: 12px;
    color: #6c757d;
}

.comment-body {
    color: #333;
    line-height: 1.6;
    font-size: 14px;
}

.progress-section {
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    color: white;
}

.progress-number {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}

.progress-label {
    font-size: 14px;
    opacity: 0.9;
}

@media (max-width: 1024px) {
    .task-detail-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        ✓ <?php echo h($message); ?>
    </div>
<?php endif; ?>

<div class="task-breadcrumb">
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <span>›</span>
    <a href="tasks.php"><i class="fas fa-tasks"></i> Task</a>
    <span>›</span>
    <a href="progetti.php?id=<?php echo $task['progetto_id']; ?>"><?php echo h($task['progetto_nome']); ?></a>
    <span>›</span>
    <span><?php echo h($task['titolo']); ?></span>
</div>

<div class="task-detail-container">
    <!-- Colonna Principale -->
    <div class="task-main">
        <!-- Header Task -->
        <div class="task-header">
            <h1 class="task-title-edit"><?php echo h($task['titolo']); ?></h1>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <?php
                $stati_info = [
                    'da_fare' => ['label' => '📋 Da Fare', 'color' => '#6c757d'],
                    'in_corso' => ['label' => '🚀 In Corso', 'color' => '#0066cc'],
                    'in_revisione' => ['label' => '👀 In Revisione', 'color' => '#ffc107'],
                    'completato' => ['label' => '✅ Completato', 'color' => '#28a745']
                ];
                ?>
                <span class="badge badge-stato" style="background: <?php echo $stati_info[$task['stato']]['color']; ?>; color: white;">
                    <?php echo $stati_info[$task['stato']]['label']; ?>
                </span>

                <?php
                $priorita_badges = [
                    'urgente' => 'badge-urgente',
                    'alta' => 'badge-alta',
                    'media' => 'badge-media',
                    'bassa' => 'badge-bassa'
                ];
                $priorita_labels = [
                    'urgente' => '🔴 Urgente',
                    'alta' => '🟠 Alta',
                    'media' => '🟡 Media',
                    'bassa' => '⚪ Bassa'
                ];
                ?>
                <span class="badge <?php echo $priorita_badges[$task['priorita']]; ?>">
                    <?php echo $priorita_labels[$task['priorita']]; ?>
                </span>
            </div>

            <div class="task-actions">
                <button class="btn" onclick="document.getElementById('formModifica').style.display='block'; this.style.display='none';">
                    <i class="fas fa-edit"></i> Modifica
                </button>
                <a href="tasks.php?progetto=<?php echo $task['progetto_id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Torna ai Task
                </a>
                <a href="?id=<?php echo $task_id; ?>&delete=1" class="btn btn-danger"
                   onclick="return confirm('Eliminare questo task?')"
                   style="margin-left: auto;">
                    <i class="fas fa-trash"></i> Elimina
                </a>
            </div>
        </div>

        <!-- Form Modifica (nascosto inizialmente) -->
        <div class="section-card" id="formModifica" style="display: none;">
            <h3 class="section-title"><i class="fas fa-edit"></i> Modifica Task</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Titolo *</label>
                    <input type="text" name="titolo" value="<?php echo h($task['titolo']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Descrizione</label>
                    <textarea name="descrizione"><?php echo h($task['descrizione']); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Assegnato a</label>
                        <select name="assegnato_a">
                            <option value="">-- Non assegnato --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $task['assegnato_a'] == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($u['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Priorità</label>
                        <select name="priorita">
                            <option value="bassa" <?php echo $task['priorita'] === 'bassa' ? 'selected' : ''; ?>>⚪ Bassa</option>
                            <option value="media" <?php echo $task['priorita'] === 'media' ? 'selected' : ''; ?>>🟡 Media</option>
                            <option value="alta" <?php echo $task['priorita'] === 'alta' ? 'selected' : ''; ?>>🟠 Alta</option>
                            <option value="urgente" <?php echo $task['priorita'] === 'urgente' ? 'selected' : ''; ?>>🔴 Urgente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Stato</label>
                        <select name="stato">
                            <option value="da_fare" <?php echo $task['stato'] === 'da_fare' ? 'selected' : ''; ?>>📋 Da Fare</option>
                            <option value="in_corso" <?php echo $task['stato'] === 'in_corso' ? 'selected' : ''; ?>>🚀 In Corso</option>
                            <option value="in_revisione" <?php echo $task['stato'] === 'in_revisione' ? 'selected' : ''; ?>>👀 In Revisione</option>
                            <option value="completato" <?php echo $task['stato'] === 'completato' ? 'selected' : ''; ?>>✅ Completato</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Data Scadenza</label>
                        <input type="date" name="data_scadenza" value="<?php echo h($task['data_scadenza']); ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="aggiorna_task" class="btn btn-success">
                        <i class="fas fa-save"></i> Salva Modifiche
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('formModifica').style.display='none';">
                        Annulla
                    </button>
                </div>
            </form>
        </div>

        <!-- Descrizione -->
        <?php if ($task['descrizione']): ?>
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-align-left"></i> Descrizione</h3>
                <div style="line-height: 1.8; color: #333;">
                    <?php echo nl2br(h($task['descrizione'])); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Checklist -->
        <div class="section-card">
            <h3 class="section-title">
                <i class="fas fa-check-square"></i> Checklist
                <?php if (!empty($checklist)): ?>
                    <span style="font-size: 14px; font-weight: normal; color: #666; margin-left: 10px;">
                        (<?php echo count(array_filter($checklist, fn($c) => $c['completato'] == 1)); ?>/<?php echo count($checklist); ?>)
                    </span>
                <?php endif; ?>
            </h3>

            <?php foreach ($checklist as $item): ?>
                <form method="POST" class="checklist-item <?php echo $item['completato'] ? 'completato' : ''; ?>">
                    <input type="hidden" name="checklist_id" value="<?php echo $item['id']; ?>">
                    <input type="checkbox" <?php echo $item['completato'] ? 'checked' : ''; ?>
                           onchange="this.form.submit()" name="toggle_checklist" id="check_<?php echo $item['id']; ?>">
                    <label for="check_<?php echo $item['id']; ?>"><?php echo h($item['testo']); ?></label>
                </form>
            <?php endforeach; ?>

            <form method="POST" style="margin-top: 15px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="testo_checklist" placeholder="Nuovo item checklist..."
                           style="flex: 1; padding: 10px; border: 2px solid #dee2e6; border-radius: 6px;">
                    <button type="submit" name="aggiungi_checklist" class="btn">
                        <i class="fas fa-plus"></i> Aggiungi
                    </button>
                </div>
            </form>
        </div>

        <!-- Commenti -->
        <div class="section-card">
            <h3 class="section-title">
                <i class="fas fa-comments"></i> Commenti
                <span style="font-size: 14px; font-weight: normal; color: #666; margin-left: 10px;">
                    (<?php echo count($commenti); ?>)
                </span>
            </h3>

            <form method="POST" style="margin-bottom: 25px;">
                <textarea name="commento" placeholder="Scrivi un commento..." required
                          style="width: 100%; min-height: 80px; padding: 12px; border: 2px solid #dee2e6; border-radius: 6px; font-family: inherit;"></textarea>
                <button type="submit" name="aggiungi_commento" class="btn" style="margin-top: 10px;">
                    <i class="fas fa-paper-plane"></i> Invia Commento
                </button>
            </form>

            <?php foreach ($commenti as $comm): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="comment-author">
                            <i class="fas fa-user-circle"></i> <?php echo h($comm['user_nome']); ?>
                        </div>
                        <div class="comment-date">
                            <?php echo formatDateTime($comm['created_at']); ?>
                        </div>
                    </div>
                    <div class="comment-body">
                        <?php echo nl2br(h($comm['commento'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($commenti)): ?>
                <div style="text-align: center; padding: 30px; color: #adb5bd;">
                    <i class="fas fa-comments" style="font-size: 2em; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p style="margin: 0;">Nessun commento ancora</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="task-sidebar">
        <!-- Info Task -->
        <div class="section-card">
            <h3 class="section-title"><i class="fas fa-info-circle"></i> Informazioni</h3>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Progetto</span>
                    <span class="info-value">
                        <a href="progetti.php?id=<?php echo $task['progetto_id']; ?>" style="color: var(--accent);">
                            <?php echo h($task['progetto_nome']); ?>
                        </a>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Assegnato a</span>
                    <span class="info-value">
                        <?php echo $task['assegnato_nome'] ? h($task['assegnato_nome']) : '-'; ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Creato da</span>
                    <span class="info-value"><?php echo h($task['creato_nome']); ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label">Data Creazione</span>
                    <span class="info-value"><?php echo formatDate($task['created_at']); ?></span>
                </div>

                <?php if ($task['data_scadenza']): ?>
                    <div class="info-item">
                        <span class="info-label">Scadenza</span>
                        <span class="info-value"><?php echo formatDate($task['data_scadenza']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($task['data_completamento']): ?>
                    <div class="info-item">
                        <span class="info-label">Completato il</span>
                        <span class="info-value"><?php echo formatDateTime($task['data_completamento']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($task['tempo_stimato']): ?>
                    <div class="info-item">
                        <span class="info-label">Tempo Stimato</span>
                        <span class="info-value"><?php echo $task['tempo_stimato']; ?>h</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Checklist -->
        <?php if (!empty($checklist)):
            $completati = count(array_filter($checklist, fn($c) => $c['completato'] == 1));
            $totale = count($checklist);
            $percentuale = ($completati / $totale) * 100;
        ?>
            <div class="progress-section">
                <div class="progress-number"><?php echo round($percentuale); ?>%</div>
                <div class="progress-label">Checklist Completata</div>
                <div style="margin-top: 15px; font-size: 14px;">
                    <?php echo $completati; ?> di <?php echo $totale; ?> item completati
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes_footer.php'; ?>
