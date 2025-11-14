<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Template Email';

try {
    require_once 'config.php';
} catch (Throwable $e) {
    die("ERRORE config.php: " . $e->getMessage());
}

try {
    requireLogin();
} catch (Throwable $e) {
    die("ERRORE requireLogin: " . $e->getMessage());
}

try {
    $currentUser = getCurrentUser();
} catch (Throwable $e) {
    die("ERRORE getCurrentUser: " . $e->getMessage());
}

// Solo admin può gestire template
if (!$currentUser || $currentUser['ruolo'] !== 'admin') {
    $_SESSION['error_message'] = 'Accesso negato. Solo gli amministratori possono gestire i template email.';
    header('Location: dashboard.php');
    exit;
}

$message = '';
$error = '';

// Elimina template
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $db->prepare("DELETE FROM email_templates WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    $message = 'Template eliminato con successo';
    logActivity('delete_email_template', 'email_templates', $id, 'Template email eliminato');

    header('Location: email_templates.php');
    exit;
}

// Salva template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_template'])) {
    $id = $_POST['template_id'] ?? null;
    $nome = trim($_POST['nome']);
    $oggetto = trim($_POST['oggetto']);
    $corpo_html = $_POST['corpo_html']; // Non trim per preservare HTML
    $descrizione = trim($_POST['descrizione']);
    $variabili = trim($_POST['variabili_disponibili']);

    if (empty($nome) || empty($oggetto) || empty($corpo_html)) {
        $error = 'Nome, oggetto e corpo sono obbligatori';
    } else {
        if ($id) {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE email_templates SET
                    nome = :nome,
                    oggetto = :oggetto,
                    corpo_html = :corpo_html,
                    descrizione = :descrizione,
                    variabili_disponibili = :variabili,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        } else {
            // INSERT
            $stmt = $db->prepare("
                INSERT INTO email_templates (nome, oggetto, corpo_html, descrizione, variabili_disponibili)
                VALUES (:nome, :oggetto, :corpo_html, :descrizione, :variabili)
            ");
        }

        $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
        $stmt->bindValue(':oggetto', $oggetto, SQLITE3_TEXT);
        $stmt->bindValue(':corpo_html', $corpo_html, SQLITE3_TEXT);
        $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
        $stmt->bindValue(':variabili', $variabili, SQLITE3_TEXT);

        try {
            $stmt->execute();
            $message = $id ? 'Template aggiornato con successo' : 'Template creato con successo';
            logActivity($id ? 'update_email_template' : 'create_email_template', 'email_templates', $id, "Template: $nome");

            header('Location: email_templates.php');
            exit;
        } catch (Exception $e) {
            $error = 'Errore nel salvataggio: ' . $e->getMessage();
        }
    }
}

// Carica template per modifica
$edit_template = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM email_templates WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $edit_template = $result->fetchArray(SQLITE3_ASSOC);
}

// Lista template
$result = $db->query("SELECT * FROM email_templates ORDER BY nome ASC");
$templates = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $templates[] = $row;
}

include 'includes_header.php';
?>

<!-- Quill.js WYSIWYG Editor (gratuito, senza API key) -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--gray-700);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--gray-300);
    border-radius: 8px;
    font-size: 15px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--gray-600);
    font-size: 13px;
}

.template-card {
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.template-card:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 10px;
}

.template-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-900);
}

.template-description {
    color: var(--gray-600);
    margin-bottom: 10px;
    font-size: 14px;
}

.template-meta {
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: var(--gray-500);
}

.variable-tag {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-family: monospace;
    margin-right: 5px;
    margin-bottom: 5px;
}

.info-box {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 20px;
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo h($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        <strong>✗</strong> <?php echo h($error); ?>
    </div>
<?php endif; ?>

<?php if ($edit_template || isset($_GET['new'])): ?>
    <!-- Form Editor Template -->
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fas fa-<?php echo $edit_template ? 'edit' : 'plus'; ?>"></i>
                <?php echo $edit_template ? 'Modifica Template' : 'Nuovo Template'; ?>
            </h3>
        </div>

        <div class="info-box">
            <h4 style="margin-bottom: 10px; color: #856404;">
                <i class="fas fa-lightbulb"></i> Variabili Disponibili
            </h4>
            <p style="margin-bottom: 10px; color: #856404;">
                Puoi usare le seguenti variabili nei template. Verranno sostituite automaticamente con i valori reali:
            </p>
            <div>
                <span class="variable-tag">{{titolo}}</span>
                <span class="variable-tag">{{corpo}}</span>
                <span class="variable-tag">{{progetto}}</span>
                <span class="variable-tag">{{cliente}}</span>
                <span class="variable-tag">{{data_scadenza}}</span>
                <span class="variable-tag">{{priorita}}</span>
                <span class="variable-tag">{{stato}}</span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="template_id" value="<?php echo $edit_template['id'] ?? ''; ?>">

            <div class="form-group">
                <label>Nome Template *</label>
                <input type="text" name="nome" value="<?php echo h($edit_template['nome'] ?? ''); ?>" required
                       placeholder="alert_scadenza">
                <small>Identificatore univoco del template (usa underscore, senza spazi)</small>
            </div>

            <div class="form-group">
                <label>Oggetto Email *</label>
                <input type="text" name="oggetto" value="<?php echo h($edit_template['oggetto'] ?? ''); ?>" required
                       placeholder="Promemoria: {{progetto}}">
                <small>Puoi usare variabili nell'oggetto</small>
            </div>

            <div class="form-group">
                <label>Descrizione</label>
                <input type="text" name="descrizione" value="<?php echo h($edit_template['descrizione'] ?? ''); ?>"
                       placeholder="Breve descrizione del template">
            </div>

            <div class="form-group">
                <label>Variabili Disponibili</label>
                <input type="text" name="variabili_disponibili" value="<?php echo h($edit_template['variabili_disponibili'] ?? '{{titolo}}, {{corpo}}, {{progetto}}'); ?>"
                       placeholder="{{titolo}}, {{corpo}}, {{progetto}}">
                <small>Lista delle variabili disponibili per questo template (separati da virgola)</small>
            </div>

            <div class="form-group">
                <label>Corpo Email HTML *</label>
                <!-- Bottoni rapidi per inserire variabili -->
                <div style="margin-bottom: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn-var" onclick="insertVariable('{{titolo}}')">+ {{titolo}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{corpo}}')">+ {{corpo}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{progetto}}')">+ {{progetto}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{cliente}}')">+ {{cliente}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{data_scadenza}}')">+ {{data_scadenza}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{priorita}}')">+ {{priorita}}</button>
                    <button type="button" class="btn-var" onclick="insertVariable('{{stato}}')">+ {{stato}}</button>
                </div>
                <div id="editor-container" style="height: 500px; background: white; border: 2px solid var(--gray-300); border-radius: 8px;">
                    <?php echo $edit_template['corpo_html'] ?? ''; ?>
                </div>
                <input type="hidden" name="corpo_html" id="corpo_html">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="save_template" class="btn btn-success">
                    <i class="fas fa-save"></i> Salva Template
                </button>
                <a href="email_templates.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annulla
                </a>
            </div>
        </form>
    </div>

    <style>
    .btn-var {
        padding: 6px 12px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        color: #495057;
        font-family: monospace;
        transition: all 0.2s;
    }
    .btn-var:hover {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }
    </style>

    <script>
    // Inizializza Quill editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'Scrivi il contenuto del template email qui...'
    });

    // Sincronizza contenuto Quill con hidden input prima del submit
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('corpo_html').value = quill.root.innerHTML;
    });

    // Funzione per inserire variabili nell'editor
    function insertVariable(variable) {
        const range = quill.getSelection(true);
        quill.insertText(range.index, variable, { 'color': '#0066cc', 'bold': true });
        quill.setSelection(range.index + variable.length);
    }
    </script>
<?php else: ?>
    <!-- Lista Template -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope-open-text"></i> Template Email (<?php echo count($templates); ?>)</h3>
            <a href="email_templates.php?new=1" class="btn">
                <i class="fas fa-plus"></i> Nuovo Template
            </a>
        </div>

        <?php if (empty($templates)): ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--gray-500);">
                <i class="fas fa-envelope-open" style="font-size: 4em; opacity: 0.3; margin-bottom: 20px;"></i>
                <p style="font-size: 18px;">Nessun template email creato</p>
                <a href="email_templates.php?new=1" class="btn" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Crea il Primo Template
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($templates as $t): ?>
                <div class="template-card">
                    <div class="template-header">
                        <div style="flex: 1;">
                            <div class="template-title">
                                <i class="fas fa-envelope"></i>
                                <?php echo h($t['nome']); ?>
                            </div>
                            <?php if ($t['descrizione']): ?>
                                <div class="template-description"><?php echo h($t['descrizione']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <a href="email_templates.php?edit=<?php echo $t['id']; ?>" class="btn" style="padding: 8px 16px; font-size: 14px;">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                            <a href="email_templates.php?delete=<?php echo $t['id']; ?>"
                               onclick="return confirm('Eliminare questo template?')"
                               class="btn btn-danger" style="padding: 8px 16px; font-size: 14px;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>

                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <div style="margin-bottom: 10px;">
                            <strong style="color: var(--gray-700);">Oggetto:</strong>
                            <span style="color: var(--gray-600);"><?php echo h($t['oggetto']); ?></span>
                        </div>

                        <?php if ($t['variabili_disponibili']): ?>
                            <div>
                                <strong style="color: var(--gray-700);">Variabili:</strong><br>
                                <?php
                                $vars = explode(',', $t['variabili_disponibili']);
                                foreach ($vars as $var): ?>
                                    <span class="variable-tag"><?php echo h(trim($var)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="template-meta" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--gray-200);">
                        <span><i class="fas fa-clock"></i> Creato: <?php echo formatDateTime($t['created_at'], 'd/m/Y H:i'); ?></span>
                        <?php if ($t['updated_at'] && $t['updated_at'] !== $t['created_at']): ?>
                            <span><i class="fas fa-edit"></i> Modificato: <?php echo formatDateTime($t['updated_at'], 'd/m/Y H:i'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="smtp_settings.php" class="btn">
        <i class="fas fa-cog"></i> Configurazione SMTP
    </a>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
