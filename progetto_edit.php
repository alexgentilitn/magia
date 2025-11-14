<?php
require_once 'config.php';
requireLogin();

$pageTitle = 'Gestione Progetto';
$id = $_GET['id'] ?? null;
$errors = [];

// Carica progetto esistente
$progetto = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM progetti WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $progetto = $result->fetchArray(SQLITE3_ASSOC);

    if (!$progetto) {
        header('Location: progetti.php');
        exit;
    }
}

// Lista clienti per dropdown
$clienti_result = $db->query("SELECT id, ragione_sociale FROM clienti WHERE stato = 'attivo' ORDER BY ragione_sociale ASC");
$clienti = [];
while ($row = $clienti_result->fetchArray(SQLITE3_ASSOC)) {
    $clienti[] = $row;
}

// Processa form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? null;
    $titolo = trim($_POST['titolo'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $tipo_progetto = $_POST['tipo_progetto'] ?? 'sito_web';
    $stato = $_POST['stato'] ?? 'pianificazione';
    $priorita = $_POST['priorita'] ?? 'media';
    $importo = trim($_POST['importo'] ?? '');
    $data_inizio = $_POST['data_inizio'] ?? null;
    $data_consegna = $_POST['data_consegna'] ?? null;
    $data_completamento = $_POST['data_completamento'] ?? null;
    $percentuale_completamento = $_POST['percentuale_completamento'] ?? 0;
    $ore_stimate = trim($_POST['ore_stimate'] ?? '');
    $ore_effettive = trim($_POST['ore_effettive'] ?? '');
    $note = trim($_POST['note'] ?? '');

    // Validazione
    if (empty($titolo)) {
        $errors[] = 'Il titolo è obbligatorio';
    }

    if (empty($cliente_id)) {
        $errors[] = 'Seleziona un cliente';
    }

    if (empty($data_inizio)) {
        $errors[] = 'La data di inizio è obbligatoria';
    }

    if ($importo && !is_numeric($importo)) {
        $errors[] = 'Importo non valido';
    }

    if ($ore_stimate && !is_numeric($ore_stimate)) {
        $errors[] = 'Ore stimate non valide';
    }

    if ($ore_effettive && !is_numeric($ore_effettive)) {
        $errors[] = 'Ore effettive non valide';
    }

    if (empty($errors)) {
        try {
            if ($id) {
                // Update
                $stmt = $db->prepare("
                    UPDATE progetti SET
                        cliente_id = :cliente_id,
                        titolo = :titolo,
                        descrizione = :descrizione,
                        tipo_progetto = :tipo_progetto,
                        stato = :stato,
                        priorita = :priorita,
                        importo = :importo,
                        data_inizio = :data_inizio,
                        data_consegna = :data_consegna,
                        data_completamento = :data_completamento,
                        percentuale_completamento = :percentuale_completamento,
                        ore_stimate = :ore_stimate,
                        ore_effettive = :ore_effettive,
                        note = :note
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $azione = 'modifica_progetto';
            } else {
                // Insert
                $stmt = $db->prepare("
                    INSERT INTO progetti (
                        cliente_id, titolo, descrizione, tipo_progetto, stato, priorita,
                        importo, data_inizio, data_consegna, data_completamento,
                        percentuale_completamento, ore_stimate, ore_effettive, note
                    ) VALUES (
                        :cliente_id, :titolo, :descrizione, :tipo_progetto, :stato, :priorita,
                        :importo, :data_inizio, :data_consegna, :data_completamento,
                        :percentuale_completamento, :ore_stimate, :ore_effettive, :note
                    )
                ");
                $azione = 'nuovo_progetto';
            }

            $stmt->bindValue(':cliente_id', $cliente_id, SQLITE3_INTEGER);
            $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
            $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
            $stmt->bindValue(':tipo_progetto', $tipo_progetto, SQLITE3_TEXT);
            $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);
            $stmt->bindValue(':priorita', $priorita, SQLITE3_TEXT);
            $stmt->bindValue(':importo', $importo ? floatval($importo) : null, SQLITE3_FLOAT);
            $stmt->bindValue(':data_inizio', $data_inizio, SQLITE3_TEXT);
            $stmt->bindValue(':data_consegna', $data_consegna, SQLITE3_TEXT);
            $stmt->bindValue(':data_completamento', $data_completamento, SQLITE3_TEXT);
            $stmt->bindValue(':percentuale_completamento', intval($percentuale_completamento), SQLITE3_INTEGER);
            $stmt->bindValue(':ore_stimate', $ore_stimate ? floatval($ore_stimate) : null, SQLITE3_FLOAT);
            $stmt->bindValue(':ore_effettive', $ore_effettive ? floatval($ore_effettive) : null, SQLITE3_FLOAT);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);

            $stmt->execute();

            if (!$id) {
                $id = $db->lastInsertRowID();
            }

            logActivity($azione, 'progetto', $id, $titolo);

            $_SESSION['success_message'] = 'Progetto salvato con successo';
            header('Location: progetti.php');
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
    min-height: 120px;
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

.range-container {
    display: flex;
    align-items: center;
    gap: 15px;
}

.range-container input[type="range"] {
    flex: 1;
}

.range-value {
    font-weight: 700;
    color: #3498db;
    font-size: 18px;
    min-width: 50px;
    text-align: center;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>
            <i class="fas fa-<?php echo $id ? 'edit' : 'plus'; ?>"></i>
            <?php echo $id ? 'Modifica Progetto' : 'Nuovo Progetto'; ?>
        </h3>
        <?php if ($id): ?>
            <a href="progetto_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" style="margin: 0;">
                <i class="fas fa-trash-alt"></i> Elimina Progetto
            </a>
        <?php endif; ?>
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
            <!-- Informazioni Base -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-info-circle"></i> Informazioni Base
                </h4>
            </div>

            <div class="form-group form-full">
                <label class="required">Cliente</label>
                <select name="cliente_id" required>
                    <option value="">Seleziona cliente...</option>
                    <?php foreach ($clienti as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($progetto['cliente_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo h($c['ragione_sociale']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group form-full">
                <label class="required">Titolo Progetto</label>
                <input type="text" name="titolo" value="<?php echo h($progetto['titolo'] ?? $_POST['titolo'] ?? ''); ?>" required>
            </div>

            <div class="form-group form-full">
                <label>Descrizione</label>
                <textarea name="descrizione"><?php echo h($progetto['descrizione'] ?? $_POST['descrizione'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Tipo Progetto</label>
                <select name="tipo_progetto">
                    <option value="sito_web" <?php echo ($progetto['tipo_progetto'] ?? 'sito_web') === 'sito_web' ? 'selected' : ''; ?>>Sito Web</option>
                    <option value="ecommerce" <?php echo ($progetto['tipo_progetto'] ?? '') === 'ecommerce' ? 'selected' : ''; ?>>E-commerce</option>
                    <option value="app" <?php echo ($progetto['tipo_progetto'] ?? '') === 'app' ? 'selected' : ''; ?>>Applicazione</option>
                    <option value="grafica" <?php echo ($progetto['tipo_progetto'] ?? '') === 'grafica' ? 'selected' : ''; ?>>Grafica</option>
                    <option value="seo" <?php echo ($progetto['tipo_progetto'] ?? '') === 'seo' ? 'selected' : ''; ?>>SEO</option>
                    <option value="manutenzione" <?php echo ($progetto['tipo_progetto'] ?? '') === 'manutenzione' ? 'selected' : ''; ?>>Manutenzione</option>
                    <option value="altro" <?php echo ($progetto['tipo_progetto'] ?? '') === 'altro' ? 'selected' : ''; ?>>Altro</option>
                </select>
            </div>

            <div class="form-group">
                <label>Importo (€)</label>
                <input type="number" name="importo" step="0.01" value="<?php echo h($progetto['importo'] ?? $_POST['importo'] ?? ''); ?>" placeholder="0.00">
            </div>

            <!-- Stato e Priorità -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-tasks"></i> Stato e Priorità
                </h4>
            </div>

            <div class="form-group">
                <label>Stato</label>
                <select name="stato">
                    <option value="pianificazione" <?php echo ($progetto['stato'] ?? 'pianificazione') === 'pianificazione' ? 'selected' : ''; ?>>Pianificazione</option>
                    <option value="in_corso" <?php echo ($progetto['stato'] ?? '') === 'in_corso' ? 'selected' : ''; ?>>In Corso</option>
                    <option value="completato" <?php echo ($progetto['stato'] ?? '') === 'completato' ? 'selected' : ''; ?>>Completato</option>
                    <option value="sospeso" <?php echo ($progetto['stato'] ?? '') === 'sospeso' ? 'selected' : ''; ?>>Sospeso</option>
                    <option value="annullato" <?php echo ($progetto['stato'] ?? '') === 'annullato' ? 'selected' : ''; ?>>Annullato</option>
                </select>
            </div>

            <div class="form-group">
                <label>Priorità</label>
                <select name="priorita">
                    <option value="bassa" <?php echo ($progetto['priorita'] ?? '') === 'bassa' ? 'selected' : ''; ?>>Bassa</option>
                    <option value="media" <?php echo ($progetto['priorita'] ?? 'media') === 'media' ? 'selected' : ''; ?>>Media</option>
                    <option value="alta" <?php echo ($progetto['priorita'] ?? '') === 'alta' ? 'selected' : ''; ?>>Alta</option>
                </select>
            </div>

            <div class="form-group form-full">
                <label>Percentuale Completamento</label>
                <div class="range-container">
                    <input type="range" name="percentuale_completamento" id="percentuale"
                           min="0" max="100" step="5"
                           value="<?php echo h($progetto['percentuale_completamento'] ?? 0); ?>"
                           oninput="document.getElementById('percValue').textContent = this.value + '%'">
                    <span class="range-value" id="percValue"><?php echo h($progetto['percentuale_completamento'] ?? 0); ?>%</span>
                </div>
            </div>

            <!-- Date -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-calendar"></i> Date
                </h4>
            </div>

            <div class="form-group">
                <label class="required">Data Inizio</label>
                <input type="date" name="data_inizio" value="<?php echo h($progetto['data_inizio'] ?? $_POST['data_inizio'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Data Consegna Prevista</label>
                <input type="date" name="data_consegna" value="<?php echo h($progetto['data_consegna'] ?? $_POST['data_consegna'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Data Completamento</label>
                <input type="date" name="data_completamento" value="<?php echo h($progetto['data_completamento'] ?? $_POST['data_completamento'] ?? ''); ?>">
            </div>

            <!-- Ore -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-clock"></i> Ore di Lavoro
                </h4>
            </div>

            <div class="form-group">
                <label>Ore Stimate</label>
                <input type="number" name="ore_stimate" step="0.5" value="<?php echo h($progetto['ore_stimate'] ?? $_POST['ore_stimate'] ?? ''); ?>" placeholder="0">
            </div>

            <div class="form-group">
                <label>Ore Effettive</label>
                <input type="number" name="ore_effettive" step="0.5" value="<?php echo h($progetto['ore_effettive'] ?? $_POST['ore_effettive'] ?? ''); ?>" placeholder="0">
            </div>

            <!-- Note -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-sticky-note"></i> Note
                </h4>
            </div>

            <div class="form-group form-full">
                <label>Note Interne</label>
                <textarea name="note"><?php echo h($progetto['note'] ?? $_POST['note'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Salva Progetto
            </button>
            <a href="progetti.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annulla
            </a>
        </div>
    </form>
</div>

<?php if ($id): ?>
    <!-- Sezione Documenti -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h3><i class="fas fa-folder-open"></i> Documenti del Progetto</h3>
        </div>

        <!-- Upload Nuovo Documento -->
        <div style="background: var(--gray-50); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin-bottom: 15px;"><i class="fas fa-cloud-upload-alt"></i> Carica Nuovo Documento</h4>
            <form action="upload_project_document.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="progetto_id" value="<?php echo $id; ?>">

                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">File *</label>
                        <input type="file" name="file" required accept="*/*"
                               style="width: 100%; padding: 10px; border: 2px solid var(--gray-300); border-radius: 8px;">
                        <small style="color: var(--gray-600);">Max 10MB - Tutti i formati supportati</small>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Tipo Documento</label>
                        <select name="tipo" style="width: 100%; padding: 10px; border: 2px solid var(--gray-300); border-radius: 8px;">
                            <option value="contratto">Contratto</option>
                            <option value="preventivo">Preventivo</option>
                            <option value="fattura">Fattura</option>
                            <option value="bozza">Bozza/Mockup</option>
                            <option value="documento">Documento</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Carica
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista Documenti -->
        <?php
        $stmt = $db->prepare("SELECT * FROM documenti WHERE progetto_id = :progetto_id ORDER BY created_at DESC");
        $stmt->bindValue(':progetto_id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $documenti_progetto = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $documenti_progetto[] = $row;
        }
        ?>

        <?php if (empty($documenti_progetto)): ?>
            <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                <i class="fas fa-folder-open" style="font-size: 3em; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>Nessun documento caricato per questo progetto.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><i class="fas fa-file"></i></th>
                        <th>Nome File</th>
                        <th>Tipo</th>
                        <th>Dimensione</th>
                        <th>Caricato il</th>
                        <th style="width: 150px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documenti_progetto as $doc): ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php
                                $ext = strtolower(pathinfo($doc['titolo'], PATHINFO_EXTENSION));
                                $icon_map = [
                                    'pdf' => 'fas fa-file-pdf text-danger',
                                    'doc' => 'fas fa-file-word text-primary',
                                    'docx' => 'fas fa-file-word text-primary',
                                    'xls' => 'fas fa-file-excel text-success',
                                    'xlsx' => 'fas fa-file-excel text-success',
                                    'jpg' => 'fas fa-file-image text-info',
                                    'jpeg' => 'fas fa-file-image text-info',
                                    'png' => 'fas fa-file-image text-info',
                                    'zip' => 'fas fa-file-archive text-warning'
                                ];
                                $icon = $icon_map[$ext] ?? 'fas fa-file';
                                ?>
                                <i class="<?php echo $icon; ?>" style="font-size: 1.5em;"></i>
                            </td>
                            <td><strong><?php echo h($doc['titolo']); ?></strong></td>
                            <td><span class="badge badge-<?php echo $doc['tipo']; ?>"><?php echo h($doc['tipo']); ?></span></td>
                            <td><?php echo formatFileSize($doc['file_size']); ?></td>
                            <td><?php echo formatDateTime($doc['created_at'], 'd/m/Y H:i'); ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="download.php?id=<?php echo $doc['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.85em;">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="delete_document.php?id=<?php echo $doc['id']; ?>&return=progetto_edit.php?id=<?php echo $id; ?>"
                                       onclick="return confirm('Eliminare questo documento?')"
                                       class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85em;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const fileInput = this.querySelector('input[type="file"]');
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size;
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (fileSize > maxSize) {
                e.preventDefault();
                alert('Il file è troppo grande! Dimensione massima: 10MB');
                return false;
            }
        }
    });
    </script>
<?php endif; ?>

<?php include 'includes_footer.php'; ?>
