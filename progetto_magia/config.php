<?php
/**
 * Configurazione Progetto Magia
 *
 * File di configurazione principale per il progetto Magia Donna
 */

// Configurazione Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'database_name');
define('DB_USER', 'database_user');
define('DB_PASS', 'database_password');
define('DB_CHARSET', 'utf8mb4');

// Configurazione Applicazione
define('SITE_URL', 'https://www.agstudio.digital/progetto_magia');
define('SITE_NAME', 'Progetto Magia Donna');
define('SITE_VERSION', '1.0.0');

// Percorsi
define('BASE_PATH', __DIR__);
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Configurazione Sessione
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // Solo HTTPS
session_start();

// Timezone
date_default_timezone_set('Europe/Rome');

// Gestione Errori (da disabilitare in produzione)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Cambia a 0 in produzione
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/error.log');

// Connessione Database
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Errore connessione database: " . $e->getMessage());
    die("Errore di connessione al database. Contattare l'amministratore.");
}

// Funzioni di utilità
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
