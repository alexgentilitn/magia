<?php
require_once 'config.php';
requireLogin();

// Supporta sia GET che POST
$documento_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$return_url = $_GET['return'] ?? 'documenti.php';

// Recupera informazioni documento
$stmt = $db->prepare("SELECT * FROM documenti WHERE id = :id");
$stmt->bindValue(':id', $documento_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$documento = $result->fetchArray(SQLITE3_ASSOC);

if (!$documento) {
    $_SESSION['error_message'] = 'Documento non trovato';
    header('Location: progetti.php');
    exit;
}

// Elimina file fisico se esiste
$file_path = __DIR__ . '/' . $documento['file_path'];
if (file_exists($file_path)) {
    unlink($file_path);
}

// Elimina dal database
$stmt = $db->prepare("DELETE FROM documenti WHERE id = :id");
$stmt->bindValue(':id', $documento_id, SQLITE3_INTEGER);
$stmt->execute();

logActivity('delete_document', 'documenti', $documento_id, "Eliminato documento: {$documento['titolo']}");

$_SESSION['success_message'] = 'Documento eliminato con successo';

// Redirect
header('Location: ' . $return_url);
exit;
?>
