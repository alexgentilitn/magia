<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Pipeline Vendite';

require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();

$message = '';
$error = '';

// Vista: pipeline o lista
$view = isset($_GET['view']) ? $_GET['view'] : 'pipeline';

// Crea nuovo lead
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crea_lead'])) {
    $azienda = trim($_POST['azienda']);
    $nome_contatto = trim($_POST['nome_contatto'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $stato = $_POST['stato'] ?? 'nuovo';
    $valore_stimato = !empty($_POST['valore_stimato']) ? (float)$_POST['valore_stimato'] : null;
    $probabilita = (int)($_POST['probabilita'] ?? 50);
    $fonte = $_POST['fonte'] ?? '';
    $assegnato_a = !empty($_POST['assegnato_a']) ? (int)$_POST['assegnato_a'] : null;

    if (empty($azienda)) {
        $error = "Nome azienda obbligatorio";
    } else {
        $stmt = $db->prepare("
            INSERT INTO leads (azienda, nome_contatto, email, telefono, stato, valore_stimato, probabilita, fonte, assegnato_a, created_by)
            VALUES (:azienda, :nome, :email, :telefono, :stato, :valore, :probabilita, :fonte, :assegnato, :created_by)
        ");

        $stmt->bindValue(':azienda', $azienda, SQLITE3_TEXT);
        $stmt->bindValue(':nome', $nome_contatto, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':telefono', $telefono, SQLITE3_TEXT);
        $stmt->bindValue(':stato', $stato, SQLITE3_TEXT);
        $stmt->bindValue(':valore', $valore_stimato, SQLITE3_FLOAT);
        $stmt->bindValue(':probabilita', $probabilita, SQLITE3_INTEGER);
        $stmt->bindValue(':fonte', $fonte, SQLITE3_TEXT);
        $stmt->bindValue(':assegnato', $assegnato_a, SQLITE3_INTEGER);
        $stmt->bindValue(':created_by', $currentUser['id'], SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $message = "Lead creato con successo!";
            $_POST = [];
        } else {
            $error = "Errore nella creazione del lead";
        }
    }
}

// Converti lead in cliente
if (isset($_GET['converti']) && is_numeric($_GET['converti'])) {
    $lead_id = (int)$_GET['converti'];

    // Carica lead
    $lead = $db->query("SELECT * FROM leads WHERE id = $lead_id")->fetchArray(SQLITE3_ASSOC);

    if ($lead) {
        // Crea cliente
        $stmt = $db->prepare("
            INSERT INTO clienti (ragione_sociale, contatto, email, telefono, indirizzo, citta, provincia, cap, nazione, note, stato, created_at)
            VALUES (:azienda, :contatto, :email, :telefono, :indirizzo, :citta, :provincia, :cap, :nazione, :note, 'attivo', CURRENT_TIMESTAMP)
        ");

        $note = "Convertito da Lead #$lead_id\n" . ($lead['note'] ?? '');

        $stmt->bindValue(':azienda', $lead['azienda'], SQLITE3_TEXT);
        $stmt->bindValue(':contatto', $lead['nome_contatto'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $lead['email'], SQLITE3_TEXT);
        $stmt->bindValue(':telefono', $lead['telefono'], SQLITE3_TEXT);
        $stmt->bindValue(':indirizzo', $lead['indirizzo'], SQLITE3_TEXT);
        $stmt->bindValue(':citta', $lead['citta'], SQLITE3_TEXT);
        $stmt->bindValue(':provincia', $lead['provincia'], SQLITE3_TEXT);
        $stmt->bindValue(':cap', $lead['cap'], SQLITE3_TEXT);
        $stmt->bindValue(':nazione', $lead['nazione'], SQLITE3_TEXT);
        $stmt->bindValue(':note', $note, SQLITE3_TEXT);

        if ($stmt->execute()) {
            $cliente_id = $db->lastInsertRowID();

            // Aggiorna lead
            $db->exec("
                UPDATE leads
                SET stato = 'vinto',
                    converted_to_cliente_id = $cliente_id,
                    converted_at = CURRENT_TIMESTAMP
                WHERE id = $lead_id
            ");

            header("Location: clienti.php?id=$cliente_id&message=lead_convertito");
            exit;
        }
    }
}

// Carica utenti
$users_result = $db->query("SELECT id, nome FROM users ORDER BY nome ASC");
$users = [];
while ($row = $users_result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

// Carica leads con filtri
$where = "1=1";
if (isset($_GET['assegnato']) && is_numeric($_GET['assegnato'])) {
    $where .= " AND l.assegnato_a = " . (int)$_GET['assegnato'];
}
if (isset($_GET['stato']) && !empty($_GET['stato'])) {
    $stato_filter = SQLite3::escapeString($_GET['stato']);
    $where .= " AND l.stato = '$stato_filter'";
}

$query = "
    SELECT l.*,
           u_ass.nome as assegnato_nome,
           u_cre.nome as creato_nome,
           (SELECT COUNT(*) FROM lead_activities WHERE lead_id = l.id) as attivita_count
    FROM leads l
    LEFT JOIN users u_ass ON l.assegnato_a = u_ass.id
    LEFT JOIN users u_cre ON l.created_by = u_cre.id
    WHERE $where
    ORDER BY l.created_at DESC
";

$result = $db->query($query);
$leads = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $leads[] = $row;
}

// Organizza per stato (pipeline)
$leads_by_stato = [
    'nuovo' => [],
    'qualificato' => [],
    'proposta' => [],
    'negoziazione' => [],
    'vinto' => [],
    'perso' => []
];

foreach ($leads as $lead) {
    if (isset($leads_by_stato[$lead['stato']])) {
        $leads_by_stato[$lead['stato']][] = $lead;
    }
}

// Statistiche
$stats_result = $db->query("
    SELECT
        COUNT(*) as totale,
        SUM(CASE WHEN stato = 'vinto' THEN 1 ELSE 0 END) as vinti,
        SUM(CASE WHEN stato = 'perso' THEN 1 ELSE 0 END) as persi,
        SUM(CASE WHEN stato IN ('nuovo', 'qualificato', 'proposta', 'negoziazione') THEN 1 ELSE 0 END) as aperti,
        SUM(CASE WHEN stato = 'vinto' THEN valore_stimato ELSE 0 END) as valore_vinto,
        SUM(CASE WHEN stato IN ('nuovo', 'qualificato', 'proposta', 'negoziazione') THEN valore_stimato ELSE 0 END) as valore_pipeline
    FROM leads
");
$stats = $stats_result->fetchArray(SQLITE3_ASSOC);

$conversion_rate = $stats['vinti'] + $stats['persi'] > 0
    ? round(($stats['vinti'] / ($stats['vinti'] + $stats['persi'])) * 100, 1)
    : 0;

include 'includes_header.php';
?>

<style>
.leads-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.view-switcher {
    display: flex;
    gap: 10px;
    background: #f8f9fa;
    padding: 5px;
    border-radius: 8px;
}

.view-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    color: #666;
}

.view-btn:hover {
    background: #e9ecef;
}

.view-btn.active {
    background: white;
    color: var(--accent);
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Stats Cards */
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

.stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.stat-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stat-card.danger {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    color: white;
}

.stat-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

/* Pipeline Board */
.pipeline-board {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.pipeline-column {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    min-height: 400px;
}

.pipeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.pipeline-title {
    font-weight: 700;
    font-size: 15px;
    color: #333;
}

.pipeline-count {
    background: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.lead-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid #dee2e6;
}

.lead-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.lead-card.probabilita-alta {
    border-left-color: #28a745;
}

.lead-card.probabilita-media {
    border-left-color: #ffc107;
}

.lead-card.probabilita-bassa {
    border-left-color: #dc3545;
}

.lead-azienda {
    font-weight: 600;
    font-size: 15px;
    color: #333;
    margin-bottom: 8px;
}

.lead-contatto {
    font-size: 13px;
    color: #666;
    margin-bottom: 10px;
}

.lead-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 12px;
    color: #666;
    margin-top: 10px;
}

.lead-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.lead-valore {
    font-size: 18px;
    font-weight: 700;
    color: #28a745;
    margin-top: 10px;
}

.lead-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.badge-probabilita {
    background: #e9ecef;
    color: #666;
}

/* Lista Lead */
.leads-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.lead-row {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    cursor: pointer;
    transition: all 0.2s;
}

.lead-row:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.lead-row-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.lead-row-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    font-size: 13px;
    color: #666;
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
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

@media (max-width: 768px) {
    .pipeline-board {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if ($message): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        ✓ <?php echo h($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="card" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24; margin-bottom: 20px;">
        ✗ <?php echo h($error); ?>
    </div>
<?php endif; ?>

<div class="leads-header">
    <h1 style="margin: 0;"><i class="fas fa-funnel-dollar"></i> Pipeline Vendite</h1>

    <div style="display: flex; gap: 10px; align-items: center;">
        <div class="view-switcher">
            <button class="view-btn <?php echo $view === 'pipeline' ? 'active' : ''; ?>" onclick="cambiaVista('pipeline')">
                <i class="fas fa-columns"></i> Pipeline
            </button>
            <button class="view-btn <?php echo $view === 'lista' ? 'active' : ''; ?>" onclick="cambiaVista('lista')">
                <i class="fas fa-list"></i> Lista
            </button>
        </div>

        <button class="btn" onclick="document.getElementById('modalCreaLead').classList.add('active')">
            <i class="fas fa-plus"></i> Nuovo Lead
        </button>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-value"><?php echo $stats['aperti']; ?></div>
        <div class="stat-label">Lead Aperti</div>
    </div>

    <div class="stat-card success">
        <div class="stat-value"><?php echo $stats['vinti']; ?></div>
        <div class="stat-label">Vinti</div>
    </div>

    <div class="stat-card danger">
        <div class="stat-value"><?php echo $stats['persi']; ?></div>
        <div class="stat-label">Persi</div>
    </div>

    <div class="stat-card warning">
        <div class="stat-value"><?php echo $conversion_rate; ?>%</div>
        <div class="stat-label">Tasso Conversione</div>
    </div>

    <div class="stat-card">
        <div class="stat-value" style="color: #28a745;">€<?php echo number_format($stats['valore_vinto'] ?? 0, 0, ',', '.'); ?></div>
        <div class="stat-label">Valore Vinto</div>
    </div>

    <div class="stat-card">
        <div class="stat-value" style="color: var(--accent);">€<?php echo number_format($stats['valore_pipeline'] ?? 0, 0, ',', '.'); ?></div>
        <div class="stat-label">Valore Pipeline</div>
    </div>
</div>

<!-- Vista Pipeline -->
<?php if ($view === 'pipeline'): ?>
    <div class="pipeline-board">
        <?php
        $stati_info = [
            'nuovo' => ['label' => '🆕 Nuovo', 'color' => '#6c757d'],
            'qualificato' => ['label' => '✅ Qualificato', 'color' => '#0066cc'],
            'proposta' => ['label' => '📄 Proposta Inviata', 'color' => '#fd7e14'],
            'negoziazione' => ['label' => '💬 In Negoziazione', 'color' => '#ffc107'],
            'vinto' => ['label' => '🎉 Vinto', 'color' => '#28a745'],
            'perso' => ['label' => '❌ Perso', 'color' => '#dc3545']
        ];

        foreach ($stati_info as $stato_key => $stato_data):
            $leads_stato = $leads_by_stato[$stato_key];
            $valore_totale = array_sum(array_column($leads_stato, 'valore_stimato'));
        ?>
            <div class="pipeline-column">
                <div class="pipeline-header">
                    <div class="pipeline-title" style="color: <?php echo $stato_data['color']; ?>;">
                        <?php echo $stato_data['label']; ?>
                    </div>
                    <div class="pipeline-count"><?php echo count($leads_stato); ?></div>
                </div>

                <div style="font-size: 12px; color: #666; margin-bottom: 15px;">
                    Valore: <strong>€<?php echo number_format($valore_totale, 0, ',', '.'); ?></strong>
                </div>

                <?php foreach ($leads_stato as $lead):
                    $prob_class = '';
                    if ($lead['probabilita'] >= 70) $prob_class = 'probabilita-alta';
                    elseif ($lead['probabilita'] >= 40) $prob_class = 'probabilita-media';
                    else $prob_class = 'probabilita-bassa';
                ?>
                    <div class="lead-card <?php echo $prob_class; ?>" onclick="apriLead(<?php echo $lead['id']; ?>)">
                        <div class="lead-azienda"><?php echo h($lead['azienda']); ?></div>

                        <?php if ($lead['nome_contatto']): ?>
                            <div class="lead-contatto">
                                <i class="fas fa-user"></i> <?php echo h($lead['nome_contatto']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($lead['valore_stimato']): ?>
                            <div class="lead-valore">
                                €<?php echo number_format($lead['valore_stimato'], 0, ',', '.'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="lead-meta">
                            <?php if ($lead['email']): ?>
                                <div class="lead-meta-row">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo h($lead['email']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($lead['telefono']): ?>
                                <div class="lead-meta-row">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo h($lead['telefono']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($lead['assegnato_nome']): ?>
                                <div class="lead-meta-row">
                                    <i class="fas fa-user-tag"></i>
                                    <span><?php echo h($lead['assegnato_nome']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="lead-badges">
                            <span class="badge badge-probabilita">
                                <?php echo $lead['probabilita']; ?>% probabilità
                            </span>

                            <?php if ($lead['attivita_count'] > 0): ?>
                                <span class="badge" style="background: #e9ecef; color: #666;">
                                    <i class="fas fa-history"></i> <?php echo $lead['attivita_count']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($leads_stato)): ?>
                    <div style="text-align: center; padding: 40px 20px; color: #adb5bd;">
                        <i class="fas fa-inbox" style="font-size: 2em; opacity: 0.3; margin-bottom: 10px;"></i>
                        <p style="margin: 0; font-size: 13px;">Nessun lead</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <!-- Vista Lista -->
    <div class="leads-list">
        <?php foreach ($leads as $lead): ?>
            <div class="lead-row" onclick="apriLead(<?php echo $lead['id']; ?>)">
                <div class="lead-row-header">
                    <div>
                        <h3 style="margin: 0 0 5px 0;"><?php echo h($lead['azienda']); ?></h3>
                        <?php if ($lead['nome_contatto']): ?>
                            <div style="color: #666; font-size: 14px;">
                                <i class="fas fa-user"></i> <?php echo h($lead['nome_contatto']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="text-align: right;">
                        <?php if ($lead['valore_stimato']): ?>
                            <div style="font-size: 24px; font-weight: 700; color: #28a745;">
                                €<?php echo number_format($lead['valore_stimato'], 0, ',', '.'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="badge" style="margin-top: 5px;">
                            <?php echo $lead['probabilita']; ?>% probabilità
                        </div>
                    </div>
                </div>

                <div class="lead-row-meta">
                    <div>
                        <strong>Stato:</strong>
                        <?php
                        $stati_labels = [
                            'nuovo' => '🆕 Nuovo',
                            'qualificato' => '✅ Qualificato',
                            'proposta' => '📄 Proposta',
                            'negoziazione' => '💬 Negoziazione',
                            'vinto' => '🎉 Vinto',
                            'perso' => '❌ Perso'
                        ];
                        echo $stati_labels[$lead['stato']] ?? $lead['stato'];
                        ?>
                    </div>
                    <div>
                        <strong>Assegnato a:</strong> <?php echo $lead['assegnato_nome'] ?? '-'; ?>
                    </div>
                    <div>
                        <strong>Email:</strong> <?php echo $lead['email'] ?? '-'; ?>
                    </div>
                    <div>
                        <strong>Telefono:</strong> <?php echo $lead['telefono'] ?? '-'; ?>
                    </div>
                    <div>
                        <strong>Fonte:</strong> <?php echo $lead['fonte'] ?: '-'; ?>
                    </div>
                    <div>
                        <strong>Attività:</strong> <?php echo $lead['attivita_count']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($leads)): ?>
            <div class="card" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-funnel-dollar" style="font-size: 4em; opacity: 0.2; margin-bottom: 20px;"></i>
                <p style="font-size: 18px; color: #666; margin: 0;">Nessun lead trovato</p>
                <button class="btn" style="margin-top: 20px;" onclick="document.getElementById('modalCreaLead').classList.add('active')">
                    <i class="fas fa-plus"></i> Crea il Primo Lead
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Modal Crea Lead -->
<div class="modal" id="modalCreaLead">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;"><i class="fas fa-plus-circle"></i> Nuovo Lead</h2>
            <button class="btn btn-secondary" onclick="document.getElementById('modalCreaLead').classList.remove('active')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Azienda *</label>
                    <input type="text" name="azienda" required placeholder="Nome Azienda">
                </div>

                <div class="form-group">
                    <label>Nome Contatto</label>
                    <input type="text" name="nome_contatto" placeholder="Mario Rossi">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="contatto@azienda.it">
                </div>

                <div class="form-group">
                    <label>Telefono</label>
                    <input type="tel" name="telefono" placeholder="+39 123 4567890">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Valore Stimato (€)</label>
                    <input type="number" name="valore_stimato" step="0.01" placeholder="10000">
                </div>

                <div class="form-group">
                    <label>Probabilità (%)</label>
                    <input type="number" name="probabilita" min="0" max="100" value="50">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Stato</label>
                    <select name="stato">
                        <option value="nuovo">🆕 Nuovo</option>
                        <option value="qualificato">✅ Qualificato</option>
                        <option value="proposta">📄 Proposta Inviata</option>
                        <option value="negoziazione">💬 In Negoziazione</option>
                        <option value="vinto">🎉 Vinto</option>
                        <option value="perso">❌ Perso</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fonte Lead</label>
                    <input type="text" name="fonte" placeholder="Es: Sito Web, Referral, LinkedIn">
                </div>
            </div>

            <div class="form-group">
                <label>Assegna a</label>
                <select name="assegnato_a">
                    <option value="">-- Non assegnato --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo h($u['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" name="crea_lead" class="btn btn-success">
                    <i class="fas fa-check"></i> Crea Lead
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalCreaLead').classList.remove('active')">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function cambiaVista(vista) {
    window.location.href = 'leads.php?view=' + vista;
}

function apriLead(leadId) {
    window.location.href = 'lead_detail.php?id=' + leadId;
}

// Chiudi modal cliccando fuori
document.getElementById('modalCreaLead').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});
</script>

<?php include 'includes_footer.php'; ?>
