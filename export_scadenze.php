<?php
require_once 'config.php';
requireLogin();

// Query per estrarre tutte le scadenze con info progetto e cliente
$query = "
    SELECT
        s.id,
        c.ragione_sociale as cliente,
        p.titolo as progetto,
        s.descrizione,
        s.tipo,
        s.data_scadenza,
        s.importo,
        s.stato,
        s.data_pagamento,
        s.note,
        s.created_at
    FROM scadenze s
    LEFT JOIN progetti p ON s.progetto_id = p.id
    LEFT JOIN clienti c ON p.cliente_id = c.id
    ORDER BY s.data_scadenza DESC
";

$result = $db->query($query);
$scadenze = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $scadenze[] = $row;
}

// Log attività
logActivity('export_scadenze', 'report', null, count($scadenze) . ' scadenze esportate');

// Prepara CSV
$filename = 'scadenze_' . date('Y-m-d_His') . '.csv';

// Headers per download CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');
header('Expires: 0');

// BOM UTF-8 per Excel (supporto caratteri accentati)
echo "\xEF\xBB\xBF";

// Apri output
$output = fopen('php://output', 'w');

// Intestazioni colonne
fputcsv($output, [
    'ID',
    'Cliente',
    'Progetto',
    'Descrizione',
    'Tipo',
    'Data Scadenza',
    'Importo (€)',
    'Stato',
    'Data Pagamento',
    'Note',
    'Data Creazione'
], ';');

// Dati
foreach ($scadenze as $scadenza) {
    fputcsv($output, [
        $scadenza['id'],
        $scadenza['cliente'],
        $scadenza['progetto'],
        $scadenza['descrizione'],
        $scadenza['tipo'],
        $scadenza['data_scadenza'] ? date('d/m/Y', strtotime($scadenza['data_scadenza'])) : '',
        $scadenza['importo'] ? number_format($scadenza['importo'], 2, ',', '.') : '',
        $scadenza['stato'],
        $scadenza['data_pagamento'] ? date('d/m/Y', strtotime($scadenza['data_pagamento'])) : '',
        $scadenza['note'],
        formatDateTime($scadenza['created_at'], 'd/m/Y H:i')
    ], ';');
}

fclose($output);
exit;
?>
