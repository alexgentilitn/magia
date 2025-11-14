<?php
require_once 'config.php';
requireLogin();

// Query per estrarre tutti i progetti con info cliente
$query = "
    SELECT
        p.id,
        c.ragione_sociale as cliente,
        p.titolo,
        p.descrizione,
        p.tipo_progetto,
        p.stato,
        p.priorita,
        p.importo,
        p.data_inizio,
        p.data_consegna,
        p.data_completamento,
        p.percentuale_completamento,
        p.ore_stimate,
        p.ore_effettive,
        p.note,
        p.created_at
    FROM progetti p
    LEFT JOIN clienti c ON p.cliente_id = c.id
    ORDER BY p.data_inizio DESC
";

$result = $db->query($query);
$progetti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $progetti[] = $row;
}

// Log attività
logActivity('export_progetti', 'report', null, count($progetti) . ' progetti esportati');

// Prepara CSV
$filename = 'progetti_' . date('Y-m-d_His') . '.csv';

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
    'Titolo Progetto',
    'Descrizione',
    'Tipo Progetto',
    'Stato',
    'Priorità',
    'Importo (€)',
    'Data Inizio',
    'Data Consegna',
    'Data Completamento',
    '% Completamento',
    'Ore Stimate',
    'Ore Effettive',
    'Note',
    'Data Creazione'
], ';');

// Dati
foreach ($progetti as $progetto) {
    fputcsv($output, [
        $progetto['id'],
        $progetto['cliente'],
        $progetto['titolo'],
        $progetto['descrizione'],
        $progetto['tipo_progetto'],
        $progetto['stato'],
        $progetto['priorita'],
        $progetto['importo'] ? number_format($progetto['importo'], 2, ',', '.') : '',
        $progetto['data_inizio'] ? date('d/m/Y', strtotime($progetto['data_inizio'])) : '',
        $progetto['data_consegna'] ? date('d/m/Y', strtotime($progetto['data_consegna'])) : '',
        $progetto['data_completamento'] ? date('d/m/Y', strtotime($progetto['data_completamento'])) : '',
        $progetto['percentuale_completamento'] . '%',
        $progetto['ore_stimate'],
        $progetto['ore_effettive'],
        $progetto['note'],
        formatDateTime($progetto['created_at'], 'd/m/Y H:i')
    ], ';');
}

fclose($output);
exit;
?>
