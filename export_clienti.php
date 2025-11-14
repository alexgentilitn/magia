<?php
require_once 'config.php';
requireLogin();

// Query per estrarre tutti i clienti
$query = "
    SELECT
        id,
        ragione_sociale,
        nome_referente,
        cognome_referente,
        email,
        telefono,
        cellulare,
        pec,
        partita_iva,
        codice_fiscale,
        indirizzo,
        citta,
        cap,
        provincia,
        sito_web,
        stato,
        created_at
    FROM clienti
    ORDER BY ragione_sociale ASC
";

$result = $db->query($query);
$clienti = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $clienti[] = $row;
}

// Log attività
logActivity('export_clienti', 'report', null, count($clienti) . ' clienti esportati');

// Prepara CSV
$filename = 'clienti_' . date('Y-m-d_His') . '.csv';

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
    'Ragione Sociale',
    'Nome Referente',
    'Cognome Referente',
    'Email',
    'Telefono',
    'Cellulare',
    'PEC',
    'Partita IVA',
    'Codice Fiscale',
    'Indirizzo',
    'Città',
    'CAP',
    'Provincia',
    'Sito Web',
    'Stato',
    'Data Creazione'
], ';');

// Dati
foreach ($clienti as $cliente) {
    fputcsv($output, [
        $cliente['id'],
        $cliente['ragione_sociale'],
        $cliente['nome_referente'],
        $cliente['cognome_referente'],
        $cliente['email'],
        $cliente['telefono'],
        $cliente['cellulare'],
        $cliente['pec'],
        $cliente['partita_iva'],
        $cliente['codice_fiscale'],
        $cliente['indirizzo'],
        $cliente['citta'],
        $cliente['cap'],
        $cliente['provincia'],
        $cliente['sito_web'],
        $cliente['stato'],
        formatDateTime($cliente['created_at'], 'd/m/Y H:i')
    ], ';');
}

fclose($output);
exit;
?>
