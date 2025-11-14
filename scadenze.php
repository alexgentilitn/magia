<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Gestione Scadenze';

// Gestione azioni rapide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $scadenza_id = $_POST['scadenza_id'] ?? null;

    if ($scadenza_id && $_POST['action'] === 'segna_pagato') {
        $stmt = $db->prepare("UPDATE scadenze SET stato = 'pagato', data_pagamento = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->bindValue(':id', $scadenza_id, SQLITE3_INTEGER);
        $stmt->execute();
        logActivity('scadenza_pagata', 'scadenza', $scadenza_id);
        $_SESSION['success_message'] = 'Scadenza segnata come pagata';
        header('Location: scadenze.php');
        exit;
    }

    if ($scadenza_id && $_POST['action'] === 'elimina') {
        $stmt = $db->prepare("DELETE FROM scadenze WHERE id = :id");
        $stmt->bindValue(':id', $scadenza_id, SQLITE3_INTEGER);
        $stmt->execute();
        logActivity('scadenza_eliminata', 'scadenza', $scadenza_id);
        $_SESSION['success_message'] = 'Scadenza eliminata';
        header('Location: scadenze.php');
        exit;
    }
}

// Filtri
$progetto_filter = $_GET['progetto'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';
$stato_filter = $_GET['stato'] ?? '';

$where = [];
$params = [];

if ($progetto_filter) {
    $where[] = "s.progetto_id = :progetto_id";
    $params[':progetto_id'] = $progetto_filter;
}

if ($tipo_filter) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $tipo_filter;
}

if ($stato_filter) {
    $where[] = "s.stato = :stato";
    $params[':stato'] = $stato_filter;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Lista scadenze
$query = "
    SELECT
        s.*,
        p.titolo as progetto_titolo,
        c.ragione_sociale as cliente_nome
    FROM scadenze s
    LEFT JOIN progetti p ON s.progetto_id = p.id
    LEFT JOIN clienti c ON p.cliente_id = c.id
    $where_sql
    ORDER BY s.data_scadenza ASC
";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}

$result = $stmt->execute();
$scadenze = [];
$oggi = date('Y-m-d');

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Calcola se scaduta
    $row['is_scaduta'] = ($row['stato'] === 'in_attesa' && $row['data_scadenza'] < $oggi);
    $row['is_prossima'] = ($row['stato'] === 'in_attesa' && $row['data_scadenza'] >= $oggi && $row['data_scadenza'] <= date('Y-m-d', strtotime('+7 days')));
    $scadenze[] = $row;
}

// Lista progetti per filtro
$progetti_result = $db->query("
    SELECT p.id, p.titolo, c.ragione_sociale
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    ORDER BY p.titolo ASC
");
$progetti_list = [];
while ($row = $progetti_result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_list[] = $row;
}

include 'includes_header.php';
?>

<style>
.filters {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filters select {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    min-width: 180px;
}

.scadenza-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.scadenza-card:hover {
    border-color: #3498db;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
}

.scadenza-card.scaduta {
    border-left: 5px solid #e74c3c;
    background: #fff5f5;
}

.scadenza-card.prossima {
    border-left: 5px solid #f39c12;
    background: #fffbf0;
}

.scadenza-card.pagata {
    border-left: 5px solid #27ae60;
    background: #f0fff4;
    opacity: 0.8;
}

.scadenza-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.scadenza-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.scadenza-project {
    color: #7f8c8d;
    font-size: 14px;
}

.scadenza-meta {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
    flex-wrap: wrap;
}

.scadenza-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #555;
    font-size: 14px;
}

.scadenza-meta-item i {
    color: #3498db;
    width: 20px;
}

.scadenza-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.badge-in_attesa { background: #f39c12; }
.badge-pagato { background: #27ae60; }
.badge-annullato { background: #95a5a6; }

.badge-pagamento { background: #3498db; }
.badge-consegna { background: #9b59b6; }
.badge-milestone { background: #1abc9c; }
.badge-altro { background: #95a5a6; }

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    color: #7f8c8d;
    font-size: 14px;
}

.stat-card.danger .stat-number { color: #e74c3c; }
.stat-card.warning .stat-number { color: #f39c12; }
.stat-card.success .stat-number { color: #27ae60; }
.stat-card.info .stat-number { color: #3498db; }

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
    }

    .filters select {
        width: 100%;
    }

    .scadenza-header {
        flex-direction: column;
    }

    .scadenza-actions {
        flex-direction: column;
    }
}
</style>

<?php if (isset($_SESSION['success_message'])): ?>
    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> <?php echo h($_SESSION['success_message']); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<!-- Statistiche Rapide -->
<?php
$scadute = array_filter($scadenze, fn($s) => $s['is_scaduta']);
$prossime = array_filter($scadenze, fn($s) => $s['is_prossima']);
$in_attesa = array_filter($scadenze, fn($s) => $s['stato'] === 'in_attesa');
$pagato = array_filter($scadenze, fn($s) => $s['stato'] === 'pagato');
$totale_in_attesa = array_sum(array_column($in_attesa, 'importo'));
?>

<div class="quick-stats">
    <div class="stat-card danger">
        <div class="stat-number"><?php echo count($scadute); ?></div>
        <div class="stat-label">Scadute</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-number"><?php echo count($prossime); ?></div>
        <div class="stat-label">Prossimi 7 giorni</div>
    </div>
    <div class="stat-card info">
        <div class="stat-number"><?php echo count($in_attesa); ?></div>
        <div class="stat-label">In Attesa</div>
    </div>
    <div class="stat-card success">
        <div class="stat-number"><?php echo formatMoney($totale_in_attesa); ?></div>
        <div class="stat-label">Da Incassare</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-calendar-check"></i> Scadenze (<?php echo count($scadenze); ?>)</h3>
        <div style="display: flex; gap: 10px;">
            <a href="scadenza_edit.php" class="btn"><i class="fas fa-plus"></i> Nuova Scadenza</a>
            <a href="export_scadenze.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Esporta Excel</a>
        </div>
    </div>

    <form method="GET" class="filters">
        <select name="progetto">
            <option value="">Tutti i progetti</option>
            <?php foreach ($progetti_list as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo $progetto_filter == $p['id'] ? 'selected' : ''; ?>>
                    <?php echo h($p['titolo']); ?> - <?php echo h($p['ragione_sociale']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tipo">
            <option value="">Tutti i tipi</option>
            <option value="pagamento" <?php echo $tipo_filter === 'pagamento' ? 'selected' : ''; ?>>Pagamento</option>
            <option value="consegna" <?php echo $tipo_filter === 'consegna' ? 'selected' : ''; ?>>Consegna</option>
            <option value="milestone" <?php echo $tipo_filter === 'milestone' ? 'selected' : ''; ?>>Milestone</option>
            <option value="altro" <?php echo $tipo_filter === 'altro' ? 'selected' : ''; ?>>Altro</option>
        </select>

        <select name="stato">
            <option value="">Tutti gli stati</option>
            <option value="in_attesa" <?php echo $stato_filter === 'in_attesa' ? 'selected' : ''; ?>>In Attesa</option>
            <option value="pagato" <?php echo $stato_filter === 'pagato' ? 'selected' : ''; ?>>Pagato</option>
            <option value="annullato" <?php echo $stato_filter === 'annullato' ? 'selected' : ''; ?>>Annullato</option>
        </select>

        <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtra</button>

        <?php if ($progetto_filter || $tipo_filter || $stato_filter): ?>
            <a href="scadenze.php" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        <?php endif; ?>
    </form>

    <?php if (empty($scadenze)): ?>
        <p style="text-align: center; padding: 40px; color: #999;">
            <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
            Nessuna scadenza trovata
        </p>
    <?php else: ?>
        <?php foreach ($scadenze as $s): ?>
            <div class="scadenza-card <?php
                if ($s['stato'] === 'pagato') echo 'pagata';
                elseif ($s['is_scaduta']) echo 'scaduta';
                elseif ($s['is_prossima']) echo 'prossima';
            ?>">
                <div class="scadenza-header">
                    <div>
                        <div class="scadenza-title"><?php echo h($s['descrizione']); ?></div>
                        <div class="scadenza-project">
                            <i class="fas fa-project-diagram"></i> <?php echo h($s['progetto_titolo']); ?>
                            - <i class="fas fa-building"></i> <?php echo h($s['cliente_nome']); ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: start;">
                        <span class="badge badge-<?php echo h($s['tipo']); ?>">
                            <?php echo ucfirst(h($s['tipo'])); ?>
                        </span>
                        <span class="badge badge-<?php echo h($s['stato']); ?>">
                            <?php echo $s['stato'] === 'in_attesa' ? 'In Attesa' : ucfirst(h($s['stato'])); ?>
                        </span>
                    </div>
                </div>

                <div class="scadenza-meta">
                    <div class="scadenza-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>
                            <strong>Scadenza:</strong>
                            <?php echo formatDate($s['data_scadenza']); ?>
                            <?php if ($s['is_scaduta']): ?>
                                <span style="color: #e74c3c; font-weight: 600; margin-left: 5px;">SCADUTA</span>
                            <?php elseif ($s['is_prossima']): ?>
                                <span style="color: #f39c12; font-weight: 600; margin-left: 5px;">PROSSIMA</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if ($s['importo']): ?>
                        <div class="scadenza-meta-item">
                            <i class="fas fa-euro-sign"></i>
                            <span><strong>Importo:</strong> <?php echo formatMoney($s['importo']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($s['stato'] === 'pagato' && $s['data_pagamento']): ?>
                        <div class="scadenza-meta-item">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Pagato il:</strong> <?php echo formatDate($s['data_pagamento']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($s['note']): ?>
                    <p style="margin: 15px 0 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px; color: #555;">
                        <i class="fas fa-sticky-note"></i> <?php echo h($s['note']); ?>
                    </p>
                <?php endif; ?>

                <div class="scadenza-actions">
                    <a href="scadenza_edit.php?id=<?php echo $s['id']; ?>" class="btn" style="padding: 8px 15px;">
                        <i class="fas fa-edit"></i> Modifica
                    </a>

                    <?php if ($s['stato'] === 'in_attesa'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="scadenza_id" value="<?php echo $s['id']; ?>">
                            <input type="hidden" name="action" value="segna_pagato">
                            <button type="submit" class="btn" style="padding: 8px 15px; background: #27ae60;"
                                    onclick="return confirm('Segnare questa scadenza come pagata?')">
                                <i class="fas fa-check"></i> Segna Pagato
                            </button>
                        </form>
                    <?php endif; ?>

                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="scadenza_id" value="<?php echo $s['id']; ?>">
                        <input type="hidden" name="action" value="elimina">
                        <button type="submit" class="btn btn-secondary" style="padding: 8px 15px; background: #e74c3c;"
                                onclick="return confirm('Eliminare questa scadenza?')">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes_footer.php'; ?>
