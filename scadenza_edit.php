<?php
require_once 'config.php';
requireLogin();

$pageTitle = 'Gestione Scadenza';
$id = $_GET['id'] ?? null;
$errors = [];

// Carica scadenza esistente
$scadenza = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM scadenze WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $scadenza = $result->fetchArray(SQLITE3_ASSOC);

    if (!$scadenza) {
        header('Location: scadenze.php');
        exit;
    }
}

// Lista progetti per dropdown
$progetti_result = $db->query("
    SELECT p.id, p.titolo, c.ragione_sociale
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    ORDER BY p.titolo ASC
");
$progetti = [];
while ($row = $progetti_result->fetchArray(SQLITE3_ASSOC)) {
    $progetti[] = $row;
}

// Processa form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progetto_id = $_POST['progetto_id'] ?? null;
    $descrizione = trim($_POST['descrizione'] ?? '');
    $tipo = $_POST['tipo'] ?? 'pagamento';
    $data_scadenza = $_POST['data_scadenza'] ?? null;
    $importo = trim($_POST['importo'] ?? '');
    $stato = $_POST['stato'] ?? 'in_attesa';
    $data_pagamento = $_POST['data_pagamento'] ?? null;
    $note = trim($_POST['note'] ?? '');

    // Validazione
    if (empty($progetto_id)) {
        $errors[] = 'Seleziona un progetto';
    }

    if (empty($descrizione)) {
        $errors[] = 'La descrizione è obbligatoria';
    }

    if (empty($data_scadenza)) {
        $errors[] = 'La data di scadenza è obbligatoria';
    }

    if ($importo && !is_numeric($importo)) {
        $errors[] = 'Importo non valido';
    }

    if (empty($errors)) {
        try {
            if ($id) {
                // Update
                $stmt = $db->prepare("
                    UPDATE scadenze SET
                        progetto_id = :progetto_id,
                        descrizione = :descrizione,
                        tipo = :tipo,
                        data_scadenza = :data_scadenza,
                        importo = :importo,
                        stato = :stato,
                        data_pagamento = :data_pagamento,
                        note = :note
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $azione = 'modifica_scadenza';
            } else {
                // Insert
                $stmt = $db->prepare("
                    INSERT INTO scadenze (
                        progetto_id, descrizione, tipo, data_scadenza, importo,
                        stato, data_pagamento, note
                    ) VALUES (
                        :progetto_id, :descrizione, :tipo, :data_scadenza, :importo,
                        :stato, :data_pagamento, :note
                    )
                ");
                $azione = 'nuova_scadenza';
            }

            $stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
            $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
            $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
            $stmt->bindValue(':data_scadenza', $data_scadenza, SQLITE3_TEXT);
            $stmt->bindValue(':importo', $importo ? floatval($importo) : null, SQLITE3_FLOAT);
            $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);
            $stmt->bindValue(':data_pagamento', $data_pagamento, SQLITE3_TEXT);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);

            $stmt->execute();

            if (!$id) {
                $id = $db->lastInsertRowID();
            }

            logActivity($azione, 'scadenza', $id, $descrizione);

            $_SESSION['success_message'] = 'Scadenza salvata con successo';
            header('Location: scadenze.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Errore nel salvataggio: ' . $e->getMessage();
        }
    }
}

include 'includes_header.php';
?>

<style>
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

.form-group label.required::after {
    content: ' *';
    color: #e74c3c;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
    font-family: inherit;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #fee;
    border-left: 4px solid #e74c3c;
    color: #c0392b;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn-secondary {
    background: #95a5a6;
}

.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fas fa-<?php echo $id ? 'edit' : 'plus'; ?>"></i>
            <?php echo $id ? 'Modifica Scadenza' : 'Nuova Scadenza'; ?>
        </h3>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Errori:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo h($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-grid">
            <div class="form-group form-full">
                <label class="required">Progetto</label>
                <select name="progetto_id" required>
                    <option value="">Seleziona progetto...</option>
                    <?php foreach ($progetti as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($scadenza['progetto_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                            <?php echo h($p['titolo']); ?> - <?php echo h($p['ragione_sociale']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group form-full">
                <label class="required">Descrizione</label>
                <input type="text" name="descrizione"
                       value="<?php echo h($scadenza['descrizione'] ?? $_POST['descrizione'] ?? ''); ?>"
                       placeholder="Es: Pagamento acconto 30%, Consegna milestone 1, ecc."
                       required>
            </div>

            <div class="form-group">
                <label class="required">Tipo</label>
                <select name="tipo" required>
                    <option value="pagamento" <?php echo ($scadenza['tipo'] ?? 'pagamento') === 'pagamento' ? 'selected' : ''; ?>>Pagamento</option>
                    <option value="consegna" <?php echo ($scadenza['tipo'] ?? '') === 'consegna' ? 'selected' : ''; ?>>Consegna</option>
                    <option value="milestone" <?php echo ($scadenza['tipo'] ?? '') === 'milestone' ? 'selected' : ''; ?>>Milestone</option>
                    <option value="altro" <?php echo ($scadenza['tipo'] ?? '') === 'altro' ? 'selected' : ''; ?>>Altro</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Data Scadenza</label>
                <input type="date" name="data_scadenza"
                       value="<?php echo h($scadenza['data_scadenza'] ?? $_POST['data_scadenza'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Importo (€)</label>
                <input type="number" name="importo" step="0.01"
                       value="<?php echo h($scadenza['importo'] ?? $_POST['importo'] ?? ''); ?>"
                       placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Stato</label>
                <select name="stato">
                    <option value="in_attesa" <?php echo ($scadenza['stato'] ?? 'in_attesa') === 'in_attesa' ? 'selected' : ''; ?>>In Attesa</option>
                    <option value="pagato" <?php echo ($scadenza['stato'] ?? '') === 'pagato' ? 'selected' : ''; ?>>Pagato</option>
                    <option value="annullato" <?php echo ($scadenza['stato'] ?? '') === 'annullato' ? 'selected' : ''; ?>>Annullato</option>
                </select>
            </div>

            <div class="form-group">
                <label>Data Pagamento</label>
                <input type="date" name="data_pagamento"
                       value="<?php echo h($scadenza['data_pagamento'] ?? $_POST['data_pagamento'] ?? ''); ?>">
                <small style="color: #7f8c8d; margin-top: 5px;">Compilare solo se pagato</small>
            </div>

            <div class="form-group form-full">
                <label>Note</label>
                <textarea name="note"><?php echo h($scadenza['note'] ?? $_POST['note'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Salva Scadenza
            </button>
            <a href="scadenze.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annulla
            </a>
        </div>
    </form>
</div>

<?php include 'includes_footer.php'; ?>
