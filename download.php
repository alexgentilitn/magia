<?php
require_once 'config.php';
requireLogin();

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID documento non specificato');
}

// Carica info documento
$stmt = $db->prepare("SELECT * FROM documenti WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$result = $stmt->execute();
$documento = $result->fetchArray(SQLITE3_ASSOC);

if (!$documento) {
    die('Documento non trovato');
}

// Percorso file
$file_path = __DIR__ . '/' . $documento['file_path'];

if (!file_exists($file_path)) {
    die('File non trovato sul server');
}

// Log download
logActivity('download_documento', 'documento', $id, $documento['titolo']);

// Prepara download
$file_name = basename($documento['file_path']);

// Headers per download
header('Content-Type: ' . ($documento['mime_type'] ?: 'application/octet-stream'));
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Invia file
readfile($file_path);
exit;
?>
