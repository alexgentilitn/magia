<?php
require_once 'config.php';
$pageTitle = 'Gestione Clienti';

// Lista clienti
$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if ($search) {
    $where = "WHERE ragione_sociale LIKE :search OR email LIKE :search OR citta LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$query = "SELECT * FROM clienti $where ORDER BY ragione_sociale ASC";
$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, SQLITE3_TEXT);
}

$result = $stmt->execute();
$clienti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $clienti[] = $row;
}

include 'includes_header.php';
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> <?php echo h($_SESSION['success_message']); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> Lista Clienti (<?php echo count($clienti); ?>)</h3>
        <div style="display: flex; gap: 10px;">
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cerca cliente..." style="padding: 10px; border: 2px solid #e9ecef; border-radius: 8px;">
                <button type="submit" class="btn"><i class="fas fa-search"></i> Cerca</button>
            </form>
            <a href="cliente_edit.php" class="btn"><i class="fas fa-plus"></i> Nuovo Cliente</a>
            <a href="import_clienti.php" class="btn btn-warning"><i class="fas fa-file-import"></i> Importa Excel</a>
            <a href="export_clienti.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Esporta Excel</a>
        </div>
    </div>

    <?php if (empty($clienti)): ?>
        <p style="text-align: center; padding: 40px; color: #999;">Nessun cliente trovato</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ragione Sociale</th>
                    <th>Referente</th>
                    <th>Contatti</th>
                    <th>Città</th>
                    <th>P.IVA</th>
                    <th>Stato</th>
                    <th style="text-align: center;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clienti as $cliente): ?>
                    <tr>
                        <td><strong><?php echo h($cliente['ragione_sociale']); ?></strong></td>
                        <td><?php echo h($cliente['nome_referente'] . ' ' . $cliente['cognome_referente']); ?></td>
                        <td>
                            <?php if ($cliente['email']): ?>
                                <div><i class="fas fa-envelope"></i> <?php echo h($cliente['email']); ?></div>
                            <?php endif; ?>
                            <?php if ($cliente['telefono']): ?>
                                <div><i class="fas fa-phone"></i> <?php echo h($cliente['telefono']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo h($cliente['citta']); ?></td>
                        <td><?php echo h($cliente['partita_iva']); ?></td>
                        <td>
                            <span class="badge <?php echo $cliente['stato'] == 'attivo' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo h($cliente['stato']); ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="cliente_edit.php?id=<?php echo $cliente['id']; ?>" class="btn" style="padding: 8px 15px; margin: 2px;">
                                <i class="fas fa-edit"></i> Modifica
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'includes_footer.php'; ?>
