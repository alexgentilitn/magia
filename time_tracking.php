<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Time Tracking';

$errors = [];
$success = false;

// Gestione aggiunta ore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_time'])) {
    $progetto_id = $_POST['progetto_id'] ?? null;
    $descrizione = trim($_POST['descrizione'] ?? '');
    $ore = trim($_POST['ore'] ?? '');
    $data = $_POST['data'] ?? date('Y-m-d');
    $ora_inizio = $_POST['ora_inizio'] ?? null;
    $ora_fine = $_POST['ora_fine'] ?? null;
    $fatturabile = isset($_POST['fatturabile']) ? 1 : 0;
    $note = trim($_POST['note'] ?? '');

    if (empty($progetto_id)) {
        $errors[] = 'Seleziona un progetto';
    }

    if (empty($ore) || !is_numeric($ore)) {
        $errors[] = 'Inserisci un numero di ore valido';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO time_entries (progetto_id, user_id, descrizione, ore, data, ora_inizio, ora_fine, fatturabile, note)
                VALUES (:progetto_id, :user_id, :descrizione, :ore, :data, :ora_inizio, :ora_fine, :fatturabile, :note)
            ");

            $stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
            $stmt->bindValue(':ore', floatval($ore), SQLITE3_FLOAT);
            $stmt->bindValue(':data', $data, SQLITE3_TEXT);
            $stmt->bindValue(':ora_inizio', $ora_inizio, SQLITE3_TEXT);
            $stmt->bindValue(':ora_fine', $ora_fine, SQLITE3_TEXT);
            $stmt->bindValue(':fatturabile', $fatturabile, SQLITE3_INTEGER);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);

            $stmt->execute();

            logActivity('time_entry_added', 'time_entry', $db->lastInsertRowID());

            $success = 'Ore registrate con successo';

        } catch (Exception $e) {
            $errors[] = 'Errore nel salvataggio: ' . $e->getMessage();
        }
    }
}

// Filtri
$progetto_filter = $_GET['progetto'] ?? '';
$user_filter = $_GET['user'] ?? $_SESSION['user_id'];
$data_da = $_GET['data_da'] ?? date('Y-m-01'); // Primo giorno mese corrente
$data_a = $_GET['data_a'] ?? date('Y-m-t'); // Ultimo giorno mese corrente

$where = ['1=1'];
$params = [];

if ($progetto_filter) {
    $where[] = "t.progetto_id = :progetto_id";
    $params[':progetto_id'] = $progetto_filter;
}

if ($user_filter) {
    $where[] = "t.user_id = :user_id";
    $params[':user_id'] = $user_filter;
}

if ($data_da) {
    $where[] = "t.data >= :data_da";
    $params[':data_da'] = $data_da;
}

if ($data_a) {
    $where[] = "t.data <= :data_a";
    $params[':data_a'] = $data_a;
}

$where_sql = implode(' AND ', $where);

// Lista time entries
$query = "
    SELECT
        t.*,
        p.titolo as progetto_titolo,
        c.ragione_sociale as cliente_nome,
        u.nome || ' ' || COALESCE(u.cognome, '') as utente_nome
    FROM time_entries t
    LEFT JOIN progetti p ON t.progetto_id = p.id
    LEFT JOIN clienti c ON p.cliente_id = c.id
    LEFT JOIN users u ON t.user_id = u.id
    WHERE $where_sql
    ORDER BY t.data DESC, t.created_at DESC
";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}

$result = $stmt->execute();
$time_entries = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $time_entries[] = $row;
}

// Statistiche
$totale_ore = array_sum(array_column($time_entries, 'ore'));
$ore_fatturabili = array_sum(array_map(fn($t) => $t['fatturabile'] ? $t['ore'] : 0, $time_entries));
$ore_non_fatturabili = $totale_ore - $ore_fatturabili;

// Lista progetti per dropdown
$progetti_result = $db->query("
    SELECT p.id, p.titolo, c.ragione_sociale
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    WHERE p.stato IN ('in_corso', 'pianificazione')
    ORDER BY p.titolo ASC
");
$progetti_list = [];
while ($row = $progetti_result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_list[] = $row;
}

include 'includes_header.php';
?>

<style>
.time-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
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
    margin-bottom: 6px;
    color: var(--gray-800);
    font-size: 13px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    font-size: 14px;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

.time-entry-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    padding: 15px;
    margin-bottom: 12px;
    transition: all 0.2s;
}

.time-entry-card:hover {
    box-shadow: var(--box-shadow-lg);
    transform: translateY(-1px);
}

.time-entry-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 10px;
}

.time-entry-title {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 15px;
}

.time-entry-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--gray-600);
    margin-top: 8px;
}

.time-entry-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.filters {
    background: var(--gray-50);
    padding: 15px;
    border-radius: var(--border-radius);
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.filters input,
.filters select {
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
}

.stat-small {
    text-align: center;
    padding: 15px;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    border-left: 3px solid var(--accent);
}

.stat-small-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 5px;
}

.stat-small-label {
    font-size: 12px;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .time-grid {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($success): ?>
    <div style="background: #e8f5e9; border-left: 3px solid #27ae60; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px; color: #2e7d32;">
        <i class="fas fa-check-circle"></i> <?php echo h($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div style="background: #ffebee; border-left: 3px solid #e74c3c; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px; color: #c62828;">
        <strong>Errori:</strong>
        <ul style="margin: 10px 0 0 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="time-grid">
    <!-- Form Aggiunta Ore -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.2em; color: var(--gray-900);">
            <i class="fas fa-plus-circle"></i> Registra Ore
        </h3>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group form-full">
                    <label>Progetto *</label>
                    <select name="progetto_id" required>
                        <option value="">Seleziona progetto...</option>
                        <?php foreach ($progetti_list as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo h($p['titolo']); ?> - <?php echo h($p['ragione_sociale']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ore Lavorate *</label>
                    <input type="number" name="ore" step="0.25" min="0.25" required placeholder="es: 2.5">
                </div>

                <div class="form-group">
                    <label>Data *</label>
                    <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Ora Inizio</label>
                    <input type="time" name="ora_inizio">
                </div>

                <div class="form-group">
                    <label>Ora Fine</label>
                    <input type="time" name="ora_fine">
                </div>

                <div class="form-group form-full">
                    <label>Descrizione Attività</label>
                    <input type="text" name="descrizione" placeholder="es: Sviluppo frontend, Meeting cliente...">
                </div>

                <div class="form-group form-full">
                    <label>Note</label>
                    <textarea name="note" rows="3" placeholder="Note aggiuntive..."></textarea>
                </div>

                <div class="form-group form-full">
                    <div class="checkbox-group">
                        <input type="checkbox" name="fatturabile" id="fatturabile" checked>
                        <label for="fatturabile" style="margin: 0;">Ore fatturabili</label>
                    </div>
                </div>
            </div>

            <button type="submit" name="add_time" class="btn" style="width: 100%; margin-top: 15px;">
                <i class="fas fa-save"></i> Registra Ore
            </button>
        </form>
    </div>

    <!-- Statistiche Periodo -->
    <div>
        <div class="card">
            <h3 style="margin-bottom: 20px; font-size: 1.2em; color: var(--gray-900);">
                <i class="fas fa-chart-line"></i> Riepilogo Periodo
            </h3>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                <div class="stat-small">
                    <div class="stat-small-value"><?php echo number_format($totale_ore, 2); ?>h</div>
                    <div class="stat-small-label">Totale Ore</div>
                </div>
                <div class="stat-small" style="border-left-color: #27ae60;">
                    <div class="stat-small-value"><?php echo number_format($ore_fatturabili, 2); ?>h</div>
                    <div class="stat-small-label">Fatturabili</div>
                </div>
                <div class="stat-small" style="border-left-color: #95a5a6;">
                    <div class="stat-small-value"><?php echo number_format($ore_non_fatturabili, 2); ?>h</div>
                    <div class="stat-small-label">Non Fatt.</div>
                </div>
            </div>

            <form method="GET" class="filters">
                <input type="date" name="data_da" value="<?php echo h($data_da); ?>">
                <input type="date" name="data_a" value="<?php echo h($data_a); ?>">
                <button type="submit" class="btn" style="padding: 8px 16px; font-size: 13px;">
                    <i class="fas fa-filter"></i> Filtra
                </button>
                <?php if ($data_da != date('Y-m-01') || $data_a != date('Y-m-t')): ?>
                    <a href="time_tracking.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Lista Time Entries -->
        <div class="card" style="max-height: 600px; overflow-y: auto;">
            <h3 style="margin-bottom: 15px; font-size: 1.1em; color: var(--gray-900);">
                <i class="fas fa-clock"></i> Ore Registrate (<?php echo count($time_entries); ?>)
            </h3>

            <?php if (empty($time_entries)): ?>
                <p style="text-align: center; padding: 30px; color: var(--gray-500);">
                    Nessuna ora registrata nel periodo selezionato
                </p>
            <?php else: ?>
                <?php foreach ($time_entries as $entry): ?>
                    <div class="time-entry-card">
                        <div class="time-entry-header">
                            <div>
                                <div class="time-entry-title"><?php echo h($entry['progetto_titolo']); ?></div>
                                <div style="font-size: 12px; color: var(--gray-600);">
                                    <?php echo h($entry['cliente_nome']); ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 18px; font-weight: 700; color: var(--accent);">
                                    <?php echo number_format($entry['ore'], 2); ?>h
                                </div>
                                <?php if ($entry['fatturabile']): ?>
                                    <span class="badge badge-completato" style="font-size: 10px;">Fatturabile</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($entry['descrizione']): ?>
                            <div style="margin: 8px 0; color: var(--gray-800); font-size: 14px;">
                                <?php echo h($entry['descrizione']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="time-entry-meta">
                            <div class="time-entry-meta-item">
                                <i class="fas fa-calendar"></i>
                                <?php echo formatDate($entry['data']); ?>
                            </div>
                            <?php if ($entry['ora_inizio'] && $entry['ora_fine']): ?>
                                <div class="time-entry-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?php echo h($entry['ora_inizio']); ?> - <?php echo h($entry['ora_fine']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="time-entry-meta-item">
                                <i class="fas fa-user"></i>
                                <?php echo h($entry['utente_nome']); ?>
                            </div>
                        </div>

                        <?php if ($entry['note']): ?>
                            <div style="margin-top: 8px; padding: 8px; background: var(--gray-50); border-radius: 4px; font-size: 12px; color: var(--gray-700);">
                                <i class="fas fa-sticky-note"></i> <?php echo h($entry['note']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes_footer.php'; ?>
