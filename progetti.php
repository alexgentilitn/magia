<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Gestione Progetti';

// Filtri
$search = $_GET['search'] ?? '';
$stato_filter = $_GET['stato'] ?? '';
$cliente_filter = $_GET['cliente'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(p.titolo LIKE :search OR p.descrizione LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($stato_filter) {
    $where[] = "p.stato = :stato";
    $params[':stato'] = $stato_filter;
}

if ($cliente_filter) {
    $where[] = "p.cliente_id = :cliente_id";
    $params[':cliente_id'] = $cliente_filter;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Lista progetti con info cliente
$query = "
    SELECT
        p.*,
        c.ragione_sociale as cliente_nome,
        (SELECT COUNT(*) FROM scadenze WHERE progetto_id = p.id) as num_scadenze,
        (SELECT COUNT(*) FROM documenti WHERE progetto_id = p.id) as num_documenti
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    $where_sql
    ORDER BY p.data_inizio DESC
";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}

$result = $stmt->execute();
$progetti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti[] = $row;
}

// Lista clienti per filtro
$clienti_result = $db->query("SELECT id, ragione_sociale FROM clienti ORDER BY ragione_sociale ASC");
$clienti_list = [];
while ($row = $clienti_result->fetchArray(SQLITE3_ASSOC)) {
    $clienti_list[] = $row;
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

.filters input,
.filters select {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
}

.filters input {
    flex: 1;
    min-width: 200px;
}

.filters select {
    min-width: 150px;
}

.project-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.project-card:hover {
    border-color: #3498db;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
    transform: translateY(-2px);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.project-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.project-client {
    color: #7f8c8d;
    font-size: 14px;
}

.project-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.project-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #555;
    font-size: 14px;
}

.project-meta-item i {
    color: #3498db;
    width: 20px;
}

.progress-bar {
    background: #e9ecef;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
    margin-top: 5px;
}

.progress-fill {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 100%;
    transition: width 0.3s;
}

.badge-in_corso { background: #3498db; }
.badge-completato { background: #27ae60; }
.badge-sospeso { background: #f39c12; }
.badge-annullato { background: #95a5a6; }

.badge-alta { background: #e74c3c; }
.badge-media { background: #f39c12; }
.badge-bassa { background: #95a5a6; }

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
    }

    .filters input,
    .filters select {
        width: 100%;
    }

    .project-header {
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

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-project-diagram"></i> Progetti (<?php echo count($progetti); ?>)</h3>
        <div style="display: flex; gap: 10px;">
            <a href="progetto_edit.php" class="btn"><i class="fas fa-plus"></i> Nuovo Progetto</a>
            <a href="export_progetti.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Esporta Excel</a>
        </div>
    </div>

    <form method="GET" class="filters">
        <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cerca progetto...">

        <select name="cliente">
            <option value="">Tutti i clienti</option>
            <?php foreach ($clienti_list as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $cliente_filter == $c['id'] ? 'selected' : ''; ?>>
                    <?php echo h($c['ragione_sociale']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="stato">
            <option value="">Tutti gli stati</option>
            <option value="pianificazione" <?php echo $stato_filter === 'pianificazione' ? 'selected' : ''; ?>>Pianificazione</option>
            <option value="in_corso" <?php echo $stato_filter === 'in_corso' ? 'selected' : ''; ?>>In Corso</option>
            <option value="completato" <?php echo $stato_filter === 'completato' ? 'selected' : ''; ?>>Completato</option>
            <option value="sospeso" <?php echo $stato_filter === 'sospeso' ? 'selected' : ''; ?>>Sospeso</option>
            <option value="annullato" <?php echo $stato_filter === 'annullato' ? 'selected' : ''; ?>>Annullato</option>
        </select>

        <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtra</button>

        <?php if ($search || $stato_filter || $cliente_filter): ?>
            <a href="progetti.php" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        <?php endif; ?>
    </form>

    <?php if (empty($progetti)): ?>
        <p style="text-align: center; padding: 40px; color: #999;">
            <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
            Nessun progetto trovato
        </p>
    <?php else: ?>
        <?php foreach ($progetti as $p): ?>
            <div class="project-card">
                <div class="project-header">
                    <div>
                        <div class="project-title"><?php echo h($p['titolo']); ?></div>
                        <div class="project-client">
                            <i class="fas fa-building"></i> <?php echo h($p['cliente_nome'] ?? 'Nessun cliente'); ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: start;">
                        <span class="badge badge-<?php echo h($p['stato']); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', h($p['stato']))); ?>
                        </span>
                        <span class="badge badge-<?php echo h($p['priorita']); ?>">
                            Priorità <?php echo ucfirst(h($p['priorita'])); ?>
                        </span>
                    </div>
                </div>

                <?php if ($p['descrizione']): ?>
                    <p style="color: #555; margin: 10px 0; line-height: 1.6;">
                        <?php echo h(mb_substr($p['descrizione'], 0, 200)); ?>
                        <?php if (mb_strlen($p['descrizione']) > 200): ?>...<?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if ($p['percentuale_completamento'] !== null): ?>
                    <div style="margin: 15px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-size: 13px; color: #7f8c8d;">Avanzamento</span>
                            <span style="font-size: 13px; font-weight: 600; color: #2c3e50;"><?php echo h($p['percentuale_completamento']); ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo h($p['percentuale_completamento']); ?>%;"></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="project-meta">
                    <div class="project-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><strong>Inizio:</strong> <?php echo formatDate($p['data_inizio']); ?></span>
                    </div>

                    <?php if ($p['data_consegna']): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-flag-checkered"></i>
                            <span><strong>Consegna:</strong> <?php echo formatDate($p['data_consegna']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($p['importo']): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-euro-sign"></i>
                            <span><strong>Importo:</strong> <?php echo formatMoney($p['importo']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="project-meta-item">
                        <i class="fas fa-clock"></i>
                        <span><strong>Ore:</strong> <?php echo h($p['ore_effettive'] ?? 0); ?> / <?php echo h($p['ore_stimate'] ?? '-'); ?></span>
                    </div>

                    <div class="project-meta-item">
                        <i class="fas fa-bell"></i>
                        <span><?php echo h($p['num_scadenze']); ?> scadenze</span>
                    </div>

                    <div class="project-meta-item">
                        <i class="fas fa-file-alt"></i>
                        <span><?php echo h($p['num_documenti']); ?> documenti</span>
                    </div>
                </div>

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef; display: flex; gap: 10px;">
                    <a href="progetto_edit.php?id=<?php echo $p['id']; ?>" class="btn" style="padding: 8px 15px;">
                        <i class="fas fa-edit"></i> Modifica
                    </a>
                    <a href="scadenze.php?progetto=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 8px 15px;">
                        <i class="fas fa-calendar-check"></i> Scadenze
                    </a>
                    <a href="documenti.php?progetto=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 8px 15px;">
                        <i class="fas fa-folder-open"></i> Documenti
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes_footer.php'; ?>
