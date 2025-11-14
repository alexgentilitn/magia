<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Task Management';

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$message = '';
$error = '';

// Filtra per progetto
$progetto_filter = isset($_GET['progetto']) ? (int)$_GET['progetto'] : null;
$stato_filter = isset($_GET['stato']) ? $_GET['stato'] : null;
$assegnato_filter = isset($_GET['assegnato']) ? (int)$_GET['assegnato'] : null;
$priorita_filter = isset($_GET['priorita']) ? $_GET['priorita'] : null;
$view = isset($_GET['view']) ? $_GET['view'] : 'kanban';

// Crea nuovo task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crea_task'])) {
    $progetto_id = (int)$_POST['progetto_id'];
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['descrizione'] ?? '');
    $assegnato_a = !empty($_POST['assegnato_a']) ? (int)$_POST['assegnato_a'] : null;
    $priorita = $_POST['priorita'] ?? 'media';
    $stato = $_POST['stato'] ?? 'da_fare';
    $data_scadenza = !empty($_POST['data_scadenza']) ? $_POST['data_scadenza'] : null;
    $tempo_stimato = !empty($_POST['tempo_stimato']) ? (int)$_POST['tempo_stimato'] : null;

    if (empty($titolo) || $progetto_id == 0) {
        $error = "Titolo e progetto sono obbligatori";
    } else {
        $stmt = $db->prepare("
            INSERT INTO tasks (progetto_id, titolo, descrizione, assegnato_a, creato_da, priorita, stato, data_scadenza, tempo_stimato)
            VALUES (:progetto_id, :titolo, :descrizione, :assegnato_a, :creato_da, :priorita, :stato, :data_scadenza, :tempo_stimato)
        ");

        $stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
        $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
        $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
        $stmt->bindValue(':assegnato_a', $assegnato_a, SQLITE3_INTEGER);
        $stmt->bindValue(':creato_da', $currentUser['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':priorita', $priorita, SQLITE3_TEXT);
        $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);
        $stmt->bindValue(':data_scadenza', $data_scadenza, SQLITE3_TEXT);
        $stmt->bindValue(':tempo_stimato', $tempo_stimato, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $task_id = $db->lastInsertRowID();

            // Crea notifica se assegnato
            if ($assegnato_a && $assegnato_a != $currentUser['id']) {
                $stmt_notif = $db->prepare("
                    INSERT INTO notifications (user_id, tipo, titolo, messaggio, link)
                    VALUES (:user_id, 'task_assegnato', :titolo, :messaggio, :link)
                ");
                $stmt_notif->bindValue(':user_id', $assegnato_a, SQLITE3_INTEGER);
                $stmt_notif->bindValue(':titolo', 'Nuovo Task Assegnato', SQLITE3_TEXT);
                $stmt_notif->bindValue(':messaggio', "Ti è stato assegnato il task: $titolo", SQLITE3_TEXT);
                $stmt_notif->bindValue(':link', "task_detail.php?id=$task_id", SQLITE3_TEXT);
                $stmt_notif->execute();
            }

            $message = "Task creato con successo!";
            $_POST = [];
        } else {
            $error = "Errore nella creazione del task";
        }
    }
}

// Aggiorna stato task (per Kanban drag & drop)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stato'])) {
    $task_id = (int)$_POST['task_id'];
    $nuovo_stato = $_POST['nuovo_stato'];

    $stmt = $db->prepare("UPDATE tasks SET stato = :stato, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
    $stmt->bindValue(':stato', $nuovo_stato, SQLITE3_TEXT);
    $stmt->bindValue(':id', $task_id, SQLITE3_INTEGER);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}

// Carica progetti per dropdown
$progetti_result = $db->query("SELECT id, titolo as nome FROM progetti WHERE stato = 'in_corso' ORDER BY titolo ASC");
$progetti = [];
while ($row = $progetti_result->fetchArray(SQLITE3_ASSOC)) {
    $progetti[] = $row;
}

// Carica utenti per assegnazione
$users_result = $db->query("SELECT id, nome, email FROM users ORDER BY nome ASC");
$users = [];
while ($row = $users_result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

// Query tasks con filtri
$where_clauses = ["1=1"];
$where_params = [];

if ($progetto_filter) {
    $where_clauses[] = "t.progetto_id = :progetto_filter";
}
if ($stato_filter) {
    $where_clauses[] = "t.stato = :stato_filter";
}
if ($assegnato_filter) {
    $where_clauses[] = "t.assegnato_a = :assegnato_filter";
}
if ($priorita_filter) {
    $where_clauses[] = "t.priorita = :priorita_filter";
}

$where_sql = implode(' AND ', $where_clauses);

$query = "
    SELECT t.*,
           p.titolo as progetto_nome,
           u_ass.nome as assegnato_nome,
           u_cre.nome as creato_nome,
           (SELECT COUNT(*) FROM task_checklist WHERE task_id = t.id) as checklist_totale,
           (SELECT COUNT(*) FROM task_checklist WHERE task_id = t.id AND completato = 1) as checklist_completate,
           (SELECT COUNT(*) FROM task_comments WHERE task_id = t.id) as commenti_count
    FROM tasks t
    LEFT JOIN progetti p ON t.progetto_id = p.id
    LEFT JOIN users u_ass ON t.assegnato_a = u_ass.id
    LEFT JOIN users u_cre ON t.creato_da = u_cre.id
    WHERE $where_sql
    ORDER BY
        CASE t.priorita
            WHEN 'urgente' THEN 1
            WHEN 'alta' THEN 2
            WHEN 'media' THEN 3
            WHEN 'bassa' THEN 4
        END,
        t.data_scadenza ASC NULLS LAST,
        t.created_at DESC
";

$stmt = $db->prepare($query);
if ($progetto_filter) $stmt->bindValue(':progetto_filter', $progetto_filter, SQLITE3_INTEGER);
if ($stato_filter) $stmt->bindValue(':stato_filter', $stato_filter, SQLITE3_TEXT);
if ($assegnato_filter) $stmt->bindValue(':assegnato_filter', $assegnato_filter, SQLITE3_INTEGER);
if ($priorita_filter) $stmt->bindValue(':priorita_filter', $priorita_filter, SQLITE3_TEXT);

$result = $stmt->execute();
$tasks = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $tasks[] = $row;
}

// Organizza per stato (per Kanban)
$tasks_by_stato = [
    'da_fare' => [],
    'in_corso' => [],
    'in_revisione' => [],
    'completato' => []
];

foreach ($tasks as $task) {
    $tasks_by_stato[$task['stato']][] = $task;
}

include 'includes_header.php';
?>

<style>
.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.view-switcher {
    display: flex;
    gap: 10px;
    background: #f8f9fa;
    padding: 5px;
    border-radius: 8px;
}

.view-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    color: #666;
}

.view-btn:hover {
    background: #e9ecef;
}

.view-btn.active {
    background: white;
    color: var(--accent);
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filters-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.filters-bar select {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
}

/* Kanban Board */
.kanban-board {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.kanban-column {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    min-height: 500px;
}

.kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.kanban-title {
    font-weight: 700;
    font-size: 16px;
    color: #333;
}

.kanban-count {
    background: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #666;
}

.task-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid #dee2e6;
}

.task-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.task-card.priorita-urgente {
    border-left-color: #dc3545;
}

.task-card.priorita-alta {
    border-left-color: #fd7e14;
}

.task-card.priorita-media {
    border-left-color: #ffc107;
}

.task-card.priorita-bassa {
    border-left-color: #6c757d;
}

.task-title {
    font-weight: 600;
    font-size: 15px;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.4;
}

.task-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 12px;
    color: #666;
    margin-top: 10px;
}

.task-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.task-meta i {
    width: 14px;
}

.task-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.badge-priorita {
    background: #ffc107;
    color: #000;
}

.badge-urgente {
    background: #dc3545;
    color: white;
}

.badge-alta {
    background: #fd7e14;
    color: white;
}

.badge-scadenza {
    background: #28a745;
    color: white;
}

.badge-scadenza.scaduto {
    background: #dc3545;
}

.badge-scadenza.urgente {
    background: #ffc107;
    color: #000;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-fill {
    height: 100%;
    background: #28a745;
    transition: width 0.3s;
}

/* Lista Task */
.tasks-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.task-row {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 4px solid #dee2e6;
    transition: all 0.2s;
    cursor: pointer;
}

.task-row:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.task-row-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.task-row-title {
    font-weight: 600;
    font-size: 17px;
    color: #333;
    flex: 1;
}

.task-row-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    font-size: 13px;
    color: #666;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
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
    min-height: 100px;
    font-family: inherit;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

@media (max-width: 1200px) {
    .kanban-board {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }

    .task-header {
        flex-direction: column;
        gap: 15px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        ✓ <?php echo h($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        ✗ <?php echo h($error); ?>
    </div>
<?php endif; ?>

<div class="task-header">
    <h1 style="margin: 0;"><i class="fas fa-tasks"></i> Task Management</h1>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div class="view-switcher">
            <button class="view-btn <?php echo $view === 'kanban' ? 'active' : ''; ?>" onclick="cambiaVista('kanban')">
                <i class="fas fa-columns"></i> Kanban
            </button>
            <button class="view-btn <?php echo $view === 'lista' ? 'active' : ''; ?>" onclick="cambiaVista('lista')">
                <i class="fas fa-list"></i> Lista
            </button>
        </div>

        <button class="btn" onclick="document.getElementById('modalCreaTask').classList.add('active')">
            <i class="fas fa-plus"></i> Nuovo Task
        </button>
    </div>
</div>

<!-- Filtri -->
<div class="filters-bar">
    <select onchange="applicaFiltro('progetto', this.value)">
        <option value="">Tutti i Progetti</option>
        <?php foreach ($progetti as $p): ?>
            <option value="<?php echo $p['id']; ?>" <?php echo $progetto_filter == $p['id'] ? 'selected' : ''; ?>>
                <?php echo h($p['nome']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select onchange="applicaFiltro('assegnato', this.value)">
        <option value="">Tutti gli Utenti</option>
        <?php foreach ($users as $u): ?>
            <option value="<?php echo $u['id']; ?>" <?php echo $assegnato_filter == $u['id'] ? 'selected' : ''; ?>>
                <?php echo h($u['nome']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select onchange="applicaFiltro('priorita', this.value)">
        <option value="">Tutte le Priorità</option>
        <option value="urgente" <?php echo $priorita_filter === 'urgente' ? 'selected' : ''; ?>>🔴 Urgente</option>
        <option value="alta" <?php echo $priorita_filter === 'alta' ? 'selected' : ''; ?>>🟠 Alta</option>
        <option value="media" <?php echo $priorita_filter === 'media' ? 'selected' : ''; ?>>🟡 Media</option>
        <option value="bassa" <?php echo $priorita_filter === 'bassa' ? 'selected' : ''; ?>>⚪ Bassa</option>
    </select>

    <?php if ($progetto_filter || $stato_filter || $assegnato_filter || $priorita_filter): ?>
        <button class="btn btn-secondary" onclick="window.location.href='tasks.php?view=<?php echo $view; ?>'">
            <i class="fas fa-times"></i> Reset Filtri
        </button>
    <?php endif; ?>
</div>

<!-- Vista Kanban -->
<?php if ($view === 'kanban'): ?>
    <div class="kanban-board">
        <?php
        $stati = [
            'da_fare' => ['label' => '📋 Da Fare', 'color' => '#6c757d'],
            'in_corso' => ['label' => '🚀 In Corso', 'color' => '#0066cc'],
            'in_revisione' => ['label' => '👀 In Revisione', 'color' => '#ffc107'],
            'completato' => ['label' => '✅ Completato', 'color' => '#28a745']
        ];

        foreach ($stati as $stato_key => $stato_info):
            $tasks_stato = $tasks_by_stato[$stato_key];
        ?>
            <div class="kanban-column">
                <div class="kanban-header">
                    <div class="kanban-title" style="color: <?php echo $stato_info['color']; ?>;">
                        <?php echo $stato_info['label']; ?>
                    </div>
                    <div class="kanban-count"><?php echo count($tasks_stato); ?></div>
                </div>

                <?php foreach ($tasks_stato as $task): ?>
                    <div class="task-card priorita-<?php echo $task['priorita']; ?>" onclick="apriTask(<?php echo $task['id']; ?>)">
                        <div class="task-title"><?php echo h($task['titolo']); ?></div>

                        <div class="task-meta">
                            <div class="task-meta-row">
                                <i class="fas fa-briefcase"></i>
                                <span><?php echo h($task['progetto_nome']); ?></span>
                            </div>

                            <?php if ($task['assegnato_nome']): ?>
                                <div class="task-meta-row">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo h($task['assegnato_nome']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($task['data_scadenza']):
                                $scadenza_date = new DateTime($task['data_scadenza']);
                                $oggi = new DateTime();
                                $diff = $oggi->diff($scadenza_date);
                                $giorni_diff = (int)$diff->format('%R%a');

                                $scadenza_class = '';
                                if ($giorni_diff < 0) $scadenza_class = 'scaduto';
                                elseif ($giorni_diff <= 3) $scadenza_class = 'urgente';
                            ?>
                                <div class="task-meta-row">
                                    <i class="fas fa-calendar"></i>
                                    <span class="badge-scadenza <?php echo $scadenza_class; ?>" style="padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                                        <?php echo formatDate($task['data_scadenza']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($task['checklist_totale'] > 0): ?>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo ($task['checklist_completate'] / $task['checklist_totale']) * 100; ?>%"></div>
                            </div>
                            <div style="font-size: 11px; color: #666; margin-top: 4px;">
                                <?php echo $task['checklist_completate']; ?>/<?php echo $task['checklist_totale']; ?> completati
                            </div>
                        <?php endif; ?>

                        <div class="task-badges">
                            <?php if ($task['priorita'] === 'urgente'): ?>
                                <span class="badge badge-urgente">🔴 URGENTE</span>
                            <?php elseif ($task['priorita'] === 'alta'): ?>
                                <span class="badge badge-alta">🟠 Alta</span>
                            <?php endif; ?>

                            <?php if ($task['commenti_count'] > 0): ?>
                                <span class="badge" style="background: #e9ecef; color: #666;">
                                    <i class="fas fa-comment"></i> <?php echo $task['commenti_count']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($tasks_stato)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: #adb5bd;">
                        <i class="fas fa-inbox" style="font-size: 2em; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p style="margin: 0;">Nessun task</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <!-- Vista Lista -->
    <div class="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <div class="task-row priorita-<?php echo $task['priorita']; ?>" onclick="apriTask(<?php echo $task['id']; ?>)">
                <div class="task-row-header">
                    <div class="task-row-title"><?php echo h($task['titolo']); ?></div>
                    <div class="task-badges">
                        <?php
                        $stati_labels = [
                            'da_fare' => '📋 Da Fare',
                            'in_corso' => '🚀 In Corso',
                            'in_revisione' => '👀 In Revisione',
                            'completato' => '✅ Completato'
                        ];
                        ?>
                        <span class="badge" style="background: #e9ecef; color: #666;">
                            <?php echo $stati_labels[$task['stato']]; ?>
                        </span>
                    </div>
                </div>

                <?php if ($task['descrizione']): ?>
                    <div style="color: #666; font-size: 14px; margin-bottom: 12px;">
                        <?php echo nl2br(h(substr($task['descrizione'], 0, 150))); ?>
                        <?php echo strlen($task['descrizione']) > 150 ? '...' : ''; ?>
                    </div>
                <?php endif; ?>

                <div class="task-row-meta">
                    <div>
                        <strong>Progetto:</strong> <?php echo h($task['progetto_nome']); ?>
                    </div>
                    <div>
                        <strong>Assegnato a:</strong> <?php echo $task['assegnato_nome'] ? h($task['assegnato_nome']) : '-'; ?>
                    </div>
                    <div>
                        <strong>Priorità:</strong>
                        <?php
                        $priorita_icons = [
                            'urgente' => '🔴 Urgente',
                            'alta' => '🟠 Alta',
                            'media' => '🟡 Media',
                            'bassa' => '⚪ Bassa'
                        ];
                        echo $priorita_icons[$task['priorita']];
                        ?>
                    </div>
                    <div>
                        <strong>Scadenza:</strong> <?php echo $task['data_scadenza'] ? formatDate($task['data_scadenza']) : '-'; ?>
                    </div>
                    <?php if ($task['checklist_totale'] > 0): ?>
                        <div>
                            <strong>Checklist:</strong> <?php echo $task['checklist_completate']; ?>/<?php echo $task['checklist_totale']; ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <strong>Commenti:</strong> <?php echo $task['commenti_count']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($tasks)): ?>
            <div class="card" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-tasks" style="font-size: 4em; opacity: 0.2; margin-bottom: 20px;"></i>
                <p style="font-size: 18px; color: #666; margin: 0;">Nessun task trovato</p>
                <button class="btn" style="margin-top: 20px;" onclick="document.getElementById('modalCreaTask').classList.add('active')">
                    <i class="fas fa-plus"></i> Crea il Primo Task
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Modal Crea Task -->
<div class="modal" id="modalCreaTask">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;"><i class="fas fa-plus-circle"></i> Nuovo Task</h2>
            <button class="btn btn-secondary" onclick="document.getElementById('modalCreaTask').classList.remove('active')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Progetto *</label>
                <select name="progetto_id" required>
                    <option value="">-- Seleziona Progetto --</option>
                    <?php foreach ($progetti as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo h($p['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Titolo Task *</label>
                <input type="text" name="titolo" required placeholder="Es: Implementare login utenti">
            </div>

            <div class="form-group">
                <label>Descrizione</label>
                <textarea name="descrizione" placeholder="Descrizione dettagliata del task..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Assegna a</label>
                    <select name="assegnato_a">
                        <option value="">-- Non assegnato --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo h($u['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priorità</label>
                    <select name="priorita">
                        <option value="bassa">⚪ Bassa</option>
                        <option value="media" selected>🟡 Media</option>
                        <option value="alta">🟠 Alta</option>
                        <option value="urgente">🔴 Urgente</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Stato</label>
                    <select name="stato">
                        <option value="da_fare" selected>📋 Da Fare</option>
                        <option value="in_corso">🚀 In Corso</option>
                        <option value="in_revisione">👀 In Revisione</option>
                        <option value="completato">✅ Completato</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Data Scadenza</label>
                    <input type="date" name="data_scadenza">
                </div>
            </div>

            <div class="form-group">
                <label>Tempo Stimato (ore)</label>
                <input type="number" name="tempo_stimato" min="1" placeholder="Es: 8">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" name="crea_task" class="btn btn-success">
                    <i class="fas fa-check"></i> Crea Task
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalCreaTask').classList.remove('active')">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cambiaVista(vista) {
    const url = new URL(window.location);
    url.searchParams.set('view', vista);
    window.location.href = url.toString();
}

function applicaFiltro(tipo, valore) {
    const url = new URL(window.location);
    if (valore) {
        url.searchParams.set(tipo, valore);
    } else {
        url.searchParams.delete(tipo);
    }
    window.location.href = url.toString();
}

function apriTask(taskId) {
    window.location.href = 'task_detail.php?id=' + taskId;
}

// Chiudi modal cliccando fuori
document.getElementById('modalCreaTask').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});
</script>

<?php include 'includes_footer.php'; ?>
