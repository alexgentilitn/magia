<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Dashboard';

// Statistiche
$stats = [];

// Clienti
$result = $db->query("SELECT COUNT(*) as count FROM clienti WHERE stato = 'attivo'");
$stats['clienti_attivi'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

// Progetti
$result = $db->query("SELECT COUNT(*) as count FROM progetti WHERE stato IN ('pianificazione', 'in_corso')");
$stats['progetti_attivi'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

// Scadenze imminenti (prossimi 7 giorni)
$result = $db->query("SELECT COUNT(*) as count FROM scadenze WHERE stato = 'in_attesa' AND date(data_scadenza) <= date('now', '+7 days')");
$stats['scadenze_imminenti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

// Fatturato in corso
$result = $db->query("SELECT SUM(importo) as totale FROM progetti WHERE stato IN ('in_corso', 'completato')");
$stats['fatturato'] = $result->fetchArray(SQLITE3_ASSOC)['totale'] ?? 0;

// Ultimi clienti
$result = $db->query("SELECT * FROM clienti ORDER BY created_at DESC LIMIT 5");
$ultimi_clienti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $ultimi_clienti[] = $row;
}

// Progetti recenti
$result = $db->query("
    SELECT p.*, c.ragione_sociale
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    ORDER BY p.created_at DESC
    LIMIT 5
");
$progetti_recenti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_recenti[] = $row;
}

// Scadenze prossime
$result = $db->query("
    SELECT s.*, c.ragione_sociale, p.titolo as progetto_titolo
    FROM scadenze s
    LEFT JOIN progetti p ON s.progetto_id = p.id
    LEFT JOIN clienti c ON p.cliente_id = c.id
    WHERE s.stato = 'in_attesa'
    ORDER BY s.data_scadenza ASC
    LIMIT 5
");
$scadenze_prossime = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $scadenze_prossime[] = $row;
}

include 'includes_header.php';
?>

<!-- Statistiche -->
<div class="stats-grid">
    <div class="stat-card" style="border-left-color: #667eea;">
        <h4>Clienti Attivi</h4>
        <div class="stat-value"><?php echo $stats['clienti_attivi']; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #f39c12;">
        <h4>Progetti Attivi</h4>
        <div class="stat-value"><?php echo $stats['progetti_attivi']; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #e74c3c;">
        <h4>Scadenze Imminenti</h4>
        <div class="stat-value"><?php echo $stats['scadenze_imminenti']; ?></div>
    </div>
    <div class="stat-card" style="border-left-color: #27ae60;">
        <h4>Fatturato Totale</h4>
        <div class="stat-value"><?php echo formatMoney($stats['fatturato']); ?></div>
    </div>
</div>

<!-- Scadenze Imminenti -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Scadenze Imminenti</h3>
        <a href="scadenze.php" class="btn">Vedi Tutte</a>
    </div>
    <?php if (empty($scadenze_prossime)): ?>
        <p style="color: #999; text-align: center; padding: 20px;">Nessuna scadenza imminente</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descrizione</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Importo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scadenze_prossime as $scadenza): ?>
                    <tr>
                        <td><span class="badge badge-warning"><?php echo h($scadenza['tipo']); ?></span></td>
                        <td><?php echo h($scadenza['descrizione']); ?></td>
                        <td><?php echo h($scadenza['ragione_sociale']); ?></td>
                        <td><?php echo formatDate($scadenza['data_scadenza']); ?></td>
                        <td><strong><?php echo formatMoney($scadenza['importo']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Progetti Recenti -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-briefcase"></i> Progetti Recenti</h3>
        <a href="progetti.php" class="btn">Vedi Tutti</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Titolo</th>
                <th>Cliente</th>
                <th>Stato</th>
                <th>Importo</th>
                <th>Consegna</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($progetti_recenti as $progetto): ?>
                <tr>
                    <td><strong><?php echo h($progetto['titolo']); ?></strong></td>
                    <td><?php echo h($progetto['ragione_sociale']); ?></td>
                    <td>
                        <?php
                        $badge_class = match($progetto['stato']) {
                            'completato' => 'badge-success',
                            'in_corso' => 'badge-info',
                            'preventivo' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo h($progetto['stato']); ?></span>
                    </td>
                    <td><?php echo formatMoney($progetto['importo']); ?></td>
                    <td><?php echo formatDate($progetto['data_consegna']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Ultimi Clienti -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> Ultimi Clienti</h3>
        <a href="clienti.php" class="btn">Vedi Tutti</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Ragione Sociale</th>
                <th>Referente</th>
                <th>Email</th>
                <th>Città</th>
                <th>Aggiunto il</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ultimi_clienti as $cliente): ?>
                <tr>
                    <td><strong><?php echo h($cliente['ragione_sociale']); ?></strong></td>
                    <td><?php echo h($cliente['nome_referente'] . ' ' . $cliente['cognome_referente']); ?></td>
                    <td><?php echo h($cliente['email']); ?></td>
                    <td><?php echo h($cliente['citta']); ?></td>
                    <td><?php echo formatDate($cliente['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes_footer.php'; ?>
