<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Import Clienti da Excel';

$errors = [];
$preview_data = [];
$import_results = null;

// Fase 1: Upload e parsing file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {

    if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Errore durante il caricamento del file';
    } else {
        $file_path = $_FILES['csv_file']['tmp_name'];

        // Apri file CSV
        $handle = fopen($file_path, 'r');

        if ($handle === false) {
            $errors[] = 'Impossibile leggere il file CSV';
        } else {
            // Leggi prima riga (intestazioni)
            $headers = fgetcsv($handle, 0, ';');

            if ($headers === false) {
                $errors[] = 'File CSV vuoto o non valido';
            } else {
                // Leggi tutte le righe
                $row_number = 1;
                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    $row_number++;

                    // Salta righe vuote
                    if (empty(array_filter($row))) continue;

                    // Mappa colonne
                    $cliente = [
                        'row' => $row_number,
                        'ragione_sociale' => trim($row[1] ?? ''),
                        'nome_referente' => trim($row[2] ?? ''),
                        'cognome_referente' => trim($row[3] ?? ''),
                        'email' => trim($row[4] ?? ''),
                        'telefono' => trim($row[5] ?? ''),
                        'cellulare' => trim($row[6] ?? ''),
                        'pec' => trim($row[7] ?? ''),
                        'partita_iva' => trim($row[8] ?? ''),
                        'codice_fiscale' => trim($row[9] ?? ''),
                        'indirizzo' => trim($row[10] ?? ''),
                        'citta' => trim($row[11] ?? ''),
                        'cap' => trim($row[12] ?? ''),
                        'provincia' => trim($row[13] ?? ''),
                        'sito_web' => trim($row[14] ?? ''),
                        'stato' => trim($row[15] ?? 'attivo'),
                        'errors' => []
                    ];

                    // Validazione
                    if (empty($cliente['ragione_sociale'])) {
                        $cliente['errors'][] = 'Ragione sociale obbligatoria';
                    }

                    if (!empty($cliente['email']) && !filter_var($cliente['email'], FILTER_VALIDATE_EMAIL)) {
                        $cliente['errors'][] = 'Email non valida';
                    }

                    if (!empty($cliente['pec']) && !filter_var($cliente['pec'], FILTER_VALIDATE_EMAIL)) {
                        $cliente['errors'][] = 'PEC non valida';
                    }

                    // Verifica duplicati per email
                    if (!empty($cliente['email'])) {
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM clienti WHERE email = :email");
                        $stmt->bindValue(':email', $cliente['email'], SQLITE3_TEXT);
                        $result = $stmt->execute();
                        if ($result->fetchArray(SQLITE3_ASSOC)['count'] > 0) {
                            $cliente['errors'][] = 'Email già esistente nel database';
                        }
                    }

                    $preview_data[] = $cliente;
                }
            }

            fclose($handle);
        }
    }
}

// Fase 2: Conferma import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_import'])) {
    $imported = 0;
    $skipped = 0;
    $import_errors = [];

    $data = json_decode($_POST['preview_data'], true);

    foreach ($data as $cliente) {
        // Salta righe con errori
        if (!empty($cliente['errors'])) {
            $skipped++;
            $import_errors[] = "Riga {$cliente['row']}: " . implode(', ', $cliente['errors']);
            continue;
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO clienti (
                    ragione_sociale, nome_referente, cognome_referente, email, telefono,
                    cellulare, pec, partita_iva, codice_fiscale, indirizzo, citta, cap,
                    provincia, sito_web, stato
                ) VALUES (
                    :ragione_sociale, :nome_referente, :cognome_referente, :email, :telefono,
                    :cellulare, :pec, :partita_iva, :codice_fiscale, :indirizzo, :citta, :cap,
                    :provincia, :sito_web, :stato
                )
            ");

            $stmt->bindValue(':ragione_sociale', $cliente['ragione_sociale'], SQLITE3_TEXT);
            $stmt->bindValue(':nome_referente', $cliente['nome_referente'], SQLITE3_TEXT);
            $stmt->bindValue(':cognome_referente', $cliente['cognome_referente'], SQLITE3_TEXT);
            $stmt->bindValue(':email', $cliente['email'], SQLITE3_TEXT);
            $stmt->bindValue(':telefono', $cliente['telefono'], SQLITE3_TEXT);
            $stmt->bindValue(':cellulare', $cliente['cellulare'], SQLITE3_TEXT);
            $stmt->bindValue(':pec', $cliente['pec'], SQLITE3_TEXT);
            $stmt->bindValue(':partita_iva', $cliente['partita_iva'], SQLITE3_TEXT);
            $stmt->bindValue(':codice_fiscale', $cliente['codice_fiscale'], SQLITE3_TEXT);
            $stmt->bindValue(':indirizzo', $cliente['indirizzo'], SQLITE3_TEXT);
            $stmt->bindValue(':citta', $cliente['citta'], SQLITE3_TEXT);
            $stmt->bindValue(':cap', $cliente['cap'], SQLITE3_TEXT);
            $stmt->bindValue(':provincia', $cliente['provincia'], SQLITE3_TEXT);
            $stmt->bindValue(':sito_web', $cliente['sito_web'], SQLITE3_TEXT);
            $stmt->bindValue(':stato', $cliente['stato'], SQLITE3_TEXT);

            $stmt->execute();
            $imported++;

        } catch (Exception $e) {
            $skipped++;
            $import_errors[] = "Riga {$cliente['row']}: " . $e->getMessage();
        }
    }

    logActivity('import_clienti', 'clienti', null, "$imported clienti importati, $skipped saltati");

    $import_results = [
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $import_errors
    ];
}

include 'includes_header.php';
?>

<style>
.upload-box {
    border: 3px dashed #e9ecef;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s;
    margin: 20px 0;
}

.upload-box:hover {
    border-color: #3498db;
    background: #f0f8ff;
}

.upload-icon {
    font-size: 64px;
    color: #3498db;
    margin-bottom: 20px;
}

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.alert-success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    color: #155724;
}

.alert-warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    color: #856404;
}

.alert-danger {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    color: #721c24;
}

.preview-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 14px;
}

.preview-table th {
    background: #2c3e50;
    color: white;
    padding: 12px;
    text-align: left;
    position: sticky;
    top: 0;
}

.preview-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
}

.preview-table tr:hover {
    background: #f8f9fa;
}

.row-error {
    background: #fff5f5 !important;
}

.error-badge {
    background: #e74c3c;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-left: 5px;
}

.success-badge {
    background: #27ae60;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-left: 5px;
}
</style>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-import"></i> Import Clienti da Excel/CSV</h3>
        <a href="clienti.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Torna ai Clienti</a>
    </div>

    <?php if ($import_results): ?>
        <div class="alert-success">
            <h3><i class="fas fa-check-circle"></i> Import Completato!</h3>
            <p style="margin: 15px 0; font-size: 16px;">
                <strong><?php echo $import_results['imported']; ?></strong> clienti importati con successo<br>
                <?php if ($import_results['skipped'] > 0): ?>
                    <strong><?php echo $import_results['skipped']; ?></strong> righe saltate per errori
                <?php endif; ?>
            </p>

            <?php if (!empty($import_results['errors'])): ?>
                <div class="alert-warning" style="margin-top: 20px;">
                    <strong>Dettagli errori:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($import_results['errors'] as $error): ?>
                            <li><?php echo h($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <a href="clienti.php" class="btn" style="margin-top: 20px;">
                <i class="fas fa-users"></i> Vai alla Lista Clienti
            </a>
        </div>

    <?php elseif (!empty($preview_data)): ?>
        <div class="info-box">
            <h3><i class="fas fa-eye"></i> Anteprima Dati da Importare</h3>
            <p>Controlla i dati prima di procedere con l'importazione.</p>
            <p><strong>Totale righe:</strong> <?php echo count($preview_data); ?></p>
            <p><strong>Righe valide:</strong> <?php echo count(array_filter($preview_data, fn($c) => empty($c['errors']))); ?></p>
            <p><strong>Righe con errori:</strong> <?php echo count(array_filter($preview_data, fn($c) => !empty($c['errors']))); ?></p>
        </div>

        <div style="max-height: 500px; overflow-y: auto;">
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>Riga</th>
                        <th>Ragione Sociale</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Città</th>
                        <th>P.IVA</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preview_data as $cliente): ?>
                        <tr class="<?php echo !empty($cliente['errors']) ? 'row-error' : ''; ?>">
                            <td><?php echo $cliente['row']; ?></td>
                            <td>
                                <?php echo h($cliente['ragione_sociale']); ?>
                                <?php if (!empty($cliente['errors'])): ?>
                                    <span class="error-badge"><?php echo count($cliente['errors']); ?> errori</span>
                                    <div style="font-size: 12px; color: #e74c3c; margin-top: 5px;">
                                        <?php echo implode(', ', array_map('h', $cliente['errors'])); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="success-badge">✓ OK</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo h($cliente['email']); ?></td>
                            <td><?php echo h($cliente['telefono']); ?></td>
                            <td><?php echo h($cliente['citta']); ?></td>
                            <td><?php echo h($cliente['partita_iva']); ?></td>
                            <td><?php echo h($cliente['stato']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" style="margin-top: 20px;">
            <input type="hidden" name="preview_data" value="<?php echo h(json_encode($preview_data)); ?>">
            <input type="hidden" name="confirm_import" value="1">

            <?php if (count(array_filter($preview_data, fn($c) => empty($c['errors']))) > 0): ?>
                <button type="submit" class="btn" style="background: #27ae60;">
                    <i class="fas fa-check"></i> Conferma Importazione (<?php echo count(array_filter($preview_data, fn($c) => empty($c['errors']))); ?> clienti)
                </button>
            <?php else: ?>
                <div class="alert-danger">
                    <strong>Impossibile procedere:</strong> Tutte le righe contengono errori. Correggi il file CSV e riprova.
                </div>
            <?php endif; ?>

            <a href="import_clienti.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annulla
            </a>
        </form>

    <?php else: ?>
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Come funziona l'import</h3>
            <ol style="margin: 15px 0 0 20px; line-height: 1.8;">
                <li><strong>Prepara il file Excel/CSV</strong> con le colonne nell'ordine corretto</li>
                <li><strong>Carica il file</strong> usando il form qui sotto</li>
                <li><strong>Controlla l'anteprima</strong> dei dati che verranno importati</li>
                <li><strong>Conferma l'importazione</strong> - solo le righe valide verranno inserite</li>
            </ol>

            <h4 style="margin-top: 20px;">📋 Formato File CSV Richiesto</h4>
            <p>Il file deve avere le seguenti colonne <strong>nell'ordine esatto</strong>:</p>
            <div style="background: white; padding: 15px; border-radius: 5px; margin-top: 10px; font-family: monospace; font-size: 13px;">
                ID, Ragione Sociale*, Nome Referente, Cognome Referente, Email, Telefono, Cellulare, PEC,
                Partita IVA, Codice Fiscale, Indirizzo, Città, CAP, Provincia, Sito Web, Stato, Data Creazione
            </div>
            <p style="margin-top: 10px; font-size: 14px;"><strong>*</strong> = Campo obbligatorio</p>

            <h4 style="margin-top: 20px;">💡 Suggerimento</h4>
            <p>Usa la funzione <strong>"Esporta Excel"</strong> dalla lista clienti per ottenere un file con il formato corretto che puoi usare come template!</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="upload-box">
                <div class="upload-icon">
                    <i class="fas fa-file-csv"></i>
                </div>
                <h3>Carica File CSV</h3>
                <p style="color: #666; margin: 15px 0;">
                    Seleziona un file Excel (.csv) esportato o preparato secondo il formato richiesto
                </p>
                <input type="file" name="csv_file" accept=".csv" required
                       style="margin: 20px auto; display: block; padding: 10px;">
                <button type="submit" class="btn" style="margin-top: 10px;">
                    <i class="fas fa-upload"></i> Carica e Visualizza Anteprima
                </button>
            </div>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="alert-danger">
                <strong>Errori:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo h($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes_footer.php'; ?>
