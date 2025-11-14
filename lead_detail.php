<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$lead_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lead_id == 0) {
    header('Location: leads.php');
    exit;
}

// Carica lead
$stmt = $db->prepare("
    SELECT l.*, u_ass.nome as assegnato_nome, u_cre.nome as creato_nome
    FROM leads l
    LEFT JOIN users u_ass ON l.assegnato_a = u_ass.id
    LEFT JOIN users u_cre ON l.created_by = u_cre.id
    WHERE l.id = :id
");
$stmt->bindValue(':id', $lead_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$lead = $result->fetchArray(SQLITE3_ASSOC);

if (!$lead) {
    header('Location: leads.php');
    exit;
}

$pageTitle = $lead['azienda'];

$message = '';

// Aggiungi attività
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi_attivita'])) {
    $tipo = $_POST['tipo'];
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['descrizione'] ?? '');
    $esito = trim($_POST['esito'] ?? '');

    $stmt = $db->prepare("
        INSERT INTO lead_activities (lead_id, tipo, titolo, descrizione, esito, user_id)
        VALUES (:lead_id, :tipo, :titolo, :descrizione, :esito, :user_id)
    ");
    $stmt->bindValue(':lead_id', $lead_id, SQLITE3_INTEGER);
    $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
    $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
    $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
    $stmt->bindValue(':esito', $esito, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
    $stmt->execute();

    $message = "Attività registrata";
    $_POST = [];
}

// Aggiorna lead
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiorna_lead'])) {
    $stmt = $db->prepare("
        UPDATE leads
        SET azienda = :azienda,
            nome_contatto = :nome,
            email = :email,
            telefono = :telefono,
            stato = :stato,
            probabilita = :prob,
            valore_stimato = :valore,
            note = :note,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");

    $stmt->bindValue(':azienda', trim($_POST['azienda']), SQLITE3_TEXT);
    $stmt->bindValue(':nome', trim($_POST['nome_contatto']), SQLITE3_TEXT);
    $stmt->bindValue(':email', trim($_POST['email']), SQLITE3_TEXT);
    $stmt->bindValue(':telefono', trim($_POST['telefono']), SQLITE3_TEXT);
    $stmt->bindValue(':stato', $_POST['stato'], SQLITE3_TEXT);
    $stmt->bindValue(':prob', (int)$_POST['probabilita'], SQLITE3_INTEGER);
    $stmt->bindValue(':valore', !empty($_POST['valore_stimato']) ? (float)$_POST['valore_stimato'] : null, SQLITE3_FLOAT);
    $stmt->bindValue(':note', trim($_POST['note']), SQLITE3_TEXT);
    $stmt->bindValue(':id', $lead_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        $message = "Lead aggiornato";
        // Ricarica
        $result = $db->query("SELECT l.*, u_ass.nome as assegnato_nome FROM leads l LEFT JOIN users u_ass ON l.assegnato_a = u_ass.id WHERE l.id = $lead_id");
        $lead = $result->fetchArray(SQLITE3_ASSOC);
    }
}

// Carica attività
$activities = [];
$result = $db->query("
    SELECT a.*, u.nome as user_nome
    FROM lead_activities a
    LEFT JOIN users u ON a.user_id = u.id
    WHERE a.lead_id = $lead_id
    ORDER BY a.data_attivita DESC
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $activities[] = $row;
}

include 'includes_header.php';
?>

<style>
.lead-detail-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.section-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid {
    display: grid;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.activity-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid var(--accent);
}

.activity-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
}

@media (max-width: 1024px) {
    .lead-detail-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; color: #155724; margin-bottom: 20px;">✓ <?php echo h($message); ?></div>
<?php endif; ?>

<div style="margin-bottom: 20px;">
    <a href="leads.php"><i class="fas fa-arrow-left"></i> Torna alla Pipeline</a>
</div>

<div class="lead-detail-container">
    <div>
        <div class="section-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0 0 10px 0;"><?php echo h($lead['azienda']); ?></h1>
                    <?php if ($lead['nome_contatto']): ?>
                        <div style="color: #666;"><i class="fas fa-user"></i> <?php echo h($lead['nome_contatto']); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($lead['stato'] !== 'vinto' && $lead['stato'] !== 'perso'): ?>
                    <a href="?converti=<?php echo $lead_id; ?>" class="btn btn-success" onclick="return confirm('Convertire in cliente?')">
                        <i class="fas fa-check-circle"></i> Converti in Cliente
                    </a>
                <?php endif; ?>
            </div>

            <button class="btn" onclick="document.getElementById('formModifica').style.display='block'; this.style.display='none';">
                <i class="fas fa-edit"></i> Modifica
            </button>
        </div>

        <div class="section-card" id="formModifica" style="display: none;">
            <h3 class="section-title">Modifica Lead</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Azienda *</label>
                    <input type="text" name="azienda" value="<?php echo h($lead['azienda']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Contatto</label>
                        <input type="text" name="nome_contatto" value="<?php echo h($lead['nome_contatto']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo h($lead['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Telefono</label>
                        <input type="tel" name="telefono" value="<?php echo h($lead['telefono']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Valore Stimato (€)</label>
                        <input type="number" name="valore_stimato" step="0.01" value="<?php echo $lead['valore_stimato']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Stato</label>
                        <select name="stato">
                            <option value="nuovo" <?php echo $lead['stato']==='nuovo'?'selected':''; ?>>🆕 Nuovo</option>
                            <option value="qualificato" <?php echo $lead['stato']==='qualificato'?'selected':''; ?>>✅ Qualificato</option>
                            <option value="proposta" <?php echo $lead['stato']==='proposta'?'selected':''; ?>>📄 Proposta</option>
                            <option value="negoziazione" <?php echo $lead['stato']==='negoziazione'?'selected':''; ?>>💬 Negoziazione</option>
                            <option value="vinto" <?php echo $lead['stato']==='vinto'?'selected':''; ?>>🎉 Vinto</option>
                            <option value="perso" <?php echo $lead['stato']==='perso'?'selected':''; ?>>❌ Perso</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Probabilità (%)</label>
                        <input type="number" name="probabilita" min="0" max="100" value="<?php echo $lead['probabilita']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Note</label>
                    <textarea name="note" rows="4"><?php echo h($lead['note']); ?></textarea>
                </div>

                <button type="submit" name="aggiorna_lead" class="btn btn-success">
                    <i class="fas fa-save"></i> Salva
                </button>
            </form>
        </div>

        <div class="section-card">
            <h3 class="section-title"><i class="fas fa-history"></i> Attività (<?php echo count($activities); ?>)</h3>

            <form method="POST" style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="margin-top: 0;">Registra Nuova Attività</h4>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="tipo" required>
                            <option value="chiamata">📞 Chiamata</option>
                            <option value="email">📧 Email</option>
                            <option value="riunione">👥 Riunione</option>
                            <option value="nota">📝 Nota</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Titolo *</label>
                        <input type="text" name="titolo" required placeholder="Es: Chiamata di follow-up">
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrizione</label>
                    <textarea name="descrizione" rows="3" placeholder="Cosa è successo..."></textarea>
                </div>

                <div class="form-group">
                    <label>Esito</label>
                    <input type="text" name="esito" placeholder="Es: Interessato, da ricontattare">
                </div>

                <button type="submit" name="aggiungi_attivita" class="btn">
                    <i class="fas fa-plus"></i> Registra Attività
                </button>
            </form>

            <?php foreach ($activities as $act): ?>
                <div class="activity-item">
                    <div class="activity-header">
                        <span>
                            <?php
                            $icons = ['chiamata' => '📞', 'email' => '📧', 'riunione' => '👥', 'nota' => '📝'];
                            echo ($icons[$act['tipo']] ?? '📋') . ' ' . h($act['titolo']);
                            ?>
                        </span>
                        <span style="font-size: 12px; color: #666; font-weight: normal;">
                            <?php echo formatDateTime($act['data_attivita']); ?>
                        </span>
                    </div>

                    <?php if ($act['descrizione']): ?>
                        <div style="margin-bottom: 10px; color: #333;"><?php echo nl2br(h($act['descrizione'])); ?></div>
                    <?php endif; ?>

                    <?php if ($act['esito']): ?>
                        <div style="font-size: 13px; color: #666;">
                            <strong>Esito:</strong> <?php echo h($act['esito']); ?>
                        </div>
                    <?php endif; ?>

                    <div style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                        <i class="fas fa-user"></i> <?php echo h($act['user_nome']); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($activities)): ?>
                <div style="text-align: center; padding: 40px; color: #adb5bd;">
                    <i class="fas fa-history" style="font-size: 2em; opacity: 0.3;"></i>
                    <p>Nessuna attività registrata</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="section-card">
            <h3 class="section-title"><i class="fas fa-info-circle"></i> Informazioni</h3>

            <div class="info-grid">
                <?php if ($lead['valore_stimato']): ?>
                    <div class="info-item">
                        <span>Valore Stimato</span>
                        <strong style="color: #28a745; font-size: 18px;">€<?php echo number_format($lead['valore_stimato'], 0, ',', '.'); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <span>Probabilità</span>
                    <strong><?php echo $lead['probabilita']; ?>%</strong>
                </div>

                <div class="info-item">
                    <span>Stato</span>
                    <strong>
                        <?php
                        $stati = ['nuovo' => '🆕 Nuovo', 'qualificato' => '✅ Qualificato', 'proposta' => '📄 Proposta', 'negoziazione' => '💬 Negoziazione', 'vinto' => '🎉 Vinto', 'perso' => '❌ Perso'];
                        echo $stati[$lead['stato']] ?? $lead['stato'];
                        ?>
                    </strong>
                </div>

                <?php if ($lead['email']): ?>
                    <div class="info-item">
                        <span>Email</span>
                        <a href="mailto:<?php echo h($lead['email']); ?>"><?php echo h($lead['email']); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($lead['telefono']): ?>
                    <div class="info-item">
                        <span>Telefono</span>
                        <a href="tel:<?php echo h($lead['telefono']); ?>"><?php echo h($lead['telefono']); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($lead['assegnato_nome']): ?>
                    <div class="info-item">
                        <span>Assegnato a</span>
                        <strong><?php echo h($lead['assegnato_nome']); ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($lead['fonte']): ?>
                    <div class="info-item">
                        <span>Fonte</span>
                        <span><?php echo h($lead['fonte']); ?></span>
                    </div>
                <?php endif; ?>

                <div class="info-item">
                    <span>Creato da</span>
                    <span><?php echo h($lead['creato_nome']); ?></span>
                </div>

                <div class="info-item">
                    <span>Data Creazione</span>
                    <span><?php echo formatDate($lead['created_at']); ?></span>
                </div>

                <?php if ($lead['converted_at']): ?>
                    <div class="info-item">
                        <span>Convertito il</span>
                        <span><?php echo formatDateTime($lead['converted_at']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($lead['note']): ?>
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-sticky-note"></i> Note</h3>
                <div style="line-height: 1.8;"><?php echo nl2br(h($lead['note'])); ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes_footer.php'; ?>
