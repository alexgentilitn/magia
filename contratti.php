<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Contratti';

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$message = '';

// Crea contratto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crea_contratto'])) {
    $stmt = $db->prepare("
        INSERT INTO contratti (cliente_id, titolo, descrizione, tipo, valore, data_inizio, data_scadenza, rinnovo_automatico, frequenza_rinnovo, giorni_preavviso, stato)
        VALUES (:cliente, :titolo, :desc, :tipo, :valore, :inizio, :scadenza, :rinnovo, :freq, :giorni, :stato)
    ");

    $stmt->bindValue(':cliente', (int)$_POST['cliente_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':titolo', trim($_POST['titolo']), SQLITE3_TEXT);
    $stmt->bindValue(':desc', trim($_POST['descrizione']), SQLITE3_TEXT);
    $stmt->bindValue(':tipo', $_POST['tipo'], SQLITE3_TEXT);
    $stmt->bindValue(':valore', !empty($_POST['valore']) ? (float)$_POST['valore'] : null, SQLITE3_FLOAT);
    $stmt->bindValue(':inizio', $_POST['data_inizio'], SQLITE3_TEXT);
    $stmt->bindValue(':scadenza', $_POST['data_scadenza'], SQLITE3_TEXT);
    $stmt->bindValue(':rinnovo', isset($_POST['rinnovo_automatico']) ? 1 : 0, SQLITE3_INTEGER);
    $stmt->bindValue(':freq', $_POST['frequenza_rinnovo'] ?? '', SQLITE3_TEXT);
    $stmt->bindValue(':giorni', (int)($_POST['giorni_preavviso'] ?? 30), SQLITE3_INTEGER);
    $stmt->bindValue(':stato', 'attivo', SQLITE3_TEXT);

    if ($stmt->execute()) {
        $message = "Contratto creato con successo!";
    }
}

// Carica clienti
$clienti = [];
$result = $db->query("SELECT id, ragione_sociale FROM clienti WHERE stato = 'attivo' ORDER BY ragione_sociale ASC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $clienti[] = $row;
}

// Carica contratti
$query = "
    SELECT c.*, cl.ragione_sociale
    FROM contratti c
    LEFT JOIN clienti cl ON c.cliente_id = cl.id
    ORDER BY c.data_scadenza ASC
";
$result = $db->query($query);
$contratti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $contratti[] = $row;
}

// Contratti in scadenza (prossimi 30 giorni)
$oggi = date('Y-m-d');
$tra_30_giorni = date('Y-m-d', strtotime('+30 days'));

$in_scadenza = array_filter($contratti, function($c) use ($oggi, $tra_30_giorni) {
    return $c['stato'] === 'attivo' && $c['data_scadenza'] >= $oggi && $c['data_scadenza'] <= $tra_30_giorni;
});

include 'includes_header.php';
?>

<style>
.contratti-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.contratti-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contratto-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 4px solid #28a745;
}

.contratto-card.scadenza-vicina {
    border-left-color: #ffc107;
}

.contratto-card.scaduto {
    border-left-color: #dc3545;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    max-width: 700px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.form-group {
    margin-bottom: 20px;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; color: #155724; margin-bottom: 20px;">✓ <?php echo h($message); ?></div>
<?php endif; ?>

<div class="contratti-header">
    <h1><i class="fas fa-file-contract"></i> Gestione Contratti</h1>
    <button class="btn" onclick="document.getElementById('modalCreaContratto').classList.add('active')">
        <i class="fas fa-plus"></i> Nuovo Contratto
    </button>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div style="font-size: 32px; font-weight: 700;"><?php echo count(array_filter($contratti, fn($c) => $c['stato'] === 'attivo')); ?></div>
        <div style="font-size: 14px; color: #666;">Contratti Attivi</div>
    </div>

    <div class="stat-card warning">
        <div style="font-size: 32px; font-weight: 700;"><?php echo count($in_scadenza); ?></div>
        <div style="font-size: 14px;">In Scadenza (30gg)</div>
    </div>

    <div class="stat-card">
        <div style="font-size: 32px; font-weight: 700; color: #28a745;">
            €<?php echo number_format(array_sum(array_column(array_filter($contratti, fn($c) => $c['stato'] === 'attivo'), 'valore')) ?? 0, 0, ',', '.'); ?>
        </div>
        <div style="font-size: 14px; color: #666;">Valore Totale Attivo</div>
    </div>
</div>

<?php if (!empty($in_scadenza)): ?>
    <div class="card" style="background: #fff3cd; border-color: #ffc107; color: #856404; margin-bottom: 20px;">
        <strong>⚠️ Attenzione:</strong> <?php echo count($in_scadenza); ?> contratto/i in scadenza nei prossimi 30 giorni
    </div>
<?php endif; ?>

<div class="contratti-list">
    <?php foreach ($contratti as $contratto):
        $scadenza_date = new DateTime($contratto['data_scadenza']);
        $oggi_date = new DateTime();
        $diff = $oggi_date->diff($scadenza_date);
        $giorni_diff = (int)$diff->format('%R%a');

        $class = '';
        if ($giorni_diff < 0) $class = 'scaduto';
        elseif ($giorni_diff <= 30) $class = 'scadenza-vicina';
    ?>
        <div class="contratto-card <?php echo $class; ?>">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0 0 5px 0;"><?php echo h($contratto['titolo']); ?></h3>
                    <div style="color: #666; font-size: 14px;">
                        <i class="fas fa-building"></i> <?php echo h($contratto['ragione_sociale']); ?>
                    </div>
                </div>

                <?php if ($contratto['valore']): ?>
                    <div style="font-size: 24px; font-weight: 700; color: #28a745;">
                        €<?php echo number_format($contratto['valore'], 0, ',', '.'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 13px; color: #666;">
                <div>
                    <strong>Tipo:</strong> <?php echo h($contratto['tipo']); ?>
                </div>
                <div>
                    <strong>Inizio:</strong> <?php echo formatDate($contratto['data_inizio']); ?>
                </div>
                <div>
                    <strong>Scadenza:</strong>
                    <span style="<?php echo $giorni_diff < 30 ? 'color: #dc3545; font-weight: 700;' : ''; ?>">
                        <?php echo formatDate($contratto['data_scadenza']); ?>
                        <?php if ($giorni_diff >= 0 && $giorni_diff <= 30): ?>
                            (<?php echo $giorni_diff; ?> giorni)
                        <?php endif; ?>
                    </span>
                </div>
                <div>
                    <strong>Stato:</strong>
                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: <?php echo $contratto['stato'] === 'attivo' ? '#d4edda' : '#f8d7da'; ?>;">
                        <?php echo ucfirst($contratto['stato']); ?>
                    </span>
                </div>
                <?php if ($contratto['rinnovo_automatico']): ?>
                    <div>
                        <strong>Rinnovo:</strong> ✅ Automatico (<?php echo h($contratto['frequenza_rinnovo']); ?>)
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($contratto['descrizione']): ?>
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6; color: #666;">
                    <?php echo nl2br(h($contratto['descrizione'])); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($contratti)): ?>
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-file-contract" style="font-size: 4em; opacity: 0.2; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; color: #666; margin: 0;">Nessun contratto trovato</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Crea Contratto -->
<div class="modal" id="modalCreaContratto">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;">Nuovo Contratto</h2>
            <button class="btn btn-secondary" onclick="document.getElementById('modalCreaContratto').classList.remove('active')">✕</button>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Cliente *</label>
                <select name="cliente_id" required>
                    <option value="">-- Seleziona Cliente --</option>
                    <?php foreach ($clienti as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo h($c['ragione_sociale']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Titolo Contratto *</label>
                <input type="text" name="titolo" required placeholder="Es: Contratto Manutenzione Annuale">
            </div>

            <div class="form-group">
                <label>Descrizione</label>
                <textarea name="descrizione" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="manutenzione">Manutenzione</option>
                        <option value="abbonamento">Abbonamento</option>
                        <option value="consulenza">Consulenza</option>
                        <option value="licenza">Licenza</option>
                        <option value="altro">Altro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Valore (€)</label>
                    <input type="number" name="valore" step="0.01">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Data Inizio *</label>
                    <input type="date" name="data_inizio" required>
                </div>

                <div class="form-group">
                    <label>Data Scadenza *</label>
                    <input type="date" name="data_scadenza" required>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="rinnovo_automatico" value="1" style="width: auto;">
                    Rinnovo Automatico
                </label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Frequenza Rinnovo</label>
                    <select name="frequenza_rinnovo">
                        <option value="">-- Seleziona --</option>
                        <option value="mensile">Mensile</option>
                        <option value="trimestrale">Trimestrale</option>
                        <option value="semestrale">Semestrale</option>
                        <option value="annuale">Annuale</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Giorni Preavviso</label>
                    <input type="number" name="giorni_preavviso" value="30">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" name="crea_contratto" class="btn btn-success">
                    <i class="fas fa-check"></i> Crea Contratto
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalCreaContratto').classList.remove('active')">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('modalCreaContratto').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>

<?php include 'includes_footer.php'; ?>
