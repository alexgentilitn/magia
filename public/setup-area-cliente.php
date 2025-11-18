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
        .check-item .icon.warning {
            background: #ffc107;
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
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.9em;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 5px solid #0d6efd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
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
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        .tab-button {
            background: none;
            border: none;
            padding: 15px 25px;
            cursor: pointer;
            color: #6c757d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab-button.active {
            color: #E91E8C;
            border-bottom-color: #E91E8C;
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🦋 Setup Area Cliente</h1>
        <p class="subtitle">MA.GIA DONNA - Verifica e Configurazione Database</p>

        <!-- Tab Navigation -->
        <div class="tab-buttons">
            <button class="tab-button active" onclick="switchTab('verifica')">📋 Verifica</button>
            <button class="tab-button" onclick="switchTab('struttura')">🗂️ Struttura</button>
            <button class="tab-button" onclick="switchTab('test')">🧪 Test Dati</button>
            <button class="tab-button" onclick="switchTab('info')">ℹ️ Informazioni</button>
        </div>

        <!-- Tab 1: Verifica -->
        <div id="tab-verifica" class="tab-content active">
            <div class="info-box">
                <strong>📌 Cosa fa questo script:</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Verifica la connessione al database</li>
                    <li>Controlla l'esistenza delle tabelle necessarie</li>
                    <li>Verifica i campi richiesti</li>
                    <li>Testa la relazione Utente-Cliente</li>
                </ul>
            </div>

            <div id="check-results">
                <!-- I risultati verranno inseriti qui dal JavaScript -->
            </div>

            <button onclick="runChecks()" style="width: 100%;">
                🔍 Avvia Verifica Completa
            </button>
        </div>

        <!-- Tab 2: Struttura -->
        <div id="tab-struttura" class="tab-content">
            <h2 style="color: #7B2869; margin-bottom: 20px;">📊 Struttura Database</h2>

            <h3 style="color: #E91E8C; margin-top: 30px;">Tabella: utenti</h3>
            <div class="status-box">
                <strong>Campi principali:</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>id (Primary Key)</li>
                    <li>email (unique)</li>
                    <li>password</li>
                    <li>nome, cognome</li>
                    <li>telefono</li>
                    <li>tipo_utente (amministratore, professionista, cliente)</li>
                    <li>attivo, email_verificata</li>
                </ul>
            </div>

            <h3 style="color: #E91E8C; margin-top: 30px;">Tabella: clienti</h3>
            <div class="status-box">
                <strong>Campi principali:</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>id (Primary Key)</li>
                    <li>utente_id (Foreign Key → utenti)</li>
                    <li>nome, cognome, codice_fiscale</li>
                    <li>indirizzo, citta, provincia, cap</li>
                    <li>telefono_mobile, email</li>
                    <li><strong>Dati corporei:</strong> peso_iniziale, altezza, circonferenze</li>
                    <li><strong>Programma:</strong> programma_attuale, inizio_programma, fine_programma</li>
                    <li><strong>Consensi:</strong> consenso_privacy, consenso_marketing, consenso_foto</li>
                    <li>certificato_medico, certificato_medico_scadenza</li>
                </ul>
            </div>

            <h3 style="color: #E91E8C; margin-top: 30px;">Relazioni</h3>
            <div class="status-box success">
                <strong>✅ Utente hasOne Cliente</strong><br>
                Un utente con tipo_utente='cliente' ha un profilo cliente collegato tramite utente_id
            </div>
        </div>

        <!-- Tab 3: Test Dati -->
        <div id="tab-test" class="tab-content">
            <div class="info-box">
                <strong>🧪 Crea utente e cliente di test</strong><br>
                Questo creerà un utente cliente di prova per testare l'area cliente
            </div>

            <div id="test-results"></div>

            <button onclick="createTestUser()">
                👤 Crea Utente Test
            </button>

            <div class="status-box warning" style="margin-top: 20px;">
                <strong>⚠️ Credenziali Test Create:</strong><br>
                Email: <code>test.cliente@magia.it</code><br>
                Password: <code>password123</code><br>
                (Le credenziali verranno mostrate dopo la creazione)
            </div>
        </div>

        <!-- Tab 4: Info -->
        <div id="tab-info" class="tab-content">
            <h2 style="color: #7B2869; margin-bottom: 20px;">ℹ️ Informazioni Implementazione</h2>

            <div class="status-box">
                <h3>🎨 Design</h3>
                <p>L'area cliente segue il design mobile-first come da demo grafica fornita, con:</p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Dashboard con 5 bottoni colorati (Balla & Snella, Alimentazione, Pelle, Community, Coaching)</li>
                    <li>Profilo completo con 4 sezioni tabbed</li>
                    <li>Pagina programmi con progressione visuale</li>
                    <li>Calendario con filtri e vista mensile</li>
                </ul>
            </div>

            <div class="status-box">
                <h3>🔗 Utilizzo nelle View</h3>
                <p>Per accedere ai dati del cliente nelle view Blade:</p>
                <pre>{{ Auth::user()->nome }}          // Nome dall'utente
{{ Auth::user()->cliente->peso_iniziale }}  // Dati dal profilo cliente
{{ Auth::user()->cliente->programma_attuale }}</pre>
            </div>

            <div class="status-box">
                <h3>📁 File Creati</h3>
                <ul style="margin-left: 20px;">
                    <li>resources/views/cliente/dashboard.blade.php</li>
                    <li>resources/views/cliente/profilo.blade.php</li>
                    <li>resources/views/cliente/programmi.blade.php</li>
                    <li>resources/views/cliente/calendario.blade.php</li>
                </ul>
            </div>

            <div class="status-box warning">
                <h3>⚠️ Fix Necessario</h3>
                <p>Le view create usano <code>Auth::user()->campo</code> invece di <code>Auth::user()->cliente->campo</code>.</p>
                <p><strong>Verrà corretto automaticamente dopo questa verifica.</strong></p>
            </div>
        </div>
    </div>

    <script>
        // Switch tra tab
        function switchTab(tabName) {
            // Nascondi tutti i contenuti
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Mostra il contenuto selezionato
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Esegui verifiche
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

                // Aggiorna barra progresso
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

            // Risultato finale
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

        // Crea utente test
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

<?php
/**
 * API Endpoints per le verifiche
 */
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // Carica Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    $action = $_GET['action'];

    try {
        switch ($action) {
            case 'check_db':
                $connected = \DB::connection()->getPdo() !== null;
                echo json_encode([
                    'success' => $connected,
                    'message' => $connected ? 'Connessione al database riuscita' : 'Impossibile connettersi al database'
                ]);
                break;

            case 'check_utenti':
                $exists = \Schema::hasTable('utenti');
                $count = $exists ? \DB::table('utenti')->count() : 0;
                echo json_encode([
                    'success' => $exists,
                    'message' => $exists ? "Tabella utenti trovata ({$count} record)" : 'Tabella utenti NON trovata'
                ]);
                break;

            case 'check_clienti':
                $exists = \Schema::hasTable('clienti');
                $count = $exists ? \DB::table('clienti')->count() : 0;
                echo json_encode([
                    'success' => $exists,
                    'message' => $exists ? "Tabella clienti trovata ({$count} record)" : 'Tabella clienti NON trovata'
                ]);
                break;

            case 'check_fields':
                $fields = ['peso_iniziale', 'altezza', 'programma_attuale', 'consenso_privacy'];
                $missing = [];

                foreach ($fields as $field) {
                    if (!\Schema::hasColumn('clienti', $field)) {
                        $missing[] = $field;
                    }
                }

                $success = empty($missing);
                echo json_encode([
                    'success' => $success,
                    'message' => $success ? 'Tutti i campi richiesti sono presenti' : 'Campi mancanti: ' . implode(', ', $missing)
                ]);
                break;

            case 'check_relation':
                try {
                    $utente = \App\Models\Utente::where('tipo_utente', 'cliente')->first();
                    if ($utente && $utente->cliente) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Relazione Utente->Cliente funzionante'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Nessun cliente trovato o relazione non configurata'
                        ]);
                    }
                } catch (\Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Errore nella relazione: ' . $e->getMessage()
                    ]);
                }
                break;

            case 'create_test_user':
                \DB::beginTransaction();
                try {
                    // Crea utente
                    $utente = \App\Models\Utente::create([
                        'email' => 'test.cliente@magia.it',
                        'password' => bcrypt('password123'),
                        'nome' => 'Maria',
                        'cognome' => 'Test',
                        'telefono' => '3331234567',
                        'tipo_utente' => 'cliente',
                        'attivo' => true,
                        'email_verificata' => true,
                    ]);

                    // Crea profilo cliente
                    $cliente = \App\Models\Cliente::create([
                        'utente_id' => $utente->id,
                        'nome' => 'Maria',
                        'cognome' => 'Test',
                        'codice_fiscale' => 'TSTMRA90A01H501X',
                        'indirizzo' => 'Via Test 123',
                        'citta' => 'Trento',
                        'provincia' => 'TN',
                        'cap' => '38100',
                        'telefono_mobile' => '3331234567',
                        'email' => 'test.cliente@magia.it',
                        'data_nascita' => '1990-01-01',
                        'peso_iniziale' => 65.5,
                        'altezza' => 165,
                        'programma_attuale' => 'Balla & Snella',
                        'data_iscrizione' => now(),
                        'inizio_programma' => now(),
                        'fine_programma' => now()->addMonths(3),
                        'consenso_privacy' => true,
                        'stato_cliente' => 'attivo',
                        'codice_cliente' => 'CLT' . str_pad($utente->id, 5, '0', STR_PAD_LEFT),
                    ]);

                    \DB::commit();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Utente test creato con successo',
                        'email' => 'test.cliente@magia.it',
                        'password' => 'password123',
                        'utente_id' => $utente->id,
                        'cliente_id' => $cliente->id
                    ]);
                } catch (\Exception $e) {
                    \DB::rollBack();
                    echo json_encode([
                        'success' => false,
                        'message' => 'Errore: ' . $e->getMessage()
                    ]);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Azione non valida']);
        }
    } catch (\Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Errore: ' . $e->getMessage()
        ]);
    }

    exit;
}
?>
