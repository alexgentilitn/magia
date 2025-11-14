<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: progetti.php');
    exit;
}

$progetto_id = (int)($_POST['progetto_id'] ?? 0);
$tipo = $_POST['tipo'] ?? 'documento';

// Verifica progetto esiste
$stmt = $db->prepare("SELECT id, titolo FROM progetti WHERE id = :id");
$stmt->bindValue(':id', $progetto_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$progetto = $result->fetchArray(SQLITE3_ASSOC);

if (!$progetto) {
    $_SESSION['error_message'] = 'Progetto non trovato';
    header('Location: progetti.php');
    exit;
}

// Upload file
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error_message'] = 'Errore durante l\'upload del file';
    header('Location: progetto_edit.php?id=' . $progetto_id);
    exit;
}

$file = $_FILES['file'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Validazione dimensione (10MB max)
$max_size = 10 * 1024 * 1024;
if ($file_size > $max_size) {
    $_SESSION['error_message'] = 'File troppo grande. Dimensione massima: 10MB';
    header('Location: progetto_edit.php?id=' . $progetto_id);
    exit;
}

// Genera nome file univoco
$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
$unique_filename = uniqid() . '_' . time() . '.' . $file_extension;

// Directory upload
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_path = 'uploads/' . $unique_filename;
$full_path = __DIR__ . '/' . $file_path;

// Sposta file
if (!move_uploaded_file($file_tmp, $full_path)) {
    $_SESSION['error_message'] = 'Errore durante il salvataggio del file';
    header('Location: progetto_edit.php?id=' . $progetto_id);
    exit;
}

// Salva nel database
$stmt = $db->prepare("
    INSERT INTO documenti (progetto_id, titolo, tipo, file_path, file_size, mime_type, created_at)
    VALUES (:progetto_id, :titolo, :tipo, :file_path, :file_size, :mime_type, CURRENT_TIMESTAMP)
");

$stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
$stmt->bindValue(':titolo', $file_name, SQLITE3_TEXT);
$stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
$stmt->bindValue(':file_path', $file_path, SQLITE3_TEXT);
$stmt->bindValue(':file_size', $file_size, SQLITE3_INTEGER);
$stmt->bindValue(':mime_type', $file['type'], SQLITE3_TEXT);
$stmt->execute();

$documento_id = $db->lastInsertRowID();

logActivity('upload_document', 'documenti', $documento_id, "Upload documento: $file_name per progetto: {$progetto['titolo']}");

$_SESSION['success_message'] = 'Documento caricato con successo';
header('Location: progetto_edit.php?id=' . $progetto_id);
exit;
?>
