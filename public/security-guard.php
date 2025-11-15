<?php
/**
 * SECURITY GUARD - Protezione script debug/admin pubblici
 *
 * Questo file viene incluso all'inizio di ogni script debug per:
 * 1. Bloccare l'accesso in ambiente production
 * 2. Verificare IP whitelist
 * 3. Implementare rate limiting contro brute force
 */

// ==================== CONFIGURAZIONE ====================

// DISABILITA COMPLETAMENTE IN PRODUZIONE
define('SECURITY_DISABLE_IN_PRODUCTION', true);

// IP WHITELIST - Solo questi IP possono accedere
// IMPORTANTE: Aggiungere qui gli IP autorizzati!
$ALLOWED_IPS = [
    '127.0.0.1',           // Localhost
    '::1',                 // Localhost IPv6
    // Aggiungi qui gli IP degli amministratori autorizzati:
    // 'XX.XX.XX.XX',      // IP Ufficio
    // 'YY.YY.YY.YY',      // IP Casa
];

// RATE LIMITING
define('MAX_LOGIN_ATTEMPTS', 5);        // Max tentativi in finestra temporale
define('RATE_LIMIT_WINDOW', 300);       // 5 minuti (in secondi)
define('LOCKOUT_DURATION', 1800);       // 30 minuti di blocco dopo max tentativi

// ==================== FUNZIONI PROTEZIONE ====================

/**
 * Ottiene IP reale del client (considera proxy)
 */
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    // Check se dietro proxy (CloudFlare, Nginx, etc)
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    return trim($ip);
}

/**
 * Verifica se ambiente è production
 */
function isProduction() {
    if (file_exists(__DIR__.'/../.env')) {
        $envContent = file_get_contents(__DIR__.'/../.env');
        if (preg_match('/APP_ENV\s*=\s*production/i', $envContent)) {
            return true;
        }
    }
    return false;
}

/**
 * Verifica IP whitelist
 */
function isIPAllowed($clientIP, $allowedIPs) {
    return in_array($clientIP, $allowedIPs);
}

/**
 * Gestione rate limiting
 */
function checkRateLimit($clientIP) {
    $lockFile = sys_get_temp_dir() . '/security_guard_' . md5($clientIP) . '.lock';

    // Verifica se IP è in lockout
    if (file_exists($lockFile)) {
        $lockData = json_decode(file_get_contents($lockFile), true);

        if (isset($lockData['locked_until'])) {
            if (time() < $lockData['locked_until']) {
                $remainingMinutes = ceil(($lockData['locked_until'] - time()) / 60);
                return [
                    'allowed' => false,
                    'message' => "Troppi tentativi falliti. Riprova tra $remainingMinutes minuti."
                ];
            } else {
                // Lockout scaduto, reset
                unlink($lockFile);
            }
        }
    }

    return ['allowed' => true];
}

/**
 * Registra tentativo di login fallito
 */
function registerFailedAttempt($clientIP) {
    $lockFile = sys_get_temp_dir() . '/security_guard_' . md5($clientIP) . '.lock';

    $attempts = 1;
    $firstAttempt = time();

    if (file_exists($lockFile)) {
        $lockData = json_decode(file_get_contents($lockFile), true);

        // Reset se fuori dalla finestra temporale
        if ((time() - $lockData['first_attempt']) > RATE_LIMIT_WINDOW) {
            $attempts = 1;
            $firstAttempt = time();
        } else {
            $attempts = $lockData['attempts'] + 1;
            $firstAttempt = $lockData['first_attempt'];
        }
    }

    $lockData = [
        'attempts' => $attempts,
        'first_attempt' => $firstAttempt,
        'last_attempt' => time()
    ];

    // Se supera max tentativi, blocca l'IP
    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $lockData['locked_until'] = time() + LOCKOUT_DURATION;
    }

    file_put_contents($lockFile, json_encode($lockData));
}

/**
 * Reset rate limit dopo login riuscito
 */
function resetRateLimit($clientIP) {
    $lockFile = sys_get_temp_dir() . '/security_guard_' . md5($clientIP) . '.lock';
    if (file_exists($lockFile)) {
        unlink($lockFile);
    }
}

/**
 * Blocca accesso e mostra messaggio di errore
 */
function denyAccess($reason) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Accesso Negato</title>
        <style>
            body { font-family: Arial; background: #dc3545; color: white; padding: 50px; text-align: center; }
            .error-box { max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 40px; border-radius: 8px; }
            h1 { font-size: 48px; margin: 0 0 20px 0; }
            p { font-size: 18px; line-height: 1.6; }
            .icon { font-size: 64px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <div class="icon">🚫</div>
            <h1>Accesso Negato</h1>
            <p><?= htmlspecialchars($reason) ?></p>
            <hr style="margin: 30px 0; border: none; border-top: 1px solid rgba(255,255,255,0.3);">
            <p style="font-size: 14px; opacity: 0.8;">
                Se ritieni di dover avere accesso a questa risorsa, contatta l'amministratore di sistema.
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ==================== VERIFICA SICUREZZA ====================

// CHECK 1: Blocca se in produzione
if (SECURITY_DISABLE_IN_PRODUCTION && isProduction()) {
    error_log("SECURITY: Tentativo di accesso a script debug in produzione da IP: " . getClientIP());
    denyAccess("Questo strumento non è disponibile in ambiente di produzione.");
}

// CHECK 2: Verifica IP whitelist
$clientIP = getClientIP();
if (!isIPAllowed($clientIP, $ALLOWED_IPS)) {
    error_log("SECURITY: Tentativo di accesso da IP non autorizzato: $clientIP");
    denyAccess("Il tuo indirizzo IP ($clientIP) non è autorizzato ad accedere a questa risorsa.");
}

// CHECK 3: Rate limiting
$rateLimitCheck = checkRateLimit($clientIP);
if (!$rateLimitCheck['allowed']) {
    error_log("SECURITY: IP $clientIP bloccato per rate limiting");
    denyAccess($rateLimitCheck['message']);
}

// ==================== LOG ACCESSO ====================
error_log("SECURITY: Accesso autorizzato a script debug da IP: $clientIP");

// Se arriviamo qui, l'accesso è consentito
// Esponi funzioni helper per gli script che includono questo file
return [
    'registerFailedAttempt' => 'registerFailedAttempt',
    'resetRateLimit' => 'resetRateLimit',
    'clientIP' => $clientIP
];
