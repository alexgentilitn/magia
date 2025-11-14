<?php
require_once 'config.php';
requireLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if (!$id) {
    $_SESSION['error_message'] = 'Progetto non specificato';
    header('Location: progetti.php');
    exit;
}

// Carica progetto
$stmt = $db->prepare("SELECT * FROM progetti WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$result = $stmt->execute();
$progetto = $result->fetchArray(SQLITE3_ASSOC);

if (!$progetto) {
    $_SESSION['error_message'] = 'Progetto non trovato';
    header('Location: progetti.php');
    exit;
}

// Conta documenti associati
$stmt = $db->prepare("SELECT COUNT(*) as count FROM documenti WHERE progetto_id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$result = $stmt->execute();
$doc_count = $result->fetchArray(SQLITE3_ASSOC)['count'];

// Processa eliminazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $action = $_POST['document_action'] ?? 'delete';

    // Gestisci documenti
    if ($doc_count > 0) {
        if ($action === 'move_to_generic') {
            // Sposta documenti in area generica (rimuovi progetto_id)
            $stmt = $db->prepare("UPDATE documenti SET progetto_id = NULL WHERE progetto_id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            logActivity('move_documents_generic', 'documenti', null, "Spostati $doc_count documenti in area generica da progetto: {$progetto['titolo']}");
        } else {
            // Elimina tutti i documenti associati
            $stmt = $db->prepare("SELECT file_path FROM documenti WHERE progetto_id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $result = $stmt->execute();

            while ($doc = $result->fetchArray(SQLITE3_ASSOC)) {
                $file_path = __DIR__ . '/' . $doc['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $stmt = $db->prepare("DELETE FROM documenti WHERE progetto_id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
        }
    }

    // Elimina scadenze associate
    $stmt = $db->prepare("DELETE FROM scadenze WHERE progetto_id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    // Elimina attività associate (se esiste tabella attività)
    $tables = [];
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $tables[] = $row['name'];
    }

    if (in_array('attivita', $tables)) {
        $stmt = $db->prepare("DELETE FROM attivita WHERE progetto_id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    // Elimina progetto
    $stmt = $db->prepare("DELETE FROM progetti WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    logActivity('delete_project', 'progetti', $id, "Eliminato progetto: {$progetto['titolo']}");

    $_SESSION['success_message'] = 'Progetto eliminato con successo';
    header('Location: progetti.php');
    exit;
}

$pageTitle = 'Elimina Progetto';
include 'includes_header.php';
?>

<div class="card">
    <div class="card-header" style="background: #dc3545; color: white;">
        <h3><i class="fas fa-exclamation-triangle"></i> Conferma Eliminazione Progetto</h3>
    </div>

    <div style="padding: 30px;">
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h4 style="color: #856404; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Attenzione!
            </h4>
            <p style="color: #856404; margin: 0; font-size: 16px;">
                Stai per eliminare il progetto <strong>"<?php echo h($progetto['titolo']); ?>"</strong>.
                <br>Questa azione è irreversibile.
            </p>
        </div>

        <?php if ($doc_count > 0): ?>
            <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h4 style="color: #1976d2; margin-bottom: 15px;">
                    <i class="fas fa-folder-open"></i> Documenti Associati
                </h4>
                <p style="color: #1565c0; margin-bottom: 15px;">
                    Questo progetto ha <strong><?php echo $doc_count; ?> documento<?php echo $doc_count > 1 ? 'i' : ''; ?></strong> associato<?php echo $doc_count > 1 ? 'i' : ''; ?>.
                    <br>Scegli cosa fare con <?php echo $doc_count > 1 ? 'essi' : 'esso'; ?>:
                </p>

                <form method="POST" id="deleteForm">
                    <input type="hidden" name="confirm_delete" value="1">

                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; padding: 15px; background: white; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; margin-bottom: 10px;">
                            <input type="radio" name="document_action" value="move_to_generic" checked style="margin-right: 12px; width: 20px; height: 20px;">
                            <div>
                                <strong style="display: block; color: #2c3e50; margin-bottom: 5px;">
                                    <i class="fas fa-folder"></i> Sposta in Area Documenti Generici
                                </strong>
                                <span style="color: #6c757d; font-size: 14px;">
                                    I documenti verranno conservati nell'area documenti generici (senza associazione a progetto)
                                </span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; padding: 15px; background: white; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="document_action" value="delete" style="margin-right: 12px; width: 20px; height: 20px;">
                            <div>
                                <strong style="display: block; color: #dc3545; margin-bottom: 5px;">
                                    <i class="fas fa-trash-alt"></i> Elimina Definitivamente
                                </strong>
                                <span style="color: #6c757d; font-size: 14px;">
                                    I documenti verranno eliminati permanentemente dal sistema
                                </span>
                            </div>
                        </label>
                    </div>

                    <div style="padding: 20px; background: #fff5f5; border: 2px solid #dc3545; border-radius: 8px; margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="confirmCheck" required style="margin-right: 10px; width: 18px; height: 18px;">
                            <span style="color: #721c24;">
                                <strong>Confermo di voler eliminare questo progetto</strong>
                            </span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-danger" id="deleteBtn" style="flex: 1;">
                            <i class="fas fa-trash-alt"></i> Elimina Progetto
                        </button>
                        <a href="progetto_edit.php?id=<?php echo $id; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">
                            <i class="fas fa-times"></i> Annulla
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="confirm_delete" value="1">
                <input type="hidden" name="document_action" value="delete">

                <div style="padding: 20px; background: #fff5f5; border: 2px solid #dc3545; border-radius: 8px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="confirmCheck" required style="margin-right: 10px; width: 18px; height: 18px;">
                        <span style="color: #721c24;">
                            <strong>Confermo di voler eliminare questo progetto</strong>
                        </span>
                    </label>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-danger" id="deleteBtn" style="flex: 1;">
                        <i class="fas fa-trash-alt"></i> Elimina Progetto
                    </button>
                    <a href="progetto_edit.php?id=<?php echo $id; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
// Abilita pulsante elimina solo quando checkbox è selezionato
document.getElementById('confirmCheck').addEventListener('change', function() {
    document.getElementById('deleteBtn').disabled = !this.checked;
});

// Inizialmente disabilita il pulsante
document.getElementById('deleteBtn').disabled = true;
</script>

<?php include 'includes_footer.php'; ?>
