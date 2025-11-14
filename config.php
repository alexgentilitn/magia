<?php
/**
 * AGstudio CRM - Configurazione
 * Sistema di gestione clienti e progetti per web agency
 */

// Avvia sessione
session_start();

// Database SQLite3
$db_path = __DIR__ . '/agstudio.db';

try {
    $db = new SQLite3($db_path);
    $db->enableExceptions(true);
    $db->busyTimeout(5000);
} catch (Exception $e) {
    die("Errore connessione database: " . $e->getMessage());
}

// Configurazione App
define('APP_NAME', 'AGstudio');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://www.agstudio.digital/magia/');

// Funzioni Helper
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;

    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

function logActivity($azione, $entita_tipo = null, $entita_id = null, $dettagli = null) {
    global $db;

    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $db->prepare("
        INSERT INTO activity_log (user_id, azione, entita_tipo, entita_id, dettagli, ip_address)
        VALUES (:user_id, :azione, :entita_tipo, :entita_id, :dettagli, :ip)
    ");

    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':azione', $azione, SQLITE3_TEXT);
    $stmt->bindValue(':entita_tipo', $entita_tipo, SQLITE3_TEXT);
    $stmt->bindValue(':entita_id', $entita_id, SQLITE3_INTEGER);
    $stmt->bindValue(':dettagli', $dettagli, SQLITE3_TEXT);
    $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);

    return $stmt->execute();
}

function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

function formatMoney($amount) {
    if ($amount === null || $amount === '') return '-';
    return '€ ' . number_format($amount, 2, ',', '.');
}

function formatFileSize($bytes) {
    if ($bytes === null || $bytes === '') return '-';
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Verifica se l'utente corrente ha un determinato ruolo
 */
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;

    $roles_hierarchy = ['user' => 1, 'manager' => 2, 'admin' => 3];
    $user_level = $roles_hierarchy[$user['ruolo']] ?? 0;
    $required_level = $roles_hierarchy[$role] ?? 0;

    return $user_level >= $required_level;
}

/**
 * Verifica se l'utente ha permesso di scrittura
 */
function canWrite() {
    $user = getCurrentUser();
    if (!$user) return false;

    // Admin e manager possono sempre scrivere
    if (in_array($user['ruolo'], ['admin', 'manager'])) {
        return true;
    }

    // Utenti normali dipendono dai permessi
    $permessi = $user['permessi'] ?? 'all';
    return $permessi !== 'read';
}

/**
 * Verifica se l'utente può eliminare record
 */
function canDelete() {
    $user = getCurrentUser();
    if (!$user) return false;

    // Solo admin e manager possono eliminare
    return in_array($user['ruolo'], ['admin', 'manager']);
}

/**
 * Richiede un ruolo specifico, altrimenti termina
 */
function requireRole($role) {
    if (!hasRole($role)) {
        die('Accesso negato. Permessi insufficienti.');
    }
}
?>
