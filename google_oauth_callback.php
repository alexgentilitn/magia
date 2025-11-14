<?php
require_once 'config.php';
requireLogin();

$currentUser = getCurrentUser();

// Verifica state (CSRF protection)
$state = $_GET['state'] ?? '';
if (empty($state) || $state !== ($_SESSION['google_oauth_state'] ?? '')) {
    die('Errore: CSRF token non valido');
}

// Verifica errore
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    header('Location: google_calendar_setup.php?error=' . urlencode($error));
    exit;
}

// Ottieni authorization code
$code = $_GET['code'] ?? '';
if (empty($code)) {
    die('Errore: Authorization code mancante');
}

// Carica credenziali Google
$result = $db->query("SELECT * FROM google_credentials LIMIT 1");
$credentials = $result->fetchArray(SQLITE3_ASSOC);

if (!$credentials) {
    die('Errore: Credenziali Google non configurate');
}

// Scambia code per access token e refresh token
$token_data = [
    'code' => $code,
    'client_id' => $credentials['client_id'],
    'client_secret' => $credentials['client_secret'],
    'redirect_uri' => $credentials['redirect_uri'],
    'grant_type' => 'authorization_code'
];

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die('Errore durante l\'ottenimento del token: ' . $response);
}

$token_result = json_decode($response, true);

if (!isset($token_result['refresh_token'])) {
    die('Errore: Refresh token non ricevuto. Prova a disconnettere e riconnettere l\'account Google.');
}

// Salva refresh token nel database
$stmt = $db->prepare("UPDATE users SET google_refresh_token = :token WHERE id = :id");
$stmt->bindValue(':token', $token_result['refresh_token'], SQLITE3_TEXT);
$stmt->bindValue(':id', $currentUser['id'], SQLITE3_INTEGER);
$stmt->execute();

logActivity('connect_google', 'users', $currentUser['id'], 'Connesso Google Calendar');

// Pulisci state dalla sessione
unset($_SESSION['google_oauth_state']);

// Redirect a setup con messaggio di successo
header('Location: google_calendar_setup.php?success=1');
exit;
?>
