<?php
require_once 'config.php';
requireLogin();

$pageTitle = 'Gestione Documento';
$id = $_GET['id'] ?? null;
$errors = [];

// Carica documento esistente
$documento = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM documenti WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $documento = $result->fetchArray(SQLITE3_ASSOC);

    if (!$documento) {
        header('Location: documenti.php');
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

// Lista clienti per dropdown
$clienti_result = $db->query("SELECT id, ragione_sociale FROM clienti ORDER BY ragione_sociale ASC");
$clienti = [];
while ($row = $clienti_result->fetchArray(SQLITE3_ASSOC)) {
    $clienti[] = $row;
}

// Processa form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progetto_id = $_POST['progetto_id'] ?? null;
    $cliente_id = $_POST['cliente_id'] ?? null;
    $titolo = trim($_POST['titolo'] ?? '');
    $tipo = $_POST['tipo'] ?? 'documento';
    $note = trim($_POST['note'] ?? '');

    // Validazione
    if (empty($titolo)) {
        $errors[] = 'Il titolo è obbligatorio';
    }

    // Upload file (solo per nuovo documento o se viene caricato nuovo file)
    $file_uploaded = false;
    $file_path = $documento['file_path'] ?? null;
    $file_size = $documento['file_size'] ?? null;
    $mime_type = $documento['mime_type'] ?? null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_uploaded = true;

        // Crea directory uploads se non esiste
        $upload_dir = __DIR__ . '/uploads';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validazione file
        $max_size = 10 * 1024 * 1024; // 10MB
        if ($_FILES['file']['size'] > $max_size) {
            $errors[] = 'Il file è troppo grande (massimo 10MB)';
        }

        // Estensioni permesse
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'txt'];
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Tipo di file non permesso. Estensioni permesse: ' . implode(', ', $allowed_extensions);
        }

        if (empty($errors)) {
            // Genera nome file unico
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['file']['name']);
            $file_path = 'uploads/' . $file_name;
            $full_path = __DIR__ . '/' . $file_path;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $full_path)) {
                $file_size = $_FILES['file']['size'];
                $mime_type = $_FILES['file']['type'];

                // Se stiamo modificando e c'era un vecchio file, eliminalo
                if ($documento && $documento['file_path']) {
                    $old_file = __DIR__ . '/' . $documento['file_path'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            } else {
                $errors[] = 'Errore durante l\'upload del file';
            }
        }
    } elseif (!$id) {
        // Nuovo documento senza file
        $errors[] = 'Devi caricare un file';
    }

    if (empty($errors)) {
        try {
            if ($id) {
                // Update
                $stmt = $db->prepare("
                    UPDATE documenti SET
                        progetto_id = :progetto_id,
                        cliente_id = :cliente_id,
                        titolo = :titolo,
                        tipo = :tipo,
                        note = :note,
                        file_path = :file_path,
                        file_size = :file_size,
                        mime_type = :mime_type
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $azione = 'modifica_documento';
            } else {
                // Insert
                $stmt = $db->prepare("
                    INSERT INTO documenti (
                        progetto_id, cliente_id, titolo, tipo, note,
                        file_path, file_size, mime_type
                    ) VALUES (
                        :progetto_id, :cliente_id, :titolo, :tipo, :note,
                        :file_path, :file_size, :mime_type
                    )
                ");
                $azione = 'nuovo_documento';
            }

            $stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
            $stmt->bindValue(':cliente_id', $cliente_id, SQLITE3_INTEGER);
            $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
            $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);
            $stmt->bindValue(':file_path', $file_path, SQLITE3_TEXT);
            $stmt->bindValue(':file_size', $file_size, SQLITE3_INTEGER);
            $stmt->bindValue(':mime_type', $mime_type, SQLITE3_TEXT);

            $stmt->execute();

            if (!$id) {
                $id = $db->lastInsertRowID();
            }

            logActivity($azione, 'documento', $id, $titolo);

            $_SESSION['success_message'] = 'Documento salvato con successo';
            header('Location: documenti.php');
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

.file-upload {
    border: 2px dashed #e9ecef;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s;
}

.file-upload:hover {
    border-color: #3498db;
    background: #f0f8ff;
}

.file-upload input[type="file"] {
    display: none;
}

.file-upload-label {
    cursor: pointer;
    display: inline-block;
}

.file-upload-icon {
    font-size: 48px;
    color: #3498db;
    margin-bottom: 15px;
}

.file-info {
    margin-top: 15px;
    padding: 10px;
    background: #fff;
    border-radius: 5px;
    font-size: 14px;
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

.alert-info {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    color: #1565c0;
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
            <i class="fas fa-<?php echo $id ? 'edit' : 'upload'; ?>"></i>
            <?php echo $id ? 'Modifica Documento' : 'Carica Documento'; ?>
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

    <div class="alert alert-info">
        <strong>ℹ️ Informazioni Upload:</strong>
        <ul style="margin: 10px 0 0 20px; font-size: 14px;">
            <li>Dimensione massima: 10 MB</li>
            <li>Formati permessi: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, RAR, TXT</li>
            <li>I file verranno salvati in modo sicuro nella cartella uploads/</li>
        </ul>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <!-- Upload File -->
            <?php if (!$id || true): ?>
                <div class="form-full">
                    <div class="form-group">
                        <label <?php echo !$id ? 'class="required"' : ''; ?>>
                            <?php echo $id ? 'Sostituisci File (opzionale)' : 'File da Caricare'; ?>
                        </label>
                        <div class="file-upload">
                            <label for="file" class="file-upload-label">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div style="color: #555;">
                                    <strong>Clicca per selezionare il file</strong><br>
                                    oppure trascina qui il file
                                </div>
                            </label>
                            <input type="file" id="file" name="file" <?php echo !$id ? 'required' : ''; ?>
                                   onchange="document.getElementById('filename').textContent = this.files[0] ? this.files[0].name + ' (' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB)' : 'Nessun file selezionato'">
                            <div class="file-info" id="filename">
                                <?php if ($documento && $documento['file_path']): ?>
                                    File attuale: <strong><?php echo h(basename($documento['file_path'])); ?></strong>
                                    (<?php echo formatFileSize($documento['file_size']); ?>)
                                <?php else: ?>
                                    Nessun file selezionato
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informazioni Documento -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-top: 2px solid #e9ecef; padding-top: 20px;">
                    <i class="fas fa-info-circle"></i> Informazioni Documento
                </h4>
            </div>

            <div class="form-group form-full">
                <label class="required">Titolo</label>
                <input type="text" name="titolo"
                       value="<?php echo h($documento['titolo'] ?? $_POST['titolo'] ?? ''); ?>"
                       placeholder="Es: Fattura 2025/001, Contratto Sviluppo Sito"
                       required>
            </div>

            <div class="form-group">
                <label>Tipo Documento</label>
                <select name="tipo">
                    <option value="fattura" <?php echo ($documento['tipo'] ?? 'documento') === 'fattura' ? 'selected' : ''; ?>>Fattura</option>
                    <option value="contratto" <?php echo ($documento['tipo'] ?? '') === 'contratto' ? 'selected' : ''; ?>>Contratto</option>
                    <option value="preventivo" <?php echo ($documento['tipo'] ?? '') === 'preventivo' ? 'selected' : ''; ?>>Preventivo</option>
                    <option value="documento" <?php echo ($documento['tipo'] ?? 'documento') === 'documento' ? 'selected' : ''; ?>>Documento Generico</option>
                    <option value="altro" <?php echo ($documento['tipo'] ?? '') === 'altro' ? 'selected' : ''; ?>>Altro</option>
                </select>
            </div>

            <div class="form-group">
                <label>Associa a Progetto</label>
                <select name="progetto_id">
                    <option value="">Nessun progetto</option>
                    <?php foreach ($progetti as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($documento['progetto_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                            <?php echo h($p['titolo']); ?>
                            <?php if ($p['ragione_sociale']): ?>
                                - <?php echo h($p['ragione_sociale']); ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Associa a Cliente</label>
                <select name="cliente_id">
                    <option value="">Nessun cliente</option>
                    <?php foreach ($clienti as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($documento['cliente_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo h($c['ragione_sociale']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group form-full">
                <label>Note</label>
                <textarea name="note" placeholder="Note aggiuntive sul documento..."><?php echo h($documento['note'] ?? $_POST['note'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Salva Documento
            </button>
            <a href="documenti.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annulla
            </a>
        </div>
    </form>
</div>

<?php include 'includes_footer.php'; ?>
