<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Spese Progetto';

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$progetto_id = isset($_GET['progetto']) ? (int)$_GET['progetto'] : 0;
$message = '';

// Aggiungi spesa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi_spesa'])) {
    $stmt = $db->prepare("
        INSERT INTO progetto_spese (progetto_id, descrizione, categoria, importo, data_spesa, rimborsabile, fornitore, note, user_id)
        VALUES (:progetto, :desc, :cat, :importo, :data, :rimb, :forn, :note, :user)
    ");

    $stmt->bindValue(':progetto', (int)$_POST['progetto_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':desc', trim($_POST['descrizione']), SQLITE3_TEXT);
    $stmt->bindValue(':cat', $_POST['categoria'], SQLITE3_TEXT);
    $stmt->bindValue(':importo', (float)$_POST['importo'], SQLITE3_FLOAT);
    $stmt->bindValue(':data', $_POST['data_spesa'], SQLITE3_TEXT);
    $stmt->bindValue(':rimb', isset($_POST['rimborsabile']) ? 1 : 0, SQLITE3_INTEGER);
    $stmt->bindValue(':forn', trim($_POST['fornitore'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':note', trim($_POST['note'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':user', $currentUser['id'], SQLITE3_INTEGER);

    if ($stmt->execute()) {
        $message = "Spesa aggiunta!";
    }
}

// Carica progetti
$progetti = [];
$result = $db->query("SELECT id, titolo as nome FROM progetti ORDER BY titolo ASC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti[] = $row;
}

// Carica spese
$where = $progetto_id > 0 ? "WHERE s.progetto_id = $progetto_id" : "WHERE 1=1";

$query = "
    SELECT s.*, p.titolo as progetto_nome, u.nome as user_nome
    FROM progetto_spese s
    LEFT JOIN progetti p ON s.progetto_id = p.id
    LEFT JOIN users u ON s.user_id = u.id
    $where
    ORDER BY s.data_spesa DESC
";

$result = $db->query($query);
$spese = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $spese[] = $row;
}

$totale_spese = array_sum(array_column($spese, 'importo'));
$totale_rimborsabili = array_sum(array_column(array_filter($spese, fn($s) => $s['rimborsabile'] == 1), 'importo'));

include 'includes_header.php';
?>

<style>
.spese-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.spese-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.spesa-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 4px solid #0066cc;
}

.spesa-card.rimborsabile {
    border-left-color: #ffc107;
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
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; color: #155724; margin-bottom: 20px;">✓ <?php echo h($message); ?></div>
<?php endif; ?>

<div class="spese-header">
    <h1><i class="fas fa-receipt"></i> Spese Progetto</h1>
    <button class="btn" onclick="document.getElementById('modalSpesa').classList.add('active')">
        <i class="fas fa-plus"></i> Nuova Spesa
    </button>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div style="font-size: 32px; font-weight: 700; color: #dc3545;">€<?php echo number_format($totale_spese, 2, ',', '.'); ?></div>
        <div style="font-size: 14px; color: #666;">Totale Spese</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 32px; font-weight: 700; color: #ffc107;">€<?php echo number_format($totale_rimborsabili, 2, ',', '.'); ?></div>
        <div style="font-size: 14px; color: #666;">Da Rimborsare</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 32px; font-weight: 700; color: #666;"><?php echo count($spese); ?></div>
        <div style="font-size: 14px; color: #666;">Spese Totali</div>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <select onchange="window.location.href='progetto_spese.php?progetto=' + this.value" style="padding: 10px; border-radius: 6px; border: 2px solid #dee2e6;">
        <option value="0">Tutti i Progetti</option>
        <?php foreach ($progetti as $p): ?>
            <option value="<?php echo $p['id']; ?>" <?php echo $progetto_id == $p['id'] ? 'selected' : ''; ?>>
                <?php echo h($p['nome']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="spese-list">
    <?php foreach ($spese as $spesa): ?>
        <div class="spesa-card <?php echo $spesa['rimborsabile'] ? 'rimborsabile' : ''; ?>">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0 0 5px 0;"><?php echo h($spesa['descrizione']); ?></h3>
                    <div style="color: #666; font-size: 14px;">
                        <i class="fas fa-briefcase"></i> <?php echo h($spesa['progetto_nome']); ?>
                    </div>
                </div>

                <div style="font-size: 24px; font-weight: 700; color: #dc3545;">
                    €<?php echo number_format($spesa['importo'], 2, ',', '.'); ?>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 13px; color: #666;">
                <div>
                    <strong>Categoria:</strong> <?php echo h($spesa['categoria']); ?>
                </div>
                <div>
                    <strong>Data:</strong> <?php echo formatDate($spesa['data_spesa']); ?>
                </div>
                <?php if ($spesa['fornitore']): ?>
                    <div>
                        <strong>Fornitore:</strong> <?php echo h($spesa['fornitore']); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <strong>Inserita da:</strong> <?php echo h($spesa['user_nome']); ?>
                </div>
                <?php if ($spesa['rimborsabile']): ?>
                    <div>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #fff3cd; color: #856404;">
                            💳 Rimborsabile
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($spesa['note']): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; color: #666;">
                    <?php echo nl2br(h($spesa['note'])); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($spese)): ?>
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-receipt" style="font-size: 4em; opacity: 0.2; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; color: #666; margin: 0;">Nessuna spesa registrata</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Nuova Spesa -->
<div class="modal" id="modalSpesa">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;">Nuova Spesa</h2>
            <button class="btn btn-secondary" onclick="document.getElementById('modalSpesa').classList.remove('active')">✕</button>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Progetto *</label>
                <select name="progetto_id" required>
                    <option value="">-- Seleziona Progetto --</option>
                    <?php foreach ($progetti as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo $progetto_id == $p['id'] ? 'selected' : ''; ?>>
                            <?php echo h($p['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Descrizione *</label>
                <input type="text" name="descrizione" required placeholder="Es: Acquisto server">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="categoria">
                        <option value="hardware">Hardware</option>
                        <option value="software">Software</option>
                        <option value="trasferta">Trasferta</option>
                        <option value="materiali">Materiali</option>
                        <option value="consulenza">Consulenza</option>
                        <option value="altro">Altro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Importo (€) *</label>
                    <input type="number" name="importo" step="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Data Spesa *</label>
                    <input type="date" name="data_spesa" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Fornitore</label>
                    <input type="text" name="fornitore">
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="rimborsabile" value="1" style="width: auto;">
                    Rimborsabile
                </label>
            </div>

            <div class="form-group">
                <label>Note</label>
                <textarea name="note" rows="3"></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="aggiungi_spesa" class="btn btn-success">
                    <i class="fas fa-check"></i> Aggiungi Spesa
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalSpesa').classList.remove('active')">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('modalSpesa').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>

<?php include 'includes_footer.php'; ?>
