<?php
require_once 'config.php';
requireLogin();

// Carica credenziali Google
$result = $db->query("SELECT * FROM google_credentials LIMIT 1");
$credentials = $result->fetchArray(SQLITE3_ASSOC);

if (!$credentials) {
    die('Credenziali Google non configurate. Contatta un amministratore.');
}

// Parametri OAuth
$params = [
    'client_id' => $credentials['client_id'],
    'redirect_uri' => $credentials['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/calendar.readonly',
    'access_type' => 'offline',
    'prompt' => 'consent',
    'state' => bin2hex(random_bytes(16)) // CSRF protection
];

// Salva state in sessione per verifica
$_SESSION['google_oauth_state'] = $params['state'];

// Redirect a Google OAuth
$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $auth_url);
exit;
?>
