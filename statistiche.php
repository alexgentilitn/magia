<?php
require_once 'config.php';
requireLogin();
$pageTitle = 'Statistiche e Report';

// Statistiche generali
$stats = [];

// Totali
$result = $db->query("SELECT COUNT(*) as count FROM clienti");
$stats['totale_clienti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM clienti WHERE stato = 'attivo'");
$stats['clienti_attivi'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT COUNT(*) as count FROM progetti");
$stats['totale_progetti'] = $result->fetchArray(SQLITE3_ASSOC)['count'];

$result = $db->query("SELECT SUM(importo) as totale FROM progetti");
$stats['fatturato_totale'] = $result->fetchArray(SQLITE3_ASSOC)['totale'] ?? 0;

$result = $db->query("SELECT SUM(importo) as totale FROM progetti WHERE stato IN ('in_corso', 'pianificazione')");
$stats['fatturato_attivo'] = $result->fetchArray(SQLITE3_ASSOC)['totale'] ?? 0;

$result = $db->query("SELECT SUM(importo) as totale FROM scadenze WHERE stato = 'in_attesa'");
$stats['da_incassare'] = $result->fetchArray(SQLITE3_ASSOC)['totale'] ?? 0;

$result = $db->query("SELECT SUM(importo) as totale FROM scadenze WHERE stato = 'pagato'");
$stats['gia_incassato'] = $result->fetchArray(SQLITE3_ASSOC)['totale'] ?? 0;

// Progetti per stato
$progetti_stato = [];
$result = $db->query("
    SELECT stato, COUNT(*) as count, SUM(importo) as totale
    FROM progetti
    GROUP BY stato
    ORDER BY count DESC
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_stato[] = $row;
}

// Progetti per tipo
$progetti_tipo = [];
$result = $db->query("
    SELECT tipo_progetto, COUNT(*) as count, SUM(importo) as totale
    FROM progetti
    GROUP BY tipo_progetto
    ORDER BY count DESC
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_tipo[] = $row;
}

// Progetti per priorità
$progetti_priorita = [];
$result = $db->query("
    SELECT priorita, COUNT(*) as count
    FROM progetti
    WHERE stato IN ('in_corso', 'pianificazione')
    GROUP BY priorita
    ORDER BY
        CASE priorita
            WHEN 'alta' THEN 1
            WHEN 'media' THEN 2
            WHEN 'bassa' THEN 3
        END
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti_priorita[] = $row;
}

// Scadenze per stato
$scadenze_stato = [];
$result = $db->query("
    SELECT stato, COUNT(*) as count, SUM(importo) as totale
    FROM scadenze
    GROUP BY stato
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $scadenze_stato[] = $row;
}

// Top 5 clienti per fatturato
$top_clienti = [];
$result = $db->query("
    SELECT c.ragione_sociale, COUNT(p.id) as num_progetti, SUM(p.importo) as totale
    FROM clienti c
    LEFT JOIN progetti p ON c.id = p.cliente_id
    GROUP BY c.id
    HAVING totale > 0
    ORDER BY totale DESC
    LIMIT 5
");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $top_clienti[] = $row;
}

// Performance ore (progetti completati)
$result = $db->query("
    SELECT
        SUM(ore_stimate) as ore_stimate_totali,
        SUM(ore_effettive) as ore_effettive_totali
    FROM progetti
    WHERE stato = 'completato' AND ore_stimate > 0 AND ore_effettive > 0
");
$ore_stats = $result->fetchArray(SQLITE3_ASSOC);

include 'includes_header.php';
?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    border-left: 4px solid #3498db;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h4 {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    color: #95a5a6;
}

.chart-container {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.chart-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.bar-chart {
    margin: 20px 0;
}

.bar-item {
    margin-bottom: 15px;
}

.bar-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
    color: #555;
}

.bar-bg {
    height: 30px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
    position: relative;
}

.bar-fill {
    height: 100%;
    background: #3498db;
    display: flex;
    align-items: center;
    padding: 0 10px;
    color: white;
    font-weight: 600;
    font-size: 13px;
    transition: width 0.5s;
}

.two-column-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .two-column-grid {
        grid-template-columns: 1fr;
    }
}

.table-simple {
    width: 100%;
    border-collapse: collapse;
}

.table-simple th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
}

.table-simple td {
    padding: 12px;
    border-bottom: 1px solid #f1f3f5;
}

.table-simple tr:hover {
    background: #f8f9fa;
}

.progress-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: conic-gradient(#27ae60 var(--progress), #e9ecef 0);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.progress-circle::before {
    content: '';
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-value {
    position: relative;
    z-index: 1;
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}
</style>

<!-- Statistiche Generali -->
<div class="stats-grid">
    <div class="stat-card" style="border-left-color: #3498db;">
        <h4>Clienti Totali</h4>
        <div class="stat-value"><?php echo $stats['totale_clienti']; ?></div>
        <div class="stat-label"><?php echo $stats['clienti_attivi']; ?> attivi</div>
    </div>

    <div class="stat-card" style="border-left-color: #9b59b6;">
        <h4>Progetti Totali</h4>
        <div class="stat-value"><?php echo $stats['totale_progetti']; ?></div>
        <div class="stat-label">In archivio</div>
    </div>

    <div class="stat-card" style="border-left-color: #27ae60;">
        <h4>Fatturato Totale</h4>
        <div class="stat-value"><?php echo formatMoney($stats['fatturato_totale']); ?></div>
        <div class="stat-label">Tutti i progetti</div>
    </div>

    <div class="stat-card" style="border-left-color: #f39c12;">
        <h4>Fatturato Attivo</h4>
        <div class="stat-value"><?php echo formatMoney($stats['fatturato_attivo']); ?></div>
        <div class="stat-label">In corso e pianificati</div>
    </div>

    <div class="stat-card" style="border-left-color: #e74c3c;">
        <h4>Da Incassare</h4>
        <div class="stat-value"><?php echo formatMoney($stats['da_incassare']); ?></div>
        <div class="stat-label">Scadenze in attesa</div>
    </div>

    <div class="stat-card" style="border-left-color: #1abc9c;">
        <h4>Già Incassato</h4>
        <div class="stat-value"><?php echo formatMoney($stats['gia_incassato']); ?></div>
        <div class="stat-label">Scadenze pagate</div>
    </div>
</div>

<div class="two-column-grid">
    <!-- Progetti per Stato -->
    <div class="chart-container">
        <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Progetti per Stato</h3>
        <div class="bar-chart">
            <?php
            $max_count = !empty($progetti_stato) ? max(array_column($progetti_stato, 'count')) : 1;
            foreach ($progetti_stato as $item):
                $percentage = ($item['count'] / $max_count) * 100;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <span><strong><?php echo ucfirst(str_replace('_', ' ', $item['stato'])); ?></strong></span>
                        <span><?php echo $item['count']; ?> progetti - <?php echo formatMoney($item['totale']); ?></span>
                    </div>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width: <?php echo $percentage; ?>%;">
                            <?php if ($percentage > 20): ?><?php echo $item['count']; ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Progetti per Tipo -->
    <div class="chart-container">
        <h3 class="chart-title"><i class="fas fa-chart-pie"></i> Progetti per Tipo</h3>
        <div class="bar-chart">
            <?php
            $max_count = !empty($progetti_tipo) ? max(array_column($progetti_tipo, 'count')) : 1;
            foreach ($progetti_tipo as $item):
                $percentage = ($item['count'] / $max_count) * 100;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <span><strong><?php echo ucfirst(str_replace('_', ' ', $item['tipo_progetto'])); ?></strong></span>
                        <span><?php echo $item['count']; ?> progetti - <?php echo formatMoney($item['totale']); ?></span>
                    </div>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width: <?php echo $percentage; ?>%; background: #3498db;">
                            <?php if ($percentage > 20): ?><?php echo $item['count']; ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="two-column-grid">
    <!-- Top Clienti -->
    <div class="chart-container">
        <h3 class="chart-title"><i class="fas fa-trophy"></i> Top 5 Clienti per Fatturato</h3>
        <table class="table-simple">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Progetti</th>
                    <th>Fatturato</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($top_clienti as $cliente): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo h($cliente['ragione_sociale']); ?></td>
                        <td><?php echo $cliente['num_progetti']; ?></td>
                        <td><strong><?php echo formatMoney($cliente['totale']); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Progetti per Priorità -->
    <div class="chart-container">
        <h3 class="chart-title"><i class="fas fa-exclamation-circle"></i> Progetti Attivi per Priorità</h3>
        <div class="bar-chart">
            <?php
            $max_count = !empty($progetti_priorita) ? max(array_column($progetti_priorita, 'count')) : 1;
            $colors = [
                'alta' => '#e74c3c',
                'media' => '#f39c12',
                'bassa' => '#95a5a6'
            ];
            foreach ($progetti_priorita as $item):
                $percentage = ($item['count'] / $max_count) * 100;
            ?>
                <div class="bar-item">
                    <div class="bar-label">
                        <span><strong>Priorità <?php echo ucfirst($item['priorita']); ?></strong></span>
                        <span><?php echo $item['count']; ?> progetti</span>
                    </div>
                    <div class="bar-bg">
                        <div class="bar-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $colors[$item['priorita']]; ?>;">
                            <?php if ($percentage > 20): ?><?php echo $item['count']; ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Performance Ore -->
<?php if ($ore_stats && $ore_stats['ore_stimate_totali'] > 0): ?>
<div class="chart-container">
    <h3 class="chart-title"><i class="fas fa-clock"></i> Performance Ore (Progetti Completati)</h3>
    <div style="display: flex; justify-content: space-around; align-items: center; padding: 20px;">
        <div style="text-align: center;">
            <h4 style="color: #7f8c8d; margin-bottom: 10px;">Ore Stimate</h4>
            <div class="stat-value" style="color: #3498db;"><?php echo number_format($ore_stats['ore_stimate_totali'], 1); ?>h</div>
        </div>
        <div style="font-size: 48px; color: #95a5a6;">→</div>
        <div style="text-align: center;">
            <h4 style="color: #7f8c8d; margin-bottom: 10px;">Ore Effettive</h4>
            <div class="stat-value" style="color: <?php echo $ore_stats['ore_effettive_totali'] <= $ore_stats['ore_stimate_totali'] ? '#27ae60' : '#e74c3c'; ?>;">
                <?php echo number_format($ore_stats['ore_effettive_totali'], 1); ?>h
            </div>
        </div>
        <div style="text-align: center;">
            <?php
            $diff = $ore_stats['ore_effettive_totali'] - $ore_stats['ore_stimate_totali'];
            $diff_percent = ($diff / $ore_stats['ore_stimate_totali']) * 100;
            ?>
            <h4 style="color: #7f8c8d; margin-bottom: 10px;">Differenza</h4>
            <div class="stat-value" style="font-size: 24px; color: <?php echo $diff > 0 ? '#e74c3c' : '#27ae60'; ?>;">
                <?php echo $diff > 0 ? '+' : ''; ?><?php echo number_format($diff, 1); ?>h
            </div>
            <div class="stat-label">(<?php echo $diff > 0 ? '+' : ''; ?><?php echo number_format($diff_percent, 1); ?>%)</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Scadenze per Stato -->
<div class="chart-container">
    <h3 class="chart-title"><i class="fas fa-calendar-check"></i> Scadenze per Stato</h3>
    <div class="bar-chart">
        <?php
        $max_count = !empty($scadenze_stato) ? max(array_column($scadenze_stato, 'count')) : 1;
        $colors = [
            'in_attesa' => '#f39c12',
            'pagato' => '#27ae60',
            'annullato' => '#95a5a6'
        ];
        foreach ($scadenze_stato as $item):
            $percentage = ($item['count'] / $max_count) * 100;
        ?>
            <div class="bar-item">
                <div class="bar-label">
                    <span><strong><?php echo ucfirst(str_replace('_', ' ', $item['stato'])); ?></strong></span>
                    <span><?php echo $item['count']; ?> scadenze - <?php echo formatMoney($item['totale']); ?></span>
                </div>
                <div class="bar-bg">
                    <div class="bar-fill" style="width: <?php echo $percentage; ?>%; background: <?php echo $colors[$item['stato']] ?? '#3498db'; ?>;">
                        <?php if ($percentage > 20): ?><?php echo $item['count']; ?><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes_footer.php'; ?>
