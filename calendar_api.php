<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$currentUser = getCurrentUser();

try {
    switch ($action) {
        case 'events':
            getEvents();
            break;

        case 'create':
            createEvent();
            break;

        case 'create_generic':
            createGenericEvent();
            break;

        case 'update_date':
            updateEventDate();
            break;

        case 'sync_google':
            syncGoogleCalendar();
            break;

        case 'delete_event':
            deleteEvent();
            break;

        default:
            throw new Exception('Azione non valida');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Carica eventi per il calendario
 */
function getEvents() {
    global $db, $currentUser;

    $start = $_GET['start'] ?? date('Y-m-01');
    $end = $_GET['end'] ?? date('Y-m-t');

    $events = [];

    // Carica scadenze dal database
    $stmt = $db->prepare("
        SELECT
            s.*,
            p.titolo as progetto_titolo,
            c.ragione_sociale
        FROM scadenze s
        LEFT JOIN progetti p ON s.progetto_id = p.id
        LEFT JOIN clienti c ON p.cliente_id = c.id
        WHERE DATE(s.data_scadenza) BETWEEN :start AND :end
        ORDER BY s.data_scadenza
    ");
    $stmt->bindValue(':start', $start, SQLITE3_TEXT);
    $stmt->bindValue(':end', $end, SQLITE3_TEXT);
    $result = $stmt->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $events[] = [
            'id' => 'scadenza-' . $row['id'],
            'title' => $row['descrizione'],
            'start' => $row['data_scadenza'],
            'allDay' => true,
            'className' => 'scadenza-' . $row['stato'],
            'extendedProps' => [
                'type' => 'scadenza',
                'id' => $row['id'],
                'progetto' => $row['progetto_titolo'],
                'cliente' => $row['ragione_sociale'],
                'importo' => formatCurrency($row['importo']),
                'stato' => $row['stato'],
                'tipo' => $row['tipo'],
                'note' => $row['note']
            ]
        ];
    }

    // Carica eventi generici (note, appuntamenti, ecc.)
    $stmt = $db->prepare("
        SELECT *
        FROM calendar_events
        WHERE user_id = :user_id
        AND DATE(data_inizio) BETWEEN :start AND :end
        ORDER BY data_inizio
    ");
    $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
    $stmt->bindValue(':start', $start, SQLITE3_TEXT);
    $stmt->bindValue(':end', $end, SQLITE3_TEXT);
    $result = $stmt->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $events[] = [
            'id' => 'generic-' . $row['id'],
            'title' => $row['titolo'],
            'start' => $row['data_inizio'],
            'end' => $row['data_fine'],
            'allDay' => $row['tutto_il_giorno'] == 1,
            'backgroundColor' => $row['colore'],
            'borderColor' => $row['colore'],
            'extendedProps' => [
                'type' => 'generic',
                'id' => $row['id'],
                'tipo_evento' => $row['tipo_evento'],
                'descrizione' => $row['descrizione'],
                'priorita' => $row['priorita'],
                'completato' => $row['completato']
            ]
        ];
    }

    // Carica eventi da Google Calendar se connesso
    if (!empty($currentUser['google_refresh_token'])) {
        try {
            $google_events = getGoogleCalendarEvents($start, $end);
            $events = array_merge($events, $google_events);
        } catch (Exception $e) {
            // Se errore Google Calendar, continua senza eventi Google
            error_log('Errore Google Calendar: ' . $e->getMessage());
        }
    }

    echo json_encode($events);
}

/**
 * Crea nuova scadenza
 */
function createEvent() {
    global $db, $currentUser;

    $descrizione = $_POST['descrizione'] ?? '';
    $progetto_id = (int)($_POST['progetto_id'] ?? 0);
    $data_scadenza = $_POST['data_scadenza'] ?? '';
    $importo = $_POST['importo'] ?? 0;
    $tipo = $_POST['tipo'] ?? 'pagamento';
    $note = $_POST['note'] ?? '';

    if (empty($descrizione) || empty($progetto_id) || empty($data_scadenza)) {
        throw new Exception('Campi obbligatori mancanti');
    }

    $stmt = $db->prepare("
        INSERT INTO scadenze (progetto_id, descrizione, data_scadenza, importo, tipo, note, stato)
        VALUES (:progetto_id, :descrizione, :data_scadenza, :importo, :tipo, :note, 'in_attesa')
    ");
    $stmt->bindValue(':progetto_id', $progetto_id, SQLITE3_INTEGER);
    $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
    $stmt->bindValue(':data_scadenza', $data_scadenza, SQLITE3_TEXT);
    $stmt->bindValue(':importo', $importo, SQLITE3_FLOAT);
    $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
    $stmt->bindValue(':note', $note, SQLITE3_TEXT);
    $stmt->execute();

    $scadenza_id = $db->lastInsertRowID();

    logActivity('create_scadenza', 'scadenze', $scadenza_id, "Creata scadenza: $descrizione");

    // Sincronizza con Google Calendar se connesso
    if (!empty($currentUser['google_refresh_token'])) {
        try {
            exportScadenzaToGoogle($scadenza_id);
        } catch (Exception $e) {
            error_log('Errore export Google: ' . $e->getMessage());
        }
    }

    echo json_encode(['success' => true, 'id' => $scadenza_id]);
}

/**
 * Crea nuovo evento generico
 */
function createGenericEvent() {
    global $db, $currentUser;

    $titolo = $_POST['titolo'] ?? '';
    $tipo_evento = $_POST['tipo_evento'] ?? 'nota';
    $data_inizio = $_POST['data_inizio_generic'] ?? '';
    $data_fine = $_POST['data_fine'] ?? null;
    $tutto_il_giorno = isset($_POST['tutto_il_giorno']) ? 1 : 0;
    $colore = $_POST['colore'] ?? '#9c27b0';
    $descrizione = $_POST['descrizione_generic'] ?? '';

    if (empty($titolo) || empty($data_inizio)) {
        throw new Exception('Titolo e data inizio sono obbligatori');
    }

    // Se "tutto il giorno", converti data in formato corretto
    if ($tutto_il_giorno && strlen($data_inizio) == 10) {
        $data_inizio .= ' 00:00:00';
    }

    $stmt = $db->prepare("
        INSERT INTO calendar_events (user_id, titolo, descrizione, tipo_evento, data_inizio, data_fine, tutto_il_giorno, colore)
        VALUES (:user_id, :titolo, :descrizione, :tipo_evento, :data_inizio, :data_fine, :tutto_il_giorno, :colore)
    ");
    $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
    $stmt->bindValue(':titolo', $titolo, SQLITE3_TEXT);
    $stmt->bindValue(':descrizione', $descrizione, SQLITE3_TEXT);
    $stmt->bindValue(':tipo_evento', $tipo_evento, SQLITE3_TEXT);
    $stmt->bindValue(':data_inizio', $data_inizio, SQLITE3_TEXT);
    $stmt->bindValue(':data_fine', $data_fine, SQLITE3_TEXT);
    $stmt->bindValue(':tutto_il_giorno', $tutto_il_giorno, SQLITE3_INTEGER);
    $stmt->bindValue(':colore', $colore, SQLITE3_TEXT);
    $stmt->execute();

    $event_id = $db->lastInsertRowID();

    logActivity('create_calendar_event', 'calendar_events', $event_id, "Creato evento: $titolo");

    echo json_encode(['success' => true, 'id' => $event_id]);
}

/**
 * Aggiorna data evento (drag & drop)
 */
function updateEventDate() {
    global $db, $currentUser;

    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? '';
    $new_date = $_POST['new_date'] ?? '';

    if (!$id || !$type || !$new_date) {
        throw new Exception('Parametri mancanti');
    }

    if ($type === 'scadenza') {
        // Aggiorna scadenza
        $stmt = $db->prepare("UPDATE scadenze SET data_scadenza = :data WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':data', $new_date, SQLITE3_TEXT);
        $stmt->execute();

        logActivity('update_scadenza', 'scadenze', $id, "Data scadenza aggiornata");

        // Aggiorna anche su Google Calendar se connesso
        if (!empty($currentUser['google_refresh_token'])) {
            try {
                updateScadenzaOnGoogle($id);
            } catch (Exception $e) {
                error_log('Errore update Google: ' . $e->getMessage());
            }
        }
    } elseif ($type === 'generic') {
        // Aggiorna evento generico
        $stmt = $db->prepare("UPDATE calendar_events SET data_inizio = :data WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':data', $new_date, SQLITE3_TEXT);
        $stmt->execute();

        logActivity('update_calendar_event', 'calendar_events', $id, "Data evento aggiornata");
    }

    echo json_encode(['success' => true]);
}

/**
 * Elimina evento
 */
function deleteEvent() {
    global $db, $currentUser;

    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? '';

    if (!$id || !$type) {
        throw new Exception('Parametri mancanti');
    }

    if ($type === 'generic') {
        $stmt = $db->prepare("DELETE FROM calendar_events WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
        $stmt->execute();

        logActivity('delete_calendar_event', 'calendar_events', $id, "Evento eliminato");
    } else {
        throw new Exception('Tipo evento non eliminabile');
    }

    echo json_encode(['success' => true]);
}

/**
 * Sincronizza con Google Calendar
 */
function syncGoogleCalendar() {
    global $currentUser;

    if (empty($currentUser['google_refresh_token'])) {
        throw new Exception('Google Calendar non connesso');
    }

    // Import da Google Calendar
    $imported = importGoogleEvents();

    // Export scadenze a Google Calendar
    $exported = exportScadenzeToGoogle();

    echo json_encode([
        'success' => true,
        'imported' => $imported,
        'exported' => $exported
    ]);
}

/**
 * Ottieni eventi da Google Calendar
 */
function getGoogleCalendarEvents($start, $end) {
    global $currentUser;

    $access_token = getGoogleAccessToken($currentUser['google_refresh_token']);
    if (!$access_token) {
        return [];
    }

    $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?' . http_build_query([
        'timeMin' => $start . 'T00:00:00Z',
        'timeMax' => $end . 'T23:59:59Z',
        'singleEvents' => 'true',
        'orderBy' => 'startTime'
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new Exception('Errore caricamento Google Calendar');
    }

    $data = json_decode($response, true);
    $events = [];

    if (isset($data['items'])) {
        foreach ($data['items'] as $item) {
            // Skip eventi creati dal nostro CRM
            if (isset($item['extendedProperties']['private']['crm_scadenza_id'])) {
                continue;
            }

            $start_date = $item['start']['dateTime'] ?? $item['start']['date'] ?? null;
            $end_date = $item['end']['dateTime'] ?? $item['end']['date'] ?? null;

            if ($start_date) {
                $events[] = [
                    'id' => 'google-' . $item['id'],
                    'title' => $item['summary'] ?? 'Evento senza titolo',
                    'start' => $start_date,
                    'end' => $end_date,
                    'allDay' => isset($item['start']['date']),
                    'className' => 'google-event',
                    'extendedProps' => [
                        'type' => 'google',
                        'google_id' => $item['id'],
                        'description' => $item['description'] ?? ''
                    ]
                ];
            }
        }
    }

    return $events;
}

/**
 * Esporta singola scadenza a Google Calendar
 */
function exportScadenzaToGoogle($scadenza_id) {
    global $db, $currentUser;

    $access_token = getGoogleAccessToken($currentUser['google_refresh_token']);
    if (!$access_token) {
        return false;
    }

    // Carica scadenza
    $stmt = $db->prepare("
        SELECT s.*, p.titolo as progetto_titolo, c.ragione_sociale
        FROM scadenze s
        LEFT JOIN progetti p ON s.progetto_id = s.id
        LEFT JOIN clienti c ON p.cliente_id = c.id
        WHERE s.id = :id
    ");
    $stmt->bindValue(':id', $scadenza_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $scadenza = $result->fetchArray(SQLITE3_ASSOC);

    if (!$scadenza) {
        return false;
    }

    // Verifica se già esportata
    if (!empty($scadenza['google_event_id'])) {
        return updateScadenzaOnGoogle($scadenza_id);
    }

    // Crea evento Google
    $event = [
        'summary' => $scadenza['descrizione'],
        'description' => "Scadenza CRM\n" .
                        "Cliente: " . $scadenza['ragione_sociale'] . "\n" .
                        "Progetto: " . $scadenza['progetto_titolo'] . "\n" .
                        "Importo: " . formatCurrency($scadenza['importo']) . "\n\n" .
                        ($scadenza['note'] ?? ''),
        'start' => [
            'date' => $scadenza['data_scadenza']
        ],
        'end' => [
            'date' => $scadenza['data_scadenza']
        ],
        'extendedProperties' => [
            'private' => [
                'crm_scadenza_id' => (string)$scadenza_id
            ]
        ]
    ];

    $ch = curl_init('https://www.googleapis.com/calendar/v3/calendars/primary/events');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        // Salva ID evento Google nel database
        $stmt = $db->prepare("UPDATE scadenze SET google_event_id = :event_id WHERE id = :id");
        $stmt->bindValue(':event_id', $data['id'], SQLITE3_TEXT);
        $stmt->bindValue(':id', $scadenza_id, SQLITE3_INTEGER);
        $stmt->execute();

        return true;
    }

    return false;
}

/**
 * Aggiorna scadenza su Google Calendar
 */
function updateScadenzaOnGoogle($scadenza_id) {
    global $db, $currentUser;

    $access_token = getGoogleAccessToken($currentUser['google_refresh_token']);
    if (!$access_token) {
        return false;
    }

    // Carica scadenza
    $stmt = $db->prepare("SELECT * FROM scadenze WHERE id = :id");
    $stmt->bindValue(':id', $scadenza_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $scadenza = $result->fetchArray(SQLITE3_ASSOC);

    if (!$scadenza || empty($scadenza['google_event_id'])) {
        return false;
    }

    $event = [
        'start' => ['date' => $scadenza['data_scadenza']],
        'end' => ['date' => $scadenza['data_scadenza']]
    ];

    $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $scadenza['google_event_id'];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code === 200;
}

/**
 * Esporta tutte le scadenze non ancora esportate
 */
function exportScadenzeToGoogle() {
    global $db;

    $exported = 0;
    $result = $db->query("
        SELECT id FROM scadenze
        WHERE google_event_id IS NULL
        AND data_scadenza >= DATE('now')
        LIMIT 50
    ");

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        if (exportScadenzaToGoogle($row['id'])) {
            $exported++;
        }
    }

    return $exported;
}

/**
 * Importa eventi da Google Calendar
 */
function importGoogleEvents() {
    // Per ora non importiamo eventi Google come scadenze
    // Solo visualizzazione nel calendario
    return 0;
}

/**
 * Ottieni access token da refresh token
 */
function getGoogleAccessToken($refresh_token) {
    global $db;

    // Carica credenziali Google
    $result = $db->query("SELECT * FROM google_credentials LIMIT 1");
    $credentials = $result->fetchArray(SQLITE3_ASSOC);

    if (!$credentials) {
        return null;
    }

    $data = [
        'client_id' => $credentials['client_id'],
        'client_secret' => $credentials['client_secret'],
        'refresh_token' => $refresh_token,
        'grant_type' => 'refresh_token'
    ];

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    return $result['access_token'] ?? null;
}
?>
