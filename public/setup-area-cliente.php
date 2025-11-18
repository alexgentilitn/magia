<?php
/**
 * Script di Verifica Area Cliente - MA.GIA DONNA
 * Versione semplificata con connessione diretta al database
 */

// Configurazione database (da .env)
$db_config = [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'agstudiodiital_magia',
    'username' => 'agstudiodiital_agstudiomagia',
    'password' => '$Magia2015!',
];

// Gestione API endpoints
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $pdo = new PDO(
            "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        switch ($_GET['action']) {
            case 'check_db':
                echo json_encode([
                    'success' => true,
                    'message' => 'Connessione al database riuscita (' . $db_config['database'] . ')'
                ]);
                break;

            case 'check_utenti':
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM utenti");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo json_encode([
                    'success' => true,
                    'message' => "Tabella utenti trovata ({$count} record)"
                ]);
                break;

            case 'check_clienti':
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM clienti");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo json_encode([
                    'success' => true,
                    'message' => "Tabella clienti trovata ({$count} record)"
                ]);
                break;

            case 'check_fields':
                $fields = ['peso_iniziale', 'altezza', 'programma_attuale', 'consenso_privacy'];
                $stmt = $pdo->query("DESCRIBE clienti");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

                $missing = array_diff($fields, $columns);
                $success = empty($missing);

                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Tutti i campi richiesti sono presenti' : 'Campi mancanti: ' . implode(', ', $missing)
                ]);
                break;

            case 'check_relation':
                $stmt = $pdo->query("
                    SELECT u.id, u.nome, u.email, c.id as cliente_id, c.programma_attuale
                    FROM utenti u
                    INNER JOIN clienti c ON c.utente_id = u.id
                    WHERE u.tipo_utente = 'cliente'
                    LIMIT 1
                ");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Relazione Utente-Cliente funzionante (trovato: ' . $result['nome'] . ')'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Nessun cliente trovato con utente collegato'
                    ]);
                }
                break;

            case 'create_test_user':
                $pdo->beginTransaction();

                try {
                    // Verifica se esiste già
                    $stmt = $pdo->prepare("SELECT id FROM utenti WHERE email = ?");
                    $stmt->execute(['test.cliente@magia.it']);
                    if ($stmt->fetch()) {
                        throw new Exception('Utente test già esistente');
                    }

                    // Crea utente
                    $stmt = $pdo->prepare("
                        INSERT INTO utenti (email, password, nome, cognome, telefono, tipo_utente, attivo, email_verificata, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([
                        'test.cliente@magia.it',
                        password_hash('password123', PASSWORD_DEFAULT),
                        'Maria',
                        'Test',
                        '3331234567',
                        'cliente',
                        1,
                        1
                    ]);
                    $utente_id = $pdo->lastInsertId();

                    // Crea profilo cliente
                    $stmt = $pdo->prepare("
                        INSERT INTO clienti (
                            utente_id, nome, cognome, codice_fiscale,
                            indirizzo, citta, provincia, cap,
                            telefono_mobile, email, data_nascita,
                            peso_iniziale, altezza,
                            programma_attuale, data_iscrizione, inizio_programma, fine_programma,
                            consenso_privacy, stato_cliente, codice_cliente,
                            created_at, updated_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $utente_id,
                        'Maria',
                        'Test',
                        'TSTMRA90A01H501X',
                        'Via Test 123',
                        'Trento',
                        'TN',
                        '38100',
                        '3331234567',
                        'test.cliente@magia.it',
                        '1990-01-01',
                        65.5,
                        165,
                        'Balla & Snella',
                        1,
                        'attivo',
                        'CLT' . str_pad($utente_id, 5, '0', STR_PAD_LEFT)
                    ]);
                    $cliente_id = $pdo->lastInsertId();

                    $pdo->commit();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Utente test creato con successo',
                        'email' => 'test.cliente@magia.it',
                        'password' => 'password123',
                        'utente_id' => $utente_id,
                        'cliente_id' => $cliente_id
                    ]);

                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Azione non valida']);
        }

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore database: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore: ' . $e->getMessage()
        ]);
    }

    exit;
}

// HTML Interface (resto uguale)
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup & Verifica Area Cliente - MA.GIA DONNA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #7B2869 0%, #E91E8C 50%, #FF6B35 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #7B2869;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #E91E8C;
            margin-bottom: 30px;
            font-size: 1.2em;
        }
        .status-box {
            background: #f8f9fa;
            border-left: 5px solid #E91E8C;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .status-box.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .status-box.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .status-box.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .check-item {
            padding: 15px;
            margin: 10px 0;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .check-item .icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-center;
            font-weight: bold;
            color: white;
            margin-right: 15px;
        }
        .check-item .icon.ok {
            background: #28a745;
        }
        .check-item .icon.error {
            background: #dc3545;
        }
        button {
            background: linear-gradient(135deg, #7B2869 0%, #E91E8C 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1em;
            cursor: pointer;
            margin: 10px 5px;
            transition: transform 0.2s;
        }
        button:hover {
            transform: scale(1.05);
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #7B2869 0%, #E91E8C 100%);
            transition: width 0.5s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 5px solid #0d6efd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🦋 Setup Area Cliente</h1>
        <p class="subtitle">MA.GIA DONNA - Verifica e Configurazione Database</p>

        <div class="info-box">
            <strong>📌 Cosa fa questo script:</strong>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Verifica la connessione al database</li>
                <li>Controlla l'esistenza delle tabelle necessarie</li>
                <li>Verifica i campi richiesti</li>
                <li>Testa la relazione Utente-Cliente</li>
                <li>Può creare un utente di test</li>
            </ul>
        </div>

        <div id="check-results"></div>

        <button onclick="runChecks()" style="width: 100%;">
            🔍 Avvia Verifica Completa
        </button>

        <div style="margin-top: 30px;">
            <button onclick="createTestUser()" style="background: linear-gradient(135deg, #FF6B35 0%, #FF8A5C 100%);">
                👤 Crea Utente Test
            </button>
            <div id="test-results" style="margin-top: 20px;"></div>
        </div>

        <div class="status-box" style="margin-top: 30px;">
            <h3>✅ Il Database è GIÀ Pronto!</h3>
            <p>Non servono modifiche al database. Le view sono state corrette per usare:</p>
            <pre>Auth::user()->cliente->peso_iniziale
Auth::user()->cliente->programma_attuale
ecc...</pre>
        </div>
    </div>

    <script>
        async function runChecks() {
            const results = document.getElementById('check-results');
            results.innerHTML = '<div class="progress-bar"><div class="progress-fill" style="width: 0%">0%</div></div>';

            const checks = [
                { name: 'Connessione Database', url: 'setup-area-cliente.php?action=check_db' },
                { name: 'Tabella utenti', url: 'setup-area-cliente.php?action=check_utenti' },
                { name: 'Tabella clienti', url: 'setup-area-cliente.php?action=check_clienti' },
                { name: 'Campi profilo cliente', url: 'setup-area-cliente.php?action=check_fields' },
                { name: 'Relazione Utente-Cliente', url: 'setup-area-cliente.php?action=check_relation' }
            ];

            let html = '';
            let successCount = 0;

            for (let i = 0; i < checks.length; i++) {
                const check = checks[i];
                const progress = ((i + 1) / checks.length) * 100;

                results.querySelector('.progress-fill').style.width = progress + '%';
                results.querySelector('.progress-fill').textContent = Math.round(progress) + '%';

                try {
                    const response = await fetch(check.url);
                    const data = await response.json();

                    const icon = data.success ? 'ok' : 'error';
                    if (data.success) successCount++;

                    html += `
                        <div class="check-item">
                            <div style="display: flex; align-items: center;">
                                <div class="icon ${icon}">${data.success ? '✓' : '✗'}</div>
                                <div>
                                    <strong>${check.name}</strong><br>
                                    <small style="color: #6c757d;">${data.message}</small>
                                </div>
                            </div>
                        </div>
                    `;
                } catch (error) {
                    html += `
                        <div class="check-item">
                            <div style="display: flex; align-items: center;">
                                <div class="icon error">✗</div>
                                <div>
                                    <strong>${check.name}</strong><br>
                                    <small style="color: #dc3545;">Errore: ${error.message}</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }

            const allOk = successCount === checks.length;
            const statusClass = allOk ? 'success' : (successCount > 0 ? 'warning' : 'error');

            results.innerHTML = `
                <div class="status-box ${statusClass}">
                    <h3>${allOk ? '✅ Tutto OK!' : (successCount > 0 ? '⚠️ Verifiche Parziali' : '❌ Errori Rilevati')}</h3>
                    <p>Verifiche completate: ${successCount}/${checks.length}</p>
                </div>
                ${html}
            `;
        }

        async function createTestUser() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<p>Creazione in corso...</p>';

            try {
                const response = await fetch('setup-area-cliente.php?action=create_test_user');
                const data = await response.json();

                if (data.success) {
                    results.innerHTML = `
                        <div class="status-box success">
                            <h3>✅ Utente Test Creato!</h3>
                            <p><strong>Email:</strong> ${data.email}</p>
                            <p><strong>Password:</strong> ${data.password}</p>
                            <p><strong>ID Utente:</strong> ${data.utente_id}</p>
                            <p><strong>ID Cliente:</strong> ${data.cliente_id}</p>
                            <p style="margin-top: 10px;"><a href="/login" style="color: #E91E8C; font-weight: bold;">→ Vai al Login</a></p>
                        </div>
                    `;
                } else {
                    results.innerHTML = `
                        <div class="status-box error">
                            <h3>❌ Errore</h3>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                results.innerHTML = `
                    <div class="status-box error">
                        <h3>❌ Errore</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>
