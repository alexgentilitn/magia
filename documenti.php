<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Gestione Documenti';

// Gestione azioni rapide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $documento_id = $_POST['documento_id'] ?? null;

    if ($documento_id && $_POST['action'] === 'elimina') {
        // Carica info documento
        $stmt = $db->prepare("SELECT * FROM documenti WHERE id = :id");
        $stmt->bindValue(':id', $documento_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $doc = $result->fetchArray(SQLITE3_ASSOC);

        if ($doc) {
            // Elimina file fisico
            $file_path = __DIR__ . '/' . $doc['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Elimina record database
            $stmt = $db->prepare("DELETE FROM documenti WHERE id = :id");
            $stmt->bindValue(':id', $documento_id, SQLITE3_INTEGER);
            $stmt->execute();

            logActivity('documento_eliminato', 'documento', $documento_id, $doc['titolo']);
            $_SESSION['success_message'] = 'Documento eliminato con successo';
        }
        header('Location: documenti.php');
        exit;
    }
}

// Filtri
$progetto_filter = $_GET['progetto'] ?? '';
$cliente_filter = $_GET['cliente'] ?? '';
$tipo_filter = $_GET['tipo'] ?? '';

$where = [];
$params = [];

if ($progetto_filter === 'generic') {
    // Documenti generici (senza progetto)
    $where[] = "d.progetto_id IS NULL";
} elseif ($progetto_filter) {
    // Progetto specifico
    $where[] = "d.progetto_id = :progetto_id";
    $params[':progetto_id'] = $progetto_filter;
}

if ($cliente_filter) {
    $where[] = "d.cliente_id = :cliente_id";
    $params[':cliente_id'] = $cliente_filter;
}

if ($tipo_filter) {
    $where[] = "d.tipo = :tipo";
    $params[':tipo'] = $tipo_filter;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Lista documenti
$query = "
    SELECT
        d.*,
        p.titolo as progetto_titolo,
        c.ragione_sociale as cliente_nome
    FROM documenti d
    LEFT JOIN progetti p ON d.progetto_id = p.id
    LEFT JOIN clienti c ON d.cliente_id = c.id
    $where_sql
    ORDER BY d.created_at DESC
";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}

$result = $stmt->execute();
$documenti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $documenti[] = $row;
}

// Lista progetti per filtro
$progetti_result = $db->query("SELECT id, titolo FROM progetti ORDER BY titolo ASC");
$progetti_list = [];
while ($row = $progetti_result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_list[] = $row;
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

.filters select {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    min-width: 180px;
}

.document-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
    display: flex;
    gap: 20px;
    align-items: start;
}

.document-card:hover {
    border-color: #3498db;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
}

.document-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.icon-pdf { background: #e74c3c; color: white; }
.icon-doc { background: #2980b9; color: white; }
.icon-xls { background: #27ae60; color: white; }
.icon-img { background: #9b59b6; color: white; }
.icon-zip { background: #f39c12; color: white; }
.icon-other { background: #95a5a6; color: white; }

.document-content {
    flex: 1;
}

.document-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.document-meta {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 10px;
}

.document-info {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.document-info-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #555;
}

.document-actions {
    display: flex;
    gap: 10px;
    flex-direction: column;
}

.badge-fattura { background: #e74c3c; }
.badge-contratto { background: #2980b9; }
.badge-preventivo { background: #f39c12; }
.badge-documento { background: #95a5a6; }
.badge-altro { background: #7f8c8d; }

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
    }

    .filters select {
        width: 100%;
    }

    .document-card {
        flex-direction: column;
    }

    .document-actions {
        flex-direction: row;
        width: 100%;
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
        <h3><i class="fas fa-folder-open"></i> Documenti (<?php echo count($documenti); ?>)</h3>
        <a href="documento_edit.php" class="btn"><i class="fas fa-plus"></i> Carica Documento</a>
    </div>

    <form method="GET" class="filters">
        <select name="progetto">
            <option value="">Tutti i progetti</option>
            <option value="generic" <?php echo $progetto_filter === 'generic' ? 'selected' : ''; ?>>
                📁 Documenti Generici (senza progetto)
            </option>
            <?php foreach ($progetti_list as $p): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo $progetto_filter == $p['id'] ? 'selected' : ''; ?>>
                    <?php echo h($p['titolo']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="cliente">
            <option value="">Tutti i clienti</option>
            <?php foreach ($clienti_list as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $cliente_filter == $c['id'] ? 'selected' : ''; ?>>
                    <?php echo h($c['ragione_sociale']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tipo">
            <option value="">Tutti i tipi</option>
            <option value="fattura" <?php echo $tipo_filter === 'fattura' ? 'selected' : ''; ?>>Fattura</option>
            <option value="contratto" <?php echo $tipo_filter === 'contratto' ? 'selected' : ''; ?>>Contratto</option>
            <option value="preventivo" <?php echo $tipo_filter === 'preventivo' ? 'selected' : ''; ?>>Preventivo</option>
            <option value="documento" <?php echo $tipo_filter === 'documento' ? 'selected' : ''; ?>>Documento</option>
            <option value="altro" <?php echo $tipo_filter === 'altro' ? 'selected' : ''; ?>>Altro</option>
        </select>

        <button type="submit" class="btn"><i class="fas fa-filter"></i> Filtra</button>

        <?php if ($progetto_filter || $cliente_filter || $tipo_filter): ?>
            <a href="documenti.php" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        <?php endif; ?>
    </form>

    <?php if (empty($documenti)): ?>
        <p style="text-align: center; padding: 40px; color: #999;">
            <i class="fas fa-file" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
            Nessun documento caricato
        </p>
    <?php else: ?>
        <?php foreach ($documenti as $d): ?>
            <?php
            // Determina icona file
            $ext = strtolower(pathinfo($d['file_path'], PATHINFO_EXTENSION));
            $icon_class = 'icon-other';
            $icon = 'fa-file';

            if ($ext === 'pdf') { $icon_class = 'icon-pdf'; $icon = 'fa-file-pdf'; }
            elseif (in_array($ext, ['doc', 'docx'])) { $icon_class = 'icon-doc'; $icon = 'fa-file-word'; }
            elseif (in_array($ext, ['xls', 'xlsx'])) { $icon_class = 'icon-xls'; $icon = 'fa-file-excel'; }
            elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) { $icon_class = 'icon-img'; $icon = 'fa-file-image'; }
            elseif (in_array($ext, ['zip', 'rar'])) { $icon_class = 'icon-zip'; $icon = 'fa-file-archive'; }
            ?>

            <div class="document-card">
                <div class="document-icon <?php echo $icon_class; ?>">
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>

                <div class="document-content">
                    <div class="document-title"><?php echo h($d['titolo']); ?></div>

                    <div class="document-meta">
                        <?php if ($d['progetto_titolo']): ?>
                            <i class="fas fa-project-diagram"></i> <?php echo h($d['progetto_titolo']); ?>
                        <?php endif; ?>
                        <?php if ($d['cliente_nome']): ?>
                            - <i class="fas fa-building"></i> <?php echo h($d['cliente_nome']); ?>
                        <?php endif; ?>
                    </div>

                    <span class="badge badge-<?php echo h($d['tipo']); ?>">
                        <?php echo ucfirst(h($d['tipo'])); ?>
                    </span>

                    <div class="document-info">
                        <div class="document-info-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo formatDateTime($d['created_at']); ?></span>
                        </div>
                        <div class="document-info-item">
                            <i class="fas fa-hdd"></i>
                            <span><?php echo formatFileSize($d['file_size']); ?></span>
                        </div>
                        <div class="document-info-item">
                            <i class="fas fa-file-alt"></i>
                            <span><?php echo strtoupper($ext); ?></span>
                        </div>
                    </div>

                    <?php if ($d['note']): ?>
                        <p style="margin: 10px 0 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 13px; color: #555;">
                            <i class="fas fa-sticky-note"></i> <?php echo h($d['note']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="document-actions">
                    <a href="download.php?id=<?php echo $d['id']; ?>" class="btn" style="padding: 10px 15px; white-space: nowrap;">
                        <i class="fas fa-download"></i> Download
                    </a>

                    <a href="documento_edit.php?id=<?php echo $d['id']; ?>" class="btn btn-secondary" style="padding: 10px 15px; white-space: nowrap;">
                        <i class="fas fa-edit"></i> Modifica
                    </a>

                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="documento_id" value="<?php echo $d['id']; ?>">
                        <input type="hidden" name="action" value="elimina">
                        <button type="submit" class="btn btn-secondary"
                                style="padding: 10px 15px; background: #e74c3c; white-space: nowrap;"
                                onclick="return confirm('Eliminare questo documento?')">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes_footer.php'; ?>
