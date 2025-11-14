<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Lezioni - Database Viewer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 {
            color: #764ba2;
            margin-bottom: 10px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #764ba2;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            white-space: nowrap;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-true {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-false, .badge-null {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-ricorrente {
            background: #dbeafe;
            color: #1e40af;
        }
        .highlight-problem {
            background: #fef3c7 !important;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<?php
// Connessione database
$host = 'localhost';
$dbname = 'progetti_ai';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query per statistiche
    $stats = [
        'totale' => $pdo->query("SELECT COUNT(*) FROM lezioni WHERE deleted_at IS NULL")->fetchColumn(),
        'visibili' => $pdo->query("SELECT COUNT(*) FROM lezioni WHERE visibile_calendario = 1 AND deleted_at IS NULL")->fetchColumn(),
        'non_visibili' => $pdo->query("SELECT COUNT(*) FROM lezioni WHERE (visibile_calendario = 0 OR visibile_calendario IS NULL) AND deleted_at IS NULL")->fetchColumn(),
        'ricorrenti' => $pdo->query("SELECT COUNT(*) FROM lezioni WHERE ricorrente = 1 AND deleted_at IS NULL")->fetchColumn(),
        'figlie' => $pdo->query("SELECT COUNT(*) FROM lezioni WHERE lezione_padre_id IS NOT NULL AND deleted_at IS NULL")->fetchColumn(),
    ];

    // Query per tutte le lezioni
    $stmt = $pdo->query("
        SELECT
            id,
            titolo,
            data,
            DATE_FORMAT(ora_inizio, '%H:%i') as ora_inizio,
            DATE_FORMAT(ora_fine, '%H:%i') as ora_fine,
            tipologia,
            stato,
            ricorrente,
            frequenza_ricorrenza,
            fine_ricorrenza,
            lezione_padre_id,
            visibile_calendario,
            posti_occupati,
            posti_totali,
            created_at
        FROM lezioni
        WHERE deleted_at IS NULL
        ORDER BY data ASC, ora_inizio ASC
    ");
    $lezioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("<div class='container'><div class='header'><h1>❌ Errore Connessione Database</h1><p>" . $e->getMessage() . "</p></div></div>");
}
?>

<div class="container">
    <div class="header">
        <h1>🔍 Debug Lezioni Database</h1>
        <p style="color: #666;">Visualizza tutte le lezioni e identifica problemi di visibilità nel calendario</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>Totale Lezioni</h3>
            <div class="number"><?= $stats['totale'] ?></div>
        </div>
        <div class="stat-card">
            <h3>✅ Visibili Calendario</h3>
            <div class="number" style="color: #10b981;"><?= $stats['visibili'] ?></div>
        </div>
        <div class="stat-card">
            <h3>❌ Non Visibili</h3>
            <div class="number" style="color: #ef4444;"><?= $stats['non_visibili'] ?></div>
        </div>
        <div class="stat-card">
            <h3>🔄 Ricorrenti</h3>
            <div class="number" style="color: #3b82f6;"><?= $stats['ricorrenti'] ?></div>
        </div>
        <div class="stat-card">
            <h3>📅 Lezioni Figlie</h3>
            <div class="number" style="color: #8b5cf6;"><?= $stats['figlie'] ?></div>
        </div>
    </div>

    <?php if ($stats['non_visibili'] > 0): ?>
    <div class="header" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
        <h2 style="color: #92400e; font-size: 18px;">⚠️ Problema Rilevato</h2>
        <p style="color: #78350f; margin-top: 10px;">
            Ci sono <strong><?= $stats['non_visibili'] ?></strong> lezioni con <code>visibile_calendario</code> = false o null.
            Queste lezioni NON appariranno nel calendario.
        </p>
        <p style="color: #78350f; margin-top: 10px;">
            <strong>Soluzione:</strong> Vai su <code>/admin/maintenance</code> e clicca su "Correggi Lezioni"
        </p>
    </div>
    <?php endif; ?>

    <div class="table-container">
        <h2 style="margin-bottom: 15px; color: #333;">📋 Elenco Completo Lezioni</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titolo</th>
                    <th>Data</th>
                    <th>Orario</th>
                    <th>Tipo</th>
                    <th>Stato</th>
                    <th>Visibile?</th>
                    <th>Ricorrente</th>
                    <th>Frequenza</th>
                    <th>Padre ID</th>
                    <th>Posti</th>
                    <th>Creata il</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lezioni as $lezione):
                    $problema = ($lezione['visibile_calendario'] == 0 || $lezione['visibile_calendario'] === null);
                    $classe = $problema ? 'highlight-problem' : '';
                ?>
                <tr class="<?= $classe ?>">
                    <td><strong><?= $lezione['id'] ?></strong></td>
                    <td><?= htmlspecialchars($lezione['titolo']) ?></td>
                    <td><?= date('d/m/Y', strtotime($lezione['data'])) ?></td>
                    <td><?= $lezione['ora_inizio'] ?> - <?= $lezione['ora_fine'] ?></td>
                    <td><?= $lezione['tipologia'] ?></td>
                    <td><?= $lezione['stato'] ?></td>
                    <td>
                        <?php if ($lezione['visibile_calendario'] === null): ?>
                            <span class="badge badge-null">NULL</span>
                        <?php elseif ($lezione['visibile_calendario'] == 1): ?>
                            <span class="badge badge-true">SÌ</span>
                        <?php else: ?>
                            <span class="badge badge-false">NO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $lezione['ricorrente'] ? '<span class="badge badge-ricorrente">SÌ</span>' : 'No' ?>
                    </td>
                    <td><?= $lezione['frequenza_ricorrenza'] ?: '-' ?></td>
                    <td><?= $lezione['lezione_padre_id'] ?: '-' ?></td>
                    <td><?= $lezione['posti_occupati'] ?>/<?= $lezione['posti_totali'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($lezione['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="header" style="margin-top: 20px;">
        <h3 style="color: #333; margin-bottom: 10px;">💡 Legenda</h3>
        <div style="color: #666; line-height: 1.8;">
            <p>🟡 <strong>Righe evidenziate in giallo:</strong> Lezioni con problemi di visibilità</p>
            <p>✅ <strong>Badge verde (SÌ):</strong> Lezione visibile nel calendario</p>
            <p>❌ <strong>Badge rosso (NO/NULL):</strong> Lezione NON visibile nel calendario</p>
            <p>🔄 <strong>Badge blu Ricorrente:</strong> Lezione padre che genera altre occorrenze</p>
            <p>📅 <strong>Padre ID:</strong> Se presente, questa è una lezione figlia di una serie ricorrente</p>
        </div>
    </div>
</div>

</body>
</html>
