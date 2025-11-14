<?php
require_once 'config.php';
requireLogin();

$pageTitle = 'Gestione Cliente';
$id = $_GET['id'] ?? null;
$errors = [];
$success = false;

// Carica cliente esistente
$cliente = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM clienti WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $cliente = $result->fetchArray(SQLITE3_ASSOC);

    if (!$cliente) {
        header('Location: clienti.php');
        exit;
    }
}

// Processa form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ragione_sociale = trim($_POST['ragione_sociale'] ?? '');
    $nome_referente = trim($_POST['nome_referente'] ?? '');
    $cognome_referente = trim($_POST['cognome_referente'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $cellulare = trim($_POST['cellulare'] ?? '');
    $pec = trim($_POST['pec'] ?? '');
    $partita_iva = trim($_POST['partita_iva'] ?? '');
    $codice_fiscale = trim($_POST['codice_fiscale'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $citta = trim($_POST['citta'] ?? '');
    $cap = trim($_POST['cap'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $sito_web = trim($_POST['sito_web'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $stato = $_POST['stato'] ?? 'attivo';

    // Validazione
    if (empty($ragione_sociale)) {
        $errors[] = 'La ragione sociale è obbligatoria';
    }

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email non valida';
    }

    if ($pec && !filter_var($pec, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'PEC non valida';
    }

    if (empty($errors)) {
        try {
            if ($id) {
                // Update
                $stmt = $db->prepare("
                    UPDATE clienti SET
                        ragione_sociale = :ragione_sociale,
                        nome_referente = :nome_referente,
                        cognome_referente = :cognome_referente,
                        email = :email,
                        telefono = :telefono,
                        cellulare = :cellulare,
                        pec = :pec,
                        partita_iva = :partita_iva,
                        codice_fiscale = :codice_fiscale,
                        indirizzo = :indirizzo,
                        citta = :citta,
                        cap = :cap,
                        provincia = :provincia,
                        sito_web = :sito_web,
                        note = :note,
                        stato = :stato
                    WHERE id = :id
                ");
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $azione = 'modifica_cliente';
            } else {
                // Insert
                $stmt = $db->prepare("
                    INSERT INTO clienti (
                        ragione_sociale, nome_referente, cognome_referente, email, telefono,
                        cellulare, pec, partita_iva, codice_fiscale, indirizzo, citta, cap,
                        provincia, sito_web, note, stato
                    ) VALUES (
                        :ragione_sociale, :nome_referente, :cognome_referente, :email, :telefono,
                        :cellulare, :pec, :partita_iva, :codice_fiscale, :indirizzo, :citta, :cap,
                        :provincia, :sito_web, :note, :stato
                    )
                ");
                $azione = 'nuovo_cliente';
            }

            $stmt->bindValue(':ragione_sociale', $ragione_sociale, SQLITE3_TEXT);
            $stmt->bindValue(':nome_referente', $nome_referente, SQLITE3_TEXT);
            $stmt->bindValue(':cognome_referente', $cognome_referente, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':telefono', $telefono, SQLITE3_TEXT);
            $stmt->bindValue(':cellulare', $cellulare, SQLITE3_TEXT);
            $stmt->bindValue(':pec', $pec, SQLITE3_TEXT);
            $stmt->bindValue(':partita_iva', $partita_iva, SQLITE3_TEXT);
            $stmt->bindValue(':codice_fiscale', $codice_fiscale, SQLITE3_TEXT);
            $stmt->bindValue(':indirizzo', $indirizzo, SQLITE3_TEXT);
            $stmt->bindValue(':citta', $citta, SQLITE3_TEXT);
            $stmt->bindValue(':cap', $cap, SQLITE3_TEXT);
            $stmt->bindValue(':provincia', $provincia, SQLITE3_TEXT);
            $stmt->bindValue(':sito_web', $sito_web, SQLITE3_TEXT);
            $stmt->bindValue(':note', $note, SQLITE3_TEXT);
            $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);

            $stmt->execute();

            if (!$id) {
                $id = $db->lastInsertRowID();
            }

            logActivity($azione, 'cliente', $id, $ragione_sociale);

            $_SESSION['success_message'] = 'Cliente salvato con successo';
            header('Location: clienti.php');
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

.alert-success {
    background: #efe;
    border-left: 4px solid #27ae60;
    color: #229954;
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
            <?php echo $id ? 'Modifica Cliente' : 'Nuovo Cliente'; ?>
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
            <!-- Dati Aziendali -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-building"></i> Dati Aziendali
                </h4>
            </div>

            <div class="form-group form-full">
                <label class="required">Ragione Sociale</label>
                <input type="text" name="ragione_sociale" value="<?php echo h($cliente['ragione_sociale'] ?? $_POST['ragione_sociale'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Partita IVA</label>
                <input type="text" name="partita_iva" value="<?php echo h($cliente['partita_iva'] ?? $_POST['partita_iva'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Codice Fiscale</label>
                <input type="text" name="codice_fiscale" value="<?php echo h($cliente['codice_fiscale'] ?? $_POST['codice_fiscale'] ?? ''); ?>">
            </div>

            <!-- Referente -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-user"></i> Referente
                </h4>
            </div>

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome_referente" value="<?php echo h($cliente['nome_referente'] ?? $_POST['nome_referente'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Cognome</label>
                <input type="text" name="cognome_referente" value="<?php echo h($cliente['cognome_referente'] ?? $_POST['cognome_referente'] ?? ''); ?>">
            </div>

            <!-- Contatti -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-phone"></i> Contatti
                </h4>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo h($cliente['email'] ?? $_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>PEC</label>
                <input type="email" name="pec" value="<?php echo h($cliente['pec'] ?? $_POST['pec'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Telefono</label>
                <input type="tel" name="telefono" value="<?php echo h($cliente['telefono'] ?? $_POST['telefono'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Cellulare</label>
                <input type="tel" name="cellulare" value="<?php echo h($cliente['cellulare'] ?? $_POST['cellulare'] ?? ''); ?>">
            </div>

            <div class="form-group form-full">
                <label>Sito Web</label>
                <input type="url" name="sito_web" value="<?php echo h($cliente['sito_web'] ?? $_POST['sito_web'] ?? ''); ?>" placeholder="https://">
            </div>

            <!-- Indirizzo -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-map-marker-alt"></i> Indirizzo
                </h4>
            </div>

            <div class="form-group form-full">
                <label>Indirizzo</label>
                <input type="text" name="indirizzo" value="<?php echo h($cliente['indirizzo'] ?? $_POST['indirizzo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Città</label>
                <input type="text" name="citta" value="<?php echo h($cliente['citta'] ?? $_POST['citta'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>CAP</label>
                <input type="text" name="cap" value="<?php echo h($cliente['cap'] ?? $_POST['cap'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Provincia</label>
                <input type="text" name="provincia" value="<?php echo h($cliente['provincia'] ?? $_POST['provincia'] ?? ''); ?>" maxlength="2" placeholder="ES: MI">
            </div>

            <div class="form-group">
                <label>Stato</label>
                <select name="stato">
                    <option value="attivo" <?php echo ($cliente['stato'] ?? 'attivo') === 'attivo' ? 'selected' : ''; ?>>Attivo</option>
                    <option value="inattivo" <?php echo ($cliente['stato'] ?? '') === 'inattivo' ? 'selected' : ''; ?>>Inattivo</option>
                </select>
            </div>

            <!-- Note -->
            <div class="form-full">
                <h4 style="color: #2c3e50; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                    <i class="fas fa-sticky-note"></i> Note
                </h4>
            </div>

            <div class="form-group form-full">
                <label>Note</label>
                <textarea name="note"><?php echo h($cliente['note'] ?? $_POST['note'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Salva Cliente
            </button>
            <a href="clienti.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annulla
            </a>
        </div>
    </form>
</div>

<?php include 'includes_footer.php'; ?>
