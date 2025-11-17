<?php
/**
 * DB EXECUTE - Esegue query SQL via HTTP
 * Permette a Claude di eseguire query automaticamente sul database
 */

// Configurazione
define('API_SECRET', '$Magia2025!');
define('MAX_RESULTS', 1000);

// Headers per JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// Leggi input JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verifica autenticazione
if (!isset($input['secret']) || $input['secret'] !== API_SECRET) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Invalid secret.']);
    exit;
}

// Verifica presenza query
if (!isset($input['sql']) || empty(trim($input['sql']))) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing SQL query.']);
    exit;
}

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $sql = trim($input['sql']);
    $response = [
        'success' => true,
        'query' => $sql,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Determina tipo di query
    $queryType = strtoupper(substr(ltrim($sql), 0, 6));

    if (in_array($queryType, ['SELECT', 'SHOW T', 'SHOW C', 'DESCRI'])) {
        // Query che restituiscono dati
        $results = DB::select($sql);
        $response['type'] = 'select';
        $response['rows'] = count($results);
        $response['data'] = array_slice($results, 0, MAX_RESULTS);

        if (count($results) > MAX_RESULTS) {
            $response['warning'] = 'Results truncated to ' . MAX_RESULTS . ' rows';
        }
    } else {
        // Query che modificano dati (INSERT, UPDATE, DELETE, CREATE, ALTER, DROP)
        $affected = DB::statement($sql);
        $response['type'] = 'statement';
        $response['affected'] = $affected;
        $response['message'] = 'Query executed successfully';
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
